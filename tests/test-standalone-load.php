<?php
/**
 * The plugin loads when WM Suite Hub is not installed.
 *
 * Until 2026-09-14 the spoke adapter declared `implements WM_Plugin_Module_Interface` unconditionally,
 * so on a site without wm-suite-hub every request ended in "Interface not found". This file must run in
 * its own PHP process: test-social-care-hub.php loads a stub of that interface, which hides the defect.
 * WP_PLUGIN_DIR points to an empty directory so the hub cannot be found on disk.
 *
 * Run: php tests/test-standalone-load.php   (exit 0 = green). Standalone, no WordPress needed.
 */

declare(strict_types=1);

$empty_plugins = sys_get_temp_dir() . '/wm-social-care-standalone-' . getmypid();
if (!is_dir($empty_plugins)) {
    mkdir($empty_plugins, 0700, true);
}

define('ABSPATH', dirname(__DIR__) . '/');
define('WP_PLUGIN_DIR', $empty_plugins);

$GLOBALS['wm_standalone_filters'] = [];
function add_action(...$args) {}
function add_filter($name, ...$args) { $GLOBALS['wm_standalone_filters'][] = $name; }
function add_shortcode(...$args) {}
function load_plugin_textdomain(...$args) {}
function plugin_dir_path($file) { return dirname($file) . '/'; }
function plugin_dir_url($file) { return ''; }
function plugin_basename($file) { return basename($file); }
function is_admin() { return false; }

$passed = 0;
$failed = 0;
function wm_standalone_assert(string $name, bool $ok): void {
    global $passed, $failed;
    echo ($ok ? '[PASS] ' : '[FAIL] ') . $name . "\n";
    $ok ? $passed++ : $failed++;
}

try {
    require dirname(__DIR__) . '/wm-social-care-accessibility-hub.php';
    $loaded = true;
} catch (\Throwable $e) {
    echo '[ERROR] ' . get_class($e) . ': ' . $e->getMessage() . "\n";
    $loaded = false;
}

wm_standalone_assert('plugin file loads without wm-suite-hub on disk', $loaded);
wm_standalone_assert('hub contract is not declared', !interface_exists('ArnoldWender\SuiteHub\Contracts\WM_Plugin_Module_Interface'));
wm_standalone_assert('spoke adapter class is not declared', !class_exists('ArnoldWender\SocialCareA11y\Spoke\Social_Care_Spoke_Adapter', false));

if ($loaded) {
    $plugin = \ArnoldWender\SocialCareA11y\Social_Care_Hub_Plugin::instance();
    $plugin->register_with_suite_hub();
    wm_standalone_assert('no adapter instance without the hub', $plugin->get_spoke_adapter() === null);
    wm_standalone_assert('no hub filter registered without the hub', $GLOBALS['wm_standalone_filters'] === []);
    wm_standalone_assert('plugin identity still answers', $plugin->get_module_slug() === 'wm-social-care-accessibility-hub' && $plugin->get_version() === WM_SOCIAL_CARE_VERSION);
}

@rmdir($empty_plugins);

echo "\n=== STANDALONE LOAD: {$passed} passed, {$failed} failed ===\n";
exit(0 === $failed ? 0 : 1);
