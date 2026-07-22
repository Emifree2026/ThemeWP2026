<?php
/**
 * Contact section — German.
 *
 * Hard-coded translation of section-contact.php. 4-field contact form
 * (left) + 3 contact-info cards (right). Form submit is wired to the
 * same admin-ajax.php endpoint as the English form. The AJAX handler
 * in functions.php reads emifree_contact_recipient_email(), which
 * returns contact.website@emifree.com by default (overridable via
 * the EMIFREE_CONTACT_RECIPIENT_EMAIL wp-config constant) regardless
 * of language. The Email Us card shown to visitors below still
 * displays info@emifree.com by design — the visible-card address
 * and the form-submission inbox are intentionally separate.
 *
 * Per-section JS (assets/js/sections/contact.js) is language-agnostic
 * — it operates on data-emifree-* attributes. The localized success /
 * error messages come from wp_localize_script() in functions.php, which
 * uses WordPress __() (default text-domain) — those strings are
 * already English. Localizing them is a separate piece (header/footer
 * chrome is out of scope per the user's "homepage sections only" choice).
 */
emifree_enqueue_contact_script();

$emifree_contact_icons = array(
	'mail'    => '<rect width="16" height="16" x="3" y="5" rx="2"></rect><path d="m3 7 9 6 9-6"></path>',
	'phone'   => '<path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>',
	'map-pin' => '<path d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0"></path><circle cx="12" cy="10" r="3"></circle>',
	'send'    => '<path d="m22 2-7 20-4-9-9-4Z"></path><path d="M22 2 11 13"></path>',
	'loader'  => '<path d="M21 12a9 9 0 1 1-6.219-8.56"></path>',
);

$emifree_contact_info = array(
	array(
		'icon'        => 'mail',
		'title'       => 'E-Mail an uns',
		'content'     => 'info@emifree.com',
		'description' => 'Schreiben Sie uns jederzeit eine E-Mail',
		'href'        => 'mailto:info@emifree.com',
	),
	array(
		'icon'        => 'phone',
		'title'       => 'Rufen Sie uns an',
		'content'     => '+49 307 628 3520',
		'description' => 'Mo–Fr von 8 bis 18 Uhr',
		'href'        => 'tel:+493076283520',
	),
	array(
		'icon'        => 'map-pin',
		'title'       => 'Anfahrt',
		'content'     => 'Pestalozzistraße 13',
		'description' => '12557 Berlin, Deutschland.',
		'href'        => '',
	),
);
?>

<section id="contact" class="py-12 md:py-24 bg-slate-50 md:bg-gradient-to-br md:from-slate-50 md:via-white md:to-blue-50 scroll-mt-20">
	<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

		<div class="text-center mb-20">
			<h2 class="text-4xl md:text-5xl font-bold text-zinc-900 mb-6">
				Bereit für <span class="text-blue-700">sauberere Luft?</span>
			</h2>
			<p class="text-xl text-zinc-600 max-w-3xl mx-auto">
				Planen Sie Ihr nächstes Filtrationsprojekt mit uns und vereinbaren Sie eine kostenlose Beratung mit unserem Engineering-Team.
			</p>
		</div>

		<div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-start">

			<div class="bg-white rounded-3xl p-8 shadow-xl">
				<h3 class="text-2xl font-bold text-zinc-900 mb-6">Schreiben Sie uns eine Nachricht</h3>

				<div
					id="emifree-contact-result"
					class="hidden rounded-xl p-4 mb-6 text-sm"
					role="status"
					aria-live="polite"
				></div>

				<form
					id="emifree-contact-form"
					class="space-y-6"
					method="post"
					action="<?php echo esc_url( admin_url( 'admin-ajax.php' ) . '?action=send_contact' ); ?>"
					novalidate
				>
					<input type="hidden" name="emifree_contact_nonce" value="<?php echo esc_attr( wp_create_nonce( 'emifree_contact' ) ); ?>">

					<?php /*
					 * Antispam — Tier 1: timestamp + honeypot.
					 * See EN sibling (template-parts/section-contact.php) for
					 * the full rationale — identical implementation.
					 */ ?>
					<input type="hidden" name="emifree_ts" value="<?php echo esc_attr( time() ); ?>">
					<input
						type="text"
						name="website_url"
						tabindex="-1"
						autocomplete="off"
						aria-hidden="true"
						style="position: absolute; left: -9999px; width: 1px; height: 1px; overflow: hidden;"
					>

					<?php /*
					 * Product-of-interest tag — populated by contact.js when
					 * the visitor clicks a product-section "Request Quote"
					 * CTA. The slug (e.g. "mechanical") is whitelisted
					 * server-side in emifree_handle_contact_submit(); the
					 * label (e.g. "Mechanische Filtration") is human input
					 * from the same data-emifree-inquiry-label attribute,
					 * sanitized + length-capped before embedding in the
					 * email subject line. Mirrors the EN template's hidden
					 * inputs above.
					 */ ?>
					<input type="hidden" name="product" value="">
					<input type="hidden" name="product_label" value="">

					<div>
						<label for="emifree-contact-name" class="sr-only">Name</label>
						<input
							type="text"
							name="name"
							id="emifree-contact-name"
							required
							minlength="2"
							autocomplete="name"
							placeholder="Ihr Name"
							data-emifree-contact-field="name"
							class="emifree-contact-input w-full px-4 py-4 border-2 rounded-xl focus:outline-none focus:ring-0 transition-all duration-200 border-slate-200 focus:border-blue-500"
						>
						<p data-emifree-contact-error="name" class="hidden text-red-500 border-red-300 text-sm mt-2"></p>
					</div>

					<div>
						<label for="emifree-contact-email" class="sr-only">E-Mail</label>
						<input
							type="email"
							name="email"
							id="emifree-contact-email"
							required
							autocomplete="email"
							inputmode="email"
							placeholder="Ihre E-Mail"
							data-emifree-contact-field="email"
							class="emifree-contact-input w-full px-4 py-4 border-2 rounded-xl focus:outline-none focus:ring-0 transition-all duration-200 border-slate-200 focus:border-blue-500"
						>
						<p data-emifree-contact-error="email" class="hidden text-red-500 text-sm mt-2"></p>
					</div>

					<div>
						<label for="emifree-contact-company" class="sr-only">Firmenname</label>
						<input
							type="text"
							name="company"
							id="emifree-contact-company"
							required
							minlength="2"
							autocomplete="organization"
							placeholder="Firmenname"
							data-emifree-contact-field="company"
							class="emifree-contact-input w-full px-4 py-4 border-2 rounded-xl focus:outline-none focus:ring-0 transition-all duration-200 border-slate-200 focus:border-blue-500"
						>
						<p data-emifree-contact-error="company" class="hidden text-red-500 text-sm mt-2"></p>
					</div>

					<div>
						<label for="emifree-contact-message" class="sr-only">Nachricht</label>
						<textarea
							name="message"
							id="emifree-contact-message"
							required
							minlength="10"
							rows="5"
							placeholder="Erzählen Sie uns von Ihrem Projekt..."
							data-emifree-contact-field="message"
							class="emifree-contact-input w-full px-4 py-4 border-2 rounded-xl focus:outline-none focus:ring-0 transition-all duration-200 border-slate-200 focus:border-blue-500 resize-y"
						></textarea>
						<p data-emifree-contact-error="message" class="hidden text-red-500 text-sm mt-2"></p>
					</div>

					<button
						type="submit"
						id="emifree-contact-submit"
						class="w-full text-white py-4 rounded-xl font-semibold text-lg hover:shadow-xl transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2"
						style="background: linear-gradient(90deg, #1d4ed8 0%, #06b6d4 100%); box-shadow: 0 10px 25px rgba(6, 182, 212, 0.25);"
					>
						<span data-emifree-contact-submit-label>Nachricht senden</span>
						<svg
							data-emifree-contact-submit-icon-idle
							class="w-5 h-5"
							fill="none"
							stroke="currentColor"
							stroke-width="2"
							stroke-linecap="round"
							stroke-linejoin="round"
							viewBox="0 0 24 24"
							aria-hidden="true"
						>
							<?php echo $emifree_contact_icons['send']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped — SVG markup, controlled. ?>
						</svg>
						<svg
							data-emifree-contact-submit-icon-loading
							class="hidden w-5 h-5 animate-spin"
							fill="none"
							stroke="currentColor"
							stroke-width="2"
							stroke-linecap="round"
							stroke-linejoin="round"
							viewBox="0 0 24 24"
							aria-hidden="true"
						>
							<?php echo $emifree_contact_icons['loader']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped — SVG markup, controlled. ?>
						</svg>
					</button>
				</form>
			</div>

			<div>
				<h3 class="text-2xl font-bold text-zinc-900 mb-6">Kontakt aufnehmen</h3>
				<div class="space-y-6">
					<?php foreach ( $emifree_contact_info as $emifree_info ) : ?>
						<div class="bg-white rounded-2xl p-6 shadow-lg hover:shadow-xl transition-all duration-300 flex items-start gap-4">
							<div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0" style="background: linear-gradient(135deg, #2563eb 0%, #06b6d4 100%); box-shadow: 0 8px 20px rgba(37, 99, 235, 0.2);">
								<svg
									class="w-6 h-6 text-white"
									fill="none"
									stroke="currentColor"
									stroke-width="2"
									stroke-linecap="round"
									stroke-linejoin="round"
									viewBox="0 0 24 24"
									aria-hidden="true"
								>
									<?php echo $emifree_contact_icons[ $emifree_info['icon'] ]; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped — SVG markup, controlled. ?>
								</svg>
							</div>
							<div>
								<h4 class="font-semibold text-zinc-900 mb-1">
									<?php echo esc_html( $emifree_info['title'] ); ?>
								</h4>
								<?php if ( ! empty( $emifree_info['href'] ) ) : ?>
									<a
										href="<?php echo esc_url( $emifree_info['href'] ); ?>"
										class="text-slate-700 hover:text-blue-700 transition-colors duration-200"
									>
										<?php echo esc_html( $emifree_info['content'] ); ?>
									</a>
								<?php else : ?>
									<p class="text-slate-700">
										<?php echo esc_html( $emifree_info['content'] ); ?>
									</p>
								<?php endif; ?>
								<p class="text-sm text-slate-500 mt-1">
									<?php echo esc_html( $emifree_info['description'] ); ?>
								</p>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
			</div>

		</div>

	</div>
</section>