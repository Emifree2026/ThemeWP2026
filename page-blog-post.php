<?php
/**
 * Page template: /blog/{slug}/
 * Renders a single blog post with per-post SEO + JSON-LD schema.
 *
 * Mirrors src/pages/BlogPost.jsx from the React app. The shim:
 *  - Looks up the post by slug (the value of the emifree_blog_slug
 *    query var, populated by the ^blog/([^/]+)/?$ rewrite rule).
 *  - 404s if the slug doesn't match a known post (with WP's normal
 *    404 template lookup as a courtesy).
 *  - Registers per-post SEO + JSON-LD BlogPosting schema via
 *    emifree_seo_blog_post().
 *  - Computes the "Read next" suggestion (any post that isn't the
 *    current one).
 *  - Renders the template part inside get_header() / get_footer().
 */

require_once get_template_directory() . '/inc/i18n.php';
emifree_require_section_data( 'knowledge' );
emifree_require_section_data( 'blog-cards' );

$emifree_requested_slug = get_query_var( 'emifree_blog_slug' );
$emifree_current_post  = $emifree_requested_slug ? emifree_get_post_by_slug( $emifree_requested_slug ) : null;

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
	echo '<h1>Article not found</h1><p><a href="/blog">Back to all articles</a></p>';
	exit;
}

// Build next-post (any post that isn't the current one).
$emifree_next_post = null;
foreach ( emifree_get_all_posts_sorted() as $emifree_candidate_slug => $emifree_candidate ) {
	if ( $emifree_candidate_slug !== $emifree_current_post['slug'] ) {
		$emifree_next_post = $emifree_candidate;
		break;
	}
}

// Per-post SEO meta + JSON-LD BlogPosting schema.
emifree_seo_blog_post( $emifree_current_post, $emifree_next_post );

get_header();

require_once get_template_directory() . '/template-parts/page-blog-post.php';

get_footer();