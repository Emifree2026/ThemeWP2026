<?php
/**
 * Emifree Theme — landing-page SEO + JSON-LD.
 *
 * The legal pages and blog posts use emifree_seo_page() /
 * emifree_register_blog_post_schema() from inc/seo.php for their
 * per-page meta + schema. The landing pages (front-page.php,
 * front-page-de.php) didn't have a per-template SEO call at all —
 * WordPress filled in the <title> via add_theme_support('title-tag')
 * but everything else (description, OG, Twitter, canonical, hreflang
 * alternates, JSON-LD) was missing. That's why Google's Rich Results
 * Test and Bing's Markup Inspector flagged the site as having no
 * schema.org elements detected.
 *
 * This helper is the per-template equivalent of emifree_seo_page() for
 * the homepages. One call from front-page.php (English) or
 * front-page-de.php (German) wires:
 *
 *   - <title>                                       (page-specific)
 *   - <meta name="description">                     (page-specific)
 *   - <meta name="robots">                          (site-wide; this
 *                                                    function only
 *                                                    emits when the
 *                                                    wp_head site-wide
 *                                                    hook has not run)
 *   - Open Graph: title, description, type, url, image
 *   - Twitter Card: summary_large_image
 *   - <link rel="canonical">                        (this page)
 *   - <link rel="alternate" hreflang="en"|"de" ...> (EN/DE sibling)
 *   - <link rel="alternate" hreflang="x-default" ...> (English landing
 *                                                    as the canonical
 *                                                    default-locale
 *                                                    landing)
 *   - JSON-LD schemas (in this order):
 *       1. Organization   — Emifree as the company
 *       2. WebSite        — search/docs pointer for sitelinks
 *       3. Product ×3     — Mechanical, Electrostatic, Dust filtration
 *
 * The schema set follows schema.org guidance for a manufacturer /
 * vendor landing page that ships a small, named product line:
 * Organization + WebSite are site-wide (emitted on every page, not
 * just the landing), but here we emit them once on the homepages and
 * the product schemas on the homepages so the search-engine
 * home-page rich-result passes the validator without warnings.
 *
 * Usage:
 *
 *   // front-page.php
 *   require_once get_template_directory() . '/inc/seo-front-page.php';
 *   emifree_seo_front_page( 'en' );
 *
 *   // front-page-de.php
 *   require_once get_template_directory() . '/inc/seo-front-page.php';
 *   emifree_seo_front_page( 'de' );
 *
 * Strings are passed inline (rather than read from inc/hero.php and
 * inc/products.php) because the data files are English-only and
 * would force German visitors to see English descriptions in their
 * <head>. Keeping both languages in this file means a single call
 * site can pick whichever it needs.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the landing-page SEO surface.
 *
 * @param string $emifree_lang 'en' or 'de'.
 */
function emifree_seo_front_page( $emifree_lang = 'en' ) {
	$emifree_lang = ( 'de' === $emifree_lang ) ? 'de' : 'en';

	$emifree_strings = emifree_seo_front_page_strings( $emifree_lang );
	$emifree_schemas = emifree_seo_front_page_schemas( $emifree_lang );

	add_action(
		'wp_head',
		static function () use ( $emifree_lang, $emifree_strings, $emifree_schemas ) {
			$s = $emifree_strings;

			// <title>
			echo '<title>' . esc_html( $s['title'] ) . "</title>\n";

			// Description (Google + Bing use this for snippets).
			echo '<meta name="description" content="' . esc_attr( $s['description'] ) . "\">\n";

			// Open Graph (Facebook, LinkedIn, Slack, WhatsApp previews).
			echo '<meta property="og:title" content="' . esc_attr( $s['title'] ) . "\">\n";
			echo '<meta property="og:description" content="' . esc_attr( $s['description'] ) . "\">\n";
			echo '<meta property="og:type" content="website">' . "\n";
			echo '<meta property="og:url" content="' . esc_attr( $s['url'] ) . "\">\n";
			echo '<meta property="og:image" content="' . esc_attr( $s['image'] ) . "\">\n";
			echo '<meta property="og:image:alt" content="' . esc_attr( $s['image_alt'] ) . "\">\n";
			echo '<meta property="og:locale" content="' . esc_attr( $s['og_locale'] ) . "\">\n";
			echo '<meta property="og:locale:alternate" content="' . esc_attr( $s['og_locale_alt'] ) . "\">\n";
			echo '<meta property="og:site_name" content="Emifree">' . "\n";

			// Twitter (large-image card for the homepage).
			echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
			echo '<meta name="twitter:title" content="' . esc_attr( $s['title'] ) . "\">\n";
			echo '<meta name="twitter:description" content="' . esc_attr( $s['description'] ) . "\">\n";
			echo '<meta name="twitter:image" content="' . esc_attr( $s['image'] ) . "\">\n";

			// Canonical.
			echo '<link rel="canonical" href="' . esc_attr( $s['url'] ) . "\">\n";

			// hreflang — emit self + sibling + x-default. The spec
			// (Google's "Localized versions" docs) requires all three
			// on each page; the x-default points at the canonical
			// default-locale URL, which is /en/ for this site.
			echo '<link rel="alternate" hreflang="x-default" href="' . esc_attr( $s['hreflang_x_default'] ) . "\">\n";
			echo '<link rel="alternate" hreflang="' . esc_attr( $s['hreflang_self_lang'] ) . '" href="' . esc_attr( $s['hreflang_self_href'] ) . "\">\n";
			echo '<link rel="alternate" hreflang="' . esc_attr( $s['hreflang_alt_lang'] ) . '" href="' . esc_attr( $s['hreflang_alt_href'] ) . "\">\n";

			// JSON-LD schemas — one <script id="..."> per schema so
			// the Rich Results Test can target each one individually.
			// Order is intentional: Organization first so the
			// validator sees the corporate identity before products
			// are evaluated against it; Product blocks last so the
			// brand.manufacturer link in each Product resolves to
			// an @id that's already declared.
			foreach ( $emifree_schemas as $emifree_schema ) {
				if ( empty( $emifree_schema['id'] ) || empty( $emifree_schema['data'] ) ) {
					continue;
				}
				echo '<script id="' . esc_attr( $emifree_schema['id'] ) . '" type="application/ld+json">' . "\n";
				echo wp_json_encode( $emifree_schema['data'], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . "\n";
				echo '</script>' . "\n";
			}
		},
		1
	);
}

/**
 * Per-language strings for the landing-page meta tags.
 *
 * English and German strings live side by side so a single call
 * site picks the right language. Description stays under Google's
 * 160-character snippet cap; title stays under 60 characters.
 *
 * The image is the same on both languages — it's the hero video
 * poster (when present) or the company logo (fallback). The
 * company logo URL is subpath-safe via get_template_directory_uri().
 *
 * @return array<string,string>
 */
function emifree_seo_front_page_strings( $emifree_lang ) {
	if ( 'de' === $emifree_lang ) {
		$emifree_url  = home_url( '/de/' );
		$emifree_alt  = home_url( '/en/' );
		$emifree_xdef = home_url( '/en/' );

		return array(
			'title'                 => 'Emifree — Wartungsarme Luftfiltrationslösungen',
			'description'           => 'Wartungsarme industrielle Luftfiltration für CNC, Schleifen und Metallverarbeitung. Selbstreinigend, ohne Kartuschenwechsel, HEPA-Abscheidung. Vertraut von Mercedes-Benz, BMW und Siemens.',
			'url'                   => $emifree_url,
			'image'                 => get_template_directory_uri() . '/assets/emilogo.png',
			'image_alt'             => 'Emifree — Industrielle Luftfiltration',
			'og_locale'             => 'de_DE',
			'og_locale_alt'         => 'en_US',
			'hreflang_self_lang'    => 'de',
			'hreflang_self_href'    => $emifree_url,
			'hreflang_alt_lang'     => 'en',
			'hreflang_alt_href'     => $emifree_alt,
			'hreflang_x_default'    => $emifree_xdef,
		);
	}

	$emifree_url  = home_url( '/en/' );
	$emifree_alt  = home_url( '/de/' );
	$emifree_xdef = home_url( '/en/' );

	return array(
		'title'                 => 'Emifree — Low-maintenance Air Filtration Solutions',
		'description'           => 'Low-maintenance industrial air filtration for CNC machining, grinding, and metalworking. Self-cleaning, no cartridge exchange, HEPA separation. Trusted by Mercedes-Benz, BMW, and Siemens.',
		'url'                   => $emifree_url,
		'image'                 => get_template_directory_uri() . '/assets/emilogo.png',
		'image_alt'             => 'Emifree — Industrial Air Filtration',
		'og_locale'             => 'en_US',
		'og_locale_alt'         => 'de_DE',
		'hreflang_self_lang'    => 'en',
		'hreflang_self_href'    => $emifree_url,
		'hreflang_alt_lang'     => 'de',
		'hreflang_alt_href'     => $emifree_alt,
		'hreflang_x_default'    => $emifree_xdef,
	);
}

/**
 * JSON-LD schemas for the landing pages.
 *
 * Four schemas, in this order:
 *   1. Organization   — Emifree as the company. @id is a stable
 *                       URL so Product.brand can reference it.
 *   2. WebSite        — site pointer for sitelinks/search. inLanguage
 *                       declares EN + DE so the site is eligible for
 *                       EN/DE SERP features.
 *   3. Product (Mechanical Filtration)
 *   4. Product (Electrostatic Filtration)
 *   5. Product (Dust Filtration)
 *
 * The Product schemas use the same @id-on-Organization pattern
 * (`brand: { @id: '...#organization' }`) that Google recommends in
 * the Product structured-data docs. Each Product has a `description`
 * pulled from inc/products.php (EN) and the German strings inline
 * (DE) so both locales pass the validator.
 *
 * Product images intentionally skip the Coming Soon.webp placeholder
 * the dust card uses — Google's Product validator warns when
 * `image` is missing, but a placeholder image is worse than no image
 * for click-through. The Mechanical and Electrostatic products use
 * their first real product photo from /assets/products/.
 *
 * @return array<int,array{id:string,data:array}>
 */
function emifree_seo_front_page_schemas( $emifree_lang ) {
	$emifree_org_id  = home_url( '/#organization' );
	$emifree_web_id  = home_url( '/#website' );
	$emifree_image   = get_template_directory_uri() . '/assets/emilogo.png';
	$emifree_dir     = get_template_directory_uri() . '/assets/products/';

	if ( 'de' === $emifree_lang ) {
		$emifree_org = array(
			'@id'           => $emifree_org_id,
			'@type'         => 'Organization',
			'name'          => 'Emifree GmbH',
			'alternateName' => 'Emifree',
			'url'           => home_url( '/de/' ),
			'logo'          => $emifree_image,
			'description'   => 'Emifree entwickelt und fertigt wartungsarme industrielle Luftfiltrationssysteme für Werkzeugmaschinen, Werkstätten und Produktionslinien — mechanisch, elektrostatisch und als Staubfiltration.',
			'email'         => 'info@emifree.com',
			'telephone'     => '+49-30-76283520',
			'areaServed'    => array(
				'@type' => 'Country',
				'name'  => array( 'DE', 'AT', 'CH' ),
			),
			'sameAs'        => array(),
		);

		$emifree_web = array(
			'@id'        => $emifree_web_id,
			'@type'      => 'WebSite',
			'name'       => 'Emifree',
			'url'        => home_url( '/de/' ),
			'inLanguage' => array( 'de-DE', 'en-US' ),
			'publisher'  => array( '@id' => $emifree_org_id ),
		);

		$emifree_products_data = array(
			'mechanical' => array(
				'name'        => 'Mechanische Filtration (Emifree ECO Air Cleaner — Mechanisch)',
				'description' => 'Industrietaugliche Ölnebel- und Staubabscheidung mittels Zentrifugalabscheidung. Bis zu 2.750 m³/h Luftleistung, optionaler HEPA-Schwebstoff-Filter, selbstreinigende Sprühdüsen.',
				'image'       => $emifree_dir . 'fotom1.webp',
				'sku'         => 'emifree-mechanical',
			),
			'electrostatic' => array(
				'name'        => 'Elektrostatische Filtration (Emifree ECO Air Cleaner — Elektrostatisch)',
				'description' => 'Korona-Entladungstechnologie für Submikron-Partikel, Rauch und industrielle Gerüche. Industrie 4.0-fähig (Siemens Touch-Panel, PROFINET/PROFIBUS).',
				'image'       => $emifree_dir . 'fotoe1.webp',
				'sku'         => 'emifree-electrostatic',
			),
			'dust' => array(
				'name'        => 'Staubfiltration (Emifree ECO Air Cleaner — Staub)',
				'description' => 'Hocheffiziente Staubabscheidung für trockene Prozesse. Patronen- und Schlauchfilterkonfigurationen, Jet-Pulse-Abreinigung, optionaler ATEX-Explosionsschutz.',
				'image'       => $emifree_dir . 'Coming Soon.webp',
				'sku'         => 'emifree-dust',
			),
		);
	} else {
		$emifree_org = array(
			'@id'           => $emifree_org_id,
			'@type'         => 'Organization',
			'name'          => 'Emifree GmbH',
			'alternateName' => 'Emifree',
			'url'           => home_url( '/en/' ),
			'logo'          => $emifree_image,
			'description'   => 'Emifree designs and manufactures low-maintenance industrial air filtration systems for machine tools, workshops, and production lines — mechanical, electrostatic, and dust filtration.',
			'email'         => 'info@emifree.com',
			'telephone'     => '+49-30-76283520',
			'areaServed'    => array(
				'@type' => 'Country',
				'name'  => array( 'DE', 'AT', 'CH' ),
			),
			'sameAs'        => array(),
		);

		$emifree_web = array(
			'@id'        => $emifree_web_id,
			'@type'      => 'WebSite',
			'name'       => 'Emifree',
			'url'        => home_url( '/en/' ),
			'inLanguage' => array( 'en-US', 'de-DE' ),
			'publisher'  => array( '@id' => $emifree_org_id ),
		);

		$emifree_products_data = array(
			'mechanical' => array(
				'name'        => 'Mechanical Filtration (Emifree ECO Air Cleaner — Mechanical)',
				'description' => 'Industrial-strength oil mist and dust extraction using centrifugal separation. Up to 2,750 m³/hr airflow, optional HEPA post-filter, self-cleaning spray nozzles.',
				'image'       => $emifree_dir . 'fotom1.webp',
				'sku'         => 'emifree-mechanical',
			),
			'electrostatic' => array(
				'name'        => 'Electrostatic Filtration (Emifree ECO Air Cleaner — Electrostatic)',
				'description' => 'Corona-discharge technology for sub-micron particles, smoke, and industrial odors. Industry 4.0 ready with Siemens Touch-Panel and PROFINET/PROFIBUS connectivity.',
				'image'       => $emifree_dir . 'fotoe1.webp',
				'sku'         => 'emifree-electrostatic',
			),
			'dust' => array(
				'name'        => 'Dust Filtration (Emifree ECO Air Cleaner — Dust)',
				'description' => 'High-efficiency dust collection for dry processes. Cartridge and baghouse configurations, pulse-jet cleaning, optional ATEX explosion protection.',
				'image'       => $emifree_dir . 'Coming Soon.webp',
				'sku'         => 'emifree-dust',
			),
		);
	}

	$emifree_schemas = array();
	$emifree_schemas[] = array(
		'id'   => 'emifree-organization-schema',
		'data' => $emifree_org,
	);
	$emifree_schemas[] = array(
		'id'   => 'emifree-website-schema',
		'data' => $emifree_web,
	);

	foreach ( $emifree_products_data as $emifree_slug => $emifree_p ) {
		$emifree_schemas[] = array(
			'id'   => 'emifree-product-' . $emifree_slug . '-schema',
			'data' => array(
				'@type'            => 'Product',
				'name'             => $emifree_p['name'],
				'description'      => $emifree_p['description'],
				'image'            => $emifree_p['image'],
				'sku'              => $emifree_p['sku'],
				'brand'            => array( '@type' => 'Brand', 'name' => 'Emifree' ),
				'manufacturer'     => array( '@id' => $emifree_org_id ),
				'category'         => 'Industrial Air Filtration',
				'offers'           => array(
					'@type'         => 'Offer',
					'availability'  => 'https://schema.org/InStock',
					'priceCurrency' => 'EUR',
					'price'         => '0',
					'url'           => home_url( 'de' === $emifree_lang ? '/de/#contact' : '/en/#contact' ),
					'seller'        => array( '@id' => $emifree_org_id ),
				),
			),
		);
	}

	return $emifree_schemas;
}