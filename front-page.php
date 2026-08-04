<?php
/**
 * Front page — composes the homepage from modular template parts.
 *
 * Sections ship in order:
 *  - Hero (Piece 4)
 *  - Applications (Piece 5)
 *  - Products (Piece 6)
 *  - Technology (Piece 7)
 *  - Knowledge (Piece 8)
 *  - Contact (Piece 9)
 *  - Inquiry modal (Piece 10, included as overlay)
 */

// Landing-page SEO surface — meta description, OG/Twitter cards,
// canonical, hreflang alternates (en ↔ de + x-default), and the
// Organization / WebSite / Product JSON-LD set. Without this the
// homepage ships no description and the validator flags it as
// having no schema.org elements detected. Must run BEFORE
// get_header() so the wp_head callbacks are registered before the
// <head> is emitted.
require_once get_template_directory() . '/inc/seo-front-page.php';
emifree_seo_front_page( 'en' );

get_header();
?>

<main>
	<?php get_template_part( 'template-parts/section', 'hero' ); ?>

	<?php if ( locate_template( 'template-parts/section-products.php' ) ) : ?>
		<?php get_template_part( 'template-parts/section', 'products' ); ?>
	<?php endif; ?>

	<?php if ( locate_template( 'template-parts/section-applications.php' ) ) : ?>
		<?php get_template_part( 'template-parts/section', 'applications' ); ?>
	<?php endif; ?>

	<?php if ( locate_template( 'template-parts/section-technology.php' ) ) : ?>
		<?php get_template_part( 'template-parts/section', 'technology' ); ?>
	<?php endif; ?>

	<?php if ( locate_template( 'template-parts/section-knowledge.php' ) ) : ?>
		<?php get_template_part( 'template-parts/section', 'knowledge' ); ?>
	<?php endif; ?>

	<?php if ( locate_template( 'template-parts/section-contact.php' ) ) : ?>
		<?php get_template_part( 'template-parts/section', 'contact' ); ?>
	<?php endif; ?>
</main>

<?php
// Inquiry modal overlay (lands in Piece 10). Renders nothing if
// the template part doesn't exist yet, so older installs don't 500.
if ( locate_template( 'template-parts/inquiry-modal.php' ) ) {
	get_template_part( 'template-parts/inquiry-modal' );
}

get_footer();