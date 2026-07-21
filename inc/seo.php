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
	// Derived from home_url() so the constant is subpath-aware: a root
	// install produces 'https://emifree.com', a subpath install
	// (home at /wordpress) produces 'https://emifree.com/wordpress'.
	// All canonicals, OG/Twitter URLs, and JSON-LD schema URLs are
	// built off this constant, so making it track the WP-configured
	// home URL is what keeps the site SEO-correct on any deployment.
	//
	// Replaces the previous hardcoded 'https://emifree.com' that broke
	// every canonical/schema URL on staging subdomains and on subpath
	// production installs.
	define( 'EMIFREE_SITE_URL', home_url() );
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
 * Per-post SEO for /blog/{slug}/ articles (legacy PHP-array path).
 *
 * Backward-compatible entry point — accepts the legacy PHP-array
 * shape from emifree_blog_posts() and delegates to the shared
 * emifree_register_blog_post_schema() helper. New CPT-driven posts
 * use emifree_seo_blog_post_from_cpt() instead; both paths emit the
 * same meta + JSON-LD surface.
 *
 * @param array      $emifree_post     Single post array (slug, title, excerpt, date, author, ...).
 * @param array|null $emifree_next_post Optional "Read next" post (currently unused but reserved for related-posts schema).
 */
function emifree_seo_blog_post( $emifree_post, $emifree_next_post = null ) {
	if ( empty( $emifree_post ) || empty( $emifree_post['slug'] ) ) {
		return;
	}

	$emifree_slug        = $emifree_post['slug'];
	$emifree_title       = $emifree_post['title'];
	$emifree_excerpt     = isset( $emifree_post['excerpt'] ) ? (string) $emifree_post['excerpt'] : '';
	$emifree_date        = isset( $emifree_post['date'] ) ? (string) $emifree_post['date'] : '';
	$emifree_author      = isset( $emifree_post['author'] ) ? (string) $emifree_post['author'] : '';
	$emifree_category    = isset( $emifree_post['category'] ) ? (string) $emifree_post['category'] : 'Technical Guide';
	$emifree_url         = home_url( '/blog/' . $emifree_slug );
	$emifree_image_url   = '';
	$emifree_modified    = $emifree_date;
	$emifree_in_language = 'en-US';

	// Resolve hero image from the legacy hero_image filename.
	if ( ! empty( $emifree_post['hero_image'] ) ) {
		$emifree_image_url = get_template_directory_uri() . '/assets/images/blog/' . $emifree_post['hero_image'];
	}

	emifree_register_blog_post_schema(
		array(
			'title'         => $emifree_title,
			'excerpt'       => $emifree_excerpt,
			'date'          => $emifree_date,
			'modified'      => $emifree_modified,
			'author'        => $emifree_author,
			'url'           => $emifree_url,
			'category'      => $emifree_category,
			'image_url'     => $emifree_image_url,
			'lang'          => $emifree_in_language,
			'schema_id'     => 'emifree-blogpost-schema',
			'hreflang_self' => array(
				'lang' => 'en',
				'href' => $emifree_url,
			),
		)
	);
}

/**
 * Per-post SEO for CPT-driven /blog/{slug}/ articles.
 *
 * Reads every field from WP_Post + post_meta, then calls the same
 * shared helper as emifree_seo_blog_post(). Used by page-blog-post*.php
 * shims when a blog_post CPT entry is found for the requested slug.
 *
 * Adds these on top of the legacy path:
 *   - og:image / twitter:image (Media Library URL of the featured image)
 *   - inLanguage ('en-US' or 'de-DE' from emifree_language meta)
 *   - article:modified_time (post_modified_gmt)
 *   - hreflang alternate link tags (when a sibling exists)
 *
 * @param int|WP_Post $emifree_post_or_id CPT post or post ID.
 */
function emifree_seo_blog_post_from_cpt( $emifree_post_or_id ) {
	$emifree_post = get_post( $emifree_post_or_id );
	if ( ! $emifree_post || 'blog_post' !== $emifree_post->post_type ) {
		return;
	}

	$emifree_id           = (int) $emifree_post->ID;
	$emifree_title        = get_the_title( $emifree_post );
	$emifree_excerpt      = (string) $emifree_post->post_excerpt;
	$emifree_date         = mysql2date( 'Y-m-d', $emifree_post->post_date );
	$emifree_modified     = mysql2date( 'c', $emifree_post->post_modified_gmt );
	$emifree_author       = get_the_author_meta( 'display_name', $emifree_post->post_author );
	if ( ! $emifree_author ) {
		$emifree_author = 'Emifree Team';
	}
	$emifree_category     = (string) get_post_meta( $emifree_id, 'emifree_category', true );
	if ( '' === $emifree_category ) {
		$emifree_category = 'Technical Guide';
	}
	$emifree_lang_meta    = (string) get_post_meta( $emifree_id, 'emifree_language', true );
	$emifree_in_language  = 'de' === $emifree_lang_meta ? 'de-DE' : 'en-US';
	$emifree_image_url    = emifree_get_post_hero_image_url( $emifree_post );

	// URL prefix depends on the language meta so the canonical points
	// to the correct locale-scoped path.
	$emifree_url_prefix   = 'de' === $emifree_lang_meta ? '/de/blog/' : '/blog/';
	$emifree_url          = home_url( $emifree_url_prefix . $emifree_post->post_name );

	// Resolve hreflang alternates via emifree_translation_of meta.
	$emifree_sibling_id   = (int) get_post_meta( $emifree_id, 'emifree_translation_of', true );
	$emifree_hreflang     = emifree_resolve_blog_post_hreflang( $emifree_id, $emifree_url, $emifree_lang_meta, $emifree_sibling_id );

	emifree_register_blog_post_schema(
		array(
			'title'         => $emifree_title,
			'excerpt'       => $emifree_excerpt,
			'date'          => $emifree_date,
			'modified'      => $emifree_modified,
			'author'        => $emifree_author,
			'url'           => $emifree_url,
			'category'      => $emifree_category,
			'image_url'     => $emifree_image_url,
			'lang'          => $emifree_in_language,
			'schema_id'     => 'emifree-blogpost-schema',
			'hreflang_self' => $emifree_hreflang['self'],
			'hreflang_alt'  => $emifree_hreflang['alt'],
		)
	);
}

/**
 * Build the hreflang alternate link set for a blog post.
 *
 * Returns an array with two keys:
 *   - 'self' => ['lang' => 'en'|'de', 'href' => 'https://...']
 *   - 'alt'  => ['lang' => 'en'|'de', 'href' => 'https://...'] (or null when no sibling)
 *
 * Looks up the sibling post via emifree_translation_of meta and resolves
 * its canonical URL with the correct locale prefix.
 *
 * @param int    $emifree_post_id     Current post ID.
 * @param string $emifree_current_url Current post URL.
 * @param string $emifree_current_lang 'en' or 'de' (or '').
 * @param int    $emifree_sibling_id  Sibling post ID, or 0.
 * @return array{self:array, alt:?array}
 */
function emifree_resolve_blog_post_hreflang( $emifree_post_id, $emifree_current_url, $emifree_current_lang, $emifree_sibling_id ) {
	$emifree_self_lang = in_array( $emifree_current_lang, array( 'en', 'de' ), true ) ? $emifree_current_lang : 'en';
	$emifree_self      = array(
		'lang' => $emifree_self_lang,
		'href' => $emifree_current_url,
	);

	if ( $emifree_sibling_id <= 0 || $emifree_sibling_id === $emifree_post_id ) {
		return array( 'self' => $emifree_self, 'alt' => null );
	}

	$emifree_sibling = get_post( $emifree_sibling_id );
	if ( ! $emifree_sibling || 'blog_post' !== $emifree_sibling->post_type ) {
		return array( 'self' => $emifree_self, 'alt' => null );
	}

	$emifree_sibling_lang  = (string) get_post_meta( $emifree_sibling->ID, 'emifree_language', true );
	$emifree_sibling_lang  = in_array( $emifree_sibling_lang, array( 'en', 'de' ), true ) ? $emifree_sibling_lang : 'en';
	$emifree_sibling_prefix = 'de' === $emifree_sibling_lang ? '/de/blog/' : '/blog/';
	$emifree_sibling_url    = home_url( $emifree_sibling_prefix . $emifree_sibling->post_name );

	// Avoid emitting a self-referencing alternate (degenerate case).
	if ( $emifree_sibling_lang === $emifree_self_lang ) {
		return array( 'self' => $emifree_self, 'alt' => null );
	}

	return array(
		'self' => $emifree_self,
		'alt'  => array(
			'lang' => $emifree_sibling_lang,
			'href' => $emifree_sibling_url,
		),
	);
}

/**
 * Shared wp_head emitter for blog posts.
 *
 * All blog-post meta + JSON-LD passes through this single function so
 * the legacy PHP-array path and the CPT path produce byte-equivalent
 * output (modulo language and og:image, which differ by source).
 *
 * Accepts a single $emifree_args array with keys:
 *   title, excerpt, date (Y-m-d), modified (ISO 8601), author, url,
 *   category, image_url ('' = no image), lang ('en-US' / 'de-DE'),
 *   schema_id, hreflang_self, hreflang_alt (optional).
 *
 * Emits:
 *   <title>, description, og:title/description/type/url/image,
 *   article:published_time/article:modified_time/article:author/article:section,
 *   twitter:card/title/description/image,
 *   <link rel="canonical">, <link rel="alternate" hreflang=…> (× 0-2),
 *   BlogPosting JSON-LD with inLanguage + author/worksFor + publisher.
 */
function emifree_register_blog_post_schema( $emifree_args ) {
	$emifree_defaults = array(
		'title'         => '',
		'excerpt'       => '',
		'date'          => '',
		'modified'      => '',
		'author'        => '',
		'url'           => '',
		'category'      => 'Technical Guide',
		'image_url'     => '',
		'lang'          => 'en-US',
		'schema_id'     => 'emifree-blogpost-schema',
		'hreflang_self' => null,
		'hreflang_alt'  => null,
	);
	$emifree_args     = array_merge( $emifree_defaults, $emifree_args );

	$emifree_og_title = $emifree_args['title'] . ' | Emifree Engineering Blog';

	add_action(
		'wp_head',
		static function () use ( $emifree_args, $emifree_og_title ) {
			$emifree_a = $emifree_args;

			echo '<title>' . esc_html( $emifree_og_title ) . '</title>' . "\n";
			echo '<meta name="description" content="' . esc_attr( $emifree_a['excerpt'] ) . '">' . "\n";

			// Open Graph (article-type for blog posts).
			echo '<meta property="og:title" content="' . esc_attr( $emifree_og_title ) . '">' . "\n";
			echo '<meta property="og:description" content="' . esc_attr( $emifree_a['excerpt'] ) . '">' . "\n";
			echo '<meta property="og:type" content="article">' . "\n";
			echo '<meta property="og:url" content="' . esc_attr( $emifree_a['url'] ) . '">' . "\n";
			echo '<meta property="article:published_time" content="' . esc_attr( $emifree_a['date'] ) . '">' . "\n";
			if ( ! empty( $emifree_a['modified'] ) ) {
				echo '<meta property="article:modified_time" content="' . esc_attr( $emifree_a['modified'] ) . '">' . "\n";
			}
			echo '<meta property="article:author" content="' . esc_attr( $emifree_a['author'] ) . '">' . "\n";
			echo '<meta property="article:section" content="' . esc_attr( $emifree_a['category'] ) . '">' . "\n";
			if ( ! empty( $emifree_a['image_url'] ) ) {
				echo '<meta property="og:image" content="' . esc_attr( $emifree_a['image_url'] ) . '">' . "\n";
				echo '<meta property="og:image:alt" content="' . esc_attr( $emifree_a['title'] ) . '">' . "\n";
			}

			// Twitter.
			echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
			echo '<meta name="twitter:title" content="' . esc_attr( $emifree_og_title ) . '">' . "\n";
			echo '<meta name="twitter:description" content="' . esc_attr( $emifree_a['excerpt'] ) . '">' . "\n";
			if ( ! empty( $emifree_a['image_url'] ) ) {
				echo '<meta name="twitter:image" content="' . esc_attr( $emifree_a['image_url'] ) . '">' . "\n";
			}

			// Canonical.
			echo '<link rel="canonical" href="' . esc_attr( $emifree_a['url'] ) . '">' . "\n";

			// hreflang alternates — self + (optional) sibling. Both
			// are emitted as alternate link tags; search engines use
			// the self-pointing hreflang alongside the canonical to
			// disambiguate the per-language URL set.
			if ( ! empty( $emifree_a['hreflang_self']['lang'] ) && ! empty( $emifree_a['hreflang_self']['href'] ) ) {
				echo '<link rel="alternate" hreflang="' . esc_attr( $emifree_a['hreflang_self']['lang'] ) . '" href="' . esc_attr( $emifree_a['hreflang_self']['href'] ) . '">' . "\n";
			}
			if ( ! empty( $emifree_a['hreflang_alt']['lang'] ) && ! empty( $emifree_a['hreflang_alt']['href'] ) ) {
				echo '<link rel="alternate" hreflang="' . esc_attr( $emifree_a['hreflang_alt']['lang'] ) . '" href="' . esc_attr( $emifree_a['hreflang_alt']['href'] ) . '">' . "\n";
			}

			// Per-post BlogPosting JSON-LD.
			$emifree_schema = array(
				'@context'         => 'https://schema.org',
				'@type'            => 'BlogPosting',
				'headline'         => $emifree_a['title'],
				'description'      => $emifree_a['excerpt'],
				'datePublished'    => $emifree_a['date'],
				'dateModified'     => ! empty( $emifree_a['modified'] ) ? $emifree_a['modified'] : $emifree_a['date'],
				'inLanguage'       => $emifree_a['lang'],
				'author'           => array(
					'@type'    => 'Person',
					'name'     => $emifree_a['author'],
					'worksFor' => array(
						'@type' => 'Organization',
						'name'  => 'Emifree GmbH',
					),
				),
				'publisher'        => array(
					'@type' => 'Organization',
					'name'  => 'Emifree GmbH',
					'url'   => home_url(),
				),
				'mainEntityOfPage' => array(
					'@type' => 'WebPage',
					'@id'   => $emifree_a['url'],
				),
				'url'              => $emifree_a['url'],
				'keywords'         => $emifree_a['category'],
			);
			if ( ! empty( $emifree_a['image_url'] ) ) {
				$emifree_schema['image'] = $emifree_a['image_url'];
			}

			echo '<script id="' . esc_attr( $emifree_a['schema_id'] ) . '" type="application/ld+json">' . "\n";
			echo wp_json_encode( $emifree_schema, JSON_UNESCAPED_SLASHES ) . "\n";
			echo '</script>' . "\n";
		},
		1
	);
}

/**
 * Emit a site-wide <link rel="sitemap"> tag.
 *
 * Tells browsers + crawlers where to find the sitemap. Subpath-safe
 * via home_url() with leading slash — root install gets
 * https://emifree.com/sitemap.xml, subpath install gets
 * https://emifree.com/wordpress/sitemap.xml.
 *
 * Hooked at wp_head priority 2, alongside the robots meta below and
 * after the preconnect hints in inc/analytics.php (priority 1). No
 * risk of duplication — the explore phase confirmed no other
 * <link rel="sitemap"> emitter exists in the theme or any plugin.
 */
function emifree_sitemap_link_tag() {
	echo '<link rel="sitemap" type="application/xml" href="' . esc_url( home_url( '/sitemap.xml' ) ) . '">' . "\n";
}
add_action( 'wp_head', 'emifree_sitemap_link_tag', 2 );

/**
 * Emit a site-wide <meta name="robots"> tag.
 *
 * max-snippet:-1         → Google may show any-length snippets for
 *                           blog posts. Lets rich content surface.
 * max-image-preview:large → large thumbnail in SERPs (vs. default
 *                           small). High CTR for blog cards.
 * max-video-preview:-1   → no video preview limit (no video today;
 *                           if/when added, Google will respect this).
 *
 * index, follow is the default behavior so it's spelled out for
 * clarity. No noindex anywhere — every public page should be indexed.
 */
function emifree_robots_meta_tags() {
	echo '<meta name="robots" content="index,follow,max-snippet:-1,max-image-preview:large,max-video-preview:-1">' . "\n";
}
add_action( 'wp_head', 'emifree_robots_meta_tags', 2 );