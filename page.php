<?php
/**
 * Default page template — fallback for any page that doesn't match a
 * more specific template (page-{slug}.php).
 *
 * In practice, every page we ship uses a page-{slug}.php shim that
 * delegates to a template part (page-impressum.php, page-privacy.php,
 * etc.). This file is the safety net.
 */

get_header();
?>

<main>
	<section class="py-24 bg-white">
		<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="inline-flex items-center gap-2 text-blue-700 hover:text-blue-800 font-medium mb-6 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 rounded">
				<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 12H5"></path>
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m12 19-7-7 7-7"></path>
				</svg>
				Back to home
			</a>

			<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
				<article>
					<h1 class="text-4xl md:text-5xl font-bold text-zinc-900 mb-6"><?php the_title(); ?></h1>
					<div class="text-zinc-700 leading-relaxed text-lg">
						<?php the_content(); ?>
					</div>
				</article>
			<?php endwhile; endif; ?>
		</div>
	</section>
</main>

<?php get_footer();