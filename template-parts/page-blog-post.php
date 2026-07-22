<?php
/**
 * Single blog post — /blog/{slug}/.
 *
 * Mirrors src/pages/BlogPost.jsx from the React app. The rendered
 * body comes from data/posts/{slug}.php via emifree_get_post_body_html()
 * and is sanitized via wp_kses_post() before echo. The "Read next"
 * card points to whichever other post exists (with only 2 posts,
 * the suggestion is trivially the other one).
 *
 * Reads the slug from the `emifree_blog_slug` query var populated
 * by the rewrite rule in functions.php. If the slug is unknown the
 * page-blog-post.php shim already 404'd, but a defensive fallback
 * renders an inline "Article not found" message.
 *
 * No JS behaviors needed for this page (no tabs, no toggle).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once get_template_directory() . '/inc/knowledge.php';

$emifree_requested_slug = get_query_var( 'emifree_blog_slug' );

// The shim (page-blog-post.php) resolves $emifree_current_post (with
// $emifree_is_cpt set for CPT-driven posts) BEFORE require_once-ing
// this template part. Trust those values when present. If the shim
// didn't set $emifree_current_post (e.g. template-part rendered
// standalone, or the shim 404'd), fall back to a legacy-array lookup
// as a defensive measure. CRITICAL: we MUST NOT overwrite the shim's
// CPT-shaped $emifree_current_post with a legacy-array lookup —
// CPT slugs aren't in emifree_blog_posts(), so overwriting would
// null-out the variable and trigger the "Article not found" branch.
if ( ! isset( $emifree_current_post ) || ! $emifree_current_post ) {
	$emifree_current_post = $emifree_requested_slug ? emifree_get_post_by_slug( $emifree_requested_slug ) : null;
	if ( $emifree_current_post ) {
		$emifree_is_cpt = false;  // legacy lookup, so explicitly not CPT.
	}
}

if ( ! $emifree_current_post ) {
	// Defensive fallback — the shim should have already 404'd.
	?>
	<div class="min-h-screen bg-white">
		<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-16 text-center">
			<h1 class="text-3xl font-bold text-zinc-900 mb-4">Article not found</h1>
			<p class="text-zinc-600 mb-6">The post you're looking for doesn't exist or has been moved.</p>
			<a href="<?php echo esc_url( home_url( '/blog/' ) ); ?>" class="inline-flex items-center gap-2 text-blue-700 hover:text-blue-800 font-medium">
				Back to all articles
			</a>
		</div>
	</div>
	<?php
	return;
}

// "Read next" — any post that isn't the current one.
$emifree_next_post = null;
foreach ( emifree_get_all_posts_sorted() as $emifree_candidate_slug => $emifree_candidate ) {
	if ( $emifree_candidate_slug !== $emifree_current_post['slug'] ) {
		$emifree_next_post = $emifree_candidate;
		break;
	}
}

$emifree_body_html = ! empty( $emifree_is_cpt )
	? apply_filters( 'the_content', $emifree_current_post['body_raw'] ?? '' )
	: emifree_get_post_body_html( $emifree_current_post['slug'] );
?>

<div class="min-h-screen bg-white">

	<?php /* ----- Header band ----- */ ?>
	<div class="bg-slate-50 border-b border-slate-200">
		<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 pt-20 pb-12">
			<a href="<?php echo esc_url( home_url( '/en/' ) ); ?>" class="inline-flex items-center gap-2 text-blue-700 hover:text-blue-800 font-medium mb-6 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2 rounded">
				<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 12H5"></path>
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m12 19-7-7 7-7"></path>
				</svg>
				Back to home
			</a>

			<nav aria-label="breadcrumb" class="mb-6 text-sm text-zinc-500">
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="hover:text-blue-700">Home</a>
				<span class="mx-2" aria-hidden="true">/</span>
				<a href="<?php echo esc_url( home_url( '/blog/' ) ); ?>" class="hover:text-blue-700">Blog</a>
				<span class="mx-2" aria-hidden="true">/</span>
				<span class="text-zinc-700"><?php echo esc_html( $emifree_current_post['category'] ); ?></span>
			</nav>

			<h1 class="text-3xl md:text-4xl lg:text-5xl font-bold text-zinc-900 leading-tight mb-6">
				<?php echo esc_html( $emifree_current_post['title'] ); ?>
			</h1>

			<div class="flex flex-wrap items-center gap-x-5 gap-y-2 text-sm text-zinc-600">
				<span class="inline-flex items-center gap-1.5">
					<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true">
						<circle cx="12" cy="12" r="10"></circle>
						<circle cx="12" cy="12" r="3"></circle>
					</svg>
					<span>
						<span class="font-semibold text-zinc-900"><?php echo esc_html( $emifree_current_post['author'] ); ?></span>
						<span class="text-zinc-500"> &middot; <?php echo esc_html( $emifree_current_post['author_role'] ); ?></span>
					</span>
				</span>
				<span class="inline-flex items-center gap-1.5">
					<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true">
						<rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
						<line x1="16" y1="2" x2="16" y2="6"></line>
						<line x1="8" y1="2" x2="8" y2="6"></line>
						<line x1="3" y1="10" x2="21" y2="10"></line>
					</svg>
					<time datetime="<?php echo esc_attr( $emifree_current_post['date'] ); ?>">
						<?php echo esc_html( $emifree_current_post['formatted_date'] ); ?>
					</time>
				</span>
				<span class="inline-flex items-center gap-1.5">
					<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true">
						<circle cx="12" cy="12" r="10"></circle>
						<polyline points="12 6 12 12 16 14"></polyline>
					</svg>
					<?php echo esc_html( $emifree_current_post['read_time'] ); ?>
				</span>
				<span class="inline-block px-2.5 py-0.5 bg-blue-50 text-blue-700 rounded-full font-medium">
					<?php echo esc_html( $emifree_current_post['category'] ); ?>
				</span>
			</div>
		</div>
	</div>

	<?php /* ----- Article body ----- */ ?>
	<article class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-12" itemscope itemtype="https://schema.org/BlogPosting">
		<meta itemprop="datePublished" content="<?php echo esc_attr( $emifree_current_post['date'] ); ?>">
		<meta itemprop="author" content="<?php echo esc_attr( $emifree_current_post['author'] ); ?>">
		<link itemprop="url" href="<?php echo esc_url( home_url( '/blog/' . $emifree_current_post['slug'] ) ); ?>">

		<div class="prose text-zinc-700">
			<?php
			if ( ! empty( $emifree_is_cpt ) ) {
				// Gutenberg content — already sanitized through 'the_content' filter chain.
				echo $emifree_body_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped — sanitized via the_content filter.
			} else {
				echo wp_kses_post( $emifree_body_html ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped — sanitized via wp_kses_post.
			}
			?>
		</div>

		<?php /* ----- Article footer (back-to-all + read-next) ----- */ ?>
		<div class="mt-16 pt-8 border-t border-slate-200">
			<div class="flex items-center justify-between mb-8">
				<a href="<?php echo esc_url( home_url( '/blog/' ) ); ?>" class="inline-flex items-center gap-2 text-blue-700 hover:text-blue-800 font-medium focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2 rounded">
					<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 12H5"></path>
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m12 19-7-7 7-7"></path>
					</svg>
					Back to all articles
				</a>
			</div>

			<?php if ( $emifree_next_post ) : ?>
				<a href="<?php echo esc_url( home_url( '/blog/' . $emifree_next_post['slug'] . '/' ) ); ?>" class="group block bg-slate-50 hover:bg-blue-50 border border-slate-200 hover:border-blue-200 rounded-2xl p-6 transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2">
					<p class="text-xs font-semibold uppercase tracking-wider text-blue-700 mb-2">Read next</p>
					<p class="text-lg font-bold text-zinc-900 group-hover:text-blue-800 leading-snug">
						<?php echo esc_html( $emifree_next_post['title'] ); ?>
					</p>
					<p class="text-sm text-zinc-600 mt-2 line-clamp-2"><?php echo esc_html( $emifree_next_post['excerpt'] ); ?></p>
					<span class="inline-flex items-center gap-1 mt-4 text-sm font-medium text-blue-700 group-hover:gap-2 transition-all">
						Continue reading
						<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14"></path>
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m12 5 7 7-7 7"></path>
						</svg>
					</span>
				</a>
			<?php endif; ?>
		</div>
	</article>

</div>