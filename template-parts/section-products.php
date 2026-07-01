<?php
/**
 * Products section — Mechanical / Electrostatic / Dust tabs with image
 * gallery, specs table, features grid, applications, and a per-product
 * inquiry CTA. Mirrors src/components/Products.jsx from the React app.
 *
 * Tab switching + active-image cycling inside a tab is handled by
 * assets/js/sections/products.js, which is enqueued in functions.php
 * when this section template is loaded.
 */

require_once get_template_directory() . '/inc/products.php';
emifree_enqueue_section_script( 'products' );
?>

<section id="products" class="py-12 md:py-24 bg-slate-50">
	<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

		<div class="text-center mb-16">
			<h2 class="text-4xl md:text-5xl font-bold text-zinc-900 mb-6">
				Industrial Air Filtration Product Range
			</h2>
			<p class="text-xl text-zinc-600 max-w-3xl mx-auto">
				Professional-grade filtration systems designed for CNC workshops,
				metalworking shops, and industrial manufacturing environments.
			</p>
		</div>

		<!-- Tabs (Mechanical / Electrostatic / Dust) -->
		<div class="flex flex-wrap justify-center gap-4 mb-12" role="tablist" aria-label="Product lines">
			<?php $emifree_first = true; foreach ( emifree_products() as $emifree_key => $emifree_product ) : ?>
				<button
					type="button"
					role="tab"
					id="emifree-tab-<?php echo esc_attr( $emifree_key ); ?>"
					aria-selected="<?php echo $emifree_first ? 'true' : 'false'; ?>"
					aria-controls="emifree-panel-<?php echo esc_attr( $emifree_key ); ?>"
					data-emifree-tab="<?php echo esc_attr( $emifree_key ); ?>"
					class="emifree-product-tab px-8 py-4 rounded-full font-semibold transition-all duration-300 flex items-center gap-2 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2 <?php echo $emifree_first ? 'bg-blue-700 text-white shadow-lg' : 'bg-white text-zinc-600 hover:bg-slate-100 hover:text-blue-700 border border-slate-200'; ?>"
				>
					<?php
					// Tiny inline SVG glyph per tab — purely decorative, the
					// accessible name comes from the button text below.
					$emifree_tab_icons = array(
						'mechanical'    => '<svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="3"></circle><path d="M19 12a7 7 0 0 1-7 7"></path></svg>',
						'electrostatic' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon></svg>',
						'dust'          => '<svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path></svg>',
					);
					echo $emifree_tab_icons[ $emifree_key ]; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped — SVG markup, controlled.
					?>
					<?php echo esc_html( $emifree_product['name'] ); ?>
				</button>
			<?php $emifree_first = false; endforeach; ?>
		</div>

		<!-- Panels -->
		<?php $emifree_first = true; foreach ( emifree_products() as $emifree_key => $emifree_product ) : ?>
			<div
				role="tabpanel"
				id="emifree-panel-<?php echo esc_attr( $emifree_key ); ?>"
				aria-labelledby="emifree-tab-<?php echo esc_attr( $emifree_key ); ?>"
				data-emifree-panel="<?php echo esc_attr( $emifree_key ); ?>"
				class="<?php echo $emifree_first ? '' : 'hidden'; ?> emifree-product-panel"
			>

				<div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-start">

					<!-- Image Gallery (left column) -->
					<div class="space-y-4" data-emifree-gallery="<?php echo esc_attr( $emifree_key ); ?>">
						<div class="relative bg-white rounded-2xl overflow-hidden shadow-lg aspect-[4/3] flex items-center justify-center">
							<?php $emifree_img_index = 0; foreach ( $emifree_product['images'] as $emifree_img ) : ?>
								<img
									src="<?php echo esc_url( get_template_directory_uri() . '/assets/products/' . $emifree_img ); ?>"
									alt="<?php echo esc_attr( $emifree_product['name'] ); ?> — view <?php echo esc_attr( $emifree_img_index + 1 ); ?>"
									class="absolute inset-0 w-full h-full object-contain p-6 <?php echo 0 === $emifree_img_index ? '' : 'hidden'; ?>"
									data-emifree-image="<?php echo esc_attr( $emifree_img_index ); ?>"
									loading="lazy"
									decoding="async"
								>
							<?php $emifree_img_index++; endforeach; ?>
						</div>

						<!-- Thumbnails -->
						<div class="flex gap-4 justify-center">
							<?php $emifree_img_index = 0; foreach ( $emifree_product['images'] as $emifree_img ) : ?>
								<button
									type="button"
									data-emifree-thumb="<?php echo esc_attr( $emifree_img_index ); ?>"
									class="emifree-product-thumb relative rounded-xl overflow-hidden shadow-md transition-all duration-300 flex-shrink-0 <?php echo 0 === $emifree_img_index ? 'ring-2 ring-blue-700 ring-offset-2 scale-105' : 'opacity-70 hover:opacity-100'; ?>"
									aria-label="View image <?php echo esc_attr( $emifree_img_index + 1 ); ?>"
								>
									<div class="w-28 h-28 bg-white p-2">
										<img
											src="<?php echo esc_url( get_template_directory_uri() . '/assets/products/' . $emifree_img ); ?>"
											alt=""
											class="w-full h-full object-contain"
											loading="lazy"
											decoding="async"
										>
									</div>
								</button>
							<?php $emifree_img_index++; endforeach; ?>
						</div>
					</div>

					<!-- Product info (right column) -->
					<div class="space-y-6">
						<div>
							<h3 class="text-3xl font-bold text-zinc-900 mb-2">
								<?php echo esc_html( $emifree_product['tagline'] ); ?>
							</h3>
							<p class="text-lg text-zinc-600">
								<?php echo esc_html( $emifree_product['short_desc'] ); ?>
							</p>
						</div>

						<p class="text-zinc-600 leading-relaxed">
							<?php echo esc_html( $emifree_product['description'] ); ?>
						</p>

						<!-- Applications -->
						<div>
							<h4 class="font-semibold text-zinc-900 mb-3">Applications:</h4>
							<div class="flex flex-wrap gap-2">
								<?php foreach ( $emifree_product['applications'] as $emifree_app ) : ?>
									<span class="px-4 py-2 bg-cyan-50 text-cyan-700 text-sm font-medium rounded-full">
										<?php echo esc_html( $emifree_app ); ?>
									</span>
								<?php endforeach; ?>
							</div>
						</div>

						<!-- Features (4-tile grid) -->
						<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
							<?php
							$emifree_icons = emifree_product_icons();
							foreach ( $emifree_product['features'] as $emifree_feature ) :
								?>
								<div class="bg-white p-4 rounded-xl shadow-sm border border-slate-100">
									<div class="flex items-start gap-3">
										<div class="p-2 bg-blue-100 rounded-lg">
											<svg class="w-5 h-5" fill="none" stroke="#1d4ed8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true">
												<?php echo $emifree_icons[ $emifree_feature['icon'] ]; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped — SVG markup, controlled. ?>
											</svg>
										</div>
										<div>
											<h5 class="font-semibold text-zinc-900 text-sm">
												<?php echo esc_html( $emifree_feature['title'] ); ?>
											</h5>
											<p class="text-zinc-500 text-xs mt-1">
												<?php echo esc_html( $emifree_feature['desc'] ); ?>
											</p>
										</div>
									</div>
								</div>
							<?php endforeach; ?>
						</div>

						<!-- Inquiry CTA — opens the inquiry modal (Piece 10) -->
						<button
							type="button"
							data-emifree-inquiry="<?php echo esc_attr( $emifree_key ); ?>"
							class="emifree-open-inquiry w-full bg-gradient-to-r from-blue-700 to-cyan-500 text-white px-8 py-4 rounded-full font-semibold text-lg hover:shadow-xl transition-all duration-300 flex items-center justify-center gap-2 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2"
						>
							<?php echo esc_html( $emifree_product['cta'] ); ?>
							<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 18l6-6-6-6"></path>
							</svg>
						</button>
					</div>
				</div>
			</div>
		<?php $emifree_first = false; endforeach; ?>

	</div>
</section>