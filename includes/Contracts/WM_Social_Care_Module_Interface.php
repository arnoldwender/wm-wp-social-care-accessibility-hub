<?php
declare(strict_types=1);

namespace ArnoldWender\SocialCareA11y\Contracts;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Identity shared by the plugin singleton and the hub adapter.
 *
 * Until 2026-09-14 the contract also required is_bfsg_compliant(), which both classes answered with a
 * fixed `true` that nothing read, and get_sbom_package(), whose BOM document the hub could not use.
 */
interface WM_Social_Care_Module_Interface {
    public function get_module_slug(): string;
    public function get_name(): string;
    public function get_version(): string;
}
