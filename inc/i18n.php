<?php
/**
 * Emifree theme — bilingual helpers (English / German).
 *
 * The active language is read from the `emifree_lang` cookie, which is
 * set by the Header's EN/DE switcher. Defaults to 'en' if the cookie
 * is absent.
 *
 * Section templates load bilingual data via:
 *
 *     emifree_require_section_data( 'hero' );
 *
 *     // Then the section's functions are available:
 *     // emifree_hero_data() returns an array, OR
 *     // the file defines top-level constants / functions.
 *
 * The German data file pattern (per the user's technology_de.php
 * example): a sibling file `inc/{slug}-de.php` that exposes the same
 * functions as the English file, with German content. The helper
 * loads the German file when active, otherwise English.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get the active site language code ('en' or 'de').
 *
 * Read from the emifree_lang cookie set by the Header language
 * switcher. Falls back to 'en' if absent or unrecognized.
 */
function emifree_get_lang() {
	if ( ! isset( $_COOKIE['emifree_lang'] ) ) {
		return 'en';
	}
	$emifree_raw = strtolower( sanitize_text_field( wp_unslash( $_COOKIE['emifree_lang'] ) ) );
	return in_array( $emifree_raw, array( 'en', 'de' ), true ) ? $emifree_raw : 'en';
}

/**
 * Load a section's data file for the active language.
 *
 * Tries `inc/{slug}-de.php` when active language is German AND that
 * file exists, otherwise `inc/{slug}.php` (English). Each file is
 * expected to define the same set of top-level functions (or
 * constants) — the difference is just the strings they return.
 *
 * Use this at the top of a section template:
 *
 *     require_once get_template_directory() . '/inc/i18n.php';
 *     emifree_require_section_data( 'applications' );
 *     $icons = emifree_application_icons();
 */
function emifree_require_section_data( $slug ) {
	$emifree_active = emifree_get_lang();
	$emifree_base    = get_template_directory() . '/inc/';
	$emifree_chosen  = $emifree_base . $slug . '.php';
	if ( 'de' === $emifree_active && file_exists( $emifree_base . $slug . '-de.php' ) ) {
		$emifree_chosen = $emifree_base . $slug . '-de.php';
	}
	require_once $emifree_chosen;
}

/**
 * Conditional include for the homepage dispatch path.
 *
 * Used by front-page.php to optionally load the German hero variant
 * when active. Falls back to English. Returns nothing; just loads
 * the file.
 */
function emifree_require_hero_data() {
	emifree_require_section_data( 'hero' );
}
