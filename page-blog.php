<?php
/**
 * Page template: /blog/
 * Renders the blog index with per-page SEO + JSON-LD schema.
 *
 * Mirrors src/pages/Blog.jsx from the React app (which does its
 * document.title / meta / canonical / og / twitter / JSON-LD
 * updates via useEffect). The WordPress equivalent registers the
 * same meta via wp_head callbacks so the markup is server-rendered.
 */

require_once get_template_directory() . '/inc/i18n.php';

/**
 * Per-page SEO registration. Mirrors the React's useEffect in
 * Blog.jsx (title, description, OG, Twitter, canonical, JSON-LD
 * Blog schema with blogPost entries for each post).
 *
 * Wrapped in a function so the localized strings + posts lookup are
 * isolated, matching the pattern used by the legal page shims.
 */
function emifree_blog_seo() {
	$emifree_posts = function_exists( 'emifree_blog_posts' ) ? emifree_blog_posts() : array();

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
			'blogPost'    => array_map(
				static function ( $emifree_p ) {
					return array(
						'@type'         => 'BlogPosting',
						'headline'      => $emifree_p['title'],
						'url'           => home_url( '/blog/' . $emifree_p['slug'] ),
						'datePublished' => $emifree_p['date'],
						'author'        => array(
							'@type'    => 'Person',
							'name'     => $emifree_p['author'],
							'worksFor' => array(
								'@type' => 'Organization',
								'name'  => 'Emifree GmbH',
							),
						),
					);
				},
				array_values( $emifree_posts )
			),
		)
	);
}
emifree_blog_seo();

get_header();

require_once get_template_directory() . '/template-parts/page-blog-index.php';

get_footer();