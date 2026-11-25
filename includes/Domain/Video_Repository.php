<?php
declare(strict_types=1);

namespace ArnoldWender\SocialCareA11y\Domain;

if (!defined('ABSPATH')) {
    exit;
}

final class Video_Repository {
    public const CPT_SLUG = 'wm_loesungsweg';
    public const TAX_CATEGORY = 'wm_kategorie';
    public const TAX_TARGET_GROUP = 'wm_zielgruppe';
    public const TAX_HILFSMITTEL = 'wm_hilfsmittel';
    public const TAX_PFLEGEGRAD = 'wm_pflegegrad';

    public static function register_cpt_and_taxonomies(): void {
        if (!function_exists('register_post_type') || !function_exists('register_taxonomy')) {
            return;
        }

        // 1. Custom Post Type: Lösungsweg (Kinästhetik & Pflege)
        register_post_type(self::CPT_SLUG, [
            'labels' => [
                'name'               => __('Lösungswege', 'wm-social-care-accessibility-hub'),
                'singular_name'      => __('Lösungsweg', 'wm-social-care-accessibility-hub'),
                'add_new_item'       => __('Neuen Lösungsweg anlegen', 'wm-social-care-accessibility-hub'),
                'edit_item'          => __('Lösungsweg bearbeiten', 'wm-social-care-accessibility-hub'),
                'search_items'       => __('Lösungswege durchsuchen', 'wm-social-care-accessibility-hub'),
                'not_found'          => __('Keine Lösungswege gefunden', 'wm-social-care-accessibility-hub'),
            ],
            'public'              => true,
            'has_archive'         => true,
            'show_in_rest'        => true,
            'rest_base'           => 'loesungswege',
            'menu_icon'           => 'dashicons-video-alt3',
            'supports'            => ['title', 'editor', 'thumbnail', 'excerpt', 'custom-fields'],
            'rewrite'             => ['slug' => 'loesungswege'],
        ]);

        // 2. Taxonomías
        register_taxonomy(self::TAX_CATEGORY, self::CPT_SLUG, [
            'labels'        => ['name' => __('Kategorien', 'wm-social-care-accessibility-hub'), 'singular_name' => __('Kategorie', 'wm-social-care-accessibility-hub')],
            'hierarchical'  => true,
            'show_in_rest'  => true,
            'rewrite'       => ['slug' => 'kategorie-loesungsweg'],
        ]);

        register_taxonomy(self::TAX_TARGET_GROUP, self::CPT_SLUG, [
            'labels'        => ['name' => __('Zielgruppen', 'wm-social-care-accessibility-hub'), 'singular_name' => __('Zielgruppe', 'wm-social-care-accessibility-hub')],
            'hierarchical'  => false,
            'show_in_rest'  => true,
        ]);

        register_taxonomy(self::TAX_HILFSMITTEL, self::CPT_SLUG, [
            'labels'        => ['name' => __('Hilfsmittel', 'wm-social-care-accessibility-hub'), 'singular_name' => __('Hilfsmittel', 'wm-social-care-accessibility-hub')],
            'hierarchical'  => false,
            'show_in_rest'  => true,
        ]);

        register_taxonomy(self::TAX_PFLEGEGRAD, self::CPT_SLUG, [
            'labels'        => ['name' => __('Pflegegrade', 'wm-social-care-accessibility-hub'), 'singular_name' => __('Pflegegrad', 'wm-social-care-accessibility-hub')],
            'hierarchical'  => true,
            'show_in_rest'  => true,
        ]);
    }

    /**
     * Sanitiza campos de texto para prevenir Formula Injection (CWE-1236).
     */
    public static function sanitize_formula_injection(string $value): string {
        $first_char = substr(ltrim($value), 0, 1);
        if (in_array($first_char, ['=', '+', '-', '@', "\t", "\r"], true)) {
            return "'" . $value;
        }
        return $value;
    }
}
