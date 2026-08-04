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
 * Language-aware: when the request URI starts with `/de/` (after
 * stripping the home subpath, so this works on both root installs
 * and subpath installs like '/wordpress/de/...'), labels come back
 * in German and hrefs are prefixed with the German route so a user
 * who selects German stays on German after clicking any nav item.
 * The active-language detection uses the request path (not the
 * emifree_lang cookie) because the path is the source of truth for
 * which template was actually rendered — the cookie can be stale.
 *
 * hrefs are built with home_url() so the home subpath is encoded
 * automatically — on a root install '/de/#applications' resolves to
 * 'https://example.com/de/#applications', on a subpath install
 * '/wordpress/de/#applications' resolves to
 * 'https://example.com/wordpress/de/#applications'. The header.js
 * smooth-scroll handler intercepts `a[href^="/#"]` and `a[href^="/"]`
 * uniformly — the same-path check covers both the root and subpath
 * install shapes via pathnames parsed after the subpath is stripped.
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
		// truth for which page is being rendered. Strip the home subpath
		// first so '/wordpress/de/impressum/' becomes '/de/impressum/'
		// before the prefix check — mirrors the normalization in
		// emifree_get_lang() and inc/footer.php.
		$emifree_uri  = isset( $_SERVER['REQUEST_URI'] ) ? wp_unslash( $_SERVER['REQUEST_URI'] ) : '';
		$emifree_uri  = (string) parse_url( $emifree_uri, PHP_URL_PATH );
		$emifree_home = function_exists( 'emifree_home_subpath' ) ? emifree_home_subpath() : '';
		if ( '' !== $emifree_home && 0 === strpos( $emifree_uri, $emifree_home ) ) {
			$emifree_uri = substr( $emifree_uri, strlen( $emifree_home ) );
		}
		$emifree_is_de = ( 0 === strpos( $emifree_uri, '/de' ) );

		if ( $emifree_is_de ) {
			return array(
				array( 'label' => 'Produkte',    'href' => home_url( '/de/#products' ) ),
				array( 'label' => 'Anwendungen', 'href' => home_url( '/de/#applications' ) ),
				array( 'label' => 'Wissen',      'href' => home_url( '/de/#knowledge' ) ),
				array( 'label' => 'Technologie', 'href' => home_url( '/de/#technology' ) ),
				array( 'label' => 'Kontakt',     'href' => home_url( '/de/#contact' ) ),
			);
		}

		return array(
			// /en/#anchor (not bare /#anchor) so the in-page link stays on
			// /en/ — otherwise the click navigates to / which the
			// emifree_maybe_redirect_home_to_de() handler 301s to /de/,
			// silently flipping EN users to German mid-click.
			array( 'label' => 'Products',     'href' => home_url( '/en/#products' ) ),
			array( 'label' => 'Applications', 'href' => home_url( '/en/#applications' ) ),
			array( 'label' => 'Knowledge',    'href' => home_url( '/en/#knowledge' ) ),
			array( 'label' => 'Technology',   'href' => home_url( '/en/#technology' ) ),
			array( 'label' => 'Contact',      'href' => home_url( '/en/#contact' ) ),
		);
	}
endif;