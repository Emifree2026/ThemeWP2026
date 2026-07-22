<?php
/**
 * Contact section — 4-field contact form (left) + 3 contact-info
 * cards (right). Mirrors src/components/Contact.jsx from the React
 * app post-cleanup (no KPI strip, "Direction" not "Visit Us").
 *
 * The form's submit is wired via assets/js/sections/contact.js to
 * admin-ajax.php with action=send_contact. The AJAX handler lives in
 * functions.php (emifree_handle_contact_submit).
 *
 * Per the user's decision, the section heading keeps the React's
 * gradient text on "cleaner air?" (explicit override of the no-AI-tells
 * rule for this section). The "Direction" card renders plain text with
 * no link (per user direction).
 *
 * The Inquiry CTAs in Products (data-emifree-inquiry) and Technology
 * (data-emifree-inquiry="technology") fall back to smooth-scroll to
 * #contact when the future Inquiry modal (Piece 10) hasn't shipped.
 */

require_once get_template_directory() . '/inc/i18n.php';
emifree_require_section_data( 'contact' );
emifree_enqueue_contact_script();

$emifree_contact_icons = emifree_contact_icons();
$emifree_contact_info   = emifree_contact_info();
?>

<section id="contact" class="py-12 md:py-24 bg-slate-50 md:bg-gradient-to-br md:from-slate-50 md:via-white md:to-blue-50 scroll-mt-20">
	<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

		<?php /* ----- Section header ----- */ ?>
		<div class="text-center mb-20">
			<h2 class="text-4xl md:text-5xl font-bold text-zinc-900 mb-6">
				Ready for <span class="text-blue-700">cleaner air?</span>
			</h2>
			<p class="text-xl text-zinc-600 max-w-3xl mx-auto">
				Plan your next filtration project with us and schedule a free consultation with our engineering team.
			</p>
		</div>

		<div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-start">

			<?php /* ----- Left column: contact form ----- */ ?>
			<div class="bg-white rounded-3xl p-8 shadow-xl">
				<h3 class="text-2xl font-bold text-zinc-900 mb-6">Send us a message</h3>

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
					<?php /* Server-side nonce — duplicated in emifreeContact.nonce for JS */ ?>
					<input type="hidden" name="emifree_contact_nonce" value="<?php echo esc_attr( wp_create_nonce( 'emifree_contact' ) ); ?>">

					<?php /*
					 * Antispam — Tier 1:
					 *
					 *   1. Timestamp (emifree_ts) — set to seconds-since-epoch at
					 *      page render time. JS contact.js also rewrites it on
					 *      DOMContentLoaded to be more precise, but the SSR
					 *      value is fine as a fallback for no-JS browsers. The
					 *      server rejects submissions where
					 *        (now - emifree_ts) < EMIFREE_CONTACT_MIN_SECONDS
					 *      or > EMIFREE_CONTACT_MAX_SECONDS, killing instant-
					 *      fire spam and stale-form-replay attacks.
					 *
					 *   2. Honeypot (website_url) — visually hidden (off-screen,
					 *      aria-hidden, tabindex -1) so real users never fill it.
					 *      Volume bots fill every field including this one; the
					 *      server rejects any submission where it's non-empty.
					 *      The name 'website_url' is what 90%+ of dumb bots
					 *      reflexively probe for; using an obvious name is the
					 *      whole point.
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
					 * label (e.g. "Mechanical Filtration") is human input
					 * from the same data-emifree-inquiry-label attribute,
					 * sanitized + length-capped before embedding in the
					 * email subject line.
					 */ ?>
					<input type="hidden" name="product" value="">
					<input type="hidden" name="product_label" value="">

					<?php /* Name */ ?>
					<div>
						<label for="emifree-contact-name" class="sr-only">Name</label>
						<input
							type="text"
							name="name"
							id="emifree-contact-name"
							required
							minlength="2"
							autocomplete="name"
							placeholder="Your Name"
							data-emifree-contact-field="name"
							class="emifree-contact-input w-full px-4 py-4 border-2 rounded-xl focus:outline-none focus:ring-0 transition-all duration-200 border-slate-200 focus:border-blue-500"
						>
						<p data-emifree-contact-error="name" class="hidden text-red-500 border-red-300 text-sm mt-2"></p>
					</div>

					<?php /* Email */ ?>
					<div>
						<label for="emifree-contact-email" class="sr-only">Email</label>
						<input
							type="email"
							name="email"
							id="emifree-contact-email"
							required
							autocomplete="email"
							inputmode="email"
							placeholder="Your Email"
							data-emifree-contact-field="email"
							class="emifree-contact-input w-full px-4 py-4 border-2 rounded-xl focus:outline-none focus:ring-0 transition-all duration-200 border-slate-200 focus:border-blue-500"
						>
						<p data-emifree-contact-error="email" class="hidden text-red-500 text-sm mt-2"></p>
					</div>

					<?php /* Company */ ?>
					<div>
						<label for="emifree-contact-company" class="sr-only">Company Name</label>
						<input
							type="text"
							name="company"
							id="emifree-contact-company"
							required
							minlength="2"
							autocomplete="organization"
							placeholder="Company Name"
							data-emifree-contact-field="company"
							class="emifree-contact-input w-full px-4 py-4 border-2 rounded-xl focus:outline-none focus:ring-0 transition-all duration-200 border-slate-200 focus:border-blue-500"
						>
						<p data-emifree-contact-error="company" class="hidden text-red-500 text-sm mt-2"></p>
					</div>

					<?php /* Message */ ?>
					<div>
						<label for="emifree-contact-message" class="sr-only">Message</label>
						<textarea
							name="message"
							id="emifree-contact-message"
							required
							minlength="10"
							rows="5"
							placeholder="Tell us about your project..."
							data-emifree-contact-field="message"
							class="emifree-contact-input w-full px-4 py-4 border-2 rounded-xl focus:outline-none focus:ring-0 transition-all duration-200 border-slate-200 focus:border-blue-500 resize-y"
						></textarea>
						<p data-emifree-contact-error="message" class="hidden text-red-500 text-sm mt-2"></p>
					</div>

					<?php /* Submit button */ ?>
					<button
						type="submit"
						id="emifree-contact-submit"
						class="w-full text-white py-4 rounded-xl font-semibold text-lg hover:shadow-xl transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2"
						style="background: linear-gradient(90deg, #1d4ed8 0%, #06b6d4 100%); box-shadow: 0 10px 25px rgba(6, 182, 212, 0.25);"
					>
						<span data-emifree-contact-submit-label>Send Message</span>
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

			<?php /* ----- Right column: contact info cards ----- */ ?>
			<div>
				<h3 class="text-2xl font-bold text-zinc-900 mb-6">Get in touch</h3>
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