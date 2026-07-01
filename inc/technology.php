<?php
/**
 * Technology data + SVG icons.
 *
 * Mirrors src/components/Technology.jsx from the React app. Two
 * filtration technologies (ECO AIR, EARIA), each with a selector card
 * (badge, title, subtitle, description, bullet list, scroll anchor) and
 * a Process section (title, subtitle, 3 or 4 step entries with image
 * filename + caption).
 *
 * Icons are inline SVG paths from lucide-react (24x24 viewBox,
 * stroke-based) — no external icon library required.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'emifree_technology_icons' ) ) :
	function emifree_technology_icons() {
		return array(
			'check'      => '<path d="M21.801 10A10 10 0 1 1 17 3.335"></path><path d="m9 11 3 3L22 4"></path>',
			'move-right' => '<path d="M18 8L22 12L18 16"></path><path d="M2 12H22"></path>',
			'arrow-right'=> '<path d="M5 12h14"></path><path d="m12 5 7 7-7 7"></path>',
		);
	}
endif;

if ( ! function_exists( 'emifree_technologies' ) ) :
	function emifree_technologies() {
		return array(
			'eco-air' => array(
				'badge'           => 'Best for Oil Mist & Emulsions',
				'badge_bg'        => 'bg-amber-100',
				'badge_text'      => 'text-amber-800',
				'title'           => 'ECO AIR CLEANER',
				'subtitle'        => 'Mechanical Oil Mist Separator',
				'description'     => 'Constant suction performance through self-cleaning mechanical filtration and oil recycling.',
				'bullets'         => array(
					'Self-cleaning system',
					'No filter mats',
					'Oil recycling',
					'Up to 1,500 m³/h',
				),
				'process_title'   => 'How ECO AIR Works',
				'process_subtitle'=> 'Mechanical separation + coalescence',
				'steps'           => array(
					array(
						'title' => 'Pre-Filtration',
						'desc'  => 'Stainless steel mesh captures larger droplets before separation.',
						'image' => 'Step1.1_ECOAIR.jpg',
					),
					array(
						'title' => 'Coalescence',
						'desc'  => 'Rotating drums enlarge tiny oil particles into larger droplets for efficient separation and oil recovery.',
						'image' => 'Step2.1_ECOAIR.webp',
					),
					array(
						'title' => 'Self-Cleaning & Recycling',
						'desc'  => 'Integrated spraying nozzles maintain constant performance and recycle collected oil.',
						'image' => 'Step3_clean.webp',
					),
				),
				'anchor_id'       => 'technology-eco-air',
				'inquiry_type'    => 'eco-air',
			),
			'earia' => array(
				'badge'           => 'Best for Smoke & Fine Aerosols',
				'badge_bg'        => 'bg-purple-100',
				'badge_text'      => 'text-purple-800',
				'title'           => 'EARIA',
				'subtitle'        => 'Electrostatic Filtration System',
				'description'     => 'Electrostatic filtration for smoke, oil mist and ultra-fine particles with minimal maintenance.',
				'bullets'         => array(
					'Smoke & aerosol capture',
					'Electrostatic filtration',
					'No cartridge exchange',
					'Adjustable up to 1000 m³/h',
				),
				'process_title'   => 'How EARIA Works',
				'process_subtitle'=> 'Electrostatic filtration',
				'steps'           => array(
					array(
						'title' => 'Stainless steel pre-filter',
						'desc'  => 'First stage captures larger particles to protect ionizer.',
						'image' => 'Step 1.webp',
					),
					array(
						'title' => 'Ionization',
						'desc'  => 'High voltage ionizer charges particles for electrostatic capture.',
						'image' => 'Step 2.webp',
					),
					array(
						'title' => 'Collector plates',
						'desc'  => 'Alternating plates attract and hold charged particles.',
						'image' => 'Step 3.webp',
					),
					array(
						'title' => 'Oil particle collection',
						'desc'  => 'Self-cleaning drain removes collected oil automatically.',
						'image' => 'Step 4.webp',
					),
				),
				'anchor_id'       => 'technology-earia',
				'inquiry_type'    => 'earia',
			),
		);
	}
endif;
