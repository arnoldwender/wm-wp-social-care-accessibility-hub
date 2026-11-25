<?php
/**
 * Contrato mínimo de WM Suite Hub para poder correr los tests sin el Hub.
 *
 * POR QUÉ EXISTE ESTE FICHERO
 * --------------------------
 * `Social_Care_Spoke_Adapter` declara `implements
 * \ArnoldWender\SuiteHub\Contracts\WM_Plugin_Module_Interface`, y ese
 * `implements` es INCONDICIONAL — pero la carga de la interfaz sí es
 * condicional (`file_exists( WP_PLUGIN_DIR . '/wm-suite-hub/...' )`). Si el
 * Hub no está en disco, PHP no puede declarar la clase y el fichero fatalea
 * antes de que corra un solo test.
 *
 * En la máquina de Arnold `WP_PLUGIN_DIR` resuelve al workspace, donde el
 * repo hermano `wm-suite-hub` SÍ existe, así que la suite pasaba en local
 * mientras era imposible que pasara en un checkout limpio. La cabecera del
 * test afirmaba "Zero WP dependency for CI execution" y esa afirmación no
 * reproducía: la dependencia no era WordPress, era un repo hermano sin
 * declarar. Lo destapó el runner local de act, no la máquina.
 *
 * CÓMO FUNCIONA
 * -------------
 * Se prefiere SIEMPRE la interfaz real cuando está disponible, para que en un
 * entorno con el Hub instalado los tests sigan validando contra el contrato
 * de verdad y no contra una copia que puede derivar. El stub sólo entra
 * cuando no hay Hub — que es exactamente el caso de CI.
 *
 * Cinco plugins hermanos (wm-dsgvo-privacy-eraser, wm-bfsg-ast-remediator,
 * wm-dsgvo-newsletter-suite, wm-kanzlei-ai-sanitizer, wm-cyber-events-core) ya
 * resuelven esto declarando el mismo stub en su harness; esto no inventa un
 * patrón nuevo, adopta el que la flota ya usa. Se declara con sintaxis de
 * bloque de namespace en vez de con evaluación dinámica de código, porque el
 * propio CI corre una auditoría de seguridad sobre las fuentes.
 *
 * @package ArnoldWender\SocialCareA11y\Tests
 */

declare(strict_types=1);

namespace {
    // Ruta canónica del Hub cuando está instalado como plugin hermano.
    if (
        ! \interface_exists('ArnoldWender\SuiteHub\Contracts\WM_Plugin_Module_Interface')
        && \defined('WP_PLUGIN_DIR')
        && \file_exists(WP_PLUGIN_DIR . '/wm-suite-hub/src/Contracts/WM_Plugin_Module_Interface.php')
    ) {
        require_once WP_PLUGIN_DIR . '/wm-suite-hub/src/Contracts/WM_Plugin_Module_Interface.php';
    }
}

namespace ArnoldWender\SuiteHub\Contracts {
    if ( ! \interface_exists('ArnoldWender\SuiteHub\Contracts\WM_Plugin_Module_Interface') ) {
        /**
         * Espejo de src/Contracts/WM_Plugin_Module_Interface.php de wm-suite-hub.
         *
         * Si el Hub añade un método a su interfaz, este stub queda corto y el
         * adapter dejaría de satisfacer el contrato real aunque los tests
         * siguieran verdes. Esa deriva la cubre el test de paridad en
         * test-social-care-hub.php, que compara este stub contra la interfaz
         * real cuando el Hub está presente en disco.
         */
        interface WM_Plugin_Module_Interface {
            public function get_module_slug(): string;
            public function get_name(): string;
            public function get_version(): string;
            public function get_description(): string;
            public function get_health_status(): string;
            public function get_sbom_data(): array;
            public function get_dependencies(): array;
            public function get_settings_url(): ?string;
            public function get_telemetry_metrics(): array;
            public function execute_action(string $action, array $params = []): array;
        }
    }
}
