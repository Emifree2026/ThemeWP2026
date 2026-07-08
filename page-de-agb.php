<?php
/**
 * Page template: /de/agb/
 * Delegates to template-parts/page-legal.php with slug=agb + lang=de.
 */

require_once get_template_directory() . '/template-parts/page-legal.php';
emifree_seo_register( 'agb', 'de' );
get_header();
emifree_render_legal_page_body( 'agb', 'de' );
get_footer();
