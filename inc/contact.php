<?php
/**
 * Contact data + SVG icons.
 *
 * Mirrors src/components/Contact.jsx from the React app post-cleanup
 * (no KPI strip, "Direction" not "Visit Us"). Used by:
 *  - the homepage Contact section (template-parts/section-contact.php),
 *    which renders the form + the three contact-info cards;
 *  - the AJAX handler in functions.php, which reads the recipient email
 *    via emifree_contact_recipient_email().
 *
 * Icons are inline SVG paths from lucide-react (24x24 viewBox,
 * stroke-based) — no external icon library required.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'emifree_contact_icons' ) ) :
	function emifree_contact_icons() {
		return array(
			'mail'    => '<rect width="16" height="16" x="3" y="5" rx="2"></rect><path d="m3 7 9 6 9-6"></path>',
			'phone'   => '<path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>',
			'map-pin' => '<path d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0"></path><circle cx="12" cy="10" r="3"></circle>',
			'send'    => '<path d="m22 2-7 20-4-9-9-4Z"></path><path d="M22 2 11 13"></path>',
			'loader'  => '<path d="M21 12a9 9 0 1 1-6.219-8.56"></path>',
		);
	}
endif;

if ( ! function_exists( 'emifree_contact_info' ) ) :
	function emifree_contact_info() {
		return array(
			array(
				'icon'        => 'mail',
				'title'       => 'Email Us',
				'content'     => 'info@emifree.com',
				'description' => 'Send us an email anytime',
				'href'        => 'mailto:info@emifree.com',
			),
			array(
				'icon'        => 'phone',
				'title'       => 'Call Us',
				'content'     => '+49 307 628 3520',
				'description' => 'Mon-Fri from 8am to 6pm',
				'href'        => 'tel:+493076283520',
			),
			array(
				'icon'        => 'map-pin',
				'title'       => 'Direction',
				'content'     => 'Pestalozzistraße 13',
				'description' => '12557 Berlin, Germany.',
				'href'        => '',
			),
		);
	}
endif;

if ( ! function_exists( 'emifree_contact_recipient_email' ) ) :
	/**
	 * The address that the contact-form's wp_mail() delivers to.
	 *
	 * This is the inbox that "leads" land in — it is *not* the address
	 * shown to visitors on the page (the Email Us card on each Contact
	 * section still shows info@emifree.com by design). The default
	 * here is contact.website@emifree.com so that production form
	 * submissions auto-route into the lead-handling inbox even before
	 * wp-config.php is updated.
	 *
	 * Override per environment via wp-config.php:
	 *
	 *   define( 'EMIFREE_CONTACT_RECIPIENT_EMAIL', 'dev@yourdomain.com' );
	 */
	function emifree_contact_recipient_email() {
		if ( defined( 'EMIFREE_CONTACT_RECIPIENT_EMAIL' ) && EMIFREE_CONTACT_RECIPIENT_EMAIL ) {
			return EMIFREE_CONTACT_RECIPIENT_EMAIL;
		}
		return 'contact.website@emifree.com';
	}
endif;