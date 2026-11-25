<?php
/**
 * WM Suite Hub spoke adapter: identity, health, telemetry, self-test and SBOM component, all measured
 * from what the plugin registers on this site.
 *
 * @package ArnoldWender\SocialCareA11y
 */

declare(strict_types=1);

namespace ArnoldWender\SocialCareA11y\Spoke;

use ArnoldWender\SocialCareA11y\Contracts\WM_Social_Care_Module_Interface;
use ArnoldWender\SocialCareA11y\Domain\Care_Needs_Checker;
use ArnoldWender\SocialCareA11y\Domain\Seed_Data_Manager;
use ArnoldWender\SocialCareA11y\Domain\Video_Repository;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// The hub sorts after this plugin, so its contract is loaded from disk when the hub is installed.
if ( ! interface_exists( 'ArnoldWender\SuiteHub\Contracts\WM_Plugin_Module_Interface' ) && defined( 'WP_PLUGIN_DIR' ) && file_exists( WP_PLUGIN_DIR . '/wm-suite-hub/src/Contracts/WM_Plugin_Module_Interface.php' ) ) {
    require_once WP_PLUGIN_DIR . '/wm-suite-hub/src/Contracts/WM_Plugin_Module_Interface.php';
}

// Without the hub there is no contract to implement. Until 2026-09-14 the class was declared anyway,
// which is a fatal error ("Interface not found") on every request of a site without wm-suite-hub.
if ( ! interface_exists( 'ArnoldWender\SuiteHub\Contracts\WM_Plugin_Module_Interface' ) ) {
    return;
}

final class Social_Care_Spoke_Adapter implements WM_Social_Care_Module_Interface, \ArnoldWender\SuiteHub\Contracts\WM_Plugin_Module_Interface {
    /** The shortcodes Shortcodes::register() adds; health and self-test check each one. */
    public const SHORTCODES = [
        'wm_social_care_bedarfscheck',
        'wm_leichte_sprache_card',
        'wm_a11y_toolbar',
        'wm_videothek_loesungswege',
        'wm_video_player',
    ];

    public function get_module_slug(): string {
        return 'wm-social-care-accessibility-hub';
    }

    public function get_name(): string {
        return 'WM Social Care Accessibility & Kinästhetik Hub';
    }

    public function get_version(): string {
        return defined('WM_SOCIAL_CARE_VERSION') ? WM_SOCIAL_CARE_VERSION : '1.3.2';
    }

    public function get_description(): string {
        return 'Shortcodes für eine Leichte-Sprache-Karte, einen Pflege-Bedarfscheck mit den Beträgen nach SGB XI, eine Barrierefreiheits-Toolbar und eine Videothek mit acht eingebauten Beispiel-Lösungswegen.';
    }

    /**
     * What is missing of the registrations made on `init`: the five shortcodes and the post type.
     *
     * @return string[]|null Missing names, or null before `init` (nothing can be measured yet).
     */
    private function missing_registrations(): ?array {
        if (function_exists('did_action') && did_action('init') === 0) {
            return null;
        }

        $missing = [];
        foreach (self::SHORTCODES as $tag) {
            if (!function_exists('shortcode_exists') || !shortcode_exists($tag)) {
                $missing[] = $tag;
            }
        }
        if (!function_exists('post_type_exists') || !post_type_exists(Video_Repository::CPT_SLUG)) {
            $missing[] = Video_Repository::CPT_SLUG;
        }
        return $missing;
    }

    /**
     * HEALTHY when every shortcode and the post type are registered, DEGRADED when some are missing
     * or `init` has not run yet, ERROR when none is registered. Until 2026-09-14 this returned
     * HEALTHY unconditionally.
     */
    public function get_health_status(): string {
        $missing = $this->missing_registrations();
        if ($missing === null) {
            return 'DEGRADED';
        }
        if ($missing === []) {
            return 'HEALTHY';
        }
        return count($missing) === count(self::SHORTCODES) + 1 ? 'ERROR' : 'DEGRADED';
    }

    /** The plugin runs without any other WM Suite plugin. */
    public function get_dependencies(): array {
        return [];
    }

    public function get_settings_url(): ?string {
        return function_exists('admin_url') ? admin_url('admin.php?page=wm-social-care-accessibility-hub') : null;
    }

    /**
     * Measured values only. Until 2026-09-14 this returned fixed strings ("COMPLIANT", "ACTIVE") and
     * 24 videos, while the code ships eight examples and nothing assesses compliance.
     */
    public function get_telemetry_metrics(): array {
        $missing  = $this->missing_registrations();
        $examples = Seed_Data_Manager::get_seed_data();

        $external_hosts = [];
        foreach ($examples as $item) {
            $url  = (string) ($item['videoUrl'] ?? '');
            $host = (string) (function_exists('wp_parse_url') ? wp_parse_url($url, PHP_URL_HOST) : parse_url($url, PHP_URL_HOST)); // phpcs:ignore WordPress.WP.AlternativeFunctions.parse_url_parse_url -- fallback for the standalone tests only.
            if ($host !== '') {
                $external_hosts[$host] = ($external_hosts[$host] ?? 0) + 1;
            }
        }

        $published = null;
        if (function_exists('post_type_exists') && post_type_exists(Video_Repository::CPT_SLUG) && function_exists('wp_count_posts')) {
            $counts    = wp_count_posts(Video_Repository::CPT_SLUG);
            $published = is_object($counts) ? (int) ($counts->publish ?? 0) : null;
        }

        return [
            'registrations_measured'  => $missing !== null,
            'missing_registrations'   => $missing,
            'builtin_examples'        => count($examples),
            'example_video_hosts'     => $external_hosts,
            'published_loesungswege'  => $published,
            'statutory_amounts_as_of' => Care_Needs_Checker::STATUTORY_AS_OF,
        ];
    }

    /**
     * `self_test` checks the registrations and computes the statutory table for care degrees 1-5.
     * Any other action fails; until 2026-09-14 every action name answered "executed successfully".
     */
    public function execute_action(string $action, array $params = []): array {
        if ($action !== 'self_test') {
            return [
                'success' => false,
                'message' => sprintf('Unbekannte Spoke-Aktion "%s".', function_exists('esc_html') ? esc_html($action) : $action),
            ];
        }

        $missing = $this->missing_registrations();
        try {
            $rows = count(Care_Needs_Checker::entitlement_table());
        } catch (\Throwable $e) {
            $rows = 0;
        }

        if ($missing === null) {
            return ['success' => false, 'message' => 'Selbsttest vor init: Registrierungen noch nicht messbar.'];
        }
        if ($missing !== [] || $rows !== 5) {
            return [
                'success' => false,
                'message' => sprintf('Selbsttest fehlgeschlagen: fehlend %s; Tabelle mit %d von 5 Pflegegraden.', $missing === [] ? 'nichts' : implode(', ', $missing), $rows),
            ];
        }
        return [
            'success' => true,
            'message' => sprintf('Selbsttest bestanden: %d Shortcodes und der Beitragstyp registriert, Beträge für 5 Pflegegrade (Stand %s).', count(self::SHORTCODES), Care_Needs_Checker::STATUTORY_AS_OF),
        ];
    }

    /**
     * One CycloneDX component; the hub merges it into its own document. Until 2026-09-14 this returned
     * a whole BOM (bomFormat, metadata, components), which the hub nested inside a component.
     */
    public function get_sbom_data(): array {
        $file = defined('WM_SOCIAL_CARE_FILE') ? WM_SOCIAL_CARE_FILE : dirname(__DIR__, 2) . '/wm-social-care-accessibility-hub.php';

        return [
            'bom-ref'     => 'pkg:wordpress/wm-social-care-accessibility-hub@' . $this->get_version(),
            'type'        => 'application',
            'name'        => $this->get_module_slug(),
            'version'     => $this->get_version(),
            'description' => $this->get_description(),
            'hashes'      => [
                [
                    'alg'     => 'SHA-256',
                    'content' => is_file($file) ? (string) hash_file('sha256', $file) : '',
                ],
            ],
            'licenses'    => [
                ['license' => ['id' => 'GPL-2.0-or-later']],
            ],
            // Features, not legal labels: the plugin makes no compliance assessment.
            'properties'  => [
                ['name' => 'wm:features:shortcodes', 'value' => implode(', ', self::SHORTCODES)],
                ['name' => 'wm:features:rest', 'value' => 'wm-social-care/v1: GET /loesungswege, GET /bedarfscheck'],
                ['name' => 'wm:features:content-model', 'value' => 'post type wm_loesungsweg with four taxonomies'],
                ['name' => 'wm:statutory-amounts-as-of', 'value' => Care_Needs_Checker::STATUTORY_AS_OF],
            ],
        ];
    }
}
