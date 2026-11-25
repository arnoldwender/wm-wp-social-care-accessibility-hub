<?php
declare(strict_types=1);

namespace ArnoldWender\SocialCareA11y\Admin;

use ArnoldWender\SocialCareA11y\Domain\Video_Repository;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * The plugin's single admin screen: what the plugin registers on this site (shortcodes, the
 * video library) and the shortcode reference. Rendered from the WM Suite family sheets —
 * the plugin carries no admin sheet of its own.
 */
final class Admin_Dashboard {
    // The text domain is spelled out at every call: Plugin Check rejects a constant there
    // (WordPress.WP.I18n.NonSingularStringLiteralDomain, 30 errors measured 2026-09-14).
    private const SLUG = 'wm-social-care-accessibility-hub';
    public const SETTINGS_GROUP = 'wm_social_care_settings';
    public const OPTION_DELETE_ON_UNINSTALL = 'wm_social_care_delete_data_on_uninstall';

    public static function register(): void {
        add_action('admin_menu', [self::class, 'add_menu']);
        add_action('admin_init', [self::class, 'register_settings']);
        add_action('admin_enqueue_scripts', [self::class, 'enqueue_assets']);
    }

    public static function register_settings(): void {
        if (!function_exists('register_setting')) {
            return;
        }

        register_setting(
            self::SETTINGS_GROUP,
            'wm_social_care_delete_data_on_uninstall',
            [
                'type'              => 'string',
                'sanitize_callback' => [self::class, 'sanitize_delete_on_uninstall'],
                'default'           => '0',
            ]
        );
    }

    public static function sanitize_delete_on_uninstall(mixed $value): string {
        return in_array($value, ['1', 1, true, 'on', 'yes'], true) ? '1' : '0';
    }

    public static function set_delete_data_on_uninstall(bool $enabled): bool {
        if (function_exists('current_user_can') && !current_user_can('manage_options')) {
            return false;
        }
        if (!function_exists('update_option')) {
            return false;
        }

        return (bool) update_option('wm_social_care_delete_data_on_uninstall', $enabled ? '1' : '0');
    }

    /**
     * Family menu icon: a monochrome hub-and-spoke mark, filled shapes only so the WordPress
     * svg-painter can recolour it with the admin colour scheme. Placeholder until the family
     * icon is decided (hackaton plan D12).
     */
    public static function family_menu_icon(): string {
        $svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><circle cx="10" cy="10" r="2.6" fill="black"/><circle cx="10" cy="3" r="1.8" fill="black"/><circle cx="16.1" cy="13.5" r="1.8" fill="black"/><circle cx="3.9" cy="13.5" r="1.8" fill="black"/><rect x="9.3" y="4.5" width="1.4" height="3.4" fill="black"/><rect x="9.3" y="12.1" width="1.4" height="3.4" fill="black" transform="rotate(-60 10 10)"/><rect x="9.3" y="12.1" width="1.4" height="3.4" fill="black" transform="rotate(60 10 10)"/></svg>';
        return 'data:image/svg+xml;base64,' . base64_encode($svg);
    }

    public static function add_menu(): void {
        add_menu_page(
            __('Social Care Accessibility Hub', 'wm-social-care-accessibility-hub'),
            __('Social Care A11y', 'wm-social-care-accessibility-hub'),
            'manage_options',
            self::SLUG,
            [self::class, 'render_page'],
            self::family_menu_icon(),
            30
        );
    }

    /**
     * Family sheets (canonical in the hub, byte-identical copies here): every WM plugin registers
     * the same two handles, so WordPress keeps one copy however many plugins are active. The page
     * slug is the plugin's own, so the hook is toplevel_page_<slug> standalone and under the hub
     * alike. Versioned by file time: a sheet updated under an unchanged plugin version was served
     * from the browser cache (measured in the fleet 2026-09-14).
     */
    public static function enqueue_assets(string $hook_suffix): void {
        if (!str_contains($hook_suffix, self::SLUG)) {
            return;
        }

        $root = WM_SOCIAL_CARE_PATH . 'assets/css/';
        wp_enqueue_style(
            'wm-admin-tokens',
            WM_SOCIAL_CARE_URL . 'assets/css/wm-admin-tokens.css',
            [],
            (string) (@filemtime($root . 'wm-admin-tokens.css') ?: WM_SOCIAL_CARE_VERSION)
        );
        wp_enqueue_style(
            'wm-admin-ui',
            WM_SOCIAL_CARE_URL . 'assets/css/wm-admin-ui.css',
            ['wm-admin-tokens'],
            (string) (@filemtime($root . 'wm-admin-ui.css') ?: WM_SOCIAL_CARE_VERSION)
        );
    }

    /**
     * Every status on the screen is measured when the page renders. Until 2026-09-14 the
     * checklist printed four hardcoded ticks that nothing could ever change, and until 2026-09-15
     * the video library and the single player had no row, so their missing registration showed nothing.
     *
     * @return array<int, array{standard: string, ok: bool, note: string}>
     */
    private static function measured_rows(): array {
        $cpt       = post_type_exists(Video_Repository::CPT_SLUG);
        $counts    = $cpt ? wp_count_posts(Video_Repository::CPT_SLUG) : null;
        $published = is_object($counts) ? (int) ($counts->publish ?? 0) : 0;

        return [
            [
                'standard' => __('Leichte-Sprache-Karte', 'wm-social-care-accessibility-hub'),
                'ok'       => shortcode_exists('wm_leichte_sprache_card'),
                'note'     => __('Shortcode wm_leichte_sprache_card mit Mediopunkt-Trennung für Fachbegriffe.', 'wm-social-care-accessibility-hub'),
            ],
            [
                'standard' => __('Pflege-Bedarfscheck', 'wm-social-care-accessibility-hub'),
                'ok'       => shortcode_exists('wm_social_care_bedarfscheck'),
                'note'     => __('Shortcode wm_social_care_bedarfscheck: gesetzliche Höchstbeträge nach §§ 36, 37, 42a und 45b SGB XI für die Pflegegrade 1 bis 5.', 'wm-social-care-accessibility-hub'),
            ],
            [
                'standard' => __('Barrierefreiheits-Toolbar', 'wm-social-care-accessibility-hub'),
                'ok'       => shortcode_exists('wm_a11y_toolbar'),
                'note'     => __('Shortcode wm_a11y_toolbar: Schriftgröße, Kontrast, Vorlesen.', 'wm-social-care-accessibility-hub'),
            ],
            [
                'standard' => __('Videothek', 'wm-social-care-accessibility-hub'),
                'ok'       => shortcode_exists('wm_videothek_loesungswege'),
                'note'     => __('Shortcode wm_videothek_loesungswege: die acht eingebauten Beispiele untereinander.', 'wm-social-care-accessibility-hub'),
            ],
            [
                'standard' => __('Einzelner Player', 'wm-social-care-accessibility-hub'),
                'ok'       => shortcode_exists('wm_video_player'),
                'note'     => __('Shortcode wm_video_player: ein eingebautes Beispiel über das Attribut id (1 bis 8).', 'wm-social-care-accessibility-hub'),
            ],
            [
                'standard' => __('Beitragstyp Lösungsweg', 'wm-social-care-accessibility-hub'),
                'ok'       => $cpt,
                'note'     => sprintf(
                    /* translators: %d: number of published Lösungsweg posts */
                    _n(
                        '%d Lösungsweg veröffentlicht. Die Shortcodes wm_videothek_loesungswege und wm_video_player zeigen nicht diese Beiträge, sondern acht eingebaute Beispiele.',
                        '%d Lösungswege veröffentlicht. Die Shortcodes wm_videothek_loesungswege und wm_video_player zeigen nicht diese Beiträge, sondern acht eingebaute Beispiele.',
                        $published,
                        'wm-social-care-accessibility-hub'
                    ),
                    $published
                ),
            ],
        ];
    }

    public static function render_page(): void {
        if (function_exists('current_user_can') && !current_user_can('manage_options')) {
            return;
        }

        $rows = self::measured_rows();
        $delete_on_uninstall = function_exists('get_option')
            && self::sanitize_delete_on_uninstall(get_option('wm_social_care_delete_data_on_uninstall', '0')) === '1';
        $shortcodes = [
            'wm_social_care_bedarfscheck'  => __('Auswahl des Pflegegrads mit den gesetzlichen Höchstbeträgen (Stand der Beträge im Code).', 'wm-social-care-accessibility-hub'),
            'wm_leichte_sprache_card'      => __('Content-Box in Leichter Sprache mit Mediopunkt; Attribute title und pictogram, Inhalt zwischen den Tags.', 'wm-social-care-accessibility-hub'),
            'wm_a11y_toolbar'              => __('Barrierefreiheits-Toolbar (Schriftgröße, Kontrast, Vorlesen).', 'wm-social-care-accessibility-hub'),
            'wm_videothek_loesungswege'    => __('Alle acht eingebauten Beispiel-Lösungswege untereinander, ohne Filter.', 'wm-social-care-accessibility-hub'),
            'wm_video_player'              => __('Ein eingebautes Beispiel mit Schritten und Zusammenfassung in Leichter Sprache; Attribut id (1 bis 8).', 'wm-social-care-accessibility-hub'),
        ];
        ?>
        <div class="wrap wm-admin wm-social-care-admin-wrap">
            <header class="wm-admin-header">
                <div class="wm-header-title">
                    <span class="wm-badge wm-badge-cyan"><?php esc_html_e('Leichte Sprache & Pflege', 'wm-social-care-accessibility-hub'); ?></span>
                    <h1><?php esc_html_e('Social Care Accessibility Hub', 'wm-social-care-accessibility-hub'); ?></h1>
                    <p class="wm-subtitle"><?php esc_html_e('Was dieses Plugin auf dieser Website registriert. Es prüft keine Seite auf Barrierefreiheit und bewertet keine Rechtskonformität.', 'wm-social-care-accessibility-hub'); ?></p>
                </div>
                <div class="wm-header-badges">
                    <span class="wm-badge-family" title="<?php esc_attr_e('Part of the WM Suite plugin family', 'wm-social-care-accessibility-hub'); ?>">
                        <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><circle cx="12" cy="12" r="3"/><circle cx="12" cy="4" r="2"/><circle cx="19" cy="16" r="2"/><circle cx="5" cy="16" r="2"/><path d="M12 7v2M13.9 13.2l3.4 2M10.1 13.2l-3.4 2"/></svg>
                        <?php esc_html_e('WM Suite', 'wm-social-care-accessibility-hub'); ?>
                        <span class="wm-badge-family-state"><?php echo defined('WM_SUITE_HUB_VERSION') ? esc_html__('Hub', 'wm-social-care-accessibility-hub') : esc_html__('Standalone', 'wm-social-care-accessibility-hub'); ?></span>
                    </span>
                </div>
            </header>

            <div class="wm-card">
                <h2><span class="dashicons dashicons-yes-alt" aria-hidden="true"></span> <?php esc_html_e('Registrierte Funktionen', 'wm-social-care-accessibility-hub'); ?></h2>
                <?php // Until 1.3.1 this caption said the example videos point at an external server; none of the eight built-in examples has carried a video since 2026-11-19. ?>
                <p class="wm-card-caption"><?php esc_html_e('Jeder Status wird beim Aufruf dieser Seite gemessen, nicht behauptet. Skripte und Stile liegen im Plugin; die eingebauten Beispiele enthalten kein Video.', 'wm-social-care-accessibility-hub'); ?></p>
                <table class="wm-table">
                    <thead>
                        <tr>
                            <th scope="col"><?php esc_html_e('Funktion', 'wm-social-care-accessibility-hub'); ?></th>
                            <th scope="col"><?php esc_html_e('Status', 'wm-social-care-accessibility-hub'); ?></th>
                            <th scope="col"><?php esc_html_e('Was registriert ist', 'wm-social-care-accessibility-hub'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($rows as $row) : ?>
                            <tr>
                                <td><strong><?php echo esc_html($row['standard']); ?></strong></td>
                                <td>
                                    <span class="wm-status-pill <?php echo $row['ok'] ? 'wm-status-ok' : 'wm-status-warn'; ?>">
                                        <?php echo $row['ok'] ? esc_html__('Registriert', 'wm-social-care-accessibility-hub') : esc_html__('Nicht registriert', 'wm-social-care-accessibility-hub'); ?>
                                    </span>
                                </td>
                                <td><?php echo esc_html($row['note']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="wm-card">
                <h2><span class="dashicons dashicons-shortcode" aria-hidden="true"></span> <?php esc_html_e('Shortcodes für Seiten und Beiträge', 'wm-social-care-accessibility-hub'); ?></h2>
                <ul class="wm-card-body">
                    <?php foreach ($shortcodes as $tag => $description) : ?>
                        <li><code>[<?php echo esc_html($tag); ?>]</code> — <?php echo esc_html($description); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <div class="wm-card">
                <h2><span class="dashicons dashicons-admin-generic" aria-hidden="true"></span> <?php esc_html_e('Deinstallation & Datenerhalt', 'wm-social-care-accessibility-hub'); ?></h2>
                <p class="wm-card-caption"><?php esc_html_e('Standardmäßig bewahrt das Plugin alle Optionen und Inhalte beim Löschen auf. Aktivieren Sie die folgende Option nur, wenn Plugin-Einstellungen und temporäre Rate-Limit-Daten bei der Deinstallation endgültig entfernt werden sollen.', 'wm-social-care-accessibility-hub'); ?></p>
                <?php if (function_exists('admin_url') && function_exists('settings_fields')) : ?>
                    <form method="post" action="<?php echo esc_url(admin_url('options.php')); ?>">
                        <?php settings_fields(self::SETTINGS_GROUP); ?>
                        <p>
                            <label for="wm-social-care-delete-data-on-uninstall">
                                <input
                                    type="checkbox"
                                    id="wm-social-care-delete-data-on-uninstall"
                                    name="wm_social_care_delete_data_on_uninstall"
                                    value="1"
                                    <?php if (function_exists('checked')) { checked($delete_on_uninstall, true); } ?>
                                />
                                <?php esc_html_e('Plugin-Optionen und Rate-Limit-Transients beim Löschen des Plugins entfernen (Beiträge vom Typ Lösungsweg bleiben als Website-Inhalt erhalten).', 'wm-social-care-accessibility-hub'); ?>
                            </label>
                        </p>
                        <?php if (function_exists('submit_button')) { submit_button(__('Einstellung speichern', 'wm-social-care-accessibility-hub')); } ?>
                    </form>
                <?php endif; ?>
            </div>
        </div>
        <?php
    }
}
