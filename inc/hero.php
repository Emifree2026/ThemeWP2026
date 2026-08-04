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
 *   - 'headline'              (string) — the H1.
 *   - 'value_strip'           (array<{label, icon}>) — three short
 *                            keyword chips rendered as a horizontal
 *                            strip directly under the headline, each
 *                            as a pill-shaped button with a small
 *                            blue SVG icon prefix. The 'icon' field is
 *                            a 'kind' key the template maps to inline
 *                            SVG markup ('cycle', 'cartridge',
 *                            'hepa') — keeps the data array free of
 *                            raw SVG and the icon set extensible.
 *   - 'primary_cta_label'     (string) — text for the single dominant
 *                            button ("Contact Us!"). Anchors to #contact.
 *   - 'secondary_link_label'  (string) — text for the quiet underlined
 *                            link that sits between the value strip
 *                            and the primary CTA, routing to
 *                            #technology. NO competing button — one
 *                            CTA, one path.
 *   - 'logos_label'           (string) — the small caps caption above
 *                            the logos row.
 *   - 'logos'                 (array<{name, file, max}>) — name, file in
 *                            assets/logo_clients/, and inline max-width
 *                            style. Unchanged.
 */
function emifree_hero_data() {
	return array(
		'headline'              => 'Low maintenance air filtration solutions',
		'value_strip'           => array(
			array( 'label' => 'Self-cleaning',       'icon' => 'cycle' ),
			array( 'label' => 'No cartridge exchange', 'icon' => 'cartridge' ),
			array( 'label' => 'HEPA filter',          'icon' => 'hepa' ),
		),
		'primary_cta_label'     => 'Contact Us!',
		'secondary_link_label'  => 'See the technology behind it →',
		'logos_label'           => 'Trusted by industry leaders',
		'logos'                 => array(
			array( 'name' => 'Mercedes-Benz', 'file' => 'mb_svg.svg',       'max' => 'clamp(28px, 4.5vw, 50px)' ),
			array( 'name' => 'BMW',           'file' => 'bmw.svg',          'max' => 'clamp(30px, 5vw, 55px)' ),
			array( 'name' => 'GM',            'file' => 'gm.svg',           'max' => 'clamp(30px, 5vw, 55px)' ),
			array( 'name' => 'NSK',           'file' => 'NSK.svg',          'max' => 'clamp(45px, 8vw, 100px)' ),
			array( 'name' => 'Knorr-Bremse',  'file' => 'knorr.svg',        'max' => 'clamp(60px, 11vw, 130px)' ),
			array( 'name' => 'Siemens',       'file' => 'siemens_logo.svg', 'max' => 'clamp(55px, 9vw, 100px)' ),
		),
	);
}
