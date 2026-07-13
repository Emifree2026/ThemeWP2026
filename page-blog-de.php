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
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Local DE posts metadata. The metadata is duplicated here (and in
// page-blog-post-de.php) so each page shim is self-contained — keeps
// the i18n refactor additive and avoids growing the global function
// namespace during this piece. If a future piece needs these from
// another template, hoist into inc/.
$emifree_de_posts = array(
	'the-strategic-edge-of-clean-air' => array(
		'id'            => '1',
		'slug'          => 'the-strategic-edge-of-clean-air',
		'title'         => 'Der strategische Vorteil sauberer Luft: Warum Hochleistungs-Ölnebelfiltration für die moderne Zerspanung unverzichtbar ist',
		'date'          => '2026-06-29',
		'author'        => 'Victoria Pedroza',
	),
	'precision-in-every-breath' => array(
		'id'            => '2',
		'slug'          => 'precision-in-every-breath',
		'title'         => 'Präzision in jedem Atemzug: Ein technischer Leitfaden zur industriellen Ölnebelfiltration',
		'date'          => '2026-06-29',
		'author'        => 'Victoria Pedroza',
	),
);

/**
 * Per-page SEO + Blog schema (German edition).
 *
 * Mirrors emifree_blog_seo() in page-blog.php but with German strings
 * + German author/publisher references. Uses the same
 * emifree_seo_page_with_schema() helper as the English version.
 */
emifree_seo_page_with_schema(
	'Emifree Engineering-Blog — Einblicke in industrielle Luftfiltration',
	'Technische Leitfäden und Praxiseinblicke zur industriellen Ölnebelfiltration, CNC-Luftqualität, mechanischen und elektrostatischen Abscheideverfahren sowie EU-Compliance. Aus dem Engineering-Team von Emifree.',
	EMIFREE_SITE_URL . '/de/blog',
	'emifree-blog-schema-de',
	array(
		'@context'    => 'https://schema.org',
		'@type'       => 'Blog',
		'name'        => 'Emifree Engineering-Blog',
		'description' => 'Technische Leitfäden und Praxiseinblicke zur industriellen Ölnebelfiltration aus dem Engineering-Team von Emifree.',
		'url'         => EMIFREE_SITE_URL . '/de/blog',
		'inLanguage'  => 'de-DE',
		'publisher'   => array(
			'@type' => 'Organization',
			'name'  => 'Emifree GmbH',
			'url'   => EMIFREE_SITE_URL,
		),
		'blogPost'    => array_map(
			static function ( $emifree_p ) {
				return array(
					'@type'         => 'BlogPosting',
					'headline'      => $emifree_p['title'],
					'url'           => EMIFREE_SITE_URL . '/de/blog/' . $emifree_p['slug'],
					'datePublished' => $emifree_p['date'],
					'inLanguage'    => 'de-DE',
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
			array_values( $emifree_de_posts )
		),
	)
);

get_header();

require_once get_template_directory() . '/template-parts/page-blog-index-de.php';

get_footer();