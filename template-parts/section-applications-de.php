<?php
/**
 * Applications section — German.
 *
 * Hard-coded translation of section-applications.php. 6 industrial
 * segments with inline SVG icons + German descriptions + SEO questions.
 * Icons are inline SVG paths from lucide-react (24x24 viewBox,
 * stroke-based) — same paths as the English version, no shared loader.
 */
?>

<?php
// German application icons — identical SVG paths to the English version.
// Inlined here so the German template is fully self-contained.
$emifree_app_icons = array(
	'cog'      => '<circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 1 19.4a1.65 1.65 0 0 0-1.82-.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 1a1.65 1.65 0 0 0 .33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path>',
	'flame'    => '<path d="M8.5 14.5A2.5 2.5 0 0 0 11 12c0-1.38-.5-2-1-3-1.072-2.143-.224-4.054 2-6 .5 2.5 2 4.9 4 6.5 2 1.6 3 3.5 3 5.5a7 7 0 1 1-14 0c0-1.153.433-2.294 1-3a2.5 2.5 0 0 0 2.5 2.5z"></path>',
	'sparkles' => '<path d="M9.937 15.5A2 2 0 0 0 8.5 14.063l-6.135-1.582a.5.5 0 0 1 0-.962L8.5 9.936A2 2 0 0 0 9.937 8.5l1.582-6.135a.5.5 0 0 1 .963 0L14.063 8.5A2 2 0 0 0 15.5 9.937l6.135 1.581a.5.5 0 0 1 0 .964L15.5 14.063a2 2 0 0 0-1.437 1.437l-1.582 6.135a.5.5 0 0 1-.963 0z"></path><path d="M20 3v4"></path><path d="M22 5h-4"></path><path d="M4 17v2"></path><path d="M5 18H3"></path>',
	'wrench'   => '<path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"></path>',
	'factory'  => '<path d="M2 20a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8l-7 5V8l-7 5V4a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2Z"></path>',
	'car'      => '<path d="M19 17h2c.6 0 1-.4 1-1v-3c0-.9-.7-1.7-1.5-1.9C18.7 10.6 16 10 16 10s-1.3-1.4-2.2-2.3c-.5-.4-1.1-.7-1.8-.7H5c-.6 0-1.1.4-1.4.9l-1.4 2.9A3.7 3.7 0 0 0 2 12v4c0 .6.4 1 1 1h2"></path><circle cx="7" cy="17" r="2"></circle><path d="M9 17h6"></path><circle cx="17" cy="17" r="2"></circle>',
);

// German applications data — inlined to keep the section self-contained.
$emifree_apps = array(
	array(
		'icon'        => 'cog',
		'title'       => 'CNC-Bearbeitung & Werkzeugbau',
		'description' => 'Entfernen Sie Ölnebel und Kühlmitteldämpfe von CNC-Drehmaschinen, Fräsmaschinen und Portalfräsen, ohne die Produktion zu unterbrechen. Halten Sie Ihre Maschinen sauber und Ihre Mitarbeiter gesund.',
		'color'       => 'from-blue-700 to-blue-900',
		'question'    => 'Wie entferne ich Ölnebel von meiner CNC-Maschine, ohne die Produktion zu unterbrechen?',
	),
	array(
		'icon'        => 'flame',
		'title'       => 'Metallbearbeitung & Schweißen',
		'description' => 'Erfassen Sie Schweißrauch, Metallstaub und Schleifpartikel direkt an der Quelle. Schützen Sie Ihre Mitarbeiter vor schädlichen Feinstäuben und erfüllen Sie EU-Sicherheitsstandards.',
		'color'       => 'from-cyan-600 to-cyan-800',
		'question'    => 'Was ist die beste Methode, um Schweißrauch in einer kleinen Werkstatt zu filtern?',
	),
	array(
		'icon'        => 'sparkles',
		'title'       => 'Schleifen & Polieren',
		'description' => 'Extrahieren Sie Schleifstaub aus Flachschleif-, Rundschleif- und Polierprozessen. Verlängern Sie die Lebensdauer Ihrer Anlagen und verbessern Sie die Oberflächengüte.',
		'color'       => 'from-slate-600 to-slate-800',
		'question'    => 'Wie lässt sich Staub von Flachschleifmaschinen kontrollieren?',
	),
	array(
		'icon'        => 'wrench',
		'title'       => 'Lager- & Präzisionsteilefertigung',
		'description' => 'Sorgen Sie für saubere Luft in der Lagerproduktion, Getriebefertigung und Präzisionstechnik. Schützen Sie empfindliche Bauteile vor Verunreinigung.',
		'color'       => 'from-blue-600 to-cyan-700',
		'question'    => 'Beste Luftfiltration für Reinräume in der Lagerfertigung',
	),
	array(
		'icon'        => 'factory',
		'title'       => 'Schwerindustrie & Metallverarbeitung',
		'description' => 'Bewältigen Sie hohe Staub- und Nebelbelastungen in Fertigungshallen, Presswerken und Montagelinien. Industrietaugliche Filtration für anspruchsvolle Umgebungen.',
		'color'       => 'from-slate-700 to-zinc-800',
		'question'    => 'Industrielle Staubabsauganlage für Metallverarbeitungsbetriebe',
	),
	array(
		'icon'        => 'car',
		'title'       => 'Automobilindustrie',
		'description' => 'Erfassen Sie Ölnebel, Kühlmittelaerosole und Schweißrauch in Fertigungslinien der Automobilindustrie. Halten Sie Lackierkabinen partikelfrei und Reinräume für die EV-Batteriemontage normkonform.',
		'color'       => 'from-cyan-700 to-cyan-600',
		'question'    => 'Beste Luftfiltrationsanlage für Ölnebel- und Schweißrauchabsaugung in der Automobilfertigung',
	),
);
?>

<section id="applications" class="py-12 md:py-24 bg-white">
	<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

		<div class="text-center mb-20">
			<h2 class="text-4xl md:text-5xl font-bold mb-6" style="background: linear-gradient(90deg, #1d4ed8 0%, #06b6d4 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; color: transparent;">
				Industrielle Luftfiltration für jede Anwendung
			</h2>
			<p class="text-xl text-zinc-600 max-w-3xl mx-auto">
				Vom kleinen Handwerksbetrieb bis zur großen Produktionshalle – Emifree-Systeme erfassen Ölnebel, Schweißrauch und Staub direkt an der Quelle.
			</p>
		</div>

		<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
			<?php foreach ( $emifree_apps as $emifree_app ) : ?>
				<article class="group relative bg-white rounded-2xl p-8 shadow-lg hover:shadow-2xl transition-all duration-300 border border-slate-100 hover:border-slate-200">
					<div class="relative">
						<div class="w-16 h-16 rounded-2xl flex items-center justify-center mb-6 transition-transform duration-300 group-hover:scale-110" style="background: linear-gradient(135deg, #2563eb 0%, #06b6d4 100%); box-shadow: 0 10px 25px rgba(37, 99, 235, 0.25);">
							<svg class="w-8 h-8" fill="none" stroke="#ffffff" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true" style="filter: drop-shadow(0 1px 2px rgba(0,0,0,0.2));">
								<?php echo $emifree_app_icons[ $emifree_app['icon'] ]; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped — SVG markup, controlled input. ?>
							</svg>
						</div>

						<h3 class="text-xl font-bold text-zinc-900 mb-4 transition-colors duration-300 group-hover:text-blue-700">
							<?php echo esc_html( $emifree_app['title'] ); ?>
						</h3>

						<p class="text-zinc-600 leading-relaxed mb-4">
							<?php echo esc_html( $emifree_app['description'] ); ?>
						</p>

						<!-- SEO question — surfaced as a subtle italic line.
						     text-zinc-600 keeps WCAG AA contrast (5.74:1 on the
						     white card) while still reading as muted copy. -->
						<p class="text-sm text-zinc-600 italic">
							&ldquo;<?php echo esc_html( $emifree_app['question'] ); ?>&rdquo;
						</p>
					</div>
				</article>
			<?php endforeach; ?>
		</div>

		<div class="text-center mt-16">
			<a
				href="#technology"
				class="text-white px-8 py-4 rounded-full font-semibold text-lg hover:shadow-xl transition-all duration-300 inline-block"
				style="background: linear-gradient(90deg, #1d4ed8 0%, #06b6d4 100%); box-shadow: 0 10px 25px rgba(6, 182, 212, 0.25);"
			>
				Finden Sie Ihre Lösung
			</a>
		</div>

	</div>
</section>