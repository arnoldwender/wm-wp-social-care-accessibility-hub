<?php
declare(strict_types=1);

namespace ArnoldWender\SocialCareA11y\API;

use ArnoldWender\SocialCareA11y\Domain\Care_Needs_Checker;
use ArnoldWender\SocialCareA11y\Domain\Seed_Data_Manager;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Read-only frontend routes under wm-social-care/v1 protected by HTTP method verification,
 * transient-based HMAC-SHA256 IP rate limiting, and strict schema/handler sanitization.
 */
final class REST_Controller {
    public const RATE_LIMIT_MAX = 60;
    public const RATE_LIMIT_WINDOW = 60;
    public const TRANSIENT_PREFIX = 'wm_sc_rl_';

    public static function register_routes(): void {
        if (!function_exists('add_action') || !function_exists('register_rest_route')) {
            return;
        }

        add_action('rest_api_init', function () {
            register_rest_route('wm-social-care/v1', '/loesungswege', [
                'methods'             => 'GET',
                'callback'            => [self::class, 'get_loesungswege'],
                'permission_callback' => [self::class, 'check_public_read_permission'],
            ]);

            // Out-of-range care degrees answer 400. Until 2026-09-14 they were answered with the
            // amounts of care degree 2 and status 200.
            register_rest_route('wm-social-care/v1', '/bedarfscheck', [
                'methods'             => 'GET',
                'callback'            => [self::class, 'calculate_bedarfscheck'],
                'permission_callback' => [self::class, 'check_public_read_permission'],
                'args'                => self::bedarfscheck_args(),
            ]);
        });
    }

    /**
     * Permission callback for read-only public frontend routes: enforces GET/HEAD and per-IP HMAC rate limiting.
     */
    public static function check_public_read_permission(mixed $request = null): mixed {
        if (is_object($request) && method_exists($request, 'get_method')) {
            $method = strtoupper((string) $request->get_method());
            if ($method !== '' && $method !== 'GET' && $method !== 'HEAD') {
                return class_exists('\WP_Error')
                    ? new \WP_Error(
                        'wm_social_care_method_not_allowed',
                        __('Nur Leseanfragen (GET) sind für diesen Endpunkt erlaubt.', 'wm-social-care-accessibility-hub'),
                        ['status' => 405]
                    )
                    : false;
            }
        }

        if (!self::consume_rate_limit_slot()) {
            return class_exists('\WP_Error')
                ? new \WP_Error(
                    'wm_social_care_rate_limited',
                    __('Zu viele Anfragen. Bitte versuchen Sie es in einer Minute erneut.', 'wm-social-care-accessibility-hub'),
                    ['status' => 429]
                )
                : false;
        }

        return true;
    }

    /**
     * Computes a non-reversible HMAC-SHA256 digest of the client IP address so raw IPs are never stored in transients.
     */
    public static function client_ip_hmac(?string $ip = null): string {
        if ($ip === null) {
            $server_ip = isset($_SERVER['REMOTE_ADDR']) && is_string($_SERVER['REMOTE_ADDR'])
                ? (function_exists('wp_unslash') ? (string) wp_unslash($_SERVER['REMOTE_ADDR']) : $_SERVER['REMOTE_ADDR']) // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Validated with FILTER_VALIDATE_IP below.
                : '127.0.0.1';
            $raw_ip = $server_ip;
        } else {
            $raw_ip = $ip;
        }

        $clean_ip = filter_var(trim($raw_ip), FILTER_VALIDATE_IP) ?: '0.0.0.0';
        $secret   = function_exists('wp_salt')
            ? (string) wp_salt('auth')
            : (defined('AUTH_SALT') ? (string) AUTH_SALT : 'wm-social-care-local-rate-salt');

        return hash_hmac('sha256', $clean_ip, $secret);
    }

    /**
     * Increments and checks the 60-second transient counter for the hashed client IP.
     */
    public static function consume_rate_limit_slot(?string $ip = null): bool {
        if (!function_exists('get_transient') || !function_exists('set_transient')) {
            return true;
        }

        $max = self::RATE_LIMIT_MAX;
        if (function_exists('apply_filters')) {
            $filtered = (int) apply_filters('wm_social_care_rate_limit_per_minute', $max);
            $max      = max(1, $filtered);
        }

        $key   = self::TRANSIENT_PREFIX . substr(self::client_ip_hmac($ip), 0, 24);
        $count = (int) get_transient($key);
        if ($count >= $max) {
            return false;
        }

        set_transient($key, $count + 1, self::RATE_LIMIT_WINDOW);
        return true;
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public static function bedarfscheck_args(): array {
        return [
            'pflegegrad' => [
                'description'       => 'Pflegegrad 1 bis 5.',
                'type'              => 'integer',
                'minimum'           => 1,
                'maximum'           => 5,
                'default'           => 2,
                'validate_callback' => 'rest_validate_request_arg',
                'sanitize_callback' => 'rest_sanitize_request_arg',
            ],
        ];
    }

    /** The eight built-in examples of Seed_Data_Manager. */
    public static function get_loesungswege(mixed $request = null): array {
        $seed = Seed_Data_Manager::get_seed_data();
        return [
            'status' => 'success',
            'count'  => count($seed),
            'data'   => $seed,
        ];
    }

    /** Statutory amounts for one care degree; care degree 2 when called without a request. */
    public static function calculate_bedarfscheck(mixed $request = null): array {
        $raw = (is_object($request) && method_exists($request, 'get_param'))
            ? $request->get_param('pflegegrad')
            : 2;

        if ($raw === null || $raw === '') {
            $pg = 2;
        } else {
            $clean = is_scalar($raw)
                ? trim(function_exists('sanitize_text_field') ? (string) sanitize_text_field((string) $raw) : (string) $raw)
                : '';
            if (!preg_match('/^[1-5]$/', $clean)) {
                throw new \InvalidArgumentException('Ungültiger Pflegegrad. Erlaubt sind Werte von 1 bis 5.');
            }
            $pg = (int) $clean;
        }

        return [
            'status' => 'success',
            'data'   => Care_Needs_Checker::calculate_entitlements($pg),
        ];
    }
}
