<?php
/**
 * Page template: /blog/{slug}/
 * Renders a single blog post with per-post SEO + JSON-LD schema.
 *
 * Mirrors src/pages/BlogPost.jsx from the React app. The shim:
 *  - Looks up the post by slug (the value of the emifree_blog_slug
 *    query var, populated by the ^blog/([^/]+)/?$ rewrite rule).
 *  - Prefers a blog_post CPT entry when one exists for that slug;
 *    falls back to the legacy PHP-array (emifree_blog_posts()).
 *  - 404s if neither resolves (with WP's normal 404 template lookup
 *    as a courtesy).
 *  - Registers per-post SEO + JSON-LD BlogPosting schema — CPT
 *    entries use emifree_seo_blog_post_from_cpt() (adds og:image +
 *    inLanguage + hreflang); legacy posts use emifree_seo_blog_post().
 *  - Computes the "Read next" suggestion (any post that isn't the
 *    current one — legacy posts only; CPT-driven pages show the same
 *    fallback until emifree_get_all_blog_posts_merged() is wired).
 *  - Renders the template part inside get_header() / get_footer().
 */

require_once get_template_directory() . '/inc/i18n.php';
emifree_require_section_data( 'knowledge' );
emifree_require_section_data( 'blog-cards' );

$emifree_requested_slug = get_query_var( 'emifree_blog_slug' );
$emifree_is_cpt         = false;
$emifree_current_post   = null;

// CPT-first lookup. If a published blog_post matches the slug, prefer
// it over the legacy PHP-array (because the CPT entry represents an
// editorial update — the array is frozen at deploy time). Language
// meta is checked so an EN request doesn't accidentally pick up a
// DE sibling when they share a slug.
if ( $emifree_requested_slug ) {
	$emifree_cpt_post = emifree_query_cpt_blog_post_by_slug( $emifree_requested_slug );
	if ( $emifree_cpt_post ) {
		$emifree_cpt_lang = (string) get_post_meta( $emifree_cpt_post->ID, 'emifree_language', true );
		if ( '' === $emifree_cpt_lang || 'en' === $emifree_cpt_lang ) {
			$emifree_current_post = emifree_cpt_to_array_shape( $emifree_cpt_post );
			$emifree_is_cpt       = true;
		}
	}
}

// Fall back to legacy PHP-array.
if ( ! $emifree_current_post && $emifree_requested_slug ) {
	$emifree_current_post = emifree_get_post_by_slug( $emifree_requested_slug );
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
	echo '<h1>Article not found</h1><p><a href="' . esc_url( home_url( '/blog/' ) ) . '">Back to all articles</a></p>';
	exit;
}

// Build next-post (any post that isn't the current one). On CPT pages
// the suggestion comes from the merged feed; on legacy pages it comes
// from emifree_get_all_posts_sorted().
$emifree_next_post = null;
if ( $emifree_is_cpt ) {
	foreach ( emifree_get_all_blog_posts_merged( 'en' ) as $emifree_candidate_slug => $emifree_candidate ) {
		if ( $emifree_candidate_slug !== $emifree_current_post['slug'] ) {
			$emifree_next_post = emifree_normalize_post_for_card( $emifree_candidate );
			break;
		}
	}
} else {
	foreach ( emifree_get_all_posts_sorted() as $emifree_candidate_slug => $emifree_candidate ) {
		if ( $emifree_candidate_slug !== $emifree_current_post['slug'] ) {
			$emifree_next_post = $emifree_candidate;
			break;
		}
	}
}

// Per-post SEO meta + JSON-LD BlogPosting schema.
if ( $emifree_is_cpt ) {
	emifree_seo_blog_post_from_cpt( (int) $emifree_current_post['id'] );
} else {
	emifree_seo_blog_post( $emifree_current_post, $emifree_next_post );
}

get_header();

require_once get_template_directory() . '/template-parts/page-blog-post.php';

get_footer();