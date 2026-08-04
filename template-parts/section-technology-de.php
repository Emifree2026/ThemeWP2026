<?php
/**
 * Technology section — German.
 *
 * Hard-coded translation of section-technology.php. ECO AIR + EARIA
 * selector cards, two Process sections (step-by-step with image +
 * description), and a CTA card routing to inquiry + knowledge.
 *
 * Step switching + mobile/desktop step-list split are handled by
 * assets/js/sections/technology.js — language-agnostic, identical
 * between English and German markup.
 */
emifree_enqueue_section_script( 'technology' );

$emifree_tech_uri = get_template_directory_uri() . '/assets/tech/';

$emifree_tech_icons = array(
	'check'      => '<path d="M21.801 10A10 10 0 1 1 17 3.335"></path><path d="m9 11 3 3L22 4"></path>',
	'move-right' => '<path d="M18 8L22 12L18 16"></path><path d="M2 12H22"></path>',
	'arrow-right'=> '<path d="M5 12h14"></path><path d="m12 5 7 7-7 7"></path>',
);

$emifree_technologies = array(
	'eco-air' => array(
		'badge'           => 'Ideal für Ölnebel & Emulsionen',
		'badge_bg'        => 'bg-amber-100',
		'badge_text'      => 'text-amber-800',
		'title'           => 'ECO AIR CLEANER',
		'subtitle'        => 'Mechanischer Ölnebelabscheider',
		'description'     => 'Konstante Absaugleistung durch selbstreinigende mechanische Filtration und Ölrecycling.',
		'bullets'         => array(
			'Selbstreinigendes System',
			'Keine Filtermatten',
			'Ölrecycling',
			'Bis zu 1.500 m³/h',
		),
		'process_title'   => 'So funktioniert ECO AIR',
		'process_subtitle'=> 'Mechanische Abscheidung + Koaleszenz',
		// initial_step picks which image is visible on first paint.
		// Step order stays logical (capture → separate → clean); the
		// carousel leads with the differentiation moment.
		// Self-Cleaning & Recycling. See inc/technology.php for the
		// English-side comment.
		'initial_step'    => 2,
		'steps'           => array(
			array(
				'title' => 'Vorfiltration',
				'desc'  => 'Ein Edelstahlgeflecht fängt größere Tröpfchen vor der eigentlichen Abscheidung auf.',
				'image' => 'Step1.1_ECOAIR.jpg',
			),
			array(
				'title' => 'Koaleszenz',
				'desc'  => 'Rotierende Trommeln vergrößern feinste Ölpartikel zu größeren Tröpfchen für eine effiziente Abscheidung und Ölrückgewinnung.',
				'image' => 'Step2.1_ECOAIR.webp',
			),
			array(
				'title' => 'Selbstreinigung & Recycling',
				'desc'  => 'Integrierte Sprühdüsen sorgen für konstante Leistung und recyceln das gesammelte Öl.',
				'image' => 'Step3_clean.webp',
			),
		),
		'anchor_id'       => 'technology-eco-air',
	),
	'earia' => array(
		'badge'           => 'Ideal für Rauch & feine Aerosole',
		'badge_bg'        => 'bg-purple-100',
		'badge_text'      => 'text-purple-800',
		'title'           => 'EARIA',
		'subtitle'        => 'Elektrostatisches Filtersystem',
		'description'     => 'Elektrostatische Filtration für Rauch, Ölnebel und feinste Partikel bei minimalem Wartungsaufwand.',
		'bullets'         => array(
			'Erfassung von Rauch & Aerosolen',
			'Elektrostatische Filtration',
			'Kein Kartuschenwechsel',
			'Regelbar bis zu 1.000 m³/h',
		),
		'process_title'   => 'So funktioniert EARIA',
		'process_subtitle'=> 'Elektrostatische Filtration',
		'steps'           => array(
			array(
				'title' => 'Edelstahl-Vorfilter',
				'desc'  => 'Die erste Stufe fängt größere Partikel ab und schützt den Ionisator.',
				'image' => 'Step 1.webp',
			),
			array(
				'title' => 'Ionisation',
				'desc'  => 'Ein Hochspannungs-Ionisator lädt die Partikel elektrostatisch auf.',
				'image' => 'Step 2.webp',
			),
			array(
				'title' => 'Sammelplatten',
				'desc'  => 'Wechselnd angeordnete Platten ziehen die geladenen Partikel an und halten sie fest.',
				'image' => 'Step 3.webp',
			),
			array(
				'title' => 'Ölpartikelsammlung',
				'desc'  => 'Ein selbstreinigender Ablauf entfernt das gesammelte Öl automatisch.',
				'image' => 'Step 4.webp',
			),
		),
		'anchor_id'       => 'technology-earia',
	),
);
?>

<section id="technology" class="py-12 md:py-24 bg-slate-50 md:bg-gradient-to-br md:from-slate-50 md:via-white md:to-blue-50 scroll-mt-20">
	<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

		<?php /* ----- Block 1: Hero / Decision Intro ----- */ ?>
		<div class="text-center mb-20">
			<h2 class="text-4xl md:text-5xl font-bold text-slate-900 mb-6">
				Wählen Sie die richtige <span class="text-blue-700">Filtrationstechnologie</span> für Ihren Prozess
			</h2>
			<p class="text-xl text-slate-600 max-w-3xl mx-auto">
				Ob Ölnebel, Emulsionen, Rauch oder ultrafeine Aerosole – unsere selbstreinigenden Filtersysteme liefern sauberere Luft, geringeren Wartungsaufwand und stabile Leistung.
			</p>
			<p class="text-slate-500 text-sm mt-3">
				Vergleichen Sie die Technologien unten oder springen Sie direkt zur passenden Lösung.
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
						So funktioniert es
						<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true">
							<?php echo $emifree_tech_icons['move-right']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped — SVG markup, controlled. ?>
						</svg>
					</button>
				</div>
			<?php endforeach; ?>
		</div>

		<?php /* ----- Block 3: How It Works ----- */ ?>
		<div class="grid grid-cols-1 lg:grid-cols-2 gap-12 mb-20">
			<?php foreach ( $emifree_technologies as $emifree_t_key => $emifree_t ) :
				$emifree_initial = isset( $emifree_t['initial_step'] ) ? (int) $emifree_t['initial_step'] : 0;
				?>
				<section
					id="<?php echo esc_attr( $emifree_t['anchor_id'] ); ?>"
					data-emifree-process="<?php echo esc_attr( $emifree_t_key ); ?>"
					data-active-step="<?php echo esc_attr( $emifree_initial ); ?>"
					data-emifree-initial-step="<?php echo esc_attr( $emifree_initial ); ?>"
					class="bg-white rounded-3xl shadow-xl border border-slate-100 p-6 md:p-8 scroll-mt-20"
				>
					<h3 class="text-2xl font-bold text-slate-900">
						<?php echo esc_html( $emifree_t['process_title'] ); ?>
					</h3>
					<p class="text-slate-500 text-sm mb-6">
						<?php echo esc_html( $emifree_t['process_subtitle'] ); ?>
					</p>

					<?php /* Desktop step list */ ?>
					<div class="hidden md:flex flex-col gap-2 mb-6" data-emifree-step-list="desktop">
						<?php $emifree_step_index = 0; foreach ( $emifree_t['steps'] as $emifree_step ) : ?>
							<button
								type="button"
								data-emifree-step="<?php echo esc_attr( $emifree_step_index ); ?>"
								data-emifree-step-variant="desktop"
								class="emifree-step-btn-desktop text-left px-4 py-3 rounded-xl transition-all <?php echo $emifree_initial === $emifree_step_index ? 'bg-blue-50 font-semibold text-blue-800' : 'hover:bg-slate-50 text-slate-600'; ?> focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2"
							>
								<span class="text-sm font-medium"><?php echo esc_html( $emifree_step['title'] ); ?></span>
							</button>
						<?php $emifree_step_index++; endforeach; ?>
					</div>

					<?php /* Mobile step list */ ?>
					<div class="md:hidden mb-6 overflow-x-auto whitespace-nowrap flex gap-2 pb-2" style="-webkit-overflow-scrolling: touch;" data-emifree-step-list="mobile">
						<?php $emifree_step_index = 0; foreach ( $emifree_t['steps'] as $emifree_step ) : ?>
							<button
								type="button"
								data-emifree-step="<?php echo esc_attr( $emifree_step_index ); ?>"
								data-emifree-step-variant="mobile"
								class="emifree-step-btn-mobile px-4 py-2 rounded-full text-sm transition flex-shrink-0 <?php echo $emifree_initial === $emifree_step_index ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'; ?> focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2"
							>
								<?php echo esc_html( $emifree_step['title'] ); ?>
							</button>
						<?php $emifree_step_index++; endforeach; ?>
					</div>

					<div class="rounded-2xl overflow-hidden bg-slate-100 h-48 md:h-80 flex items-center justify-center p-4">
						<?php $emifree_step_index = 0; foreach ( $emifree_t['steps'] as $emifree_step ) : ?>
							<img
								src="<?php echo esc_url( $emifree_tech_uri . $emifree_step['image'] ); ?>"
								alt="<?php echo esc_attr( $emifree_step['title'] ); ?>"
								data-emifree-image="<?php echo esc_attr( $emifree_step_index ); ?>"
								class="max-w-full max-h-full w-auto h-auto object-contain <?php echo $emifree_initial === $emifree_step_index ? '' : 'hidden'; ?>"
								loading="lazy"
								decoding="async"
							>
						<?php $emifree_step_index++; endforeach; ?>
					</div>

					<div class="mt-4">
						<?php $emifree_step_index = 0; foreach ( $emifree_t['steps'] as $emifree_step ) : ?>
							<div data-emifree-step-caption="<?php echo esc_attr( $emifree_step_index ); ?>" class="<?php echo $emifree_initial === $emifree_step_index ? '' : 'hidden'; ?>">
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
			<div class="max-w-4xl mx-auto rounded-3xl shadow-xl p-8 md:p-12 text-center bg-blue-50">
				<h3 class="text-3xl md:text-4xl font-bold text-slate-900">
					Nicht sicher, welche Filtrationstechnologie zu Ihrer Anwendung passt?
				</h3>
				<p class="text-slate-700 text-lg mt-4 max-w-2xl mx-auto">
					Nennen Sie uns Ihre Verunreinigungsart, Ihren Luftleistungsbedarf oder Ihren Maschinenaufbau – wir empfehlen die passende Lösung.
				</p>
				<div class="flex flex-col sm:flex-row justify-center gap-4 mt-8">
					<button
						type="button"
						data-emifree-inquiry="technology"
						class="px-6 py-3.5 min-h-[44px] inline-flex items-center justify-center border border-slate-300 bg-white rounded-full font-medium text-slate-800 hover:bg-slate-50 transition focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2"
					>
						Expertenempfehlung anfordern
					</button>
					<a
						href="#knowledge"
						class="px-6 py-3.5 min-h-[44px] inline-flex items-center justify-center rounded-full font-medium text-blue-700 hover:text-blue-800 gap-1 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2"
					>
						Weitere technische Daten anzeigen
						<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true">
							<?php echo $emifree_tech_icons['arrow-right']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped — SVG markup, controlled. ?>
						</svg>
					</a>
				</div>
			</div>
		</div>

	</div>
</section>