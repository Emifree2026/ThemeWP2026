<?php
/**
 * Page template: /blog/
 * Renders the blog index with per-page SEO + JSON-LD schema.
 *
 * Mirrors src/pages/Blog.jsx from the React app (which does its
 * document.title / meta / canonical / og / twitter / JSON-LD
 * updates via useEffect). The WordPress equivalent registers the
 * same meta via wp_head callbacks so the markup is server-rendered.
 *
 * The post feed is the merged result of legacy PHP-array posts and
 * any blog_post CPT entries (emifree_get_all_blog_posts_merged()).
 * CPT entries show up automatically as soon as they're published.
 */

require_once get_template_directory() . '/inc/i18n.php';
require_once get_template_directory() . '/inc/knowledge.php';

/**
 * Per-page SEO registration. Mirrors the React's useEffect in
 * Blog.jsx (title, description, OG, Twitter, canonical, JSON-LD
 * Blog schema with blogPost entries for each post).
 *
 * Wrapped in a function so the localized strings + posts lookup are
 * isolated, matching the pattern used by the legal page shims.
 */
function emifree_blog_seo() {
	$emifree_posts = function_exists( 'emifree_get_all_blog_posts_merged' )
		? emifree_get_all_blog_posts_merged( 'en' )
		: ( function_exists( 'emifree_blog_posts' ) ? emifree_blog_posts() : array() );

	$emifree_blog_post_entries = array();
	foreach ( $emifree_posts as $emifree_slug => $emifree_p ) {
		// Normalized entries expose `slug` + `title` + `formatted_date`
		// but NOT raw `date` / `author`. Resolve those for JSON-LD:
		// legacy arrays carry `date` + `author` already; CPT entries
		// need a re-lookup by slug to keep the schema accurate.
		$emifree_date   = isset( $emifree_p['date'] ) ? $emifree_p['date'] : '';
		$emifree_author = isset( $emifree_p['author'] ) ? $emifree_p['author'] : '';
		if ( '' === $emifree_date || '' === $emifree_author ) {
			$emifree_cpt_lookup = emifree_query_cpt_blog_post_by_slug( $emifree_slug );
			if ( $emifree_cpt_lookup ) {
				if ( '' === $emifree_date ) {
					$emifree_date = mysql2date( 'Y-m-d', $emifree_cpt_lookup->post_date );
				}
				if ( '' === $emifree_author ) {
					$emifree_author = get_the_author_meta( 'display_name', $emifree_cpt_lookup->post_author );
					if ( ! $emifree_author ) {
						$emifree_author = 'Emifree Team';
					}
				}
			}
		}
		if ( '' === $emifree_date || '' === $emifree_author ) {
			// Skip entries we can't enrich — keeps the JSON-LD clean
			// (better to omit than to emit a malformed BlogPosting).
			continue;
		}
		$emifree_blog_post_entries[] = array(
			'@type'         => 'BlogPosting',
			'headline'      => $emifree_p['title'],
			'url'           => home_url( '/blog/' . $emifree_slug ),
			'datePublished' => $emifree_date,
			'author'        => array(
				'@type'    => 'Person',
				'name'     => $emifree_author,
				'worksFor' => array(
					'@type' => 'Organization',
					'name'  => 'Emifree GmbH',
				),
			),
		);
	}

	emifree_seo_page_with_schema(
		'Emifree Engineering Blog — Industrial Air Filtration Insights',
		'Technical guides and field insights on industrial oil mist filtration, CNC air quality, mechanical vs electrostatic separation, and EU regulatory compliance. From the Emifree engineering team.',
		home_url( '/blog' ),
		'emifree-blog-schema',
		array(
			'@context'    => 'https://schema.org',
			'@type'       => 'Blog',
			'name'        => 'Emifree Engineering Blog',
			'description' => 'Technical guides and field insights on industrial oil mist filtration from the Emifree engineering team.',
			'url'         => home_url( '/blog' ),
			'publisher'   => array(
				'@type' => 'Organization',
				'name'  => 'Emifree GmbH',
				'url'   => home_url(),
			),
			'blogPost'    => $emifree_blog_post_entries,
		)
	);
}
emifree_blog_seo();

get_header();

require_once get_template_directory() . '/template-parts/page-blog-index.php';

get_footer();