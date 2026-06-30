<?php
/**
 * Footer link data + helpers — single source of truth for the Footer.
 *
 * Mirrors src/components/Footer.jsx post-cleanup state. Three columns:
 * Company (Blog, Contact), Resources (Case Studies), Legal (Impressum,
 * Privacy Policy, General Terms). Each link resolves to either an
 * in-page anchor (#contact, #knowledge) or a real route (/blog,
 * /impressum, /privacy, /terms). Routes not yet shipped will 404
 * until those pieces land — that is expected and temporary.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'emifree_footer_links' ) ) :
	function emifree_footer_links() {
		return array(
			'Company'   => array(
				array( 'name' => 'Blog',    'href' => '/blog' ),
				array( 'name' => 'Contact', 'href' => '#contact' ),
			),
			'Resources' => array(
				array( 'name' => 'Case Studies', 'href' => '#knowledge' ),
			),
			'Legal'     => array(
				array( 'name' => 'Impressum',            'href' => '/impressum' ),
				array( 'name' => 'Privacy Policy',       'href' => '/privacy' ),
				array( 'name' => 'General Terms (GTC)',  'href' => '/terms' ),
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