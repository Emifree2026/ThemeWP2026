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
 * Language-aware: when the request URI starts with `/de/`, labels
 * come back in German and hrefs are prefixed with `/de/` so a user
 * who selects German stays on German after clicking any nav item.
 * The active-language detection uses the request path (not the
 * emifree_lang cookie) because the path is the source of truth for
 * which template was actually rendered — the cookie can be stale.
 *
 * The header.js smooth-scroll handler intercepts `a[href^="/#"]` and
 * matches path equality so the same-page link still scrolls smoothly
 * after this server-side language switch. The handler's same-path
 * check covers both `/` and `/de/` so a click on Anwendungen while
 * already on `/de/` scrolls in place instead of doing a full reload.
 *
 * When a target section hasn't shipped yet the link still points home
 * correctly; the click just lands at the top of the homepage.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'emifree_nav_items' ) ) :
	function emifree_nav_items() {
		// Path-based detection — emifree_get_lang() reads a cookie which
		// can be stale or absent; the request URI is the actual ground
		// truth for which page is being rendered.
		$emifree_uri = isset( $_SERVER['REQUEST_URI'] ) ? wp_unslash( $_SERVER['REQUEST_URI'] ) : '';
		$emifree_is_de = ( 0 === strpos( $emifree_uri, '/de' ) );

		if ( $emifree_is_de ) {
			return array(
				array( 'label' => 'Anwendungen', 'href' => '/de/#applications' ),
				array( 'label' => 'Produkte',    'href' => '/de/#products'     ),
				array( 'label' => 'Wissen',      'href' => '/de/#knowledge'    ),
				array( 'label' => 'Technologie', 'href' => '/de/#technology'   ),
				array( 'label' => 'Kontakt',     'href' => '/de/#contact'      ),
			);
		}

		return array(
			array( 'label' => 'Applications', 'href' => '/#applications' ),
			array( 'label' => 'Products',     'href' => '/#products'     ),
			array( 'label' => 'Knowledge',    'href' => '/#knowledge'    ),
			array( 'label' => 'Technology',   'href' => '/#technology'   ),
			array( 'label' => 'Contact',      'href' => '/#contact'      ),
		);
	}
endif;