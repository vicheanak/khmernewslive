<?php
/**
 * seoboost back compat functionality
 *
 * Prevents seoboost from running on WordPress versions prior to 4.7,
 * since this theme is not meant to be backward compatible beyond that and
 * relies on many newer functions and markup changes introduced in 4.7.
 *
 * @package seoboost
 */

/**
 * Prevent switching to seoboost on old versions of WordPress.
 *
 * Switches to the default theme.
 *
 */
function seoboost_switch_theme() {
	switch_theme( WP_DEFAULT_THEME );
	unset( $_GET['activated'] );
	add_action( 'admin_notices', 'seoboost_upgrade_notice' );
}
add_action( 'after_switch_theme', 'seoboost_switch_theme' );

/**
 * Adds a message for unsuccessful theme switch.
 *
 * Prints an update nag after an unsuccessful attempt to switch to
 * seoboost on WordPress versions prior to 4.7.
 * @global string $wp_version WordPress version.
 */
function seoboost_upgrade_notice() {
	$message = sprintf( __( 'seoboost requires at least WordPress version 4.7. You are running version %s. Please upgrade and try again.', 'seoboost' ), $GLOBALS['wp_version'] );
	printf( '<div class="error"><p>%s</p></div>', $message );
}

/**
 * Prevents the Customizer from being loaded on WordPress versions prior to 4.7.
 * @global string $wp_version WordPress version.
 */
function seoboost_customize() {
	wp_die( sprintf( __( 'seoboost requires at least WordPress version 4.7. You are running version %s. Please upgrade and try again.', 'seoboost' ), $GLOBALS['wp_version'] ), '', array(
		'back_link' => true,
	) );
}
add_action( 'load-customize.php', 'seoboost_customize' );

/**
 * Prevents the Theme Preview from being loaded on WordPress versions prior to 4.7.
 * @global string $wp_version WordPress version.
 */
function seoboost_preview() {
	if ( isset( $_GET['preview'] ) ) {
		wp_die( sprintf( __( 'seoboost requires at least WordPress version 4.7. You are running version %s. Please upgrade and try again.', 'seoboost' ), $GLOBALS['wp_version'] ) );
	}
}
add_action( 'template_redirect', 'seoboost_preview' );
