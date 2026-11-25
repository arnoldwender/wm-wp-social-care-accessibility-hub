<?php
/**
 * Literal mutations for tests/run-mutations.sh: each entry undoes one correction of 2026-09-15; the
 * standalone suite must report a failure for every one. Search strings must occur exactly once.
 *
 * @package ArnoldWender\SocialCareA11y
 */

return [
	[
		'Bedarfscheck typed array again (TypeError on WordPress 6.4)',
		[
			[ 'includes/Frontend/Shortcodes.php', 'public static function render_bedarfscheck(array|string $atts = []): string {', 'public static function render_bedarfscheck(array $atts = []): string {' ],
		],
	],
	[
		'Library typed array again (TypeError on WordPress 6.4)',
		[
			[ 'includes/Frontend/Shortcodes.php', 'public static function render_videothek(array|string $atts = []): string {', 'public static function render_videothek(array $atts = []): string {' ],
		],
	],
	[
		'Single player typed array again (TypeError on WordPress 6.4)',
		[
			[ 'includes/Frontend/Shortcodes.php', 'public static function render_single_player(array|string $atts = []): string {', 'public static function render_single_player(array $atts = []): string {' ],
		],
	],
	[
		'Card typed array again (TypeError on WordPress 6.4)',
		[
			[ 'includes/Frontend/Shortcodes.php', 'public static function render_leichte_sprache_card(array|string $atts = [], ?string $content = null): string {', 'public static function render_leichte_sprache_card(array $atts = [], ?string $content = null): string {' ],
		],
	],
	[
		'Card escapes the paragraph markup of WordPress again',
		[
			[ 'includes/Frontend/Shortcodes.php', '$text    = self::plain_text((string) $content);', '$text    = (string) $content;' ],
		],
	],
	[
		'Bedarfscheck element IDs shared by every instance again',
		[
			[ 'includes/Frontend/Shortcodes.php', '$uid    = function_exists(\'wp_unique_id\') ? wp_unique_id(\'wm-bedarfscheck-\') : uniqid(\'wm-bedarfscheck-\');', '$uid    = \'wm-bedarfscheck\';' ],
		],
	],
	[
		'Select described by the whole result region again',
		[
			[ 'includes/Frontend/Shortcodes.php', '<select id="<?php echo esc_attr($ids[\'select\']); ?>" class="wm-form-select">', '<select id="<?php echo esc_attr($ids[\'select\']); ?>" class="wm-form-select" aria-describedby="<?php echo esc_attr($ids[\'result\']); ?>">' ],
		],
	],
	[
		'Result region announced when filled on load again',
		[
			[ 'includes/Frontend/Shortcodes.php', 'class="wm-calc-result">', 'class="wm-calc-result" role="status" aria-live="polite">' ],
		],
	],
	[
		'Select border of 2026-09-14 (1.48:1)',
		[
			[ 'includes/Frontend/Shortcodes.php', 'border: var(--border-width-2) solid var(--color-slate-600);', 'border: var(--border-width-2) solid var(--color-slate-300);' ],
		],
	],
	[
		'Bedarfscheck focus outline of 2026-09-14 (1.67:1)',
		[
			[ 'includes/Frontend/Shortcodes.php', 'outline: var(--focus-ring-width) solid var(--color-primary-800);', 'outline: var(--focus-ring-width) solid var(--color-primary-300);' ],
		],
	],
	[
		'Bedarfscheck button label not translatable again',
		[
			[ 'includes/Frontend/Shortcodes.php', '<?php esc_html_e(\'Ansprüche berechnen\', \'wm-social-care-accessibility-hub\'); ?>', 'Ansprüche berechnen' ],
		],
	],
	[
		'Library heading calls the examples a database again',
		[
			[ 'includes/Frontend/Shortcodes.php', '<?php esc_html_e(\'Beispiel-Lösungswege\', \'wm-social-care-accessibility-hub\'); ?></h2>', 'LösungsWege-Datenbank (LWD)</h2>' ],
		],
	],
	[
		'Player styles printed with every card again',
		[
			[ 'includes/Frontend/Accessible_Video_Player.php', '$print_styles = !self::$styles_printed;', '$print_styles = true;' ],
		],
	],
	[
		'Player label not translatable again',
		[
			[ 'includes/Frontend/Accessible_Video_Player.php', '<?php esc_html_e(\'Dauer:\', \'wm-social-care-accessibility-hub\'); ?>', 'Dauer:' ],
		],
	],
	[
		'Token sheet only enqueued after wp_head (lands in the footer) again',
		[
			[ 'includes/Frontend/Shortcodes.php', 'if (!$head_printed) {', 'if (true) {' ],
		],
	],
	[
		'Inline token sheet printed by every component',
		[
			[ 'includes/Frontend/Shortcodes.php', 'self::$inline_tokens_printed = true;', '' ],
		],
	],
	[
		'Token slate-400 missing from the sheet',
		[
			[ 'assets/css/wm-social-care-frontend.css', "    --color-slate-400: #94a3b8;\n", '' ],
		],
	],
	[
		'Read aloud reads the toolbar labels again',
		[
			[ 'includes/Frontend/Accessibility_Toolbar.php', 'if (bar) bar.hidden = true;', '' ],
		],
	],
	[
		'A failed speech synthesis keeps the Stopp label again',
		[
			[ 'includes/Frontend/Accessibility_Toolbar.php', 'utterance.onerror = () => setSpeaking(false);', '' ],
		],
	],
	[
		'Read aloud fixed to German again',
		[
			[ 'includes/Frontend/Accessibility_Toolbar.php', 'utterance.lang = html.lang || \'de-DE\';', 'utterance.lang = \'de-DE\';' ],
		],
	],
	[
		'High contrast hides the pressed state again',
		[
			[ 'includes/Frontend/Accessibility_Toolbar.php', 'html[data-wm-contrast="high"] .wm-a11y-btn[aria-pressed="true"],', 'html[data-wm-contrast="high"] .wm-a11y-btn[aria-pressed="mixed"],' ],
		],
	],
	[
		'Toolbar button border of 2026-09-14 (1.72:1)',
		[
			[ 'includes/Frontend/Accessibility_Toolbar.php', 'border: var(--border-width-2) solid var(--color-slate-400);', 'border: var(--border-width-2) solid var(--color-slate-700);' ],
		],
	],
	[
		'Toolbar labels not translatable again',
		[
			[ 'includes/Frontend/Accessibility_Toolbar.php', '\'stop\'        => __(\'Stopp\', \'wm-social-care-accessibility-hub\'),', '\'stop\'        => \'Stopp\',' ],
		],
	],
	[
		'Admin row for the single player measures nothing',
		[
			[ 'includes/Admin/Admin_Dashboard.php', '\'ok\'       => shortcode_exists(\'wm_video_player\'),', '\'ok\'       => true,' ],
		],
	],
	[
		'Admin row for the video library measures nothing',
		[
			[ 'includes/Admin/Admin_Dashboard.php', '\'ok\'       => shortcode_exists(\'wm_videothek_loesungswege\'),', '\'ok\'       => true,' ],
		],
	],
	[
		'1.3.1: the admin caption points at external example videos again',
		[
			[ 'includes/Admin/Admin_Dashboard.php', 'die eingebauten Beispiele enthalten kein Video.', 'die Beispielvideos verweisen auf einen externen Server.' ],
		],
	],
	[
		'calc cuts fractions to a care degree again',
		[
			[ 'includes/CLI/Social_Care_CLI.php', 'if (!is_scalar($raw) || !preg_match(\'/^[1-5]$/\', trim((string) $raw))) {', 'if (!is_scalar($raw) || (int) $raw < 1 || (int) $raw > 5) {' ],
		],
	],
	[
		'calc prints a zero joint-budget line again',
		[
			[ 'includes/CLI/Social_Care_CLI.php', 'if ($result[\'verhinderungspflege_year\'] > 0) {', 'if (true) {' ],
		],
	],
	[
		'calc prints amounts with a decimal point again',
		[
			[ 'includes/CLI/Social_Care_CLI.php', 'number_format($amount, 2, \',\', \'.\') . \' €\'', 'sprintf(\'%.2f\', $amount) . \' €\'' ],
		],
	],
	[
		'Enum without the direct-access guard again',
		[
			[ 'includes/Contracts/Language_Level_Enum.php', "namespace ArnoldWender\\SocialCareA11y\\Contracts;\n\nif (!defined('ABSPATH')) {\n    exit;\n}\n\n", "namespace ArnoldWender\\SocialCareA11y\\Contracts;\n\n" ],
		],
	],
	[
		'Seed data class without the direct-access guard again',
		[
			[ 'includes/Domain/Seed_Data_Manager.php', "if (!defined('ABSPATH')) {\n    exit;\n}\n\nfinal class Seed_Data_Manager {", 'final class Seed_Data_Manager {' ],
		],
	],
	[
		'Template without a string of the code',
		[
			[ 'languages/wm-social-care-accessibility-hub.pot', 'msgid "Ansprüche berechnen"', 'msgid "Berechnen"' ],
		],
	],
	[
		'de_DE catalog double-encoded again',
		[
			[ 'languages/wm-social-care-accessibility-hub-de_DE.po', 'msgid "Aktueller Pflegegrad:"', 'msgid "Aktueller PflegegrÃ¤d:"' ],
		],
	],
	[
		'README embeds the marketing banner again',
		[
			[ 'README.md', "## Images\n\n", "## Images\n\n![WM Social Care Accessibility Hub banner](assets/images/social-care-banner.jpg)\n\n" ],
		],
	],
	[
		'AGENTS.md diverges from CLAUDE.md',
		[
			[ 'AGENTS.md', 'There is no `/src/`.', 'Core business logic is organized under `/src/` and `/includes/`.' ],
		],
	],
	[
		'llms.txt claims PSR-12 again',
		[
			[ 'llms.txt', '- PHP 8.1+ with strict types; WordPress coding style', '- PHP 8.1+ / Strict Types / PSR-12 / WPCS' ],
		],
	],
	[
		'Marketing images in the package again',
		[
			[ '.distignore', "\nassets/images/\n", "\nassets/imgs-kept/\n" ],
		],
	],
	[
		'CI without the mutation check',
		[
			[ '.github/workflows/ci.yml', 'run: bash tests/run-mutations.sh', 'run: echo skipped' ],
		],
	],
	[
		'CHANGELOG heading with an emoji again',
		[
			[ 'CHANGELOG.md', '### Fixed (2026-09-15, reader pass)', '### 🐛 Fixed (2026-09-15, reader pass)' ],
		],
	],
	[
		'readme.txt short description over 150 characters',
		[
			[ 'readme.txt', 'and eight example guides.', 'and eight example guides with steps and an Easy Language summary for each one of them.' ],
		],
	],
	[
		'Uninstall deletes options without explicit opt-in',
		[
			[ 'uninstall.php', "if ( ! in_array( \$wm_social_care_delete, [ '1', 1, true, 'on', 'yes' ], true ) ) {\n    return;\n}", '' ],
		],
	],
	[
		'REST permission callback skips rate limiting',
		[
			[ 'includes/API/REST_Controller.php', 'if (!self::consume_rate_limit_slot()) {', 'if (false) {' ],
		],
	],
	[
		'Bedarfscheck filter wm_social_care_bedarfscheck_result skipped',
		[
			[ 'includes/Domain/Care_Needs_Checker.php', "\$filtered = apply_filters('wm_social_care_bedarfscheck_result', \$result, \$pflegegrad, \$include_verhinderung);", '$filtered = null;' ],
		],
	],
	[
		'Loesungswege filter wm_social_care_loesungswege_items skipped',
		[
			[ 'includes/Domain/Seed_Data_Manager.php', "\$filtered = apply_filters('wm_social_care_loesungswege_items', \$items);", '$filtered = null;' ],
		],
	],
	[
		'Block Bindings source wm-social-care/info not registered',
		[
			[ 'includes/Frontend/Ecosystem_Integrations.php', "register_block_bindings_source('wm-social-care/info', [", "if (false) register_block_bindings_source('wm-social-care/info', [" ],
		],
	],
];
