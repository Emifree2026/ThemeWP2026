<?php
/**
 * Technology section — ECO AIR + EARIA selector cards, two Process
 * sections (step-by-step with image + description), and a CTA card
 * routing to inquiry + knowledge. Mirrors src/components/Technology.jsx
 * from the React app.
 *
 * Step switching + mobile/desktop step-list split + smooth-scroll for
 * the in-section "Learn How It Works" anchors + inquiry CustomEvent
 * are handled by assets/js/sections/technology.js, enqueued in
 * functions.php when this section template is loaded.
 */

require_once get_template_directory() . '/inc/technology.php';
emifree_enqueue_section_script( 'technology' );

$emifree_tech_uri = get_template_directory_uri() . '/assets/tech/';
$emifree_tech_icons = emifree_technology_icons();
$emifree_technologies = emifree_technologies();
?>

<section id="technology" class="py-24 bg-gradient-to-br from-slate-50 via-white to-blue-50 scroll-mt-20">
	<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

		<?php /* ----- Block 1: Hero / Decision Intro ----- */ ?>
		<div class="text-center mb-20">
			<h2 class="text-4xl md:text-5xl font-bold text-slate-900 mb-6">
				Choose the Right <span class="text-blue-700">Filtration Technology</span> for Your Process
			</h2>
			<p class="text-xl text-slate-600 max-w-3xl mx-auto">
				Whether you handle oil mist, emulsions, smoke, or ultra-fine aerosols — our self-cleaning
				filtration systems deliver cleaner air, lower maintenance, and stable performance.
			</p>
			<p class="text-slate-400 text-sm mt-3">
				Compare technologies below or jump directly to the right solution.
			</p>
		</div>

		<?php /* ----- Block 2: Technology Selector Cards ----- */ ?>
		<div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-20">
			<?php foreach ( $emifree_technologies as $emifree_t_key => $emifree_t ) : ?>
				<div class="bg-white rounded-3xl shadow-xl hover:shadow-2xl transition-all duration-300 p-6 md:p-8 border border-slate-100">
					<span class="inline-block px-3 py-1 rounded-full <?php echo esc_attr( $emifree_t['badge_bg'] ); ?> <?php echo esc_attr( $emifree_t['badge_text'] ); ?> text-xs font-semibold mb-4">
						<?php echo esc_html( $emifree_t['badge'] ); ?>
					</span>
					<h3 class="text-2xl md:text-3xl font-bold text-slate-900">
						<?php echo esc_html( $emifree_t['title'] ); ?>
					</h3>
					<p class="text-slate-500 text-sm mt-1">
						<?php echo esc_html( $emifree_t['subtitle'] ); ?>
					</p>
					<p class="text-slate-600 mt-4">
						<?php echo esc_html( $emifree_t['description'] ); ?>
					</p>
					<ul class="mt-5 space-y-2">
						<?php foreach ( $emifree_t['bullets'] as $emifree_bullet ) : ?>
							<li class="flex items-center gap-2 text-slate-700">
								<svg class="w-[18px] h-[18px] text-emerald-600 flex-shrink-0" fill="none" stroke="#10b981" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true">
									<?php echo $emifree_tech_icons['check']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped — SVG markup, controlled. ?>
								</svg>
								<span><?php echo esc_html( $emifree_bullet ); ?></span>
							</li>
						<?php endforeach; ?>
					</ul>
					<button
						type="button"
						data-emifree-tech-anchor="<?php echo esc_attr( $emifree_t['anchor_id'] ); ?>"
						class="mt-6 text-blue-600 font-medium flex items-center gap-1 hover:gap-2 transition-all focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2"
					>
						Learn How It Works
						<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true">
							<?php echo $emifree_tech_icons['move-right']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped — SVG markup, controlled. ?>
						</svg>
					</button>
				</div>
			<?php endforeach; ?>
		</div>

		<?php /* ----- Block 3: How It Works — side by side ProcessSections ----- */ ?>
		<div class="grid grid-cols-1 lg:grid-cols-2 gap-12 mb-20">
			<?php foreach ( $emifree_technologies as $emifree_t_key => $emifree_t ) : ?>
				<section
					id="<?php echo esc_attr( $emifree_t['anchor_id'] ); ?>"
					data-emifree-process="<?php echo esc_attr( $emifree_t_key ); ?>"
					data-active-step="0"
					class="bg-white rounded-3xl shadow-xl border border-slate-100 p-6 md:p-8 scroll-mt-20"
				>
					<h3 class="text-2xl font-bold text-slate-900">
						<?php echo esc_html( $emifree_t['process_title'] ); ?>
					</h3>
					<p class="text-slate-500 text-sm mb-6">
						<?php echo esc_html( $emifree_t['process_subtitle'] ); ?>
					</p>

					<?php /* Desktop step list — visible by default, hidden below md */ ?>
					<div class="hidden md:flex flex-col gap-2 mb-6" data-emifree-step-list="desktop">
						<?php $emifree_step_index = 0; foreach ( $emifree_t['steps'] as $emifree_step ) : ?>
							<button
								type="button"
								data-emifree-step="<?php echo esc_attr( $emifree_step_index ); ?>"
								data-emifree-step-variant="desktop"
								class="emifree-step-btn-desktop text-left px-4 py-3 rounded-xl transition-all <?php echo 0 === $emifree_step_index ? 'bg-blue-50 font-semibold text-blue-800' : 'hover:bg-slate-50 text-slate-600'; ?> focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2"
							>
								<span class="text-sm font-medium"><?php echo esc_html( $emifree_step['title'] ); ?></span>
							</button>
						<?php $emifree_step_index++; endforeach; ?>
					</div>

					<?php /* Mobile step list — pills, horizontal scroll; hidden at md+ */ ?>
					<div class="md:hidden mb-6 overflow-x-auto whitespace-nowrap flex gap-2 pb-2" data-emifree-step-list="mobile">
						<?php $emifree_step_index = 0; foreach ( $emifree_t['steps'] as $emifree_step ) : ?>
							<button
								type="button"
								data-emifree-step="<?php echo esc_attr( $emifree_step_index ); ?>"
								data-emifree-step-variant="mobile"
								class="emifree-step-btn-mobile px-4 py-2 rounded-full text-sm transition flex-shrink-0 <?php echo 0 === $emifree_step_index ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'; ?> focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2"
							>
								<?php echo esc_html( $emifree_step['title'] ); ?>
							</button>
						<?php $emifree_step_index++; endforeach; ?>
					</div>

					<?php /* Step images — only the active one is visible; JS toggles the .hidden class */ ?>
					<div class="rounded-2xl overflow-hidden bg-slate-100 h-64 md:h-80 flex items-center justify-center p-4">
						<?php $emifree_step_index = 0; foreach ( $emifree_t['steps'] as $emifree_step ) : ?>
							<img
								src="<?php echo esc_url( $emifree_tech_uri . $emifree_step['image'] ); ?>"
								alt="<?php echo esc_attr( $emifree_step['title'] ); ?>"
								data-emifree-image="<?php echo esc_attr( $emifree_step_index ); ?>"
								class="max-w-full max-h-full w-auto h-auto object-contain <?php echo 0 === $emifree_step_index ? '' : 'hidden'; ?>"
								loading="lazy"
								decoding="async"
							>
						<?php $emifree_step_index++; endforeach; ?>
					</div>

					<?php /* Step captions — only the active one is visible */ ?>
					<div class="mt-4">
						<?php $emifree_step_index = 0; foreach ( $emifree_t['steps'] as $emifree_step ) : ?>
							<div data-emifree-step-caption="<?php echo esc_attr( $emifree_step_index ); ?>" class="<?php echo 0 === $emifree_step_index ? '' : 'hidden'; ?>">
								<h4 class="text-lg font-semibold text-slate-900"><?php echo esc_html( $emifree_step['title'] ); ?></h4>
								<p class="text-slate-600 mt-1"><?php echo esc_html( $emifree_step['desc'] ); ?></p>
							</div>
						<?php $emifree_step_index++; endforeach; ?>
					</div>
				</section>
			<?php endforeach; ?>
		</div>

		<?php /* ----- Block 4: CTA Section ----- */ ?>
		<div class="px-0 py-0">
			<div class="max-w-4xl mx-auto bg-gradient-to-br from-blue-50 to-slate-100 rounded-3xl shadow-xl p-8 md:p-12 text-center">
				<h3 class="text-3xl md:text-4xl font-bold text-slate-900">
					Not sure which filtration technology fits your application?
				</h3>
				<p class="text-slate-700 text-lg mt-4 max-w-2xl mx-auto">
					Tell us your contamination type, airflow requirements, or machine setup — we'll recommend the right solution.
				</p>
				<div class="flex flex-col sm:flex-row justify-center gap-4 mt-8">
					<button
						type="button"
						data-emifree-inquiry="technology"
						class="px-6 py-3 border border-slate-300 bg-white rounded-full font-medium text-slate-800 hover:bg-slate-50 transition focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2"
					>
						Get expert recommendation
					</button>
					<a
						href="#knowledge"
						class="px-6 py-3 rounded-full font-medium text-blue-700 hover:text-blue-800 inline-flex items-center justify-center gap-1 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2"
					>
						View more Technical Specs
						<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true">
							<?php echo $emifree_tech_icons['arrow-right']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped — SVG markup, controlled. ?>
						</svg>
					</a>
				</div>
			</div>
		</div>

	</div>
</section>
