<?php
/**
 * Emifree Theme — virtual /sitemap.xml.
 *
 * Five pieces:
 *   1. Disable WP core sitemap (wp_sitemaps_enabled filter). Without
 *      this, WP 5.5+ serves /wp-sitemap.xml + per-type /wp-sitemap-*-
 *      *.xml in parallel — two competing sitemap surfaces would
 *      confuse GSC + Bing WMT. The disable is the user's call (per
 *      Phase 3 clarification).
 *   2. Rewrite rule + query var, registered at 'top' priority. Same
 *      pattern as inc/robots.php.
 *   3. template_redirect handler at priority 20 that prints the XML
 *      and exits. The X-Robots-Tag: noindex header is defensive so
 *      even if a crawler tried to index the sitemap itself, it would
 *      be told not to. The Cache-Control header is a courtesy for
 *      humans hitting /sitemap.xml directly in a browser.
 *   4. emifree_get_sitemap_xml() transient-cached wrapper (key
 *      emifree_sitemap_xml_v1, TTL HOUR_IN_SECONDS). Crawlers that
 *      bypass HTTP cache still benefit from the server-side cache.
 *   5. emifree_build_sitemap_xml() that enumerates every URL the
 *      site serves: 10 static (homepages, legal pages, blog indexes)
 *      plus 2 per blog post (EN + DE). The merged feed in
 *      inc/knowledge.php (emifree_get_all_blog_posts_merged) is the
 *      canonical source — it already merges legacy PHP-array posts
 *      with CPT entries and filters by language.
 *
 * Cache invalidation hooks (save_post_blog_post, before_delete_post,
 * wp_trash_post, untrash_post) all call delete_transient on the
 * sitemap cache key. This means a freshly-published post appears in
 * the sitemap on the next request — no 1-hour stale window.
 *
 * Single sitemap (no /sitemap_index.xml) is correct for this site:
 * the spec allows up to 50,000 URLs / 50 MB per sitemap; the
 * Emifree site projects under 200 URLs for the foreseeable future.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// 1. Disable WP core sitemap so /sitemap.xml is the single canonical
//    source. Without this, /wp-sitemap.xml + the per-type split files
//    would also be served — confusing for GSC + Bing WMT submissions.
add_filter( 'wp_sitemaps_enabled', '__return_false' );

// 2. Rewrite rule + query var.
function emifree_register_sitemap_route() {
	add_rewrite_rule(
		'^sitemap\.xml$',
		'index.php?emifree_sitemap=1',
		'top'
	);
}
add_action( 'init', 'emifree_register_sitemap_route' );

function emifree_register_sitemap_query_var( $vars ) {
	$vars[] = 'emifree_sitemap';
	return $vars;
}
add_filter( 'query_vars', 'emifree_register_sitemap_query_var' );

// 3. Handler. Returns early unless the request carries the
//    emifree_sitemap query var, so it's safe to leave hooked.
function emifree_serve_sitemap_xml() {
	if ( ! get_query_var( 'emifree_sitemap' ) ) {
		return;
	}
	nocache_headers();
	header( 'Content-Type: application/xml; charset=utf-8' );
	header( 'X-Robots-Tag: noindex' );
	header( 'Cache-Control: public, max-age=300, s-maxage=300, must-revalidate' );
	echo emifree_get_sitemap_xml(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped — generated XML, every value escaped via esc_xml() below.
	exit;
}
add_action( 'template_redirect', 'emifree_serve_sitemap_xml', 20 );

// 4. Transient-cached wrapper.
function emifree_get_sitemap_xml() {
	$emifree_cache_key = 'emifree_sitemap_xml_v3';
	$emifree_cached    = get_transient( $emifree_cache_key );
	if ( false !== $emifree_cached ) {
		return $emifree_cached;
	}
	$emifree_xml = emifree_build_sitemap_xml();
	set_transient( $emifree_cache_key, $emifree_xml, HOUR_IN_SECONDS );
	return $emifree_xml;
}

// 5. Build the full XML document.
function emifree_build_sitemap_xml() {
	$emifree_urls = array_merge(
		emifree_collect_sitemap_static_urls(),
		emifree_collect_sitemap_blog_post_urls()
	);

	$emifree_lines   = array();
	$emifree_lines[] = '<?xml version="1.0" encoding="UTF-8"?>';
	$emifree_lines[] = '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"';
	$emifree_lines[] = '        xmlns:xhtml="http://www.w3.org/1999/xhtml">';

	foreach ( $emifree_urls as $emifree_url ) {
		$emifree_lines[] = '  <url>';
		$emifree_lines[] = '    <loc>' . emifree_xml_escape_url( $emifree_url['loc'] ) . '</loc>';
		if ( ! empty( $emifree_url['lastmod'] ) ) {
			$emifree_lines[] = '    <lastmod>' . emifree_xml_escape( $emifree_url['lastmod'] ) . '</lastmod>';
		}
		if ( ! empty( $emifree_url['changefreq'] ) ) {
			$emifree_lines[] = '    <changefreq>' . emifree_xml_escape( $emifree_url['changefreq'] ) . '</changefreq>';
		}
		if ( isset( $emifree_url['priority'] ) ) {
			$emifree_lines[] = '    <priority>' . emifree_xml_escape( (string) $emifree_url['priority'] ) . '</priority>';
		}
		// hreflang alternates — emitted as <xhtml:link> per URL so
		// Google can pair EN/DE siblings without a separate file.
		if ( ! empty( $emifree_url['hreflang'] ) ) {
			foreach ( $emifree_url['hreflang'] as $emifree_lang_code => $emifree_lang_url ) {
				$emifree_lines[] = '    <xhtml:link rel="alternate" hreflang="' . emifree_xml_escape( $emifree_lang_code ) . '" href="' . emifree_xml_escape_url( $emifree_lang_url ) . '"/>';
			}
		}
		$emifree_lines[] = '  </url>';
	}

	$emifree_lines[] = '</urlset>';

	return implode( "\n", $emifree_lines ) . "\n";
}

/**
 * Static URL set — homepages, legal pages, blog indexes.
 * 10 entries. lastmod is the sitemap build time (these pages change
 * rarely; the 1-hour transient + cache-bust on save_post covers any
 * real change).
 */
function emifree_collect_sitemap_static_urls() {
	$emifree_now = mysql2date( 'c', current_time( 'mysql', true ) );

	return array(
		array(
			'loc'        => home_url( '/en/' ),
			'lastmod'    => $emifree_now,
			'changefreq' => 'daily',
			'priority'   => '1.0',
		),
		array(
			'loc'        => home_url( '/de/' ),
			'lastmod'    => $emifree_now,
			'changefreq' => 'daily',
			'priority'   => '1.0',
		),
		array(
			'loc'        => home_url( '/impressum/' ),
			'lastmod'    => $emifree_now,
			'changefreq' => 'monthly',
			'priority'   => '0.5',
		),
		array(
			'loc'        => home_url( '/privacy/' ),
			'lastmod'    => $emifree_now,
			'changefreq' => 'monthly',
			'priority'   => '0.5',
		),
		array(
			'loc'        => home_url( '/terms/' ),
			'lastmod'    => $emifree_now,
			'changefreq' => 'monthly',
			'priority'   => '0.5',
		),
		array(
			'loc'        => home_url( '/de/impressum/' ),
			'lastmod'    => $emifree_now,
			'changefreq' => 'monthly',
			'priority'   => '0.5',
		),
		array(
			'loc'        => home_url( '/de/datenschutz/' ),
			'lastmod'    => $emifree_now,
			'changefreq' => 'monthly',
			'priority'   => '0.5',
		),
		array(
			'loc'        => home_url( '/de/agb/' ),
			'lastmod'    => $emifree_now,
			'changefreq' => 'monthly',
			'priority'   => '0.5',
		),
		array(
			'loc'        => home_url( '/blog/' ),
			'lastmod'    => $emifree_now,
			'changefreq' => 'daily',
			'priority'   => '0.9',
		),
		array(
			'loc'        => home_url( '/de/blog/' ),
			'lastmod'    => $emifree_now,
			'changefreq' => 'daily',
			'priority'   => '0.9',
		),
	);
}

/**
 * Dynamic URL set — blog posts, language-paired.
 *
 * For each entry returned by emifree_get_all_blog_posts_merged('en'),
 * emit two <url> blocks: one for /blog/{slug}/, one for
 * /de/blog/{slug}/. Slug parity is guaranteed by
 * emifree_mirror_slug_to_sibling() in inc/cpt-blog.php:304, so the DE
 * sibling is always reachable at the same slug.
 *
 * Both <url> blocks carry the same pair of hreflang alternates —
 * one self, one alternate — which is what Google wants for paired
 * locales.
 *
 * lastmod source:
 *   - CPT entries: post_modified_gmt (ISO 8601).
 *   - Legacy entries (from emifree_blog_posts()): the 'date' field
 *     cast to midnight ISO 8601 — legacy posts are frozen content.
 */
function emifree_collect_sitemap_blog_post_urls() {
	// The merged feed + normalizer live in inc/knowledge.php. functions.php
	// doesn't load it globally — it loads on demand from page-blog*.php
	// shims. The sitemap endpoint never hits those shims, so require
	// knowledge.php here to make sure emifree_get_all_blog_posts_merged()
	// and emifree_normalize_post_for_card() exist.
	if ( ! function_exists( 'emifree_get_all_blog_posts_merged' ) ) {
		$emifree_knowledge_path = get_template_directory() . '/inc/knowledge.php';
		if ( file_exists( $emifree_knowledge_path ) ) {
			require_once $emifree_knowledge_path;
		}
	}

	if ( ! function_exists( 'emifree_get_all_blog_posts_merged' ) ) {
		return array();
	}

	$emifree_merged = emifree_get_all_blog_posts_merged( 'en' );
	$emifree_urls   = array();

	foreach ( $emifree_merged as $emifree_slug => $emifree_post ) {
		// Resolve lastmod — prefer modified_gmt (set on CPT entries
		// via emifree_cpt_to_array_shape in inc/knowledge.php),
		// fall back to the legacy 'date' field.
		$emifree_lastmod = '';
		if ( ! empty( $emifree_post['modified_gmt'] ) ) {
			$emifree_lastmod = mysql2date( 'c', $emifree_post['modified_gmt'] );
		} elseif ( ! empty( $emifree_post['date'] ) ) {
			$emifree_lastmod = $emifree_post['date'] . 'T00:00:00+00:00';
		}

		$emifree_en_url = home_url( '/blog/' . $emifree_slug . '/' );
		$emifree_de_url = home_url( '/de/blog/' . $emifree_slug . '/' );

		// Both EN and DE <url> blocks carry the same hreflang pair.
		$emifree_hreflang = array(
			'en' => $emifree_en_url,
			'de' => $emifree_de_url,
		);

		$emifree_urls[] = array(
			'loc'        => $emifree_en_url,
			'lastmod'    => $emifree_lastmod,
			'changefreq' => 'weekly',
			'priority'   => '0.8',
			'hreflang'   => $emifree_hreflang,
		);
		$emifree_urls[] = array(
			'loc'        => $emifree_de_url,
			'lastmod'    => $emifree_lastmod,
			'changefreq' => 'weekly',
			'priority'   => '0.8',
			'hreflang'   => $emifree_hreflang,
		);
	}

	return $emifree_urls;
}

/**
 * XML-escape a string for use inside element text. WP 6.1+ ships a
 * global esc_xml() in wp-includes/formatting.php — use it where
 * available, fall back to a manual escape on older installs. The
 * fallback only handles the five XML predefined entities, which is
 * all that's required by the sitemap spec.
 */
function emifree_xml_escape( $emifree_value ) {
	if ( function_exists( '\\esc_xml' ) ) {
		return \esc_xml( $emifree_value );
	}
	return htmlspecialchars( (string) $emifree_value, ENT_XML1 | ENT_QUOTES, 'UTF-8' );
}

/**
 * XML-escape a URL. Same as emifree_xml_escape() — URLs need the
 * same five XML predefined entities (especially & → &amp;) escaped.
 */
function emifree_xml_escape_url( $emifree_url ) {
	return emifree_xml_escape( (string) $emifree_url );
}

/**
 * XML-escape a URL. Same as esc_xml() but also encodes the ampersand
 * as &amp; (which is what the sitemap spec requires inside <loc>
 * and href attributes — the raw & breaks XML parsing).
 */
function esc_xml_url( $emifree_url ) {
	return esc_xml( (string) $emifree_url );
}

/**
 * Cache invalidation — bust the sitemap transient whenever a blog
 * post is published, updated, trashed, or restored. Priority 20 on
 * save_post_blog_post runs after emifree_save_blog_meta_box (priority
 * 10) so the post meta is final by the time we invalidate. The
 * generic before_delete_post / wp_trash_post / untrash_post hooks
 * carry a post_type guard so unrelated deletes don't churn the
 * transient.
 */
function emifree_invalidate_sitemap_cache( $emifree_post_id = 0 ) {
	if ( $emifree_post_id ) {
		$emifree_post = get_post( $emifree_post_id );
		if ( $emifree_post && 'blog_post' !== $emifree_post->post_type ) {
			return;
		}
	}
	delete_transient( 'emifree_sitemap_xml_v3' );
}
add_action( 'save_post_blog_post', 'emifree_invalidate_sitemap_cache', 20 );
add_action( 'before_delete_post', 'emifree_invalidate_sitemap_cache' );
add_action( 'wp_trash_post', 'emifree_invalidate_sitemap_cache' );
add_action( 'untrash_post', 'emifree_invalidate_sitemap_cache' );
