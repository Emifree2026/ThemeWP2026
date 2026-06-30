<?php
/**
 * Emifree Theme — primary entry point.
 *
 * Responsibilities:
 *  - Enqueue built CSS (and per-section JS via wp_enqueue_script when added)
 *  - Declare theme support (title-tag, post-thumbnails)
 *  - Provide template helpers (loaded on demand in subsequent pieces)
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! defined( 'EMIFREE_THEME_VERSION' ) ) {
	define( 'EMIFREE_THEME_VERSION', '1.0.0' );
}

/**
 * Enqueue built stylesheet (assets/css/main.css, committed to the repo so
 * the theme is install-and-go). Also enqueues per-section JS files.
 * Per-section JS is loaded only on pages where the section actually
 * renders — header.js loads everywhere (header.php is global), the
 * others load only on the routes that use them.
 */
function emifree_enqueue_assets() {
	wp_enqueue_style(
		'emifree-main',
		get_template_directory_uri() . '/assets/css/main.css',
		array(),
		EMIFREE_THEME_VERSION
	);

	// Global header script — loaded on every page because the
	// header is rendered by header.php globally.
	wp_enqueue_script(
		'emifree-header',
		get_template_directory_uri() . '/assets/js/sections/header.js',
		array(),
		EMIFREE_THEME_VERSION,
		true
	);
}
add_action( 'wp_enqueue_scripts', 'emifree_enqueue_assets' );

/**
 * Theme support declarations. title-tag delegates <title> rendering to WP;
 * post-thumbnails enables featured image support.
 */
add_theme_support( 'title-tag' );
add_theme_support( 'post-thumbnails' );