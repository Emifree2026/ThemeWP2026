<?php
/**
 * Applications section — 6 industrial segments with icons + descriptions + SEO questions.
 *
 * Mirrors src/components/Applications.jsx from the React app.
 * Icon SVGs come from emifree_application_icons() in inc/applications.php
 * — no external icon library required for WordPress.
 */

require_once get_template_directory() . '/inc/applications.php';
?>

<section id="applications" class="py-12 md:py-24 bg-white">
	<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

		<div class="text-center mb-20">
			<h2 class="text-4xl md:text-5xl font-bold text-zinc-900 mb-6">
				Industrial Air Filtration for Every Application
			</h2>
			<p class="text-xl text-zinc-600 max-w-3xl mx-auto">
				From small tool shops to large manufacturing facilities — Emifree
				systems capture oil mist, welding fumes, and dust at the source.
			</p>
		</div>

		<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
			<?php foreach ( emifree_applications() as $emifree_app ) : ?>
				<article class="group relative bg-white rounded-2xl p-8 shadow-lg hover:shadow-2xl transition-all duration-300 border border-slate-100 hover:border-slate-200">
					<div class="relative">
						<div class="w-16 h-16 rounded-2xl bg-gradient-to-br <?php echo esc_attr( $emifree_app['color'] ); ?> flex items-center justify-center mb-6 transition-transform duration-300 group-hover:scale-110">
							<svg class="w-8 h-8" fill="none" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true">
								<?php
								$emifree_icons = emifree_application_icons();
								echo $emifree_icons[ $emifree_app['icon'] ]; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped — SVG markup, controlled input.
								?>
							</svg>
						</div>

						<h3 class="text-xl font-bold text-zinc-900 mb-4 transition-colors duration-300 group-hover:text-blue-700">
							<?php echo esc_html( $emifree_app['title'] ); ?>
						</h3>

						<p class="text-zinc-600 leading-relaxed mb-4">
							<?php echo esc_html( $emifree_app['description'] ); ?>
						</p>

						<!-- SEO question — surfaced as a subtle italic line. -->
						<p class="text-sm text-zinc-400 italic">
							&ldquo;<?php echo esc_html( $emifree_app['question'] ); ?>&rdquo;
						</p>
					</div>
				</article>
			<?php endforeach; ?>
		</div>

		<div class="text-center mt-16">
			<a
				href="#technology"
				class="bg-gradient-to-r from-blue-700 to-cyan-500 text-white px-8 py-4 rounded-full font-semibold text-lg hover:shadow-xl transition-all duration-300 inline-block"
			>
				Find Your Solution
			</a>
		</div>

	</div>
</section>