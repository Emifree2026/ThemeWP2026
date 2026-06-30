<?php
/**
 * Applications data + SVG icons.
 *
 * Mirrors src/components/Applications.jsx from the React app. Each entry
 * has an icon, title, description, color gradient class, and SEO question.
 * Icons are inline SVG paths from the lucide-react library (24x24
 * viewBox, stroke-based) — no external icon library required.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'emifree_application_icons' ) ) :
	/**
	 * Returns a map of icon name => SVG inner (just the <path> elements).
	 * The template wraps each path in a <svg> element so the visual size
	 * class (w-8 h-8) applies.
	 */
	function emifree_application_icons() {
		return array(
			'cog'      => '<circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 1 19.4a1.65 1.65 0 0 0-1.82-.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 1a1.65 1.65 0 0 0 .33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path>',
			'flame'    => '<path d="M8.5 14.5A2.5 2.5 0 0 0 11 12c0-1.38-.5-2-1-3-1.072-2.143-.224-4.054 2-6 .5 2.5 2 4.9 4 6.5 2 1.6 3 3.5 3 5.5a7 7 0 1 1-14 0c0-1.153.433-2.294 1-3a2.5 2.5 0 0 0 2.5 2.5z"></path>',
			'sparkles' => '<path d="M9.937 15.5A2 2 0 0 0 8.5 14.063l-6.135-1.582a.5.5 0 0 1 0-.962L8.5 9.936A2 2 0 0 0 9.937 8.5l1.582-6.135a.5.5 0 0 1 .963 0L14.063 8.5A2 2 0 0 0 15.5 9.937l6.135 1.581a.5.5 0 0 1 0 .964L15.5 14.063a2 2 0 0 0-1.437 1.437l-1.582 6.135a.5.5 0 0 1-.963 0z"></path><path d="M20 3v4"></path><path d="M22 5h-4"></path><path d="M4 17v2"></path><path d="M5 18H3"></path>',
			'wrench'   => '<path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"></path>',
			'factory'  => '<path d="M2 20a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8l-7 5V8l-7 5V4a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2Z"></path>',
			'car'      => '<path d="M19 17h2c.6 0 1-.4 1-1v-3c0-.9-.7-1.7-1.5-1.9C18.7 10.6 16 10 16 10s-1.3-1.4-2.2-2.3c-.5-.4-1.1-.7-1.8-.7H5c-.6 0-1.1.4-1.4.9l-1.4 2.9A3.7 3.7 0 0 0 2 12v4c0 .6.4 1 1 1h2"></path><circle cx="7" cy="17" r="2"></circle><path d="M9 17h6"></path><circle cx="17" cy="17" r="2"></circle>',
		);
	}
endif;

if ( ! function_exists( 'emifree_applications' ) ) :
	function emifree_applications() {
		return array(
			array(
				'icon'        => 'cog',
				'title'       => 'CNC Machining & Tool Shops',
				'description' => 'Remove oil mist and coolant fumes from CNC lathes, mills, and routers without stopping production. Keep your machines clean and operators healthy.',
				'color'       => 'from-blue-700 to-blue-900',
				'question'    => 'How do I remove oil mist from my CNC machine without stopping production?',
			),
			array(
				'icon'        => 'flame',
				'title'       => 'Metalworking & Welding',
				'description' => 'Capture welding fumes, metal dust, and grinding particles at the source. Protect your workforce from harmful particulates and meet EU safety standards.',
				'color'       => 'from-cyan-600 to-cyan-800',
				'question'    => 'What is the best way to filter welding fumes in a small workshop?',
			),
			array(
				'icon'        => 'sparkles',
				'title'       => 'Grinding & Polishing',
				'description' => 'Extract abrasive dust from surface grinding, cylindrical grinding, and polishing operations. Extend equipment lifespan and improve surface finish quality.',
				'color'       => 'from-slate-600 to-slate-800',
				'question'    => 'How to control dust from surface grinding machines?',
			),
			array(
				'icon'        => 'wrench',
				'title'       => 'Bearing & Precision Parts Manufacturing',
				'description' => 'Maintain clean air in bearing production, gear manufacturing, and precision engineering. Protect sensitive components from contamination.',
				'color'       => 'from-blue-600 to-cyan-700',
				'question'    => 'Best air filtration for bearing manufacturing clean rooms',
			),
			array(
				'icon'        => 'factory',
				'title'       => 'Heavy Industry & Metal Fabrication',
				'description' => 'Handle high-volume dust and mist generation in fabrication shops, press rooms, and assembly lines. Industrial-grade filtration for demanding environments.',
				'color'       => 'from-slate-700 to-zinc-800',
				'question'    => 'Industrial dust extraction system for metal fabrication shop',
			),
			array(
				'icon'        => 'car',
				'title'       => 'Automotive',
				'description' => 'Capture oil mist, coolant aerosol, and welding fumes from automotive production lines. Keep paint booths particle-free and EV battery assembly clean room compliant.',
				'color'       => 'from-cyan-700 to-cyan-600',
				'question'    => 'Best air filtration system for automotive manufacturing plant oil mist and welding fume extraction',
			),
		);
	}
endif;