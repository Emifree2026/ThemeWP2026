<?php
/**
 * Knowledge data + SVG icons.
 *
 * Mirrors src/components/Knowledge.jsx and src/data/blogPosts.jsx from
 * the React app post-cleanup (no FAQ, no Latest Articles grid). Three
 * panels (blog / about / downloads):
 *
 *  - emifree_knowledge_icons() — name => inner-SVG map for every
 *    lucide icon used by the section (tab labels, panel headings,
 *    card icons, FeaturedBlogCard meta row).
 *  - emifree_blog_posts() — the 2 real articles from blogPosts.jsx
 *    (Precision in Every Breath; The Strategic Edge of Clean Air).
 *    Only metadata + a one-paragraph body preview; the full body
 *    ports with Pieces 15 + 16.
 *  - emifree_catalog_pdfs() — 4 catalog entries. Only 2 PDFs exist
 *    on disk in this commit (ECO AIR EN, ECO AIR DE in assets/catalog/);
 *    the EARIA EN + Full Range 2026 slots render as "coming soon"
 *    placeholders to preserve React parity.
 *
 * Icons are inline SVG paths from lucide-react (24×24 viewBox,
 * stroke-based) — no external icon library required.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'emifree_knowledge_icons' ) ) :
	function emifree_knowledge_icons() {
		return array(
			'book-open'      => '<path d="M12 7v14"></path><path d="M3 18a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1h7a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H4a1 1 0 0 1-1-1z"></path><path d="M21 18a1 1 0 0 0 1-1V4a1 1 0 0 0-1-1h-7a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h7a1 1 0 0 0 1-1z"></path>',
			'users'          => '<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M22 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path>',
			'download'       => '<path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line>',
			'award'          => '<circle cx="12" cy="8" r="6"></circle><path d="M15.477 12.89 17 22l-5-3-5 3 1.523-9.11"></path>',
			'book-marked'    => '<path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H19a1 1 0 0 1 1 1v18a1 1 0 0 1-1 1H6.5a1 1 0 0 1 0-5H20"></path><polyline points="9 10 11 12 13 10"></polyline>',
			'building-2'     => '<path d="M10 12h4"></path><path d="M10 8h4"></path><path d="M14 21v-4a2 2 0 0 0-4 0v4"></path><path d="M6 10H4a2 2 0 0 0-2 2v7a2 2 0 0 0 2 2h2"></path><path d="M22 19h-2a2 2 0 0 1-2-2v-7a2 2 0 0 1 2-2h2"></path><path d="M18 21V5a2 2 0 0 0-2-2H8a2 2 0 0 0-2 2v16"></path>',
			'leaf'           => '<path d="M11 20A7 7 0 0 1 9.8 6.1C15.5 5 17 4.48 19.2 2.96c1.4 1 2 5.04 2 7.04 0 5.52-4.48 10-10 10Z"></path><path d="M2 21c0-3 1.85-5.36 5.08-6"></path>',
			'shield'         => '<path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"></path>',
			'settings'       => '<path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"></path><circle cx="12" cy="12" r="3"></circle>',
			'target'         => '<circle cx="12" cy="12" r="10"></circle><circle cx="12" cy="12" r="6"></circle><circle cx="12" cy="12" r="2"></circle>',
			'file-text'      => '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><line x1="10" y1="9" x2="8" y2="9"></line>',
			'chevron-right'  => '<path d="m9 18 6-6-6-6"></path>',
			'arrow-right'    => '<path d="M5 12h14"></path><path d="m12 5 7 7-7 7"></path>',
			'calendar'       => '<rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line>',
			'clock'          => '<circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline>',
		);
	}
endif;

if ( ! function_exists( 'emifree_blog_posts' ) ) :
	function emifree_blog_posts() {
		return array(
			'the-strategic-edge-of-clean-air' => array(
				'id'            => '1',
				'slug'          => 'the-strategic-edge-of-clean-air',
				'title'         => 'The Strategic Edge of Clean Air: Why High-Performance Oil Mist Filtration is Essential for Modern Machining',
				'excerpt'       => 'Industrial oil mist filtration is not an accessory — it is a strategic investment in workplace safety, equipment longevity, and operational efficiency for high-precision machining environments.',
				'category'      => 'Technical Guide',
				'date'          => '2026-06-29',
				'formatted_date'=> 'June 29, 2026',
				'read_time'     => '5 min read',
				'author'        => 'Victoria Pedroza',
				'author_role'   => 'Product Manager, Emifree GmbH',
				'hero_image'    => 'Workers_operating_CNC_machines.jpeg',
				'body_preview'  => 'In modern precision manufacturing, factory air quality is no longer a peripheral concern. The air inside a workshop directly influences equipment reliability, regulatory compliance, and — most critically — workforce health. An industrial oil mist collector positioned at each machine tool is a strategic investment, not an accessory.',
			),
			'precision-in-every-breath' => array(
				'id'            => '2',
				'slug'          => 'precision-in-every-breath',
				'title'         => 'Precision in Every Breath: A Technical Guide to Industrial Oil Mist Filtration',
				'excerpt'       => 'A technical comparison of mechanical and electrostatic oil mist filtration technologies — and how source-capture extraction protects your workforce, machinery, and the bottom line.',
				'category'      => 'Technical Guide',
				'date'          => '2026-06-29',
				'formatted_date'=> 'June 29, 2026',
				'read_time'     => '7 min read',
				'author'        => 'Victoria Pedroza',
				'author_role'   => 'Product Manager, Emifree GmbH',
				'hero_image'    => 'CNC_2.jpg',
				'body_preview'  => 'For facility managers and production engineers, factory air quality is an operational necessity — not a checkbox. Oil mist generated by high-speed machining, grinding, and turning operations has measurable impact on health outcomes, machine uptime, and the bottom line. This article walks through how oil mist forms, why source-capture filtration matters, and how to choose between mechanical and electrostatic separation.',
			),
		);
	}
endif;

if ( ! function_exists( 'emifree_catalog_pdfs' ) ) :
	function emifree_catalog_pdfs() {
		$emifree_catalog_uri = get_template_directory_uri() . '/assets/catalog/';
		return array(
			array(
				'name'      => 'ECO AIR Cleaner Catalog',
				'filename'  => 'emifree_eco_air_cleaner_katalog_ENG_v4.2.pdf',
				'size'      => '2.1 MB',
				'lang'      => 'EN',
				'available' => true,
				'url'       => $emifree_catalog_uri . 'emifree_eco_air_cleaner_katalog_ENG_v4.2.pdf',
			),
			array(
				'name'      => 'ECO AIR Cleaner Katalog',
				'filename'  => 'emifree_eco_air_cleaner_katalog_DE_v4.2.pdf',
				'size'      => '2.1 MB',
				'lang'      => 'DE',
				'available' => true,
				'url'       => $emifree_catalog_uri . 'emifree_eco_air_cleaner_katalog_DE_v4.2.pdf',
			),
			array(
				'name'      => 'EARIA Electrostatic Catalog',
				'filename'  => 'earia-catalog-en.pdf',
				'size'      => '3.8 MB',
				'lang'      => 'EN',
				'available' => false,
				'url'       => '',
			),
			array(
				'name'      => 'Full Product Range 2026',
				'filename'  => 'full-range-2026.pdf',
				'size'      => '12.5 MB',
				'lang'      => 'EN',
				'available' => false,
				'url'       => '',
			),
		);
	}
endif;

if ( ! function_exists( 'emifree_knowledge_pdf_card' ) ) :
	/**
	 * Render a single catalog card.
	 *
	 * Branches on the entry's `available` flag: a real `<a download>` when
	 * the PDF exists on disk, or a non-interactive `<div aria-disabled>` with
	 * opacity-60 + "(coming soon)" copy when it doesn't. This preserves
	 * React parity for the 4-card grid without fabricating file URLs.
	 *
	 * @param array $emifree_pdf   Catalog entry from emifree_catalog_pdfs().
	 * @param array $emifree_icons Icon map from emifree_knowledge_icons().
	 */
	function emifree_knowledge_pdf_card( $emifree_pdf, $emifree_icons ) {
		$emifree_has_link = ! empty( $emifree_pdf['available'] ) && ! empty( $emifree_pdf['url'] );

		if ( $emifree_has_link ) {
			$emifree_open = '<a href="' . esc_url( $emifree_pdf['url'] ) . '" download class="bg-white rounded-xl p-6 shadow-sm border border-slate-100 hover:shadow-lg hover:border-blue-200 transition-all duration-300 group block focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2">';
			$emifree_close = '</a>';
		} else {
			$emifree_open = '<div class="bg-white rounded-xl p-6 shadow-sm border border-slate-100 opacity-60 cursor-not-allowed" aria-disabled="true">';
			$emifree_close = '</div>';
		}

		echo $emifree_open; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped — controlled opening tag (anchor or div).
		?>
		<div class="w-12 h-12 bg-blue-100 <?php echo $emifree_has_link ? 'group-hover:bg-blue-700' : ''; ?> rounded-xl flex items-center justify-center mb-4 transition-colors duration-300">
			<svg class="w-6 h-6 text-blue-700 <?php echo $emifree_has_link ? 'group-hover:text-white' : ''; ?> transition-colors duration-300" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true">
				<?php echo $emifree_icons['file-text']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped — SVG markup, controlled. ?>
			</svg>
		</div>
		<h4 class="text-zinc-900 font-semibold mb-2 leading-snug">
			<?php echo esc_html( $emifree_pdf['name'] ); ?>
			<?php if ( ! $emifree_has_link ) : ?>
				<span class="text-slate-500 font-normal">(coming soon)</span>
			<?php endif; ?>
		</h4>
		<div class="flex items-center justify-between text-sm text-slate-500">
			<span><?php echo esc_html( $emifree_pdf['size'] ); ?></span>
			<span class="bg-slate-100 px-2 py-0.5 rounded text-xs font-semibold"><?php echo esc_html( $emifree_pdf['lang'] ); ?></span>
		</div>
		<?php
		echo $emifree_close; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped — controlled closing tag.
	}
endif;

if ( ! function_exists( 'emifree_get_post_by_slug' ) ) :
	/**
	 * Fetch a single blog post's metadata by slug, or null if missing.
	 *
	 * Mirrors the React's getPostBySlug() in src/data/blogPosts.jsx.
	 * Used by /blog/{slug}/ routing to look up the post before rendering.
	 *
	 * @param string $emifree_slug Post slug.
	 * @return array|null Post array from emifree_blog_posts(), or null.
	 */
	function emifree_get_post_by_slug( $emifree_slug ) {
		$emifree_posts = emifree_blog_posts();
		return isset( $emifree_posts[ $emifree_slug ] ) ? $emifree_posts[ $emifree_slug ] : null;
	}
endif;

if ( ! function_exists( 'emifree_get_post_body_html' ) ) :
	/**
	 * Load a single blog post's HTML body from data/posts/{slug}.php.
	 *
	 * Returns '' (empty string) if no body file exists, the slug is
	 * unknown, or the file returns a malformed array. The renderer
	 * (template-parts/page-blog-post.php) passes the return value
	 * through wp_kses_post() before echo.
	 *
	 * @param string $emifree_slug Post slug.
	 * @return string Post body HTML, or empty string if not available.
	 */
	function emifree_get_post_body_html( $emifree_slug ) {
		$emifree_path = get_template_directory() . '/data/posts/' . $emifree_slug . '.php';
		if ( ! file_exists( $emifree_path ) ) {
			return '';
		}
		$emifree_body = include $emifree_path;
		if ( ! is_array( $emifree_body ) || empty( $emifree_body['body_html'] ) ) {
			return '';
		}
		return $emifree_body['body_html'];
	}
endif;

if ( ! function_exists( 'emifree_get_all_posts_sorted' ) ) :
	/**
	 * Return all blog posts sorted by date descending.
	 *
	 * Used to determine the "Read next" suggestion on a single-post
	 * page (most-recent other post). With only 2 posts this trivially
	 * resolves to "the other one". Returns the array keyed by slug,
	 * matching how emifree_blog_posts() keys its data.
	 *
	 * @return array<slug => post> Sorted by date DESC.
	 */
	function emifree_get_all_posts_sorted() {
		$emifree_posts = emifree_blog_posts();
		uasort(
			$emifree_posts,
			static function ( $emifree_a, $emifree_b ) {
				return strcmp( $emifree_b['date'], $emifree_a['date'] );
			}
		);
		return $emifree_posts;
	}
endif;

/* -------------------------------------------------------------------------
 * CPT-aware helpers — sit alongside the legacy PHP-array path so
 * existing callers stay unchanged when there are no CPT entries.
 *
 * The seam between the two data sources is emifree_normalize_post_for_card()
 * (shared shape consumed by inc/blog-cards.php) and
 * emifree_cpt_to_array_shape() (CPT → legacy shape consumed by the
 * single-post template-parts, which still reads array keys like
 * `title`, `excerpt`, `date`, `formatted_date`, etc.).
 * ------------------------------------------------------------------------- */

if ( ! function_exists( 'emifree_normalize_post_for_card' ) ) :
	/**
	 * Reduce any post source (PHP-array OR WP_Post of type blog_post)
	 * to the minimal shape consumed by emifree_blog_card() and
	 * emifree_featured_blog_card() in inc/blog-cards.php.
	 *
	 * The shape:
	 *   - slug              string
	 *   - title             string
	 *   - excerpt           string
	 *   - category          string
	 *   - formatted_date    string  (human-readable)
	 *   - read_time         string
	 *   - hero_image_url    string  (full URL, NOT a bare filename)
	 *
	 * Legacy callers keep passing PHP-array posts with a `hero_image`
	 * FILENAME; this helper resolves that into a full URL via the
	 * assets/images/blog/ directory. CPT callers pass WP_Post, and we
	 * resolve the featured-image attachment URL via the Media Library.
	 *
	 * @param array|WP_Post $emifree_source Legacy array or WP_Post.
	 * @return array Normalized shape.
	 */
	function emifree_normalize_post_for_card( $emifree_source ) {
		if ( $emifree_source instanceof WP_Post ) {
			$emifree_id = (int) $emifree_source->ID;
			return array(
				'slug'           => $emifree_source->post_name,
				'title'          => get_the_title( $emifree_source ),
				'excerpt'        => $emifree_source->post_excerpt,
				'category'       => (string) get_post_meta( $emifree_id, 'emifree_category', true ),
				'date'           => mysql2date( 'Y-m-d', $emifree_source->post_date ),
				'formatted_date' => mysql2date( get_option( 'date_format' ), $emifree_source->post_date ),
				'modified_gmt'   => $emifree_source->post_modified_gmt,
				'read_time'      => (string) get_post_meta( $emifree_id, 'emifree_read_time', true ),
				'hero_image_url' => emifree_get_post_hero_image_url( $emifree_id ),
			);
		}

		// Legacy PHP-array shape.
		return array(
			'slug'           => isset( $emifree_source['slug'] ) ? (string) $emifree_source['slug'] : '',
			'title'          => isset( $emifree_source['title'] ) ? (string) $emifree_source['title'] : '',
			'excerpt'        => isset( $emifree_source['excerpt'] ) ? (string) $emifree_source['excerpt'] : '',
			'category'       => isset( $emifree_source['category'] ) ? (string) $emifree_source['category'] : '',
			'date'           => isset( $emifree_source['date'] ) ? (string) $emifree_source['date'] : '',
			'formatted_date' => isset( $emifree_source['formatted_date'] ) ? (string) $emifree_source['formatted_date'] : '',
			'read_time'      => isset( $emifree_source['read_time'] ) ? (string) $emifree_source['read_time'] : '',
			'hero_image_url' => emifree_get_post_hero_image_url( $emifree_source ),
		);
	}
endif;

if ( ! function_exists( 'emifree_get_post_hero_image_url' ) ) :
	/**
	 * Resolve a post's hero image to a full URL.
	 *
	 * - For WP_Post of type blog_post: read the featured-image
	 *   attachment; return its URL. Falls back to '' when no featured
	 *   image is set.
	 * - For a legacy PHP-array (with `slug` and `hero_image` keys):
	 *   resolve to {template_uri}/assets/images/blog/{hero_image}.
	 * - For a bare post ID int: same as WP_Post path.
	 * - Otherwise: '' (no image).
	 *
	 * @param array|WP_Post|int $emifree_source Legacy array, WP_Post, or post ID.
	 * @return string Full URL, or empty string if not resolvable.
	 */
	function emifree_get_post_hero_image_url( $emifree_source ) {
		// WP_Post path — featured image.
		if ( $emifree_source instanceof WP_Post ) {
			$emifree_thumb_id = get_post_thumbnail_id( $emifree_source );
			if ( $emifree_thumb_id ) {
				$emifree_url = wp_get_attachment_url( $emifree_thumb_id );
				if ( $emifree_url ) {
					return $emifree_url;
				}
			}
			// Fallback: legacy filename meta stored on the CPT.
			$emifree_legacy_filename = get_post_meta( $emifree_source->ID, 'emifree_hero_image_legacy', true );
			if ( $emifree_legacy_filename ) {
				return get_template_directory_uri() . '/assets/images/blog/' . $emifree_legacy_filename;
			}
			return '';
		}

		// Bare post ID.
		if ( is_numeric( $emifree_source ) ) {
			$emifree_post = get_post( (int) $emifree_source );
			if ( $emifree_post ) {
				return emifree_get_post_hero_image_url( $emifree_post );
			}
			return '';
		}

		// Legacy PHP-array path — filename → assets/images/blog/.
		if ( is_array( $emifree_source ) && ! empty( $emifree_source['hero_image'] ) ) {
			return get_template_directory_uri() . '/assets/images/blog/' . $emifree_source['hero_image'];
		}

		return '';
	}
endif;

if ( ! function_exists( 'emifree_cpt_to_array_shape' ) ) :
	/**
	 * Convert a WP_Post of type blog_post into the legacy array shape
	 * consumed by the single-post template-parts.
	 *
	 * The single-post template reads these keys: id, slug, title,
	 * excerpt, category, date, formatted_date, read_time, author,
	 * author_role, hero_image (filename), body_raw (raw post_content
	 * for CPT-driven render), is_cpt flag.
	 *
	 * `body_html` is intentionally NOT populated — CPT bodies are
	 * rendered via apply_filters('the_content', $body_raw) in the
	 * template, not via wp_kses_post( emifree_get_post_body_html() ).
	 *
	 * @param WP_Post $emifree_post CPT post.
	 * @return array Legacy-shaped array, or null if not a blog_post.
	 */
	function emifree_cpt_to_array_shape( $emifree_post ) {
		if ( ! $emifree_post instanceof WP_Post || 'blog_post' !== $emifree_post->post_type ) {
			return null;
		}

		$emifree_id           = (int) $emifree_post->ID;
		$emifree_date_iso     = mysql2date( 'Y-m-d', $emifree_post->post_date );
		$emifree_formatted_dt = mysql2date( get_option( 'date_format' ), $emifree_post->post_date );

		// Author display name falls back to "Emifree Team" when unset
		// (e.g. on a fresh CPT entry whose author_user hasn't been
		// assigned by the editor — post_author defaults to current user).
		$emifree_author = get_the_author_meta( 'display_name', $emifree_post->post_author );
		if ( ! $emifree_author ) {
			$emifree_author = 'Emifree Team';
		}

		return array(
			'id'             => (string) $emifree_id,
			'slug'           => $emifree_post->post_name,
			'title'          => get_the_title( $emifree_post ),
			'excerpt'        => $emifree_post->post_excerpt,
			'category'       => (string) get_post_meta( $emifree_id, 'emifree_category', true ),
			'date'           => $emifree_date_iso,
			'formatted_date' => $emifree_formatted_dt,
			'read_time'      => (string) get_post_meta( $emifree_id, 'emifree_read_time', true ),
			'author'         => $emifree_author,
			'author_role'    => (string) get_post_meta( $emifree_id, 'emifree_author_role', true ),
			'language'       => (string) get_post_meta( $emifree_id, 'emifree_language', true ),
			'translation_of' => (int) get_post_meta( $emifree_id, 'emifree_translation_of', true ),
			'modified_iso'   => mysql2date( 'c', $emifree_post->post_modified_gmt ),
			'body_raw'       => $emifree_post->post_content,
			'is_cpt'         => true,
		);
	}
endif;

if ( ! function_exists( 'emifree_query_cpt_blog_post_by_slug' ) ) :
	/**
	 * Look up a published blog_post CPT entry by slug.
	 *
	 * Returns the WP_Post or null. Slug is matched against post_name.
	 * Used by the page-blog-post*.php shims to prefer a CPT entry
	 * over the legacy PHP-array when both exist for the same slug.
	 *
	 * Implementation note: uses get_posts() + a direct $wpdb lookup
	 * as fallback. We avoid `new WP_Query( 'name' => $slug )` because
	 * the global $wp_query is in 404 state on these URLs (the CPT is
	 * publicly_queryable=false so WP can't resolve the rewrite's query
	 * vars to a real post), and creating a fresh WP_Query inside that
	 * state was returning null inconsistently. get_posts() does not
	 * touch the global $wp_query.
	 *
	 * @param string $emifree_slug Post slug.
	 * @return WP_Post|null
	 */
	function emifree_query_cpt_blog_post_by_slug( $emifree_slug ) {
		if ( ! $emifree_slug ) {
			return null;
		}

		// Primary path: get_posts() — independent of $wp_query state.
		$emifree_posts = get_posts(
			array(
				'post_type'      => 'blog_post',
				'post_status'    => 'publish',
				'name'           => $emifree_slug,
				'posts_per_page' => 1,
				'no_found_rows'  => true,
				'suppress_filters' => false,
			)
		);
		if ( ! empty( $emifree_posts ) ) {
			return $emifree_posts[0];
		}

		// Fallback: direct $wpdb lookup. Bypasses WP_Query entirely.
		// get_posts() can be filtered by other plugins (relevanssi,
		// polylang, etc.) — this gives us a guaranteed-correct answer
		// for our own internal routing logic.
		global $wpdb;
		$emifree_id = (int) $wpdb->get_var(
			$wpdb->prepare(
				"SELECT ID FROM {$wpdb->posts}
				 WHERE post_type = 'blog_post'
				   AND post_status = 'publish'
				   AND post_name = %s
				 LIMIT 1",
				$emifree_slug
			)
		);
		if ( $emifree_id ) {
			$emifree_post = get_post( $emifree_id );
			if ( $emifree_post && 'blog_post' === $emifree_post->post_type ) {
				return $emifree_post;
			}
		}

		return null;
	}
endif;

if ( ! function_exists( 'emifree_blog_posts_de' ) ) :
	/**
	 * Full-metadata DE blog-post array (legacy PHP-array path).
	 *
	 * Mirrors the EN emifree_blog_posts() shape so that
	 * emifree_get_all_blog_posts_merged( 'de', ... ) can normalize
	 * both languages uniformly. The DE template-part used to inline
	 * the same data; centralizing here keeps the shim + template-part
	 * + merged-feed helper all reading from one source of truth.
	 *
	 * @return array<slug => post>
	 */
	function emifree_blog_posts_de() {
		return array(
			'the-strategic-edge-of-clean-air' => array(
				'id'             => '1',
				'slug'           => 'the-strategic-edge-of-clean-air',
				'title'          => 'Der strategische Vorteil sauberer Luft: Warum Hochleistungs-Ölnebelfiltration für die moderne Zerspanung unverzichtbar ist',
				'excerpt'        => 'Industrielle Ölnebelfiltration ist kein Zubehör, sondern eine strategische Investition in Arbeitssicherheit, Anlagenlebensdauer und Betriebseffizienz in hochpräzisen Fertigungsumgebungen.',
				'category'       => 'Technischer Leitfaden',
				'date'           => '2026-06-29',
				'formatted_date' => '29. Juni 2026',
				'read_time'      => '5 Min. Lesezeit',
				'author'         => 'Victoria Pedroza',
				'author_role'    => 'Produktmanagerin, Emifree GmbH',
				'hero_image'     => 'Workers_operating_CNC_machines.jpeg',
			),
			'precision-in-every-breath' => array(
				'id'             => '2',
				'slug'           => 'precision-in-every-breath',
				'title'          => 'Präzision in jedem Atemzug: Ein technischer Leitfaden zur industriellen Ölnebelfiltration',
				'excerpt'        => 'Ein technischer Vergleich mechanischer und elektrostatischer Ölnebelfiltrationstechnologien – und wie die Absaugung direkt an der Quelle Ihre Mitarbeiter, Ihre Maschinen und Ihr Ergebnis schützt.',
				'category'       => 'Technischer Leitfaden',
				'date'           => '2026-06-29',
				'formatted_date' => '29. Juni 2026',
				'read_time'      => '7 Min. Lesezeit',
				'author'         => 'Victoria Pedroza',
				'author_role'    => 'Produktmanagerin, Emifree GmbH',
				'hero_image'     => 'CNC_2.jpg',
			),
		);
	}
endif;

if ( ! function_exists( 'emifree_get_all_blog_posts_merged' ) ) :
	/**
	 * Return a slug-keyed array of blog posts: legacy PHP-array first,
	 * then CPT entries (filtered by language) appended for any slug
	 * not already present.
	 *
	 * Used by the /blog/ index shims (page-blog.php + page-blog-de.php)
	 * to drive the card grid. CPT results are normalized via
	 * emifree_normalize_post_for_card() so callers see one shape.
	 *
	 * For the German variant we still need the legacy DE metadata
	 * array; since page-blog-de.php defines its own $emifree_de_posts
	 * inline and emifree_blog_posts() only returns EN, we accept a
	 * $emifree_legacy_posts override parameter that callers in DE
	 * shims pass in. Default = emifree_blog_posts() (EN).
	 *
	 * @param string $emifree_lang 'en' or 'de'.
	 * @param array|null $emifree_legacy_posts Optional override for legacy posts (used by DE shim).
	 * @return array<slug => normalized-post> Sorted by date DESC.
	 */
	function emifree_get_all_blog_posts_merged( $emifree_lang = 'en', $emifree_legacy_posts = null ) {
		if ( null === $emifree_legacy_posts ) {
			$emifree_legacy_posts = emifree_blog_posts();
		}

		$emifree_merged = array();
		foreach ( $emifree_legacy_posts as $emifree_slug => $emifree_post ) {
			$emifree_merged[ $emifree_slug ] = emifree_normalize_post_for_card( $emifree_post );
		}

		// Pull CPT entries for this language. CPT entries with no
		// language set are included regardless (useful for drafts /
		// unflagged posts); strict language match otherwise.
		$emifree_cpt_query = new WP_Query(
			array(
				'post_type'      => 'blog_post',
				'post_status'    => 'publish',
				'posts_per_page' => 100,
				'orderby'        => 'date',
				'order'          => 'DESC',
				'no_found_rows'  => true,
				'meta_query'     => array(
					'relation' => 'OR',
					array(
						'key'     => 'emifree_language',
						'value'   => $emifree_lang,
						'compare' => '=',
					),
					array(
						'key'     => 'emifree_language',
						'compare' => 'NOT EXISTS',
					),
				),
			)
		);

		if ( $emifree_cpt_query->have_posts() ) {
			foreach ( $emifree_cpt_query->posts as $emifree_cpt_post ) {
				if ( ! isset( $emifree_merged[ $emifree_cpt_post->post_name ] ) ) {
					$emifree_merged[ $emifree_cpt_post->post_name ] = emifree_normalize_post_for_card( $emifree_cpt_post );
				}
			}
		}
		wp_reset_postdata();

		// Sort by date DESC. We rely on each entry's `date` key
		// (ISO Y-m-d) — both legacy arrays and CPT entries expose
		// this. CPT entries use mysql2date('Y-m-d', post_date) so the
		// comparison is locale-independent.
		uasort(
			$emifree_merged,
			static function ( $emifree_a, $emifree_b ) {
				$emifree_ad = isset( $emifree_a['date'] ) ? (string) $emifree_a['date'] : '';
				$emifree_bd = isset( $emifree_b['date'] ) ? (string) $emifree_b['date'] : '';
				return strcmp( $emifree_bd, $emifree_ad );
			}
		);

		return $emifree_merged;
	}
endif;

