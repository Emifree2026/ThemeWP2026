<?php
/**
 * Page template: /terms/
 * Delegates to template-parts/page-legal.php with slug=terms.
 */

require_once get_template_directory() . '/template-parts/page-legal.php';
emifree_seo_register( 'terms' );
get_header();
emifree_render_legal_page_body( 'terms' );
get_footer();