<?php
declare(strict_types=1);

namespace ArnoldWender\SocialCareA11y\Frontend;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * [wm_a11y_toolbar]: text size, high contrast and read-aloud for the visitor's current page view.
 * Nothing is stored; a reload resets every setting.
 */
final class Accessibility_Toolbar {
    private static int $instance_count = 0;
    private static bool $assets_printed = false;

    /** Reset render counters between isolated test renders. */
    public static function reset_render_state(): void {
        self::$instance_count = 0;
        self::$assets_printed = false;
    }

    public static function render(): string {
        self::$instance_count++;
        $suffix       = 1 === self::$instance_count ? '' : '-' . self::$instance_count;
        $toolbar_id   = 'wm-a11y-toolbar' . $suffix;
        $print_assets = ! self::$assets_printed;
        self::$assets_printed = true;

        $tokens = Shortcodes::frontend_tokens();
        $labels = [
            'read'        => __('Vorlesen', 'wm-social-care-accessibility-hub'),
            'stop'        => __('Stopp', 'wm-social-care-accessibility-hub'),
            'unsupported' => __('Ihr Browser unterstützt keine direkte Sprachausgabe.', 'wm-social-care-accessibility-hub'),
        ];

        ob_start();
        ?>
        <aside id="<?php echo esc_attr($toolbar_id); ?>" class="wm-a11y-toolbar" aria-label="<?php esc_attr_e('Barrierefreiheits-Optionen', 'wm-social-care-accessibility-hub'); ?>" data-label-read="<?php echo esc_attr($labels['read']); ?>" data-label-stop="<?php echo esc_attr($labels['stop']); ?>" data-label-unsupported="<?php echo esc_attr($labels['unsupported']); ?>">
            <div class="wm-a11y-container">
                <!-- Text size -->
                <div class="wm-a11y-group" role="group" aria-label="<?php esc_attr_e('Schriftgröße anpassen', 'wm-social-care-accessibility-hub'); ?>">
                    <button type="button" id="<?php echo esc_attr('wm-a11y-font-dec' . $suffix); ?>" class="wm-a11y-btn" data-wm-a11y-action="font-dec" aria-label="<?php echo esc_attr('A- — ' . __('Schriftgröße verkleinern', 'wm-social-care-accessibility-hub')); ?>">A-</button>
                    <button type="button" id="<?php echo esc_attr('wm-a11y-font-reset' . $suffix); ?>" class="wm-a11y-btn" data-wm-a11y-action="font-reset" aria-label="<?php echo esc_attr('A — ' . __('Schriftgröße zurücksetzen', 'wm-social-care-accessibility-hub')); ?>">A</button>
                    <button type="button" id="<?php echo esc_attr('wm-a11y-font-inc' . $suffix); ?>" class="wm-a11y-btn" data-wm-a11y-action="font-inc" aria-label="<?php echo esc_attr('A+ — ' . __('Schriftgröße vergrößern', 'wm-social-care-accessibility-hub')); ?>">A+</button>
                </div>

                <!-- High contrast -->
                <div class="wm-a11y-group" role="group" aria-label="<?php esc_attr_e('Farbkontrast wählen', 'wm-social-care-accessibility-hub'); ?>">
                    <button type="button" id="<?php echo esc_attr('wm-a11y-contrast-toggle' . $suffix); ?>" class="wm-a11y-btn wm-btn-contrast" data-wm-a11y-action="contrast" aria-pressed="false">
                        <svg class="wm-a11y-icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M12 2a10 10 0 1 0 0 20 10 10 0 0 0 0-20Zm0 2v16a8 8 0 0 1 0-16Z" fill="currentColor"/></svg>
                        <?php esc_html_e('Kontrast', 'wm-social-care-accessibility-hub'); ?>
                    </button>
                </div>

                <!-- Read aloud (browser Web Speech API, no request leaves the browser from this script) -->
                <div class="wm-a11y-group" role="group" aria-label="<?php esc_attr_e('Vorlesefunktion', 'wm-social-care-accessibility-hub'); ?>">
                    <button type="button" id="<?php echo esc_attr('wm-a11y-speech-toggle' . $suffix); ?>" class="wm-a11y-btn wm-btn-speech" data-wm-a11y-action="speech" data-speaking="false">
                        <svg class="wm-a11y-icon wm-a11y-icon-play" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M4 9v6h4l5 4V5L8 9H4Zm12.5 3a4.5 4.5 0 0 0-2.5-4v8a4.5 4.5 0 0 0 2.5-4Z" fill="currentColor"/></svg>
                        <svg class="wm-a11y-icon wm-a11y-icon-stop" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M6 6h12v12H6z" fill="currentColor"/></svg>
                        <span class="wm-a11y-speech-label"><?php echo esc_html($labels['read']); ?></span>
                    </button>
                </div>

                <span class="wm-a11y-status" role="status" aria-live="polite" hidden></span>
            </div>
        </aside>

        <?php if ( $print_assets ) : ?>
        <style>
        .wm-a11y-toolbar {
            position: sticky;
            top: 0;
            z-index: var(--layer-toolbar);
            background: var(--color-slate-900);
            color: var(--color-white);
            padding: var(--space-2) var(--space-4);
            font-family: var(--font-sans);
            box-shadow: var(--shadow-md);
        }
        .wm-a11y-container {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: flex-end;
            gap: var(--space-3);
            max-width: var(--container-toolbar);
            margin: 0 auto;
        }
        .wm-a11y-group {
            display: flex;
            align-items: center;
            gap: var(--space-1);
        }
        /* The button fill is close to the toolbar colour, so the border carries the boundary: at least 3:1 (was 1.72:1). */
        .wm-a11y-btn {
            background: var(--color-slate-800);
            color: var(--color-slate-50);
            border: var(--border-width-2) solid var(--color-slate-400);
            border-radius: var(--radius-md);
            padding: var(--space-1-5) var(--space-3);
            font-size: var(--text-sm);
            font-weight: 600;
            cursor: pointer;
            min-height: var(--touch-target-min);
            min-width: var(--touch-target-min);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: var(--space-1-5);
            transition: var(--transition-colors);
        }
        .wm-a11y-btn:hover, .wm-a11y-btn:focus-visible, .wm-a11y-btn[aria-pressed="true"] {
            background: var(--color-primary-700);
            border-color: var(--color-white);
        }
        .wm-a11y-btn:focus-visible {
            outline: var(--focus-ring-width) solid var(--color-primary-400);
            outline-offset: var(--focus-ring-offset);
        }
        .wm-a11y-icon {
            width: 1em;
            height: 1em;
            flex: 0 0 auto;
        }
        .wm-btn-speech .wm-a11y-icon-stop,
        .wm-btn-speech[data-speaking="true"] .wm-a11y-icon-play {
            display: none;
        }
        .wm-btn-speech[data-speaking="true"] .wm-a11y-icon-stop {
            display: inline;
        }
        .wm-a11y-status {
            font-size: var(--text-sm);
            color: var(--color-slate-50);
            background: var(--color-slate-800);
            border: var(--border-width-1) solid var(--color-slate-400);
            border-radius: var(--radius-md);
            padding: var(--space-1-5) var(--space-3);
        }
        /* High-contrast mode */
        html[data-wm-contrast="high"] {
            filter: contrast(140%) !important;
            background-color: var(--wm-sc-contrast-background) !important;
            color: var(--wm-sc-contrast-foreground) !important;
        }
        html[data-wm-contrast="high"] * {
            background-color: var(--wm-sc-contrast-background) !important;
            color: var(--wm-sc-contrast-foreground) !important;
            border-color: var(--wm-sc-contrast-foreground) !important;
        }
        /* The rule above gives every element the same colours, so the pressed contrast button and the speaking
           read-aloud button looked like the others until 2026-09-15; they invert the two colours instead. */
        html[data-wm-contrast="high"] .wm-a11y-btn[aria-pressed="true"],
        html[data-wm-contrast="high"] .wm-btn-speech[data-speaking="true"] {
            background-color: var(--wm-sc-contrast-foreground) !important;
            color: var(--wm-sc-contrast-background) !important;
        }
        html[data-wm-fontsize="large"] {
            font-size: 120% !important;
        }
        html[data-wm-fontsize="xlarge"] {
            font-size: 140% !important;
        }
        @media (prefers-reduced-motion: reduce) {
            .wm-a11y-btn { transition: none !important; }
        }
        </style>

        <script>
        (function() {
            if (typeof window === 'undefined') return;

            const labels = <?php echo wp_json_encode($labels, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- translated plain strings, JSON-encoded with < > & as escapes. ?>;
            const html = document.documentElement;
            let currentFontSize = 'normal';
            let isHighContrast = false;
            let isSpeaking = false;

            const syncContrastButtons = () => {
                document.querySelectorAll('.wm-a11y-toolbar [data-wm-a11y-action="contrast"]').forEach((btn) => {
                    btn.setAttribute('aria-pressed', isHighContrast ? 'true' : 'false');
                });
            };

            const setSpeaking = (speaking) => {
                isSpeaking = speaking;
                document.querySelectorAll('.wm-a11y-toolbar').forEach((bar) => {
                    const speechBtn = bar.querySelector('[data-wm-a11y-action="speech"]');
                    const speechLabel = speechBtn?.querySelector('.wm-a11y-speech-label');
                    if (speechBtn) {
                        speechBtn.setAttribute('data-speaking', speaking ? 'true' : 'false');
                    }
                    if (speechLabel) {
                        speechLabel.textContent = speaking ? (bar.dataset.labelStop || labels.stop) : (bar.dataset.labelRead || labels.read);
                    }
                });
            };

            const pageText = (bar) => {
                const root = document.querySelector('main') || document.body;
                const wasHidden = bar ? bar.hidden : false;
                if (bar) bar.hidden = true;
                const text = root.innerText || '';
                if (bar) bar.hidden = wasHidden;
                return text;
            };

            document.querySelectorAll('.wm-a11y-toolbar').forEach((bar) => {
                if (bar.dataset.wmA11yBound === '1') {
                    return;
                }
                bar.dataset.wmA11yBound = '1';

                // 1. Text size: three steps on <html>; only text sized in rem or em follows.
                bar.querySelector('[data-wm-a11y-action="font-inc"]')?.addEventListener('click', () => {
                    if (currentFontSize === 'normal') {
                        html.setAttribute('data-wm-fontsize', 'large');
                        currentFontSize = 'large';
                    } else if (currentFontSize === 'large') {
                        html.setAttribute('data-wm-fontsize', 'xlarge');
                        currentFontSize = 'xlarge';
                    }
                });

                bar.querySelector('[data-wm-a11y-action="font-dec"]')?.addEventListener('click', () => {
                    if (currentFontSize === 'xlarge') {
                        html.setAttribute('data-wm-fontsize', 'large');
                        currentFontSize = 'large';
                    } else {
                        html.removeAttribute('data-wm-fontsize');
                        currentFontSize = 'normal';
                    }
                });

                bar.querySelector('[data-wm-a11y-action="font-reset"]')?.addEventListener('click', () => {
                    html.removeAttribute('data-wm-fontsize');
                    currentFontSize = 'normal';
                });

                // 2. High contrast
                bar.querySelector('[data-wm-a11y-action="contrast"]')?.addEventListener('click', () => {
                    isHighContrast = !isHighContrast;
                    if (isHighContrast) {
                        html.setAttribute('data-wm-contrast', 'high');
                    } else {
                        html.removeAttribute('data-wm-contrast');
                    }
                    syncContrastButtons();
                });

                // 3. Read aloud: the first 3000 characters of <main> (or <body>) in the language of the page.
                const speechButton = bar.querySelector('[data-wm-a11y-action="speech"]');
                speechButton?.addEventListener('click', () => {
                    if (!('speechSynthesis' in window)) {
                        const statusEl = bar.querySelector('.wm-a11y-status');
                        if (statusEl) {
                            statusEl.textContent = bar.dataset.labelUnsupported || labels.unsupported;
                            statusEl.hidden = false;
                        }
                        return;
                    }

                    if (isSpeaking) {
                        window.speechSynthesis.cancel();
                        setSpeaking(false);
                    } else {
                        const utterance = new SpeechSynthesisUtterance(pageText(bar).slice(0, 3000));
                        utterance.lang = html.lang || 'de-DE';
                        // A failed synthesis left the button on "Stopp" until 2026-09-15.
                        utterance.onend = () => setSpeaking(false);
                        utterance.onerror = () => setSpeaking(false);
                        window.speechSynthesis.speak(utterance);
                        setSpeaking(true);
                    }
                });
            });
        })();
        </script>
        <?php endif; ?>
        <?php
        return $tokens . (ob_get_clean() ?: '');
    }
}
