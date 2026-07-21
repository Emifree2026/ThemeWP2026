<?php
/**
 * Single blog post (German) — /de/blog/{slug}/.
 *
 * Hard-coded translation of template-parts/page-blog-post.php.
 *
 * Article body comes from data/posts/{slug}-de.php via
 * emifree_get_post_body_html_de() (defined alongside this template
 * in page-blog-post-de.php). The slug is read from the
 * emifree_blog_slug query var, populated by the rewrite rule
 * registered in functions.php.
 *
 * The "Read next" suggestion points to whichever other post exists,
 * using the same sorted-by-date helper (emifree_get_all_posts_sorted_de,
 * defined in page-blog-post-de.php).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// page-blog-post-de.php (the shim) resolves $emifree_current_post,
// $emifree_next_post, and $emifree_is_cpt BEFORE require_once-ing
// this template part. We trust those values here so the CPT-first
// lookup happens once per request, in one place. CRITICAL: do NOT
// overwrite $emifree_current_post when the shim has set it — CPT
// slugs aren't in the legacy DE array, so a legacy lookup would
// null-out the variable and trigger the "Artikel nicht gefunden"
// branch below.
$emifree_requested_slug = get_query_var( 'emifree_blog_slug' );

if ( ! isset( $emifree_current_post ) || ! $emifree_current_post ) {
	// Defensive fallback path — only reached when this template part
	// is rendered outside of page-blog-post-de.php.
	if ( $emifree_requested_slug && function_exists( 'emifree_get_post_by_slug_de' ) ) {
		$emifree_current_post = emifree_get_post_by_slug_de( $emifree_requested_slug );
		if ( $emifree_current_post ) {
			$emifree_is_cpt = false;  // legacy lookup, so explicitly not CPT.
		}
	}
}

if ( ! $emifree_current_post ) {
	?>
	<div class="min-h-screen bg-white">
		<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-16 text-center">
			<h1 class="text-3xl font-bold text-zinc-900 mb-4">Artikel nicht gefunden</h1>
			<p class="text-zinc-600 mb-6">Der gesuchte Beitrag existiert nicht oder wurde verschoben.</p>
			<a href="<?php echo esc_url( home_url( '/de/blog/' ) ); ?>" class="inline-flex items-center gap-2 text-blue-700 hover:text-blue-800 font-medium">
				Zurück zu allen Artikeln
			</a>
		</div>
	</div>
	<?php
	return;
}

// "Read next" — only fill in here when the shim didn't already
// provide it. The shim wires $emifree_next_post from either the
// merged DE feed (CPT path) or emifree_get_all_posts_sorted_de()
// (legacy path).
if ( ! isset( $emifree_next_post ) || ! $emifree_next_post ) {
	$emifree_next_post = null;
	if ( function_exists( 'emifree_get_all_posts_sorted_de' ) ) {
		foreach ( emifree_get_all_posts_sorted_de() as $emifree_candidate_slug => $emifree_candidate ) {
			if ( $emifree_candidate_slug !== $emifree_current_post['slug'] ) {
				$emifree_next_post = $emifree_candidate;
				break;
			}
		}
	}
}

// Body HTML — CPT-driven posts use the Gutenberg content;
// legacy DE posts load from data/posts/{slug}-de.php, falling back
// to the English body file when the DE version is missing.
$emifree_is_cpt  = ! empty( $emifree_is_cpt );
$emifree_body_html = '';
if ( $emifree_is_cpt ) {
	$emifree_body_html = apply_filters( 'the_content', $emifree_current_post['body_raw'] ?? '' );
} else {
	if ( function_exists( 'emifree_get_post_body_html_de' ) ) {
		$emifree_body_html = emifree_get_post_body_html_de( $emifree_current_post['slug'] );
	}
	if ( '' === $emifree_body_html && function_exists( 'emifree_get_post_body_html' ) ) {
		$emifree_body_html = emifree_get_post_body_html( $emifree_current_post['slug'] );
	}
}
?>

<div class="min-h-screen bg-white">

	<?php /* ----- Header band ----- */ ?>
	<div class="bg-slate-50 border-b border-slate-200">
		<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 pt-20 pb-12">
			<a href="<?php echo esc_url( home_url( '/de/' ) ); ?>" class="inline-flex items-center gap-2 text-blue-700 hover:text-blue-800 font-medium mb-6 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2 rounded">
				<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 12H5"></path>
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m12 19-7-7 7-7"></path>
				</svg>
				Zurück zur Startseite
			</a>

			<nav aria-label="Brotkrumen" class="mb-6 text-sm text-zinc-500">
				<a href="<?php echo esc_url( home_url( '/de/' ) ); ?>" class="hover:text-blue-700">Startseite</a>
				<span class="mx-2" aria-hidden="true">/</span>
				<a href="<?php echo esc_url( home_url( '/de/blog/' ) ); ?>" class="hover:text-blue-700">Blog</a>
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
		<link itemprop="url" href="<?php echo esc_url( home_url( '/de/blog/' . $emifree_current_post['slug'] ) ); ?>">

		<div class="prose text-zinc-700">
			<?php
			if ( $emifree_is_cpt ) {
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
				<a href="<?php echo esc_url( home_url( '/de/blog/' ) ); ?>" class="inline-flex items-center gap-2 text-blue-700 hover:text-blue-800 font-medium focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2 rounded">
					<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 12H5"></path>
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m12 19-7-7 7-7"></path>
					</svg>
					Zurück zu allen Artikeln
				</a>
			</div>

			<?php if ( $emifree_next_post ) : ?>
				<a href="<?php echo esc_url( home_url( '/de/blog/' . $emifree_next_post['slug'] . '/' ) ); ?>" class="group block bg-slate-50 hover:bg-blue-50 border border-slate-200 hover:border-blue-200 rounded-2xl p-6 transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2">
					<p class="text-xs font-semibold uppercase tracking-wider text-blue-700 mb-2">Als Nächstes lesen</p>
					<p class="text-lg font-bold text-zinc-900 group-hover:text-blue-800 leading-snug">
						<?php echo esc_html( $emifree_next_post['title'] ); ?>
					</p>
					<p class="text-sm text-zinc-600 mt-2 line-clamp-2"><?php echo esc_html( $emifree_next_post['excerpt'] ); ?></p>
					<span class="inline-flex items-center gap-1 mt-4 text-sm font-medium text-blue-700 group-hover:gap-2 transition-all">
						Weiterlesen
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