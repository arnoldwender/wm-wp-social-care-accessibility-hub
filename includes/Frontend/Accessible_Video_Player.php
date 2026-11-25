<?php
declare(strict_types=1);

namespace ArnoldWender\SocialCareA11y\Frontend;

use ArnoldWender\SocialCareA11y\Domain\Leichte_Sprache_Governor;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * One example card: native HTML5 player (browser controls), optional captions track, numbered steps and
 * the Leichte Sprache summary. Tokens come from the frontend sheet.
 */
final class Accessible_Video_Player {
    /** The card styles go out once per request; until 2026-09-15 the library printed them eight times. */
    private static bool $styles_printed = false;

    public static function render(array $video_data): string {
        // Values stay raw here and are escaped where they are printed (late escaping): escaping
        // them once at the top and echoing them bare hid the escape from every later reader.
        $video_id     = (string)($video_data['id'] ?? uniqid('wm-video-'));
        $title        = (string)($video_data['title'] ?? 'Video');
        $video_url    = (string)($video_data['videoUrl'] ?? $video_data['video_url'] ?? '');
        $captions_url = (string)($video_data['captionsUrl'] ?? $video_data['captions_url'] ?? '');
        $steps        = (array)($video_data['steps'] ?? []);
        $raw_ls       = (string)($video_data['leichteSprache'] ?? $video_data['leichte_sprache_text'] ?? '');
        $leichte_text = $raw_ls !== '' ? Leichte_Sprache_Governor::apply_mediopunkt($raw_ls) : '';
        $duration     = (string)($video_data['duration'] ?? '');
        /* translators: %s: title of the example. */
        $card_label   = sprintf(__('Lösungsweg: %s', 'wm-social-care-accessibility-hub'), $title);
        $print_styles = !self::$styles_printed;
        self::$styles_printed = true;

        ob_start();
        ?>
        <article class="wm-accessible-player-card" aria-label="<?php echo esc_attr($card_label); ?>">
            <header class="wm-player-header">
                <h3 class="wm-player-title"><?php echo esc_html($title); ?></h3>
                <?php if ($duration !== ''): ?>
                    <span class="wm-player-badge"><span class="wm-sr-only"><?php esc_html_e('Dauer:', 'wm-social-care-accessibility-hub'); ?> </span><?php echo esc_html($duration); ?></span>
                <?php endif; ?>
            </header>

            <?php if ($video_url !== ''): ?>
                <div class="wm-video-wrapper">
                    <video
                        id="<?php echo esc_attr($video_id); ?>"
                        class="wm-html5-video"
                        controls
                        preload="metadata"
                        playsinline
                        aria-label="<?php echo esc_attr($title); ?>"
                    >
                        <source src="<?php echo esc_url($video_url); ?>" type="video/mp4">
                        <?php if ($captions_url !== ''): ?>
                            <track
                                label="<?php esc_attr_e('Deutsch (Untertitel)', 'wm-social-care-accessibility-hub'); ?>"
                                kind="subtitles"
                                srclang="de"
                                src="<?php echo esc_url($captions_url); ?>"
                                default
                            >
                        <?php endif; ?>
                        <?php esc_html_e('Ihr Browser unterstützt kein direktes HTML5-Video.', 'wm-social-care-accessibility-hub'); ?>
                    </video>
                </div>
            <?php endif; ?>

            <?php
            // Until 2026-09-14 a hidden live region here announced keys (M for mute, C for captions,
            // arrows for 5 seconds) that no script of the plugin implements; the player uses the
            // browser's own controls, so nothing is announced.
            ?>
            <?php if (!empty($steps)): ?>
                <div class="wm-video-steps-section">
                    <h4 class="wm-steps-heading"><?php esc_html_e('Schritt-für-Schritt-Anleitung:', 'wm-social-care-accessibility-hub'); ?></h4>
                    <ol class="wm-steps-list">
                        <?php foreach ($steps as $index => $step): ?>
                            <li class="wm-step-item">
                                <span class="wm-step-num"><?php echo (int) $index + 1; ?>.</span>
                                <span class="wm-step-text"><?php echo esc_html((string)$step); ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ol>
                </div>
            <?php endif; ?>

            <?php if (!empty($leichte_text)): ?>
                <div class="wm-video-ls-section">
                    <h4 class="wm-ls-heading"><?php esc_html_e('In Leichter Sprache (DIN SPEC 33429):', 'wm-social-care-accessibility-hub'); ?></h4>
                    <p class="wm-ls-body"><?php echo esc_html($leichte_text); ?></p>
                </div>
            <?php endif; ?>
        </article>

        <?php if ($print_styles): ?>
        <style>
        .wm-accessible-player-card {
            background: var(--color-white);
            color: var(--color-slate-800);
            border: var(--border-width-2) solid var(--color-slate-300);
            border-radius: var(--radius-lg);
            padding: var(--space-6);
            margin: var(--space-6) 0;
            box-shadow: var(--shadow-sm);
            font-family: var(--font-sans);
        }
        .wm-player-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: var(--space-4);
            flex-wrap: wrap;
            gap: var(--space-2);
        }
        .wm-player-title {
            margin: 0;
            font-size: var(--text-xl);
            color: var(--color-slate-900);
        }
        .wm-player-badge {
            background: var(--color-slate-100);
            color: var(--color-slate-600);
            padding: var(--space-1) var(--space-2-5);
            border-radius: var(--radius-full);
            font-size: var(--text-sm);
            font-weight: 600;
        }
        .wm-video-wrapper {
            background: var(--color-black);
            border-radius: var(--radius-md);
            overflow: hidden;
            margin-bottom: var(--space-5);
        }
        .wm-html5-video {
            width: 100%;
            max-height: var(--video-max-height);
            display: block;
        }
        .wm-video-steps-section {
            margin-top: var(--space-5);
            padding-top: var(--space-4);
            border-top: var(--border-width-1) solid var(--color-slate-200);
        }
        .wm-steps-heading {
            font-size: var(--text-lg);
            color: var(--color-slate-800);
            margin-top: 0;
            margin-bottom: var(--space-3);
        }
        .wm-steps-list {
            padding-left: 0;
            list-style: none;
            margin: 0;
            display: flex;
            flex-direction: column;
            gap: var(--space-2-5);
        }
        .wm-step-item {
            display: flex;
            gap: var(--space-3);
            line-height: var(--leading-relaxed);
            color: var(--color-slate-700);
        }
        .wm-step-num {
            font-weight: 700;
            color: var(--color-primary-600);
            min-width: var(--step-number-min-width);
        }
        .wm-video-ls-section {
            margin-top: var(--space-4-5);
            padding: var(--space-4);
            background: var(--color-primary-50);
            border-left: var(--border-width-4) solid var(--color-primary-600);
            border-radius: var(--radius-md);
        }
        .wm-ls-heading {
            color: var(--color-primary-800);
            margin: 0 0 var(--space-2) 0;
            font-size: var(--text-lg);
        }
        .wm-ls-body {
            color: var(--color-primary-900);
            margin: 0;
            line-height: var(--leading-relaxed);
        }
        /* Visually hidden, still read by screen readers (the standard clip technique). */
        .wm-sr-only {
            position: absolute;
            width: 1px;
            height: 1px;
            padding: 0;
            margin: -1px;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            white-space: nowrap;
            border-width: 0;
        }
        </style>
        <?php endif; ?>
        <?php
        return ob_get_clean() ?: '';
    }
}
