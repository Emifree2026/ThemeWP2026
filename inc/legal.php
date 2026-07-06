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
function emifree_legal_page( $slug ) {
	switch ( $slug ) {
		case 'impressum':
			return array(
				'title'       => 'Impressum · Emifree GmbH',
				'description' => 'Legal notice for Emifree GmbH, Berlin — Managing Director Ingo Wagner, HRB 133977 B, VAT DE 815286735.',
				'url'         => EMIFREE_SITE_URL . '/impressum',
				'schema'      => array(
					'@context'      => 'https://schema.org',
					'@type'         => 'WebPage',
					'name'          => 'Impressum',
					'url'           => EMIFREE_SITE_URL . '/impressum',
					'inLanguage'    => 'en',
					'description'   => 'Legal notice for Emifree GmbH, Berlin — Managing Director Ingo Wagner, HRB 133977 B, VAT DE 815286735.',
					'publisher'     => array(
						'@type' => 'Organization',
						'name'  => 'Emifree GmbH',
						'url'   => EMIFREE_SITE_URL,
					),
				),
			);
		case 'privacy':
			return array(
				'title'       => 'Privacy Policy · Emifree GmbH',
				'description' => 'Privacy policy for the Emifree GmbH website — GDPR-compliant notice on data collection, processing, your rights, and the cookies/plugins we use.',
				'url'         => EMIFREE_SITE_URL . '/privacy',
				'schema'      => array(
					'@context'      => 'https://schema.org',
					'@type'         => 'WebPage',
					'name'          => 'Privacy Policy',
					'url'           => EMIFREE_SITE_URL . '/privacy',
					'inLanguage'    => 'en',
					'description'   => 'Privacy policy for the Emifree GmbH website — GDPR-compliant notice on data collection, processing, your rights, and the cookies/plugins we use.',
					'publisher'     => array(
						'@type' => 'Organization',
						'name'  => 'Emifree GmbH',
						'url'   => EMIFREE_SITE_URL,
					),
				),
			);
		case 'terms':
			return array(
				'title'       => 'General Terms and Conditions (GTC) · Emifree GmbH',
				'description' => 'Emifree GmbH General Terms and Conditions (GTC) for B2B sales of industrial air filtration systems. Applicable law: Federal Republic of Germany. Exclusive jurisdiction: Berlin.',
				'url'         => EMIFREE_SITE_URL . '/terms',
				'schema'      => array(
					'@context'      => 'https://schema.org',
					'@type'         => 'WebPage',
					'name'          => 'General Terms and Conditions',
					'url'           => EMIFREE_SITE_URL . '/terms',
					'inLanguage'    => 'en',
					'description'   => 'Emifree GmbH General Terms and Conditions (GTC) for B2B sales of industrial air filtration systems. Applicable law: Federal Republic of Germany. Exclusive jurisdiction: Berlin.',
					'publisher'     => array(
						'@type' => 'Organization',
						'name'  => 'Emifree GmbH',
						'url'   => EMIFREE_SITE_URL,
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
function emifree_render_legal_body( $slug ) {
	switch ( $slug ) {
		case 'impressum':
			return emifree_render_impressum_body();

		case 'privacy':
			return emifree_render_privacy_body();

		case 'terms':
			return emifree_render_terms_body();
	}
	return '';
}

/* -------------------------------------------------------------------------
 * Individual page body renderers.
 *
 * Output is composed via string concatenation (ob_* buffering) so we
 * don't tangle PHP tags inside HTML — cleaner than mixing them.
 * ------------------------------------------------------------------------- */

function emifree_render_impressum_body(): string {
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
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="inline-flex items-center gap-2 text-blue-700 hover:text-blue-800 font-medium focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 rounded">
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

function emifree_render_privacy_body(): string {
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
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="inline-flex items-center gap-2 text-blue-700 hover:text-blue-800 font-medium focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 rounded">
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

function emifree_render_terms_body(): string {
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
			Information concerning the collection, storage, and processing of personal data does not form part of these commercial terms and conditions and is governed exclusively and separately by the Seller's designated <a href="/privacy" class="text-blue-700 hover:text-blue-800 underline">Privacy Policy</a>.
		</p>

		<h2 class="text-2xl font-bold text-zinc-900 mt-10 mb-4">§ 9 Governing Law, Jurisdiction &amp; Severability</h2>
		<p class="text-lg leading-relaxed mb-4">(1) The contractual relationship between the Seller and the customer shall be governed exclusively by the laws of the Federal Republic of Germany. The application of the UN Convention on Contracts for the International Sale of Goods (CISG) is explicitly excluded.</p>
		<p class="text-lg leading-relaxed mb-4">(2) The exclusive place of jurisdiction for all disputes arising out of or in connection with this contract is the registered corporate seat of the Seller in <strong>Berlin</strong>, provided that the customer is a merchant within the meaning of the HGB, a legal entity under public law, or a special fund under public law. However, the Seller remains entitled to file a suit at the customer's primary place of business.</p>
		<p class="text-lg leading-relaxed mb-10">(3) Should individual provisions of these terms be or become invalid, the validity of the remaining provisions shall remain unaffected. The invalid provision shall be replaced by a valid clause that comes closest to the economic intent of the original text.</p>

		<div class="mt-12 pt-8 border-t border-slate-200">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="inline-flex items-center gap-2 text-blue-700 hover:text-blue-800 font-medium focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 rounded">
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