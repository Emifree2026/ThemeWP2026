<?php
/**
 * Products data + SVG icons.
 *
 * Mirrors src/components/Products.jsx from the React app. Three product
 * lines (Mechanical, Electrostatic, Dust), each with tagline, shortDesc,
 * description, images, 4 features, 6 specs, applications list, and CTA.
 * Icons are inline SVG paths from lucide-react (24x24 viewBox).
 *
 * NOTE on the auto-play carousel: the React version cycles images every
 * 4 seconds. The WordPress version renders all images statically; the
 * tab's per-section JS (assets/js/sections/products.js) handles the
 * active-image indicator click — no auto-play by default, which is the
 * better a11y choice for a site where the visitor is likely reading the
 * product specs in parallel.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'emifree_product_icons' ) ) :
	function emifree_product_icons() {
		return array(
			'settings'  => '<path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"></path><circle cx="12" cy="12" r="3"></circle>',
			'zap'      => '<path d="M4 14a1 1 0 0 1-.78-1.63l9.9-10.2a.5.5 0 0 1 .86.46l-1.92 6.02A1 1 0 0 0 13 10h7a1 1 0 0 1 .78 1.63l-9.9 10.2a.5.5 0 0 1-.86-.46l1.92-6.02A1 1 0 0 0 11 14z"></path>',
			'shield'   => '<path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"></path>',
			'droplets' => '<path d="M7 16.3c2.2 0 4-1.83 4-4.05 0-1.16-.57-2.26-1.71-3.19S7.29 6.75 7 5.3c-.29 1.45-1.14 2.84-2.29 3.76S3 11.1 3 12.25c0 2.22 1.8 4.05 4 4.05z"></path><path d="M12.56 14.69c1.46 0 2.64-1.22 2.64-2.7 0-.78-.38-1.51-1.13-2.13C13.33 9.31 13 8.49 13 7.7c0-.79-.29 1.61-.92 2.43-.69.91-1.85 1.66-1.85 2.86 0 1.48 1.18 2.7 2.64 2.7z"></path><path d="M17 16.3c2.2 0 4-1.83 4-4.05 0-1.16-.57-2.26-1.71-3.19S17.29 6.75 17 5.3c-.29 1.45-1.14 2.84-2.29 3.76S13 11.1 13 12.25c0 2.22 1.8 4.05 4 4.05z"></path>',
			'cpu'      => '<rect x="4" y="4" width="16" height="16" rx="2"></rect><rect x="9" y="9" width="6" height="6"></rect><line x1="9" y1="2" x2="9" y2="4"></line><line x1="15" y1="2" x2="15" y2="4"></line><line x1="9" y1="20" x2="9" y2="22"></line><line x1="15" y1="20" x2="15" y2="22"></line><line x1="20" y1="9" x2="22" y2="9"></line><line x1="20" y1="14" x2="22" y2="14"></line><line x1="2" y1="9" x2="4" y2="9"></line><line x1="2" y1="14" x2="4" y2="14"></line>',
			'wrench'   => '<path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"></path>',
			'wifi'     => '<path d="M5 13a10 10 0 0 1 14 0"></path><path d="M8.5 16.5a5 5 0 0 1 7 0"></path><line x1="12" y1="20" x2="12.01" y2="20"></line>',
			'box'      => '<path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline><line x1="12" y1="22.08" x2="12" y2="12"></line>',
			'gauge'    => '<path d="M12 14l4-4"></path><path d="M3.34 19a10 10 0 1 1 17.32 0"></path>',
			'layers'   => '<polygon points="12 2 2 7 12 12 22 7 12 2"></polygon><polyline points="2 17 12 22 22 17"></polyline><polyline points="2 12 12 17 22 12"></polyline>',
		);
	}
endif;

if ( ! function_exists( 'emifree_products' ) ) :
	function emifree_products() {
		$emifree_uri = get_template_directory_uri() . '/assets/products/';

		return array(
			'mechanical'    => array(
				'name'        => 'Mechanische Filtration',
				'tagline'     => 'Industrielle Öl- und Staubabsaugung',
				'short_desc'  => 'Zentrifugale Abscheidetechnologie für den Dauereinsatz in der CNC-Bearbeitung. Zuverlässige Leistung, minimaler Wartungsaufwand.',
				'description' => 'Unsere mechanischen Filtrationssysteme nutzen Zentrifugalkraft, um Ölnebel und Kühlmitteldämpfe direkt an der Quelle abzuscheiden. Entwickelt für CNC-Drehmaschinen, Fräsmaschinen, Schleifmaschinen und industrielle Werkstätten, in denen kontinuierliche Produktion entscheidend ist.',
				'images'      => array( 'fotom1.webp', 'fotom5.webp', 'fotom6.webp' ),
				'features'    => array(
					array( 'icon' => 'settings', 'title' => 'Robuste Bauweise',          'desc' => 'Industrietaugliches Blechgehäuse mit pulverbeschichteter Oberfläche für hohe Langlebigkeit auch in anspruchsvollen Werkstattumgebungen' ),
					array( 'icon' => 'zap',     'title' => 'Hohe Luftleistung',           'desc' => 'Bis zu 2.750 m³/h Luftleistung für mehrere gleichzeitige Bearbeitungsprozesse' ),
					array( 'icon' => 'shield',  'title' => 'Optionaler HEPA-Filter',      'desc' => 'Der optionale HEPA-Nachfilter erreicht eine Partikelabscheidung von 99,95 % für Reinraumanwendungen' ),
					array( 'icon' => 'droplets','title' => 'Selbstreinigung',             'desc' => 'Die integrierten Sprühdüsen ermöglichen die Reinigung des Sammelsystems, ohne das Modul zu entfernen' ),
				),
				'specs'        => array(
					array( 'label' => 'Luftleistung',     'value' => '1.500 - 2.750', 'unit' => 'm³/h' ),
					array( 'label' => 'Motorleistung',    'value' => '1,5 - 3,0',     'unit' => 'kW' ),
					array( 'label' => 'Filtertyp',        'value' => 'Zentrifugal + HEPA', 'unit' => 'Optional' ),
					array( 'label' => 'Geräuschpegel',    'value' => '< 65',          'unit' => 'dB' ),
					array( 'label' => 'Gewicht',          'value' => '85 - 120',      'unit' => 'kg' ),
					array( 'label' => 'Abmessungen',      'value' => '600 x 600 x 1.200', 'unit' => 'mm' ),
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
				'specs'        => array(
					array( 'label' => 'Abmessungen',      'value' => '818 × 466 × 566',     'unit' => 'mm' ),
					array( 'label' => 'Technologie',      'value' => 'Koronaentladung',    'unit' => 'Ionisation' ),
					array( 'label' => 'Partikelerfassung','value' => 'Submikronbereich',   'unit' => 'Einschließlich Rauch' ),
					array( 'label' => 'Konnektivität',    'value' => 'PROFINET/PROFIBUS',  'unit' => 'Optional' ),
					array( 'label' => 'Bedienpanel',      'value' => 'Siemens Touch',      'unit' => 'Premium' ),
					array( 'label' => 'Service',          'value' => 'Selbstreinigend',    'unit' => 'Optional' ),
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
					array( 'icon' => 'box',    'title' => 'Modulares Design',            'desc' => 'Skalierbare Patronen- und Schlauchfilterkonfigurationen, angepasst an Luftleistungs- und Platzanforderungen.' ),
					array( 'icon' => 'gauge',  'title' => 'Druckluft-Abreinigung',        'desc' => 'Die automatische Druckluftreinigung hält den Druckverlust niedrig und verlängert die Filterlebensdauer.' ),
					array( 'icon' => 'shield', 'title' => 'Explosionsschutz',             'desc' => 'Optionale ATEX-zertifizierte Komponenten für den sicheren Betrieb in Umgebungen mit brennbarem Staub.' ),
					array( 'icon' => 'layers', 'title' => 'Verschiedene Filtermedien',    'desc' => 'Wählen Sie zwischen Zellulose-, Polyester- oder PTFE-Membranen für optimale Effizienz bei Ihrem spezifischen Staubtyp.' ),
				),
				'specs'        => array(
					array( 'label' => 'Luftleistung',        'value' => '2.000 - 10.000', 'unit' => 'm³/h' ),
					array( 'label' => 'Filterfläche',        'value' => '20 - 200',       'unit' => 'm²' ),
					array( 'label' => 'Reinigungsmethode',   'value' => 'Druckluft-Abreinigung', 'unit' => 'Automatisch' ),
					array( 'label' => 'Staubbelastung',      'value' => 'Bis zu 100',     'unit' => 'g/m³' ),
					array( 'label' => 'Wirkungsgrad',        'value' => '> 99,9',         'unit' => '%' ),
					array( 'label' => 'Betriebstemperatur',  'value' => '-20 bis +80',    'unit' => '°C' ),
				),
				'applications' => array( 'Holzbearbeitung', 'Metallschleifen', 'Mineralienverarbeitung', 'Lebensmittel & Getreide', 'Pharmaindustrie' ),
				'cta'          => 'Angebot für Staubfiltration anfordern',
			),
		);
	}
endif;
