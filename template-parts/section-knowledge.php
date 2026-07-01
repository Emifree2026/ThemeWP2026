<?php
/**
 * Knowledge section — 3 tabs (Industry Insights / About Us / Downloads).
 *
 * Mirrors src/components/Knowledge.jsx from the React app post-cleanup
 * (no FAQ, no Latest Articles grid).
 *
 *  - Industry Insights: 2 FeaturedBlogCards (rendered via the shared
 *    emifree_featured_blog_card() helper in inc/blog-cards.php) + a
 *    "View All Articles" link to /blog/.
 *  - About Us: "Our Story" 2-column grid + stats, "Our Mission" 4 value
 *    cards, and a "Trusted by Industry Leaders" client-name strip.
 *  - Downloads: 4 catalog cards linking to /assets/catalog/<file>.pdf.
 *    Only 2 PDFs exist on disk in this commit (ECO AIR EN, ECO AIR DE);
 *    the EARIA EN + Full Range 2026 slots render as "coming soon"
 *    placeholders to preserve React parity. Plus a "Need Custom
 *    Documentation?" CTA banner linking to /#contact.
 *
 * Tab switching is handled by assets/js/sections/knowledge.js,
 * enqueued in functions.php when this section template is loaded.
 *
 * Card links (/blog/{slug}/) and the Contact CTA (/#contact) 404 in this
 * commit — known-limited, fixed in Pieces 15 + 16 + 9 respectively.
 */

require_once get_template_directory() . '/inc/knowledge.php';
require_once get_template_directory() . '/inc/blog-cards.php';
emifree_enqueue_section_script( 'knowledge' );

$emifree_knowledge_icons   = emifree_knowledge_icons();
$emifree_blog_posts        = emifree_blog_posts();
$emifree_catalog_pdfs      = emifree_catalog_pdfs();

// Tab config: key => [label, icon-key]. Drives the tab list; the panel
// IDs follow the same key (data-emifree-tab="blog" pairs with
// data-emifree-panel="blog").
$emifree_knowledge_tabs = array(
	'blog'      => array(
		'label' => 'Industry Insights',
		'icon'  => 'book-open',
	),
	'about'     => array(
		'label' => 'About Us',
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

		<?php /* ----- Section header ----- */ ?>
		<div class="text-center mb-16">
			<h2 class="text-4xl md:text-5xl font-bold text-zinc-900 mb-6">
				Resources &amp; <span class="text-blue-700">Knowledge</span>
			</h2>
			<p class="text-xl text-slate-600 max-w-3xl mx-auto">
				Explore our latest insights, company updates, and technical resources to stay informed about industrial air filtration.
			</p>
		</div>

		<?php /* ----- Tab list (3 tabs) ----- */ ?>
		<div class="flex flex-wrap justify-center gap-4 mb-12" role="tablist" aria-label="Knowledge center sections">
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
				Featured Articles
			</h3>

			<div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
				<?php foreach ( $emifree_blog_posts as $emifree_post ) : ?>
					<?php emifree_featured_blog_card( $emifree_post ); ?>
				<?php endforeach; ?>
			</div>

			<div class="text-center">
				<a
					href="/blog"
					class="inline-flex items-center gap-2 bg-white border-2 border-blue-700 text-blue-700 px-8 py-3 rounded-full font-semibold hover:bg-blue-700 hover:text-white transition-all duration-300 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2"
				>
					View All Articles
					<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true">
						<?php echo $emifree_knowledge_icons['arrow-right']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped — SVG markup, controlled. ?>
					</svg>
				</a>
			</div>
		</div>

		<?php /* ----- Panel B: About Us ----- */ ?>
		<div
			role="tabpanel"
			id="emifree-panel-about"
			aria-labelledby="emifree-tab-about"
			data-emifree-panel="about"
			class="hidden emifree-knowledge-panel"
		>
			<?php /* Our Story — 2-column grid */ ?>
			<div class="grid grid-cols-1 lg:grid-cols-2 gap-12 mb-16 items-center">
				<div>
					<h3 class="text-2xl md:text-3xl font-bold text-zinc-900 flex items-center gap-3 mb-6">
						<svg class="w-7 h-7 text-blue-700" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true">
							<?php echo $emifree_knowledge_icons['book-marked']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped — SVG markup, controlled. ?>
						</svg>
						Our Story
					</h3>
					<p class="text-lg text-slate-600 mb-6 leading-relaxed">
						Founded in Berlin in 2010, Emifree GmbH emerged from a vision to create filtration solutions that balance industrial performance with environmental responsibility. What started as a small engineering team focused on CNC machining applications has grown into a global leader in air filtration technology.
					</p>

					<div class="grid grid-cols-3 gap-6">
						<?php
						$emifree_stats = array(
							array( 'value' => '2010', 'label' => 'Founded' ),
							array( 'value' => '500+', 'label' => 'Clients Worldwide' ),
							array( 'value' => '15+',  'label' => 'Countries' ),
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
					<svg class="w-20 h-20 text-blue-700" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true">
						<?php echo $emifree_knowledge_icons['building-2']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped — SVG markup, controlled. ?>
					</svg>
					<p class="text-lg font-semibold text-zinc-900 mt-4">Based in Berlin, Germany</p>
					<p class="text-sm text-slate-600">Serving industry worldwide since 2010</p>
				</div>
			</div>

			<?php /* Our Mission — 4 value cards */ ?>
			<div class="mb-16 text-center">
				<h3 class="text-2xl md:text-3xl font-bold text-zinc-900 mb-4">Our Mission</h3>
				<p class="text-lg text-slate-600 max-w-3xl mx-auto mb-12">
					We believe that clean air is a fundamental right in every workplace. Our mission is to develop innovative filtration technologies that protect workers, reduce environmental impact, and help industries operate more sustainably.
				</p>

				<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 text-left">
					<?php
					$emifree_values = array(
						array(
							'icon'  => 'leaf',
							'title' => 'Sustainability',
							'desc'  => 'Designing products that minimize environmental footprint',
						),
						array(
							'icon'  => 'shield',
							'title' => 'Safety First',
							'desc'  => 'Protecting worker health with proven filtration efficiency',
						),
						array(
							'icon'  => 'settings',
							'title' => 'Innovation',
							'desc'  => 'Continuously improving our technology and processes',
						),
						array(
							'icon'  => 'target',
							'title' => 'Reliability',
							'desc'  => 'Delivering consistent performance our customers can trust',
						),
					);
					foreach ( $emifree_values as $emifree_value ) :
						?>
						<div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 hover:shadow-lg transition-all duration-300">
							<div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center mb-4">
								<svg class="w-6 h-6 text-blue-700" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true">
									<?php echo $emifree_knowledge_icons[ $emifree_value['icon'] ]; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped — SVG markup, controlled. ?>
								</svg>
							</div>
							<h4 class="font-semibold text-zinc-900 mb-2"><?php echo esc_html( $emifree_value['title'] ); ?></h4>
							<p class="text-sm text-slate-600"><?php echo esc_html( $emifree_value['desc'] ); ?></p>
						</div>
					<?php endforeach; ?>
				</div>
			</div>

			<?php /* Trusted by Industry Leaders — clients strip */ ?>
			<div class="bg-slate-100 rounded-3xl p-8 text-center">
				<h3 class="text-2xl md:text-3xl font-bold text-zinc-900 mb-6">Trusted by Industry Leaders</h3>
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
					<?php echo $emifree_knowledge_icons['book-open']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped — SVG markup, controlled. ?>
				</svg>
				Product Brochures
			</h3>

			<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
				<?php foreach ( $emifree_catalog_pdfs as $emifree_pdf ) : ?>
					<?php emifree_knowledge_pdf_card( $emifree_pdf, $emifree_knowledge_icons ); ?>
				<?php endforeach; ?>
			</div>

			<div class="bg-gradient-to-r from-blue-700 to-cyan-600 rounded-3xl p-8 md:p-12 text-white text-center">
				<h3 class="text-2xl md:text-3xl font-bold mb-4">Need Custom Documentation?</h3>
				<p class="text-lg text-blue-100 mb-8 max-w-2xl mx-auto">
					Can't find what you're looking for? Contact our technical team for custom datasheets, CAD drawings, or specific documentation for your application.
				</p>
				<a
					href="/#contact"
					class="inline-flex items-center gap-2 bg-white text-blue-700 px-8 py-4 rounded-full font-semibold hover:bg-blue-50 transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2"
				>
					Contact Technical Support
					<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true">
						<?php echo $emifree_knowledge_icons['arrow-right']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped — SVG markup, controlled. ?>
					</svg>
				</a>
			</div>
		</div>

	</div>
</section>
