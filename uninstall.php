<?php
declare(strict_types=1);

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}

/**
 * Opt-in uninstall handler (D-UNINSTALL).
 *
 * By default (`wm_social_care_delete_data_on_uninstall` = '0' / false), all options and site content
 * are preserved when the plugin is deleted. Only when an administrator explicitly opts in on the
 * plugin's admin page does uninstall remove the plugin's option and rate-limit transients.
 * Posts of the `wm_loesungsweg` post type and their taxonomy terms remain preserved as site content.
 *
 * @package ArnoldWender\SocialCareA11y
 */

$wm_social_care_delete = function_exists( 'get_option' )
    ? get_option( 'wm_social_care_delete_data_on_uninstall', '0' )
    : '0';

if ( ! in_array( $wm_social_care_delete, [ '1', 1, true, 'on', 'yes' ], true ) ) {
    return;
}

if ( function_exists( 'delete_option' ) ) {
    delete_option( 'wm_social_care_delete_data_on_uninstall' );
}

if ( function_exists( 'is_multisite' ) && is_multisite() && function_exists( 'delete_site_option' ) ) {
    delete_site_option( 'wm_social_care_delete_data_on_uninstall' );
}

global $wpdb;
if (
    isset( $wpdb )
    && is_object( $wpdb )
    && isset( $wpdb->options )
    && method_exists( $wpdb, 'query' )
    && method_exists( $wpdb, 'prepare' )
    && method_exists( $wpdb, 'esc_like' )
) {
    $wm_sc_transient_like = $wpdb->esc_like( '_transient_wm_sc_rl_' ) . '%';
    $wm_sc_timeout_like   = $wpdb->esc_like( '_transient_timeout_wm_sc_rl_' ) . '%';
    // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Bulk cleanup of hashed rate-limit transients on explicit opt-in uninstall.
    $wpdb->query(
        $wpdb->prepare(
            "DELETE FROM {$wpdb->options} WHERE option_name LIKE %s OR option_name LIKE %s",
            $wm_sc_transient_like,
            $wm_sc_timeout_like
        )
    );
}
