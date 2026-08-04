<?php
/**
 * Blog cards — reusable rendering helpers shared across the Knowledge
 * section (Piece 8) and the /blog/ index (Piece 15) + the upcoming
 * /blog/{slug}/ single-post pages (Piece 16). Each function expects
 * the post array shape from emifree_blog_posts() in inc/knowledge.php.
 *
 * Two card variants:
 *  - emifree_featured_blog_card(): the large, 2-up card used by the
 *    homepage Knowledge section (Piece 8).
 *  - emifree_blog_card(): the smaller, 3-up card used by the /blog/
 *    index (Piece 15). Mirrors the React's BlogCard defined in
 *    src/components/Knowledge.jsx at lines 516–558.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Resolve the hero image URL for a post passed to a card.
 *
 * Cards accept either:
 *   - legacy PHP-array shape (with `hero_image` FILENAME key)
 *   - normalized shape (with `hero_image_url` URL key)
 *   - raw WP_Post
 *
 * The normalized and WP_Post paths delegate to the shared helper in
 * inc/knowledge.php. The legacy path preserves byte-equivalent output
 * by re-applying the same {template_uri}/assets/images/blog/{filename}
 * join.
 *
 * @param array|WP_Post $emifree_post
 * @return string Hero image URL, or empty string when no image.
 */
function emifree_blog_card_hero_src( $emifree_post ) {
	if ( is_array( $emifree_post ) && ! empty( $emifree_post['hero_image_url'] ) ) {
		return (string) $emifree_post['hero_image_url'];
	}
	if ( is_array( $emifree_post ) && ! empty( $emifree_post['hero_image'] ) ) {
		return get_template_directory_uri() . '/assets/images/blog/' . $emifree_post['hero_image'];
	}
	if ( function_exists( 'emifree_get_post_hero_image_url' ) ) {
		return emifree_get_post_hero_image_url( $emifree_post );
	}
	return '';
}

/**
 * Render a FeaturedBlogCard (large variant).
 *
 * Wraps the card in an `<a>` so the entire tile is clickable. The
 * `href` points to /blog/{slug}/, which currently 404s; the route
 * ships with Piece 16.
 *
 * Accepts both the legacy PHP-array shape (with `hero_image`
 * filename) and the normalized shape (with `hero_image_url`).
 *
 * @param array|WP_Post $emifree_post Post array or WP_Post.
 */
function emifree_featured_blog_card( $emifree_post ) {
	if ( empty( $emifree_post ) || ( is_array( $emifree_post ) && empty( $emifree_post['slug'] ) ) ) {
		return;
	}

	$emifree_icons     = emifree_knowledge_icons();
	// home_url() preserves the WP install subpath (e.g. '/wordpress/...'
	// on a subpath install, '/' on a root install). A bare '/blog/...'
	// would drop the subpath on click and 404.
	$emifree_permalink = home_url( '/blog/' . ( is_array( $emifree_post ) ? $emifree_post['slug'] : $emifree_post->post_name ) . '/' );
	$emifree_hero_src  = emifree_blog_card_hero_src( $emifree_post );
	$emifree_title     = is_array( $emifree_post ) ? $emifree_post['title'] : get_the_title( $emifree_post );
	$emifree_hero_alt  = $emifree_title;
	$emifree_category  = is_array( $emifree_post ) ? ( $emifree_post['category'] ?? '' ) : (string) get_post_meta( $emifree_post->ID, 'emifree_category', true );
	$emifree_formatted = is_array( $emifree_post ) ? ( $emifree_post['formatted_date'] ?? '' ) : mysql2date( get_option( 'date_format' ), $emifree_post->post_date );
	$emifree_read_time = is_array( $emifree_post ) ? ( $emifree_post['read_time'] ?? '' ) : (string) get_post_meta( $emifree_post->ID, 'emifree_read_time', true );
	$emifree_excerpt   = is_array( $emifree_post ) ? ( $emifree_post['excerpt'] ?? '' ) : $emifree_post->post_excerpt;
	?>

	<a
		href="<?php echo esc_url( $emifree_permalink ); ?>"
		class="group block bg-white rounded-3xl overflow-hidden shadow-lg hover:shadow-xl transition-all duration-300 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2"
	>
		<div class="aspect-video bg-gradient-to-br from-blue-100 to-cyan-100 relative overflow-hidden">
			<img
				src="<?php echo esc_url( $emifree_hero_src ); ?>"
				alt="<?php echo esc_attr( $emifree_hero_alt ); ?>"
				class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
				loading="lazy"
				decoding="async"
				width="1280"
				height="720"
			>
			<span class="absolute top-4 left-4 bg-amber-500 text-zinc-900 px-3 py-1 rounded-full text-sm font-semibold">
				<?php echo esc_html( $emifree_category ); ?>
			</span>
		</div>

		<div class="p-6">
			<div class="flex items-center gap-4 text-sm text-slate-500 mb-3">
				<span class="inline-flex items-center gap-1">
					<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true">
						<?php echo $emifree_icons['calendar']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped — SVG markup, controlled. ?>
					</svg>
					<?php echo esc_html( $emifree_formatted ); ?>
				</span>
				<span class="inline-flex items-center gap-1">
					<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true">
						<?php echo $emifree_icons['clock']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped — SVG markup, controlled. ?>
					</svg>
					<?php echo esc_html( $emifree_read_time ); ?>
				</span>
			</div>

			<h3 class="text-xl font-bold text-zinc-900 mb-3 group-hover:text-blue-700 transition-colors">
				<?php echo esc_html( $emifree_title ); ?>
			</h3>

			<p class="text-slate-600 mb-4">
				<?php echo esc_html( $emifree_excerpt ); ?>
			</p>

			<span class="inline-flex items-center gap-1 text-blue-700 font-medium">
				Read article
				<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true">
					<?php echo $emifree_icons['chevron-right']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped — SVG markup, controlled. ?>
				</svg>
			</span>
		</div>
	</a>

	<?php
}

/**
 * Render a BlogCard (small variant for the /blog/ index grid).
 *
 * Compact card with a smaller hero image (h-40 instead of aspect-video),
 * a category text badge below the image (instead of an overlay chip),
 * and a simplified footer (date + read time, no icons). Title and
 * excerpt are clamped to 2 lines via line-clamp-2 so the grid stays
 * uniform regardless of post length.
 *
 * Accepts both the legacy PHP-array shape (with `hero_image`
 * filename) and the normalized shape (with `hero_image_url`).
 *
 * @param array|WP_Post $emifree_post Post array or WP_Post.
 */
function emifree_blog_card( $emifree_post ) {
	if ( empty( $emifree_post ) || ( is_array( $emifree_post ) && empty( $emifree_post['slug'] ) ) ) {
		return;
	}

	// See note in emifree_featured_blog_card() above — home_url() keeps
	// the WP install subpath in the href, which a bare '/blog/...' would
	// lose on subpath installs like /wordpress/.
	$emifree_permalink = home_url( '/blog/' . ( is_array( $emifree_post ) ? $emifree_post['slug'] : $emifree_post->post_name ) . '/' );
	$emifree_hero_src  = emifree_blog_card_hero_src( $emifree_post );
	$emifree_title     = is_array( $emifree_post ) ? $emifree_post['title'] : get_the_title( $emifree_post );
	$emifree_hero_alt  = $emifree_title;
	$emifree_category  = is_array( $emifree_post ) ? ( $emifree_post['category'] ?? '' ) : (string) get_post_meta( $emifree_post->ID, 'emifree_category', true );
	$emifree_formatted = is_array( $emifree_post ) ? ( $emifree_post['formatted_date'] ?? '' ) : mysql2date( get_option( 'date_format' ), $emifree_post->post_date );
	$emifree_read_time = is_array( $emifree_post ) ? ( $emifree_post['read_time'] ?? '' ) : (string) get_post_meta( $emifree_post->ID, 'emifree_read_time', true );
	$emifree_excerpt   = is_array( $emifree_post ) ? ( $emifree_post['excerpt'] ?? '' ) : $emifree_post->post_excerpt;
	?>

	<a
		href="<?php echo esc_url( $emifree_permalink ); ?>"
		class="group relative block bg-white rounded-xl overflow-hidden shadow-sm hover:shadow-lg transition-all duration-300 border border-slate-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2"
		aria-label="<?php echo esc_attr( 'Read article: ' . $emifree_title ); ?>"
	>
		<div class="h-40 bg-gradient-to-br from-slate-100 to-blue-50 relative overflow-hidden">
			<img
				src="<?php echo esc_url( $emifree_hero_src ); ?>"
				alt="<?php echo esc_attr( $emifree_hero_alt ); ?>"
				class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
				loading="lazy"
				decoding="async"
				width="1280"
				height="720"
			>
		</div>

		<div class="p-5">
			<span class="text-xs font-semibold text-blue-700 bg-blue-50 px-2 py-1 rounded-full">
				<?php echo esc_html( $emifree_category ); ?>
			</span>

			<h3 class="text-lg font-bold text-zinc-900 mt-3 mb-2 line-clamp-2 group-hover:text-blue-700 transition-colors">
				<?php echo esc_html( $emifree_title ); ?>
			</h3>

			<p class="text-sm text-slate-600 mb-4 line-clamp-2">
				<?php echo esc_html( $emifree_excerpt ); ?>
			</p>

			<div class="flex items-center justify-between text-xs text-slate-500">
				<span><?php echo esc_html( $emifree_formatted ); ?></span>
				<span><?php echo esc_html( $emifree_read_time ); ?></span>
			</div>
		</div>
	</a>

	<?php
}
