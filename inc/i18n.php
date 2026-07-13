<?php
/**
 * i18n.php — kept as a no-op shim for the English section templates.
 *
 * The previous bilingual architecture (function guards, dispatcher,
 * emifree_call() helper) was replaced with a hard-separated approach:
 *   - English homepage (/) uses section-{name}.php + inc/{name}.php
 *   - German homepage (/de/) uses section-{name}-de.php with strings
 *     inlined directly into the template — no data loader required.
 *
 * The German data files (inc/{name}_de.php) and the German blog-cards
 * file were deleted alongside this shim. inc/i18n.php is kept so
 * existing English section templates continue to work unchanged:
 * their `emifree_require_section_data( $slug )` call still resolves to
 * inc/{slug}.php (English) — never to a deleted _de file.
 *
 * If a future piece wants to delete this file too, grep for
 * emifree_require_section_data across template-parts/section-*.php
 * and replace those calls with direct `require_once` of inc/{slug}.php.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Load the English data file for a section. Always English; the German
 * sections are fully self-contained and don't call this helper.
 *
 * @param string $slug Section slug (e.g. 'hero', 'applications').
 */
function emifree_require_section_data( $slug ) {
	$emifree_path = get_template_directory() . '/inc/' . $slug . '.php';
	if ( file_exists( $emifree_path ) ) {
		require_once $emifree_path;
	}
}

/**
 * Legacy alias — kept for any code that still calls the old name.
 */
function emifree_require_hero_data() {
	emifree_require_section_data( 'hero' );
}