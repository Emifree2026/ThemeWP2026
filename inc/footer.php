<?php
/**
 * Footer link data + helpers — single source of truth for the Footer.
 *
 * Mirrors src/components/Footer.jsx post-cleanup state. Three columns:
 * Company (Blog, Contact), Resources (Case Studies), Legal (Impressum,
 * Privacy Policy, General Terms).
 *
 * In-page anchors are stored as full path + fragment (`/#contact`,
 * `/#knowledge`) so they work cross-page from non-homepage routes
 * (e.g. /impressum/, /privacy/, /terms/). The smooth-scroll handler in
 * header.js matches both `#anchor` and `/#anchor` and only intercepts
 * the latter when the user is already on the same path; otherwise
 * the browser performs a full navigation to the homepage.
 *
 * Language-aware: when the request URI starts with `/de/`, the
 * Company / Resources column headers + the Blog / Contact /
 * Case Studies labels come back in German and the in-page hrefs are
 * prefixed with `/de/` so a user who selected German stays on
 * German after clicking any footer link. The Legal column has its
 * own dispatcher (cookie-based, name_en/name_de + href_en/href_de)
 * that's untouched here.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'emifree_footer_links' ) ) :
	function emifree_footer_links() {
		// Path-based detection (same reasoning as inc/nav.php — the
		// request URI is the source of truth for which page was rendered,
		// not the cookie which can be stale).
		$emifree_uri = isset( $_SERVER['REQUEST_URI'] ) ? wp_unslash( $_SERVER['REQUEST_URI'] ) : '';
		$emifree_is_de = ( 0 === strpos( $emifree_uri, '/de' ) );

		if ( $emifree_is_de ) {
			return array(
				'Unternehmen' => array(
					// home_url() preserves the WP install subpath on subpath
					// installs (e.g. /wordpress/de/...). A bare '/de/...'
					// href would drop the subpath on click and 404.
					array( 'name' => 'Blog',    'href' => home_url( '/de/blog/' ) ),
					array( 'name' => 'Kontakt', 'href' => home_url( '/de/#contact' ) ),
				),
				'Ressourcen' => array(
					array( 'name' => 'Fallstudien', 'href' => home_url( '/de/#knowledge' ) ),
				),
				'Legal'      => array(
					array( 'name_en' => 'Imprint',              'name_de' => 'Impressum',            'href_en' => home_url( '/impressum/' ),     'href_de' => home_url( '/de/impressum/' ) ),
					array( 'name_en' => 'Privacy Policy',       'name_de' => 'Datenschutz',          'href_en' => home_url( '/privacy/' ),       'href_de' => home_url( '/de/datenschutz/' ) ),
					array( 'name_en' => 'General Terms (GTC)',  'name_de' => 'AGB',                  'href_en' => home_url( '/terms/' ),         'href_de' => home_url( '/de/agb/' ) ),
				),
			);
		}

		return array(
			'Company'   => array(
				// See note above — home_url() preserves the WP install
				// subpath on subpath installs.
				array( 'name' => 'Blog',    'href' => home_url( '/blog/' ) ),
				array( 'name' => 'Contact', 'href' => home_url( '/#contact' ) ),
			),
			'Resources' => array(
				array( 'name' => 'Case Studies', 'href' => home_url( '/#knowledge' ) ),
			),
			    'Legal'     => array(
				    array( 'name_en' => 'Imprint',              'name_de' => 'Impressum',            'href_en' => home_url( '/impressum/' ),     'href_de' => home_url( '/de/impressum/' ) ),
				    array( 'name_en' => 'Privacy Policy',       'name_de' => 'Datenschutz',          'href_en' => home_url( '/privacy/' ),       'href_de' => home_url( '/de/datenschutz/' ) ),
				    array( 'name_en' => 'General Terms (GTC)',  'name_de' => 'AGB',                  'href_en' => home_url( '/terms/' ),         'href_de' => home_url( '/de/agb/' ) ),
			    ),
		);
	}
endif;

if ( ! function_exists( 'emifree_social_links' ) ) :
	function emifree_social_links() {
		return array(
			array(
				'name' => 'LinkedIn',
				'href' => 'https://www.linkedin.com/company/emifree-gmbh/',
				'svg'  => 'M20.45 20.45h-3.55v-5.57c0-1.33-.02-3.04-1.85-3.04-1.85 0-2.14 1.45-2.14 2.94v5.67H9.36V9h3.41v1.56h.05c.48-.9 1.64-1.85 3.38-1.85 3.61 0 4.28 2.38 4.28 5.47v6.27zM5.34 7.43a2.06 2.06 0 110-4.12 2.06 2.06 0 010 4.12zM7.12 20.45H3.56V9h3.56v11.45z',
			),
			array(
				'name' => 'Email',
				'href' => 'mailto:info@emifree.com',
				'svg'  => 'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z',
			),
		);
	}
endif;