<?php
/**
 * Nav data — single source of truth for header nav links.
 *
 * Each link points at an in-page anchor that exists on the homepage
 * (`front-page.php`). When a target section hasn't shipped yet, the
 * anchor is still rendered; the click won't find a target until the
 * section is composed into front-page.php.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'emifree_nav_items' ) ) :
	function emifree_nav_items() {
		return array(
			array( 'label' => 'Applications', 'href' => '#applications' ),
			array( 'label' => 'Products',     'href' => '#products'     ),
			array( 'label' => 'Knowledge',    'href' => '#knowledge'    ),
			array( 'label' => 'Technology',   'href' => '#technology'   ),
			array( 'label' => 'Contact',      'href' => '#contact'      ),
		);
	}
endif;