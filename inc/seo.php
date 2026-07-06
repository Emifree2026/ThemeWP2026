<?php
/**
 * SEO helpers — per-page meta tags + JSON-LD injection.
 *
 * Mirrors the React pages (src/pages/Impressum.jsx, Privacy.jsx,
 * Terms.jsx, BlogPost.jsx) which inject meta + canonical + JSON-LD
 * via useEffect. The WordPress equivalent registers wp_head
 * callbacks at template-top so the meta is server-rendered into
 * the HTML head — better SEO than client-side React injection
 * because crawlers see the meta on the first byte of HTML.
 *
 * Usage from a page template (top of file, before any output):
 *
 *     emifree_seo_page( 'Impressum · Emifree GmbH',
 *         'Legal notice for Emifree GmbH, Berlin...',
 *         'https://emifree.com/impressum',
 *         [ 'schema_id' => 'emifree-impressum-schema',
 *           'schema'    => [ '@type' => 'WebPage', ... ] ] );
 *
 * That's it — title, description, OG, Twitter, canonical, and
 * JSON-LD all wired in one call. Each page calls it once.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! defined( 'EMIFREE_SITE_URL' ) ) {
	define( 'EMIFREE_SITE_URL', 'https://emifree.com' );
}

/**
 * One-call per-page setup. Registers all per-page meta tags +
 * canonical + optional JSON-LD schema against wp_head.
 *
 * The 4th argument is optional. Pass an array of schemas as
 *   array( 'id' => 'emifree-impressum-schema', 'data' => array( ... ) )
 * to inject one JSON-LD block. Multiple schemas can be passed.
 *
 * Use global $post if available and the call doesn't pass a
 * $url — useful for single-post templates. Otherwise the caller
 * must pass the URL explicitly so the canonical is unambiguous.
 */
function emifree_seo_page( $title, $description, $url, $schemas = array() ) {
	add_action(
		'wp_head',
		static function () use ( $title, $description, $url, $schemas ) {
			// <title>
			echo '<title>' . esc_html( $title ) . '</title>' . "\n";

			// Description
			echo '<meta name="description" content="' . esc_attr( $description ) . '">' . "\n";

			// Open Graph
			echo '<meta property="og:title" content="' . esc_attr( $title ) . '">' . "\n";
			echo '<meta property="og:description" content="' . esc_attr( $description ) . '">' . "\n";
			echo '<meta property="og:type" content="website">' . "\n";
			echo '<meta property="og:url" content="' . esc_attr( $url ) . '">' . "\n";

			// Twitter
			echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
			echo '<meta name="twitter:title" content="' . esc_attr( $title ) . '">' . "\n";
			echo '<meta name="twitter:description" content="' . esc_attr( $description ) . '">' . "\n";

			// Canonical
			echo '<link rel="canonical" href="' . esc_attr( $url ) . '">' . "\n";

			// JSON-LD schemas
			foreach ( (array) $schemas as $emifree_schema ) {
				if ( empty( $emifree_schema['id'] ) || empty( $emifree_schema['data'] ) ) {
					continue;
				}
				echo '<script id="' . esc_attr( $emifree_schema['id'] ) . '" type="application/ld+json">' . "\n";
				echo wp_json_encode( $emifree_schema['data'], JSON_UNESCAPED_SLASHES ) . "\n";
				echo '</script>' . "\n";
			}
		},
		1
	);
}

/**
 * Helper: caller-friendly wrapper for the single-schema case.
 * Most pages have exactly one WebPage/BlogPosting schema, so:
 *
 *     emifree_seo_page(
 *         'Title',
 *         'Description',
 *         'https://...',
 *         'emifree-impressum-schema',
 *         [ '@type' => 'WebPage', ... ]
 *     );
 */
function emifree_seo_page_with_schema( $title, $description, $url, $schema_id, $schema_data ) {
	emifree_seo_page( $title, $description, $url, array(
		array( 'id' => $schema_id, 'data' => $schema_data ),
	) );
}