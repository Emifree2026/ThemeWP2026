<?php
/**
 * Products section — German.
 *
 * Hard-coded translation of section-products.php. Three product lines
 * (Mechanisch / Elektrostatisch / Staub) with image gallery, features
 * grid, applications list, and per-product inquiry CTA.
 *
 * Tab switching + image cycling are handled by the same
 * assets/js/sections/products.js — the JS is language-agnostic.
 */
emifree_enqueue_section_script( 'products' );

// Compact feature icons — short SVG paths, decorative only (the
// accessible name comes from the heading text).
$emifree_product_icons = array(
	'settings'  => '<circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 1 19.4a1.65 1.65 0 0 0-1.82-.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 1a1.65 1.65 0 0 0 .33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path>',
	'zap'       => '<path d="M4 14a1 1 0 0 1-.78-1.63l9.9-10.2a.5.5 0 0 1 .86.46l-1.92 6.02A1 1 0 0 0 13 10h7a1 1 0 0 1 .78 1.63l-9.9 10.2a.5.5 0 0 1-.86-.46l1.92-6.02A1 1 0 0 0 11 14z"></path>',
	'shield'    => '<path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"></path>',
	'droplets'  => '<path d="M7 16.3c2.2 0 4-1.83 4-4.05 0-1.16-.57-2.26-1.71-3.19S7.29 6.75 7 5.3c-.29 1.45-1.14 2.84-2.29 3.76S3 11.1 3 12.25c0 2.22 1.8 4.05 4 4.05z"></path><path d="M12.56 14.69c1.46 0 2.64-1.22 2.64-2.7 0-.78-.38-1.51-1.13-2.13C13.33 9.31 13 8.49 13 7.7c0-.79-.29 1.61-.92 2.43-.69.91-1.85 1.66-1.85 2.86 0 1.48 1.18 2.7 2.64 2.7z"></path><path d="M17 16.3c2.2 0 4-1.83 4-4.05 0-1.16-.57-2.26-1.71-3.19S17.29 6.75 17 5.3c-.29 1.45-1.14 2.84-2.29 3.76S13 11.1 13 12.25c0 2.22 1.8 4.05 4 4.05z"></path>',
	'cpu'       => '<rect x="4" y="4" width="16" height="16" rx="2"></rect><rect x="9" y="9" width="6" height="6"></rect>',
	'wrench'    => '<path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"></path>',
	'wifi'      => '<path d="M5 13a10 10 0 0 1 14 0"></path><path d="M8.5 16.5a5 5 0 0 1 7 0"></path><line x1="12" y1="20" x2="12.01" y2="20"></line>',
	'box'       => '<path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline><line x1="12" y1="22.08" x2="12" y2="12"></line>',
	'gauge'     => '<path d="M12 14l4-4"></path><path d="M3.34 19a10 10 0 1 1 17.32 0"></path>',
	'layers'    => '<polygon points="12 2 2 7 12 12 22 7 12 2"></polygon><polyline points="2 17 12 22 22 17"></polyline><polyline points="2 12 12 17 22 12"></polyline>',
);

// German product data — inlined to keep the section self-contained.
$emifree_products = array(
	'mechanical'    => array(
		'name'        => 'Mechanische Filtration',
		'tagline'     => 'Industrielle Öl- und Staubabsaugung',
		'short_desc'  => 'Zentrifugale Abscheidetechnologie für den Dauereinsatz in der CNC-Bearbeitung. Zuverlässige Leistung, minimaler Wartungsaufwand.',
		'description' => 'Unsere mechanischen Filtrationssysteme nutzen Zentrifugalkraft, um Ölnebel und Kühlmitteldämpfe direkt an der Quelle abzuscheiden. Entwickelt für CNC-Drehmaschinen, Fräsmaschinen, Schleifmaschinen und industrielle Werkstätten, in denen kontinuierliche Produktion entscheidend ist.',
		'images'      => array( 'fotom1.webp', 'fotom5.webp', 'fotom6.webp' ),
		'features'    => array(
			array( 'icon' => 'settings', 'title' => 'Robuste Bauweise',      'desc' => 'Industrietaugliches Blechgehäuse mit pulverbeschichteter Oberfläche für hohe Langlebigkeit auch in anspruchsvollen Werkstattumgebungen' ),
			array( 'icon' => 'zap',      'title' => 'Hohe Luftleistung',     'desc' => 'Bis zu 2.750 m³/h Luftleistung für mehrere gleichzeitige Bearbeitungsprozesse' ),
			array( 'icon' => 'shield',   'title' => 'Optionaler HEPA-Filter','desc' => 'Der optionale HEPA-Nachfilter erreicht eine Partikelabscheidung von 99,95 % für Reinraumanwendungen' ),
			array( 'icon' => 'droplets', 'title' => 'Selbstreinigung',       'desc' => 'Die integrierten Sprühdüsen ermöglichen die Reinigung des Sammelsystems, ohne das Modul zu entfernen' ),
		),
		'applications' => array( 'CNC-Bearbeitung', 'Schleifen', 'Drehen', 'Fräsen', 'Funkenerosion' ),
		'cta'          => 'Angebot für mechanische Filtration anfordern',
	),
	'electrostatic' => array(
		'name'        => 'Elektrostatische Filtration',
		'tagline'     => 'Fortschrittliche Koronaentladungstechnologie',
		'short_desc'  => 'Überlegene Abscheidung von Feinstpartikeln, Rauch, Ölnebel im Submikronbereich und industriellen Gerüchen – dort, wo mechanische Filter an ihre Grenzen stoßen.',
		'description' => 'Fortschrittliche Koronaentladungstechnologie zur Abscheidung feinster Partikel. Ideal für Rauch, Ölnebel im Submikronbereich und die Kontrolle industrieller Gerüche. Die elektrostatische Filtration ionisiert Partikel und scheidet sie mit hoher Effizienz auf Sammelplatten ab – dort, wo herkömmliche Filter an ihre Grenzen stoßen.',
		'images'      => array( 'fotoe1.webp', 'fotoe2.webp', 'fotoe3.webp' ),
		'features'    => array(
			array( 'icon' => 'cpu',    'title' => 'Elektrostatische Technologie', 'desc' => 'Ionisiert und erfasst Partikel im Submikronbereich (einschließlich Rauch) auf Sammelplatten. Erzielt hohe Abscheideleistung, wo herkömmliche Filter an ihre Grenzen stoßen.' ),
			array( 'icon' => 'wrench', 'title' => 'Wartungsarmer Betrieb',         'desc' => 'Robuste Ionisatorkonstruktion mit optionalem Selbstreinigungssystem. Reduziert manuelle Reinigungsintervalle und verlängert die Lebensdauer.' ),
			array( 'icon' => 'wifi',   'title' => 'Industrie-4.0-fähig',           'desc' => 'Die Premium-Version verfügt über ein Siemens-Touch-Panel, PROFINET/PROFIBUS-Anbindung und Echtzeit-Parameterüberwachung für die Integration in intelligente Fabriken.' ),
			array( 'icon' => 'box',    'title' => 'Kompakt & flexibel',            'desc' => 'Grundfläche von 818 × 466 × 566 mm. Einfach nachrüstbar. Der optionale Service-Wagen ermöglicht die Reinigung vor Ort, ohne das Modul zu entfernen.' ),
		),
		'applications' => array( 'Bearbeitung mit Hochgeschwindigkeitswerkzeugen', 'Rauch durch Kühlschmierstoffe', 'Industrielles Löten & Schweißen', 'Chemische & pharmazeutische Prozesse' ),
		'cta'          => 'Angebot für elektrostatische Filtration anfordern',
	),
	'dust'          => array(
		'name'        => 'Staubfiltration',
		'tagline'     => 'Hocheffiziente Staubabscheidung für Trockenprozesse',
		'short_desc'  => 'Zuverlässige Patronen- und Schlauchfilterlösungen für hohe Staubbelastungen aus Holzbearbeitung, Metallschleifen und Schüttgutumschlag.',
		'description' => 'Unsere Staubfiltrationssysteme sind für den Einsatz bei trockenem Staub konzipiert. Dank fortschrittlicher Filtermedientechnologie und Druckluft-Abreinigung sorgen sie für gleichbleibende Luftleistung und eine lange Filterlebensdauer – selbst in den anspruchsvollsten industriellen Umgebungen.',
		'images'      => array( 'Coming Soon.webp', 'Coming Soon.webp', 'Coming Soon.webp' ),
		'features'    => array(
			array( 'icon' => 'box',    'title' => 'Modulares Design',         'desc' => 'Skalierbare Patronen- und Schlauchfilterkonfigurationen, angepasst an Luftleistungs- und Platzanforderungen.' ),
			array( 'icon' => 'gauge',  'title' => 'Druckluft-Abreinigung',    'desc' => 'Die automatische Druckluftreinigung hält den Druckverlust niedrig und verlängert die Filterlebensdauer.' ),
			array( 'icon' => 'shield', 'title' => 'Explosionsschutz',         'desc' => 'Optionale ATEX-zertifizierte Komponenten für den sicheren Betrieb in Umgebungen mit brennbarem Staub.' ),
			array( 'icon' => 'layers', 'title' => 'Verschiedene Filtermedien','desc' => 'Wählen Sie zwischen Zellulose-, Polyester- oder PTFE-Membranen für optimale Effizienz bei Ihrem spezifischen Staubtyp.' ),
		),
		'applications' => array( 'Holzbearbeitung', 'Metallschleifen', 'Mineralienverarbeitung', 'Lebensmittel & Getreide', 'Pharmaindustrie' ),
		'cta'          => 'Angebot für Staubfiltration anfordern',
	),
);

// Tab SVG glyphs — kept tiny (single-path), decorative only.
$emifree_tab_icons = array(
	'mechanical'    => '<svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="3"></circle><path d="M12 1v6m0 10v6M4.22 4.22l4.24 4.24m7.08 7.08l4.24 4.24M1 12h6m10 0h6M4.22 19.78l4.24-4.24m7.08-7.08l4.24-4.24"></path></svg>',
	'electrostatic' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon></svg>',
	'dust'          => '<svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polygon points="12 2 2 7 12 12 22 7 12 2"></polygon><polyline points="2 17 12 22 22 17"></polyline></svg>',
);
?>

<section id="products" class="py-12 md:py-24 bg-slate-50">
	<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

		<div class="text-center mb-16">
			<h2 class="text-4xl md:text-5xl font-bold text-zinc-900 mb-6">
				Industrielles Luftfiltrations-Produktsortiment
			</h2>
			<p class="text-xl text-zinc-600 max-w-3xl mx-auto">
				Professionelle Filtrationssysteme für CNC-Werkstätten, Metallbearbeitungsbetriebe und industrielle Fertigungsumgebungen.
			</p>
		</div>

		<!-- Tabs (Mechanisch / Elektrostatisch / Staub) -->
		<div class="flex flex-wrap justify-center gap-4 mb-12" role="tablist" aria-label="Produktlinien">
			<?php $emifree_first = true; foreach ( $emifree_products as $emifree_key => $emifree_product ) : ?>
				<button
					type="button"
					role="tab"
					id="emifree-tab-<?php echo esc_attr( $emifree_key ); ?>"
					aria-selected="<?php echo $emifree_first ? 'true' : 'false'; ?>"
					aria-controls="emifree-panel-<?php echo esc_attr( $emifree_key ); ?>"
					data-emifree-tab="<?php echo esc_attr( $emifree_key ); ?>"
					class="emifree-product-tab px-8 py-4 rounded-full font-semibold transition-all duration-300 flex items-center gap-2 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2 <?php echo $emifree_first ? 'bg-blue-700 text-white shadow-lg' : 'bg-white text-zinc-600 hover:bg-slate-100 hover:text-blue-700 border border-slate-200'; ?>"
				>
					<?php echo $emifree_tab_icons[ $emifree_key ]; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped — SVG markup, controlled. ?>
					<?php echo esc_html( $emifree_product['name'] ); ?>
				</button>
			<?php $emifree_first = false; endforeach; ?>
		</div>

		<!-- Panels -->
		<?php $emifree_first = true; foreach ( $emifree_products as $emifree_key => $emifree_product ) : ?>
			<div
				role="tabpanel"
				id="emifree-panel-<?php echo esc_attr( $emifree_key ); ?>"
				aria-labelledby="emifree-tab-<?php echo esc_attr( $emifree_key ); ?>"
				data-emifree-panel="<?php echo esc_attr( $emifree_key ); ?>"
				class="<?php echo $emifree_first ? '' : 'hidden'; ?> emifree-product-panel"
			>
				<div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-start">
					<div class="space-y-4" data-emifree-gallery="<?php echo esc_attr( $emifree_key ); ?>">
						<div class="relative bg-white rounded-2xl overflow-hidden shadow-lg aspect-[4/3] flex items-center justify-center">
							<?php $emifree_img_index = 0; foreach ( $emifree_product['images'] as $emifree_img ) : ?>
								<img
									src="<?php echo esc_url( get_template_directory_uri() . '/assets/products/' . $emifree_img ); ?>"
									alt="<?php echo esc_attr( $emifree_product['name'] ); ?> – Ansicht <?php echo esc_attr( $emifree_img_index + 1 ); ?>"
									class="absolute inset-0 w-full h-full object-contain p-6 <?php echo 0 === $emifree_img_index ? '' : 'hidden'; ?>"
									data-emifree-image="<?php echo esc_attr( $emifree_img_index ); ?>"
									loading="lazy"
									decoding="async"
								>
							<?php $emifree_img_index++; endforeach; ?>
						</div>
						<div class="flex gap-4 justify-center">
							<?php $emifree_img_index = 0; foreach ( $emifree_product['images'] as $emifree_img ) : ?>
								<button
									type="button"
									data-emifree-thumb="<?php echo esc_attr( $emifree_img_index ); ?>"
									class="emifree-product-thumb relative rounded-xl overflow-hidden shadow-md transition-all duration-300 flex-shrink-0 <?php echo 0 === $emifree_img_index ? 'ring-2 ring-blue-700 ring-offset-2 scale-105' : 'opacity-70 hover:opacity-100'; ?>"
									aria-label="Bild <?php echo esc_attr( $emifree_img_index + 1 ); ?> anzeigen"
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

						<div>
							<h4 class="font-semibold text-zinc-900 mb-3">Anwendungen:</h4>
							<div class="flex flex-wrap gap-2">
								<?php foreach ( $emifree_product['applications'] as $emifree_app ) : ?>
									<span class="px-4 py-2 bg-cyan-50 text-cyan-700 text-sm font-medium rounded-full">
										<?php echo esc_html( $emifree_app ); ?>
									</span>
								<?php endforeach; ?>
							</div>
						</div>

						<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
							<?php foreach ( $emifree_product['features'] as $emifree_feature ) : ?>
								<div class="bg-white p-4 rounded-xl shadow-sm border border-slate-100">
									<div class="flex items-start gap-3">
										<div class="p-2 bg-blue-100 rounded-lg">
											<svg class="w-5 h-5" fill="none" stroke="#1d4ed8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true">
												<?php echo $emifree_product_icons[ $emifree_feature['icon'] ]; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped — SVG markup, controlled. ?>
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

						<button
							type="button"
							data-emifree-inquiry="<?php echo esc_attr( $emifree_key ); ?>"
							data-emifree-inquiry-label="<?php echo esc_attr( $emifree_product['name'] ); ?>"
							class="emifree-open-inquiry w-full text-white px-8 py-4 rounded-full font-semibold text-lg hover:shadow-xl transition-all duration-300 flex items-center justify-center gap-2 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2"
							style="background: linear-gradient(90deg, #1d4ed8 0%, #06b6d4 100%); box-shadow: 0 10px 25px rgba(6, 182, 212, 0.25);"
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