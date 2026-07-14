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

// i18n.php shim — kept so the English section templates continue to
// work unchanged. The bilingual dispatcher (emifree_get_lang +
// function-guard approach) was retired; the German templates inline
// their own data. See inc/i18n.php for the full rationale.
require_once get_template_directory() . '/inc/i18n.php';

// SEO helpers — defines emifree_seo_page(), emifree_seo_page_with_schema(),
// emifree_seo_blog_post(), and the EMIFREE_SITE_URL constant. Used by
// page-blog.php, page-blog-post.php, and the German blog shim siblings.
// Loaded globally so every page template can call into it; defining
// functions inside inc/seo.php is idempotent (no re-declare errors).
require_once get_template_directory() . '/inc/seo.php';

/**
 * Get the active site language code ('en' or 'de') for the Header
 * dispatcher. Path is the source of truth — a request to /de/...
 * always resolves to 'de', even on a first visit with no cookie
 * (e.g. after the / → /de/ 301 redirect lands a fresh user on /de/).
 * The emifree_lang cookie is the fallback for routes that don't
 * carry the language prefix (/impressum/, /blog/, etc.); default
 * is 'en'. Mirrors the path-then-cookie pattern in inc/nav.php and
 * inc/footer.php.
 */
function emifree_get_lang() {
	$emifree_uri = isset( $_SERVER['REQUEST_URI'] ) ? wp_unslash( $_SERVER['REQUEST_URI'] ) : '';
	if ( 0 === strpos( $emifree_uri, '/de' ) ) {
		return 'de';
	}
	if ( ! isset( $_COOKIE['emifree_lang'] ) ) {
		return 'en';
	}
	$emifree_raw = strtolower( sanitize_text_field( wp_unslash( $_COOKIE['emifree_lang'] ) ) );
	return in_array( $emifree_raw, array( 'en', 'de' ), true ) ? $emifree_raw : 'en';
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
		'index.php?emifree_legal=impressum&emifree_lang=en',
		'top'
	);
	add_rewrite_rule(
		'^privacy/?$',
		'index.php?emifree_legal=privacy&emifree_lang=en',
		'top'
	);
	add_rewrite_rule(
		'^terms/?$',
		'index.php?emifree_legal=terms&emifree_lang=en',
		'top'
	);
	// German (de) routes — slug names match the German page names
	// (impressum unchanged, datenschutz, agb).
	add_rewrite_rule(
		'^de/impressum/?$',
		'index.php?emifree_legal=impressum&emifree_lang=de',
		'top'
	);
	add_rewrite_rule(
		'^de/datenschutz/?$',
		'index.php?emifree_legal=datenschutz&emifree_lang=de',
		'top'
	);
	add_rewrite_rule(
		'^de/agb/?$',
		'index.php?emifree_legal=agb&emifree_lang=de',
		'top'
	);
}
add_action( 'init', 'emifree_register_legal_routes' );

/**
 * Expose the emifree_legal and emifree_lang query vars so WP
 * recognizes them. The template_redirect hook then routes the
 * request to the right page-{slug}.php template based on the
 * query var.
 */
function emifree_register_legal_query_var( $vars ) {
	$vars[] = 'emifree_legal';
	$vars[] = 'emifree_lang';
	return $vars;
}
add_filter( 'query_vars', 'emifree_register_legal_query_var' );

/**
 * On the template_redirect step, if the request carries our
 * emifree_legal query var, hand the template selection to the
 * matching page-{slug}.php template. The slug includes the
 * language prefix (e.g. "impressum" for English, "impressum"
 * for German — same slug because we use one template per page
 * that dispatches on emifree_lang).
 */
function emifree_route_legal_template() {
	$emifree_slug = get_query_var( 'emifree_legal' );
	$emifree_lang = get_query_var( 'emifree_lang' );
	if ( ! $emifree_slug ) {
		return;
	}
	$emifree_templates = array(
		// English
		'impressum'   => 'page-impressum.php',
		'privacy'     => 'page-privacy.php',
		'terms'       => 'page-terms.php',
		// German
		'datenschutz' => 'page-de-datenschutz.php',
		'agb'         => 'page-de-agb.php',
	);
	// German Impressum uses the same slug as English (just /de/impressum/).
	if ( 'de' === $emifree_lang && 'impressum' === $emifree_slug ) {
		$emifree_template = 'page-de-impressum.php';
	} elseif ( isset( $emifree_templates[ $emifree_slug ] ) ) {
		$emifree_template = $emifree_templates[ $emifree_slug ];
	} else {
		return;
	}
	if ( ! isset( $emifree_template ) ) {
		return;
	}
	add_filter(
		'template_include',
		static function ( $template ) use ( $emifree_template ) {
			$emifree_target = locate_template( $emifree_template );
			return $emifree_target ? $emifree_target : $template;
		}
	);
}
add_action( 'template_redirect', 'emifree_route_legal_template' );

/**
 * Flush rewrite rules on theme activation so the legal + blog routes
 * take effect immediately after install/switch. Without this, the
 * user would need to re-save permalinks under Settings > Permalinks
 * to see /impressum/ /privacy/ /terms/ /blog/ resolve.
 */
function emifree_flush_section_rewrite_rules() {
	emifree_register_legal_routes();
	emifree_register_blog_route();
	flush_rewrite_rules( false );
}
add_action( 'after_switch_theme', 'emifree_flush_section_rewrite_rules' );

/**
 * One-shot self-flush on the next page load after the routing code
 * was added. The transient flag (`emifree_section_routes_flushed`)
 * is set after a successful flush, so this only runs once per deploy
 * (not on every request). After it fires, `after_switch_theme`
 * continues to handle future theme switches.
 *
 * Also clears the legacy `emifree_legal_routes_flushed` transient
 * from earlier versions (Piece 12-14) so installs that already
 * flushed under the old name won't have a stale flag blocking the
 * unified flush from firing.
 */
function emifree_maybe_flush_section_routes() {
	if ( get_transient( 'emifree_section_routes_flushed_v6' ) ) {
		return;
	}
	emifree_register_legal_routes();
	emifree_register_blog_route();
	emifree_register_homepage_lang_route();
	// Hard flush (true) — the soft flush (false) only updates when rules
	// changed, which can leave stale v4 rules in the DB if the v4 transient
	// was set under a different code path. Hard flush is idempotent and
	// safe to call on every version bump.
	flush_rewrite_rules( true );
	delete_transient( 'emifree_legal_routes_flushed' );
	delete_transient( 'emifree_section_routes_flushed' );
	delete_transient( 'emifree_section_routes_flushed_v2' );
	delete_transient( 'emifree_section_routes_flushed_v3' );
	delete_transient( 'emifree_section_routes_flushed_v4' );
	delete_transient( 'emifree_section_routes_flushed_v5' );
	set_transient( 'emifree_section_routes_flushed_v6', 1, DAY_IN_SECONDS );
}
add_action( 'init', 'emifree_maybe_flush_section_routes', 99 );

/* -------------------------------------------------------------------------
 * /blog/ route — same plumbing pattern as the legal routes.
 *
 * Routes /blog/ to page-blog.php without requiring a Page record in
 * wp_posts. Page-blog.php handles its own SEO + body rendering.
 *
 * The German equivalents (/de/blog/, /de/blog/{slug}/) registered
 * alongside set emifree_blog_lang=de so the dispatcher can pick the
 * German sibling (page-blog-de.php, page-blog-post-de.php).
 * ------------------------------------------------------------------------- */

function emifree_register_blog_route() {
	add_rewrite_rule(
		'^blog/?$',
		'index.php?emifree_blog=index',
		'top'
	);
	add_rewrite_rule(
		'^blog/([^/]+)/?$',
		'index.php?emifree_blog=post&emifree_blog_slug=$matches[1]',
		'top'
	);
	// German (de) blog routes — slug names mirror the English ones
	// so the link (href) just adds the /de/ prefix.
	add_rewrite_rule(
		'^de/blog/?$',
		'index.php?emifree_blog=index&emifree_blog_lang=de',
		'top'
	);
	add_rewrite_rule(
		'^de/blog/([^/]+)/?$',
		'index.php?emifree_blog=post&emifree_blog_slug=$matches[1]&emifree_blog_lang=de',
		'top'
	);
}
add_action( 'init', 'emifree_register_blog_route' );

/**
 * Homepage language routes — /de/ and /en/.
 *
 * Each route registers as a SEPARATE WP rewrite so the homepage serves
 * the correct language even if the user's cookie is unset. We deliberately
 * don't tie these routes to a cookie: the language switcher sets the
 * cookie AND navigates, but a user who lands on /de/ directly (e.g. via
 * a Google search) sees German regardless of any saved preference, and
 * an English user hitting /en/ sees English even if their cookie expired.
 *
 * The bare / (and the alternate WP URL /index.php) is redirected to /de/
 * by emifree_maybe_redirect_home_to_de() below — that's the default-lang
 * flip, not this dispatcher.
 */
function emifree_register_homepage_lang_route() {
	add_rewrite_rule(
		'^de/?$',
		'index.php?emifree_homepage_lang=de',
		'top'
	);
	add_rewrite_rule(
		'^en/?$',
		'index.php?emifree_homepage_lang=en',
		'top'
	);
}
add_action( 'init', 'emifree_register_homepage_lang_route' );

function emifree_register_homepage_lang_query_var( $vars ) {
	$vars[] = 'emifree_homepage_lang';
	return $vars;
}
add_filter( 'query_vars', 'emifree_register_homepage_lang_query_var' );

/**
 * Dispatch /de/ and /en/ to the matching homepage template.
 *
 * The bare / (and /index.php) is redirected to /de/ by
 * emifree_maybe_redirect_home_to_de() before this dispatcher runs,
 * so this callback only fires for the explicit /de/ and /en/
 * routes. Cookie-based language detection inside the templates
 * picks the right strings regardless — the dispatcher only picks
 * the template file.
 */
function emifree_route_homepage_lang_template() {
	$emifree_lang = get_query_var( 'emifree_homepage_lang' );
	if ( 'de' === $emifree_lang ) {
		$emifree_target = locate_template( 'front-page-de.php' );
	} elseif ( 'en' === $emifree_lang ) {
		$emifree_target = locate_template( 'front-page.php' );
	} else {
		return;
	}
	if ( ! $emifree_target ) {
		// Fall back to whatever the active front-page.php is so the
		// route serves content rather than 404'ing — better than a
		// blank white screen if a template file goes missing.
		$emifree_target = locate_template( 'front-page.php' );
	}
	add_filter(
		'template_include',
		static function ( $template ) use ( $emifree_target ) {
			return $emifree_target ? $emifree_target : $template;
		}
	);
}
add_action( 'template_redirect', 'emifree_route_homepage_lang_template' );

/**
 * Language-aware Header dispatcher.
 *
 * When the active language is German (emifree_lang cookie = 'de'),
 * every page that loads header.php via get_header() should instead
 * load header-de.php. We do this at template_include time by mapping
 * the resolved template to the German sibling.
 *
 * This avoids needing to modify every page template to call
 * get_header('de') — the dispatcher does it once, globally.
 *
 * Note: header-de.php is provided by the user (data file mirror of
 * header.php with German strings).
 */
function emifree_route_de_header_template( $template ) {
	if ( 'de' !== emifree_get_lang() ) {
		return $template;
	}
	// Only swap for the active theme's header.php (not third-party
	// plugin templates). The basename check handles that.
	$emifree_base = basename( $template );
	if ( 'header.php' !== $emifree_base ) {
		return $template;
	}
	$emifree_de_header = locate_template( 'header-de.php' );
	return $emifree_de_header ? $emifree_de_header : $template;
}
add_filter( 'template_include', 'emifree_route_de_header_template' );

/**
 * Default-language redirect — /  →  /de/.
 *
 * German is the primary language of this site (primary market is
 * Germany, traffic skews German). A fresh visitor with no
 * emifree_lang cookie who hits the bare homepage is bounced to
 * /de/ via a 301 permanent redirect. 301 is correct here — this
 * is a permanent flip, not a temporary routing decision — and
 * transfers any existing PageRank from / to /de/.
 *
 * The redirect only fires when:
 *   - the request URI is the bare homepage (or its /index.php
 *     alternate), so /impressum/, /blog/, /en/, /de/, etc. are
 *     untouched, and
 *   - the user does not have an emifree_lang=en cookie, so any
 *     English user who explicitly opted into English keeps seeing
 *     English on subsequent visits (the language switcher writes
 *     the cookie AND navigates to /en/).
 *
 * Priority 5 fires before the homepage template dispatcher at
 * priority 10, so the redirect wins when both could apply.
 */
function emifree_maybe_redirect_home_to_de() {
	if ( is_admin() ) {
		return;
	}
	$emifree_uri  = isset( $_SERVER['REQUEST_URI'] ) ? wp_unslash( $_SERVER['REQUEST_URI'] ) : '';
	$emifree_path = parse_url( $emifree_uri, PHP_URL_PATH );
	// Only the bare homepage — both with and without trailing slash,
	// plus the /index.php alternate URL WordPress may serve.
	if ( ! in_array( rtrim( (string) $emifree_path, '/' ), array( '', '/', '/index.php' ), true ) ) {
		return;
	}
	// Don't redirect English users (cookie opt-in).
	if ( isset( $_COOKIE['emifree_lang'] ) && 'en' === strtolower( sanitize_text_field( wp_unslash( $_COOKIE['emifree_lang'] ) ) ) ) {
		return;
	}
	wp_safe_redirect( home_url( '/de/' ), 301 );
	exit;
}
add_action( 'template_redirect', 'emifree_maybe_redirect_home_to_de', 5 );

function emifree_register_blog_query_var( $vars ) {
	$vars[] = 'emifree_blog';
	$vars[] = 'emifree_blog_slug';
	$vars[] = 'emifree_blog_lang';
	return $vars;
}
add_filter( 'query_vars', 'emifree_register_blog_query_var' );

function emifree_route_blog_template() {
	$emifree_blog_mode = get_query_var( 'emifree_blog' );
	if ( ! $emifree_blog_mode ) {
		return;
	}
	$emifree_is_de = ( 'de' === get_query_var( 'emifree_blog_lang' ) );

	if ( 'index' === $emifree_blog_mode ) {
		$emifree_template_name = $emifree_is_de ? 'page-blog-de.php' : 'page-blog.php';
	} elseif ( 'post' === $emifree_blog_mode ) {
		$emifree_template_name = $emifree_is_de ? 'page-blog-post-de.php' : 'page-blog-post.php';
	} else {
		return;
	}
	add_filter(
		'template_include',
		static function ( $template ) use ( $emifree_template_name ) {
			$emifree_target = locate_template( $emifree_template_name );
			return $emifree_target ? $emifree_target : $template;
		}
	);
}
add_action( 'template_redirect', 'emifree_route_blog_template' );

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
 * Tawk.to live chat widget.
 *
 * Renders the Tawk.to bootstrap inline (defining `window.Tawk_API`)
 * and then loads the actual widget script from tawk.to's CDN. Both
 * fire on wp_footer (priority 100 — late) so they don't block page
 * render.
 *
 * Property ID: 1jsu0245o (separate widget from the production
 * emifree.com widget, which uses 1jogl5hfo). If you want this local
 * site to share the same inbox as production, swap the property ID.
 *
 * Privacy note: per the Privacy Policy text, the Tawk.to widget
 * "will not load, and no data will be transferred until you grant
 * permission via the Cookiebot banner." That gate is currently NOT
 * implemented — the widget loads unconditionally, matching the
 * behavior of the React app's index.html. If you want strict
 * consent-gating here, swap this for a Cookiebot API call that
 * fires on consent.
 */
function emifree_enqueue_tawk_widget() {
	if ( is_admin() ) {
		return;
	}
	$emifree_tawk_property_id = '1jsu0245o';
	add_action(
		'wp_footer',
		static function () use ( $emifree_tawk_property_id ) {
			?>
			<!--Start of Tawk.to Script-->
			<script type="text/javascript">
			var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();
			(function(){
			var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
			s1.async=true;
			s1.src='https://embed.tawk.to/6a046f4de4f8631c3c9f3766/<?php echo esc_js( $emifree_tawk_property_id ); ?>';
			s1.charset='UTF-8';
			s1.setAttribute('crossorigin','*');
			s0.parentNode.insertBefore(s1,s0);
			})();
			</script>
			<!--End of Tawk.to Script-->
			<?php
		},
		100
	);
}
add_action( 'wp_enqueue_scripts', 'emifree_enqueue_tawk_widget' );

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