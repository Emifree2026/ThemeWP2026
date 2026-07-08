<?php
/**
 * Page template: /de/impressum/
 * Delegates to template-parts/page-legal.php with slug=impressum + lang=de.
 */

require_once get_template_directory() . '/template-parts/page-legal.php';
emifree_seo_register( 'impressum', 'de' );
get_header();
emifree_render_legal_page_body( 'impressum', 'de' );
get_footer();
