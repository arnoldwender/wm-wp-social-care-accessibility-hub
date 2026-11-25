<?php
/**
 * Runtime check of wm-social-care-accessibility-hub inside a real WordPress.
 *
 * Renders the shortcodes through WordPress' own content functions (wpautop, shortcode_unautop, do_blocks,
 * do_shortcode), reads the admin rows and the compiled de_DE catalog, calls the REST route through rest_do_request()
 * and the WP-CLI command in a child process, and runs the hub adapter. The plugin stores nothing: the check compares
 * the option names that contain "social" and "care" before and after and exits 1 if they changed.
 *
 *   wp eval-file wp-content/plugins/wm-social-care-accessibility-hub/tests/wp-runtime-social-care.php --user=1
 *   ssh <box> 'wp --path=<www> eval-file - --user=1' < tests/wp-runtime-social-care.php
 *
 * `wp eval-file` runs the file inside a method: state lives in $GLOBALS.
 *
 * @package ArnoldWender\SocialCareA11y
 */

defined( 'ABSPATH' ) || exit;

use ArnoldWender\SocialCareA11y\Admin\Admin_Dashboard;
use ArnoldWender\SocialCareA11y\Frontend\Accessibility_Toolbar;
use ArnoldWender\SocialCareA11y\Frontend\Shortcodes;
use ArnoldWender\SocialCareA11y\Social_Care_Hub_Plugin;

if ( ! class_exists( Shortcodes::class ) || ! method_exists( Shortcodes::class, 'frontend_tokens' ) ) {
	WP_CLI::error( 'wm-social-care-accessibility-hub with the reader-pass fixes (Shortcodes::frontend_tokens) is not loaded.' );
}

global $wpdb;
$GLOBALS['scrt']       = [ 'pass' => 0, 'fail' => 0 ];
$GLOBALS['scrt_check'] = static function ( bool $ok, string $label ): void {
	$GLOBALS['scrt'][ $ok ? 'pass' : 'fail' ]++;
	echo ( $ok ? '  [PASS] ' : '  [FAIL] ' ) . $label . "\n";
};
$GLOBALS['scrt_state'] = static function (): array {
	global $wpdb;
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery
	return $wpdb->get_col( $wpdb->prepare( 'SELECT option_name FROM %i WHERE option_name LIKE %s ORDER BY option_name', $wpdb->options, '%' . $wpdb->esc_like( 'social' ) . '%' . $wpdb->esc_like( 'care' ) . '%' ) );
};
$GLOBALS['scrt_before'] = $GLOBALS['scrt_state']();

register_shutdown_function(
	static function (): void {
		$same = $GLOBALS['scrt_state']() === $GLOBALS['scrt_before'];
		echo "\n  STORAGE CHECK: " . ( $same ? 'no social-care option or transient written' : 'DIFFERENT' ) . "\n";
		echo "  RUNTIME RESULTS: {$GLOBALS['scrt']['pass']} PASSED | {$GLOBALS['scrt']['fail']} FAILED\n";
		exit( $same && 0 === $GLOBALS['scrt']['fail'] ? 0 : 1 );
	}
);

$check = $GLOBALS['scrt_check'];

echo "\n=== WM SOCIAL CARE ACCESSIBILITY HUB · RUNTIME ===\n";

// --- Shortcodes ------------------------------------------------------------------------------------------------------
$typed = [];
foreach ( [ 'render_bedarfscheck', 'render_videothek', 'render_single_player', 'render_leichte_sprache_card' ] as $method ) {
	try {
		$typed[ $method ] = '' !== Shortcodes::$method( '' ) ? 'ok' : 'empty';
	} catch ( \Throwable $t ) {
		$typed[ $method ] = get_class( $t );
	}
}
$check( [ 'ok', 'ok', 'ok', 'ok' ] === array_values( $typed ), "the four shortcodes with attributes render with '' as WordPress before 6.5 passes it (was: TypeError): " . implode( ', ', $typed ) );

$classic = do_shortcode( shortcode_unautop( wpautop( "[wm_leichte_sprache_card title=\"Information\"]\nDie Pflegekasse zahlt.\n\nDer Pflegegrad bleibt.\n[/wm_leichte_sprache_card]" ) ) );
$block   = do_shortcode( do_blocks( "<!-- wp:shortcode -->\n[wm_leichte_sprache_card]Zeile eins\nZeile zwei[/wm_leichte_sprache_card]\n<!-- /wp:shortcode -->" ) );
$check( ! str_contains( $classic . $block, '&lt;' ) && str_contains( $classic, 'Pflege·kasse' ) && str_contains( $block, 'Zeile zwei' ), 'the Easy Language card shows no paragraph markup of WordPress as text, classic or block (was: 4 and 1 escaped tags)' );

$two = Shortcodes::render_bedarfscheck() . Shortcodes::render_bedarfscheck();
preg_match_all( '/<select id="([^"]+)"/', $two, $selects );
$library = Shortcodes::render_videothek();
$check( 2 === count( array_unique( $selects[1] ) ) && ! str_contains( $two, 'aria-describedby' ) && substr_count( $library, '<style' ) <= 2 && ! str_contains( $library, 'Datenbank' ), 'two checks get their own IDs; the library prints the player styles once and calls the examples examples (was: shared IDs, 9 style blocks, "Datenbank")' );

$GLOBALS['scrt_gettext'] = 0;
$counter                 = static function ( $translation, $text, $domain ) {
	if ( 'wm-social-care-accessibility-hub' === $domain ) {
		++$GLOBALS['scrt_gettext'];
	}
	return $translation;
};
add_filter( 'gettext', $counter, 10, 3 );
$toolbar = Accessibility_Toolbar::render();
Shortcodes::render_bedarfscheck();
remove_filter( 'gettext', $counter, 10 );
$check( $GLOBALS['scrt_gettext'] >= 15 && str_contains( $toolbar, 'utterance.onerror' ), "frontend strings go through gettext ({$GLOBALS['scrt_gettext']} calls for the toolbar and one check; was 0) and read aloud recovers from an error" );

// --- Admin rows ----------------------------------------------------------------------------------------------------------
$rows = ( new ReflectionMethod( Admin_Dashboard::class, 'measured_rows' ) )->invoke( null );
$check( 6 === count( $rows ) && [] === array_filter( $rows, static fn( $row ) => ! $row['ok'] ), 'the admin screen measures each of the five shortcodes and the post type (was: 4 rows, the two video shortcodes unmeasured)' );

// --- Catalog -------------------------------------------------------------------------------------------------------------
require_once ABSPATH . WPINC . '/pomo/mo.php';
$mo       = new MO();
$imported = $mo->import_from_file( WM_SOCIAL_CARE_PATH . 'languages/wm-social-care-accessibility-hub-de_DE.mo' );
$double   = array_filter( array_keys( $mo->entries ), static fn( $key ) => str_contains( (string) $key, 'Ã' ) );
$check( $imported && [] === $double, 'the compiled de_DE catalog imports and carries no double-encoded msgid (' . count( $mo->entries ) . ' entries; was: unreadable)' );

// --- REST and WP-CLI -----------------------------------------------------------------------------------------------------
$rest = static function ( string $pflegegrad ): array {
	$request = new WP_REST_Request( 'GET', '/wm-social-care/v1/bedarfscheck' );
	$request->set_query_params( [ 'pflegegrad' => $pflegegrad ] );
	$response = rest_ensure_response( rest_do_request( $request ) );
	return [ $response->get_status(), $response->get_data()['data']['pflegegrad'] ?? null ];
};
$check( [ 400, null ] === $rest( '2.5' ) && [ 200, 3 ] === $rest( '3' ), 'REST bedarfscheck answers 400 for 2.5 and the amounts of care degree 3 for 3' );

$cli      = static fn( string $args ) => WP_CLI::runcommand( 'wm-social-care calc ' . $args, [ 'return' => 'all', 'exit_error' => false, 'launch' => true ] );
$fraction = $cli( '--pflegegrad=2.9' );
$one      = $cli( '--pflegegrad=1' );
$check( 0 !== $fraction->return_code && 0 === $one->return_code && ! str_contains( (string) $one->stdout, '§ 42a' ) && str_contains( (string) $one->stdout, '131,00 €' ), 'wp wm-social-care calc rejects 2.9 and prints care degree 1 without a zero joint-budget line (was: care degree 2, "Bis zu 0.00")' );

// --- Hub adapter ---------------------------------------------------------------------------------------------------------
$adapter = Social_Care_Hub_Plugin::instance()->get_spoke_adapter();
if ( null !== $adapter ) {
	$check( 'HEALTHY' === $adapter->get_health_status(), 'hub adapter reports HEALTHY with the five shortcodes and the post type registered' );
} else {
	echo "  [INFO] hub not installed: adapter not checked\n";
}
