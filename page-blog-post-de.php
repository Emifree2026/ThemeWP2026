<?php
/**
 * Page template (German): /de/blog/{slug}/.
 *
 * Hard-coded translation of page-blog-post.php.
 *
 * - Looks up the post by slug (the value of the emifree_blog_slug
 *   query var, populated by the ^de/blog/([^/]+)/?$ rewrite rule).
 * - Prefers a blog_post CPT entry when one exists for that slug;
 *   falls back to the legacy $emifree_de_posts PHP-array.
 * - 404s if neither resolves.
 * - Registers per-post SEO + JSON-LD BlogPosting schema via the
 *   shared emifree_register_blog_post_schema() helper in inc/seo.php
 *   (adds og:image + inLanguage + hreflang when CPT-driven).
 * - Computes the "Read next" suggestion (any post that isn't the
 *   current one, using the German posts feed).
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

require_once get_template_directory() . '/inc/knowledge.php';
require_once get_template_directory() . '/inc/seo.php';

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
$emifree_is_cpt         = false;
$emifree_current_post   = null;

// CPT-first lookup. When a published blog_post matches the slug with
// language=de (or no language set), prefer it over the legacy
// $emifree_de_posts PHP-array.
if ( $emifree_requested_slug ) {
	$emifree_cpt_post = emifree_query_cpt_blog_post_by_slug( $emifree_requested_slug );
	if ( $emifree_cpt_post ) {
		$emifree_cpt_lang = (string) get_post_meta( $emifree_cpt_post->ID, 'emifree_language', true );
		if ( '' === $emifree_cpt_lang || 'de' === $emifree_cpt_lang ) {
			$emifree_current_post = emifree_cpt_to_array_shape( $emifree_cpt_post );
			$emifree_is_cpt       = true;
		}
	}
}

// Fall back to legacy DE PHP-array.
if ( ! $emifree_current_post && $emifree_requested_slug ) {
	$emifree_current_post = emifree_get_post_by_slug_de( $emifree_requested_slug );
}

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
	echo '<h1>Artikel nicht gefunden</h1><p><a href="' . esc_url( home_url( '/de/blog/' ) ) . '">Zurück zu allen Artikeln</a></p>';
	exit;
}

// Build next-post (any post that isn't the current one). On CPT pages
// the suggestion comes from the merged DE feed; on legacy pages it
// comes from the inline $emifree_de_posts sorted array.
$emifree_next_post = null;
if ( $emifree_is_cpt ) {
	foreach ( emifree_get_all_blog_posts_merged( 'de', $emifree_de_posts ) as $emifree_candidate_slug => $emifree_candidate ) {
		if ( $emifree_candidate_slug !== $emifree_current_post['slug'] ) {
			$emifree_next_post = emifree_normalize_post_for_card( $emifree_candidate );
			break;
		}
	}
} else {
	foreach ( emifree_get_all_posts_sorted_de() as $emifree_candidate_slug => $emifree_candidate ) {
		if ( $emifree_candidate_slug !== $emifree_current_post['slug'] ) {
			$emifree_next_post = $emifree_candidate;
			break;
		}
	}
}

/**
 * Per-post SEO + JSON-LD BlogPosting schema (German edition).
 *
 * CPT entries use emifree_seo_blog_post_from_cpt() (gains og:image +
 * hreflang + article:modified_time via the shared helper). Legacy DE
 * posts use emifree_register_blog_post_schema() directly so the
 * inLanguage: 'de-DE' value is preserved from the old inline closure.
 */
if ( $emifree_is_cpt ) {
	emifree_seo_blog_post_from_cpt( (int) $emifree_current_post['id'] );
} else {
	$emifree_url         = home_url( '/de/blog/' . $emifree_current_post['slug'] );
	$emifree_image_url   = '';
	if ( ! empty( $emifree_current_post['hero_image'] ) ) {
		$emifree_image_url = get_template_directory_uri() . '/assets/images/blog/' . $emifree_current_post['hero_image'];
	}
	emifree_register_blog_post_schema(
		array(
			'title'         => $emifree_current_post['title'],
			'excerpt'       => $emifree_current_post['excerpt'],
			'date'          => $emifree_current_post['date'],
			'modified'      => $emifree_current_post['date'],
			'author'        => $emifree_current_post['author'],
			'url'           => $emifree_url,
			'category'      => isset( $emifree_current_post['category'] ) ? $emifree_current_post['category'] : 'Technischer Leitfaden',
			'image_url'     => $emifree_image_url,
			'lang'          => 'de-DE',
			'schema_id'     => 'emifree-blogpost-schema-de',
			// Legacy DE posts have no emifree_translation_of pointer
			// — skip hreflang entirely so the legacy rendering stays
			// byte-equivalent to today's output.
			'hreflang_self' => null,
			'hreflang_alt'  => null,
		)
	);
}

get_header();

require_once get_template_directory() . '/template-parts/page-blog-post-de.php';

get_footer();