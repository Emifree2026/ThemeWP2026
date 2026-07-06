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
 * Render a FeaturedBlogCard (large variant).
 *
 * Wraps the card in an `<a>` so the entire tile is clickable. The
 * `href` points to /blog/{slug}/, which currently 404s; the route
 * ships with Piece 16.
 *
 * @param array $emifree_post Post array from emifree_blog_posts().
 */
function emifree_featured_blog_card( $emifree_post ) {
	if ( empty( $emifree_post ) || empty( $emifree_post['slug'] ) ) {
		return;
	}

	$emifree_blog_uri    = get_template_directory_uri() . '/assets/images/blog/';
	$emifree_icons       = emifree_knowledge_icons();
	$emifree_permalink   = '/blog/' . $emifree_post['slug'] . '/';
	$emifree_hero_src    = $emifree_blog_uri . $emifree_post['hero_image'];
	$emifree_hero_alt    = $emifree_post['title'];
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
			<span class="absolute top-4 left-4 bg-amber-500 text-white px-3 py-1 rounded-full text-sm font-semibold">
				<?php echo esc_html( $emifree_post['category'] ); ?>
			</span>
		</div>

		<div class="p-6">
			<div class="flex items-center gap-4 text-sm text-slate-500 mb-3">
				<span class="inline-flex items-center gap-1">
					<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true">
						<?php echo $emifree_icons['calendar']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped — SVG markup, controlled. ?>
					</svg>
					<?php echo esc_html( $emifree_post['formatted_date'] ); ?>
				</span>
				<span class="inline-flex items-center gap-1">
					<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true">
						<?php echo $emifree_icons['clock']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped — SVG markup, controlled. ?>
					</svg>
					<?php echo esc_html( $emifree_post['read_time'] ); ?>
				</span>
			</div>

			<h3 class="text-xl font-bold text-zinc-900 mb-3 group-hover:text-blue-700 transition-colors">
				<?php echo esc_html( $emifree_post['title'] ); ?>
			</h3>

			<p class="text-slate-600 mb-4">
				<?php echo esc_html( $emifree_post['excerpt'] ); ?>
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
 * @param array $emifree_post Post array from emifree_blog_posts().
 */
function emifree_blog_card( $emifree_post ) {
	if ( empty( $emifree_post ) || empty( $emifree_post['slug'] ) ) {
		return;
	}

	$emifree_blog_uri  = get_template_directory_uri() . '/assets/images/blog/';
	$emifree_permalink = '/blog/' . $emifree_post['slug'] . '/';
	$emifree_hero_src  = $emifree_blog_uri . $emifree_post['hero_image'];
	$emifree_hero_alt  = $emifree_post['title'];
	?>

	<a
		href="<?php echo esc_url( $emifree_permalink ); ?>"
		class="group relative block bg-white rounded-xl overflow-hidden shadow-sm hover:shadow-lg transition-all duration-300 border border-slate-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2"
		aria-label="<?php echo esc_attr( 'Read article: ' . $emifree_post['title'] ); ?>"
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
				<?php echo esc_html( $emifree_post['category'] ); ?>
			</span>

			<h3 class="text-lg font-bold text-zinc-900 mt-3 mb-2 line-clamp-2 group-hover:text-blue-700 transition-colors">
				<?php echo esc_html( $emifree_post['title'] ); ?>
			</h3>

			<p class="text-sm text-slate-600 mb-4 line-clamp-2">
				<?php echo esc_html( $emifree_post['excerpt'] ); ?>
			</p>

			<div class="flex items-center justify-between text-xs text-slate-500">
				<span><?php echo esc_html( $emifree_post['formatted_date'] ); ?></span>
				<span><?php echo esc_html( $emifree_post['read_time'] ); ?></span>
			</div>
		</div>
	</a>

	<?php
}
