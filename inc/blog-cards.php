<?php
/**
 * Blog cards — reusable rendering helpers shared across the Knowledge
 * section (Piece 8) and the upcoming /blog/ index + single-post pages
 * (Pieces 15 + 16). Each function expects the post array shape from
 * emifree_blog_posts() in inc/knowledge.php.
 *
 * Mirrors the FeaturedBlogCard component defined inline in
 * src/components/Knowledge.jsx at lines 458–510 of the React app.
 * BlogCard (a separate React component) is intentionally NOT ported —
 * it's dead code in the React source (defined but never rendered). If
 * /blog/ ever needs a smaller list-row variant, add emifree_blog_card()
 * here at that time.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Render a FeaturedBlogCard.
 *
 * Wraps the card in an `<a>` so the entire tile is clickable. The
 * `href` points to /blog/{slug}/, which 404s in this commit but is
 * the route Piece 16 will serve.
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
