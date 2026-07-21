<?php
/**
 * Legal page content — Impressum, Privacy Policy, General Terms.
 *
 * Each function returns a PHP array with the page's:
 *   - title (for <title> and OG)
 *   - description (for meta description and OG)
 *   - url (canonical + og:url)
 *   - content (rendered HTML, semantic structure)
 *   - schema (the per-page JSON-LD WebPage data)
 *
 * Content mirrors src/pages/{Impressum,Privacy,Terms}.jsx from the
 * React app. Privacy page intentionally has NO Cookiebot callout
 * (the React external edit removed it; preserving that state here).
 *
 * Templates (template-parts/page-impressum.php, etc.) call
 * emifree_legal_page( 'impressum' ), emifree_seo_page(),
 * emifree_render_legal_body() in sequence.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Fetch a legal page's metadata by slug.
 *
 * @return array{title:string,description:string,url:string,content:string,schema:array}|null
 */
function emifree_legal_page( $slug, $lang = 'en' ) {
	switch ( $lang ) {
		case 'de':
			return emifree_legal_page_de( $slug );
		default:
			return emifree_legal_page_en( $slug );
	}
}

function emifree_legal_page_en( $slug ) {
	switch ( $slug ) {
		case 'impressum':
			return array(
				'title'       => 'Impressum · Emifree GmbH',
				'description' => 'Legal notice for Emifree GmbH, Berlin — Managing Director Ingo Wagner, HRB 133977 B, VAT DE 815286735.',
				'url'         => home_url() . '/impressum',
				'lang'        => 'en',
				'lang_slug'   => 'impressum',
				'de_slug'     => 'impressum',
				'de_url'      => home_url() . '/de/impressum',
				'schema'      => array(
					'@context'      => 'https://schema.org',
					'@type'         => 'WebPage',
					'name'          => 'Impressum',
					'url'           => home_url() . '/impressum',
					'inLanguage'    => 'en',
					'description'   => 'Legal notice for Emifree GmbH, Berlin — Managing Director Ingo Wagner, HRB 133977 B, VAT DE 815286735.',
					'publisher'     => array(
						'@type' => 'Organization',
						'name'  => 'Emifree GmbH',
						'url'   => home_url(),
					),
				),
			);
		case 'privacy':
			return array(
				'title'       => 'Privacy Policy · Emifree GmbH',
				'description' => 'Privacy policy for the Emifree GmbH website — GDPR-compliant notice on data collection, processing, your rights, and the cookies/plugins we use.',
				'url'         => home_url() . '/privacy',
				'lang'        => 'en',
				'lang_slug'   => 'privacy',
				'de_slug'     => 'datenschutz',
				'de_url'      => home_url() . '/de/datenschutz',
				'schema'      => array(
					'@context'      => 'https://schema.org',
					'@type'         => 'WebPage',
					'name'          => 'Privacy Policy',
					'url'           => home_url() . '/privacy',
					'inLanguage'    => 'en',
					'description'   => 'Privacy policy for the Emifree GmbH website — GDPR-compliant notice on data collection, processing, your rights, and the cookies/plugins we use.',
					'publisher'     => array(
						'@type' => 'Organization',
						'name'  => 'Emifree GmbH',
						'url'   => home_url(),
					),
				),
			);
		case 'terms':
			return array(
				'title'       => 'General Terms and Conditions (GTC) · Emifree GmbH',
				'description' => 'Emifree GmbH General Terms and Conditions (GTC) for B2B sales of industrial air filtration systems. Applicable law: Federal Republic of Germany. Exclusive jurisdiction: Berlin.',
				'url'         => home_url() . '/terms',
				'lang'        => 'en',
				'lang_slug'   => 'terms',
				'de_slug'     => 'agb',
				'de_url'      => home_url() . '/de/agb',
				'schema'      => array(
					'@context'      => 'https://schema.org',
					'@type'         => 'WebPage',
					'name'          => 'General Terms and Conditions',
					'url'           => home_url() . '/terms',
					'inLanguage'    => 'en',
					'description'   => 'Emifree GmbH General Terms and Conditions (GTC) for B2B sales of industrial air filtration systems. Applicable law: Federal Republic of Germany. Exclusive jurisdiction: Berlin.',
					'publisher'     => array(
						'@type' => 'Organization',
						'name'  => 'Emifree GmbH',
						'url'   => home_url(),
					),
				),
			);
	}
	return null;
}

function emifree_legal_page_de( $slug ) {
	switch ( $slug ) {
		case 'impressum':
			return array(
				'title'       => 'Impressum · Emifree GmbH',
				'description' => 'Rechtliche Hinweise der Emifree GmbH, Berlin — Geschäftsführer Ingo Wagner, HRB 133977 B, USt-IdNr. DE 815286735.',
				'url'         => home_url() . '/de/impressum',
				'lang'        => 'de',
				'lang_slug'   => 'impressum',
				'en_slug'     => 'impressum',
				'en_url'      => home_url() . '/impressum',
				'schema'      => array(
					'@context'      => 'https://schema.org',
					'@type'         => 'WebPage',
					'name'          => 'Impressum',
					'url'           => home_url() . '/de/impressum',
					'inLanguage'    => 'de',
					'description'   => 'Rechtliche Hinweise der Emifree GmbH, Berlin — Geschäftsführer Ingo Wagner, HRB 133977 B, USt-IdNr. DE 815286735.',
					'publisher'     => array(
						'@type' => 'Organization',
						'name'  => 'Emifree GmbH',
						'url'   => home_url(),
					),
				),
			);
		case 'datenschutz':
			return array(
				'title'       => 'Datenschutzerklärung · Emifree GmbH',
				'description' => 'Datenschutzerklärung der Emifree GmbH — DSGVO-konforme Hinweise zur Erhebung, Verarbeitung und Speicherung personenbezogener Daten sowie zu Ihren Rechten als betroffene Person.',
				'url'         => home_url() . '/de/datenschutz',
				'lang'        => 'de',
				'lang_slug'   => 'datenschutz',
				'en_slug'     => 'privacy',
				'en_url'      => home_url() . '/privacy',
				'schema'      => array(
					'@context'      => 'https://schema.org',
					'@type'         => 'WebPage',
					'name'          => 'Datenschutzerklärung',
					'url'           => home_url() . '/de/datenschutz',
					'inLanguage'    => 'de',
					'description'   => 'Datenschutzerklärung der Emifree GmbH — DSGVO-konforme Hinweise zur Erhebung, Verarbeitung und Speicherung personenbezogener Daten sowie zu Ihren Rechten als betroffene Person.',
					'publisher'     => array(
						'@type' => 'Organization',
						'name'  => 'Emifree GmbH',
						'url'   => home_url(),
					),
				),
			);
		case 'agb':
			return array(
				'title'       => 'Allgemeine Geschäftsbedingungen (AGB) · Emifree GmbH',
				'description' => 'Allgemeine Geschäftsbedingungen (AGB) der Emifree GmbH für den B2B-Vertrieb industrieller Filteranlagen. Anwendbares Recht: Bundesrepublik Deutschland. Ausschließlicher Gerichtsstand: Berlin.',
				'url'         => home_url() . '/de/agb',
				'lang'        => 'de',
				'lang_slug'   => 'agb',
				'en_slug'     => 'terms',
				'en_url'      => home_url() . '/terms',
				'schema'      => array(
					'@context'      => 'https://schema.org',
					'@type'         => 'WebPage',
					'name'          => 'Allgemeine Geschäftsbedingungen (AGB)',
					'url'           => home_url() . '/de/agb',
					'inLanguage'    => 'de',
					'description'   => 'Allgemeine Geschäftsbedingungen (AGB) der Emifree GmbH für den B2B-Vertrieb industrieller Filteranlagen. Anwendbares Recht: Bundesrepublik Deutschland. Ausschließlicher Gerichtsstand: Berlin.',
					'publisher'     => array(
						'@type' => 'Organization',
						'name'  => 'Emifree GmbH',
						'url'   => home_url(),
					),
				),
			);
	}
	return null;
}

/**
 * Render the body HTML for a legal page. The actual content is
 * inline below per slug — there's enough shared structure (page
 * header band, semantic article body, back-to-home footer) that
 * this single helper is preferable to three template parts.
 */
function emifree_render_legal_body( $slug, $lang = 'en' ) {
	// Prefer Markdown sources in assets/Legal when available. Filenames follow
	// the pattern: impressum_en.md, impressum_de.md, privacy_policy_en.md, etc.
	$map = array(
		'impressum' => 'impressum',
		'privacy'   => 'privacy_policy',
		'terms'     => 'terms',
		'datenschutz' => 'privacy_policy',
		'agb'         => 'terms',
	);
	$base = isset( $map[ $slug ] ) ? $map[ $slug ] : $slug;
	$md_file = get_template_directory() . '/assets/Legal/' . $base . '_' . $lang . '.md';
	if ( file_exists( $md_file ) ) {
		$md = file_get_contents( $md_file );
		if ( false !== $md ) {
			// Convert a small subset of Markdown to safe HTML for these pages.
			$html = emifree_simple_markdown_to_html( $md );
			return '<article class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-12 text-zinc-700">' . $html . '</article>';
		}
	}
	if ( 'de' === $lang ) {
		switch ( $slug ) {
			case 'impressum':
				return emifree_render_impressum_de_body( $lang );
			case 'datenschutz':
				return emifree_render_datenschutz_de_body( $lang );
			case 'agb':
				return emifree_render_agb_de_body( $lang );
		}
		return '';
	}
	switch ( $slug ) {
		case 'impressum':
			return emifree_render_impressum_body( $lang );

		case 'privacy':
			return emifree_render_privacy_body( $lang );

		case 'terms':
			return emifree_render_terms_body( $lang );
	}
	return '';
}

/* -------------------------------------------------------------------------
 * Individual page body renderers.
 *
 * Output is composed via string concatenation (ob_* buffering) so we
 * don't tangle PHP tags inside HTML — cleaner than mixing them.
 * ------------------------------------------------------------------------- */

function emifree_render_impressum_body( $lang = 'en' ): string {
	$emifree_back_to_root = ( 'de' === $lang ) ? home_url( '/de/' ) : home_url( '/en/' );
	ob_start();
	?>
	<article class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-12 text-zinc-700">
		<p class="text-lg leading-relaxed mb-6">
			<strong>Information pursuant to § 5 TMG (German Telemedia Act) / § 2 DL-InfoV:</strong>
		</p>
		<p class="text-lg leading-relaxed mb-10">
			Emifree GmbH Produktion von Filteranlagen<br>
			Pestalozzistraße 13<br>
			12557 Berlin, Germany
		</p>

		<h2 class="text-2xl font-bold text-zinc-900 mt-10 mb-4">Represented by the Managing Director</h2>
		<p class="text-lg leading-relaxed mb-6">Ingo Wagner</p>

		<h2 class="text-2xl font-bold text-zinc-900 mt-10 mb-4">Contact Information</h2>
		<ul class="text-lg leading-relaxed mb-6 space-y-1">
			<li><strong>Phone:</strong> <a href="tel:+493076283520" class="text-blue-700 hover:text-blue-800">+49 3076283520</a></li>
			<li><strong>E-Mail:</strong> <a href="mailto:info@emifree.com" class="text-blue-700 hover:text-blue-800">info@emifree.com</a></li>
			<li><strong>Internet:</strong> <a href="https://www.emifree.com" class="text-blue-700 hover:text-blue-800">www.emifree.com</a></li>
		</ul>

		<h2 class="text-2xl font-bold text-zinc-900 mt-10 mb-4">Register Entry</h2>
		<p class="text-lg leading-relaxed mb-6">
			Commercial Register Entry.<br>
			<strong>Registration Court:</strong> District Court (Amtsgericht) Berlin (Charlottenburg)<br>
			<strong>Registration Number:</strong> HRB 133977 B
		</p>

		<h2 class="text-2xl font-bold text-zinc-900 mt-10 mb-4">VAT ID</h2>
		<p class="text-lg leading-relaxed mb-6">
			Value Added Tax Identification Number pursuant to § 27 a Value Added Tax Act:<br>
			<strong>DE 815286735</strong>
		</p>

		<h2 class="text-2xl font-bold text-zinc-900 mt-10 mb-4">Important Notice for Consumers (B2B Exclusivity)</h2>
		<p class="text-lg leading-relaxed mb-6">
			This website and the goods displayed and published herein by Emifree GmbH are directed exclusively at commercial entities / traders (as defined by § 14 BGB, § 1 Paragraph 2 HGB, and § 15 II EStG). The conclusion of purchase contracts and the sale of goods to private individuals / consumers pursuant to § 13 BGB is strictly excluded.
		</p>

		<h2 class="text-2xl font-bold text-zinc-900 mt-10 mb-4">
			Person Responsible for Content pursuant to § 18 Paragraph 2 MStV
		</h2>
		<p class="text-lg leading-relaxed mb-10">
			Ingo Wagner<br>
			Pestalozzistraße 13<br>
			12557 Berlin, Germany
		</p>

		<hr class="my-10 border-slate-200">
		<p class="text-sm text-zinc-500 italic">
			Note: The domain www.emifree.com and other domains accessing this legal notice are the legal property of Emifree GmbH.
		</p>

		<div class="mt-12 pt-8 border-t border-slate-200">
			<a href="<?php echo esc_url( $emifree_back_to_root ); ?>" class="inline-flex items-center gap-2 text-blue-700 hover:text-blue-800 font-medium focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 rounded">
				<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 12H5"></path>
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m12 19-7-7 7-7"></path>
				</svg>
				Back to home
			</a>
		</div>
	</article>
	<?php
	return (string) ob_get_clean();
}

function emifree_render_privacy_body( $lang = 'en' ): string {
	$emifree_back_to_root = ( 'de' === $lang ) ? home_url( '/de/' ) : home_url( '/en/' );
	ob_start();
	?>
	<article class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-12 text-zinc-700">

		<h2 class="text-2xl font-bold text-zinc-900 mt-2 mb-4">1. Privacy at a Glance</h2>

		<h3 class="text-xl font-semibold text-zinc-900 mt-8 mb-3">General Information</h3>
		<p class="text-lg leading-relaxed mb-6">
			The following notes provide a simple overview of what happens to your personal data when you visit this website. Personal data is any data with which you can be personally identified.
		</p>

		<h3 class="text-xl font-semibold text-zinc-900 mt-8 mb-3">Data Collection on Our Website</h3>
		<ul class="text-lg leading-relaxed mb-6 space-y-3 list-disc pl-6">
			<li>
				<strong>Who is responsible for data collection on this website?</strong> The data processing on this website is carried out by the website operator: Emifree GmbH Produktion von Filteranlagen, Pestalozzistraße 13, 12557 Berlin, Germany. Email: <a href="mailto:info@emifree.com" class="text-blue-700 hover:text-blue-800">info@emifree.com</a>.
			</li>
			<li>
				<strong>How do we collect your data?</strong> On one hand, your data is collected when you provide it to us (e.g., by entering it into a contact form, live chat, or newsletter registration). Other data is collected automatically or based on your consent when you visit the website via our IT systems (e.g., IP address, browser type, time of page view).
			</li>
			<li>
				<strong>What do we use your data for?</strong> Part of the data is collected to ensure the error-free provision of the website. Other data can be used to analyze user behavior or provide customer support channels (such as live chat).
			</li>
		</ul>

		<h2 class="text-2xl font-bold text-zinc-900 mt-12 mb-4">2. General Notes and Mandatory Information</h2>

		<h3 class="text-xl font-semibold text-zinc-900 mt-8 mb-3">Legal Basis for Processing</h3>
		<p class="text-lg leading-relaxed mb-4">
			We process personal data in accordance with the GDPR (General Data Protection Regulation) and the German TDDDG:
		</p>
		<ul class="text-lg leading-relaxed mb-6 space-y-3 list-disc pl-6">
			<li><strong>Consent (Art. 6 (1)(a) GDPR):</strong> For specific purposes (e.g., tracking cookies, newsletter subscription, live chat functionality), we only process data after obtaining your explicit consent.</li>
			<li><strong>Performance of a Contract or Pre-contractual Measures (Art. 6 (1)(b) GDPR):</strong> If processing is necessary for the performance of a contract to which you are a party or to take steps at your request prior to entering into a contract (B2B inquiries).</li>
			<li><strong>Legal Obligation (Art. 6 (1)(c) GDPR):</strong> If we are subject to a legal obligation (e.g., documenting cookie consent choices).</li>
			<li><strong>Legitimate Interests (Art. 6 (1)(f) GDPR):</strong> To safeguard our legitimate business interests (e.g., maintaining technical stability of the website, IT security).</li>
		</ul>

		<h3 class="text-xl font-semibold text-zinc-900 mt-8 mb-3">Your Rights as a Data Subject</h3>
		<p class="text-lg leading-relaxed mb-4">
			Under applicable statutory provisions, you have the following rights regarding your personal data at any time:
		</p>
		<ul class="text-lg leading-relaxed mb-6 space-y-3 list-disc pl-6">
			<li><strong>Access (Art. 15 GDPR):</strong> You have the right to obtain information about the origin, recipient, and purpose of your stored personal data free of charge.</li>
			<li><strong>Rectification (Art. 16 GDPR) or Erasure (Art. 17 GDPR):</strong> You may request the correction of incorrect data or the erasure of your data.</li>
			<li><strong>Restriction of Processing (Art. 18 GDPR):</strong> You have the right to request the restriction of the processing of your data.</li>
			<li><strong>Data Portability (Art. 20 GDPR):</strong> You can request that we hand over your data to you or a third party in a standard, machine-readable format.</li>
			<li><strong>Withdrawal of Consent (Art. 7 (3) GDPR):</strong> Many data processing operations are only possible with your express consent. You can withdraw consent you have already given at any time with future effect.</li>
			<li><strong>Right to Lodge a Complaint (Art. 77 GDPR):</strong> In the event of data protection violations, you have the right to lodge a complaint with a competent data protection supervisory authority.</li>
		</ul>

		<h2 class="text-2xl font-bold text-zinc-900 mt-12 mb-4">3. Consent Management and Plugins</h2>

		<h3 class="text-xl font-semibold text-zinc-900 mt-8 mb-3">Cookiebot</h3>
		<p class="text-lg leading-relaxed mb-6">
			We use the consent management tool "Cookiebot" operated by Usercentrics A/S (Havnegade 39, 1058 Copenhagen, Denmark).
		</p>
		<ul class="text-lg leading-relaxed mb-6 space-y-3 list-disc pl-6">
			<li><strong>Purpose:</strong> Cookiebot is used to obtain your consent for storing certain cookies on your terminal device and to document this in accordance with data protection regulations.</li>
			<li><strong>Legal Basis:</strong> Processing is carried out to fulfill a legal obligation pursuant to Art. 6 (1)(c) GDPR in conjunction with § 25 (1) TDDDG.</li>
			<li><strong>Data Stored:</strong> When you enter our website, a Cookiebot cookie ("CookieConsent") is stored in your browser, recording your preferences or withdrawal of consent. This data is retained until you delete the cookie or the purpose for data storage no longer applies.</li>
		</ul>

		<h3 class="text-xl font-semibold text-zinc-900 mt-8 mb-3">Tawk.to (Live Chat)</h3>
		<p class="text-lg leading-relaxed mb-6">
			We use live chat software provided by tawk.to inc. (101 Hunter Avenue, Suite 102, Cary, NC 27511, USA).
		</p>
		<ul class="text-lg leading-relaxed mb-6 space-y-3 list-disc pl-6">
			<li><strong>Purpose:</strong> The live chat enables fast, direct communication with our B2B clients and prospects.</li>
			<li><strong>Legal Basis:</strong> Tawk.to is utilized exclusively on the basis of your explicit consent pursuant to Art. 6 (1)(a) GDPR. The chat widget will not load, and no data will be transferred until you grant permission via the Cookiebot banner.</li>
			<li><strong>Data Processed:</strong> Utilizing the chat processes technical infrastructure details (IP address, browser type, operating system, geographic region, visit duration) as well as any chat contents entered by you (e.g., name, email address, messages).</li>
			<li><strong>Third-Country Transfer:</strong> Data is transferred to tawk.to servers in the USA. Because tawk.to processes data outside the EU, Standard Contractual Clauses (SCCs) have been implemented to guarantee an appropriate level of data protection.</li>
			<li><strong>Withdrawal:</strong> You can adjust or withdraw your consent at any time via the Cookiebot settings link on our website.</li>
		</ul>

		<h3 class="text-xl font-semibold text-zinc-900 mt-8 mb-3">Google Analytics</h3>
		<p class="text-lg leading-relaxed mb-6">
			We use Google Analytics, a web analytics service provided by Google Ireland Limited (Gordon House, Barrow Street, Dublin 4, Ireland).
		</p>
		<ul class="text-lg leading-relaxed mb-6 space-y-3 list-disc pl-6">
			<li><strong>Purpose:</strong> Analyzing website usage to design and optimize our B2B online presence.</li>
			<li><strong>Legal Basis:</strong> Usage takes place exclusively after your explicit consent via Art. 6 (1)(a) GDPR and § 25 (1) TDDDG.</li>
			<li><strong>IP Anonymization:</strong> We utilize Google Analytics strictly with activated IP anonymization (IP masking), meaning your IP address is truncated by Google within EU member states before transmission.</li>
			<li><strong>Data Transfer:</strong> The IP address transmitted by your browser within the framework of Google Analytics will not be merged with other Google data. Data may be transferred to Google LLC in the USA (certified under the EU-US Data Privacy Framework).</li>
		</ul>

		<h2 class="text-2xl font-bold text-zinc-900 mt-12 mb-4">4. Newsletters and Contact Forms</h2>

		<h3 class="text-xl font-semibold text-zinc-900 mt-8 mb-3">Newsletter Data</h3>
		<p class="text-lg leading-relaxed mb-6">
			If you wish to receive the newsletter offered on the website, we require an email address from you as well as information that allows us to verify that you are the owner of the specified email address.
		</p>
		<ul class="text-lg leading-relaxed mb-6 space-y-3 list-disc pl-6">
			<li><strong>Tracking:</strong> By registering, you agree that we may analyze your clicking behavior on links within the newsletter in an anonymized / pseudononymized form to perfectly tailor content to our commercial client base.</li>
			<li><strong>Legal Basis:</strong> Art. 6 (1)(a) GDPR (Consent).</li>
			<li><strong>Withdrawal:</strong> You can withdraw your consent to the storage of data, the email address, and its use for sending the newsletter at any time via the "unsubscribe" link in the newsletter.</li>
		</ul>

		<div class="mt-12 pt-8 border-t border-slate-200">
			<a href="<?php echo esc_url( $emifree_back_to_root ); ?>" class="inline-flex items-center gap-2 text-blue-700 hover:text-blue-800 font-medium focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 rounded">
				<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 12H5"></path>
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m12 19-7-7 7-7"></path>
				</svg>
				Back to home
			</a>
		</div>
	</article>
	<?php
	return (string) ob_get_clean();
}

function emifree_render_terms_body( $lang = 'en' ): string {
	$emifree_back_to_root = ( 'de' === $lang ) ? home_url( '/de/' ) : home_url( '/en/' );
	ob_start();
	?>
	<article class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-12 text-zinc-700">
		<p class="text-lg leading-relaxed mb-6">
			<strong>Emifree GmbH Produktion von Filteranlagen</strong> Pestalozzistraße 13, 12557 Berlin, Germany<br>
			Phone: +49 3076283520 | E-Mail: info@emifree.com
		</p>

		<h2 class="text-2xl font-bold text-zinc-900 mt-10 mb-4">§ 1 Scope &amp; B2B Exclusivity</h2>
		<p class="text-lg leading-relaxed mb-4">(1) These General Terms and Conditions (GTC) apply exclusively to all business relations, deliveries, and offers between Emifree GmbH (hereinafter "Seller") and the customer in the version valid at the time of the order.</p>
		<p class="text-lg leading-relaxed mb-4">(2) The Seller's catalog and web presence are directed exclusively at commercial entities, traders, and entrepreneurs within the meaning of § 14 BGB (German Civil Code), § 1 Paragraph 2 HGB (German Commercial Code), and § 15 II EStG (German Income Tax Act). Sales and purchase contracts involving private consumers (§ 13 BGB) are strictly excluded. By submitting an order, the customer guarantees that they are acting as a commercial entity.</p>
		<p class="text-lg leading-relaxed mb-6">(3) Deviating, conflicting, or supplementary terms and conditions of the customer shall not become part of the contract unless the Seller has explicitly agreed to their validity in writing.</p>

		<h2 class="text-2xl font-bold text-zinc-900 mt-10 mb-4">§ 2 Formation of Contract</h2>
		<p class="text-lg leading-relaxed mb-4">(1) The presentation of products on the website does not constitute a legally binding offer, but rather a non-binding online catalog.</p>
		<p class="text-lg leading-relaxed mb-4">(2) By submitting an order request via the website, the customer issues a binding contractual offer within the meaning of § 145 BGB.</p>
		<p class="text-lg leading-relaxed mb-4">(3) The contract is concluded only when the Seller issues an explicit written order confirmation / acceptance via email (or via postal mail upon request). The customer waives the right to formal receipt of an acceptance declaration pursuant to § 151 Sentence 1 BGB.</p>
		<p class="text-lg leading-relaxed mb-4">(4) For advance payments (Vorkasse), the contract is concluded at the time of the payment request or upon successful transaction by the customer. If payment is not completed within 10 days of sending the request, the Seller is no longer bound by the transaction request.</p>
		<p class="text-lg leading-relaxed mb-6">(5) If the published specification of the goods does not align with the customer's request, the customer will be notified of potential discrepancies and a corresponding counter-offer will be extended.</p>

		<h2 class="text-2xl font-bold text-zinc-900 mt-10 mb-4">§ 3 Delivery, Shipping Costs, Transfer of Risk &amp; Inspection Obligations</h2>
		<p class="text-lg leading-relaxed mb-4">(1) Delivery periods shall be deemed approximate only. Even if a calendar delivery date is specified, it does not constitute a fixed-date commercial transaction (<em>Fixhandelsgeschäft</em>) under § 376 Paragraph 1 HGB, unless explicitly agreed upon in writing.</p>
		<p class="text-lg leading-relaxed mb-4">(2) If freights, charges, duties, taxes, or fees are introduced or increased after contract conclusion, the Seller is authorized to adjust the purchase price accordingly. Prices valid on the day of actual delivery shall apply.</p>
		<p class="text-lg leading-relaxed mb-4">(3) The buyer must note any visible damage or shortages on the delivery note immediately upon arrival and obtain written acknowledgment from the carrier. Unacknowledged damages or shortages will not be recognized by the Seller or insurers.</p>
		<p class="text-lg leading-relaxed mb-6">(4) The customer must notify the Seller in writing of patent defects immediately upon receipt of the goods at their destination, and of latent defects immediately upon discovery, providing a detailed description. Any other defect notifications must be sent via registered mail within a maximum of 10 days following receipt.</p>

		<h2 class="text-2xl font-bold text-zinc-900 mt-10 mb-4">§ 4 Warranties &amp; Defects Management</h2>
		<p class="text-lg leading-relaxed mb-4">(1) In the case of justified and timely defect notifications, the Seller shall, at its discretion, remedy the defect within a reasonable timeframe (generally within 4 weeks), deliver a flawless replacement item, or grant an appropriate price reduction.</p>
		<p class="text-lg leading-relaxed mb-4">(2) If the Seller fails to fulfill these obligations within a reasonable grace period, the customer may demand a price reduction, rescind the contract, or carry out the repair independently or via a third party at the Seller's expense.</p>
		<p class="text-lg leading-relaxed mb-4">(3) If the transaction constitutes a commercial purchase for both parties, the statutory inspection and notification requirements of §§ 377 HGB shall apply. If the subject matter of the contract is second-hand or used machinery / goods, any warranty for material defects is strictly excluded.</p>
		<p class="text-lg leading-relaxed mb-4">(4) No warranty or liability is assumed for material defects resulting from unsuitable or improper use, incorrect assembly or commissioning by the customer or third parties, normal wear and tear, or negligent handling.</p>
		<p class="text-lg leading-relaxed mb-6">(5) The return of defect-free goods is generally excluded and requires express written approval from the Seller. Returns are strictly limited to 8 business days post-delivery; older items will be returned to the customer at their expense.</p>

		<h2 class="text-2xl font-bold text-zinc-900 mt-10 mb-4">§ 5 Retention of Title</h2>
		<p class="text-lg leading-relaxed mb-4">(1) All delivered goods remain the property of the Seller until full settlement of all outstanding claims arising from the ongoing business relationship, including future or conditional claims.</p>
		<p class="text-lg leading-relaxed mb-4">(2) The buyer is authorized to resell or process the retained goods in the ordinary course of business. The buyer hereby assigns to the Seller all claims up to the invoice value arising from reselling the goods to third parties. The Seller accepts this assignment.</p>
		<p class="text-lg leading-relaxed mb-4">(3) The buyer remains authorized to collect the claim alongside the Seller. The Seller may revoke this collection authorization if the buyer falls into arrears or if their creditworthiness is materially diminished.</p>
		<p class="text-lg leading-relaxed mb-6">(4) If third parties seize or attach the retained goods, the buyer must report the Seller's ownership stake and immediately notify the Seller. The buyer shall bear all intervention costs.</p>

		<h2 class="text-2xl font-bold text-zinc-900 mt-10 mb-4">§ 6 Limitation of Liability &amp; Statute of Limitations</h2>
		<p class="text-lg leading-relaxed mb-4">(1) The Seller shall be liable without limitation for intent, gross negligence, and for culpable injury to life, body, or health.</p>
		<p class="text-lg leading-relaxed mb-4">(2) In cases of ordinary negligent breaches of essential contractual obligations (<em>Kardinalpflichten</em>), the Seller's liability shall be limited to typical, reasonably foreseeable contractual damages. Liability for loss of profit or other consequential financial damages of the customer is excluded in these cases.</p>
		<p class="text-lg leading-relaxed mb-4">(3) Any further liability of the Seller, regardless of the legal framework, is excluded to the extent permitted by law.</p>
		<p class="text-lg leading-relaxed mb-6">(4) All claims of the customer — on whatever legal grounds — shall expire 12 months from delivery or formal acceptance of the goods. This does not apply to mandatory statutory limitations or damages resulting from intent or gross negligence.</p>

		<h2 class="text-2xl font-bold text-zinc-900 mt-10 mb-4">§ 7 Confidentiality</h2>
		<p class="text-lg leading-relaxed mb-8">
			The customer is obliged to treat all information, know-how, and commercial trade secrets disclosed in connection with the performance of the order strictly confidential, and shall not pass on drawings, documentation, or other materials to third parties without the prior written consent of Emifree GmbH.
		</p>

		<h2 class="text-2xl font-bold text-zinc-900 mt-10 mb-4">§ 8 Data Protection Note</h2>
		<p class="text-lg leading-relaxed mb-8">
			Information concerning the collection, storage, and processing of personal data does not form part of these commercial terms and conditions and is governed exclusively and separately by the Seller's designated <a href="<?php echo esc_url( home_url( '/privacy/' ) ); ?>" class="text-blue-700 hover:text-blue-800 underline">Privacy Policy</a>.
		</p>

		<h2 class="text-2xl font-bold text-zinc-900 mt-10 mb-4">§ 9 Governing Law, Jurisdiction &amp; Severability</h2>
		<p class="text-lg leading-relaxed mb-4">(1) The contractual relationship between the Seller and the customer shall be governed exclusively by the laws of the Federal Republic of Germany. The application of the UN Convention on Contracts for the International Sale of Goods (CISG) is explicitly excluded.</p>
		<p class="text-lg leading-relaxed mb-4">(2) The exclusive place of jurisdiction for all disputes arising out of or in connection with this contract is the registered corporate seat of the Seller in <strong>Berlin</strong>, provided that the customer is a merchant within the meaning of the HGB, a legal entity under public law, or a special fund under public law. However, the Seller remains entitled to file a suit at the customer's primary place of business.</p>
		<p class="text-lg leading-relaxed mb-10">(3) Should individual provisions of these terms be or become invalid, the validity of the remaining provisions shall remain unaffected. The invalid provision shall be replaced by a valid clause that comes closest to the economic intent of the original text.</p>

		<div class="mt-12 pt-8 border-t border-slate-200">
			<a href="<?php echo esc_url( $emifree_back_to_root ); ?>" class="inline-flex items-center gap-2 text-blue-700 hover:text-blue-800 font-medium focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 rounded">
				<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 12H5"></path>
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m12 19-7-7 7-7"></path>
				</svg>
				Back to home
			</a>
		</div>
	</article>
	<?php
	return (string) ob_get_clean();
}
/* -------------------------------------------------------------------------
 * German (de) page body renderers.
 *
 * Mirrors src/pages/{Impressum,Privacy,Terms}.jsx content translated
 * to German. Source markdown files at assets/Legal/{impressum,
 * privacy_policy, terms}_de.md were the translation source. The
 * markup structure, section ordering, and class set match the
 * English renderers above so future style changes propagate cleanly.
 * ------------------------------------------------------------------------- */

function emifree_render_impressum_de_body( $lang = 'de' ): string {
	$emifree_back_to_root = ( 'de' === $lang ) ? home_url( '/de/' ) : home_url( '/en/' );
	ob_start();
	?>
	<article class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-12 text-zinc-700">
		<p class="text-lg leading-relaxed mb-6">
			<strong>Angaben gemäß § 5 TMG (Telemediengesetz) / § 2 DL-InfoV:</strong>
		</p>
		<p class="text-lg leading-relaxed mb-10">
			Emifree GmbH Produktion von Filteranlagen<br>
			Pestalozzistraße 13<br>
			12557 Berlin, Deutschland
		</p>

		<h2 class="text-2xl font-bold text-zinc-900 mt-10 mb-4">Vertreten durch den Geschäftsführer</h2>
		<p class="text-lg leading-relaxed mb-6">Ingo Wagner</p>

		<h2 class="text-2xl font-bold text-zinc-900 mt-10 mb-4">Kontaktdaten</h2>
		<ul class="text-lg leading-relaxed mb-6 space-y-1">
			<li><strong>Telefon:</strong> <a href="tel:+493076283520" class="text-blue-700 hover:text-blue-800">+49 3076283520</a></li>
			<li><strong>E-Mail:</strong> <a href="mailto:info@emifree.com" class="text-blue-700 hover:text-blue-800">info@emifree.com</a></li>
			<li><strong>Internet:</strong> <a href="https://www.emifree.com" class="text-blue-700 hover:text-blue-800">www.emifree.com</a></li>
		</ul>

		<h2 class="text-2xl font-bold text-zinc-900 mt-10 mb-4">Registereintrag</h2>
		<p class="text-lg leading-relaxed mb-6">
			Eintragung im Handelsregister.<br>
			<strong>Registergericht:</strong> Amtsgericht Berlin (Charlottenburg)<br>
			<strong>Registernummer:</strong> HRB 133977 B
		</p>

		<h2 class="text-2xl font-bold text-zinc-900 mt-10 mb-4">Umsatzsteuer-Identifikationsnummer</h2>
		<p class="text-lg leading-relaxed mb-6">
			Umsatzsteuer-Identifikationsnummer gemäß § 27 a Umsatzsteuergesetz:<br>
			<strong>DE 815286735</strong>
		</p>

		<h2 class="text-2xl font-bold text-zinc-900 mt-10 mb-4">Wichtiger Hinweis für Verbraucher (B2B-Exklusivität)</h2>
		<p class="text-lg leading-relaxed mb-6">
			Diese Website sowie die hierin von der Emifree GmbH dargestellten und veröffentlichten Waren richten sich ausschließlich an gewerbliche Unternehmen / Händler (im Sinne von § 14 BGB, § 1 Abs. 2 HGB und § 15 II EStG). Der Abschluss von Kaufverträgen und der Verkauf von Waren an Privatpersonen / Verbraucher gemäß § 13 BGB sind ausdrücklich ausgeschlossen.
		</p>

		<h2 class="text-2xl font-bold text-zinc-900 mt-10 mb-4">
			Inhaltlich Verantwortlicher gemäß § 18 Abs. 2 MStV
		</h2>
		<p class="text-lg leading-relaxed mb-10">
			Ingo Wagner<br>
			Pestalozzistraße 13<br>
			12557 Berlin, Deutschland
		</p>

		<hr class="my-10 border-slate-200">
		<p class="text-sm text-zinc-500 italic">
			Hinweis: Die Domain www.emifree.com und andere Domains, über die auf dieses Impressum zugegriffen werden kann, sind rechtmäßiges Eigentum der Emifree GmbH.
		</p>

		<div class="mt-12 pt-8 border-t border-slate-200">
			<a href="<?php echo esc_url( $emifree_back_to_root ); ?>" class="inline-flex items-center gap-2 text-blue-700 hover:text-blue-800 font-medium focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 rounded">
				<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 12H5"></path>
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m12 19-7-7 7-7"></path>
				</svg>
				Zur Startseite
			</a>
		</div>
	</article>
	<?php
	return (string) ob_get_clean();
}

function emifree_render_datenschutz_de_body( $lang = 'de' ): string {
	$emifree_back_to_root = ( 'de' === $lang ) ? home_url( '/de/' ) : home_url( '/en/' );
	ob_start();
	?>
	<article class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-12 text-zinc-700">

		<h2 class="text-2xl font-bold text-zinc-900 mt-2 mb-4">1. Datenschutz im Überblick</h2>

		<h3 class="text-xl font-semibold text-zinc-900 mt-8 mb-3">Allgemeine Informationen</h3>
		<p class="text-lg leading-relaxed mb-6">
			Die folgenden Hinweise geben einen einfachen Überblick darüber, was mit Ihren personenbezogenen Daten geschieht, wenn Sie diese Website besuchen. Personenbezogene Daten sind alle Daten, mit denen Sie persönlich identifiziert werden können.
		</p>

		<h3 class="text-xl font-semibold text-zinc-900 mt-8 mb-3">Datenerhebung auf unserer Website</h3>
		<ul class="text-lg leading-relaxed mb-6 space-y-3 list-disc pl-6">
			<li>
				<strong>Wer ist für die Datenerhebung auf dieser Website verantwortlich?</strong> Die Datenverarbeitung auf dieser Website erfolgt durch den Websitebetreiber: Emifree GmbH Produktion von Filteranlagen, Pestalozzistraße 13, 12557 Berlin, Deutschland. E-Mail: <a href="mailto:info@emifree.com" class="text-blue-700 hover:text-blue-800">info@emifree.com</a>.
			</li>
			<li>
				<strong>Wie erheben wir Ihre Daten?</strong> Zum einen werden Ihre Daten erhoben, wenn Sie uns diese zur Verfügung stellen (z. B. durch Eingabe in ein Kontaktformular, im Live-Chat oder bei der Anmeldung zum Newsletter). Andere Daten werden automatisch oder auf Grundlage Ihrer Einwilligung erfasst, wenn Sie die Website über unsere IT-Systeme besuchen (z. B. IP-Adresse, Browsertyp, Zeitpunkt des Seitenaufrufs).
			</li>
			<li>
				<strong>Wofür verwenden wir Ihre Daten?</strong> Ein Teil der Daten wird erfasst, um die fehlerfreie Bereitstellung der Website zu gewährleisten. Andere Daten können zur Analyse des Nutzerverhaltens oder zur Bereitstellung von Kundensupportkanälen (wie z. B. Live-Chat) verwendet werden.
			</li>
		</ul>

		<h2 class="text-2xl font-bold text-zinc-900 mt-12 mb-4">2. Allgemeine Hinweise und Pflichtangaben</h2>

		<h3 class="text-xl font-semibold text-zinc-900 mt-8 mb-3">Rechtsgrundlage für die Verarbeitung</h3>
		<p class="text-lg leading-relaxed mb-4">
			Wir verarbeiten personenbezogene Daten gemäß der DSGVO (Datenschutz-Grundverordnung) und dem deutschen TDDDG:
		</p>
		<ul class="text-lg leading-relaxed mb-6 space-y-3 list-disc pl-6">
			<li><strong>Einwilligung (Art. 6 Abs. 1 Buchstabe a DSGVO):</strong> Für bestimmte Zwecke (z. B. Tracking-Cookies, Newsletter-Abonnement, Live-Chat-Funktion) verarbeiten wir Daten erst nach Einholung Ihrer ausdrücklichen Einwilligung.</li>
			<li><strong>Erfüllung eines Vertrags oder vorvertraglicher Maßnahmen (Art. 6 Abs. 1 Buchstabe b DSGVO):</strong> Wenn die Verarbeitung zur Erfüllung eines Vertrags, dessen Vertragspartei Sie sind, oder zur Durchführung von Maßnahmen auf Ihren Wunsch hin vor Abschluss eines Vertrags erforderlich ist (B2B-Anfragen).</li>
			<li><strong>Rechtliche Verpflichtung (Art. 6 Abs. 1 Buchstabe c DSGVO):</strong> Wenn wir einer rechtlichen Verpflichtung unterliegen (z. B. Dokumentation der Einwilligungsentscheidungen zu Cookies).</li>
			<li><strong>Berechtigte Interessen (Art. 6 Abs. 1 Buchstabe f DSGVO):</strong> Zur Wahrung unserer berechtigten geschäftlichen Interessen (z. B. Aufrechterhaltung der technischen Stabilität der Website, IT-Sicherheit).</li>
		</ul>

		<h3 class="text-xl font-semibold text-zinc-900 mt-8 mb-3">Ihre Rechte als betroffene Person</h3>
		<p class="text-lg leading-relaxed mb-4">
			Gemäß den geltenden gesetzlichen Bestimmungen haben Sie jederzeit folgende Rechte in Bezug auf Ihre personenbezogenen Daten:
		</p>
		<ul class="text-lg leading-relaxed mb-6 space-y-3 list-disc pl-6">
			<li><strong>Auskunftsrecht (Art. 15 DSGVO):</strong> Sie haben das Recht, unentgeltlich Auskunft über die Herkunft, die Empfänger und den Zweck Ihrer gespeicherten personenbezogenen Daten zu erhalten.</li>
			<li><strong>Recht auf Berichtigung (Art. 16 DSGVO) oder Löschung (Art. 17 DSGVO):</strong> Sie können die Berichtigung unrichtiger Daten oder die Löschung Ihrer Daten verlangen.</li>
			<li><strong>Recht auf Einschränkung der Verarbeitung (Art. 18 DSGVO):</strong> Sie haben das Recht, die Einschränkung der Verarbeitung Ihrer Daten zu verlangen.</li>
			<li><strong>Recht auf Datenübertragbarkeit (Art. 20 DSGVO):</strong> Sie können verlangen, dass wir Ihre Daten in einem gängigen, maschinenlesbaren Format an Sie oder einen Dritten übergeben.</li>
			<li><strong>Widerrufsrecht (Art. 7 Abs. 3 DSGVO):</strong> Viele Datenverarbeitungsvorgänge sind nur mit Ihrer ausdrücklichen Einwilligung möglich. Sie können eine bereits erteilte Einwilligung jederzeit mit Wirkung für die Zukunft widerrufen.</li>
			<li><strong>Beschwerderecht (Art. 77 DSGVO):</strong> Im Falle von Datenschutzverstößen haben Sie das Recht, sich bei der zuständigen Datenschutz-Aufsichtsbehörde zu beschweren.</li>
		</ul>

		<h2 class="text-2xl font-bold text-zinc-900 mt-12 mb-4">3. Einwilligungsmanagement und Plugins</h2>

		<h3 class="text-xl font-semibold text-zinc-900 mt-8 mb-3">Cookiebot</h3>
		<p class="text-lg leading-relaxed mb-6">
			Wir verwenden das Einwilligungsmanagement-Tool „Cookiebot" der Usercentrics A/S (Havnegade 39, 1058 Kopenhagen, Dänemark).
		</p>
		<ul class="text-lg leading-relaxed mb-6 space-y-3 list-disc pl-6">
			<li><strong>Zweck:</strong> Cookiebot wird verwendet, um Ihre Einwilligung zur Speicherung bestimmter Cookies auf Ihrem Endgerät einzuholen und diese gemäß den Datenschutzbestimmungen zu dokumentieren.</li>
			<li><strong>Rechtsgrundlage:</strong> Die Verarbeitung erfolgt zur Erfüllung einer rechtlichen Verpflichtung gemäß Art. 6 Abs. 1 Buchstabe c DSGVO in Verbindung mit § 25 Abs. 1 TDDDG.</li>
			<li><strong>Gespeicherte Daten:</strong> Beim Aufruf unserer Website wird ein Cookiebot-Cookie („CookieConsent") in Ihrem Browser gespeichert, das Ihre Einwilligung oder deren Widerruf dokumentiert. Diese Daten werden gespeichert, bis Sie das Cookie löschen oder der Zweck der Datenspeicherung entfällt.</li>
		</ul>

		<h3 class="text-xl font-semibold text-zinc-900 mt-8 mb-3">Tawk.to (Live-Chat)</h3>
		<p class="text-lg leading-relaxed mb-6">
			Wir verwenden Live-Chat-Software der tawk.to inc. (101 Hunter Avenue, Suite 102, Cary, NC 27511, USA).
		</p>
		<ul class="text-lg leading-relaxed mb-6 space-y-3 list-disc pl-6">
			<li><strong>Zweck:</strong> Der Live-Chat ermöglicht eine schnelle, direkte Kommunikation mit unseren B2B-Kunden und Interessenten.</li>
			<li><strong>Rechtsgrundlage:</strong> Tawk.to wird ausschließlich auf Grundlage Ihrer ausdrücklichen Einwilligung gemäß Art. 6 Abs. 1 Buchstabe a DSGVO verwendet. Das Chat-Widget wird erst geladen und es werden keine Daten übertragen, wenn Sie Ihre Einwilligung über das Cookiebot-Banner erteilt haben.</li>
			<li><strong>Verarbeitete Daten:</strong> Bei der Nutzung des Chats werden technische Infrastrukturdaten (IP-Adresse, Browsertyp, Betriebssystem, geografische Region, Besuchsdauer) sowie die von Ihnen eingegebenen Chat-Inhalte (z. B. Name, E-Mail-Adresse, Nachrichten) verarbeitet.</li>
			<li><strong>Drittlandtransfer:</strong> Die Daten werden an Server von tawk.to in den USA übertragen. Da tawk.to Daten außerhalb der EU verarbeitet, wurden Standardvertragsklauseln (SCC) implementiert, um ein angemessenes Datenschutzniveau zu gewährleisten.</li>
			<li><strong>Widerruf:</strong> Sie können Ihre Einwilligung jederzeit über den Cookiebot-Einstellungslink auf unserer Website anpassen oder widerrufen.</li>
		</ul>

		<h3 class="text-xl font-semibold text-zinc-900 mt-8 mb-3">Google Analytics</h3>
		<p class="text-lg leading-relaxed mb-6">
			Wir verwenden Google Analytics, einen Webanalysedienst der Google Ireland Limited (Gordon House, Barrow Street, Dublin 4, Irland).
		</p>
		<ul class="text-lg leading-relaxed mb-6 space-y-3 list-disc pl-6">
			<li><strong>Zweck:</strong> Analyse der Website-Nutzung zur Gestaltung und Optimierung unseres B2B-Online-Auftritts.</li>
			<li><strong>Rechtsgrundlage:</strong> Die Nutzung erfolgt ausschließlich nach Ihrer ausdrücklichen Einwilligung gemäß Art. 6 Abs. 1 Buchstabe a DSGVO und § 25 Abs. 1 TDDDG.</li>
			<li><strong>IP-Anonymisierung:</strong> Wir setzen Google Analytics ausschließlich mit aktivierter IP-Anonymisierung ein, d. h. Ihre IP-Adresse wird von Google innerhalb von EU-Mitgliedstaaten vor der Übertragung gekürzt.</li>
			<li><strong>Datenübertragung:</strong> Die von Ihrem Browser übermittelte IP-Adresse wird im Rahmen von Google Analytics nicht mit anderen Google-Daten zusammengeführt. Daten können an die Google LLC in den USA übertragen werden (zertifiziert nach dem EU-US Data Privacy Framework).</li>
		</ul>

		<h2 class="text-2xl font-bold text-zinc-900 mt-12 mb-4">4. Newsletter und Kontaktformulare</h2>

		<h3 class="text-xl font-semibold text-zinc-900 mt-8 mb-3">Newsletter-Daten</h3>
		<p class="text-lg leading-relaxed mb-6">
			Wenn Sie den auf der Website angebotenen Newsletter erhalten möchten, benötigen wir von Ihnen eine E-Mail-Adresse sowie Informationen, die uns die Überprüfung ermöglichen, dass Sie der Inhaber der angegebenen E-Mail-Adresse sind.
		</p>
		<ul class="text-lg leading-relaxed mb-6 space-y-3 list-disc pl-6">
			<li><strong>Tracking:</strong> Mit der Registrierung willigen Sie ein, dass wir Ihr Klickverhalten auf Links im Newsletter in anonymisierter / pseudonymisierter Form analysieren, um die Inhalte optimal auf unsere gewerbliche Kundschaft zuzuschneiden.</li>
			<li><strong>Rechtsgrundlage:</strong> Art. 6 Abs. 1 Buchstabe a DSGVO (Einwilligung).</li>
			<li><strong>Widerruf:</strong> Sie können Ihre Einwilligung zur Speicherung der Daten, der E-Mail-Adresse und deren Nutzung zum Versand des Newsletters jederzeit über den „Abmelden"-Link im Newsletter widerrufen.</li>
		</ul>

		<div class="mt-12 pt-8 border-t border-slate-200">
			<a href="<?php echo esc_url( $emifree_back_to_root ); ?>" class="inline-flex items-center gap-2 text-blue-700 hover:text-blue-800 font-medium focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 rounded">
				<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 12H5"></path>
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m12 19-7-7 7-7"></path>
				</svg>
				Zur Startseite
			</a>
		</div>
	</article>
	<?php
	return (string) ob_get_clean();
}

function emifree_render_agb_de_body( $lang = 'de' ): string {
	$emifree_back_to_root = ( 'de' === $lang ) ? home_url( '/de/' ) : home_url( '/en/' );
	ob_start();
	?>
	<article class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-12 text-zinc-700">
		<p class="text-lg leading-relaxed mb-6">
			<strong>Emifree GmbH Produktion von Filteranlagen</strong> Pestalozzistraße 13, 12557 Berlin, Deutschland<br>
			Telefon: +49 3076283520 | E-Mail: info@emifree.com
		</p>

		<h2 class="text-2xl font-bold text-zinc-900 mt-10 mb-4">§ 1 Geltungsbereich &amp; B2B-Exklusivität</h2>
		<p class="text-lg leading-relaxed mb-4">(1) Diese Allgemeinen Geschäftsbedingungen (AGB) gelten ausschließlich für alle Geschäftsbeziehungen, Lieferungen und Angebote zwischen der Emifree GmbH (im Folgenden „Verkäufer") und dem Kunden in der zum Zeitpunkt der Bestellung gültigen Fassung.</p>
		<p class="text-lg leading-relaxed mb-4">(2) Der Katalog und der Internetauftritt des Verkäufers richten sich ausschließlich an gewerbliche Unternehmen, Händler und Unternehmer im Sinne von § 14 BGB, § 1 Abs. 2 HGB und § 15 II EStG. Kaufverträge mit privaten Verbrauchern (§ 13 BGB) sind ausdrücklich ausgeschlossen. Mit der Abgabe einer Bestellung versichert der Kunde, dass er als gewerbliches Unternehmen handelt.</p>
		<p class="text-lg leading-relaxed mb-6">(3) Abweichende, entgegenstehende oder ergänzende Geschäftsbedingungen des Kunden werden nicht Vertragsbestandteil, es sei denn, der Verkäufer hat ihrer Geltung ausdrücklich schriftlich zugestimmt.</p>

		<h2 class="text-2xl font-bold text-zinc-900 mt-10 mb-4">§ 2 Vertragsabschluss</h2>
		<p class="text-lg leading-relaxed mb-4">(1) Die Darstellung der Produkte auf der Website stellt kein rechtsverbindliches Angebot dar, sondern lediglich einen unverbindlichen Online-Katalog.</p>
		<p class="text-lg leading-relaxed mb-4">(2) Mit der Übermittlung einer Bestellanfrage über die Website gibt der Kunde ein verbindliches Vertragsangebot im Sinne von § 145 BGB ab.</p>
		<p class="text-lg leading-relaxed mb-4">(3) Der Vertrag kommt erst zustande, wenn der Verkäufer eine ausdrückliche schriftliche Auftragsbestätigung / Annahme per E-Mail (oder auf Wunsch per Post) versendet. Der Kunde verzichtet auf das Recht auf formellen Erhalt einer Annahmeerklärung gemäß § 151 Satz 1 BGB.</p>
		<p class="text-lg leading-relaxed mb-4">(4) Bei Vorauszahlung (Vorkasse) kommt der Vertrag zum Zeitpunkt der Zahlungsaufforderung oder mit dem erfolgreichen Abschluss der Transaktion durch den Kunden zustande. Wird die Zahlung nicht innerhalb von 10 Tagen nach Absendung der Aufforderung geleistet, ist der Verkäufer nicht mehr an die Transaktionsaufforderung gebunden.</p>
		<p class="text-lg leading-relaxed mb-6">(5) Weicht die veröffentlichte Warenbeschreibung von der Anfrage des Kunden ab, wird der Kunde auf mögliche Abweichungen hingewiesen und ein entsprechendes Gegenangebot unterbreitet.</p>

		<h2 class="text-2xl font-bold text-zinc-900 mt-10 mb-4">§ 3 Lieferung, Versandkosten, Gefahrenübergang und Prüfpflichten</h2>
		<p class="text-lg leading-relaxed mb-4">(1) Lieferfristen gelten nur als annähernd. Auch wenn ein kalendarischer Liefertermin angegeben wird, handelt es sich dabei nicht um ein Fixhandelsgeschäft gemäß § 376 Abs. 1 HGB, es sei denn, dies wurde ausdrücklich schriftlich vereinbart.</p>
		<p class="text-lg leading-relaxed mb-4">(2) Werden nach Vertragsabschluss Frachtkosten, Abgaben, Zölle, Steuern oder Gebühren eingeführt oder erhöht, ist der Verkäufer berechtigt, den Kaufpreis entsprechend anzupassen. Es gelten die am Tag der tatsächlichen Lieferung gültigen Preise.</p>
		<p class="text-lg leading-relaxed mb-4">(3) Der Käufer hat sichtbare Schäden oder Fehlmengen unverzüglich nach Eintreffen auf dem Lieferschein zu vermerken und sich vom Frachtführer eine schriftliche Bestätigung einholen zu lassen. Nicht bestätigte Schäden oder Fehlmengen werden vom Verkäufer oder den Versicherern nicht anerkannt.</p>
		<p class="text-lg leading-relaxed mb-6">(4) Der Kunde hat dem Verkäufer offensichtliche Mängel unverzüglich nach Erhalt der Ware am Bestimmungsort und versteckte Mängel unverzüglich nach ihrer Entdeckung unter Angabe einer detaillierten Beschreibung schriftlich mitzuteilen. Alle sonstigen Mängelrügen müssen innerhalb von höchstens 10 Tagen nach Erhalt per Einschreiben versandt werden.</p>

		<h2 class="text-2xl font-bold text-zinc-900 mt-10 mb-4">§ 4 Gewährleistung und Mängelabwicklung</h2>
		<p class="text-lg leading-relaxed mb-4">(1) Im Falle berechtigter und fristgerechter Mängelrügen beseitigt der Verkäufer nach seiner Wahl den Mangel innerhalb einer angemessenen Frist (in der Regel innerhalb von 4 Wochen), liefert einen mangelfreien Ersatz oder gewährt eine angemessene Preisminderung.</p>
		<p class="text-lg leading-relaxed mb-4">(2) Kommt der Verkäufer diesen Verpflichtungen innerhalb einer angemessenen Nachfrist nicht nach, kann der Kunde Preisminderung verlangen, vom Vertrag zurücktreten oder die Nachbesserung selbst oder durch Dritte auf Kosten des Verkäufers durchführen.</p>
		<p class="text-lg leading-relaxed mb-4">(3) Handelt es sich um ein beiderseitiges Handelsgeschäft, gelten die gesetzlichen Untersuchungs- und Rügepflichten gemäß §§ 377 HGB. Handelt es sich um gebrauchte Maschinen / Waren, ist jede Sachmängelhaftung ausgeschlossen.</p>
		<p class="text-lg leading-relaxed mb-4">(4) Eine Gewährleistung und Haftung ist ausgeschlossen für Sachmängel, die auf ungeeignete oder unsachgemäße Verwendung, fehlerhafte Montage oder Inbetriebnahme durch den Kunden oder Dritte, natürliche Abnutzung oder fahrlässige Behandlung zurückzuführen sind.</p>
		<p class="text-lg leading-relaxed mb-6">(5) Die Rückgabe mangelfreier Ware ist grundsätzlich ausgeschlossen und bedarf der ausdrücklichen schriftlichen Zustimmung des Verkäufers. Rückgaben sind strikt auf 8 Werktage nach Lieferung begrenzt; ältere Artikel werden auf Kosten des Kunden zurückgesandt.</p>

		<h2 class="text-2xl font-bold text-zinc-900 mt-10 mb-4">§ 5 Eigentumsvorbehalt</h2>
		<p class="text-lg leading-relaxed mb-4">(1) Alle gelieferten Waren bleiben bis zur vollständigen Begleichung sämtlicher aus der laufenden Geschäftsbeziehung resultierenden Forderungen Eigentum des Verkäufers, einschließlich künftiger oder bedingter Forderungen.</p>
		<p class="text-lg leading-relaxed mb-4">(2) Der Käufer ist berechtigt, die Vorbehaltsware im ordentlichen Geschäftsgang weiter zu veräußern oder zu verarbeiten. Der Käufer tritt hiermit alle Forderungen bis zur Höhe des Rechnungsbetrages ab, die ihm aus der Weiterveräußerung an Dritte erwachsen. Der Verkäufer nimmt diese Abtretung an.</p>
		<p class="text-lg leading-relaxed mb-4">(3) Der Käufer bleibt neben dem Verkäufer ermächtigt, die Forderung einzuziehen. Der Verkäufer kann diese Einzugsermächtigung widerrufen, wenn der Käufer in Zahlungsverzug gerät oder wenn seine Kreditwürdigkeit wesentlich gemindert ist.</p>
		<p class="text-lg leading-relaxed mb-6">(4) Bei Pfändungen oder sonstigen Eingriffen Dritter in die Vorbehaltsware hat der Käufer das Eigentum des Verkäufers anzuzeigen und diesen unverzüglich zu benachrichtigen. Der Käufer trägt alle Interventionskosten.</p>

		<h2 class="text-2xl font-bold text-zinc-900 mt-10 mb-4">§ 6 Haftungsbeschränkung &amp; Verjährung</h2>
		<p class="text-lg leading-relaxed mb-4">(1) Der Verkäufer haftet uneingeschränkt für Vorsatz und grobe Fahrlässigkeit sowie für schuldhaft verursachte Verletzungen des Lebens, des Körpers oder der Gesundheit.</p>
		<p class="text-lg leading-relaxed mb-4">(2) Bei einfach fahrlässiger Verletzung wesentlicher Vertragspflichten (Kardinalpflichten) ist die Haftung des Verkäufers auf typische, vorhersehbare Vertragsschäden begrenzt. Eine Haftung für entgangenen Gewinn oder sonstige Folgeschäden des Kunden ist in diesen Fällen ausgeschlossen.</p>
		<p class="text-lg leading-relaxed mb-4">(3) Eine weitergehende Haftung des Verkäufers ist, gleich aus welchem Rechtsgrund, soweit gesetzlich zulässig, ausgeschlossen.</p>
		<p class="text-lg leading-relaxed mb-6">(4) Alle Ansprüche des Kunden – gleich aus welchem Rechtsgrund – verjähren 12 Monate nach Ablieferung oder Abnahme der Ware. Dies gilt nicht für zwingende gesetzliche Verjährungsfristen sowie für Schäden, die auf Vorsatz oder grober Fahrlässigkeit beruhen.</p>

		<h2 class="text-2xl font-bold text-zinc-900 mt-10 mb-4">§ 7 Vertraulichkeit</h2>
		<p class="text-lg leading-relaxed mb-8">
			Der Kunde ist verpflichtet, alle im Zusammenhang mit der Auftragsabwicklung erteilten Informationen, das Know-how und die Betriebsgeheimnisse streng vertraulich zu behandeln und Zeichnungen, Dokumentationen oder sonstige Unterlagen nicht ohne vorherige schriftliche Zustimmung der Emifree GmbH an Dritte weiterzugeben.
		</p>

		<h2 class="text-2xl font-bold text-zinc-900 mt-10 mb-4">§ 8 Datenschutzhinweis</h2>
		<p class="text-lg leading-relaxed mb-8">
			Informationen zur Erhebung, Speicherung und Verarbeitung personenbezogener Daten sind nicht Bestandteil dieser Geschäftsbedingungen und werden ausschließlich und gesondert durch die <a href="<?php echo esc_url( home_url( '/de/datenschutz/' ) ); ?>" class="text-blue-700 hover:text-blue-800 underline">Datenschutzerklärung</a> des Verkäufers geregelt.
		</p>

		<h2 class="text-2xl font-bold text-zinc-900 mt-10 mb-4">§ 9 Anwendbares Recht, Gerichtsstand &amp; Salvatorische Klausel</h2>
		<p class="text-lg leading-relaxed mb-4">(1) Die vertragliche Beziehung zwischen dem Verkäufer und dem Kunden unterliegt ausschließlich dem Recht der Bundesrepublik Deutschland. Die Anwendung des Übereinkommens der Vereinten Nationen über Verträge über den internationalen Warenkauf (CISG) ist ausdrücklich ausgeschlossen.</p>
		<p class="text-lg leading-relaxed mb-4">(2) Ausschließlicher Gerichtsstand für alle sich aus oder im Zusammenhang mit diesem Vertrag ergebenden Streitigkeiten ist der Sitz des Verkäufers in <strong>Berlin</strong>, sofern der Kunde Kaufmann im Sinne des HGB, juristische Person des öffentlichen Rechts oder öffentlich-rechtliches Sondervermögen ist. Der Verkäufer ist jedoch berechtigt, am Hauptsitz des Kunden Klage zu erheben.</p>
		<p class="text-lg leading-relaxed mb-10">(3) Sollten einzelne Bestimmungen dieser AGB unwirksam sein oder werden, bleibt die Wirksamkeit der übrigen Bestimmungen unberührt. An die Stelle der unwirksamen Bestimmung tritt eine wirksame Regelung, die dem wirtschaftlichen Zweck der unwirksamen Bestimmung am nächsten kommt.</p>

		<div class="mt-12 pt-8 border-t border-slate-200">
			<a href="<?php echo esc_url( $emifree_back_to_root ); ?>" class="inline-flex items-center gap-2 text-blue-700 hover:text-blue-800 font-medium focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 rounded">
				<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 12H5"></path>
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m12 19-7-7 7-7"></path>
				</svg>
				Zur Startseite
			</a>
		</div>
	</article>
	<?php
	return (string) ob_get_clean();
}

// Markdown helper moved to inc/markdown.php
require_once get_template_directory() . '/inc/markdown.php';
