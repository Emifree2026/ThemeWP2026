<?php
/**
 * Page template: /privacy/
 * Delegates to template-parts/page-legal.php with slug=privacy.
 */

require_once get_template_directory() . '/template-parts/page-legal.php';
emifree_seo_register( 'privacy' );
get_header();
emifree_render_legal_page_body( 'privacy' );
get_footer();