<?php
declare(strict_types=1);

namespace ArnoldWender\SocialCareA11y\Frontend;

use ArnoldWender\SocialCareA11y\Domain\Care_Needs_Checker;
use ArnoldWender\SocialCareA11y\Domain\Leichte_Sprache_Governor;
use ArnoldWender\SocialCareA11y\Domain\Seed_Data_Manager;

if (!defined('ABSPATH')) {
    exit;
}

final class Shortcodes {
    public const FRONTEND_STYLE_HANDLE = 'wm-social-care-frontend';

    /** Shortcodes whose inline styles need the frontend token sheet. */
    private const STYLED_SHORTCODES = ['wm_a11y_toolbar', 'wm_social_care_bedarfscheck', 'wm_videothek_loesungswege', 'wm_video_player'];

    /** Set once the token sheet went out inline because the head had already been printed. */
    private static bool $inline_tokens_printed = false;

    public static function register(): void {
        if (!function_exists('add_shortcode')) {
            return;
        }

        add_shortcode('wm_social_care_bedarfscheck', [self::class, 'render_bedarfscheck']);
        add_shortcode('wm_leichte_sprache_card', [self::class, 'render_leichte_sprache_card']);
        add_shortcode('wm_a11y_toolbar', [Accessibility_Toolbar::class, 'render']);
        add_shortcode('wm_videothek_loesungswege', [self::class, 'render_videothek']);
        add_shortcode('wm_video_player', [self::class, 'render_single_player']);

        if (function_exists('add_action')) {
            add_action('wp_enqueue_scripts', [self::class, 'enqueue_for_current_post']);
        }
    }

    /**
     * Frontend token sheet (assets/css/wm-social-care-frontend.css). Enqueued in the head for a singular
     * view whose content uses one of the styled shortcodes, and from each render that happens before the head is
     * printed.
     */
    public static function enqueue_frontend_tokens(): void {
        if (!function_exists('wp_enqueue_style') || !defined('WM_SOCIAL_CARE_URL') || WM_SOCIAL_CARE_URL === '') {
            return;
        }
        $file = WM_SOCIAL_CARE_PATH . 'assets/css/wm-social-care-frontend.css';
        wp_enqueue_style(
            self::FRONTEND_STYLE_HANDLE,
            WM_SOCIAL_CARE_URL . 'assets/css/wm-social-care-frontend.css',
            [],
            (string) (@filemtime($file) ?: WM_SOCIAL_CARE_VERSION)
        );
    }

    /**
     * The token sheet for a component that renders now. Before wp_head it is enqueued and printed in the head. After
     * wp_head (a shortcode in a widget, a template part or an archive) a sheet enqueued then only reaches the footer,
     * so until 2026-09-15 the component's own styles painted first with undefined custom properties; in that case the
     * sheet is returned once as an inline <style> for the component to print in front of its markup.
     */
    public static function frontend_tokens(): string {
        $head_printed = function_exists('did_action') && did_action('wp_head') > 0;
        if (!$head_printed) {
            self::enqueue_frontend_tokens();
            return '';
        }
        if (self::$inline_tokens_printed || (function_exists('wp_style_is') && wp_style_is(self::FRONTEND_STYLE_HANDLE, 'done'))) {
            return '';
        }
        $file = defined('WM_SOCIAL_CARE_PATH') ? WM_SOCIAL_CARE_PATH . 'assets/css/wm-social-care-frontend.css' : '';
        $css  = ($file !== '' && is_readable($file)) ? (string) file_get_contents($file) : ''; // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- the plugin's own stylesheet on disk.
        if ($css === '') {
            return '';
        }
        self::$inline_tokens_printed = true;
        return '<style id="wm-social-care-frontend-inline-css">' . wp_strip_all_tags($css) . '</style>';
    }

    public static function enqueue_for_current_post(): void {
        if (!function_exists('is_singular') || !is_singular()) {
            return;
        }
        $post = get_post();
        if (!$post instanceof \WP_Post) {
            return;
        }
        foreach (self::STYLED_SHORTCODES as $tag) {
            if (has_shortcode($post->post_content, $tag)) {
                self::enqueue_frontend_tokens();
                return;
            }
        }
    }

    /**
     * [wm_videothek_loesungswege]: the eight built-in examples of Seed_Data_Manager, one card each. Posts of the
     * wm_loesungsweg type are not read here. Until 2026-09-15 the heading called them a "LösungsWege-Datenbank (LWD)"
     * of "barrierefreie" guides; there is no database and nothing assesses accessibility.
     *
     * $atts is typed array|string because WordPress before 6.5 passes '' for a shortcode without attributes, which the
     * former array type turned into a fatal TypeError.
     *
     * @param array<string, string>|string $atts
     */
    public static function render_videothek(array|string $atts = []): string {
        $tokens = self::frontend_tokens();
        $seed   = Seed_Data_Manager::get_seed_data();
        ob_start();
        ?>
        <section class="wm-videothek-container" aria-label="<?php esc_attr_e('Beispiel-Lösungswege', 'wm-social-care-accessibility-hub'); ?>">
            <header class="wm-videothek-header">
                <h2 class="wm-videothek-title"><?php esc_html_e('Beispiel-Lösungswege', 'wm-social-care-accessibility-hub'); ?></h2>
                <p class="wm-videothek-subtitle"><?php esc_html_e('Acht eingebaute Beispiele mit Schritt-für-Schritt-Anleitung und einer Zusammenfassung in Leichter Sprache.', 'wm-social-care-accessibility-hub'); ?></p>
            </header>

            <div class="wm-videothek-grid">
                <?php foreach ($seed as $item): ?>
                    <?php echo Accessible_Video_Player::render($item); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- render() escapes every value it prints; wp_kses_post would strip its <style> block. ?>
                <?php endforeach; ?>
            </div>
        </section>

        <style>
        .wm-videothek-container {
            max-width: var(--container-library);
            margin: var(--space-8) auto;
            padding: 0 var(--space-4);
        }
        .wm-videothek-header {
            margin-bottom: var(--space-6);
            text-align: center;
        }
        /* The library paints no background of its own, so its heading keeps the theme's text colour. */
        .wm-videothek-title {
            font-size: var(--text-3xl);
            margin-bottom: var(--space-2);
        }
        .wm-videothek-subtitle {
            font-size: var(--text-lg);
        }
        .wm-videothek-grid {
            display: flex;
            flex-direction: column;
            gap: var(--space-6);
        }
        </style>
        <?php
        return $tokens . (ob_get_clean() ?: '');
    }

    /**
     * [wm_video_player id="…"]: one built-in example by its ID (1-8). An unknown ID renders nothing;
     * until 2026-09-14 it silently showed example 1 instead.
     *
     * @param array<string, string>|string $atts
     */
    public static function render_single_player(array|string $atts = []): string {
        $atts = shortcode_atts([
            'id' => '1',
        ], is_array($atts) ? $atts : []);

        foreach (Seed_Data_Manager::get_seed_data() as $item) {
            if ($item['id'] === (string) $atts['id']) {
                return self::frontend_tokens() . Accessible_Video_Player::render($item);
            }
        }

        return '';
    }

    /**
     * [wm_social_care_bedarfscheck]: the statutory amounts of Care_Needs_Checker for the selected care
     * degree, rendered in the browser from one embedded table.
     *
     * Element IDs are unique per instance: until 2026-09-15 two checks on one page shared them and the second one
     * updated the first. The result region gets role="status" only after the script has filled it on load, so the
     * initial result is not announced and every later change is; the select no longer points aria-describedby at the
     * whole result.
     *
     * @param array<string, string>|string $atts
     */
    public static function render_bedarfscheck(array|string $atts = []): string {
        $tokens = self::frontend_tokens();
        $uid    = function_exists('wp_unique_id') ? wp_unique_id('wm-bedarfscheck-') : uniqid('wm-bedarfscheck-');
        $ids    = [
            'select' => $uid . '-pflegegrad',
            'button' => $uid . '-berechnen',
            'result' => $uid . '-ergebnis',
        ];
        $labels = [
            /* translators: %d: care degree from 1 to 5. */
            'result_title'       => __('Ergebnis für Pflegegrad %d:', 'wm-social-care-accessibility-hub'),
            'entlastung_label'   => __('Entlastungsbetrag (§ 45b SGB XI):', 'wm-social-care-accessibility-hub'),
            /* translators: 1: monthly amount in euros, 2: yearly amount in euros. */
            'entlastung_value'   => __('%1$s € / Monat (%2$s € / Jahr für Alltagsbegleitung)', 'wm-social-care-accessibility-hub'),
            'geld_label'         => __('Pflegegeld (§ 37 SGB XI):', 'wm-social-care-accessibility-hub'),
            /* translators: %s: monthly amount in euros. */
            'per_month'          => __('%s € / Monat', 'wm-social-care-accessibility-hub'),
            'verhinderung_label' => __('Verhinderungs- und Kurzzeitpflege (§ 42a SGB XI, gemeinsamer Jahresbetrag):', 'wm-social-care-accessibility-hub'),
            /* translators: %s: yearly amount in euros. */
            'verhinderung_value' => __('Bis zu %s € / Jahr', 'wm-social-care-accessibility-hub'),
            'nrw_label'          => __('Anerkennung NRW:', 'wm-social-care-accessibility-hub'),
            'nrw_value'          => __('Voll abrechenbar über alle Pflegekassen nach AnFöVO.', 'wm-social-care-accessibility-hub'),
            /* translators: %s: date of the statutory amounts (YYYY-MM-DD). */
            'as_of'              => __('Stand der Beträge: %s (gilt unverändert 2026).', 'wm-social-care-accessibility-hub'),
        ];
        ob_start();
        ?>
        <div class="wm-bedarfscheck-card" role="region" aria-label="<?php esc_attr_e('Pflege-Bedarfscheck nach SGB XI', 'wm-social-care-accessibility-hub'); ?>">
            <h3 class="wm-bedarfscheck-title"><?php esc_html_e('Pflege-Bedarfscheck (SGB XI & AnFöVO NRW)', 'wm-social-care-accessibility-hub'); ?></h3>
            <p class="wm-bedarfscheck-desc"><?php esc_html_e('Wählen Sie Ihren Pflegegrad, um Ihre gesetzlichen Leistungsansprüche zu berechnen:', 'wm-social-care-accessibility-hub'); ?></p>

            <div class="wm-bedarfscheck-form">
                <label for="<?php echo esc_attr($ids['select']); ?>" class="wm-form-label"><?php esc_html_e('Aktueller Pflegegrad:', 'wm-social-care-accessibility-hub'); ?></label>
                <select id="<?php echo esc_attr($ids['select']); ?>" class="wm-form-select">
                    <option value="1"><?php esc_html_e('Pflegegrad 1 (Geringe Beeinträchtigung)', 'wm-social-care-accessibility-hub'); ?></option>
                    <option value="2" selected><?php esc_html_e('Pflegegrad 2 (Erhebliche Beeinträchtigung)', 'wm-social-care-accessibility-hub'); ?></option>
                    <option value="3"><?php esc_html_e('Pflegegrad 3 (Schwere Beeinträchtigung)', 'wm-social-care-accessibility-hub'); ?></option>
                    <option value="4"><?php esc_html_e('Pflegegrad 4 (Schwerste Beeinträchtigung)', 'wm-social-care-accessibility-hub'); ?></option>
                    <option value="5"><?php esc_html_e('Pflegegrad 5 (Härtefall / Vollpflege)', 'wm-social-care-accessibility-hub'); ?></option>
                </select>
                <button type="button" id="<?php echo esc_attr($ids['button']); ?>" class="wm-calc-btn"><?php esc_html_e('Ansprüche berechnen', 'wm-social-care-accessibility-hub'); ?></button>
            </div>

            <div id="<?php echo esc_attr($ids['result']); ?>" class="wm-calc-result">
                <!-- Dynamically populated -->
            </div>
        </div>

        <style>
        .wm-bedarfscheck-card {
            background: var(--color-white);
            color: var(--color-slate-800);
            border: var(--border-width-2) solid var(--color-slate-200);
            border-radius: var(--radius-xl);
            padding: var(--space-6);
            margin: var(--space-5) 0;
            font-family: inherit;
            box-shadow: var(--shadow-sm);
        }
        .wm-bedarfscheck-title {
            color: var(--color-slate-900);
            margin-top: 0;
            margin-bottom: var(--space-2);
            font-size: var(--text-xl);
        }
        .wm-bedarfscheck-desc {
            color: var(--color-slate-600);
            margin-bottom: var(--space-4);
        }
        .wm-form-label {
            display: block;
            font-weight: 600;
            margin-bottom: var(--space-1-5);
            color: var(--color-slate-800);
        }
        /* The field border is the only boundary of the select on the white card: at least 3:1 (was 1.48:1). */
        .wm-form-select {
            width: 100%;
            max-width: var(--field-max-width);
            padding: var(--space-2-5) var(--space-3-5);
            border: var(--border-width-2) solid var(--color-slate-600);
            border-radius: var(--radius-md);
            font-size: var(--text-base);
            min-height: var(--touch-target-comfort);
            margin-bottom: var(--space-3);
            display: block;
        }
        .wm-calc-btn {
            background: var(--color-primary-600);
            color: var(--color-white);
            font-weight: bold;
            padding: var(--space-3) var(--space-6);
            border: none;
            border-radius: var(--radius-md);
            min-height: var(--touch-target-comfort);
            cursor: pointer;
            font-size: var(--text-base);
        }
        .wm-calc-btn:hover, .wm-calc-btn:focus-visible {
            background: var(--color-primary-700);
        }
        /* The outline sits on the white card, so it is measured against white: at least 3:1 (was 1.67:1). */
        .wm-calc-btn:focus-visible {
            outline: var(--focus-ring-width) solid var(--color-primary-800);
            outline-offset: var(--focus-ring-offset);
        }
        .wm-calc-result {
            margin-top: var(--space-5);
            padding: var(--space-4);
            background: var(--color-emerald-50);
            border: var(--border-width-1) solid var(--color-emerald-200);
            border-radius: var(--radius-md);
            color: var(--color-emerald-800);
        }
        .wm-calc-result-title {
            margin: 0 0 var(--space-2) 0;
            color: var(--color-emerald-900);
        }
        .wm-calc-result-list {
            margin: 0;
            padding-left: var(--list-indent);
            line-height: var(--leading-relaxed);
        }
        </style>

        <script>
        (function() {
            const ids = <?php echo wp_json_encode($ids); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- element IDs, JSON-encoded. ?>;
            const labels = <?php echo wp_json_encode($labels, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- translated plain strings, JSON-encoded with < > & as escapes. ?>;
            // Statutory amounts come from Care_Needs_Checker (one source for PHP, WP-CLI and this
            // script); until 2026-09-14 this table was typed by hand with the 2024 values.
            const table = <?php echo wp_json_encode( Care_Needs_Checker::entitlement_table() ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- numeric table, JSON-encoded. ?>;
            const asOf = <?php echo wp_json_encode( Care_Needs_Checker::STATUTORY_AS_OF ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- ISO date, JSON-encoded. ?>;
            const euro = (n) => new Intl.NumberFormat('de-DE').format(n);
            // printf-style placeholders of the translated strings: %s, %d and numbered %1$s.
            const fmt = (template, ...values) => {
                let next = 0;
                return template.replace(/%(?:(\d+)\$)?[sd]/g, (match, position) => String(values[position ? Number(position) - 1 : next++]));
            };

            const select = document.getElementById(ids.select);
            const result = document.getElementById(ids.result);

            function row(label, value) {
                const li = document.createElement('li');
                const strong = document.createElement('strong');
                strong.textContent = label;
                li.appendChild(strong);
                li.appendChild(document.createTextNode(' ' + value));
                return li;
            }

            function updateCalc() {
                if (!select || !result) return;

                const pg = parseInt(select.value, 10);
                const data = table[pg];
                if (!data) return;

                const heading = document.createElement('h4');
                heading.className = 'wm-calc-result-title';
                heading.textContent = fmt(labels.result_title, pg);

                const list = document.createElement('ul');
                list.className = 'wm-calc-result-list';
                list.appendChild(row(labels.entlastung_label, fmt(labels.entlastung_value, euro(data.entlastung), euro(data.entlastung_jahr))));
                list.appendChild(row(labels.geld_label, fmt(labels.per_month, euro(data.geld))));
                if (data.verhinderung > 0) {
                    list.appendChild(row(labels.verhinderung_label, fmt(labels.verhinderung_value, euro(data.verhinderung))));
                }
                list.appendChild(row(labels.nrw_label, labels.nrw_value));

                const note = document.createElement('li');
                const small = document.createElement('small');
                small.textContent = fmt(labels.as_of, asOf);
                note.appendChild(small);
                list.appendChild(note);

                result.replaceChildren(heading, list);
            }

            document.getElementById(ids.button)?.addEventListener('click', updateCalc);
            select?.addEventListener('change', updateCalc);
            updateCalc();
            result?.setAttribute('role', 'status');
        })();
        </script>
        <?php
        return $tokens . (ob_get_clean() ?: '');
    }

    /**
     * [wm_leichte_sprache_card title="…" pictogram="…"]Text[/wm_leichte_sprache_card]: the enclosed text,
     * escaped, with a Mediopunkt in the compound words of Leichte_Sprache_Governor. Without a pictogram
     * attribute an information icon is drawn (until 2026-09-14 the default was an emoji). The card has
     * no styles of its own and follows the theme.
     *
     * @param array<string, string>|string $atts
     */
    public static function render_leichte_sprache_card(array|string $atts = [], ?string $content = null): string {
        $atts = shortcode_atts([
            'title'     => __('Information in Leichter Sprache', 'wm-social-care-accessibility-hub'),
            'pictogram' => '',
        ], is_array($atts) ? $atts : []);

        $text    = self::plain_text((string) $content);
        $body    = $text !== '' ? Leichte_Sprache_Governor::apply_mediopunkt($text) : '';
        $picto   = (string) $atts['pictogram'] !== ''
            ? esc_html($atts['pictogram'])
            : '<svg viewBox="0 0 24 24" width="24" height="24" focusable="false"><circle cx="12" cy="12" r="10" fill="none" stroke="currentColor" stroke-width="2"/><path d="M11 10h2v7h-2zm0-4h2v2h-2z" fill="currentColor"/></svg>';

        return sprintf(
            '<article class="wm-leichte-sprache-box" aria-label="%s">
                <header class="wm-ls-header">
                    <span class="wm-ls-picto" aria-hidden="true">%s</span>
                    <h3 class="wm-ls-title">%s</h3>
                </header>
                <div class="wm-ls-content">%s</div>
                <footer class="wm-ls-footer">
                    <small>%s</small>
                </footer>
            </article>',
            esc_attr($atts['title']),
            $picto, // Escaped above, or the fixed SVG.
            esc_html($atts['title']),
            wpautop(esc_html($body)),
            esc_html__('Text nach den Regeln der Leichten Sprache (DIN SPEC 33429).', 'wm-social-care-accessibility-hub')
        );
    }

    /**
     * The enclosed text of a shortcode as plain text with its line breaks. WordPress runs wpautop() on post content
     * before do_shortcode(), and the shortcode block runs it on its own content, so the text can arrive with <p> and
     * <br /> already in it. Until 2026-09-15 that markup was escaped and printed as visible text ("<br />", "</p>").
     */
    private static function plain_text(string $content): string {
        $text = (string) preg_replace('#<br\s*/?>\s*#i', "\n", $content);
        $text = (string) preg_replace('#</p>\s*<p[^>]*>#i', "\n\n", $text);
        $text = wp_strip_all_tags($text);
        return trim((string) preg_replace("/\n{3,}/", "\n\n", $text));
    }
}
