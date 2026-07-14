<?php
/**
 * Shared legal-page chrome (header band, breadcrumb, article wrapper,
 * back-to-home footer). Used by Pieces 12-14.
 *
 * Two-phase API so SEO registers BEFORE wp_head fires:
 *
 *   require_once get_template_directory() . '/template-parts/page-legal.php';
 *   emifree_seo_register( 'impressum' );   // phase 1: register wp_head callbacks
 *   get_header();                          // phase 2: wp_head fires; meta lands in <head>
 *   emifree_render_legal_page_body( $slug ); // phase 3: emit the page body
 *   get_footer();
 *
 * The page shim (page-impressum.php, page-privacy.php, page-terms.php)
 * does exactly that. This template part is not auto-loaded by
 * get_template_part(); it's required by the shim explicitly.
 */

require_once get_template_directory() . '/inc/legal.php';
require_once get_template_directory() . '/inc/seo.php';

/**
 * Phase 1: register per-page meta + canonical + JSON-LD against wp_head.
 * Must run before get_header() so the callbacks are in place when
 * wp_head() actually fires.
 */
function emifree_seo_register( $slug, $lang = 'en' ) {
	$emifree_page = emifree_legal_page( $slug, $lang );
	if ( ! $emifree_page ) {
		return;
	}
	emifree_seo_page_with_schema(
		$emifree_page['title'],
		$emifree_page['description'],
		$emifree_page['url'],
		'emifree-legal-' . $lang . '-' . $slug . '-schema',
		$emifree_page['schema']
	);
}

/**
 * Phase 3: emit the page body HTML — header band, breadcrumb, article,
 * back-to-home footer.
 */
function emifree_render_legal_page_body( $slug, $lang = 'en' ) {
	$emifree_page = emifree_legal_page( $slug, $lang );
	if ( ! $emifree_page ) {
		echo '<p class="max-w-3xl mx-auto px-4 py-12 text-zinc-700">Page not found.</p>';
		return;
	}
	$emifree_back_to_home  = ( 'de' === $lang ) ? 'Zur Startseite' : 'Back to home';
	$emifree_back_to_root  = ( 'de' === $lang ) ? home_url( '/de/' ) : home_url( '/' );
	?>
	<div class="min-h-screen bg-white">
		<div class="bg-slate-50 border-b border-slate-200">
			<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 pt-20 pb-12">
				<a href="<?php echo esc_url( $emifree_back_to_root ); ?>" class="inline-flex items-center gap-2 text-blue-700 hover:text-blue-800 font-medium mb-6 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 rounded">
					<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 12H5"></path>
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m12 19-7-7 7-7"></path>
					</svg>
					<?php echo esc_html( $emifree_back_to_home ); ?>
				</a>

				<nav aria-label="breadcrumb" class="mb-6 text-sm text-zinc-500">
					<a href="<?php echo esc_url( $emifree_back_to_root ); ?>" class="hover:text-blue-700">Home</a>
					<span class="mx-2" aria-hidden="true">/</span>
					<span class="text-zinc-700"><?php echo esc_html( $emifree_page['title'] ); ?></span>
				</nav>

				<h1 class="text-3xl md:text-4xl lg:text-5xl font-bold text-zinc-900 leading-tight">
					<?php
					// Strip the " · Emifree GmbH" suffix so the page header
					// matches the React version exactly ("Legal Notice
					// (Impressum)", "Privacy Policy", "General Terms and
					// Conditions (GTC)").
					echo esc_html( str_replace( ' · Emifree GmbH', '', $emifree_page['title'] ) );
					?>
				</h1>
			</div>
		</div>

		<?php
		// Body content. emifree_render_legal_body() returns HTML.
		echo emifree_render_legal_body( $slug, $lang ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped — controlled via internal esc_* helpers.
		?>
	</div>
	<?php
}