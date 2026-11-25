<?php
/**
 * Test suite: wm-social-care-accessibility-hub
 * Standalone, no WordPress: WordPress functions are stubbed below, the Suite Hub contract by
 * tests/stubs/suite-hub-contracts.php. Loading without the hub is tested in its own process
 * (tests/test-standalone-load.php), because this file declares the contract.
 *
 * Each test method runs isolated: an exception, warning or notice fails that method instead of ending the suite before
 * its results line. Every correction of 2026-09-15 has an assertion here and an entry in tests/mutations.php, and
 * `bash tests/run-mutations.sh` requires this suite to fail for each one; the runner and CI read the results line.
 */

declare(strict_types=1);

// Mock WordPress functions if not defined
if (!defined('ABSPATH')) {
    define('ABSPATH', __DIR__ . '/../');
}

if (!defined('WP_PLUGIN_DIR')) {
    define('WP_PLUGIN_DIR', dirname(__DIR__, 2));
}

// Registries behind the stubs, so health, admin rows, enqueues and gettext calls can be measured.
$GLOBALS['wm_test_shortcodes']      = [];
$GLOBALS['wm_test_post_types']      = [];
$GLOBALS['wm_test_did_init']        = 0;
$GLOBALS['wm_test_did_head']        = 0;
$GLOBALS['wm_test_gettext']         = [];
$GLOBALS['wm_test_styles']          = [];
$GLOBALS['wm_test_style_done']      = false;
$GLOBALS['wm_test_filters']         = [];
$GLOBALS['wm_test_fired_actions']   = [];
$GLOBALS['wm_test_options']         = [];
$GLOBALS['wm_test_deleted_options'] = [];
$GLOBALS['wm_test_transients']      = [];
$GLOBALS['wm_test_settings']        = [];
$GLOBALS['wm_test_block_bindings']  = [];
$GLOBALS['wm_test_gb_tags']         = [];

/** Records a gettext call of the plugin's text domain and returns the text unchanged. */
function wm_test_gettext(string $text, string $domain): string {
    if ($domain === 'wm-social-care-accessibility-hub') {
        $GLOBALS['wm_test_gettext'][] = $text;
    }
    return $text;
}

if (!class_exists('WP_Error')) {
    class WP_Error {
        public function __construct(
            public string $code = '',
            public string $message = '',
            public mixed $data = null
        ) {}
        public function get_error_code(): string { return $this->code; }
        public function get_error_data(): mixed { return $this->data; }
    }
}
if (!class_exists('GenerateBlocks_Register_Dynamic_Tag')) {
    class GenerateBlocks_Register_Dynamic_Tag {
        public function __construct(array $config) {
            $GLOBALS['wm_test_gb_tags'][] = $config;
        }
    }
}
if (!function_exists('add_action')) {
    function add_action(...$args) {}
}
if (!function_exists('do_action')) {
    function do_action($tag, ...$args) {
        $GLOBALS['wm_test_fired_actions'][] = (string) $tag;
    }
}
if (!function_exists('add_filter')) {
    function add_filter($tag, $callback = null, ...$rest) {
        if ($callback !== null) {
            $GLOBALS['wm_test_filters'][(string) $tag][] = $callback;
        }
    }
}
if (!function_exists('apply_filters')) {
    function apply_filters($tag, $value, ...$args) {
        foreach ($GLOBALS['wm_test_filters'][(string) $tag] ?? [] as $cb) {
            $value = $cb($value, ...$args);
        }
        return $value;
    }
}
if (!function_exists('get_option')) {
    function get_option($name, $default = false) {
        return array_key_exists((string) $name, $GLOBALS['wm_test_options'])
            ? $GLOBALS['wm_test_options'][(string) $name]
            : $default;
    }
}
if (!function_exists('update_option')) {
    function update_option($name, $value, ...$args) {
        $GLOBALS['wm_test_options'][(string) $name] = $value;
        return true;
    }
}
if (!function_exists('delete_option')) {
    function delete_option($name) {
        $GLOBALS['wm_test_deleted_options'][] = (string) $name;
        unset($GLOBALS['wm_test_options'][(string) $name]);
        return true;
    }
}
if (!function_exists('get_transient')) {
    function get_transient($name) {
        return $GLOBALS['wm_test_transients'][(string) $name] ?? false;
    }
}
if (!function_exists('set_transient')) {
    function set_transient($name, $value, $expiration = 0) {
        $GLOBALS['wm_test_transients'][(string) $name] = $value;
        return true;
    }
}
if (!function_exists('register_setting')) {
    function register_setting($group, $name, $args = []) {
        $GLOBALS['wm_test_settings'][(string) $name] = ['group' => $group, 'args' => $args];
    }
}
if (!function_exists('sanitize_text_field')) {
    function sanitize_text_field($str) { return trim(strip_tags((string) $str)); }
}
if (!function_exists('wp_salt')) {
    function wp_salt($scheme = 'auth') { return 'wm-test-salt-' . (string) $scheme; }
}
if (!function_exists('register_block_bindings_source')) {
    function register_block_bindings_source($name, $args = []) {
        $GLOBALS['wm_test_block_bindings'][(string) $name] = $args;
        return true;
    }
}
if (!function_exists('get_block_bindings_source')) {
    function get_block_bindings_source($name) {
        return $GLOBALS['wm_test_block_bindings'][(string) $name] ?? null;
    }
}
if (!function_exists('add_shortcode')) {
    function add_shortcode($tag, $callback = null) { $GLOBALS['wm_test_shortcodes'][$tag] = $callback; }
}
if (!function_exists('remove_shortcode')) {
    function remove_shortcode($tag) { unset($GLOBALS['wm_test_shortcodes'][$tag]); }
}
if (!function_exists('shortcode_exists')) {
    function shortcode_exists($tag) { return isset($GLOBALS['wm_test_shortcodes'][$tag]); }
}
if (!function_exists('post_type_exists')) {
    function post_type_exists($type) { return isset($GLOBALS['wm_test_post_types'][$type]); }
}
if (!function_exists('did_action')) {
    function did_action($hook) {
        return match ($hook) {
            'init'    => $GLOBALS['wm_test_did_init'],
            'wp_head' => $GLOBALS['wm_test_did_head'],
            default   => 0,
        };
    }
}
if (!function_exists('wp_count_posts')) {
    function wp_count_posts($type) { return (object) ['publish' => 3]; }
}
if (!function_exists('load_plugin_textdomain')) {
    function load_plugin_textdomain(...$args) {}
}
if (!function_exists('plugin_basename')) {
    function plugin_basename($file) { return basename($file); }
}
if (!function_exists('plugin_dir_url')) {
    function plugin_dir_url($file) { return 'https://example.test/wp-content/plugins/wm-social-care-accessibility-hub/'; }
}
if (!function_exists('is_admin')) {
    function is_admin() { return false; }
}
if (!function_exists('esc_attr')) {
    function esc_attr($str) { return htmlspecialchars((string)$str, ENT_QUOTES, 'UTF-8'); }
}
if (!function_exists('esc_html')) {
    function esc_html($str) { return htmlspecialchars((string)$str, ENT_QUOTES, 'UTF-8'); }
}
if (!function_exists('esc_url')) {
    function esc_url($str) { return filter_var((string)$str, FILTER_SANITIZE_URL) ?: ''; }
}
if (!function_exists('wpautop')) {
    function wpautop($str) { return '<p>' . str_replace("\n", '<br/>', (string)$str) . '</p>'; }
}
if (!function_exists('wp_strip_all_tags')) {
    function wp_strip_all_tags($text) {
        $text = (string) preg_replace('@<(script|style)[^>]*?>.*?</\\1>@si', '', (string) $text);
        return trim(strip_tags($text));
    }
}
if (!function_exists('__')) {
    function __($str, $domain = 'default') { return wm_test_gettext((string) $str, (string) $domain); }
}
if (!function_exists('esc_html__')) {
    function esc_html__($str, $domain = 'default') { return esc_html(wm_test_gettext((string) $str, (string) $domain)); }
}
if (!function_exists('esc_attr__')) {
    function esc_attr__($str, $domain = 'default') { return esc_attr(wm_test_gettext((string) $str, (string) $domain)); }
}
if (!function_exists('esc_html_e')) {
    function esc_html_e($str, $domain = 'default') { echo esc_html__($str, $domain); }
}
if (!function_exists('esc_attr_e')) {
    function esc_attr_e($str, $domain = 'default') { echo esc_attr__($str, $domain); }
}
if (!function_exists('_n')) {
    function _n($single, $plural, $number, $domain = 'default') {
        wm_test_gettext((string) $single, (string) $domain);
        wm_test_gettext((string) $plural, (string) $domain);
        return (int) $number === 1 ? $single : $plural;
    }
}
if (!function_exists('shortcode_atts')) {
    function shortcode_atts($pairs, $atts) {
        $out = array();
        foreach ($pairs as $name => $default) {
            $out[$name] = array_key_exists($name, (array)$atts) ? $atts[$name] : $default;
        }
        return $out;
    }
}
if (!function_exists('register_post_type')) {
    function register_post_type($post_type, $args = []) { $GLOBALS['wm_test_post_types'][$post_type] = $args; return true; }
}
if (!function_exists('register_taxonomy')) {
    function register_taxonomy($taxonomy, $object_type, $args = []) { return true; }
}
if (!function_exists('register_rest_route')) {
    function register_rest_route($namespace, $route, $args = []) { return true; }
}
if (!function_exists('wp_json_encode')) {
    function wp_json_encode($data, $options = 0) { return json_encode($data, $options); }
}
if (!function_exists('wp_enqueue_style')) {
    function wp_enqueue_style($handle, ...$args) { $GLOBALS['wm_test_styles'][] = $handle; }
}
if (!function_exists('wp_style_is')) {
    function wp_style_is($handle, $list = 'enqueued') {
        return $list === 'done' ? $GLOBALS['wm_test_style_done'] : in_array($handle, $GLOBALS['wm_test_styles'], true);
    }
}
if (!function_exists('wp_unique_id')) {
    function wp_unique_id($prefix = '') {
        static $id = 0;
        return $prefix . (string) ++$id;
    }
}

// El contrato del Suite Hub tiene que existir ANTES de cargar el adapter: su
// `implements` es incondicional, así que sin la interfaz PHP no puede declarar
// la clase y el fichero fatalea antes del primer test. El stub prefiere la
// interfaz real cuando el Hub está en disco; sólo suple cuando no está.
require_once __DIR__ . '/stubs/suite-hub-contracts.php';
require_once __DIR__ . '/stubs/wp-cli.php';

// Require classes
require_once __DIR__ . '/../includes/Contracts/Language_Level_Enum.php';
require_once __DIR__ . '/../includes/Contracts/WM_Social_Care_Module_Interface.php';
require_once __DIR__ . '/../includes/Domain/Leichte_Sprache_Governor.php';
require_once __DIR__ . '/../includes/Domain/Care_Needs_Checker.php';
require_once __DIR__ . '/../includes/Domain/Contrast_Governor.php';
require_once __DIR__ . '/../includes/Domain/Video_Repository.php';
require_once __DIR__ . '/../includes/Domain/Seed_Data_Manager.php';
require_once __DIR__ . '/../includes/Frontend/Accessibility_Toolbar.php';
require_once __DIR__ . '/../includes/Frontend/Accessible_Video_Player.php';
require_once __DIR__ . '/../includes/Frontend/Shortcodes.php';
require_once __DIR__ . '/../includes/Frontend/Ecosystem_Integrations.php';
require_once __DIR__ . '/../includes/Admin/Admin_Dashboard.php';
require_once __DIR__ . '/../includes/API/REST_Controller.php';
require_once __DIR__ . '/../includes/Spoke/Social_Care_Spoke_Adapter.php';
require_once __DIR__ . '/../includes/CLI/Social_Care_CLI.php';
require_once __DIR__ . '/../wm-social-care-accessibility-hub.php';

use ArnoldWender\SocialCareA11y\Admin\Admin_Dashboard;
use ArnoldWender\SocialCareA11y\Contracts\Language_Level_Enum;
use ArnoldWender\SocialCareA11y\Domain\Leichte_Sprache_Governor;
use ArnoldWender\SocialCareA11y\Domain\Care_Needs_Checker;
use ArnoldWender\SocialCareA11y\Domain\Contrast_Governor;
use ArnoldWender\SocialCareA11y\Domain\Video_Repository;
use ArnoldWender\SocialCareA11y\Domain\Seed_Data_Manager;
use ArnoldWender\SocialCareA11y\Frontend\Accessible_Video_Player;
use ArnoldWender\SocialCareA11y\Frontend\Ecosystem_Integrations;
use ArnoldWender\SocialCareA11y\Frontend\Shortcodes;
use ArnoldWender\SocialCareA11y\API\REST_Controller;
use ArnoldWender\SocialCareA11y\CLI\Social_Care_CLI;
use ArnoldWender\SocialCareA11y\Frontend\Accessibility_Toolbar;
use ArnoldWender\SocialCareA11y\Spoke\Social_Care_Spoke_Adapter;
use ArnoldWender\SocialCareA11y\Social_Care_Hub_Plugin;

final class Test_Social_Care_Hub {
    private int $passed = 0;
    private int $failed = 0;

    public function run(): void {
        echo "==============================================================================\n";
        echo " Unit tests: wm-social-care-accessibility-hub                                 \n";
        echo "==============================================================================\n";

        foreach ([
            'test_language_level_enum',
            'test_leichte_sprache_mediopunkt',
            'test_leichte_sprache_sentence_auditor',
            'test_inclusion_europe_license',
            'test_care_needs_calculations',
            'test_care_needs_invalid_degree',
            'test_contrast_calculations',
            'test_video_repository_and_cwe1236',
            'test_seed_data_manager',
            'test_accessible_video_player_render',
            'test_rest_controller_endpoints',
            'test_spoke_adapter_measures',
            'test_frontend_output_honest',
            'test_frontend_tokens_defined_and_readable',
            'test_shortcodes_accept_empty_string_attributes',
            'test_card_turns_wordpress_markup_back_into_paragraphs',
            'test_bedarfscheck_instances_and_live_region',
            'test_library_labels_and_styles_once',
            'test_toolbar_read_aloud_and_contrast_state',
            'test_frontend_strings_translatable',
            'test_admin_rows_measure_every_shortcode',
            'test_admin_texts_match_the_examples',
            'test_cli_seed_writes_nothing_and_says_so',
            'test_cli_calc_whole_numbers_and_format',
            'test_every_shipped_file_guarded',
            'test_uninstall_removes_only_what_is_written',
            'test_uninstall_opt_in_default_preservation',
            'test_ecosystem_hooks_and_bridges',
            'test_standalone_load_in_own_process',
            'test_eu_24_languages_installed',
            'test_catalogs_from_code',
            'test_documents_match_the_code',
            'test_suite_hub_contract_parity',
            // Last of the render tests: it leaves the inline token sheet marked as printed.
            'test_late_token_sheet_inline_once',
        ] as $method) {
            $this->run_test($method);
        }

        echo "------------------------------------------------------------------------------\n";
        echo sprintf("  TEST SUITE RESULTS: %d PASSED | %d FAILED\n", $this->passed, $this->failed);
        echo "==============================================================================\n";

        if ($this->failed > 0) {
            exit(1);
        }
    }

    /** One method per case: an exception, warning or notice is a failed case, not a dead suite. */
    private function run_test(string $method): void {
        set_error_handler(static function (int $number, string $message, string $file, int $line): bool {
            if (!(error_reporting() & $number)) {
                return false;
            }
            throw new \ErrorException($message, 0, $number, $file, $line);
        });
        try {
            $this->$method();
        } catch (\Throwable $e) {
            $this->assert(false, sprintf('%s threw %s: %s (%s:%d)', $method, get_class($e), $e->getMessage(), basename($e->getFile()), $e->getLine()));
        } finally {
            restore_error_handler();
        }
    }

    private function assert(bool $condition, string $test_name): void {
        if ($condition) {
            echo " [PASS] " . $test_name . "\n";
            $this->passed++;
        } else {
            echo " [FAIL] " . $test_name . "\n";
            $this->failed++;
        }
    }

    private function test_language_level_enum(): void {
        $ls = Language_Level_Enum::LEICHTE_SPRACHE;
        $es = Language_Level_Enum::EINFACHE_SPRACHE;
        $st = Language_Level_Enum::STANDARD;

        $this->assert($ls->requires_target_group_audit() === true, 'Leichte Sprache requires target group testing');
        $this->assert($es->requires_target_group_audit() === false, 'Einfache Sprache does not require target group testing');
        $this->assert($st->value === 'standard', 'Standarddeutsch value is standard');
    }

    private function test_leichte_sprache_mediopunkt(): void {
        $input = "Die Pflegekasse zahlt die Alltagsbegleitung über den Entlastungsbetrag.";
        $formatted = Leichte_Sprache_Governor::apply_mediopunkt($input);

        $expected = "Die Pflege·kasse zahlt die Alltags·begleitung über den Entlastungs·betrag.";
        $this->assert($formatted === $expected, 'Compound care terms get formatted with Mediopunkt (·)');
    }

    private function test_leichte_sprache_sentence_auditor(): void {
        $short_text = "Wir helfen Ihnen im Haushalt. Die Pflegekasse bezahlt uns.";
        $audit1 = Leichte_Sprache_Governor::audit_sentence_length($short_text);
        $this->assert($audit1['is_valid'] === true, 'Short sentences pass Leichte Sprache audit');

        $long_text = "Dies ist ein extrem langer und verschachtelter Satz der viel zu viele Wörter beinhaltet und definitiv gegen die strengen Regeln der Leichten Sprache verstößt.";
        $audit2 = Leichte_Sprache_Governor::audit_sentence_length($long_text);
        $this->assert($audit2['is_valid'] === false, 'Sentences over 15 words fail Leichte Sprache audit');
    }

    private function test_inclusion_europe_license(): void {
        $valid_attr = "© European Easy-to-Read Logo: Inclusion Europe. More information at www.inclusion-europe.eu/easy-to-read";
        $this->assert(
            Leichte_Sprache_Governor::validate_inclusion_europe_license(true, $valid_attr) === true,
            'Valid Inclusion Europe license attribution passes'
        );

        $invalid_attr = "Logo: Easy to read Europe.";
        $this->assert(
            Leichte_Sprache_Governor::validate_inclusion_europe_license(true, $invalid_attr) === false,
            'Missing URL in Inclusion Europe attribution fails'
        );
    }

    private function test_care_needs_calculations(): void {
        // Statutory amounts since 2025-01-01 (+4.5 %, PUEG) and the joint annual budget since
        // 2025-07-01 (§ 42a SGB XI); unchanged for 2026. Until 2026-09-14 the code carried the
        // 2024 values (125 / 332 / 1612) and these assertions pinned them.
        $pg2 = Care_Needs_Checker::calculate_entitlements(2);
        $this->assert($pg2['entlastungsbetrag_month'] === 131.00, 'PG 2 Entlastungsbetrag is 131 €/month (§ 45b, since 2025-01-01)');
        $this->assert($pg2['entlastungsbetrag_year'] === 1572.00, 'PG 2 Entlastungsbetrag is 1.572 €/year');
        $this->assert($pg2['pflegegeld_month'] === 347.00, 'PG 2 Pflegegeld is 347 €/month (§ 37)');
        $this->assert($pg2['sachleistung_month'] === 796.00, 'PG 2 Pflegesachleistung is 796 €/month (§ 36)');
        $this->assert($pg2['verhinderungspflege_year'] === 3539.00, 'PG 2 joint annual budget Verhinderungs-/Kurzzeitpflege is 3.539 € (§ 42a, since 2025-07-01)');
        $this->assert($pg2['verhinderungspflege_max'] === 3539.00, 'PG 2 combined budget equals the joint annual budget (one pool)');
        $this->assert($pg2['statutory_as_of'] === '2025-07-01', 'Entitlements carry the statutory as-of date');

        $pg5 = Care_Needs_Checker::calculate_entitlements(5);
        $this->assert($pg5['pflegegeld_month'] === 990.00 && $pg5['sachleistung_month'] === 2299.00, 'PG 5 Pflegegeld 990 € and Sachleistung 2.299 €');

        // Pflegegrad 1
        $pg1 = Care_Needs_Checker::calculate_entitlements(1);
        $this->assert($pg1['entlastungsbetrag_month'] === 131.00, 'PG 1 has 131 €/month Entlastungsbetrag');
        $this->assert($pg1['pflegegeld_month'] === 0.00, 'PG 1 has 0 € Pflegegeld');
        $this->assert($pg1['verhinderungspflege_year'] === 0.00, 'PG 1 has 0 € Verhinderungspflege');

        // The frontend calculator prints the same table (single source), not a hand-typed copy.
        $table = Care_Needs_Checker::entitlement_table();
        $this->assert(count($table) === 5 && $table[3]['geld'] === 599.00 && $table[1]['verhinderung'] === 0.00, 'entitlement_table() covers PG 1-5 from calculate_entitlements()');
        $html = Shortcodes::render_bedarfscheck();
        $this->assert(strpos($html, wp_json_encode($table)) !== false, 'Bedarfscheck shortcode embeds Care_Needs_Checker::entitlement_table() verbatim');
        $this->assert(strpos($html, '"entlastung":125') === false && strpos($html, '1612') === false, 'Bedarfscheck shortcode carries no 2024 amounts');
        $this->assert(strpos($html, '§ 42a SGB XI') !== false, 'Bedarfscheck names the joint annual budget (§ 42a SGB XI)');
    }

    private function test_care_needs_invalid_degree(): void {
        $caught = false;
        try {
            Care_Needs_Checker::calculate_entitlements(6);
        } catch (\InvalidArgumentException $e) {
            $caught = true;
        }
        $this->assert($caught === true, 'Invalid Pflegegrad (>5) throws InvalidArgumentException');
    }

    private function test_contrast_calculations(): void {
        // Black and White = 21:1
        $ratio_bw = Contrast_Governor::calculate_contrast_ratio('#000000', '#ffffff');
        $this->assert($ratio_bw === 21.00, 'Black on White contrast ratio is exactly 21:1');

        // High contrast yellow on black (#FFFF00 on #000000)
        $ratio_yb = Contrast_Governor::calculate_contrast_ratio('#ffff00', '#000000');
        $this->assert($ratio_yb >= 19.0, 'Yellow on Black contrast ratio is >= 19:1');

        // WCAG AAA Evaluation (Yellow on Black)
        $eval = Contrast_Governor::evaluate_wcag_levels('#ffff00', '#000000');
        $this->assert($eval['wcag_aaa_normal'] === true, 'Yellow on Black passes WCAG 2.2 AAA (>= 7:1)');

        // Poor contrast (Light grey on white)
        $eval_poor = Contrast_Governor::evaluate_wcag_levels('#cccccc', '#ffffff');
        $this->assert($eval_poor['wcag_aa_normal'] === false, 'Grey on White fails WCAG 2.2 AA');
    }

    private function test_video_repository_and_cwe1236(): void {
        $this->assert(Video_Repository::CPT_SLUG === 'wm_loesungsweg', 'CPT slug is wm_loesungsweg');
        $this->assert(Video_Repository::TAX_CATEGORY === 'wm_kategorie', 'Category taxonomy is wm_kategorie');

        // CWE-1236 Formula Injection Sanitization
        $malicious_cmd = "=cmd|'/C calc'!A0";
        $sanitized = Video_Repository::sanitize_formula_injection($malicious_cmd);
        $this->assert($sanitized === "'=cmd|'/C calc'!A0", 'Formula injection prefix (=) is neutralized with single quote');

        $safe_text = "Sicherer Transfer";
        $this->assert(Video_Repository::sanitize_formula_injection($safe_text) === "Sicherer Transfer", 'Safe text is untouched');
    }

    private function test_seed_data_manager(): void {
        $data = Seed_Data_Manager::get_seed_data();
        $this->assert(count($data) === 8, 'Seed data contains exactly 8 built-in example solutions');
        $this->assert($data[0]['title'] === 'Sicherer Transfer vom Rollstuhl ins Bett', 'First solution is Rollstuhl-Transfer');
        $this->assert(isset($data[0]['steps']) && count($data[0]['steps']) === 7, 'First solution has 7 steps');
        $this->assert(isset($data[0]['leichteSprache']), 'First solution has Leichte Sprache text');
    }

    private function test_accessible_video_player_render(): void {
        // The built-in examples carry no video since 19.11.2026 (their host never resolved, D8); the player is
        // exercised with an operator's own video, which is how a published Lösungsweg reaches it.
        $sample = array_merge(Seed_Data_Manager::get_seed_data()[0], ['videoUrl' => 'https://example.com/video/transfer.mp4', 'captionsUrl' => '/captions/transfer-de.vtt']);
        $html = Accessible_Video_Player::render($sample);

        $this->assert(strpos($html, 'wm-accessible-player-card') !== false, 'Player renders card container');
        $this->assert(strpos($html, '<video') !== false, 'Player renders HTML5 <video> tag');
        $this->assert(strpos($html, '<track') !== false, 'Player renders <track> tag with German captions');
        $this->assert(strpos($html, 'Leichter Sprache') !== false, 'Player renders Leichte Sprache section');
        $this->assert(strpos($html, '<style') !== false, 'The first player of a page prints the card styles');
        $no_video = Accessible_Video_Player::render(Seed_Data_Manager::get_seed_data()[0]);
        $this->assert(strpos($no_video, '<video') === false && strpos($no_video, 'wm-accessible-player-card') !== false, 'An example without a video renders the card without an empty player');
    }

    private function test_rest_controller_endpoints(): void {
        $loesungswege = REST_Controller::get_loesungswege();
        $this->assert($loesungswege['status'] === 'success', 'REST API get_loesungswege returns success');
        $this->assert($loesungswege['count'] === 8, 'REST API returns 8 items');

        $calc = REST_Controller::calculate_bedarfscheck();
        $this->assert($calc['status'] === 'success', 'REST API calculate_bedarfscheck returns success');
        $this->assert($calc['data']['pflegegrad'] === 2, 'Default PG calculation is 2');

        // Out-of-range care degrees are rejected by the route schema (400) instead of being answered
        // with care degree 2; the callback no longer reads $_GET behind the request object.
        $args = REST_Controller::bedarfscheck_args()['pflegegrad'] ?? [];
        $this->assert(
            ($args['type'] ?? '') === 'integer' && ($args['minimum'] ?? 0) === 1 && ($args['maximum'] ?? 0) === 5
                && ($args['validate_callback'] ?? '') === 'rest_validate_request_arg',
            'bedarfscheck route validates pflegegrad as integer 1-5 with rest_validate_request_arg'
        );
        $source = $this->code_without_comments(__DIR__ . '/../includes/API/REST_Controller.php');
        $this->assert(strpos($source, '$_GET') === false, 'REST controller reads no superglobal');

        $request = new class {
            public function get_param($name) { return $name === 'pflegegrad' ? '4' : null; }
        };
        $this->assert(REST_Controller::calculate_bedarfscheck($request)['data']['pflegegrad'] === 4, 'bedarfscheck answers the requested care degree');

        // Permission callback enforces GET/HEAD and transient HMAC-SHA256 IP rate limiting.
        $post_req = new class {
            public function get_method(): string { return 'POST'; }
        };
        $perm_post = REST_Controller::check_public_read_permission($post_req);
        $this->assert($perm_post instanceof \WP_Error && $perm_post->get_error_code() === 'wm_social_care_method_not_allowed', 'REST permission callback rejects non-GET requests with 405');

        $hmac = REST_Controller::client_ip_hmac('203.0.113.42');
        $this->assert((bool) preg_match('/^[0-9a-f]{64}$/', $hmac) && strpos($hmac, '203.0.113.42') === false, 'REST rate limiter hashes client IPs with HMAC-SHA256');

        $GLOBALS['wm_test_transients'] = [];
        $_SERVER['REMOTE_ADDR']        = '198.51.100.77';
        $rl_key                        = REST_Controller::TRANSIENT_PREFIX . substr(REST_Controller::client_ip_hmac('198.51.100.77'), 0, 24);
        $this->assert(REST_Controller::check_public_read_permission() === true, 'REST permission callback allows GET below rate limit');
        $GLOBALS['wm_test_transients'][$rl_key] = REST_Controller::RATE_LIMIT_MAX;
        $blocked = REST_Controller::check_public_read_permission();
        $this->assert($blocked instanceof \WP_Error && $blocked->get_error_code() === 'wm_social_care_rate_limited', 'REST permission callback returns 429 WP_Error once rate limit is reached');
        $GLOBALS['wm_test_transients'] = [];
    }

    /** PHP source without comments and docblocks, so an assertion cannot match its own explanation. */
    private function code_without_comments(string $file): string {
        $out = '';
        foreach (token_get_all((string) file_get_contents($file)) as $token) {
            if (is_array($token) && in_array($token[0], [T_COMMENT, T_DOC_COMMENT], true)) {
                continue;
            }
            $out .= is_array($token) ? $token[1] : $token;
        }
        return $out;
    }

    private function test_spoke_adapter_measures(): void {
        $adapter = new Social_Care_Spoke_Adapter();
        $this->assert($adapter->get_module_slug() === 'wm-social-care-accessibility-hub', 'Spoke slug matches');
        $this->assert($adapter->get_version() === WM_SOCIAL_CARE_VERSION, 'Spoke version comes from WM_SOCIAL_CARE_VERSION');
        $this->assert(!method_exists($adapter, 'is_bfsg_compliant'), 'Spoke makes no fixed compliance claim (is_bfsg_compliant removed)');
        $this->assert($adapter->get_dependencies() === [], 'Spoke declares no dependency (it runs standalone)');

        // Before init nothing is registered yet: not measurable, so not HEALTHY.
        $GLOBALS['wm_test_shortcodes'] = [];
        $GLOBALS['wm_test_post_types'] = [];
        $GLOBALS['wm_test_did_init']   = 0;
        $this->assert($adapter->get_health_status() === 'DEGRADED', 'Health before init is DEGRADED, not HEALTHY');
        $this->assert($adapter->execute_action('self_test')['success'] === false, 'self_test before init fails');

        $GLOBALS['wm_test_did_init'] = 1;
        $this->assert($adapter->get_health_status() === 'ERROR', 'Health after init with nothing registered is ERROR');

        Shortcodes::register();
        Video_Repository::register_cpt_and_taxonomies();
        $this->assert($adapter->get_health_status() === 'HEALTHY', 'Health is HEALTHY with five shortcodes and the post type registered');
        $self_test = $adapter->execute_action('self_test');
        $this->assert($self_test['success'] === true, 'self_test passes with every registration present');

        $telemetry = $adapter->get_telemetry_metrics();
        $this->assert($telemetry['builtin_examples'] === 8 && $telemetry['missing_registrations'] === [], 'Telemetry counts the eight built-in examples and no missing registration');
        $this->assert($telemetry['example_video_hosts'] === [], 'Telemetry names no external video host: the built-in examples carry no video until a host is decided (D8)');
        $this->assert($telemetry['published_loesungswege'] === 3, 'Telemetry reads the published wm_loesungsweg count');
        $json = (string) json_encode($telemetry);
        $this->assert(strpos($json, 'COMPLIANT') === false && strpos($json, '24') === false, 'Telemetry carries no fixed COMPLIANT flag and no invented video count');

        remove_shortcode('wm_video_player');
        $this->assert($adapter->get_health_status() === 'DEGRADED', 'Health is DEGRADED when one shortcode is missing');
        $this->assert($adapter->execute_action('self_test')['success'] === false, 'self_test fails when one shortcode is missing');
        Shortcodes::register();

        $unknown = $adapter->execute_action('purge_everything');
        $this->assert($unknown['success'] === false, 'Unknown spoke action fails instead of reporting success');

        $sbom = $adapter->get_sbom_data();
        $this->assert(!isset($sbom['bomFormat']) && !isset($sbom['components']), 'get_sbom_data returns one component, not a whole BOM document');
        $this->assert(($sbom['type'] ?? '') === 'application' && ($sbom['name'] ?? '') === 'wm-social-care-accessibility-hub', 'SBOM component carries type and slug');
        $this->assert((bool) preg_match('/^[0-9a-f]{64}$/', (string) ($sbom['hashes'][0]['content'] ?? '')), 'SBOM component hashes the main plugin file (SHA-256)');
    }

    private function test_frontend_output_honest(): void {
        $emoji = '/[\x{1F300}-\x{1FAFF}\x{2600}-\x{27BF}\x{23E9}-\x{23FF}\x{2B00}-\x{2BFF}\x{FE0F}]/u';
        Accessibility_Toolbar::reset_render_state();
        $toolbar   = Accessibility_Toolbar::render();
        $toolbar_2 = Accessibility_Toolbar::render();
        $library   = Shortcodes::render_videothek();
        $card      = Shortcodes::render_leichte_sprache_card([], 'Die Pflegekasse zahlt.');
        $calc      = Shortcodes::render_bedarfscheck();
        foreach (['toolbar' => $toolbar, 'video library' => $library, 'Leichte Sprache card' => $card, 'Bedarfscheck' => $calc] as $name => $html) {
            $this->assert(preg_match($emoji, $html) === 0, "No emoji in the {$name} output");
        }

        // Every button has a data-wm-a11y-action handler, unique IDs across renders, WCAG 2.5.3 labels, and inline aria-live status instead of alert().
        preg_match_all('/<button[^>]*\bid="([^"]+)"/', $toolbar, $ids);
        preg_match_all('/<button[^>]*\bid="([^"]+)"/', $toolbar_2, $ids_2);
        preg_match_all('/<button[^>]*\bdata-wm-a11y-action="([^"]+)"/', $toolbar, $actions);
        $dead = array_filter($actions[1], static fn(string $act): bool => strpos($toolbar, "[data-wm-a11y-action=\"{$act}\"]") === false);
        $this->assert(count($ids[1]) === 5 && count($actions[1]) === 5 && $dead === [], 'Every toolbar button has a data-wm-a11y-action click handler (5 buttons, none dead)');
        $this->assert(array_intersect($ids[1], $ids_2[1]) === [] && strpos($toolbar_2, '<script>') === false && strpos($toolbar_2, '<style>') === false, 'Second toolbar render uses unique IDs without duplicate style/script tags');
        $this->assert(strpos($toolbar, 'aria-label="A- — Schriftgröße verkleinern"') !== false && strpos($toolbar, 'aria-label="A+ — Schriftgröße vergrößern"') !== false, 'Toolbar font buttons preserve WCAG 2.5.3 visible label prefix in aria-label');
        $this->assert(strpos($toolbar, 'alert(') === false && strpos($toolbar, 'role="status"') !== false && strpos($toolbar, 'aria-live="polite"') !== false, 'Toolbar uses an inline aria-live status region instead of alert()');
        $this->assert(strpos($toolbar, 'wm-a11y-leichte-sprache') === false, 'Toolbar has no Leichte Sprache button without function');
        $this->assert(strpos($toolbar, 'aria-pressed="false"') !== false, 'Contrast toggle exposes its state with aria-pressed');

        $this->assert(strpos($library, 'Taste M') === false && strpos($library, 'Taste C') === false, 'Player announces no keyboard shortcuts the plugin does not implement');
        $this->assert(Shortcodes::render_single_player(['id' => '99']) === '', 'Unknown video ID renders nothing instead of example 1');
        $this->assert(strpos(Shortcodes::render_single_player(['id' => '3']), 'Gebärdensprache-App') !== false, 'Known video ID renders that example');
        $this->assert(strpos($card, '<svg') !== false && strpos($card, '<article class="wm-leichte-sprache-box"') !== false && strpos($card, 'role="article"') === false, 'Leichte Sprache card draws an SVG pictogram in an <article>');
        $this->assert(strpos($card, 'Pflege·kasse') !== false, 'Leichte Sprache card applies the Mediopunkt');
    }

    private function test_frontend_tokens_defined_and_readable(): void {
        $sheet = (string) file_get_contents(__DIR__ . '/../assets/css/wm-social-care-frontend.css');
        preg_match_all('/^\s*(--[a-z0-9-]+):\s*([^;]+);/m', $sheet, $defs);
        $defined = array_combine($defs[1], array_map('trim', $defs[2]));

        $frontend = '';
        foreach (glob(__DIR__ . '/../includes/Frontend/*.php') ?: [] as $file) {
            $frontend .= (string) file_get_contents($file);
        }
        preg_match_all('/var\((--[a-z0-9-]+)/', $frontend, $used);
        $undefined = array_values(array_diff(array_unique($used[1]), array_keys($defined)));
        $this->assert(count(array_unique($used[1])) > 40 && $undefined === [], 'Every CSS custom property used by the frontend is defined in the frontend sheet (undefined: ' . (implode(', ', $undefined) ?: 'none') . ')');

        $hex_lines = array_filter(preg_split('/\R/', $frontend) ?: [], static fn(string $line): bool => (bool) preg_match('/#[0-9a-fA-F]{3,6}\b/', $line) && !preg_match('/--[a-z0-9-]+\s*:/', $line));
        $this->assert($hex_lines === [], 'No hex colour in the frontend PHP outside token definitions');

        // Every text pair the components paint (foreground on background), measured with the plugin's own governor.
        // Until 2026-09-15 eleven of them were listed.
        $pairs = [
            'toolbar text'              => ['--color-white', '--color-slate-900'],
            'toolbar button'            => ['--color-slate-50', '--color-slate-800'],
            'toolbar button hover'      => ['--color-slate-50', '--color-primary-700'],
            'calculator text'           => ['--color-slate-800', '--color-white'],
            'calculator title'          => ['--color-slate-900', '--color-white'],
            'calculator description'    => ['--color-slate-600', '--color-white'],
            'calculator button'         => ['--color-white', '--color-primary-600'],
            'calculator button hover'   => ['--color-white', '--color-primary-700'],
            'calculator result'         => ['--color-emerald-800', '--color-emerald-50'],
            'calculator result title'   => ['--color-emerald-900', '--color-emerald-50'],
            'player text'               => ['--color-slate-800', '--color-white'],
            'player title'              => ['--color-slate-900', '--color-white'],
            'player duration badge'     => ['--color-slate-600', '--color-slate-100'],
            'player step text'          => ['--color-slate-700', '--color-white'],
            'player step number'        => ['--color-primary-600', '--color-white'],
            'player Leichte Sprache'    => ['--color-primary-900', '--color-primary-50'],
            'player Leichte heading'    => ['--color-primary-800', '--color-primary-50'],
            'high contrast'             => ['--wm-sc-contrast-foreground', '--wm-sc-contrast-background'],
            'high contrast pressed'     => ['--wm-sc-contrast-background', '--wm-sc-contrast-foreground'],
        ];
        foreach ($pairs as $name => [$fg, $bg]) {
            $ratio = (isset($defined[$fg], $defined[$bg])) ? Contrast_Governor::calculate_contrast_ratio($defined[$fg], $defined[$bg]) : 0.0;
            $this->assert($ratio >= 4.5, sprintf('Contrast %s %.2f:1 >= 4.5:1', $name, $ratio));
        }

        // Control boundaries and focus outlines at 3:1 against what they sit on, and the rule really uses that token.
        // Until 2026-09-15: select border 1.48:1, Bedarfscheck focus outline 1.67:1, toolbar button border 1.72:1.
        $non_text = [
            'Bedarfscheck select border on the card'       => ['--color-slate-600', '--color-white', '.wm-form-select', 'solid var(--color-slate-600)'],
            'Bedarfscheck focus outline on the card'       => ['--color-primary-800', '--color-white', '.wm-calc-btn:focus-visible', 'solid var(--color-primary-800)'],
            'Bedarfscheck button on the card'              => ['--color-primary-600', '--color-white', '.wm-calc-btn', 'background: var(--color-primary-600)'],
            'toolbar button border on the toolbar'         => ['--color-slate-400', '--color-slate-900', '.wm-a11y-btn', 'solid var(--color-slate-400)'],
            'toolbar focus outline on the toolbar'         => ['--color-primary-400', '--color-slate-900', '.wm-a11y-btn:focus-visible', 'solid var(--color-primary-400)'],
            'toolbar focus outline on the focused button'  => ['--color-primary-400', '--color-primary-700', '.wm-a11y-btn:focus-visible', 'solid var(--color-primary-400)'],
        ];
        foreach ($non_text as $name => [$fg, $bg, $selector, $declaration]) {
            $block = preg_match('/^\s*' . preg_quote($selector, '/') . '\s*\{([^}]*)\}/m', $frontend, $m) ? $m[1] : '';
            $ratio = isset($defined[$fg], $defined[$bg]) ? Contrast_Governor::calculate_contrast_ratio($defined[$fg], $defined[$bg]) : 0.0;
            $this->assert(strpos($block, $declaration) !== false && $ratio >= 3.0, sprintf('%s uses %s at %.2f:1 >= 3:1', $name, $fg, $ratio));
        }

        // A component that paints its own background also sets its text colour; otherwise it inherits
        // the theme's (light text on a dark theme, measured on GeneratePress 2026-09-14).
        foreach (['.wm-bedarfscheck-card', '.wm-accessible-player-card'] as $selector) {
            $block = preg_match('/' . preg_quote($selector, '/') . '\s*\{([^}]*)\}/', $frontend, $m) ? $m[1] : '';
            $this->assert(strpos($block, 'background:') !== false && (bool) preg_match('/(?<!-)color:\s*var\(--color-slate-800\)/', $block), "{$selector} sets its text colour next to its background");
        }
    }

    private function test_shortcodes_accept_empty_string_attributes(): void {
        // WordPress before 6.5 passes '' for a shortcode without attributes, and the header admits 6.4.
        foreach (['render_bedarfscheck', 'render_videothek', 'render_single_player', 'render_leichte_sprache_card'] as $method) {
            try {
                $ok = Shortcodes::$method('') !== '';
            } catch (\TypeError $e) {
                $ok = false;
            }
            $this->assert($ok, "Shortcodes::{$method}('') renders instead of a TypeError (WordPress 6.4)");
        }
    }

    private function test_card_turns_wordpress_markup_back_into_paragraphs(): void {
        // What the card receives from classic content after core's wpautop() and shortcode_unautop() (WordPress 6.9.4).
        $classic = Shortcodes::render_leichte_sprache_card(['title' => 'Information'], "<br />\nDie Pflegekasse zahlt.</p>\n<p>Der Pflegegrad bleibt.<br />\n");
        $this->assert(strpos($classic, '&lt;') === false, 'Card prints no paragraph markup of WordPress as visible text (classic content)');
        $this->assert(strpos($classic, 'Die Pflege·kasse zahlt.') !== false && strpos($classic, 'Der Pflege·grad bleibt.') !== false, 'Card keeps both sentences with the Mediopunkt');
        $block = Shortcodes::render_leichte_sprache_card([], "Zeile eins<br />\nZeile zwei");
        $this->assert(strpos($block, '&lt;') === false && strpos($block, 'Zeile zwei') !== false, 'Card prints no line-break markup of the shortcode block as visible text');
        $this->assert(strpos(Shortcodes::render_leichte_sprache_card([], 'a <script>alert(1)</script> b'), '<script') === false, 'Card still prints no markup from the enclosed text');
    }

    private function test_bedarfscheck_instances_and_live_region(): void {
        $first  = Shortcodes::render_bedarfscheck();
        $second = Shortcodes::render_bedarfscheck();
        preg_match('/<select id="([^"]+)"/', $first, $a);
        preg_match('/<select id="([^"]+)"/', $second, $b);
        $this->assert(isset($a[1], $b[1]) && $a[1] !== $b[1], 'Two Bedarfschecks on one page get different element IDs');
        $this->assert(isset($a[1]) && strpos($first, '"select":"' . $a[1] . '"') !== false && strpos($first, 'document.getElementById(ids.select)') !== false, 'The script of each check addresses its own element IDs');
        $this->assert(strpos($first, 'aria-describedby') === false, 'The select is not described by the whole result region');
        $this->assert(strpos($first, 'role="status"') === false && strpos($first, "result?.setAttribute('role', 'status')") !== false, 'The result region becomes a status only after the first result is filled');
    }

    private function test_library_labels_and_styles_once(): void {
        $library = Shortcodes::render_videothek();
        $this->assert(strpos($library, 'Datenbank') === false && strpos($library, 'Barrierefreie') === false, 'Library does not call eight built-in examples a database of accessible guides');
        $this->assert(strpos($library, 'Beispiel') !== false, 'Library says that it shows built-in examples');
        $this->assert(substr_count($library, '<style') <= 2, 'Library prints the player styles at most once (' . substr_count($library, '<style') . ' style blocks)');
        $this->assert(substr_count(Shortcodes::render_single_player(['id' => '2']), '<style') === 0, 'Another player on the same page prints no second style block');
    }

    private function test_toolbar_read_aloud_and_contrast_state(): void {
        Accessibility_Toolbar::reset_render_state();
        $toolbar = Accessibility_Toolbar::render();
        $this->assert(strpos($toolbar, 'bar.hidden = true') !== false && strpos($toolbar, 'bar.hidden = wasHidden') !== false, 'Read aloud hides the toolbar while it reads the page text');
        $this->assert(strpos($toolbar, 'utterance.onerror = () => setSpeaking(false)') !== false, 'A failed speech synthesis resets the read-aloud button');
        $this->assert(strpos($toolbar, 'utterance.lang = html.lang') !== false, 'Read aloud uses the language of the page');
        $this->assert(
            (bool) preg_match('/html\[data-wm-contrast="high"\] \.wm-a11y-btn\[aria-pressed="true"\],\s*html\[data-wm-contrast="high"\] \.wm-btn-speech\[data-speaking="true"\]\s*\{\s*background-color: var\(--wm-sc-contrast-foreground\) !important;\s*color: var\(--wm-sc-contrast-background\) !important;/', $toolbar),
            'High contrast keeps the pressed and speaking buttons distinguishable'
        );
    }

    private function test_frontend_strings_translatable(): void {
        $GLOBALS['wm_test_gettext'] = [];
        Shortcodes::render_bedarfscheck();
        Accessibility_Toolbar::render();
        Shortcodes::render_videothek();
        Accessible_Video_Player::render(array_merge(Seed_Data_Manager::get_seed_data()[0], ['videoUrl' => 'https://example.com/v.mp4', 'captionsUrl' => '/c.vtt']));
        Shortcodes::render_leichte_sprache_card([], 'Text');
        $seen     = array_unique($GLOBALS['wm_test_gettext']);
        $expected = [
            'Ansprüche berechnen', 'Aktueller Pflegegrad:', 'Bis zu %s € / Jahr', 'Stand der Beträge: %s (gilt unverändert 2026).',
            'Vorlesen', 'Stopp', 'Kontrast', 'Schriftgröße vergrößern', 'Ihr Browser unterstützt keine direkte Sprachausgabe.',
            'Beispiel-Lösungswege', 'Schritt-für-Schritt-Anleitung:', 'Dauer:', 'Deutsch (Untertitel)',
            'Text nach den Regeln der Leichten Sprache (DIN SPEC 33429).',
        ];
        $missing = array_values(array_diff($expected, $seen));
        $this->assert($missing === [], 'Frontend strings go through gettext with the plugin text domain (missing: ' . (implode(' | ', $missing) ?: 'none') . ')');
    }

    private function test_admin_rows_measure_every_shortcode(): void {
        $method = new \ReflectionMethod(Admin_Dashboard::class, 'measured_rows');
        $GLOBALS['wm_test_shortcodes'] = [];
        Shortcodes::register();
        Video_Repository::register_cpt_and_taxonomies();
        $not_ok = static fn(array $rows): int => count(array_filter($rows, static fn(array $row): bool => !$row['ok']));

        $rows = $method->invoke(null);
        $this->assert(count($rows) === 6 && $not_ok($rows) === 0, 'Admin screen has a measured row for each of the five shortcodes and the post type');
        remove_shortcode('wm_video_player');
        $this->assert($not_ok($method->invoke(null)) === 1, 'Admin screen reports the single player as not registered when it is missing');
        remove_shortcode('wm_videothek_loesungswege');
        $this->assert($not_ok($method->invoke(null)) === 2, 'Admin screen reports the video library as not registered when it is missing');
        Shortcodes::register();
    }

    // 1.3.1: the admin caption said the example videos point at an external server and the
    // reference called the single player an HTML5 player, while none of the eight examples has a video.
    private function test_admin_texts_match_the_examples(): void {
        $with_video = array_filter(Seed_Data_Manager::get_seed_data(), static fn(array $item): bool => (string) ($item['videoUrl'] ?? '') !== '');
        $code       = $this->code_without_comments(__DIR__ . '/../includes/Admin/Admin_Dashboard.php');
        $claims     = preg_match('/Beispielvideos|externen Server|HTML5-Player/', $code) === 1;
        $this->assert($with_video !== [] || !$claims, 'The admin screen announces no example videos and no HTML5 player while no built-in example carries a video');
    }

    private function test_cli_seed_writes_nothing_and_says_so(): void {
        \WP_CLI::$calls = [];
        (new Social_Care_CLI())->seed([], []);
        $kinds    = array_column(\WP_CLI::$calls, 0);
        $logs     = implode("\n", array_map(static fn(array $c): string => is_string($c[1]) ? $c[1] : '', \WP_CLI::$calls));
        $table    = array_values(array_filter(\WP_CLI::$calls, static fn(array $c): bool => $c[0] === 'table'));
        $this->assert(!in_array('success', $kinds, true), 'wp wm-social-care seed reports no success for a write it does not make');
        $this->assert(strpos($logs, 'nichts in die Datenbank geschrieben') !== false, 'wp wm-social-care seed says that nothing was written');
        $this->assert(isset($table[0][1]) && count($table[0][1]) === 8, 'wp wm-social-care seed lists the eight built-in examples');
    }

    private function test_cli_calc_whole_numbers_and_format(): void {
        $cli  = new Social_Care_CLI();
        $logs = static fn(): string => implode("\n", array_map(static fn(array $c): string => is_string($c[1]) ? $c[1] : '', \WP_CLI::$calls));

        \WP_CLI::$calls = [];
        $cli->calc([], ['pflegegrad' => '2.9']);
        $this->assert(array_column(\WP_CLI::$calls, 0) === ['error'], 'wp wm-social-care calc --pflegegrad=2.9 ends with an error instead of care degree 2');

        \WP_CLI::$calls = [];
        $cli->calc([], ['pflegegrad' => '1']);
        $one = $logs();
        $this->assert(strpos($one, '§ 42a') === false, 'calc for care degree 1 prints no joint-budget line of 0 €, like the shortcode');
        $this->assert(strpos($one, 'Entlastungsbetrag: 131,00 € / Monat (1.572,00 € / Jahr)') !== false, 'calc prints the amounts in German number format');

        \WP_CLI::$calls = [];
        $cli->calc([], ['pflegegrad' => '3']);
        $this->assert(strpos($logs(), 'Bis zu 3.539,00 € / Jahr') !== false, 'calc for care degree 3 prints the joint annual budget');
    }

    private function test_every_shipped_file_guarded(): void {
        $unguarded = [];
        foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator(__DIR__ . '/../includes', FilesystemIterator::SKIP_DOTS)) as $file) {
            if (substr((string) $file, -4) !== '.php') {
                continue;
            }
            $code  = $this->code_without_comments((string) $file);
            $guard = preg_match("/if\s*\(\s*!\s*defined\(\s*'ABSPATH'\s*\)\s*\)\s*\{\s*exit;/", $code, $g, PREG_OFFSET_CAPTURE) ? $g[0][1] : PHP_INT_MAX;
            $decl  = preg_match('/^\s*(?:final\s+|abstract\s+)?(?:class|interface|enum|trait)\s/m', $code, $d, PREG_OFFSET_CAPTURE) ? $d[0][1] : PHP_INT_MAX;
            if ($guard === PHP_INT_MAX || $guard > $decl) {
                $unguarded[] = basename((string) $file);
            }
        }
        $this->assert($unguarded === [], 'Every PHP file under includes/ exits before its declaration outside WordPress (unguarded: ' . (implode(', ', $unguarded) ?: 'none') . ')');
    }

    private function test_uninstall_removes_only_what_is_written(): void {
        $uninstall = $this->code_without_comments(__DIR__ . '/../uninstall.php');
        preg_match_all("/delete_(?:site_)?(?:option|transient)\(\s*'([^']+)'/", $uninstall, $deleted);
        $code = '';
        foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator(__DIR__ . '/../includes', FilesystemIterator::SKIP_DOTS)) as $file) {
            $code .= (string) file_get_contents((string) $file);
        }
        $orphans = array_filter($deleted[1], static fn(string $name): bool => !preg_match("/(?:update|add)_(?:site_)?(?:option|transient)\(\s*'" . preg_quote($name, '/') . "'/", $code) && !preg_match("/set_(?:site_)?transient\(\s*'" . preg_quote($name, '/') . "'/", $code));
        $this->assert($orphans === [], 'uninstall.php deletes no option or transient that no code writes (orphans: ' . (implode(', ', $orphans) ?: 'none') . ')');
    }

    private function test_uninstall_opt_in_default_preservation(): void {
        $GLOBALS['wm_test_settings'] = [];
        Admin_Dashboard::register_settings();
        $this->assert(
            isset($GLOBALS['wm_test_settings']['wm_social_care_delete_data_on_uninstall'])
                && ($GLOBALS['wm_test_settings']['wm_social_care_delete_data_on_uninstall']['args']['default'] ?? null) === '0',
            'Admin_Dashboard registers wm_social_care_delete_data_on_uninstall defaulting to 0'
        );
        $this->assert(
            Admin_Dashboard::sanitize_delete_on_uninstall('') === '0' && Admin_Dashboard::sanitize_delete_on_uninstall('1') === '1',
            'Admin_Dashboard sanitizes uninstall opt-in checkbox strictly to 0 or 1'
        );

        if (!defined('WP_UNINSTALL_PLUGIN')) {
            define('WP_UNINSTALL_PLUGIN', true);
        }

        $GLOBALS['wm_test_options']         = ['wm_social_care_delete_data_on_uninstall' => '0'];
        $GLOBALS['wm_test_deleted_options'] = [];
        include __DIR__ . '/../uninstall.php';
        $this->assert($GLOBALS['wm_test_deleted_options'] === [], 'uninstall.php preserves all options by default when opt-in is 0');

        $GLOBALS['wm_test_options']         = ['wm_social_care_delete_data_on_uninstall' => '1'];
        $GLOBALS['wm_test_deleted_options'] = [];
        include __DIR__ . '/../uninstall.php';
        $this->assert(
            in_array('wm_social_care_delete_data_on_uninstall', $GLOBALS['wm_test_deleted_options'], true),
            'uninstall.php removes wm_social_care_delete_data_on_uninstall when explicitly opted in'
        );
        $GLOBALS['wm_test_options']         = [];
        $GLOBALS['wm_test_deleted_options'] = [];
    }

    private function test_ecosystem_hooks_and_bridges(): void {
        $GLOBALS['wm_test_filters'] = [];
        add_filter('wm_social_care_bedarfscheck_result', static function (array $row): array {
            $row['entlastungsbetrag_month'] = 150.00;
            return $row;
        });
        $filtered_calc = Care_Needs_Checker::calculate_entitlements(2);
        $this->assert($filtered_calc['entlastungsbetrag_month'] === 150.00, 'Filter wm_social_care_bedarfscheck_result customizes entitlement calculation');
        $GLOBALS['wm_test_filters'] = [];

        add_filter('wm_social_care_loesungswege_items', static function (array $items): array {
            $items[] = ['id' => '9', 'title' => 'Zusatzbeispiel', 'mediaType' => 'text', 'videoUrl' => '', 'captionsUrl' => '', 'steps' => [], 'duration' => '', 'leichteSprache' => 'Test'];
            return $items;
        });
        $this->assert(count(Seed_Data_Manager::get_seed_data()) === 9, 'Filter wm_social_care_loesungswege_items customizes built-in examples');
        $GLOBALS['wm_test_filters'] = [];

        $GLOBALS['wm_test_fired_actions']  = [];
        $GLOBALS['wm_test_block_bindings'] = [];
        $GLOBALS['wm_test_gb_tags']        = [];
        Social_Care_Hub_Plugin::instance()->init_plugin();

        $this->assert(in_array('wm_social_care_ready', $GLOBALS['wm_test_fired_actions'], true), 'init_plugin fires action wm_social_care_ready');
        $this->assert(isset($GLOBALS['wm_test_block_bindings']['wm-social-care/info']), 'Block Bindings source wm-social-care/info is registered');
        $this->assert(
            $GLOBALS['wm_test_gb_tags'] !== [] && ($GLOBALS['wm_test_gb_tags'][0]['tag'] ?? '') === 'wm_social_care_info',
            'GenerateBlocks 2.x dynamic tag wm_social_care_info is registered'
        );
        $this->assert(
            Ecosystem_Integrations::resolve_block_binding(['key' => 'entlastungsbetrag_month', 'pflegegrad' => 2]) === '131,00 €'
                && Ecosystem_Integrations::render_generateblocks_tag(['field' => 'pflegegeld_month', 'pflegegrad' => 3]) === '599,00 €',
            'Block Bindings and GenerateBlocks dynamic tag resolve statutory amounts accurately'
        );
        $GLOBALS['wm_test_filters'] = [];
    }

    private function test_standalone_load_in_own_process(): void {
        // proc_open with an argument list: no shell is involved.
        $script  = realpath(__DIR__ . '/test-standalone-load.php');
        $process = proc_open([PHP_BINARY, (string) $script], [1 => ['pipe', 'w'], 2 => ['redirect', 1]], $pipes);
        $output  = '';
        $code    = 1;
        if (is_resource($process)) {
            $output = (string) stream_get_contents($pipes[1]);
            fclose($pipes[1]);
            $code = proc_close($process);
        }
        $lines = preg_split('/\R/', trim($output)) ?: [];
        $this->assert($code === 0, 'Plugin loads without wm-suite-hub (separate process: ' . (end($lines) ?: 'no output') . ')');
    }

    /**
     * Catalog files exist for the loaded text domain in the 24 EU languages (Greek, Estonian, Irish and
     * Latvian are stored without a region, the others with one). Presence only; test_catalogs_from_code checks
     * their content. Until 2026-09-14 this test pinned the catalogs of the former domain wm-social-care-a11y, which
     * WordPress never loads.
     */
    private function test_eu_24_languages_installed(): void {
        $lang_dir = __DIR__ . '/../languages';
        // WordPress locale => file suffixes accepted for it.
        $wp_locales = [
            'bg_BG' => ['bg_BG'], 'cs_CZ' => ['cs_CZ'], 'da_DK' => ['da_DK'], 'de_DE' => ['de_DE'],
            'el' => ['el', 'el_GR'], 'en_GB' => ['en_GB'], 'es_ES' => ['es_ES'], 'et' => ['et', 'et_EE'],
            'fi' => ['fi', 'fi_FI'], 'fr_FR' => ['fr_FR'], 'ga' => ['ga', 'ga_IE'], 'hr' => ['hr', 'hr_HR'],
            'hu_HU' => ['hu_HU'], 'it_IT' => ['it_IT'], 'lt_LT' => ['lt_LT'], 'lv' => ['lv', 'lv_LV'],
            'mt_MT' => ['mt_MT'], 'nl_NL' => ['nl_NL'], 'pl_PL' => ['pl_PL'], 'pt_PT' => ['pt_PT'],
            'ro_RO' => ['ro_RO'], 'sk_SK' => ['sk_SK'], 'sl_SI' => ['sl_SI'], 'sv_SE' => ['sv_SE'],
        ];

        $missing = [];
        foreach ($wp_locales as $locale => $suffixes) {
            $found = array_filter($suffixes, static fn(string $suffix): bool => file_exists($lang_dir . '/wm-social-care-accessibility-hub-' . $suffix . '.mo'));
            if ($found === []) {
                $missing[] = $locale;
            }
        }
        $this->assert($missing === [], 'Catalog files of the plugin text domain exist for the 24 EU languages (missing: ' . (implode(', ', $missing) ?: 'none') . ')');
    }

    /**
     * The template holds every gettext string of the code, and no catalog of the plugin's text domain carries
     * double-encoded UTF-8. Until 2026-09-15 the template held 13 strings plus 6 double-encoded copies and none of the
     * 28 admin strings, and the compiled de_DE catalog could not be read.
     */
    private function test_catalogs_from_code(): void {
        $code = (string) file_get_contents(__DIR__ . '/../wm-social-care-accessibility-hub.php');
        foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator(__DIR__ . '/../includes', FilesystemIterator::SKIP_DOTS)) as $file) {
            $code .= (string) file_get_contents((string) $file);
        }
        preg_match_all("/\b(?:__|_e|esc_html__|esc_attr__|esc_html_e|esc_attr_e)\(\s*'((?:[^'\\\\]|\\\\.)*)'/", $code, $single);
        preg_match_all("/\b_n\(\s*'((?:[^'\\\\]|\\\\.)*)',\s*'((?:[^'\\\\]|\\\\.)*)'/", $code, $plural);
        $pot     = (string) @file_get_contents(__DIR__ . '/../languages/wm-social-care-accessibility-hub.pot');
        $missing = [];
        foreach (array_unique(array_merge($single[1], $plural[1], $plural[2])) as $msgid) {
            $needle = '"' . addcslashes(stripslashes($msgid), '"') . '"';
            if (strpos($pot, 'msgid ' . $needle) === false && strpos($pot, 'msgid_plural ' . $needle) === false) {
                $missing[] = $msgid;
            }
        }
        $this->assert(count($single[1]) >= 60 && $missing === [], sprintf('Every gettext string of the code (%d) is in the template (missing: %s)', count($single[1]), implode(' | ', array_slice($missing, 0, 5)) ?: 'none'));

        $broken = [];
        foreach (glob(__DIR__ . '/../languages/wm-social-care-accessibility-hub*.{po,pot}', GLOB_BRACE) ?: [] as $file) {
            if (preg_match('/Ã[\x{0080}-\x{00BF}]|Ã[¤¶¼Ÿ„–œ©]/u', (string) file_get_contents($file))) {
                $broken[] = basename($file);
            }
        }
        $this->assert($broken === [], 'No catalog of the plugin text domain carries double-encoded UTF-8 (' . (implode(', ', array_slice($broken, 0, 5)) ?: 'none') . ')');
    }

    private function test_documents_match_the_code(): void {
        $root   = __DIR__ . '/..';
        $readme = (string) file_get_contents($root . '/README.md');
        $this->assert(
            !preg_match('/!\[[^\]]*\]\((?!https?:)[^)]*assets\/images/', $readme) && strpos($readme, 'Translations-24%20EU%20Languages') === false && strpos($readme, 'Admin screen on the WM Suite admin family') === false,
            'README embeds no marketing image, promises no translated bodies and points at no stale screenshot'
        );
        $this->assert(is_link($root . '/AGENTS.md') && readlink($root . '/AGENTS.md') === 'CLAUDE.md', 'AGENTS.md is a symlink to CLAUDE.md');
        $this->assert(file_get_contents($root . '/AGENTS.md') === file_get_contents($root . '/CLAUDE.md') && strpos((string) file_get_contents($root . '/CLAUDE.md'), 'There is no `/src/`.') !== false, 'AGENTS.md equals CLAUDE.md and documents layout');
        $bom = json_decode((string) @file_get_contents($root . '/bom.json'), true);
        $this->assert(
            is_array($bom) && ($bom['specVersion'] ?? '') === '1.6'
                && ($bom['metadata']['component']['version'] ?? '') === WM_SOCIAL_CARE_VERSION
                && ($bom['metadata']['component']['licenses'][0]['license']['id'] ?? '') === 'GPL-2.0-or-later',
            'CycloneDX 1.6 bom.json matches plugin version and GPL-2.0-or-later license'
        );
        $this->assert(strpos((string) file_get_contents($root . '/llms.txt'), 'PSR-12') === false && strpos((string) file_get_contents($root . '/public/humans.txt'), 'PSR-12') === false, 'llms.txt and humans.txt claim no PSR-12 (the repository has no phpcs configuration)');
        $this->assert((bool) preg_match('/^assets\/images\/$/m', (string) file_get_contents($root . '/.distignore')), 'Marketing images are left out of the plugin package');
        $ci = (string) file_get_contents($root . '/.github/workflows/ci.yml');
        $this->assert(strpos($ci, 'run: bash tests/run-mutations.sh') !== false && strpos($ci, 'TEST SUITE RESULTS') !== false && strpos($ci, 'bom.json') === false, 'CI runs the mutation check, reads the results line and has no step that can only warn');
        $this->assert(preg_match('/[\x{1F300}-\x{1FAFF}\x{2600}-\x{27BF}\x{2B50}]/u', (string) file_get_contents($root . '/CHANGELOG.md')) === 0, 'CHANGELOG.md carries no emoji');
        $readme_txt = (string) file_get_contents($root . '/readme.txt');
        $short      = preg_match('/^License URI:.*\R\R(.+)$/m', $readme_txt, $m) ? trim($m[1]) : '';
        $this->assert($short !== '' && strlen($short) <= 150, 'readme.txt short description within 150 characters');
    }

    private function test_late_token_sheet_inline_once(): void {
        $GLOBALS['wm_test_styles']   = [];
        $GLOBALS['wm_test_did_head'] = 0;
        $early = Shortcodes::render_bedarfscheck();
        $this->assert(strpos($early, 'wm-social-care-frontend-inline-css') === false && in_array(Shortcodes::FRONTEND_STYLE_HANDLE, $GLOBALS['wm_test_styles'], true), 'Before wp_head the token sheet is enqueued for the head, not printed inline');

        $GLOBALS['wm_test_did_head']   = 1;
        $GLOBALS['wm_test_style_done'] = false;
        $late  = Accessibility_Toolbar::render();
        $again = Shortcodes::render_bedarfscheck();
        $this->assert(
            substr_count($late, '<style id="wm-social-care-frontend-inline-css">') === 1 && strpos($late, '--color-slate-400') !== false && strpos($late, 'wm-social-care-frontend-inline-css') < strpos($late, '<aside'),
            'After wp_head the first component prints the token sheet inline, in front of its markup'
        );
        $this->assert(strpos($again, 'wm-social-care-frontend-inline-css') === false, 'The inline token sheet is printed once per page');
        $GLOBALS['wm_test_did_head'] = 0;
    }

    /**
     * El adapter satisface el contrato del Hub, y el stub no ha derivado de él.
     *
     * Este caso existe porque la suite pasaba en la máquina de Arnold y era
     * IMPOSIBLE que pasara en un checkout limpio: el `implements` del adapter
     * es incondicional y la interfaz sólo se cargaba si el repo hermano
     * wm-suite-hub estaba en disco. Lo destapó el runner local de act.
     *
     * La paridad se comprueba comparando los DOS FICHEROS en texto, no la
     * interfaz cargada contra sí misma: cuando el Hub está presente es la real
     * la que se carga, así que preguntarle sus métodos al intérprete daría un
     * check que no puede fallar nunca.
     */
    private function test_suite_hub_contract_parity(): void {
        $iface = 'ArnoldWender\SuiteHub\Contracts\WM_Plugin_Module_Interface';

        // Esto SÍ corre en CI, y es lo que estaba roto: sin Hub el fichero
        // ni siquiera llegaba a cargarse.
        $this->assert(
            interface_exists($iface),
            'Suite Hub contract resolves without the Hub installed'
        );
        $this->assert(
            new Social_Care_Spoke_Adapter() instanceof $iface,
            'Spoke adapter satisfies the Suite Hub contract'
        );

        $real = defined('WP_PLUGIN_DIR')
            ? WP_PLUGIN_DIR . '/wm-suite-hub/src/Contracts/WM_Plugin_Module_Interface.php'
            : '';
        if ($real === '' || !file_exists($real)) {
            // Sin el Hub en disco la deriva no es medible. Se declara en vez
            // de contarse como aprobado: un check que no corre no es un check
            // que pasa.
            echo " [SKIP] Stub/Hub parity — wm-suite-hub not on disk (expected in CI)\n";
            return;
        }

        $methods = static function (string $file): array {
            preg_match_all(
                '/public function (\w+)\s*\(/',
                (string) file_get_contents($file),
                $m
            );
            $names = $m[1];
            sort($names);
            return $names;
        };

        $real_methods = $methods($real);
        $stub_methods = $methods(__DIR__ . '/stubs/suite-hub-contracts.php');

        $this->assert(
            $real_methods === $stub_methods,
            sprintf(
                'Stub mirrors the real Suite Hub interface (%d methods; missing: %s, extra: %s)',
                count($real_methods),
                implode(',', array_diff($real_methods, $stub_methods)) ?: 'none',
                implode(',', array_diff($stub_methods, $real_methods)) ?: 'none'
            )
        );
    }
}

$tester = new Test_Social_Care_Hub();
$tester->run();
