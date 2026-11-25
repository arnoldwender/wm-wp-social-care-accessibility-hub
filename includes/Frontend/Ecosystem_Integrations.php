<?php
declare(strict_types=1);

namespace ArnoldWender\SocialCareA11y\Frontend;

use ArnoldWender\SocialCareA11y\Domain\Care_Needs_Checker;
use ArnoldWender\SocialCareA11y\Domain\Seed_Data_Manager;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Ecosystem integrations for WordPress 6.5+ Block Bindings (`wm-social-care/info`)
 * and GenerateBlocks 2.x Dynamic Tags (`{{wm_social_care_info}}`).
 */
final class Ecosystem_Integrations {
    public const BINDING_SOURCE = 'wm-social-care/info';
    public const GB_TAG = 'wm_social_care_info';

    public static function register(): void {
        if (function_exists('register_block_bindings_source')) {
            $already_registered = function_exists('get_block_bindings_source')
                && get_block_bindings_source('wm-social-care/info') !== null;
            if (!$already_registered) {
                register_block_bindings_source('wm-social-care/info', [
                    'label'              => __('Social Care SGB-XI-Info', 'wm-social-care-accessibility-hub'),
                    'get_value_callback' => [self::class, 'resolve_block_binding'],
                    'uses_context'       => ['postId', 'postType'],
                ]);
            }
        }

        if (class_exists('GenerateBlocks_Register_Dynamic_Tag')) {
            new \GenerateBlocks_Register_Dynamic_Tag([
                'title'       => __('Social Care SGB-XI-Info', 'wm-social-care-accessibility-hub'),
                'tag'         => self::GB_TAG,
                'type'        => 'wm-social-care',
                'supports'    => [],
                'description' => __('Gesetzliche SGB-XI-Höchstbeträge und Stand der Beträge.', 'wm-social-care-accessibility-hub'),
                'options'     => [
                    'field' => [
                        'type'    => 'select',
                        'label'   => __('Feld', 'wm-social-care-accessibility-hub'),
                        'default' => 'summary',
                        'options' => [
                            ['value' => 'summary', 'label' => __('Zusammenfassung (Entlastungsbetrag)', 'wm-social-care-accessibility-hub')],
                            ['value' => 'entlastungsbetrag_month', 'label' => __('Entlastungsbetrag / Monat', 'wm-social-care-accessibility-hub')],
                            ['value' => 'entlastungsbetrag_year', 'label' => __('Entlastungsbetrag / Jahr', 'wm-social-care-accessibility-hub')],
                            ['value' => 'pflegegeld_month', 'label' => __('Pflegegeld / Monat (§ 37)', 'wm-social-care-accessibility-hub')],
                            ['value' => 'sachleistung_month', 'label' => __('Pflegesachleistung / Monat (§ 36)', 'wm-social-care-accessibility-hub')],
                            ['value' => 'verhinderungspflege_year', 'label' => __('Gemeinsamer Jahresbetrag (§ 42a)', 'wm-social-care-accessibility-hub')],
                            ['value' => 'statutory_as_of', 'label' => __('Stand der Beträge', 'wm-social-care-accessibility-hub')],
                            ['value' => 'examples_count', 'label' => __('Anzahl Beispiel-Lösungswege', 'wm-social-care-accessibility-hub')],
                        ],
                    ],
                    'pflegegrad' => [
                        'type'    => 'text',
                        'label'   => __('Pflegegrad (1–5)', 'wm-social-care-accessibility-hub'),
                        'default' => '2',
                    ],
                ],
                'return'      => [self::class, 'render_generateblocks_tag'],
            ]);
        }

        if (function_exists('add_filter')) {
            add_filter('generateblocks_dynamic_tag_replacement', [self::class, 'filter_generateblocks_replacement'], 10, 2);
        }
    }

    /**
     * Resolves a single statutory or seed info field for Block Bindings and GenerateBlocks.
     */
    public static function resolve_info_value(string $field = 'summary', int $pflegegrad = 2): ?string {
        $clean_field = preg_replace('/[^a-z0-9_]/', '', strtolower(trim($field))) ?: 'summary';
        $pg          = ($pflegegrad >= 1 && $pflegegrad <= 5) ? $pflegegrad : 2;
        $calc        = Care_Needs_Checker::calculate_entitlements($pg);
        $euro        = static fn(float $amount): string => number_format($amount, 2, ',', '.') . ' €';

        return match ($clean_field) {
            'entlastungsbetrag_month'  => $euro((float) $calc['entlastungsbetrag_month']),
            'entlastungsbetrag_year'   => $euro((float) $calc['entlastungsbetrag_year']),
            'pflegegeld_month'         => $euro((float) $calc['pflegegeld_month']),
            'sachleistung_month'       => $euro((float) $calc['sachleistung_month']),
            'verhinderungspflege_year' => $euro((float) $calc['verhinderungspflege_year']),
            'statutory_as_of'          => (string) $calc['statutory_as_of'],
            'examples_count'           => (string) count(Seed_Data_Manager::get_seed_data()),
            'summary'                  => sprintf(
                /* translators: 1: monthly relief amount in euros, 2: statutory as-of date. */
                __('Entlastungsbetrag: %1$s / Monat (Stand %2$s)', 'wm-social-care-accessibility-hub'),
                $euro((float) $calc['entlastungsbetrag_month']),
                (string) $calc['statutory_as_of']
            ),
            default                    => null,
        };
    }

    /**
     * Callback for WordPress 6.5+ `register_block_bindings_source`.
     *
     * @param array<string, mixed> $source_args
     */
    public static function resolve_block_binding(array $source_args, mixed $block_instance = null, string $attribute_name = ''): ?string {
        $field = is_scalar($source_args['key'] ?? $source_args['field'] ?? null)
            ? (string) ($source_args['key'] ?? $source_args['field'])
            : 'summary';
        $pg = isset($source_args['pflegegrad']) && is_scalar($source_args['pflegegrad'])
            ? (int) $source_args['pflegegrad']
            : 2;

        return self::resolve_info_value($field, $pg);
    }

    /**
     * Callback for GenerateBlocks 2.x `GenerateBlocks_Register_Dynamic_Tag`.
     *
     * @param array<string, mixed> $options
     * @param array<string, mixed> $block
     */
    public static function render_generateblocks_tag(array $options = [], array $block = [], mixed $instance = null): string {
        $field = isset($options['field']) && is_scalar($options['field']) ? (string) $options['field'] : 'summary';
        $pg    = isset($options['pflegegrad']) && is_scalar($options['pflegegrad']) ? (int) $options['pflegegrad'] : 2;
        $value = self::resolve_info_value($field, $pg);

        return $value !== null ? esc_html($value) : '';
    }

    /**
     * Filter fallback for `generateblocks_dynamic_tag_replacement` and inline `{{wm_social_care_info}}` tokens.
     */
    public static function filter_generateblocks_replacement(mixed $replacement, mixed $context = []): mixed {
        if (is_array($context) && ($context['tag'] ?? '') === self::GB_TAG && ($replacement === '' || $replacement === null)) {
            $opts = isset($context['options']) && is_array($context['options']) ? $context['options'] : [];
            return self::render_generateblocks_tag($opts);
        }

        if (is_string($replacement) && str_contains($replacement, '{{' . self::GB_TAG . '}}')) {
            return str_replace('{{' . self::GB_TAG . '}}', self::render_generateblocks_tag([]), $replacement);
        }

        return $replacement;
    }
}
