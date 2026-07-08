<?php
/**
 * Page template: /de/datenschutz/
 * Delegates to template-parts/page-legal.php with slug=datenschutz + lang=de.
 */

require_once get_template_directory() . '/template-parts/page-legal.php';
emifree_seo_register( 'datenschutz', 'de' );
get_header();
emifree_render_legal_page_body( 'datenschutz', 'de' );
get_footer();
