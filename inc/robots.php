<?php
/**
 * Emifree Theme — virtual /robots.txt.
 *
 * Three pieces, mirroring the existing emifree_register_legal_routes()
 * pattern (functions.php:164–254):
 *   1. Rewrite rule registered at 'top' priority so it wins against
 *      any plugin or future theme code that tries to claim the URL.
 *   2. A query_var so the rewrite target carries a flag we can detect
 *      in the template_redirect handler.
 *   3. A template_redirect handler at priority 20 (after the existing
 *      legal/blog/homepage dispatchers at priority 10) that prints the
 *      body and exits. The X-Robots-Tag: noindex header is defensive
 *      so even if a crawler ever tried to index the robots file, it
 *      would be told not to.
 *
 * The body explicitly Allow-s every major AI / LLM crawler active in
 * 2026. Without these blocks, crawlers like GPTBot and ClaudeBot may
 * default-Deny based on heuristics from other crawlers' behavior —
 * AI search engines (Google AI Overviews, Perplexity, ChatGPT search)
 * rely on these User-agents to ingest content for answers.
 *
 * The Sitemap: line is built at request time via home_url() so it
 * stays correct on subpath installs (the explore agent confirmed the
 * site is currently a root install, but the helper is subpath-safe).
 *
 * No wp-config constants are read here — the body is identical on
 * local + production. Comments are intentionally NOT emitted (some
 * crawlers mis-parse '#' comments despite the spec allowing them).
 *
 * Loaded globally from functions.php so the rewrite rule is registered
 * before any request can land.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Rewrite rule + query var. Registered on init; the URL surface is
 * /robots.txt only (no language variants — robots.txt is a single
 * file per spec). Pairs with emifree_serve_robots_txt() below.
 */
function emifree_register_robots_route() {
	add_rewrite_rule(
		'^robots\.txt$',
		'index.php?emifree_robots=1',
		'top'
	);
}
add_action( 'init', 'emifree_register_robots_route' );

function emifree_register_robots_query_var( $vars ) {
	$vars[] = 'emifree_robots';
	return $vars;
}
add_filter( 'query_vars', 'emifree_register_robots_query_var' );

/**
 * Serve the robots.txt body. Returns early on any request that
 * doesn't carry the emifree_robots query var so this handler is
 * safe to leave hooked at template_redirect for every page.
 */
function emifree_serve_robots_txt() {
	if ( ! get_query_var( 'emifree_robots' ) ) {
		return;
	}
	nocache_headers();
	header( 'Content-Type: text/plain; charset=utf-8' );
	header( 'X-Robots-Tag: noindex' );
	echo emifree_robots_txt_body(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped — static text body, no user input.
	exit;
}
add_action( 'template_redirect', 'emifree_serve_robots_txt', 20 );

/**
 * Build the robots.txt body.
 *
 * Structure:
 *   - "User-agent: *" with the standard Disallow list for the
 *     admin / plugins / wp-json / search / trackback / WP boilerplate
 *     file surface. /wp-json/oembed/ is explicitly Allowed so other
 *     sites can still embed posts (oEmbed is harmless public content).
 *   - One User-agent block per major AI / LLM crawler with full
 *     Allow: /. AI crawlers ignore wildcard Disallow directives from
 *     the * block in some implementations, so the per-bot Allow is
 *     what guarantees ingestion.
 *   - Sitemap: line built from home_url() so it stays correct on
 *     subpath installs.
 *
 * Returns the body as a string so the caller can echo it after the
 * headers are sent.
 */
function emifree_robots_txt_body() {
	$sitemap_url = home_url( '/sitemap.xml' );

	// Standard "noindex admin / plugins / API / thin content" list.
	// /wp-json/oembed/ stays open so third-party embeds keep working.
	$disallow_lines = array(
		'Disallow: /wp-admin/',
		'Disallow: /wp-content/plugins/',
		'Disallow: /wp-json/',
		'Disallow: /?s=',
		'Disallow: /trackback/',
		'Disallow: /comments/feed/',
		'Disallow: /readme.html',
		'Disallow: /license.txt',
		'Disallow: /wp-config-sample.php',
	);
	$allow_oembed = 'Allow: /wp-json/oembed/';

	// AI / LLM crawlers active in 2026. Order is roughly by adoption —
	// the big training-data ingesters first (GPTBot, ClaudeBot), then
	// agentic / on-demand fetchers, then specialty bots.
	$ai_crawlers = array(
		'GPTBot',
		'ChatGPT-User',
		'OAI-SearchBot',
		'ClaudeBot',
		'Claude-Web',
		'anthropic-ai',
		'PerplexityBot',
		'Perplexity-User',
		'Google-Extended',
		'Applebot-Extended',
		'Bytespider',
		'Meta-ExternalAgent',
		'Amazonbot',
		'cohere-ai',
		'DuckAssistBot',
		'Diffbot',
	);

	$emifree_lines   = array();
	$emifree_lines[] = 'User-agent: *';
	$emifree_lines   = array_merge( $emifree_lines, $disallow_lines );
	$emifree_lines[] = $allow_oembed;
	$emifree_lines[] = '';

	foreach ( $ai_crawlers as $emifree_bot ) {
		$emifree_lines[] = 'User-agent: ' . $emifree_bot;
		$emifree_lines[] = 'Allow: /';
		$emifree_lines[] = '';
	}

	$emifree_lines[] = 'Sitemap: ' . $sitemap_url;

	return implode( "\n", $emifree_lines ) . "\n";
}
