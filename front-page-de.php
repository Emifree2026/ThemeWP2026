<?php
/**
 * Front page — German.
 *
 * Same section composition as front-page.php but loads the German
 * version of each section template. Each section template calls
 * emifree_require_section_data() internally, which picks the German
 * data file (inc/{slug}-de.php) when the active language is 'de'.
 *
 * The German section variants and data files are loaded by
 * template-parts/{slug}-de.php shims and inc/{slug}-de.php files.
 * When a particular data file hasn't been provided yet, the section
 * template falls back to English.
 *
 * Mirrors the structure of front-page.php so each piece can be
 * developed independently:
 *  - Section Hero (Piece 4 + German Hero data)
 *  - Section Applications
 *  - Section Products
 *  - Section Technology
 *  - Section Knowledge
 *  - Section Contact
 *  - Inquiry modal overlay (Piece 10, when shipped)
 */

// Landing-page SEO surface — same as front-page.php but with the
// German strings set. hreflang emits en/de alternates + x-default
// pointing at /en/ (the canonical default-locale landing). Must
// run BEFORE get_header() so the wp_head callbacks are registered
// before the <head> is emitted.
require_once get_template_directory() . '/inc/seo-front-page.php';
emifree_seo_front_page( 'de' );

get_header();
?>

<main>
	<?php if ( locate_template( 'template-parts/section-hero-de.php' ) ) : ?>
		<?php get_template_part( 'template-parts/section', 'hero-de' ); ?>
	<?php endif; ?>

	<?php if ( locate_template( 'template-parts/section-products-de.php' ) ) : ?>
		<?php get_template_part( 'template-parts/section', 'products-de' ); ?>
	<?php endif; ?>

	<?php if ( locate_template( 'template-parts/section-applications-de.php' ) ) : ?>
		<?php get_template_part( 'template-parts/section', 'applications-de' ); ?>
	<?php endif; ?>

	<?php if ( locate_template( 'template-parts/section-technology-de.php' ) ) : ?>
		<?php get_template_part( 'template-parts/section', 'technology-de' ); ?>
	<?php endif; ?>

	<?php if ( locate_template( 'template-parts/section-knowledge-de.php' ) ) : ?>
		<?php get_template_part( 'template-parts/section', 'knowledge-de' ); ?>
	<?php endif; ?>

	<?php if ( locate_template( 'template-parts/section-contact-de.php' ) ) : ?>
		<?php get_template_part( 'template-parts/section', 'contact-de' ); ?>
	<?php endif; ?>
</main>

<?php
if ( locate_template( 'template-parts/inquiry-modal.php' ) ) {
	get_template_part( 'template-parts/inquiry-modal' );
}

get_footer();
