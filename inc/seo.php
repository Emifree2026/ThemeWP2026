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

/**
 * Per-post SEO for /blog/{slug}/ articles.
 *
 * Emits everything emifree_seo_page() emits PLUS article-specific tags
 * (og:type=article, article:published_time, article:author) and the
 * per-post JSON-LD BlogPosting schema. The single article schema is
 * what AI Overviews / Perplexity / Google cite for content-quality
 * scoring of long-form content (Google weights author + publisher +
 * datePublished heavily).
 *
 * Implementation is a parallel wp_head closure instead of an extension
 * to emifree_seo_page() because the per-post meta set is qualitatively
 * different from generic pages (article:* vs og:url, plus a much
 * richer BlogPosting schema with author/worksFor/publisher/
 * mainEntityOfPage).
 *
 * @param array      $emifree_post     Single post array (slug, title, excerpt, date, author, ...).
 * @param array|null $emifree_next_post Optional "Read next" post (currently unused but reserved for related-posts schema).
 */
function emifree_seo_blog_post( $emifree_post, $emifree_next_post = null ) {
	if ( empty( $emifree_post ) || empty( $emifree_post['slug'] ) ) {
		return;
	}
	$emifree_slug      = $emifree_post['slug'];
	$emifree_title    = $emifree_post['title'];
	$emifree_excerpt  = $emifree_post['excerpt'];
	$emifree_date     = $emifree_post['date'];
	$emifree_author   = $emifree_post['author'];
	$emifree_og_title = $emifree_title . ' | Emifree Engineering Blog';
	$emifree_url      = EMIFREE_SITE_URL . '/blog/' . $emifree_slug;

	add_action(
		'wp_head',
		static function () use (
			$emifree_slug, $emifree_title, $emifree_excerpt, $emifree_date,
			$emifree_author, $emifree_og_title, $emifree_url
		) {
			echo '<title>' . esc_html( $emifree_og_title ) . '</title>' . "\n";
			echo '<meta name="description" content="' . esc_attr( $emifree_excerpt ) . '">' . "\n";

			// Open Graph (article-type for blog posts).
			echo '<meta property="og:title" content="' . esc_attr( $emifree_og_title ) . '">' . "\n";
			echo '<meta property="og:description" content="' . esc_attr( $emifree_excerpt ) . '">' . "\n";
			echo '<meta property="og:type" content="article">' . "\n";
			echo '<meta property="og:url" content="' . esc_attr( $emifree_url ) . '">' . "\n";
			echo '<meta property="article:published_time" content="' . esc_attr( $emifree_date ) . '">' . "\n";
			echo '<meta property="article:author" content="' . esc_attr( $emifree_author ) . '">' . "\n";

			// Twitter.
			echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
			echo '<meta name="twitter:title" content="' . esc_attr( $emifree_og_title ) . '">' . "\n";
			echo '<meta name="twitter:description" content="' . esc_attr( $emifree_excerpt ) . '">' . "\n";

			// Canonical.
			echo '<link rel="canonical" href="' . esc_attr( $emifree_url ) . '">' . "\n";

			// Per-post BlogPosting JSON-LD. Mirrors React's useEffect.
			$emifree_schema = array(
				'@context'         => 'https://schema.org',
				'@type'            => 'BlogPosting',
				'headline'         => $emifree_title,
				'description'      => $emifree_excerpt,
				'datePublished'    => $emifree_date,
				'dateModified'     => $emifree_date,
				'author'           => array(
					'@type'    => 'Person',
					'name'     => $emifree_author,
					'worksFor' => array(
						'@type' => 'Organization',
						'name'  => 'Emifree GmbH',
					),
				),
				'publisher'        => array(
					'@type' => 'Organization',
					'name'  => 'Emifree GmbH',
					'url'   => EMIFREE_SITE_URL,
				),
				'mainEntityOfPage' => array(
					'@type' => 'WebPage',
					'@id'   => $emifree_url,
				),
				'url'              => $emifree_url,
				'keywords'         => isset( $emifree_post['category'] ) ? $emifree_post['category'] : 'Technical Guide',
			);
			echo '<script id="emifree-blogpost-schema" type="application/ld+json">' . "\n";
			echo wp_json_encode( $emifree_schema, JSON_UNESCAPED_SLASHES ) . "\n";
			echo '</script>' . "\n";
		},
		1
	);
}