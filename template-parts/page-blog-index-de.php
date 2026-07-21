<?php
/**
 * Blog index (German) — /de/blog/.
 *
 * Hard-coded translation of template-parts/page-blog-index.php. The
 * post metadata + featured card shape come from a German posts array
 * declared inline below (the previous agent's inc/knowledge_de.php
 * DE data file was removed during the homepage i18n refactor — the
 * homepage section-knowledge-de.php has its own inline copy of this
 * data for the 2-up featured cards; this is the index-page variant
 * that uses the 3-up small variant instead).
 *
 * The 3-up small card markup is duplicated inline (mirrors
 * emifree_blog_card() in inc/blog-cards.php) so this template part
 * stays self-contained — no shared helper, no data loader.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$emifree_knowledge_icons = array(
	'book-open'    => '<path d="M12 7v14"></path><path d="M3 18a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1h7a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H4a1 1 0 0 1-1-1z"></path><path d="M21 18a1 1 0 0 0 1-1V4a1 1 0 0 0-1-1h-7a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h7a1 1 0 0 0 1-1z"></path>',
	'calendar'     => '<rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line>',
);

// German blog posts — sourced from emifree_blog_posts_de() in
// inc/knowledge.php (same data the shim uses for the merged feed).
// If the shim resolved a merged DE feed (legacy + CPT), use that
// instead so newly-published CPT entries show up here too.
$emifree_blog_posts = function_exists( 'emifree_blog_posts_de' )
	? emifree_blog_posts_de()
	: array();
if ( isset( $emifree_blog_index_posts_de ) && is_array( $emifree_blog_index_posts_de ) ) {
	$emifree_blog_posts = $emifree_blog_index_posts_de;
}
?>

<div class="min-h-screen bg-white">

	<?php /* ----- Header band ----- */ ?>
	<div class="bg-slate-50 border-b border-slate-200">
		<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-20 pb-16">
			<a href="<?php echo esc_url( home_url( '/de/' ) ); ?>" class="inline-flex items-center gap-2 text-blue-700 hover:text-blue-800 font-medium mb-6 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2 rounded">
				<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 12H5"></path>
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m12 19-7-7 7-7"></path>
				</svg>
				Zurück zur Startseite
			</a>

			<h1 class="text-4xl md:text-5xl font-bold text-zinc-900 mb-4">Emifree Engineering-Blog</h1>
			<p class="text-xl text-zinc-600 max-w-3xl">
				Technische Leitfäden und Praxiseinblicke zur industriellen Ölnebelfiltration, CNC-Luftqualität und EU-Compliance – geschrieben von den Ingenieurinnen und Ingenieuren, die unsere Systeme entwickeln.
			</p>
		</div>
	</div>

	<?php /* ----- Post grid (small BlogCard variant, 3-up) ----- */ ?>
	<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
		<div class="flex items-center gap-2 mb-8 text-zinc-700">
			<svg class="w-5 h-5 text-blue-700" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true">
				<?php echo $emifree_knowledge_icons['book-open']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped — SVG markup, controlled. ?>
			</svg>
			<h2 class="text-2xl font-bold">Alle Artikel</h2>
		</div>

		<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
			<?php
			$emifree_blog_uri = get_template_directory_uri() . '/assets/images/blog/';
			foreach ( $emifree_blog_posts as $emifree_post ) :
				// home_url() preserves the WP install subpath on subpath
				// installs. A bare '/de/blog/...' would drop the subpath
				// on click and 404.
				$emifree_permalink = home_url( '/de/blog/' . ( is_array( $emifree_post ) ? $emifree_post['slug'] : $emifree_post->post_name ) . '/' );
				// Hero image: prefer the explicit `hero_image_url` from
				// the normalized/CPT shape; otherwise resolve the legacy
				// `hero_image` filename against the assets dir.
				if ( is_array( $emifree_post ) && ! empty( $emifree_post['hero_image_url'] ) ) {
					$emifree_hero_src = (string) $emifree_post['hero_image_url'];
				} elseif ( is_array( $emifree_post ) && ! empty( $emifree_post['hero_image'] ) ) {
					$emifree_hero_src = $emifree_blog_uri . $emifree_post['hero_image'];
				} else {
					$emifree_hero_src = '';
				}
				$emifree_hero_alt  = is_array( $emifree_post ) ? $emifree_post['title'] : get_the_title( $emifree_post );
				$emifree_category  = is_array( $emifree_post ) ? ( $emifree_post['category'] ?? '' ) : (string) get_post_meta( $emifree_post->ID, 'emifree_category', true );
				$emifree_formatted = is_array( $emifree_post ) ? ( $emifree_post['formatted_date'] ?? '' ) : mysql2date( get_option( 'date_format' ), $emifree_post->post_date );
				$emifree_read_time = is_array( $emifree_post ) ? ( $emifree_post['read_time'] ?? '' ) : (string) get_post_meta( $emifree_post->ID, 'emifree_read_time', true );
				$emifree_excerpt   = is_array( $emifree_post ) ? ( $emifree_post['excerpt'] ?? '' ) : $emifree_post->post_excerpt;
				?>
				<a
					href="<?php echo esc_url( $emifree_permalink ); ?>"
					class="group relative block bg-white rounded-xl overflow-hidden shadow-sm hover:shadow-lg transition-all duration-300 border border-slate-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2"
					aria-label="<?php echo esc_attr( 'Artikel lesen: ' . $emifree_hero_alt ); ?>"
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
							<?php echo esc_html( $emifree_hero_alt ); ?>
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
			<?php endforeach; ?>
		</div>
	</div>

</div>