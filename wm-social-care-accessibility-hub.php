<?php
/**
 * Plugin Name: WM Social Care Accessibility & Kinästhetik Hub
 * Plugin URI: https://arnoldwender.com/wordpress-plugins/wm-social-care-accessibility-hub/
 * Description: Shortcodes für Pflege- und Betreuungsseiten: Leichte-Sprache-Karte mit Mediopunkt, Pflege-Bedarfscheck mit den gesetzlichen Beträgen nach SGB XI, Barrierefreiheits-Toolbar (Schriftgröße, Kontrast, Vorlesen) und eine Videothek mit acht eingebauten Beispiel-Lösungswegen; Beitragstyp Lösungsweg mit vier Taxonomien.
 * Version: 1.3.2
 * Author: Arnold Wender
 * Author URI: https://arnoldwender.com
 * Text Domain: wm-social-care-accessibility-hub
 * Domain Path: /languages
 * Requires at least: 6.4
 * Tested up to: 7.1
 * Requires PHP: 8.1
 * License: GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 *
 * @package ArnoldWender\SocialCareA11y
 */

declare(strict_types=1);

namespace ArnoldWender\SocialCareA11y;

use ArnoldWender\SocialCareA11y\Contracts\WM_Social_Care_Module_Interface;
use ArnoldWender\SocialCareA11y\Domain\Video_Repository;
use ArnoldWender\SocialCareA11y\Domain\Seed_Data_Manager;
use ArnoldWender\SocialCareA11y\Frontend\Shortcodes;
use ArnoldWender\SocialCareA11y\Frontend\Accessible_Video_Player;
use ArnoldWender\SocialCareA11y\Frontend\Ecosystem_Integrations;
use ArnoldWender\SocialCareA11y\Admin\Admin_Dashboard;
use ArnoldWender\SocialCareA11y\API\REST_Controller;
use ArnoldWender\SocialCareA11y\Spoke\Social_Care_Spoke_Adapter;
use ArnoldWender\SocialCareA11y\CLI\Social_Care_CLI;

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

// Constants CLAUDE.md names; until 2026-09-14 the file defined none. The standalone test suites
// include this file without WordPress, so the two path helpers are guarded like the hooks below.
define('WM_SOCIAL_CARE_VERSION', '1.3.2');
define('WM_SOCIAL_CARE_FILE', __FILE__);
define('WM_SOCIAL_CARE_PATH', function_exists('plugin_dir_path') ? plugin_dir_path(__FILE__) : __DIR__ . '/');
define('WM_SOCIAL_CARE_URL', function_exists('plugin_dir_url') ? plugin_dir_url(__FILE__) : '');

// Autoload internal classes.
require_once __DIR__ . '/includes/Contracts/Language_Level_Enum.php';
require_once __DIR__ . '/includes/Contracts/WM_Social_Care_Module_Interface.php';
require_once __DIR__ . '/includes/Domain/Leichte_Sprache_Governor.php';
require_once __DIR__ . '/includes/Domain/Care_Needs_Checker.php';
require_once __DIR__ . '/includes/Domain/Contrast_Governor.php';
require_once __DIR__ . '/includes/Domain/Video_Repository.php';
require_once __DIR__ . '/includes/Domain/Seed_Data_Manager.php';
require_once __DIR__ . '/includes/Frontend/Accessibility_Toolbar.php';
require_once __DIR__ . '/includes/Frontend/Accessible_Video_Player.php';
require_once __DIR__ . '/includes/Frontend/Shortcodes.php';
require_once __DIR__ . '/includes/Frontend/Ecosystem_Integrations.php';
require_once __DIR__ . '/includes/Admin/Admin_Dashboard.php';
require_once __DIR__ . '/includes/API/REST_Controller.php';
require_once __DIR__ . '/includes/CLI/Social_Care_CLI.php';

final class Social_Care_Hub_Plugin implements WM_Social_Care_Module_Interface {
    private static ?self $instance = null;

    /** Null until wm-suite-hub is installed and loaded on plugins_loaded. */
    private ?Social_Care_Spoke_Adapter $spoke_adapter = null;

    public static function instance(): self {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action('init', [$this, 'init_plugin']);
        add_action('plugins_loaded', [$this, 'register_with_suite_hub'], 20);

        if (defined('WP_CLI') && WP_CLI) {
            Social_Care_CLI::register();
        }
    }

    public function init_plugin(): void {
        load_plugin_textdomain('wm-social-care-accessibility-hub', false, dirname(plugin_basename(__FILE__)) . '/languages');

        Video_Repository::register_cpt_and_taxonomies();
        Shortcodes::register();
        Ecosystem_Integrations::register();
        REST_Controller::register_routes();

        if (is_admin()) {
            Admin_Dashboard::register();
        }

        if (function_exists('do_action')) {
            do_action('wm_social_care_ready', $this);
        }
    }

    /** Registers the adapter lazily only when WM Suite Hub's contract is available. */
    public function register_with_suite_hub(): void {
        if (!interface_exists('ArnoldWender\\SuiteHub\\Contracts\\WM_Plugin_Module_Interface')) {
            if (!defined('WP_PLUGIN_DIR') || !file_exists(WP_PLUGIN_DIR . '/wm-suite-hub/src/Contracts/WM_Plugin_Module_Interface.php')) {
                return;
            }
        }

        if (!class_exists(Social_Care_Spoke_Adapter::class, false)) {
            require_once __DIR__ . '/includes/Spoke/Social_Care_Spoke_Adapter.php';
        }

        if (null === $this->spoke_adapter && class_exists(Social_Care_Spoke_Adapter::class, false)) {
            $this->spoke_adapter = new Social_Care_Spoke_Adapter();
        }

        if ($this->spoke_adapter === null || !function_exists('add_filter')) {
            return;
        }

        add_filter('wm_register_suite_module', function (array $modules): array {
            $modules[] = $this->spoke_adapter;
            return $modules;
        });
    }

    public function get_spoke_adapter(): ?Social_Care_Spoke_Adapter {
        return $this->spoke_adapter;
    }

    public function get_module_slug(): string {
        return 'wm-social-care-accessibility-hub';
    }

    public function get_name(): string {
        return 'WM Social Care Accessibility & Kinästhetik Hub';
    }

    public function get_version(): string {
        return WM_SOCIAL_CARE_VERSION;
    }
}

// Instantiate plugin singleton.
Social_Care_Hub_Plugin::instance();
