<?php
/**
 * Emifree Theme — primary entry point.
 *
 * Responsibilities:
 *  - Enqueue built CSS (and per-section JS via wp_enqueue_script when added)
 *  - Declare theme support (title-tag, post-thumbnails)
 *  - Provide template helpers (loaded on demand in subsequent pieces)
 *  - Wire the Contact form AJAX handler (Piece 9)
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! defined( 'EMIFREE_THEME_VERSION' ) ) {
	define( 'EMIFREE_THEME_VERSION', '1.0.0' );
}

/**
 * Enqueue built stylesheet (assets/css/main.css, committed to the repo so
 * the theme is install-and-go). Also enqueues per-section JS files.
 * Per-section JS is loaded only on pages where the section actually
 * renders — header.js loads everywhere (header.php is global), the
 * others load only on the routes that use them.
 */
function emifree_enqueue_assets() {
	wp_enqueue_style(
		'emifree-main',
		get_template_directory_uri() . '/assets/css/main.css',
		array(),
		EMIFREE_THEME_VERSION
	);

	// Global header script — loaded on every page because the
	// header is rendered by header.php globally.
	wp_enqueue_script(
		'emifree-header',
		get_template_directory_uri() . '/assets/js/sections/header.js',
		array(),
		EMIFREE_THEME_VERSION,
		true
	);
}
add_action( 'wp_enqueue_scripts', 'emifree_enqueue_assets' );

/**
 * Theme support declarations. title-tag delegates <title> rendering to WP;
 * post-thumbnails enables featured image support.
 */
add_theme_support( 'title-tag' );
add_theme_support( 'post-thumbnails' );

/**
 * Legal page routing — /impressum/, /privacy/, /terms/.
 *
 * The page-{slug}.php templates (and the SEO/body data in inc/legal.php
 * + inc/seo.php) are on disk; only the URI-to-template binding is
 * missing. Rather than creating empty Pages in wp_posts, we route
 * the slugs directly to the page-{slug}.php templates via a single
 * add_rewrite_rule. WP then treats the URI as if a matching Page
 * existed, with the page-{slug}.php falling out of the template
 * hierarchy naturally. The page shims do their own SEO registration
 * + body rendering via emifree_seo_register() / emifree_render_legal_page_body().
 *
 * Rewrite rules are stored in the wp_options table; we flush them on
 * theme activation (switch_theme) so the binding lands without manual
 * permalink re-saves. Note: this is the "rewrite-on-activation"
 * standard pattern.
 */
function emifree_register_legal_routes() {
	add_rewrite_rule(
		'^impressum/?$',
		'index.php?emifree_legal=impressum',
		'top'
	);
	add_rewrite_rule(
		'^privacy/?$',
		'index.php?emifree_legal=privacy',
		'top'
	);
	add_rewrite_rule(
		'^terms/?$',
		'index.php?emifree_legal=terms',
		'top'
	);
}
add_action( 'init', 'emifree_register_legal_routes' );

/**
 * Expose the emifree_legal query var so WP recognizes it. The
 * template_redirect hook then routes the request to the right
 * page-{slug}.php template based on the query var.
 */
function emifree_register_legal_query_var( $vars ) {
	$vars[] = 'emifree_legal';
	return $vars;
}
add_filter( 'query_vars', 'emifree_register_legal_query_var' );

/**
 * On the template_redirect step, if the request carries our
 * emifree_legal query var, hand the template selection to the
 * matching page-{slug}.php template.
 */
function emifree_route_legal_template() {
	$emifree_slug = get_query_var( 'emifree_legal' );
	if ( ! $emifree_slug ) {
		return;
	}
	$emifree_templates = array(
		'impressum' => 'page-impressum',
		'privacy'   => 'page-privacy',
		'terms'     => 'page-terms',
	);
	if ( ! isset( $emifree_templates[ $emifree_slug ] ) ) {
		return;
	}
	// Include the page shim in WP's template hierarchy by hooking it
	// into template_include. This runs after WP's own template
	// resolution and lets us override with the right page-{slug}.php.
	add_filter(
		'template_include',
		static function ( $template ) use ( $emifree_templates, $emifree_slug ) {
			$emifree_target = locate_template( 'page-' . $emifree_slug . '.php' );
			return $emifree_target ? $emifree_target : $template;
		}
	);
}
add_action( 'template_redirect', 'emifree_route_legal_template' );

/**
 * Flush rewrite rules on theme activation so the legal routes take
 * effect immediately after install/switch. Without this, the user
 * would need to re-save permalinks under Settings > Permalinks to
 * see /impressum/ /privacy/ /terms/ resolve.
 */
function emifree_flush_legal_rewrite_rules() {
	emifree_register_legal_routes();
	flush_rewrite_rules( false );
}
add_action( 'after_switch_theme', 'emifree_flush_legal_rewrite_rules' );

/**
 * One-shot self-flush on the next page load after the routing code
 * was added. The transient flag (`emifree_legal_routes_flushed`)
 * is set after a successful flush, so this only runs once per deploy
 * (not on every request). After it fires, `after_switch_theme`
 * continues to handle future theme switches.
 */
function emifree_maybe_flush_legal_routes() {
	if ( get_transient( 'emifree_legal_routes_flushed' ) ) {
		return;
	}
	emifree_register_legal_routes();
	flush_rewrite_rules( false );
	set_transient( 'emifree_legal_routes_flushed', 1, DAY_IN_SECONDS );
}
add_action( 'init', 'emifree_maybe_flush_legal_routes', 99 );

/**
 * Per-section JS enqueuer.
 *
 * Template parts call emifree_enqueue_section_script( 'products' ) at
 * the top, before any output. Scripts are loaded in the footer. The
 * is_admin() guard prevents loading on WP admin screens where
 * front-page.php isn't used.
 */
function emifree_enqueue_section_script( $emifree_section_slug ) {
	if ( is_admin() ) {
		return;
	}
	$emifree_section_handle = 'emifree-section-' . sanitize_key( $emifree_section_slug );
	$emifree_section_path   = get_template_directory() . '/assets/js/sections/' . sanitize_key( $emifree_section_slug ) . '.js';
	if ( file_exists( $emifree_section_path ) ) {
		wp_enqueue_script(
			$emifree_section_handle,
			get_template_directory_uri() . '/assets/js/sections/' . sanitize_key( $emifree_section_slug ) . '.js',
			array(),
			EMIFREE_THEME_VERSION,
			true
		);
	}
}

/**
 * Contact section — localizes the AJAX endpoint + nonce alongside the
 * per-section JS, then enqueues the script.
 *
 * Used by template-parts/section-contact.php. Distinct from
 * emifree_enqueue_section_script() because we need wp_localize_script()
 * to expose ajaxUrl/nonce to the JS, which the generic helper doesn't.
 *
 * Mirrors the localized strings used in the React source:
 *  - Success: "Message sent successfully! We'll get back to you as soon as possible."
 *  - Error:   a friendly fallback (real validation messages come from
 *    the server with per-field details).
 */
function emifree_enqueue_contact_script() {
	if ( is_admin() ) {
		return;
	}
	$emifree_handle = 'emifree-section-contact';
	$emifree_path   = get_template_directory() . '/assets/js/sections/contact.js';
	if ( ! file_exists( $emifree_path ) ) {
		return;
	}
	wp_enqueue_script(
		$emifree_handle,
		get_template_directory_uri() . '/assets/js/sections/contact.js',
		array(),
		EMIFREE_THEME_VERSION,
		true
	);
	wp_localize_script(
		$emifree_handle,
		'emifreeContact',
		array(
			'ajaxUrl'    => admin_url( 'admin-ajax.php' ),
			'nonce'      => wp_create_nonce( 'emifree_contact' ),
			'successMsg' => __( 'Message sent successfully! We\'ll get back to you as soon as possible.', 'emifree-theme' ),
			'errorMsg'   => __( 'Something went wrong. Please try again or email us directly.', 'emifree-theme' ),
		)
	);
}

/**
 * AJAX handler for the Contact form.
 *
 * Accepts (POST): action=send_contact, emifree_contact_nonce, name,
 * email, company, message. Sends wp_mail() to the recipient from
 * inc/contact.php and returns a JSON response.
 *
 * Registers for both logged-in and anonymous visitors via the two
 * add_action() calls below — wp_ajax_nopriv_* is the no-auth variant.
 */
function emifree_handle_contact_submit() {
	if ( ! isset( $_POST['emifree_contact_nonce'] )
		|| ! wp_verify_nonce(
			sanitize_text_field( wp_unslash( $_POST['emifree_contact_nonce'] ) ),
			'emifree_contact'
		)
	) {
		wp_send_json_error(
			array( 'message' => __( 'Security check failed. Please reload the page and try again.', 'emifree-theme' ) ),
			403
		);
	}

	$emifree_name    = isset( $_POST['name'] )    ? sanitize_text_field( wp_unslash( $_POST['name'] ) )          : '';
	$emifree_email   = isset( $_POST['email'] )   ? sanitize_email( wp_unslash( $_POST['email'] ) )               : '';
	$emifree_company = isset( $_POST['company'] ) ? sanitize_text_field( wp_unslash( $_POST['company'] ) )         : '';
	$emifree_message = isset( $_POST['message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['message'] ) )     : '';

	// Server-side re-validation — never trust the client.
	$emifree_errors = array();
	if ( strlen( $emifree_name ) < 2 ) {
		$emifree_errors['name'] = __( 'Name must be at least 2 characters.', 'emifree-theme' );
	}
	if ( ! is_email( $emifree_email ) ) {
		$emifree_errors['email'] = __( 'Please enter a valid email address.', 'emifree-theme' );
	}
	if ( strlen( $emifree_company ) < 2 ) {
		$emifree_errors['company'] = __( 'Company name must be at least 2 characters.', 'emifree-theme' );
	}
	if ( strlen( $emifree_message ) < 10 ) {
		$emifree_errors['message'] = __( 'Message must be at least 10 characters.', 'emifree-theme' );
	}
	if ( ! empty( $emifree_errors ) ) {
		wp_send_json_error(
			array(
				'message' => __( 'Please correct the highlighted fields.', 'emifree-theme' ),
				'fields'  => $emifree_errors,
			),
			400
		);
	}

	require_once get_template_directory() . '/inc/contact.php';
	$emifree_recipient = emifree_contact_recipient_email();
	$emifree_subject   = sprintf(
		'[%s] New contact form submission from %s',
		wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES ),
		$emifree_name
	);

	$emifree_body  = sprintf(
		"Name:    %s\nEmail:   %s\nCompany: %s\n\nMessage:\n%s\n",
		$emifree_name,
		$emifree_email,
		$emifree_company,
		$emifree_message
	);
	$emifree_body .= sprintf(
		"\n--\nSent: %s\nIP:   %s\nUA:   %s",
		current_time( 'mysql' ),
		isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '-',
		isset( $_SERVER['HTTP_USER_AGENT'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) ) : '-'
	);

	$emifree_headers = array( 'Reply-To: ' . $emifree_name . ' <' . $emifree_email . '>' );
	$emifree_sent    = wp_mail( $emifree_recipient, $emifree_subject, $emifree_body, $emifree_headers );

	if ( ! $emifree_sent ) {
		// Don't leak server config to the form. Log internally; tell
		// the user to email us directly (the recipient address is shown
		// in the contact-info cards just above the form).
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			error_log( '[emifree-contact] wp_mail() failed for recipient: ' . $emifree_recipient );
		}
		wp_send_json_error(
			array( 'message' => __( 'We couldn\'t send your message automatically. Please email us directly at info@emifree.com.', 'emifree-theme' ) ),
			500
		);
	}

	wp_send_json_success(
		array( 'message' => __( 'Message sent successfully! We\'ll get back to you as soon as possible.', 'emifree-theme' ) )
	);
}
add_action( 'wp_ajax_send_contact', 'emifree_handle_contact_submit' );
add_action( 'wp_ajax_nopriv_send_contact', 'emifree_handle_contact_submit' );