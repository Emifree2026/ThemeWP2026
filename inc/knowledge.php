<?php
/**
 * Knowledge data + SVG icons.
 *
 * Mirrors src/components/Knowledge.jsx and src/data/blogPosts.jsx from
 * the React app post-cleanup (no FAQ, no Latest Articles grid). Three
 * panels (blog / about / downloads):
 *
 *  - emifree_knowledge_icons() — name => inner-SVG map for every
 *    lucide icon used by the section (tab labels, panel headings,
 *    card icons, FeaturedBlogCard meta row).
 *  - emifree_blog_posts() — the 2 real articles from blogPosts.jsx
 *    (Precision in Every Breath; The Strategic Edge of Clean Air).
 *    Only metadata + a one-paragraph body preview; the full body
 *    ports with Pieces 15 + 16.
 *  - emifree_catalog_pdfs() — 4 catalog entries. Only 2 PDFs exist
 *    on disk in this commit (ECO AIR EN, ECO AIR DE in assets/catalog/);
 *    the EARIA EN + Full Range 2026 slots render as "coming soon"
 *    placeholders to preserve React parity.
 *
 * Icons are inline SVG paths from lucide-react (24×24 viewBox,
 * stroke-based) — no external icon library required.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'emifree_knowledge_icons' ) ) :
	function emifree_knowledge_icons() {
		return array(
			'book-open'      => '<path d="M12 7v14"></path><path d="M3 18a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1h7a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H4a1 1 0 0 1-1-1z"></path><path d="M21 18a1 1 0 0 0 1-1V4a1 1 0 0 0-1-1h-7a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h7a1 1 0 0 0 1-1z"></path>',
			'users'          => '<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M22 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path>',
			'download'       => '<path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line>',
			'award'          => '<circle cx="12" cy="8" r="6"></circle><path d="M15.477 12.89 17 22l-5-3-5 3 1.523-9.11"></path>',
			'book-marked'    => '<path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H19a1 1 0 0 1 1 1v18a1 1 0 0 1-1 1H6.5a1 1 0 0 1 0-5H20"></path><polyline points="9 10 11 12 13 10"></polyline>',
			'building-2'     => '<path d="M10 12h4"></path><path d="M10 8h4"></path><path d="M14 21v-4a2 2 0 0 0-4 0v4"></path><path d="M6 10H4a2 2 0 0 0-2 2v7a2 2 0 0 0 2 2h2"></path><path d="M22 19h-2a2 2 0 0 1-2-2v-7a2 2 0 0 1 2-2h2"></path><path d="M18 21V5a2 2 0 0 0-2-2H8a2 2 0 0 0-2 2v16"></path>',
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
	}
endif;

if ( ! function_exists( 'emifree_blog_posts' ) ) :
	function emifree_blog_posts() {
		return array(
			'the-strategic-edge-of-clean-air' => array(
				'id'            => '1',
				'slug'          => 'the-strategic-edge-of-clean-air',
				'title'         => 'The Strategic Edge of Clean Air: Why High-Performance Oil Mist Filtration is Essential for Modern Machining',
				'excerpt'       => 'Industrial oil mist filtration is not an accessory — it is a strategic investment in workplace safety, equipment longevity, and operational efficiency for high-precision machining environments.',
				'category'      => 'Technical Guide',
				'date'          => '2026-06-29',
				'formatted_date'=> 'June 29, 2026',
				'read_time'     => '5 min read',
				'author'        => 'Victoria Pedroza',
				'author_role'   => 'Product Manager, Emifree GmbH',
				'hero_image'    => 'Factory_floor_with_CNC_.webp',
				'body_preview'  => 'In modern precision manufacturing, factory air quality is no longer a peripheral concern. The air inside a workshop directly influences equipment reliability, regulatory compliance, and — most critically — workforce health. An industrial oil mist collector positioned at each machine tool is a strategic investment, not an accessory.',
			),
			'precision-in-every-breath' => array(
				'id'            => '2',
				'slug'          => 'precision-in-every-breath',
				'title'         => 'Precision in Every Breath: A Technical Guide to Industrial Oil Mist Filtration',
				'excerpt'       => 'A technical comparison of mechanical and electrostatic oil mist filtration technologies — and how source-capture extraction protects your workforce, machinery, and the bottom line.',
				'category'      => 'Technical Guide',
				'date'          => '2026-06-29',
				'formatted_date'=> 'June 29, 2026',
				'read_time'     => '7 min read',
				'author'        => 'Victoria Pedroza',
				'author_role'   => 'Product Manager, Emifree GmbH',
				'hero_image'    => 'CNC_2.jpg',
				'body_preview'  => 'For facility managers and production engineers, factory air quality is an operational necessity — not a checkbox. Oil mist generated by high-speed machining, grinding, and turning operations has measurable impact on health outcomes, machine uptime, and the bottom line. This article walks through how oil mist forms, why source-capture filtration matters, and how to choose between mechanical and electrostatic separation.',
			),
		);
	}
endif;

if ( ! function_exists( 'emifree_catalog_pdfs' ) ) :
	function emifree_catalog_pdfs() {
		$emifree_catalog_uri = get_template_directory_uri() . '/assets/catalog/';
		return array(
			array(
				'name'      => 'ECO AIR Cleaner Catalog',
				'filename'  => 'emifree_eco_air_cleaner_catalog__1.pdf',
				'size'      => '2.9 MB',
				'lang'      => 'EN',
				'available' => true,
				'url'       => $emifree_catalog_uri . 'emifree_eco_air_cleaner_catalog__1.pdf',
			),
			array(
				'name'      => 'ECO AIR Cleaner Katalog',
				'filename'  => 'kat_emi_de_.pdf',
				'size'      => '5.0 MB',
				'lang'      => 'DE',
				'available' => true,
				'url'       => $emifree_catalog_uri . 'kat_emi_de_.pdf',
			),
			array(
				'name'      => 'EARIA Electrostatic Catalog',
				'filename'  => 'earia-catalog-en.pdf',
				'size'      => '3.8 MB',
				'lang'      => 'EN',
				'available' => false,
				'url'       => '',
			),
			array(
				'name'      => 'Full Product Range 2026',
				'filename'  => 'full-range-2026.pdf',
				'size'      => '12.5 MB',
				'lang'      => 'EN',
				'available' => false,
				'url'       => '',
			),
		);
	}
endif;

if ( ! function_exists( 'emifree_knowledge_pdf_card' ) ) :
	/**
	 * Render a single catalog card.
	 *
	 * Branches on the entry's `available` flag: a real `<a download>` when
	 * the PDF exists on disk, or a non-interactive `<div aria-disabled>` with
	 * opacity-60 + "(coming soon)" copy when it doesn't. This preserves
	 * React parity for the 4-card grid without fabricating file URLs.
	 *
	 * @param array $emifree_pdf   Catalog entry from emifree_catalog_pdfs().
	 * @param array $emifree_icons Icon map from emifree_knowledge_icons().
	 */
	function emifree_knowledge_pdf_card( $emifree_pdf, $emifree_icons ) {
		$emifree_has_link = ! empty( $emifree_pdf['available'] ) && ! empty( $emifree_pdf['url'] );

		if ( $emifree_has_link ) {
			$emifree_open = '<a href="' . esc_url( $emifree_pdf['url'] ) . '" download class="bg-white rounded-xl p-6 shadow-sm border border-slate-100 hover:shadow-lg hover:border-blue-200 transition-all duration-300 group block focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2">';
			$emifree_close = '</a>';
		} else {
			$emifree_open = '<div class="bg-white rounded-xl p-6 shadow-sm border border-slate-100 opacity-60 cursor-not-allowed" aria-disabled="true">';
			$emifree_close = '</div>';
		}

		echo $emifree_open; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped — controlled opening tag (anchor or div).
		?>
		<div class="w-12 h-12 bg-blue-100 <?php echo $emifree_has_link ? 'group-hover:bg-blue-700' : ''; ?> rounded-xl flex items-center justify-center mb-4 transition-colors duration-300">
			<svg class="w-6 h-6 text-blue-700 <?php echo $emifree_has_link ? 'group-hover:text-white' : ''; ?> transition-colors duration-300" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true">
				<?php echo $emifree_icons['file-text']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped — SVG markup, controlled. ?>
			</svg>
		</div>
		<h4 class="text-zinc-900 font-semibold mb-2 leading-snug">
			<?php echo esc_html( $emifree_pdf['name'] ); ?>
			<?php if ( ! $emifree_has_link ) : ?>
				<span class="text-slate-500 font-normal">(coming soon)</span>
			<?php endif; ?>
		</h4>
		<div class="flex items-center justify-between text-sm text-slate-500">
			<span><?php echo esc_html( $emifree_pdf['size'] ); ?></span>
			<span class="bg-slate-100 px-2 py-0.5 rounded text-xs font-semibold"><?php echo esc_html( $emifree_pdf['lang'] ); ?></span>
		</div>
		<?php
		echo $emifree_close; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped — controlled closing tag.
	}
endif;
