<?php
/**
 * Nav data — single source of truth for header nav links.
 *
 * Each link is a full path + fragment (e.g. `/#applications`) so the
 * browser navigates to the homepage and lets the inline fragment
 * scroll land at the right section. The absolute path matters on
 * non-homepage routes such as `/impressum/`, `/privacy/`, `/terms/`:
 * without it, a click on "Applications" from the legal pages would
 * try to scroll to `#applications` on the legal page (where the
 * section doesn't exist) and effectively do nothing.
 *
 * The header.js smooth-scroll handler selects `a[href^="#"]` only,
 * so the absolute paths here fall through to native browser navigation
 * rather than being intercepted. No handler change required.
 *
 * When a target section hasn't shipped yet the link still points home
 * correctly; the click just lands at the top of the homepage.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'emifree_nav_items' ) ) :
	function emifree_nav_items() {
		return array(
			array( 'label' => 'Applications', 'href' => '/#applications' ),
			array( 'label' => 'Products',     'href' => '/#products'     ),
			array( 'label' => 'Knowledge',    'href' => '/#knowledge'    ),
			array( 'label' => 'Technology',   'href' => '/#technology'   ),
			array( 'label' => 'Contact',      'href' => '/#contact'      ),
		);
	}
endif;