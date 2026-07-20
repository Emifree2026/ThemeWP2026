<?php
/**
 * Blog index — /blog/.
 *
 * Mirrors src/pages/Blog.jsx from the React app post-cleanup. Three
 * columns on lg, two on md, one on mobile. Each card links to
 * /blog/{slug}/, which currently 404s (the route ships with Piece 16).
 *
 * No JS behaviors needed for this page (no tabs, no toggle, no
 * smooth-scroll targets within the page itself).
 */

require_once get_template_directory() . '/inc/knowledge.php';
require_once get_template_directory() . '/inc/blog-cards.php';

$emifree_blog_icons = emifree_knowledge_icons();
?>

<div class="min-h-screen bg-white">

	<?php /* ----- Header band ----- */ ?>
	<div class="bg-slate-50 border-b border-slate-200">
		<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-20 pb-16">
			<a href="/" class="inline-flex items-center gap-2 text-blue-700 hover:text-blue-800 font-medium mb-6 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2 rounded">
				<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 12H5"></path>
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m12 19-7-7 7-7"></path>
				</svg>
				Back to home
			</a>

			<h1 class="text-4xl md:text-5xl font-bold text-zinc-900 mb-4">Emifree Engineering Blog</h1>
			<p class="text-xl text-zinc-600 max-w-3xl">
				Technical guides and field insights on industrial oil mist filtration, CNC air quality, and EU regulatory compliance — written by the engineers who build our systems.
			</p>
		</div>
	</div>

	<?php /* ----- Post grid ----- */ ?>
	<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
		<div class="flex items-center gap-2 mb-8 text-zinc-700">
			<svg class="w-5 h-5 text-blue-700" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true">
				<?php echo $emifree_blog_icons['book-open']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped — SVG markup, controlled. ?>
			</svg>
			<h2 class="text-2xl font-bold">All articles</h2>
		</div>

		<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
			<?php
			$emifree_blog_index_posts = function_exists( 'emifree_get_all_blog_posts_merged' )
				? emifree_get_all_blog_posts_merged( 'en' )
				: emifree_blog_posts();
			foreach ( $emifree_blog_index_posts as $emifree_post ) :
				?>
				<?php emifree_blog_card( $emifree_post ); ?>
			<?php endforeach; ?>
		</div>
	</div>

</div>