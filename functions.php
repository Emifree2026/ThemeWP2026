<?php
/**
 * Emifree Theme — primary entry point.
 *
 * Responsibilities:
 *  - Enqueue built CSS (and per-section JS via wp_enqueue_script when added)
 *  - Declare theme support (title-tag, post-thumbnails)
 *  - Provide template helpers (loaded on demand in subsequent pieces)
 *  - Wire the Contact form AJAX handler (Piece 9)
 *  - Wire analytics tag emission via inc/analytics.php (GA4 + GSC + Bing)
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! defined( 'EMIFREE_THEME_VERSION' ) ) {
	define( 'EMIFREE_THEME_VERSION', '1.0.1' );
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

// Analytics helpers — emits Google Analytics 4 + GSC + Bing Webmaster
// verification tags against wp_head. Each tag is gated on a wp-config
// constant (EMIFREE_GA4_ID, EMIFREE_GSC_VERIFICATION,
// EMIFREE_BING_VERIFICATION) so the same theme ships to staging +
// production with different IDs. Loaded globally so the head tags
// appear on every page that hits the theme, including the static
// front-page and the legal pages.
require_once get_template_directory() . '/inc/analytics.php';

// Blog CPT — blog_post custom post type + meta + sidebar meta box +
// slug-mirroring helper. Registered as invisible to the front end
// (rewrite=false, publicly_queryable=false) so the existing
// ^blog/([^/]+)/?$ rewrite in emifree_register_blog_route() stays the
// canonical source of routing. CPT entries are resolved per-request
// inside the page-blog-post*.php shims.
require_once get_template_directory() . '/inc/cpt-blog.php';

// SEO route surfaces — virtual /robots.txt + /sitemap.xml emitted by
// the theme. Single source of truth (no physical files at the
// document root), subpath-safe via home_url(). Each file owns its
// rewrite rule + query var + template_redirect handler.
require_once get_template_directory() . '/inc/robots.php';
require_once get_template_directory() . '/inc/sitemap.php';
// LLM manifest — /llms.txt + /de/llms.txt so AI crawlers (GPTBot,
// ClaudeBot, Perplexity, Google-Extended, etc.) get a single,
// structured, plain-text manifest of who we are, our product line,
// and the principal URLs the site serves. Same virtual-route
// pattern as robots.txt + sitemap.xml; loaded globally so the
// rewrite rules register before any request can land.
require_once get_template_directory() . '/inc/llms.php';

// IndexNow — fires wp_remote_post to api.indexnow.org on every
// blog_post save. Gated on EMIFREE_INDEXNOW_KEY + EMIFREE_INDEXNOW_HOST
// being non-empty (defaults to empty in wp-config); no-op locally.
require_once get_template_directory() . '/inc/indexnow.php';

// Built-in SMTP settings page (no plugin required). Registers
// Settings → Emifree SMTP and wires the saved credentials into
// PHPMailer via the phpmailer_init action so wp_mail() delivers
// through a real SMTP server on hosts without a local MTA.
require_once get_template_directory() . '/inc/smtp-settings.php';

// Long Cache-Control headers for static assets — videos, images,
// built CSS/JS. PageSpeed flagged an 8.8 MiB cache-dwell savings
// because LocalWP's nginx serves static files with max-age=300 (or
// no Cache-Control at all for video MIME types). Replacing the
// upstream headers from PHP via wp_headers works on every host
// (Apache, nginx, LiteSpeed, Cloudflare) and clears the audit.
require_once get_template_directory() . '/inc/cache-headers.php';

/**
 * Home subpath — the directory under which WordPress is installed
 * on this site, derived from home_url(). '' for a root install
 * (home_url returns 'https://example.com', no path component),
 * '/wordpress' for a subpath install (home_url returns
 * 'https://example.com/wordpress', path is '/wordpress').
 *
 * The site lives at one of these locations, and every internal path
 * we generate or compare against (e.g. '/de/', '/impressum/') is
 * RELATIVE to this subpath — not to the bare domain. The /de/
 * rewrite rule WP registers, for example, resolves against the
 * home subpath, so '/de/' on a root install becomes
 * 'https://example.com/de/' and on a subpath install becomes
 * 'https://example.com/wordpress/de/'.
 *
 * Used by emifree_get_lang(), emifree_maybe_redirect_home_to_de(),
 * the navigation / footer helpers, and the JS path-swap helper
 * (via wp_localize_script). Cached statically after first call so
 * the parse_url hit happens once per request.
 */
function emifree_home_subpath() {
	static $emifree_cached = null;
	if ( null !== $emifree_cached ) {
		return $emifree_cached;
	}
	$emifree_home_path = parse_url( home_url(), PHP_URL_PATH );
	$emifree_cached    = rtrim( (string) $emifree_home_path, '/' );
	return $emifree_cached;
}

/**
 * Get the active site language code ('en' or 'de') for the Header
 * dispatcher. Path is the source of truth — a request to /de/...
 * always resolves to 'de', even on a first visit with no cookie
 * (e.g. after the / → /de/ 301 redirect lands a fresh user on /de/).
 * The emifree_lang cookie is the fallback for routes that don't
 * carry the language prefix (/impressum/, /blog/, etc.); default
 * is 'en'. Mirrors the path-then-cookie pattern in inc/nav.php and
 * inc/footer.php.
 *
 * Subpath-aware: the /de prefix check is run against the URI with
 * the home subpath stripped, so '/wordpress/de/impressum/' on a
 * subpath install matches as well as '/de/impressum/' on a root
 * install.
 */
function emifree_get_lang() {
	$emifree_uri  = isset( $_SERVER['REQUEST_URI'] ) ? wp_unslash( $_SERVER['REQUEST_URI'] ) : '';
	$emifree_uri  = (string) parse_url( $emifree_uri, PHP_URL_PATH );
	$emifree_home = emifree_home_subpath();
	if ( '' !== $emifree_home && 0 === strpos( $emifree_uri, $emifree_home ) ) {
		$emifree_uri = substr( $emifree_uri, strlen( $emifree_home ) );
	}
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
	// The main stylesheet is loaded non-blocking via the standard
	// preload + media-swap pattern. The <link rel="preload"> hints the
	// browser to start the fetch in parallel with HTML parsing; the
	// onload handler promotes the asset to a real stylesheet once the
	// bytes arrive. Without this, PageSpeed Insights flags main.css as
	// a render-blocking resource (~300ms on mobile) even though the
	// file is only 35 KB — the bottleneck is the network RTT, not the
	// parse time.
	//
	// The <noscript> fallback keeps the stylesheet in the document for
	// visitors with JS disabled — without it the page would render
	// unstyled. The onload media-swap is the widely-supported
	// "print-trick" pattern: setting media="print" defers the
	// stylesheet's application (browsers don't apply print-media rules
	// to the screen), then flipping media to "all" once the file is
	// loaded. No JS framework needed, no FOUC for the rest of the
	// sections (the cached layout settles in <50ms once the swap fires).
	$emifree_css_url  = get_template_directory_uri() . '/assets/css/main.css';
	$emifree_css_ver  = EMIFREE_THEME_VERSION;
	add_action(
		'wp_head',
		static function () use ( $emifree_css_url, $emifree_css_ver ) {
			echo '<link rel="preload" href="' . esc_url( $emifree_css_url ) . '?ver=' . esc_attr( $emifree_css_ver ) . '" as="style" onload="this.onload=null;this.rel=\'stylesheet\'">' . "\n";
			echo '<noscript><link rel="stylesheet" href="' . esc_url( $emifree_css_url ) . '?ver=' . esc_attr( $emifree_css_ver ) . '"></noscript>' . "\n";
		},
		3
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

	// Page-level script — hero video autoplay retry, sticky header,
	// mobile menu toggle, contact form AJAX, smooth-scroll. Loaded
	// globally because the hero + header render on every page.
	wp_enqueue_script(
		'emifree-page',
		get_template_directory_uri() . '/assets/js/main.js',
		array( 'emifree-header' ),
		EMIFREE_THEME_VERSION,
		true
	);

	// Subpath metadata — header.js needs to know where WordPress is
	// installed so language-swap math and nav-link click handlers
	// preserve the install subpath on subpath installs like
	// /wordpress/. Mirrors inc/nav.php + inc/footer.php + emifree_get_lang()
	// which all strip emifree_home_subpath() before doing prefix checks.
	wp_localize_script(
		'emifree-header',
		'emifreeSite',
		array(
			'homeSubpath' => emifree_home_subpath(), // '' on root, '/wordpress' on /wordpress install
		)
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
	emifree_register_blog_cpt();
	if ( function_exists( 'emifree_register_robots_route' ) ) {
		emifree_register_robots_route();
	}
	if ( function_exists( 'emifree_register_sitemap_route' ) ) {
		emifree_register_sitemap_route();
	}
	if ( function_exists( 'emifree_register_llms_route' ) ) {
		emifree_register_llms_route();
	}
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
	if ( get_transient( 'emifree_section_routes_flushed_v8' ) ) {
		return;
	}
	emifree_register_legal_routes();
	emifree_register_blog_route();
	emifree_register_homepage_lang_route();
	emifree_register_blog_cpt();
	if ( function_exists( 'emifree_register_robots_route' ) ) {
		emifree_register_robots_route();
	}
	if ( function_exists( 'emifree_register_sitemap_route' ) ) {
		emifree_register_sitemap_route();
	}
	if ( function_exists( 'emifree_register_llms_route' ) ) {
		emifree_register_llms_route();
	}
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
	delete_transient( 'emifree_section_routes_flushed_v6' );
	delete_transient( 'emifree_section_routes_flushed_v7' );
	set_transient( 'emifree_section_routes_flushed_v8', 1, DAY_IN_SECONDS );
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
 * Legacy URL redirect — old WPML permalinks and the old site's
 * flat slugs → the new site's /en/ or /de/ landing page (with
 * the matching in-page anchor where one exists).
 *
 * The old WordPress site used two URL patterns:
 *
 *   1. /language/<code>/<slug>/  — the WPML permalink schema. The
 *      new theme doesn't register that pattern, so every /language/...
 *      URL the old site indexed now 404s. Examples: /language/de/
 *      startseite/, /language/pl/pobierz/, /language/cz/domovska-
 *      stranka/, /language/en/product/flexible-spiral-hose/.
 *
 *   2. Bare English/German slugs — /products/, /contact,
 *      /applications/, /careers/, /karriere/, /download_en/,
 *      /download/kat_emi_de.pdf, /mechanical-oil-mist-collector/,
 *      /electrostatic-oil-mist-collector/, /impressum/, etc.
 *
 * The new site unifies everything onto a single landing page per
 * language (/en/ and /de/), with the section previously rendered at
 * its own URL now appearing as a #section on the landing page. A
 * 301 from the old URL to the new one transfers PageRank and is
 * what Google Search Console needs to consolidate the index.
 *
 * Why PHP and not .htaccess? The redirect map lives in the theme
 * repo so it ships with every environment (local, staging,
 * production) and so a non-engineer can edit it without touching
 * server config. Priority 1 fires before the home-redirect at
 * priority 5 so /language/de/ hits the legacy map before the
 * homepage dispatcher tries to handle it.
 *
 * Why not just send everything to /en/? The old URLs were seen by
 * Google per-language — sending /language/de/startseite/ to /en/
 * would drop the visitor into the wrong language. We split on the
 * detected source language and send each visitor to the matching
 * landing page (DE old URL → /de/ landing page, EN old URL → /en/
 * landing page). The exception is /language/pl/... and /language/cz/
 * and /language/sk/ — those languages are no longer shipped, so we
 * route them to /en/ (the closest fallback we'll ever offer).
 *
 * The map is a flat PHP array of exact-path => redirect-path. We
 * strip the home subpath before lookup so the same map works on a
 * root install and a /wordpress subpath install. We match on
 * the request path WITHOUT query string so search-engine referrers
 * ?utm_source=... still hit the same redirect.
 */
function emifree_legacy_redirect_map() {
	return array(
		// --- Old WPML /language/<code>/<slug>/ patterns ---
		// German WPML home + section landing slugs.
		'/language/de/'              => '/de/',
		'/language/de/startseite/'   => '/de/',
		'/language/de/produkte/'     => '/de/#products',
		'/language/de/anwendungen/'  => '/de/#applications',
		'/language/de/wissen/'       => '/de/#knowledge',
		'/language/de/technologie/'  => '/de/#technology',
		'/language/de/kontakt/'      => '/de/#contact',
		'/language/de/karriere/'     => '/de/#contact',
		// NOTE: /de/impressum/, /de/datenschutz/, /de/agb/ are already
		// the canonical URLs of the new site. They were removed from
		// the map because every entry where source == destination is a
		// self-redirect loop (ERR_TOO_MANY_REDIRECTS).
		// English WPML home + section landing slugs.
		'/language/en/'              => '/en/',
		'/language/en/startseite/'   => '/en/',
		'/language/en/products/'     => '/en/#products',
		'/language/en/applications/' => '/en/#applications',
		'/language/en/knowledge/'    => '/en/#knowledge',
		'/language/en/technology/'   => '/en/#technology',
		'/language/en/contact/'      => '/en/#contact',
		'/language/en/impressum/'    => '/impressum/',
		'/language/en/privacy/'      => '/privacy/',
		'/language/en/terms/'        => '/terms/',
		// Languages the new site doesn't ship. Routes to /en/ as the
		// closest fallback we'll surface; /de/ would mislead a
		// Polish/Slovak/Czech visitor into a German page.
		'/language/pl/'              => '/en/',
		'/language/pl/pobierz/'      => '/en/',
		'/language/sk/'              => '/en/',
		'/language/cz/'              => '/en/',
		'/language/cz/domovska-stranka/' => '/en/',
		// --- Old bare-slug English paths (WPML default language) ---
		'/products/'                 => '/en/#products',
		'/applications/'             => '/en/#applications',
		'/careers/'                  => '/en/#contact',
		'/contact'                   => '/en/#contact',
		'/contact/'                  => '/en/#contact',
		'/download_en/'              => '/en/',
		'/mechanical-oil-mist-collector/'   => '/en/#products',
		'/electrostatic-oil-mist-collector/' => '/en/#products',
		'/hello-world/'              => '/en/blog/',
		// --- Old bare-slug German paths ---
		'/karriere/'                 => '/de/#contact',
		'/startseite/'               => '/de/',
		// NOTE: /impressum/ is the canonical URL of the new site's
		// English legal page. Removed from the map because sending it
		// 301 to itself is an infinite redirect loop.
		'/download/'                 => '/de/',
		'/download/kat_emi_de.pdf'   => '/de/',
	);
}

function emifree_maybe_redirect_legacy_url() {
	if ( is_admin() ) {
		return;
	}
	$emifree_uri  = isset( $_SERVER['REQUEST_URI'] ) ? wp_unslash( $_SERVER['REQUEST_URI'] ) : '';
	$emifree_path = parse_url( $emifree_uri, PHP_URL_PATH );
	if ( ! is_string( $emifree_path ) || '' === $emifree_path ) {
		return;
	}
	// Strip the home subpath so the map entries (e.g. '/products/')
	// match the same URL on a root install and a /wordpress subpath
	// install. The subpath is the directory WP is installed under;
	// '/wordpress/products/' and '/products/' represent the same
	// legacy page in the two setups.
	$emifree_subpath = emifree_home_subpath();
	if ( '' !== $emifree_subpath && 0 === strpos( $emifree_path, $emifree_subpath ) ) {
		$emifree_path = substr( $emifree_path, strlen( $emifree_subpath ) );
	}
	// Normalize: ensure leading slash, strip trailing slash unless
	// the path is just '/'. The map keys are stored with leading
	// slash and trailing slash so every distinct legacy URL the
	// old site indexed has a row.
	$emifree_path = '/' . ltrim( $emifree_path, '/' );
	if ( '/' !== $emifree_path && '/' === substr( $emifree_path, -1 ) ) {
		// Already a trailing slash — keep.
	} elseif ( '/' !== $emifree_path && false === strpos( basename( $emifree_path ), '.' ) ) {
		// Bare path with no extension — add trailing slash so
		// '/products' and '/products/' share one map entry.
		$emifree_path .= '/';
	}
	$emifree_target = null;
	// 1. Exact-path lookup first (most specific match wins).
	$emifree_map = emifree_legacy_redirect_map();
	if ( isset( $emifree_map[ $emifree_path ] ) ) {
		$emifree_target = $emifree_map[ $emifree_path ];
	} elseif ( preg_match( '#^/language/([a-z]{2})/(?:.+)/?$#i', $emifree_path, $emifree_match ) ) {
		// 2. WPML catch-all: any /language/<code>/<anything>/ that the
		// map didn't catch. The old site had a product detail page
		// per language under /language/<code>/product/<slug>/ and
		// /language/<code>/<anything>/ for misc. landing pages. The
		// new site doesn't ship detail pages — every visitor lands
		// on the single landing page for their language. EN / DE are
		// the two languages the new site actually ships; everything
		// else falls back to /en/ as the closest language we'll
		// surface to a Polish / Slovak / Czech / Hungarian visitor.
		$emifree_code = strtolower( $emifree_match[1] );
		if ( 'de' === $emifree_code ) {
			$emifree_target = '/de/';
		} else {
			// 'en' and any other code land on /en/.
			$emifree_target = '/en/';
		}
	}
	if ( null === $emifree_target ) {
		return;
	}
	// Self-loop guard: never 301 a URL to itself. The map should not
	// contain entries where source == destination, but a defensive
	// check here prevents ERR_TOO_MANY_REDIRECTS if a future entry
	// drifts in. Compare the bare path (no fragment, no query) so a
	// map entry like '/products/' => '/products/' or
	// '/products/' => '/products/#section' is caught. The
	// fragment-stripping is what makes '/products/' => '/products/'
	// a loop even though comparison on the full target would diverge.
	$emifree_target_path = (string) parse_url( $emifree_target, PHP_URL_PATH );
	$emifree_request_path = $emifree_path;
	// Strip the home subpath from the request path so the comparison
	// matches e.g. '/wordpress/products/' against '/products/' when
	// the home subpath is '/wordpress'.
	if ( '' !== $emifree_subpath && 0 === strpos( $emifree_request_path, $emifree_subpath ) ) {
		$emifree_request_path = substr( $emifree_request_path, strlen( $emifree_subpath ) ) ?: '/';
	}
	$emifree_request_path = '/' . ltrim( $emifree_request_path, '/' );
	if ( $emifree_target_path === $emifree_request_path ) {
		return;
	}
	// Pass query string through so campaign tags survive the 301.
	$emifree_query = parse_url( $emifree_uri, PHP_URL_QUERY );
	if ( is_string( $emifree_query ) && '' !== $emifree_query ) {
		$emifree_target .= '?' . $emifree_query;
	}
	wp_safe_redirect( home_url( $emifree_target ), 301 );
	exit;
}
add_action( 'template_redirect', 'emifree_maybe_redirect_legacy_url', 1 );

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
	// plus the /index.php alternate URL WordPress may serve. On a
	// subpath install (home at /wordpress), also accept /wordpress
	// and /wordpress/index.php — those are THIS site's bare
	// homepage, not /wordpress/impressum/ or similar.
	$emifree_home       = emifree_home_subpath();
	$emifree_allowlist  = array( '', '/', '/index.php' );
	if ( '' !== $emifree_home ) {
		$emifree_allowlist[] = $emifree_home;
		$emifree_allowlist[] = $emifree_home . '/';
		$emifree_allowlist[] = $emifree_home . '/index.php';
	}
	if ( ! in_array( rtrim( (string) $emifree_path, '/' ), $emifree_allowlist, true ) ) {
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

	// Product-section prefill template — only emitted when the
	// products section is actually being loaded, so other section
	// scripts don't carry unused localize data. The {product}
	// placeholder is substituted with the human label from
	// data-emifree-inquiry-label at CTA-click time in products.js.
	//
	// Both EN + DE strings live here (rather than a translated
	// .mo file) because the template is short and tightly coupled
	// to this section — keeping it inline avoids a separate
	// gettext load and a translation context.
	if ( 'products' === $emifree_section_slug && function_exists( 'emifree_get_lang' ) ) {
		$emifree_prefill_en = "I would like a quote for {product}.\n\n";
		$emifree_prefill_de = "Ich möchte ein Angebot für {product} anfordern.\n\n";
		$emifree_prefill    = 'de' === emifree_get_lang() ? $emifree_prefill_de : $emifree_prefill_en;
		wp_localize_script(
			$emifree_section_handle,
			'emifreeProducts',
			array(
				'prefillPrefix' => $emifree_prefill,
			)
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
			'ajaxUrl'    => admin_url( 'admin-ajax.php' ) . '?action=send_contact',
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
 *
 * CORS note (added 2026-08-04): the previous version of this loader
 * hardcoded an account ID prefix that didn't match the widget IDs and
 * set `crossorigin='*'` on the dynamic script element.  Setting
 * `crossorigin='*'` switches the browser into CORS-mode for that
 * fetch, which requires `Access-Control-Allow-Origin` on the
 * response.  tawk.to's embed CDN does not always send that header on
 * widget-level loads, so the request was blocked with:
 *   "Access to script at ... from origin '...emifree.com' has been
 *    blocked by CORS policy: No 'Access-Control-Allow-Origin' header
 *    is present on the requested resource."
 * That error dropped the Lighthouse Best Practices score from 100
 * to 96.  Removing `crossorigin='*'` makes the loader a normal
 * (non-CORS) script fetch — the response is allowed regardless of
 * ACAO and the widget loads as designed.  The official Tawk.to
 * install snippet does NOT set a crossorigin attribute either.
 *
 * Widget loader URL format: `https://embed.tawk.to/{ACCOUNT}/{WIDGET}/default`.
 * The trailing `/default` is required by Tawk's CDN — without it the
 * script is served as an HTML error page.  Both the account ID and
 * the widget ID are now derived from the configured property ID
 * string (which historically was already an "<account>/<widget>" pair
 * in the Tawk dashboard URL — see the EMIFREE_TAWK_* defaults below).
 *
 * Per the original implementation, two property IDs are configured
 * (one per language) so each language routes to its own Tawk inbox
 * without language fallback.  Both IDs default to the live production
 * widgets as of 2026-07-20 — the EN widget `1jsu0245o` and the DE
 * widget `1ju1qnllp` from account `1jogl5hfo`.
 */
function emifree_enqueue_tawk_widget() {
	if ( is_admin() ) {
		return;
	}

	// Two Tawk dashboards — one per language, configured via wp-config
	// so staging + production stay in sync via code, not via UI. Override
	// in wp-config.php; defaults match the live production widgets as of
	// 2026-07-20.
	$emifree_tawk_property_id_en = defined( 'EMIFREE_TAWK_PROPERTY_ID_EN' ) && EMIFREE_TAWK_PROPERTY_ID_EN
		? EMIFREE_TAWK_PROPERTY_ID_EN
		: '1jogl5hfo/1jsu0245o';
	$emifree_tawk_property_id_de = defined( 'EMIFREE_TAWK_PROPERTY_ID_DE' ) && EMIFREE_TAWK_PROPERTY_ID_DE
		? EMIFREE_TAWK_PROPERTY_ID_DE
		: '1jogl5hfo/1ju1qnllp';

	// emifree_get_lang() is path-aware (so a /de/visit with no cookie
	// still serves the DE widget). That function lives further up in this
	// file.
	$emifree_lang          = function_exists( 'emifree_get_lang' ) ? emifree_get_lang() : 'en';
	$emifree_tawk_property_id = ( 'de' === $emifree_lang )
		? $emifree_tawk_property_id_de
		: $emifree_tawk_property_id_en;

	// Tawk widget ID strings are full "<account>/<widget>" pairs
	// (e.g. "1jogl5hfo/1jsu0245o"). The widget loader URL needs both
	// segments separated by a slash. Split here so the loader below
	// stays a single source of truth and we don't repeat the
	// hardcoded "default" path suffix.
	$emifree_tawk_id_parts     = array_pad( array_map( 'trim', explode( '/', (string) $emifree_tawk_property_id ) ), 2, '' );
	$emifree_tawk_account_part = $emifree_tawk_id_parts[0];
	$emifree_tawk_widget_part  = $emifree_tawk_id_parts[1];
	if ( '' === $emifree_tawk_account_part || '' === $emifree_tawk_widget_part ) {
		// Misconfigured property ID (missing the "/" separator) — skip the
		// widget load entirely to avoid surfacing a broken Tawk error in
		// the browser console (the previous bug silently produced a CORS
		// error every page load).
		return;
	}

	add_action(
		'wp_footer',
		static function () use ( $emifree_tawk_account_part, $emifree_tawk_widget_part ) {
			?>
			<!--Start of Tawk.to Script-->
			<script type="text/javascript">
			var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();
			(function(){
			var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
			s1.async=true;
			s1.src='https://embed.tawk.to/<?php echo esc_js( $emifree_tawk_account_part ); ?>/<?php echo esc_js( $emifree_tawk_widget_part ); ?>/default';
			s1.charset='UTF-8';
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
 * Run the three Tier 1 antispam checks against the current $_POST +
 * $_SERVER and return either true (pass) or a WP_Error.
 *
 * Why a pure function instead of inlined checks in the AJAX handler?
 *
 *   - Lets the browser-driven tests/ tests/antispam-test.php harness
 *     exercise each check in isolation, without going through
 *     admin-ajax.php or paying the side-effects of wp_mail().
 *
 *   - Allows future checks (reCAPTCHA v3, hCaptcha, Tier 2 IP block-list)
 *     to be added as additional `is_wp_error()` branches without
 *     touching the AJAX handler.
 *
 * The checks, in cheap-to-expensive order:
 *
 *   1. Honeypot field ('website_url'). Real humans never fill it
 *      because it's positioned off-screen via the template's inline
 *      CSS (position: absolute; left: -9999px) and given
 *      tabindex="-1" + aria-hidden="true". Volume bots reflexively
 *      fill every visible field; this one rejects them.
 *
 *   2. Submission timing. The 'emifree_ts' hidden field holds
 *      seconds-since-epoch at page-load time (set by contact.js on
 *      DOMContentLoaded). Reject submissions where:
 *        (now - ts) < min   → instant-fire bots (nonce scraped, then
 *                            immediately POSTed)
 *        (now - ts) > max   → stale-form-replay attacks (attacker
 *                            fetches a nonce once and tries to reuse
 *                            it days later)
 *      Defaults are configurable via wp-config constants:
 *        EMIFREE_CONTACT_MIN_SECONDS (default 3)
 *        EMIFREE_CONTACT_MAX_SECONDS (default 3600 = 1 hour)
 *
 *   3. Per-IP rate limit. Counter stored in a WP transient keyed by
 *      the SHA-256 of the request IP. Caps submissions per IP per
 *      EMIFREE_CONTACT_RATE_WINDOW (default 1 hour) at
 *      EMIFREE_CONTACT_RATE_MAX (default 3). Kills scripted spam
 *      bursts on the 4th attempt.
 *
 * All three checks return the same generic user-facing error message
 * so an attacker can't distinguish which check failed (otherwise they'd
 * tune their bot to defeat whichever check I add next). The internal
 * WP_Error CODE (honeypot, ts_missing, ts_out_of_range, rate_limited)
 * is preserved for diagnostics / tests and is NOT shown to the user.
 *
 * @return true|WP_Error
 */
function emifree_check_contact_antispam() {
	$emifree_honeypot = isset( $_POST['website_url'] )
		? trim( (string) wp_unslash( $_POST['website_url'] ) )
		: '';
	if ( '' !== $emifree_honeypot ) {
		return new WP_Error(
			'honeypot',
			__( 'Submission could not be processed. Please try again.', 'emifree-theme' ),
			array( 'status' => 400 )
		);
	}

	$emifree_min_seconds = defined( 'EMIFREE_CONTACT_MIN_SECONDS' ) ? (int) EMIFREE_CONTACT_MIN_SECONDS : 3;
	$emifree_max_seconds = defined( 'EMIFREE_CONTACT_MAX_SECONDS' ) ? (int) EMIFREE_CONTACT_MAX_SECONDS : 3600;
	$emifree_ts_raw      = isset( $_POST['emifree_ts'] ) ? (string) wp_unslash( $_POST['emifree_ts'] ) : '';
	$emifree_ts          = ctype_digit( $emifree_ts_raw ) ? (int) $emifree_ts_raw : 0;
	if ( ! $emifree_ts ) {
		return new WP_Error(
			'ts_missing',
			__( 'Submission could not be processed. Please try again.', 'emifree-theme' ),
			array( 'status' => 400 )
		);
	}
	$emifree_elapsed = time() - $emifree_ts;
	if ( $emifree_elapsed < $emifree_min_seconds || $emifree_elapsed > $emifree_max_seconds ) {
		return new WP_Error(
			'ts_out_of_range',
			__( 'Submission could not be processed. Please try again.', 'emifree-theme' ),
			array( 'status' => 400 )
		);
	}

	$emifree_ip_raw    = isset( $_SERVER['REMOTE_ADDR'] ) ? (string) wp_unslash( $_SERVER['REMOTE_ADDR'] ) : '';
	$emifree_ip_clean  = trim( $emifree_ip_raw );
	if ( '' !== $emifree_ip_clean ) {
		$emifree_rate_max    = defined( 'EMIFREE_CONTACT_RATE_MAX' )    ? (int) EMIFREE_CONTACT_RATE_MAX    : 3;
		$emifree_rate_window = defined( 'EMIFREE_CONTACT_RATE_WINDOW' ) ? (int) EMIFREE_CONTACT_RATE_WINDOW : HOUR_IN_SECONDS;
		$emifree_rate_key    = 'emifree_contact_ip_' . hash( 'sha256', $emifree_ip_clean );
		$emifree_rate_count  = (int) get_transient( $emifree_rate_key );
		if ( $emifree_rate_count >= $emifree_rate_max ) {
			return new WP_Error(
				'rate_limited',
				__( 'Too many submissions from your network. Please try again later.', 'emifree-theme' ),
				array( 'status' => 429 )
			);
		}
		// Increment AFTER passing the check so the Nth submission itself counts.
		// (1st -> count becomes 1, 2nd -> 2, 3rd -> 3, 4th arrives and sees 3 → 429.)
		set_transient( $emifree_rate_key, $emifree_rate_count + 1, $emifree_rate_window );
	}

	return true;
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

	$emifree_antispam = emifree_check_contact_antispam();
	if ( is_wp_error( $emifree_antispam ) ) {
		$emifree_code = (int) $emifree_antispam->get_error_data( 'status' );
		if ( $emifree_code < 100 ) {
			$emifree_code = 400;
		}
		wp_send_json_error(
			array( 'message' => $emifree_antispam->get_error_message() ),
			$emifree_code
		);
	}

	$emifree_name    = isset( $_POST['name'] )    ? sanitize_text_field( wp_unslash( $_POST['name'] ) )          : '';
	$emifree_email   = isset( $_POST['email'] )   ? sanitize_email( wp_unslash( $_POST['email'] ) )               : '';
	$emifree_company = isset( $_POST['company'] ) ? sanitize_text_field( wp_unslash( $_POST['company'] ) )         : '';
	$emifree_message = isset( $_POST['message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['message'] ) )     : '';

	// Product-of-interest tag — populated by contact.js when the
	// visitor clicks a product-section "Request Quote" CTA. The slug
	// is whitelisted against the three known product keys; anything
	// else is dropped (defensive: a malicious client could send
	// anything). The label comes from the same data-emifree-inquiry-
	// label attribute the client read, sanitized + control-char-
	// stripped + length-capped before being embedded into the email
	// subject + body (where it ends up inside SMTP envelope).
	$emifree_product_slug  = isset( $_POST['product'] )       ? sanitize_text_field( wp_unslash( $_POST['product'] ) )       : '';
	$emifree_product_label = isset( $_POST['product_label'] ) ? sanitize_text_field( wp_unslash( $_POST['product_label'] ) ) : '';
	$emifree_allowed_products = array( 'mechanical', 'electrostatic', 'dust' );
	if ( ! in_array( $emifree_product_slug, $emifree_allowed_products, true ) ) {
		$emifree_product_slug = '';
	}
	// Strip control characters before embedding in email headers.
	$emifree_product_label = preg_replace( '/[\x00-\x1F\x7F]/', '', $emifree_product_label );
	if ( strlen( $emifree_product_label ) > 60 ) {
		$emifree_product_label = substr( $emifree_product_label, 0, 60 );
	}
	$emifree_has_product = '' !== $emifree_product_slug && '' !== $emifree_product_label;

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
	// Subject prefix is the site name; the optional [Product] tag is
	// inserted between the prefix and the rest when the visitor
	// arrived via a product-section CTA. Inbox-filterable as
	// "[Emifree] [Mechanical Filtration]" so triage is a one-click
	// mailbox search.
	$emifree_subject   = sprintf(
		'[%s]%s New contact form submission from %s',
		wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES ),
		$emifree_has_product ? ' [' . $emifree_product_label . ']' : '',
		$emifree_name
	);

	// Body: when the product tag is set, prepend a "Product of
	// interest: X" line so the recipient sees the product name even
	// if their mail client collapses/truncates the subject line.
	$emifree_body  = '';
	if ( $emifree_has_product ) {
		$emifree_body .= sprintf( "Product of interest: %s\n", $emifree_product_label );
	}
	$emifree_body .= sprintf(
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

	// Capture PHPMailer exceptions so we can log the real SMTP failure
	// (auth, TLS handshake, RCPT TO rejection, etc.) instead of just
	// knowing wp_mail() returned false. phpmailer_init fires BEFORE
	// wp_mail() sends, so we attach the exception handler there.
	$emifree_phpmailer_error = null;
	add_action( 'phpmailer_init', function ( $emifree_phpmailer ) use ( &$emifree_phpmailer_error ) {
		$emifree_phpmailer_error = null;
		$emifree_phpmailer->SMTPDebug   = 2; // client + server transcript
		$emifree_phpmailer->Debugoutput = function ( $emifree_str, $emifree_level ) use ( &$emifree_phpmailer_error ) {
			// Collect the full SMTP transcript; final line usually
			// contains the rejection reason (535 auth, 553 envelope,
			// certificate errors, etc.).
			$emifree_phpmailer_error .= trim( (string) $emifree_str ) . "\n";
			error_log( '[emifree-contact-smtp] ' . trim( (string) $emifree_str ) );
		};
	}, 1 );

	$emifree_sent = wp_mail( $emifree_recipient, $emifree_subject, $emifree_body, $emifree_headers );

	if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
		// Trace every submission in debug mode so admins can confirm the
		// handler ran even when the SMTP transport silently drops mail.
		// (Hospitals/clinics/test installs often have no MTA at all; the
		// log is the only durable record.)
		error_log( sprintf(
			'[emifree-contact] attempt: to=%s subject="%s" sent=%s',
			$emifree_recipient,
			$emifree_subject,
			$emifree_sent ? 'true' : 'false'
		) );
	}

	if ( ! $emifree_sent ) {
		// Don't leak server config to the form. Log internally; tell
		// the user to email us directly (the recipient address is shown
		// in the contact-info cards just above the form).
		//
		// Always log the full submission body on failure — even when
		// WP_DEBUG is off. The form's user-facing error banner says
		// "we couldn't send your message automatically, please email
		// us directly"; this log line gives the admin the actual
		// message so they can recover it. PII exposure is acceptable
		// because only the site admin sees the PHP error log, and the
		// alternative is losing genuine customer enquiries.
		//
		// If the phpmailer_init hook above captured an SMTP transcript
		// (auth failure, TLS handshake, RCPT rejection), surface it
		// here so the admin can see the actual server response, not
		// just "wp_mail() returned false".
		if ( null !== $emifree_phpmailer_error && '' !== $emifree_phpmailer_error ) {
			error_log( '[emifree-contact] SMTP transcript at failure: ' . $emifree_phpmailer_error );
		}
		error_log( sprintf(
			"[emifree-contact] wp_mail() failed — submission to %s discarded from SMTP. Body follows:\n%s",
			$emifree_recipient,
			$emifree_body
		) );
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