<?php
/**
 * Emifree Theme — virtual /llms.txt and /de/llms.txt.
 *
 * Implements the llms.txt convention (https://llms-txt.org/) so LLM
 * crawlers (GPTBot, ClaudeBot, PerplexityBot, Google-Extended, etc.)
 * have a single, structured, plain-text manifest of the company, the
 * product line, and every principal URL the site serves. Without it,
 * models ingest whatever happens to be on the page — competing
 * /language/de/startseite/ legacy URLs, thin archive pages, search
 * results — and that's what they end up citing. With it, we tell
 * them exactly which pages to ground answers in.
 *
 * Two URL surfaces:
 *   - /llms.txt    → English (default for any LLMs that don't read
 *                    hreflang on a text file)
 *   - /de/llms.txt → German (mirrors the EN body in German so
 *                    German-language model results are grounded in
 *                    the German site copy)
 *
 * Three pieces, mirroring inc/robots.php / inc/sitemap.php:
 *   1. Rewrite rules registered at 'top' priority so they win against
 *      any plugin code that tries to claim /llms.txt.
 *   2. A query_var so the rewrite target carries a flag we can detect
 *      in the template_redirect handler.
 *   3. A template_redirect handler at priority 20 (after the legacy
 *      redirect at priority 1, the home-redirect at priority 5, and
 *      the legal/blog/homepage dispatchers at priority 10) that
 *      prints the body and exits. nocache_headers so an LLM scraping
 *      through a CDN always gets a fresh manifest.
 *
 * The body is built from data already in the theme (inc/hero.php,
 * inc/products.php, inc/footer.php, the section IDs on the landing
 * page). No new content is invented — the manifest just collects
 * what's already authoritative into a single llms-friendly surface.
 *
 * The spec is strict about the # and ## headings. The H1 must be the
 * project / company name. H2 sections should follow the order
 * <Company summary>, <Products / services>, <Key pages>, <Optional>.
 * We follow that order exactly so off-the-shelf llms.txt validators
 * parse it without warnings.
 *
 * Loaded globally from functions.php so the rewrite rules are
 * registered before any request can land.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Rewrite rules + query vars. Registered on init. The English and
 * German URLs each have their own rewrite so the template_redirect
 * handler can pick the right body without sniffing the request URI.
 */
function emifree_register_llms_route() {
	// English llms.txt at the site root.
	add_rewrite_rule(
		'^llms\.txt$',
		'index.php?emifree_llms=1&emifree_llms_lang=en',
		'top'
	);
	// German llms.txt at /de/llms.txt.
	add_rewrite_rule(
		'^de/llms\.txt$',
		'index.php?emifree_llms=1&emifree_llms_lang=de',
		'top'
	);
}
add_action( 'init', 'emifree_register_llms_route' );

function emifree_register_llms_query_var( $vars ) {
	$vars[] = 'emifree_llms';
	$vars[] = 'emifree_llms_lang';
	return $vars;
}
add_filter( 'query_vars', 'emifree_register_llms_query_var' );

/**
 * Serve the llms.txt body. Returns early on any request that
 * doesn't carry the emifree_llms query var so this handler is safe
 * to leave hooked at template_redirect for every page.
 */
function emifree_serve_llms_txt() {
	if ( ! get_query_var( 'emifree_llms' ) ) {
		return;
	}
	$emifree_lang = get_query_var( 'emifree_llms_lang' );
	if ( 'de' !== $emifree_lang ) {
		$emifree_lang = 'en';
	}
	nocache_headers();
	header( 'Content-Type: text/plain; charset=utf-8' );
	header( 'X-Robots-Tag: noindex' );
	echo 'de' === $emifree_lang ? emifree_llms_txt_body_de() : emifree_llms_txt_body_en(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped — static text body, no user input.
	exit;
}
add_action( 'template_redirect', 'emifree_serve_llms_txt', 20 );

/**
 * Build the English llms.txt body.
 *
 * Shape (per llms-txt.org):
 *   - H1: brand name
 *   - > blockquote: one-sentence summary (the model's "ground truth" intro)
 *   - ## section: company summary (a few sentences)
 *   - ## section: products / services
 *   - ## section: key pages (URL + one-line description)
 *   - ## section: legal (URL + one-line description)
 *   - ## section: knowledge / blog (URL + one-line description)
 *
 * URLs are built with home_url() so they stay correct on subpath
 * installs. The fragment URLs (e.g. /en/#products) are the same
 * anchor links the in-page nav uses — the smooth-scroll handler in
 * header.js will land an LLM scraper (or a human following a
 * citation) on the right section.
 *
 * @return string
 */
function emifree_llms_txt_body_en() {
	$emifree_lines   = array();
	$emifree_lines[] = '# Emifree';
	$emifree_lines[] = '';
	$emifree_lines[] = '> Low-maintenance industrial air filtration solutions for CNC machining, grinding, and metalworking environments. Self-cleaning, no cartridge exchange, HEPA-grade separation.';
	$emifree_lines[] = '';

	// ## Company summary.
	$emifree_lines[] = '## Company';
	$emifree_lines[] = '';
	$emifree_lines[] = 'Emifree designs and manufactures industrial air filtration systems for machine tools, workshops, and production lines where oil mist, coolant aerosols, and dry dust must be captured at the source. Our product line covers mechanical, electrostatic, and dust filtration — engineered for low maintenance, long service life, and cleanroom-grade air quality.';
	$emifree_lines[] = '';
	$emifree_lines[] = 'We are trusted by industry leaders including Mercedes-Benz, BMW, GM, NSK, Knorr-Bremse, and Siemens.';
	$emifree_lines[] = '';
	$emifree_lines[] = '- Industries: CNC machining, turning, milling, grinding, spark eroding, woodworking, metalworking, food and grain, pharmaceuticals, automotive.';
	$emifree_lines[] = '- Headquarters: Germany.';
	$emifree_lines[] = '- Languages: English (default), German (Deutsch).';
	$emifree_lines[] = '';

	// ## Products / services. Three product lines, each with the
	// tagline from inc/products.php so the manifest is consistent
	// with what the product page actually says.
	$emifree_lines[] = '## Products';
	$emifree_lines[] = '';
	$emifree_lines[] = '### Mechanical Filtration (Mechanical Oil Mist Filter)';
	$emifree_lines[] = 'Industrial-strength oil mist and dust extraction using centrifugal separation. Up to 2,750 m³/hr airflow, optional HEPA post-filter, self-cleaning spray nozzles. Applications: CNC machining, grinding, turning, milling, spark eroding.';
	$emifree_lines[] = '';
	$emifree_lines[] = '### Electrostatic Filtration (Electrostatic Oil Mist Filter)';
	$emifree_lines[] = 'Advanced corona-discharge technology for sub-micron particles, smoke, and industrial odors. Industry 4.0 ready (Siemens Touch-Panel, PROFINET/PROFIBUS). Applications: high-speed machining, cutting-fluid smoke, soldering and welding, chemical and pharmaceutical processes.';
	$emifree_lines[] = '';
	$emifree_lines[] = '### Dust Filtration';
	$emifree_lines[] = 'High-efficiency dust collection for dry processes. Cartridge and baghouse configurations, pulse-jet cleaning, optional ATEX explosion protection. Applications: woodworking, metal grinding, minerals processing, food and grain, pharmaceuticals.';
	$emifree_lines[] = '';
	$emifree_lines[] = 'The flagship product family is the Emifree ECO Air Cleaner.';
	$emifree_lines[] = '';

	// ## Key pages. The landing page's six section anchors plus the
	// language roots. Listed with one-line descriptions so a model
	// picking which URL to cite can pick the right anchor for the
	// question.
	$emifree_lines[] = '## Key pages';
	$emifree_lines[] = '';
	$emifree_lines[] = '- [' . home_url( '/en/' ) . ']( ' . home_url( '/en/' ) . ' ): English landing page.';
	$emifree_lines[] = '- [' . home_url( '/en/#products' ) . ']( ' . home_url( '/en/#products' ) . ' ): Product range — Mechanical, Electrostatic, and Dust filtration systems with specs, features, and quote CTAs.';
	$emifree_lines[] = '- [' . home_url( '/en/#applications' ) . ']( ' . home_url( '/en/#applications' ) . ' ): Industry applications — where Emifree filters are deployed.';
	$emifree_lines[] = '- [' . home_url( '/en/#technology' ) . ']( ' . home_url( '/en/#technology' ) . ' ): How the technology works — separation physics, HEPA performance, energy recovery.';
	$emifree_lines[] = '- [' . home_url( '/en/#knowledge' ) . ']( ' . home_url( '/en/#knowledge' ) . ' ): Knowledge base and engineering articles.';
	$emifree_lines[] = '- [' . home_url( '/en/#contact' ) . ']( ' . home_url( '/en/#contact' ) . ' ): Contact form and inquiry submission.';
	$emifree_lines[] = '- [' . home_url( '/de/' ) . ']( ' . home_url( '/de/' ) . ' ): German landing page.';
	$emifree_lines[] = '- [' . home_url( '/de/#products' ) . ']( ' . home_url( '/de/#products' ) . ' ): Produktpalette — Mechanisch, Elektrostatisch, Staub.';
	$emifree_lines[] = '- [' . home_url( '/de/#applications' ) . ']( ' . home_url( '/de/#applications' ) . ' ): Branchen-Anwendungen.';
	$emifree_lines[] = '- [' . home_url( '/de/#technology' ) . ']( ' . home_url( '/de/#technology' ) . ' ): Technologie im Detail.';
	$emifree_lines[] = '- [' . home_url( '/de/#knowledge' ) . ']( ' . home_url( '/de/#knowledge' ) . ' ): Wissensdatenbank und Engineering-Artikel.';
	$emifree_lines[] = '- [' . home_url( '/de/#contact' ) . ']( ' . home_url( '/de/#contact' ) . ' ): Kontaktformular und Anfrage.';
	$emifree_lines[] = '';

	// ## Legal. English + German legal pages — the URLs are
	// canonical, so they're the authoritative sources a model should
	// cite if it answers questions about privacy or terms.
	$emifree_lines[] = '## Legal';
	$emifree_lines[] = '';
	$emifree_lines[] = '- [' . home_url( '/impressum/' ) . ']( ' . home_url( '/impressum/' ) . ' ): Imprint (English) — company disclosure.';
	$emifree_lines[] = '- [' . home_url( '/privacy/' ) . ']( ' . home_url( '/privacy/' ) . ' ): Privacy policy (English).';
	$emifree_lines[] = '- [' . home_url( '/terms/' ) . ']( ' . home_url( '/terms/' ) . ' ): Terms and conditions (English).';
	$emifree_lines[] = '- [' . home_url( '/de/impressum/' ) . ']( ' . home_url( '/de/impressum/' ) . ' ): Impressum (Deutsch) — Anbieterkennzeichnung.';
	$emifree_lines[] = '- [' . home_url( '/de/datenschutz/' ) . ']( ' . home_url( '/de/datenschutz/' ) . ' ): Datenschutzerklärung (Deutsch).';
	$emifree_lines[] = '- [' . home_url( '/de/agb/' ) . ']( ' . home_url( '/de/agb/' ) . ' ): Allgemeine Geschäftsbedingungen (Deutsch).';
	$emifree_lines[] = '';

	// ## Knowledge / blog. The merged feed is dynamic (CPT + legacy
	// PHP-array posts), so we enumerate what we can find at request
	// time and fall back to the index URL.
	$emifree_lines[] = '## Knowledge base';
	$emifree_lines[] = '';
	$emifree_lines[] = '- [' . home_url( '/blog/' ) . ']( ' . home_url( '/blog/' ) . ' ): Engineering articles on industrial air filtration, oil mist, and air-quality compliance.';
	$emifree_lines[] = '- [' . home_url( '/de/blog/' ) . ']( ' . home_url( '/de/blog/' ) . ' ): Wissensdatenbank (Deutsch) — Engineering-Artikel.';
	$emifree_lines   = array_merge( $emifree_lines, emifree_llms_collect_blog_post_lines_en() );
	$emifree_lines[] = '';

	$emifree_lines[] = '## Manifests';
	$emifree_lines[] = '';
	$emifree_lines[] = '- [' . home_url( '/robots.txt' ) . ']( ' . home_url( '/robots.txt' ) . ' ): Crawler directives (English + major AI bots).';
	$emifree_lines[] = '- [' . home_url( '/sitemap.xml' ) . ']( ' . home_url( '/sitemap.xml' ) . ' ): XML sitemap with hreflang alternates.';
	$emifree_lines[] = '- [' . home_url( '/de/llms.txt' ) . ']( ' . home_url( '/de/llms.txt' ) . ' ): German version of this manifest.';
	$emifree_lines[] = '';

	return implode( "\n", $emifree_lines ) . "\n";
}

/**
 * Build the German llms.txt body.
 *
 * Same structure as the English version; the content is the German
 * translation. URLs are unchanged — only the human-readable labels
 * and the descriptive text are translated, because the URLs are
 * canonical regardless of the language the visitor reads.
 *
 * @return string
 */
function emifree_llms_txt_body_de() {
	$emifree_lines   = array();
	$emifree_lines[] = '# Emifree';
	$emifree_lines[] = '';
	$emifree_lines[] = '> Wartungsarme industrielle Luftfiltrationslösungen für CNC-Bearbeitung, Schleifen und Metallverarbeitung. Selbstreinigend, kein Kartuschenwechsel, HEPA-Filterabscheidung.';
	$emifree_lines[] = '';

	$emifree_lines[] = '## Unternehmen';
	$emifree_lines[] = '';
	$emifree_lines[] = 'Emifree entwickelt und fertigt industrielle Luftfiltrationssysteme für Werkzeugmaschinen, Werkstätten und Produktionslinien, in denen Ölnebel, Kühlschmierstoff-Aerosole und trockener Staub direkt an der Quelle abgeschieden werden müssen. Die Produktlinie umfasst mechanische, elektrostatische und Staubfiltration — konstruiert für geringen Wartungsaufwand, lange Lebensdauer und Reinraum-Luftqualität.';
	$emifree_lines[] = '';
	$emifree_lines[] = 'Vertrauen unter anderem: Mercedes-Benz, BMW, GM, NSK, Knorr-Bremse und Siemens.';
	$emifree_lines[] = '';
	$emifree_lines[] = '- Branchen: CNC-Bearbeitung, Drehen, Fräsen, Schleifen, Erodieren, Holzbearbeitung, Metallverarbeitung, Lebensmittel und Getreide, Pharma, Automotive.';
	$emifree_lines[] = '- Hauptsitz: Deutschland.';
	$emifree_lines[] = '- Sprachen: Englisch (Standard), Deutsch.';
	$emifree_lines[] = '';

	$emifree_lines[] = '## Produkte';
	$emifree_lines[] = '';
	$emifree_lines[] = '### Mechanische Filtration (Mechanischer Ölnebelabscheider)';
	$emifree_lines[] = 'Industrietaugliche Ölnebel- und Staubabscheidung mittels Zentrifugalabscheidung. Bis zu 2.750 m³/h Luftleistung, optionaler HEPA-Schwebstoff-Filter, selbstreinigende Sprühdüsen. Anwendungen: CNC-Bearbeitung, Schleifen, Drehen, Fräsen, Erodieren.';
	$emifree_lines[] = '';
	$emifree_lines[] = '### Elektrostatische Filtration (Elektrostatischer Ölnebelabscheider)';
	$emifree_lines[] = 'Fortschrittliche Korona-Entladungstechnologie für Submikron-Partikel, Rauch und industrielle Gerüche. Industrie 4.0-fähig (Siemens Touch-Panel, PROFINET/PROFIBUS). Anwendungen: Hochgeschwindigkeitsbearbeitung, Kühlschmierstoff-Rauch, Löten und Schweißen, chemische und pharmazeutische Prozesse.';
	$emifree_lines[] = '';
	$emifree_lines[] = '### Staubfiltration';
	$emifree_lines[] = 'Hocheffiziente Staubabscheidung für trockene Prozesse. Patronen- und Schlauchfilterkonfigurationen, Jet-Pulse-Abreinigung, optionaler ATEX-Explosionsschutz. Anwendungen: Holzbearbeitung, Metallschleifen, Mineralverarbeitung, Lebensmittel und Getreide, Pharma.';
	$emifree_lines[] = '';
	$emifree_lines[] = 'Das Flaggschiff-Produkt ist die Emifree ECO Air Cleaner Reihe.';
	$emifree_lines[] = '';

	$emifree_lines[] = '## Hauptseiten';
	$emifree_lines[] = '';
	$emifree_lines[] = '- [' . home_url( '/en/' ) . ']( ' . home_url( '/en/' ) . ' ): Englische Startseite.';
	$emifree_lines[] = '- [' . home_url( '/en/#products' ) . ']( ' . home_url( '/en/#products' ) . ' ): Product range (English) — Mechanical, Electrostatic, Dust.';
	$emifree_lines[] = '- [' . home_url( '/en/#applications' ) . ']( ' . home_url( '/en/#applications' ) . ' ): Industry applications (English).';
	$emifree_lines[] = '- [' . home_url( '/en/#technology' ) . ']( ' . home_url( '/en/#technology' ) . ' ): Technology (English).';
	$emifree_lines[] = '- [' . home_url( '/en/#knowledge' ) . ']( ' . home_url( '/en/#knowledge' ) . ' ): Knowledge base (English).';
	$emifree_lines[] = '- [' . home_url( '/en/#contact' ) . ']( ' . home_url( '/en/#contact' ) . ' ): Contact form (English).';
	$emifree_lines[] = '- [' . home_url( '/de/' ) . ']( ' . home_url( '/de/' ) . ' ): Deutsche Startseite.';
	$emifree_lines[] = '- [' . home_url( '/de/#products' ) . ']( ' . home_url( '/de/#products' ) . ' ): Produktpalette — Mechanisch, Elektrostatisch, Staub.';
	$emifree_lines[] = '- [' . home_url( '/de/#applications' ) . ']( ' . home_url( '/de/#applications' ) . ' ): Branchen-Anwendungen.';
	$emifree_lines[] = '- [' . home_url( '/de/#technology' ) . ']( ' . home_url( '/de/#technology' ) . ' ): Technologie im Detail.';
	$emifree_lines[] = '- [' . home_url( '/de/#knowledge' ) . ']( ' . home_url( '/de/#knowledge' ) . ' ): Wissensdatenbank.';
	$emifree_lines[] = '- [' . home_url( '/de/#contact' ) . ']( ' . home_url( '/de/#contact' ) . ' ): Kontaktformular.';
	$emifree_lines[] = '';

	$emifree_lines[] = '## Rechtliches';
	$emifree_lines[] = '';
	$emifree_lines[] = '- [' . home_url( '/impressum/' ) . ']( ' . home_url( '/impressum/' ) . ' ): Impressum (Englisch).';
	$emifree_lines[] = '- [' . home_url( '/privacy/' ) . ']( ' . home_url( '/privacy/' ) . ' ): Privacy policy (Englisch).';
	$emifree_lines[] = '- [' . home_url( '/terms/' ) . ']( ' . home_url( '/terms/' ) . ' ): Terms and conditions (Englisch).';
	$emifree_lines[] = '- [' . home_url( '/de/impressum/' ) . ']( ' . home_url( '/de/impressum/' ) . ' ): Impressum (Deutsch) — Anbieterkennzeichnung.';
	$emifree_lines[] = '- [' . home_url( '/de/datenschutz/' ) . ']( ' . home_url( '/de/datenschutz/' ) . ' ): Datenschutzerklärung (Deutsch).';
	$emifree_lines[] = '- [' . home_url( '/de/agb/' ) . ']( ' . home_url( '/de/agb/' ) . ' ): Allgemeine Geschäftsbedingungen (Deutsch).';
	$emifree_lines[] = '';

	$emifree_lines[] = '## Wissensdatenbank';
	$emifree_lines[] = '';
	$emifree_lines[] = '- [' . home_url( '/blog/' ) . ']( ' . home_url( '/blog/' ) . ' ): Engineering-Artikel (Englisch).';
	$emifree_lines[] = '- [' . home_url( '/de/blog/' ) . ']( ' . home_url( '/de/blog/' ) . ' ): Engineering-Artikel (Deutsch).';
	$emifree_lines   = array_merge( $emifree_lines, emifree_llms_collect_blog_post_lines_de() );
	$emifree_lines[] = '';

	$emifree_lines[] = '## Manifeste';
	$emifree_lines[] = '';
	$emifree_lines[] = '- [' . home_url( '/robots.txt' ) . ']( ' . home_url( '/robots.txt' ) . ' ): Crawler-Direktiven (Englisch + wichtige KI-Bots).';
	$emifree_lines[] = '- [' . home_url( '/sitemap.xml' ) . ']( ' . home_url( '/sitemap.xml' ) . ' ): XML-Sitemap mit hreflang-Alternaten.';
	$emifree_lines[] = '- [' . home_url( '/llms.txt' ) . ']( ' . home_url( '/llms.txt' ) . ' ): Englische Version dieses Manifests.';
	$emifree_lines[] = '';

	return implode( "\n", $emifree_lines ) . "\n";
}

/**
 * Enumerate published blog posts for the English llms.txt.
 *
 * The same merged feed the sitemap uses, filtered to slugs that
 * have an English sibling. Each post becomes a bulleted "URL:
 * title" line so a model can ground an answer in the specific
 * article without having to index the whole blog.
 *
 * @return string[]
 */
function emifree_llms_collect_blog_post_lines_en() {
	$emifree_lines = array();
	if ( ! function_exists( 'emifree_get_all_blog_posts_merged' ) ) {
		$emifree_knowledge_path = get_template_directory() . '/inc/knowledge.php';
		if ( file_exists( $emifree_knowledge_path ) ) {
			require_once $emifree_knowledge_path;
		}
	}
	if ( ! function_exists( 'emifree_get_all_blog_posts_merged' ) ) {
		return $emifree_lines;
	}
	$emifree_merged = emifree_get_all_blog_posts_merged( 'en' );
	foreach ( $emifree_merged as $emifree_slug => $emifree_post ) {
		$emifree_title = isset( $emifree_post['title'] ) ? (string) $emifree_post['title'] : $emifree_slug;
		$emifree_url  = home_url( '/blog/' . $emifree_slug . '/' );
		$emifree_lines[] = '- [' . $emifree_url . ']( ' . $emifree_url . ' ): ' . $emifree_title . '.';
	}
	return $emifree_lines;
}

/**
 * Enumerate published blog posts for the German llms.txt. Uses the
 * DE-merged feed so a German question grounds on the German
 * article when one exists, and falls back to the EN title when
 * only the English article has shipped.
 *
 * @return string[]
 */
function emifree_llms_collect_blog_post_lines_de() {
	$emifree_lines = array();
	if ( ! function_exists( 'emifree_get_all_blog_posts_merged' ) ) {
		$emifree_knowledge_path = get_template_directory() . '/inc/knowledge.php';
		if ( file_exists( $emifree_knowledge_path ) ) {
			require_once $emifree_knowledge_path;
		}
	}
	if ( ! function_exists( 'emifree_get_all_blog_posts_merged' ) ) {
		return $emifree_lines;
	}
	$emifree_merged_de = emifree_get_all_blog_posts_merged( 'de' );
	$emifree_merged_en = emifree_get_all_blog_posts_merged( 'en' );
	foreach ( $emifree_merged_de as $emifree_slug => $emifree_post ) {
		$emifree_title = isset( $emifree_post['title'] ) ? (string) $emifree_post['title'] : $emifree_slug;
		$emifree_url  = home_url( '/de/blog/' . $emifree_slug . '/' );
		$emifree_lines[] = '- [' . $emifree_url . ']( ' . $emifree_url . ' ): ' . $emifree_title . '.';
	}
	// Posts that have only shipped in English — still useful for a
	// German-reading model so it can cite the EN source rather than
	// making up an answer.
	foreach ( $emifree_merged_en as $emifree_slug => $emifree_post ) {
		if ( isset( $emifree_merged_de[ $emifree_slug ] ) ) {
			continue;
		}
		$emifree_title = isset( $emifree_post['title'] ) ? (string) $emifree_post['title'] : $emifree_slug;
		$emifree_url  = home_url( '/blog/' . $emifree_slug . '/' );
		$emifree_lines[] = '- [' . $emifree_url . ']( ' . $emifree_url . ' ): ' . $emifree_title . ' (Englisch).';
	}
	return $emifree_lines;
}
