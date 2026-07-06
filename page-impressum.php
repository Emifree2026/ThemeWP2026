<?php
/**
 * Page template: /impressum/
 * Delegates to template-parts/page-legal.php with slug=impressum.
 */

require_once get_template_directory() . '/template-parts/page-legal.php';
emifree_seo_register( 'impressum' );
get_header();
emifree_render_legal_page_body( 'impressum' );
get_footer();