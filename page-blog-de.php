<?php
/**
 * Page template (German): /de/blog/.
 *
 * Hard-coded translation of page-blog.php. Renders the German blog
 * index with per-page SEO + JSON-LD Blog schema in German.
 *
 * The emifree_seo_page_with_schema() helper in inc/seo.php is
 * language-agnostic; the German strings are simply passed as
 * parameters here.
 *
 * Post feed = emifree_get_all_blog_posts_merged('de', $emifree_de_posts)
 * (legacy DE array + any DE CPT entries appended, slug-keyed, deduped).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once get_template_directory() . '/inc/knowledge.php';

// Local DE posts metadata. Sourced from emifree_blog_posts_de() in
// inc/knowledge.php — full-shape (title, hero_image, category, etc.)
// so the merged-feed normalizer produces non-empty cards.
$emifree_de_posts = function_exists( 'emifree_blog_posts_de' )
	? emifree_blog_posts_de()
	: array();

/**
 * Per-page SEO + Blog schema (German edition).
 *
 * Mirrors emifree_blog_seo() in page-blog.php but with German strings
 * + German author/publisher references. Uses the same
 * emifree_seo_page_with_schema() helper as the English version.
 *
 * Iterates the merged DE feed (legacy + CPT) so any newly-published
 * DE CPT entry shows up automatically.
 */
$emifree_blog_index_posts_de = function_exists( 'emifree_get_all_blog_posts_merged' )
	? emifree_get_all_blog_posts_merged( 'de', $emifree_de_posts )
	: $emifree_de_posts;

$emifree_blog_post_entries_de = array();
foreach ( $emifree_blog_index_posts_de as $emifree_slug => $emifree_p ) {
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
		continue;
	}
	$emifree_blog_post_entries_de[] = array(
		'@type'         => 'BlogPosting',
		'headline'      => $emifree_p['title'],
		'url'           => home_url( '/de/blog/' . $emifree_slug ),
		'datePublished' => $emifree_date,
		'inLanguage'    => 'de-DE',
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
	'Emifree Engineering-Blog — Einblicke in industrielle Luftfiltration',
	'Technische Leitfäden und Praxiseinblicke zur industriellen Ölnebelfiltration, CNC-Luftqualität, mechanischen und elektrostatischen Abscheideverfahren sowie EU-Compliance. Aus dem Engineering-Team von Emifree.',
	home_url( '/de/blog' ),
	'emifree-blog-schema-de',
	array(
		'@context'    => 'https://schema.org',
		'@type'       => 'Blog',
		'name'        => 'Emifree Engineering-Blog',
		'description' => 'Technische Leitfäden und Praxiseinblicke zur industriellen Ölnebelfiltration aus dem Engineering-Team von Emifree.',
		'url'         => home_url( '/de/blog' ),
		'inLanguage'  => 'de-DE',
		'publisher'   => array(
			'@type' => 'Organization',
			'name'  => 'Emifree GmbH',
			'url'   => home_url(),
		),
		'blogPost'    => $emifree_blog_post_entries_de,
	)
);

get_header();

require_once get_template_directory() . '/template-parts/page-blog-index-de.php';

get_footer();