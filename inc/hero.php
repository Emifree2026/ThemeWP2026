<?php
/**
 * Hero data — English.
 *
 * Mirrors the strings in src/components/Hero.jsx. The German sibling
 * file is inc/hero_de.php (loaded automatically by
 * emifree_require_section_data() when the active language is 'de').
 *
 * The Hero section template (template-parts/section-hero.php) reads
 * these via $emifree_hero_data after requiring this file.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Return the English Hero data array.
 *
 * Shape (returned to the template):
 *   - 'headline'    (string)
 *   - 'tagline'     (string)
 *   - 'cta_label'    (string)
 *   - 'logos_label'  (string) — the small caps caption above the logos
 *   - 'logos'        (array<{name, file, max}>) — name, file in
 *     assets/logo_clients/, and inline max-width style
 */
function emifree_hero_data() {
	return array(
		'headline'   => 'Low maintenance filtration solutions',
		'tagline'    => 'Engineered to clean itself.',
		'cta_label'   => 'See how it works',
		'logos_label' => 'Trusted by industry leaders',
		'logos'       => array(
			array( 'name' => 'Mercedes-Benz', 'file' => 'mb_svg.svg',       'max' => 'clamp(28px, 4.5vw, 50px)' ),
			array( 'name' => 'BMW',           'file' => 'bmw.svg',          'max' => 'clamp(30px, 5vw, 55px)' ),
			array( 'name' => 'GM',            'file' => 'gm.svg',           'max' => 'clamp(30px, 5vw, 55px)' ),
			array( 'name' => 'NSK',           'file' => 'NSK.svg',          'max' => 'clamp(45px, 8vw, 100px)' ),
			array( 'name' => 'Knorr-Bremse',  'file' => 'knorr.svg',        'max' => 'clamp(60px, 11vw, 130px)' ),
			array( 'name' => 'Siemens',       'file' => 'siemens_logo.svg', 'max' => 'clamp(55px, 9vw, 100px)' ),
		),
	);
}
