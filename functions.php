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
 * the theme is install-and-go). When pieces add per-section JS, this function
 * will also wp_enqueue_script() them conditionally on the routes that need
 * them.
 */
function emifree_enqueue_assets() {
	wp_enqueue_style(
		'emifree-main',
		get_template_directory_uri() . '/assets/css/main.css',
		array(),
		EMIFREE_THEME_VERSION
	);
}
add_action( 'wp_enqueue_scripts', 'emifree_enqueue_assets' );

/**
 * Theme support declarations. title-tag delegates <title> rendering to WP;
 * post-thumbnails enables featured image support.
 */
add_theme_support( 'title-tag' );
add_theme_support( 'post-thumbnails' );