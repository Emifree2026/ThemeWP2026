<?php
/**
 * Footer — full markup mirroring src/components/Footer.jsx post-cleanup.
 *
 * - Newsletter signup (visual + form structure; real AJAX wiring lands
 *   with the Contact section in Piece 9. For now the form's onsubmit
 *   just prevents reload so the page doesn't jump.)
 * - Three link columns (Company / Resources / Legal) read from
 *   inc/footer.php so the data is editable in one place.
 * - Emifree GmbH legal line at the bottom (matches the React Footer).
 * - Social icons (LinkedIn, Email) with SVG paths.
 * - No gradient text on the heading (the React cleanup).
 */

require_once get_template_directory() . '/inc/footer.php';
?>

<footer class="bg-zinc-900 text-white">
	<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

		<!-- Newsletter -->
		<div class="py-16 border-b border-zinc-800">
			<div class="max-w-4xl mx-auto text-center">
				<h3 class="text-3xl md:text-4xl font-bold mb-4">
					Stay Updated with <span class="text-blue-400">Emifree</span>
				</h3>
				<p class="text-xl text-zinc-400 mb-8">
					Get the latest updates, tips, and insights delivered to your inbox.
				</p>

				<form
					id="emifree-newsletter-form"
					class="max-w-md mx-auto"
					onsubmit="event.preventDefault();"
					novalidate
				>
					<div class="flex flex-col sm:flex-row gap-4">
						<div class="flex-1">
							<input
								type="email"
								name="email"
								placeholder="Enter your email"
								required
								aria-label="Email address"
								class="w-full px-6 py-4 bg-zinc-800 border-2 border-zinc-700 rounded-full text-white placeholder-zinc-500 focus:outline-none focus:ring-0 focus:border-blue-500 transition-all duration-200"
							>
						</div>
						<button
							type="submit"
							class="bg-gradient-to-r from-blue-600 to-cyan-500 text-white px-8 py-4 rounded-full font-semibold hover:shadow-xl transition-all duration-200 inline-flex items-center justify-center"
						>
							Subscribe
							<svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
							</svg>
						</button>
					</div>
				</form>
			</div>
		</div>

		<!-- Main Footer Content -->
		<div class="py-16">
			<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-8">

				<!-- Brand section (col-span-2) -->
				<div class="lg:col-span-2">
					<h2 class="text-3xl font-bold text-blue-400 font-mono mb-4">
						Emifree
					</h2>
					<p class="text-zinc-400 mb-6 leading-relaxed">
						Choose the Right Filtration Technology for Your Process.
					</p>

					<!-- Social Links -->
					<div class="flex space-x-4">
						<?php foreach ( emifree_social_links() as $emifree_social ) : ?>
							<a
								href="<?php echo esc_url( $emifree_social['href'] ); ?>"
								target="_blank"
								rel="noopener noreferrer"
								class="w-10 h-10 bg-zinc-800 rounded-full flex items-center justify-center hover:bg-blue-600 transition-all duration-200"
								aria-label="<?php echo esc_attr( $emifree_social['name'] ); ?>"
							>
								<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
									<path d="<?php echo esc_attr( $emifree_social['svg'] ); ?>"></path>
								</svg>
							</a>
						<?php endforeach; ?>
					</div>
				</div>

				<!-- Link Columns (1 each, fills the remaining 3 cols of the 5-col grid) -->
				<?php foreach ( emifree_footer_links() as $emifree_col_label => $emifree_col_links ) : ?>
					<div>
						<h4 class="font-semibold text-white mb-4">
							<?php echo esc_html( $emifree_col_label ); ?>
						</h4>
						<ul class="space-y-3">
							<?php foreach ( $emifree_col_links as $emifree_link ) : ?>
								<li>
									<a
										href="<?php echo esc_url( $emifree_link['href'] ); ?>"
										class="text-zinc-400 hover:text-white transition-colors duration-200"
									>
										<?php echo esc_html( $emifree_link['name'] ); ?>
									</a>
								</li>
							<?php endforeach; ?>
						</ul>
					</div>
				<?php endforeach; ?>
			</div>
		</div>

		<!-- Bottom Section -->
		<div class="py-8 border-t border-zinc-800 flex flex-col md:flex-row items-center justify-between gap-4">
			<div class="text-zinc-400 text-sm">
				© <?php echo esc_html( gmdate( 'Y' ) ); ?> Emifree GmbH · Pestalozzistraße 13 · 12557 Berlin · Germany
			</div>
			<div class="flex items-center space-x-4 text-zinc-500 text-sm">
				<a href="mailto:info@emifree.com" class="hover:text-white transition-colors">info@emifree.com</a>
				<span aria-hidden="true">·</span>
				<a href="tel:+493076283520" class="hover:text-white transition-colors">+49 307 628 3520</a>
			</div>
		</div>

	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>