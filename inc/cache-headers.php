<?php
/**
 * Emifree Theme — long Cache-Control for static assets.
 *
 * PageSpeed Insights on /en/ and /de/ reports an "efficient cache
 * dwell time" issue with an 8.8 MiB savings estimate — every static
 * asset the theme ships (videos, product photos, logos, SVG icons,
 * built CSS, per-section JS) is currently served with either a
 * 300-second `Cache-Control: max-age=300, public` (images/CSS from
 * LocalWP's nginx) or no `Cache-Control` at all (videos from LocalWP
 * nginx, which doesn't include video MIME types in its cache-rules
 * map). The hero carousel videos are 3.5 MiB + 2.5 MiB + 2.2 MiB =
 * 8.2 MiB of that — a repeat visitor is re-downloading all three
 * videos on every page load.
 *
 * This file sets per-extension Cache-Control values on the response
 * via the `wp_headers` filter. The browser (and any intermediate
 * proxy/CDN) holds the file for the listed TTL instead of re-asking
 * the server.
 *
 * TTL tiers (per Google's "Efficient cache dwell time" guidance):
 *
 *   - JS / CSS / WOFF2  → 1 year.  These files are versioned via the
 *     `?ver=` query string on every wp_enqueue_* call (the theme
 *     version constant EMIFREE_THEME_VERSION is bumped on every
 *     deploy), so changing the URL invalidates them automatically.
 *     A year is the standard recommendation; anything shorter is the
 *     same as no cache for repeat visitors.
 *
 *   - Images (webp/png/jpg/svg/gif/ico) → 30 days.  Logo, product
 *     photos, client logos.  Short enough that swapping a product
 *     image doesn't strand stale copies in user caches, long
 *     enough to clear the PageSpeed warning.
 *
 *   - Videos (mp4/webm) → 30 days.  Same rationale as images.
 *
 *   - Everything else → unchanged.  HTML responses, AJAX, admin
 *     pages: never touch their headers.
 *
 * Why this filter and not .htaccess?  LocalWP runs nginx, not
 * Apache, so .htaccess mod_expires rules have no effect there.  The
 * production server may be Apache, nginx, or LiteSpeed — depending
 * on the host.  Emitting Cache-Control from PHP via wp_headers is
 * the one surface that works identically on every host.  It also
 * sits BELOW the wp_loaded/wp action stack so a caching plugin (if
 * installed) can still rewrite the headers before they reach the
 * browser.
 *
 * Important: this REPLACES the Cache-Control array (not appends to
 * it).  A response can carry multiple Cache-Control headers; browsers
 * pick the most restrictive.  LocalWP's nginx already emits
 * `max-age=300, public` — appending `max-age=31536000` here would
 * not change the effective TTL because the 300 from nginx wins.
 * Removing the upstream `max-age=300` and writing only our own value
 * is what actually bumps the TTL to 1 year / 30 days.
 *
 * Loaded globally from functions.php so it applies on every request,
 * including the virtual routes (/robots.txt, /sitemap.xml, /llms.txt)
 * that don't go through a standard theme template.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Map a request URI's file extension to a Cache-Control max-age
 * value.  Anything we don't recognise is left untouched.
 *
 * @param string $emifree_uri The request URI path component.
 * @return int|null  TTL in seconds, or null to leave the response alone.
 */
function emifree_static_asset_ttl( $emifree_uri ) {
	if ( ! is_string( $emifree_uri ) || '' === $emifree_uri ) {
		return null;
	}

	// Strip query string before extension lookup so ?ver=1.0.1
	// doesn't break the .css / .js match.
	$emifree_path = strtolower( (string) parse_url( $emifree_uri, PHP_URL_PATH ) );
	if ( '' === $emifree_path ) {
		return null;
	}

	$emifree_ext = strtolower( pathinfo( $emifree_path, PATHINFO_EXTENSION ) );
	if ( '' === $emifree_ext ) {
		return null;
	}

	$emifree_one_year = YEAR_IN_SECONDS; // 31536000
	$emifree_30_days  = 30 * DAY_IN_SECONDS; // 2592000

	// JS / CSS / font files: 1 year.  All theme-shipped JS/CSS carry
	// a ?ver= query string on enqueue, so any change bumps the URL
	// and busts the cache automatically.
	$emifree_versioned_extensions = array(
		'js'  => $emifree_one_year,
		'css' => $emifree_one_year,
		'mjs' => $emifree_one_year,
		'map' => $emifree_one_year,
		'woff'  => $emifree_one_year,
		'woff2' => $emifree_one_year,
		'ttf'   => $emifree_one_year,
		'otf'   => $emifree_one_year,
		'eot'   => $emifree_one_year,
	);
	if ( isset( $emifree_versioned_extensions[ $emifree_ext ] ) ) {
		return $emifree_versioned_extensions[ $emifree_ext ];
	}

	// Images + videos: 30 days.  Long enough to satisfy PageSpeed's
	// "longer cache duration" check; short enough that re-deploying
	// a new logo or product photo is eventually consistent for
	// repeat visitors without a hard cache-bust.
	$emifree_media_extensions = array(
		'webp' => $emifree_30_days,
		'png'  => $emifree_30_days,
		'jpg'  => $emifree_30_days,
		'jpeg' => $emifree_30_days,
		'gif'  => $emifree_30_days,
		'svg'  => $emifree_30_days,
		'ico'  => $emifree_30_days,
		'avif' => $emifree_30_days,
		'mp4'  => $emifree_30_days,
		'webm' => $emifree_30_days,
		'mov'  => $emifree_30_days,
	);
	if ( isset( $emifree_media_extensions[ $emifree_ext ] ) ) {
		return $emifree_media_extensions[ $emifree_ext ];
	}

	return null;
}

/**
 * Set Cache-Control on static asset responses.
 *
 * `wp_headers` is the WP-canonical place to mutate response headers
 * before they're sent.  It runs after `template_redirect`, so the
 * virtual routes (/robots.txt, /sitemap.xml, /llms.txt, the legal
 * pages, etc.) all see this filter on their way out.
 *
 * For any request whose URI extension matches a known static asset
 * type, REPLACE the upstream Cache-Control with our own max-age +
 * public + immutable (for versioned files).  For HTML, PHP routes,
 * AJAX, etc. — leave the headers alone, this filter is a no-op.
 *
 * @param array $emifree_headers  Existing response headers (key => value).
 * @return array  Headers with Cache-Control replaced where applicable.
 */
function emifree_set_cache_headers( $emifree_headers ) {
	$emifree_uri  = isset( $_SERVER['REQUEST_URI'] ) ? (string) wp_unslash( $_SERVER['REQUEST_URI'] ) : '';
	$emifree_ttl  = emifree_static_asset_ttl( $emifree_uri );
	if ( null === $emifree_ttl ) {
		return $emifree_headers;
	}

	// `public` lets intermediate proxies (CDN, Cloudflare, the
	// browser's HTTP cache) share the response.  `immutable` is a
	// hint to the browser that the body will not change during its
	// freshness lifetime — no conditional revalidation — which
	// matches what versioned assets already guarantee.
	$emifree_headers['Cache-Control'] = sprintf(
		'public, max-age=%d, immutable',
		(int) $emifree_ttl
	);

	// Drop the redundant Expires header if present.  When both
	// Cache-Control and Expires are set, RFC 7234 says Cache-Control
	// wins — but leaving Expires in the response confuses some
	// proxies (LiteSpeed, older Cloudflare versions) into using the
	// Expires date instead.  Stays out of the way.
	unset( $emifree_headers['Expires'] );

	return $emifree_headers;
}
add_filter( 'wp_headers', 'emifree_set_cache_headers', 20 );

/**
 * Disable WP's own Last-Modified / ETag conditional revalidation
 * for static assets too.  WP emits `Cache-Control: no-cache,
 * must-revalidate` for many of its own routes — we override it
 * above, but `nocache_headers()` calls (used by robots/sitemap/llms
 * virtual routes) emit their own Cache-Control at a higher priority.
 *
 * The robots/sitemap/llms handlers use `nocache_headers()` so the
 * LLMs + crawlers always get fresh content; that path runs at
 * `template_redirect` priority 20 with explicit `header()` calls.
 * Our `wp_headers` filter at the same priority 20 runs first
 * (filters fire before the template_redirect handler that emits the
 * header() calls), so robots/sitemap/llms win the race for those
 * routes.  Static assets don't call nocache_headers(), so our
 * longer Cache-Control sticks.
 */