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
				'name'        => 'Mechanical Filtration',
				'tagline'     => 'Industrial-Strength Oil Mist & Dust Extraction',
				'short_desc'  => 'Centrifugal separation technology for heavy-duty CNC machining environments. Reliable performance, minimal maintenance.',
				'description' => 'Our mechanical filtration systems use centrifugal force to separate oil mist and coolant fumes directly at the source. Designed for CNC lathes, mills, grinding machines, and industrial workshops where continuous production is critical.',
				'images'      => array( 'fotom1.webp', 'fotom5.webp', 'fotom6.webp' ),
				'features'    => array(
					array( 'icon' => 'settings', 'title' => 'Heavy-Duty Construction',     'desc' => 'Industrial-grade sheet metal housing with powder-coated finish for durability in harsh workshop environments' ),
					array( 'icon' => 'zap',     'title' => 'High Airflow Capacity',       'desc' => 'Up to 2,750 m³/hr airflow to handle multiple machining operations simultaneously' ),
					array( 'icon' => 'shield',  'title' => 'HEPA Filter Option',          'desc' => 'Optional HEPA post-filter achieves 99.95% particle removal for cleanroom applications' ),
					array( 'icon' => 'droplets','title' => 'Self-Cleaning',               'desc' => 'The built-in spray nozzles allow the collection system to be cleaned without removing the module' ),
				),
				'specs'        => array(
					array( 'label' => 'Airflow',      'value' => '1,500 - 2,750', 'unit' => 'm³/hr' ),
					array( 'label' => 'Motor Power',  'value' => '1.5 - 3.0',     'unit' => 'kW' ),
					array( 'label' => 'Filter Type',  'value' => 'Centrifugal + HEPA', 'unit' => 'Optional' ),
					array( 'label' => 'Noise Level',  'value' => '< 65',          'unit' => 'dB' ),
					array( 'label' => 'Weight',       'value' => '85 - 120',      'unit' => 'kg' ),
					array( 'label' => 'Dimensions',   'value' => '600 x 600 x 1,200', 'unit' => 'mm' ),
				),
				'applications' => array( 'CNC Machining', 'Grinding', 'Turning', 'Milling', 'Spark Eroding' ),
				'cta'          => 'Request Mechanical Filtration Quote',
			),
			'electrostatic' => array(
				'name'        => 'Electrostatic Filtration',
				'tagline'     => 'Advanced Corona Discharge Technology',
				'short_desc'  => 'Superior separation for fine particles, smoke, sub-micron oil mist, and industrial odors where mechanical filters reach their limits.',
				'description' => 'Advanced corona discharge technology for fine particle separation. Ideal for smoke, sub-micron oil mist, and industrial odor control. Electrostatic filtration ionizes and captures particles on collector plates with high separation efficiency where conventional filters struggle.',
				'images'      => array( 'fotoe1.webp', 'fotoe2.webp', 'fotoe3.webp' ),
				'features'    => array(
					array( 'icon' => 'cpu',    'title' => 'Electrostatic Technology', 'desc' => 'Ionizes and captures sub-micron particles (including smoke) on collector plates. Achieves high separation efficiency where conventional filters struggle.' ),
					array( 'icon' => 'wrench', 'title' => 'Low Maintenance Operation', 'desc' => 'Robust ionizer design with optional self-cleaning system. Reduces manual cleaning cycles and extends service life.' ),
					array( 'icon' => 'wifi',   'title' => 'Industry 4.0 Ready',        'desc' => 'Premium version includes Siemens Touch-Panel, PROFINET/PROFIBUS connectivity, and real-time parameter monitoring for smart factory integration.' ),
					array( 'icon' => 'box',    'title' => 'Compact & Flexible',      'desc' => '818 × 466 × 566 mm footprint. Easy to retrofit. Optional Service Trolley allows on-site cleaning without module removal.' ),
				),
				'specs'        => array(
					array( 'label' => 'Dimensions',       'value' => '818 × 466 × 566',     'unit' => 'mm' ),
					array( 'label' => 'Technology',       'value' => 'Corona Discharge',    'unit' => 'Ionization' ),
					array( 'label' => 'Particle Capture', 'value' => 'Sub-micron',         'unit' => 'Including smoke' ),
					array( 'label' => 'Connectivity',     'value' => 'PROFINET/PROFIBUS',  'unit' => 'Optional' ),
					array( 'label' => 'Control Panel',    'value' => 'Siemens Touch',      'unit' => 'Premium' ),
					array( 'label' => 'Service',          'value' => 'Self-cleaning',      'unit' => 'Optional' ),
				),
				'applications' => array( 'Machining with high-speed tools', 'Smoke from cutting fluids', 'Industrial soldering & welding', 'Chemical & pharmaceutical processes' ),
				'cta'          => 'Request Electrostatic Filtration Quote',
			),
			'dust'          => array(
				'name'        => 'Dust Filtration',
				'tagline'     => 'High-Efficiency Dust Collection for Dry Processes',
				'short_desc'  => 'Reliable cartridge and baghouse solutions for heavy dust loads from woodworking, metal grinding, and bulk material handling.',
				'description' => 'Our dust filtration systems are engineered for dry dust applications. Using advanced media technology and pulse-jet cleaning, they deliver consistent airflow and long filter life — even in the most demanding industrial settings.',
				'images'      => array( 'dust1.webp', 'dust1.webp', 'dust1.webp' ),
				'features'    => array(
					array( 'icon' => 'box',    'title' => 'Modular Design',           'desc' => 'Scalable cartridge and baghouse configurations to match your airflow and space requirements.' ),
					array( 'icon' => 'gauge',  'title' => 'Pulse-Jet Cleaning',       'desc' => 'Automatic compressed-air cleaning maintains low pressure drop and extends filter life.' ),
					array( 'icon' => 'shield', 'title' => 'Explosion Protection',    'desc' => 'Optional ATEX-certified components for safe operation in combustible dust environments.' ),
					array( 'icon' => 'layers', 'title' => 'Multiple Filtration Media', 'desc' => 'Choose from cellulose, polyester, or PTFE membranes for optimal efficiency with your specific dust type.' ),
				),
				'specs'        => array(
					array( 'label' => 'Airflow',         'value' => '2,000 - 10,000', 'unit' => 'm³/hr' ),
					array( 'label' => 'Filter Area',     'value' => '20 - 200',       'unit' => 'm²' ),
					array( 'label' => 'Cleaning Method', 'value' => 'Pulse-jet',      'unit' => 'Automated' ),
					array( 'label' => 'Dust Load',       'value' => 'Up to 100',      'unit' => 'g/m³' ),
					array( 'label' => 'Efficiency',      'value' => '> 99.9',         'unit' => '%' ),
					array( 'label' => 'Operating Temp',  'value' => '-20 to +80',    'unit' => '°C' ),
				),
				'applications' => array( 'Woodworking', 'Metal Grinding', 'Minerals Processing', 'Food & Grain', 'Pharmaceuticals' ),
				'cta'          => 'Request Dust Filtration Quote',
			),
		);
	}
endif;