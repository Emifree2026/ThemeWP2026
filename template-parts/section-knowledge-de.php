<?php
/**
 * Knowledge section — German.
 *
 * Hard-coded translation of section-knowledge.php. Three tabs
 * (Brancheneinblicke / Über uns / Downloads) with two featured
 * blog articles, a "Über uns" panel, and a catalog download grid.
 *
 * Tab switching is handled by assets/js/sections/knowledge.js.
 * The JS is language-agnostic.
 */
emifree_enqueue_section_script( 'knowledge' );

// Icon map — identical SVG paths to the English version, inlined so
// the German template is self-contained.
$emifree_knowledge_icons = array(
	'book-open'      => '<path d="M12 7v14"></path><path d="M3 18a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1h7a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H4a1 1 0 0 1-1-1z"></path><path d="M21 18a1 1 0 0 0 1-1V4a1 1 0 0 0-1-1h-7a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h7a1 1 0 0 0 1-1z"></path>',
	'users'          => '<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M22 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path>',
	'download'       => '<path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line>',
	'award'          => '<circle cx="12" cy="8" r="6"></circle><path d="M15.477 12.89 17 22l-5-3-5 3 1.523-9.11"></path>',
	'book-marked'    => '<path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H19a1 1 0 0 1 1 1v18a1 1 0 0 1-1 1H6.5a1 1 0 0 1 0-5H20"></path><polyline points="9 10 11 12 13 10"></polyline>',
	'leaf'           => '<path d="M11 20A7 7 0 0 1 9.8 6.1C15.5 5 17 4.48 19.2 2.96c1.4 1 2 5.04 2 7.04 0 5.52-4.48 10-10 10Z"></path><path d="M2 21c0-3 1.85-5.36 5.08-6"></path>',
	'shield'         => '<path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"></path>',
	'settings'       => '<path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"></path><circle cx="12" cy="12" r="3"></circle>',
	'target'         => '<circle cx="12" cy="12" r="10"></circle><circle cx="12" cy="12" r="6"></circle><circle cx="12" cy="12" r="2"></circle>',
	'file-text'      => '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><line x1="10" y1="9" x2="8" y2="9"></line>',
	'chevron-right'  => '<path d="m9 18 6-6-6-6"></path>',
	'arrow-right'    => '<path d="M5 12h14"></path><path d="m12 5 7 7-7 7"></path>',
	'calendar'       => '<rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line>',
	'clock'          => '<circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline>',
);

// German blog posts — inlined to keep the section self-contained.
$emifree_blog_posts = array(
	'the-strategic-edge-of-clean-air' => array(
		'id'            => '1',
		'slug'          => 'the-strategic-edge-of-clean-air',
		'title'         => 'Der strategische Vorteil sauberer Luft: Warum Hochleistungs-Ölnebelfiltration für die moderne Zerspanung unverzichtbar ist',
		'excerpt'       => 'Industrielle Ölnebelfiltration ist kein Zubehör, sondern eine strategische Investition in Arbeitssicherheit, Anlagenlebensdauer und Betriebseffizienz in hochpräzisen Fertigungsumgebungen.',
		'category'      => 'Technischer Leitfaden',
		'date'          => '2026-06-29',
		'formatted_date'=> '29. Juni 2026',
		'read_time'     => '5 Min. Lesezeit',
		'author'        => 'Victoria Pedroza',
		'author_role'   => 'Produktmanagerin, Emifree GmbH',
		'hero_image'    => 'Factory_floor_with_CNC_.webp',
		'body_preview'  => 'In der modernen Präzisionsfertigung ist die Luftqualität in der Fabrikhalle längst kein Nebenthema mehr. Die Luft in einer Werkstatt beeinflusst direkt die Zuverlässigkeit der Anlagen, die Einhaltung gesetzlicher Vorschriften und – am wichtigsten – die Gesundheit der Mitarbeiter. Ein industrieller Ölnebelabscheider, der an jeder Werkzeugmaschine positioniert ist, stellt eine strategische Investition dar, kein Zubehör.',
	),
	'precision-in-every-breath' => array(
		'id'            => '2',
		'slug'          => 'precision-in-every-breath',
		'title'         => 'Präzision in jedem Atemzug: Ein technischer Leitfaden zur industriellen Ölnebelfiltration',
		'excerpt'       => 'Ein technischer Vergleich mechanischer und elektrostatischer Ölnebelfiltrationstechnologien – und wie die Absaugung direkt an der Quelle Ihre Mitarbeiter, Ihre Maschinen und Ihr Ergebnis schützt.',
		'category'      => 'Technischer Leitfaden',
		'date'          => '2026-06-29',
		'formatted_date'=> '29. Juni 2026',
		'read_time'     => '7 Min. Lesezeit',
		'author'        => 'Victoria Pedroza',
		'author_role'   => 'Produktmanagerin, Emifree GmbH',
		'hero_image'    => 'CNC_2.jpg',
		'body_preview'  => 'Für Facility Manager und Produktionsingenieure ist die Luftqualität in der Fabrik eine betriebliche Notwendigkeit – kein bloßer Haken auf der Checkliste. Der bei Hochgeschwindigkeitsbearbeitung, Schleifen und Drehen entstehende Ölnebel wirkt sich messbar auf die Gesundheit der Belegschaft, die Maschinenverfügbarkeit und das Ergebnis aus. Dieser Artikel erläutert, wie Ölnebel entsteht, warum die Filtration direkt an der Quelle entscheidend ist, und wie Sie zwischen mechanischer und elektrostatischer Abscheidung wählen.',
	),
);

// German catalog PDFs — same shape as English, with German catalog entries.
$emifree_catalog_uri = get_template_directory_uri() . '/assets/catalog/';
$emifree_catalog_pdfs = array(
	array(
		'name'      => 'ECO AIR Cleaner Katalog (Englisch)',
		'filename'  => 'emifree_eco_air_cleaner_katalog_ENG_v4.2.pdf',
		'size'      => '2.1 MB',
		'lang'      => 'EN',
		'available' => true,
		'url'       => $emifree_catalog_uri . 'emifree_eco_air_cleaner_katalog_ENG_v4.2.pdf',
	),
	array(
		'name'      => 'ECO AIR Cleaner Katalog',
		'filename'  => 'emifree_eco_air_cleaner_katalog_DE_v4.2.pdf',
		'size'      => '2.1 MB',
		'lang'      => 'DE',
		'available' => true,
		'url'       => $emifree_catalog_uri . 'emifree_eco_air_cleaner_katalog_DE_v4.2.pdf',
	),
	array(
		'name'      => 'EARIA Elektrostatik-Katalog (Englisch)',
		'filename'  => 'earia-catalog-en.pdf',
		'size'      => '3.8 MB',
		'lang'      => 'EN',
		'available' => false,
		'url'       => '',
	),
	array(
		'name'      => 'Gesamtes Produktsortiment 2026',
		'filename'  => 'full-range-2026.pdf',
		'size'      => '12.5 MB',
		'lang'      => 'EN',
		'available' => false,
		'url'       => '',
	),
);

// German tab config.
$emifree_knowledge_tabs = array(
	'blog'      => array(
		'label' => 'Brancheneinblicke',
		'icon'  => 'book-open',
	),
	'about'     => array(
		'label' => 'Über uns',
		'icon'  => 'users',
	),
	'downloads' => array(
		'label' => 'Downloads',
		'icon'  => 'download',
	),
);
?>

<section id="knowledge" class="py-12 md:py-24 bg-white scroll-mt-20">
	<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

		<div class="text-center mb-16">
			<h2 class="text-4xl md:text-5xl font-bold text-zinc-900 mb-6">
				Ressourcen &amp; <span class="text-blue-700">Wissen</span>
			</h2>
			<p class="text-xl text-slate-600 max-w-3xl mx-auto">
				Erkunden Sie unsere neuesten Einblicke, Unternehmensupdates und technischen Ressourcen, um über industrielle Luftfiltration informiert zu bleiben.
			</p>
		</div>

		<div class="flex flex-wrap justify-center gap-4 mb-12" role="tablist" aria-label="Wissenszentrum-Bereiche">
			<?php $emifree_ktab_first = true; foreach ( $emifree_knowledge_tabs as $emifree_ktab_key => $emifree_ktab ) : ?>
				<button
					type="button"
					role="tab"
					id="emifree-tab-<?php echo esc_attr( $emifree_ktab_key ); ?>"
					aria-selected="<?php echo $emifree_ktab_first ? 'true' : 'false'; ?>"
					aria-controls="emifree-panel-<?php echo esc_attr( $emifree_ktab_key ); ?>"
					data-emifree-tab="<?php echo esc_attr( $emifree_ktab_key ); ?>"
					class="emifree-knowledge-tab px-8 py-4 rounded-full font-semibold transition-all duration-300 flex items-center gap-2 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2 <?php echo $emifree_ktab_first ? 'bg-blue-700 text-white shadow-lg' : 'bg-white text-slate-600 hover:bg-slate-100 hover:text-blue-700 border border-slate-200'; ?>"
				>
					<svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true">
						<?php echo $emifree_knowledge_icons[ $emifree_ktab['icon'] ]; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped — SVG markup, controlled. ?>
					</svg>
					<?php echo esc_html( $emifree_ktab['label'] ); ?>
				</button>
			<?php $emifree_ktab_first = false; endforeach; ?>
		</div>

		<?php /* ----- Panel A: Industry Insights (default visible) ----- */ ?>
		<div
			role="tabpanel"
			id="emifree-panel-blog"
			aria-labelledby="emifree-tab-blog"
			data-emifree-panel="blog"
			class="emifree-knowledge-panel"
		>
			<h3 class="text-2xl md:text-3xl font-bold text-zinc-900 flex items-center gap-3 mb-8">
				<svg class="w-7 h-7 text-amber-500" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true">
					<?php echo $emifree_knowledge_icons['award']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped — SVG markup, controlled. ?>
				</svg>
				Empfohlene Artikel
			</h3>

			<div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
				<?php
				// Inline German FeaturedBlogCard (mirrors inc/blog-cards.php
				// emifree_featured_blog_card() but with German hardcoded
				// strings for the "Read article" link).
				$emifree_blog_uri = get_template_directory_uri() . '/assets/images/blog/';
				foreach ( $emifree_blog_posts as $emifree_post ) :
					// Point to /de/blog/{slug}/ — the German blog shim.
					$emifree_permalink = '/de/blog/' . $emifree_post['slug'] . '/';
					$emifree_hero_src  = $emifree_blog_uri . $emifree_post['hero_image'];
					$emifree_hero_alt  = $emifree_post['title'];
					?>
					<a
						href="<?php echo esc_url( $emifree_permalink ); ?>"
						class="group block bg-white rounded-3xl overflow-hidden shadow-lg hover:shadow-xl transition-all duration-300 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2"
					>
						<div class="aspect-video bg-gradient-to-br from-blue-100 to-cyan-100 relative overflow-hidden">
							<img
								src="<?php echo esc_url( $emifree_hero_src ); ?>"
								alt="<?php echo esc_attr( $emifree_hero_alt ); ?>"
								class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
								loading="lazy"
								decoding="async"
								width="1280"
								height="720"
							>
							<span class="absolute top-4 left-4 bg-amber-500 text-white px-3 py-1 rounded-full text-sm font-semibold">
								<?php echo esc_html( $emifree_post['category'] ); ?>
							</span>
						</div>

						<div class="p-6">
							<div class="flex items-center gap-4 text-sm text-slate-500 mb-3">
								<span class="inline-flex items-center gap-1">
									<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true">
										<?php echo $emifree_knowledge_icons['calendar']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
									</svg>
									<?php echo esc_html( $emifree_post['formatted_date'] ); ?>
								</span>
								<span class="inline-flex items-center gap-1">
									<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true">
										<?php echo $emifree_knowledge_icons['clock']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
									</svg>
									<?php echo esc_html( $emifree_post['read_time'] ); ?>
								</span>
							</div>

							<h3 class="text-xl font-bold text-zinc-900 mb-3 group-hover:text-blue-700 transition-colors">
								<?php echo esc_html( $emifree_post['title'] ); ?>
							</h3>

							<p class="text-slate-600 mb-4">
								<?php echo esc_html( $emifree_post['excerpt'] ); ?>
							</p>

							<span class="inline-flex items-center gap-1 text-blue-700 font-medium">
								Artikel lesen
								<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true">
									<?php echo $emifree_knowledge_icons['chevron-right']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
								</svg>
							</span>
						</div>
					</a>
				<?php endforeach; ?>
			</div>

			<div class="text-center">
				<a
					href="/de/blog"
					class="inline-flex items-center gap-2 bg-white border-2 border-blue-700 text-blue-700 px-8 py-3 rounded-full font-semibold hover:bg-blue-700 hover:text-white transition-all duration-300 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2"
				>
					Alle Artikel anzeigen
					<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true">
						<?php echo $emifree_knowledge_icons['arrow-right']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</svg>
				</a>
			</div>
		</div>

		<?php /* ----- Panel B: Über uns ----- */ ?>
		<div
			role="tabpanel"
			id="emifree-panel-about"
			aria-labelledby="emifree-tab-about"
			data-emifree-panel="about"
			class="hidden emifree-knowledge-panel"
		>
			<?php /* Unsere Geschichte — 2-column grid */ ?>
			<div class="grid grid-cols-1 lg:grid-cols-2 gap-12 mb-16 items-center">
				<div>
					<h3 class="text-2xl md:text-3xl font-bold text-zinc-900 flex items-center gap-3 mb-6">
						<svg class="w-7 h-7 text-blue-700" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true">
							<?php echo $emifree_knowledge_icons['book-marked']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</svg>
						Unsere Geschichte
					</h3>
					<p class="text-lg text-slate-600 mb-6 leading-relaxed">
						Die 2010 in Berlin gegründete Emifree GmbH entstand aus der Vision, Filtrationslösungen zu entwickeln, die industrielle Leistungsfähigkeit mit ökologischer Verantwortung verbinden. Was als kleines Engineering-Team mit Fokus auf CNC-Bearbeitungsanwendungen begann, ist heute ein weltweit führender Anbieter von Luftfiltrationstechnologie.
					</p>

					<div class="grid grid-cols-3 gap-6">
						<?php
						$emifree_stats = array(
							array( 'value' => '2010',  'label' => 'Gegründet' ),
							array( 'value' => '500+',  'label' => 'Kunden weltweit' ),
							array( 'value' => '15+',   'label' => 'Länder' ),
						);
						foreach ( $emifree_stats as $emifree_stat ) :
							?>
							<div class="text-center">
								<div class="text-3xl font-bold text-blue-700 mb-1"><?php echo esc_html( $emifree_stat['value'] ); ?></div>
								<div class="text-sm text-slate-600"><?php echo esc_html( $emifree_stat['label'] ); ?></div>
							</div>
						<?php endforeach; ?>
					</div>
				</div>

				<div class="bg-gradient-to-br from-blue-100 to-cyan-50 rounded-3xl p-8 flex flex-col items-center justify-center aspect-video">
					<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/icons/berlin.png' ); ?>" alt="Standort Berlin" class="w-20 h-20 object-contain">
					<p class="text-lg font-semibold text-zinc-900 mt-4">Mit Sitz in Berlin, Deutschland</p>
					<p class="text-sm text-slate-600">Bedient die Industrie weltweit seit 2010</p>
				</div>
			</div>

			<?php /* Unsere Mission — 4 value cards */ ?>
			<div class="mb-16 text-center">
				<h3 class="text-2xl md:text-3xl font-bold text-zinc-900 mb-4">Unsere Mission</h3>
				<p class="text-lg text-slate-600 max-w-3xl mx-auto mb-12">
					Wir glauben, dass saubere Luft ein Grundrecht an jedem Arbeitsplatz ist. Unsere Mission ist die Entwicklung innovativer Filtrationstechnologien, die Mitarbeiter schützen, die Umweltbelastung reduzieren und Industrien helfen, nachhaltiger zu arbeiten.
				</p>

				<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 text-left">
					<?php
					$emifree_values = array(
						array(
							'icon'  => 'leaf',
							'title' => 'Nachhaltigkeit',
							'desc'  => 'Entwicklung von Produkten, die den ökologischen Fußabdruck minimieren',
						),
						array(
							'icon'  => 'shield',
							'title' => 'Sicherheit zuerst',
							'desc'  => 'Schutz der Gesundheit der Mitarbeiter durch bewährte Filtrationseffizienz',
						),
						array(
							'icon'  => 'settings',
							'title' => 'Innovation',
							'desc'  => 'Kontinuierliche Verbesserung unserer Technologie und Prozesse',
						),
						array(
							'icon'  => 'target',
							'title' => 'Zuverlässigkeit',
							'desc'  => 'Konstante Leistung, auf die sich unsere Kunden verlassen können',
						),
					);
					foreach ( $emifree_values as $emifree_value ) :
						?>
						<div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 hover:shadow-lg transition-all duration-300">
							<div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center mb-4">
								<svg class="w-6 h-6 text-blue-700" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true">
									<?php echo $emifree_knowledge_icons[ $emifree_value['icon'] ]; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
								</svg>
							</div>
							<h4 class="font-semibold text-zinc-900 mb-2"><?php echo esc_html( $emifree_value['title'] ); ?></h4>
							<p class="text-sm text-slate-600"><?php echo esc_html( $emifree_value['desc'] ); ?></p>
						</div>
					<?php endforeach; ?>
				</div>
			</div>

			<?php /* Vertraut von Branchenführern — client strip */ ?>
			<div class="bg-slate-100 rounded-3xl p-8 text-center">
				<h3 class="text-2xl md:text-3xl font-bold text-zinc-900 mb-6">Vertraut von Branchenführern</h3>
				<div class="flex flex-wrap justify-center gap-x-8 gap-y-3 items-center">
					<?php
					$emifree_clients = array(
						'Mercedes-Benz',
						'BMW',
						'General Motors',
						'NSK Bearings',
						'Knorr-Bremse',
						'Siemens',
						'Bosch',
						'ThyssenKrupp',
					);
					foreach ( $emifree_clients as $emifree_client_name ) :
						?>
						<span class="text-lg font-semibold text-slate-500 hover:text-slate-800 transition-colors duration-200">
							<?php echo esc_html( $emifree_client_name ); ?>
						</span>
					<?php endforeach; ?>
				</div>
			</div>
		</div>

		<?php /* ----- Panel C: Downloads ----- */ ?>
		<div
			role="tabpanel"
			id="emifree-panel-downloads"
			aria-labelledby="emifree-tab-downloads"
			data-emifree-panel="downloads"
			class="hidden emifree-knowledge-panel"
		>
			<h3 class="text-2xl md:text-3xl font-bold text-zinc-900 flex items-center gap-3 mb-8">
				<svg class="w-7 h-7 text-blue-700" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true">
					<?php echo $emifree_knowledge_icons['book-open']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</svg>
				Produktbroschüren
			</h3>

			<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
				<?php foreach ( $emifree_catalog_pdfs as $emifree_pdf ) :
					$emifree_has_link = ! empty( $emifree_pdf['available'] ) && ! empty( $emifree_pdf['url'] );
					$emifree_open    = $emifree_has_link
						? '<a href="' . esc_url( $emifree_pdf['url'] ) . '" download class="bg-white rounded-xl p-6 shadow-sm border border-slate-100 hover:shadow-lg hover:border-blue-200 transition-all duration-300 group block focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2">'
						: '<div class="bg-white rounded-xl p-6 shadow-sm border border-slate-100 opacity-60 cursor-not-allowed" aria-disabled="true">';
					$emifree_close   = $emifree_has_link ? '</a>' : '</div>';
					echo $emifree_open; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped — controlled opening tag.
					?>
					<div class="w-12 h-12 bg-blue-100 <?php echo $emifree_has_link ? 'group-hover:bg-blue-700' : ''; ?> rounded-xl flex items-center justify-center mb-4 transition-colors duration-300">
						<svg class="w-6 h-6 text-blue-700 <?php echo $emifree_has_link ? 'group-hover:text-white' : ''; ?> transition-colors duration-300" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true">
							<?php echo $emifree_knowledge_icons['file-text']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</svg>
					</div>
					<h4 class="text-zinc-900 font-semibold mb-2 leading-snug">
						<?php echo esc_html( $emifree_pdf['name'] ); ?>
						<?php if ( ! $emifree_has_link ) : ?>
							<span class="text-slate-500 font-normal">(demnächst verfügbar)</span>
						<?php endif; ?>
					</h4>
					<div class="flex items-center justify-between text-sm text-slate-500">
						<span><?php echo esc_html( $emifree_pdf['size'] ); ?></span>
						<span class="bg-slate-100 px-2 py-0.5 rounded text-xs font-semibold"><?php echo esc_html( $emifree_pdf['lang'] ); ?></span>
					</div>
					<?php
					echo $emifree_close; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped — controlled closing tag.
				endforeach; ?>
			</div>

			<div class="rounded-3xl p-8 md:p-12 text-white text-center" style="background: linear-gradient(90deg, #1d4ed8 0%, #06b6d4 100%);">
				<h3 class="text-2xl md:text-3xl font-bold mb-4">Benötigen Sie individuelle Dokumentation?</h3>
				<p class="text-lg text-blue-100 mb-8 max-w-2xl mx-auto">
					Sie finden nicht, was Sie suchen? Kontaktieren Sie unser technisches Team für individuelle Datenblätter, CAD-Zeichnungen oder spezifische Dokumentation für Ihre Anwendung.
				</p>
				<a
					href="/#contact"
					class="inline-flex items-center gap-2 bg-white text-blue-700 px-8 py-4 rounded-full font-semibold hover:bg-blue-50 transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2"
				>
					Technischen Support kontaktieren
					<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true">
						<?php echo $emifree_knowledge_icons['arrow-right']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</svg>
				</a>
			</div>
		</div>

	</div>
</section>