<?php
/**
 * Page template (German): /de/blog/{slug}/.
 *
 * Hard-coded translation of page-blog-post.php.
 *
 * - Looks up the post by slug (the value of the emifree_blog_slug
 *   query var, populated by the ^de/blog/([^/]+)/?$ rewrite rule).
 * - 404s if the slug doesn't match a known post.
 * - Registers per-post SEO + JSON-LD BlogPosting schema via the
 *   helpers in inc/seo.php (those are language-agnostic; we just
 *   pass German metadata).
 * - Computes the "Read next" suggestion (any post that isn't the
 *   current one, using the German posts array).
 * - Renders template-parts/page-blog-post-de.php.
 *
 * German posts metadata + body data live in:
 *   data/posts/{slug}-de.php   (German body HTML)
 * and in the $emifree_de_posts array declared below (German
 * metadata: title, excerpt, author, date, category, etc.).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Local German posts metadata. Mirrors the shape of
// emifree_blog_posts() in inc/knowledge.php so the template can
// read it without a separate data loader.
$emifree_de_posts = array(
	'the-strategic-edge-of-clean-air' => array(
		'id'            => '1',
		'slug'          => 'the-strategic-edge-of-clean-air',
		'title'         => 'Der strategische Vorteil sauberer Luft: Warum Hochleistungs-Ölnebelfiltration für die moderne Zerspanung unverzichtbar ist',
		'excerpt'       => 'Industrielle Ölnebelfiltration ist kein Zubehör, sondern eine strategische Investition in Arbeitssicherheit, Anlagenlebensdauer und Betriebseffizienz in hochpräzisen Fertigungsumgebungen.',
		'category'      => 'Technischer Leitfaden',
		'date'          => '2026-06-29',
		'formatted_date'=> '29. Juni 2026',
		'read_time'     => '5 Min. Lesezeit',
		'author'        => 'Victoria Pedroza',
		'author_role'   => 'Produktmanagerin, Emifree GmbH',
		'hero_image'    => 'Factory_floor_with_CNC_.webp',
	),
	'precision-in-every-breath' => array(
		'id'            => '2',
		'slug'          => 'precision-in-every-breath',
		'title'         => 'Präzision in jedem Atemzug: Ein technischer Leitfaden zur industriellen Ölnebelfiltration',
		'excerpt'       => 'Ein technischer Vergleich mechanischer und elektrostatischer Ölnebelfiltrationstechnologien – und wie die Absaugung direkt an der Quelle Ihre Mitarbeiter, Ihre Maschinen und Ihr Ergebnis schützt.',
		'category'      => 'Technischer Leitfaden',
		'date'          => '2026-06-29',
		'formatted_date'=> '29. Juni 2026',
		'read_time'     => '7 Min. Lesezeit',
		'author'        => 'Victoria Pedroza',
		'author_role'   => 'Produktmanagerin, Emifree GmbH',
		'hero_image'    => 'CNC_2.jpg',
	),
);

/**
 * Local DE helpers — kept inside this file (rather than added to a
 * shared inc/) because we don't want to grow the global function
 * namespace during the i18n refactor. If a future piece needs these
 * from another template, hoist them into inc/.
 */
function emifree_get_post_by_slug_de( $emifree_slug ) {
	global $emifree_de_posts;
	return isset( $emifree_de_posts[ $emifree_slug ] ) ? $emifree_de_posts[ $emifree_slug ] : null;
}

function emifree_get_all_posts_sorted_de() {
	global $emifree_de_posts;
	$emifree_posts = $emifree_de_posts;
	uasort(
		$emifree_posts,
		static function ( $emifree_a, $emifree_b ) {
			return strcmp( $emifree_b['date'], $emifree_a['date'] );
		}
	);
	return $emifree_posts;
}

function emifree_get_post_body_html_de( $emifree_slug ) {
	$emifree_path = get_template_directory() . '/data/posts/' . $emifree_slug . '-de.php';
	if ( ! file_exists( $emifree_path ) ) {
		return '';
	}
	$emifree_body = include $emifree_path;
	if ( ! is_array( $emifree_body ) || empty( $emifree_body['body_html'] ) ) {
		return '';
	}
	return $emifree_body['body_html'];
}

$emifree_requested_slug = get_query_var( 'emifree_blog_slug' );
$emifree_current_post  = $emifree_requested_slug ? emifree_get_post_by_slug_de( $emifree_requested_slug ) : null;

// If the slug isn't a known post, hand off to WP's 404 flow.
if ( ! $emifree_current_post ) {
	$emifree_404 = locate_template( '404.php' );
	if ( $emifree_404 ) {
		status_header( 404 );
		include $emifree_404;
		exit;
	}
	status_header( 404 );
	nocache_headers();
	echo '<h1>Artikel nicht gefunden</h1><p><a href="/de/blog">Zurück zu allen Artikeln</a></p>';
	exit;
}

// Build next-post (any post that isn't the current one).
$emifree_next_post = null;
foreach ( emifree_get_all_posts_sorted_de() as $emifree_candidate_slug => $emifree_candidate ) {
	if ( $emifree_candidate_slug !== $emifree_current_post['slug'] ) {
		$emifree_next_post = $emifree_candidate;
		break;
	}
}

/**
 * Per-post SEO + JSON-LD BlogPosting schema (German edition).
 *
 * Registers the same wp_head closure shape as emifree_seo_blog_post()
 * in inc/seo.php — same article schema fields, with German values
 * passed in. Declared locally rather than extending inc/seo.php so
 * the i18n refactor stays additive.
 */
$emifree_og_title = $emifree_current_post['title'] . ' | Emifree Engineering-Blog';
$emifree_url      = EMIFREE_SITE_URL . '/de/blog/' . $emifree_current_post['slug'];

add_action(
	'wp_head',
	static function () use (
		$emifree_current_post, $emifree_next_post,
		$emifree_og_title, $emifree_url
	) {
		$emifree_title   = $emifree_current_post['title'];
		$emifree_excerpt = $emifree_current_post['excerpt'];
		$emifree_date    = $emifree_current_post['date'];
		$emifree_author  = $emifree_current_post['author'];

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

		// JSON-LD BlogPosting schema — mirrors the React's useEffect in
		// src/pages/BlogPost.jsx, same shape as emifree_seo_blog_post().
		$emifree_schema = array(
			'@context'         => 'https://schema.org',
			'@type'            => 'BlogPosting',
			'headline'         => $emifree_title,
			'description'      => $emifree_excerpt,
			'datePublished'    => $emifree_date,
			'dateModified'     => $emifree_date,
			'inLanguage'       => 'de-DE',
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
			'keywords'         => isset( $emifree_current_post['category'] ) ? $emifree_current_post['category'] : 'Technischer Leitfaden',
		);
		echo '<script id="emifree-blogpost-schema-de" type="application/ld+json">' . "\n";
		echo wp_json_encode( $emifree_schema, JSON_UNESCAPED_SLASHES ) . "\n";
		echo '</script>' . "\n";
	},
	1
);

get_header();

require_once get_template_directory() . '/template-parts/page-blog-post-de.php';

get_footer();