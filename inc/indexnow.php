<?php
/**
 * Emifree Theme — IndexNow submission on blog_post publish/update.
 *
 * IndexNow is a unified URL-submission API used by Bing, Yandex, and
 * a handful of smaller search engines. When a URL is submitted, the
 * participating engines prioritize that URL for recrawl. The win is
 * that new blog posts are visible in Bing search results within
 * minutes instead of days.
 *
 * Configuration (wp-config.php):
 *   - EMIFREE_INDEXNOW_KEY  : the API key generated at indexnow.org.
 *                             Empty locally → no submission.
 *   - EMIFREE_INDEXNOW_HOST : the verified hostname (e.g. 'emifree.com').
 *                             Empty locally → no submission.
 *   - EMIFREE_INDEXNOW_ALLOW_CLI : opt-in flag to enable submissions
 *                                  during WP-CLI runs.
 *
 * The key verification file ({key}.txt containing just the key value)
 * must be uploaded to https://{EMIFREE_INDEXNOW_HOST}/{key}.txt on
 * production so the IndexNow API can verify host ownership. This is a
 * deploy-time step — locally the file is at
 * C:\Users\vpedr\Local Sites\landingwptest\app\public\{key}.txt but
 * the helper no-ops because the constants default to empty.
 *
 * Submission strategy: when a blog_post is saved, submit ALL static
 * URLs (homepages, legal, blog indexes) PLUS the just-saved post's
 * EN + DE URLs. ~12 URLs per submission — cheap, keeps the IndexNow
 * cache consistent with the sitemap, and doesn't require tracking
 * every post individually.
 *
 * Fire-and-forget: blocking => false so the save_post round-trip
 * doesn't wait for the API response. Failures (network errors,
 * rejected payloads) are logged when WP_DEBUG is on; silent in
 * production per IndexNow's idempotent-design intent.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Submit a list of URLs to IndexNow.
 *
 * No-ops when EMIFREE_INDEXNOW_KEY or EMIFREE_INDEXNOW_HOST is empty
 * (safe local default). Returns true on a non-WP_Error response
 * (which, with blocking=false, means the request was queued —
 * actual success/failure isn't visible to PHP).
 *
 * @param array $emifree_urls Full URLs to submit. Will be deduped + filtered.
 * @return bool True if the POST was successfully queued, false otherwise.
 */
function emifree_indexnow_submit_urls( array $emifree_urls ) {
	$emifree_key  = defined( 'EMIFREE_INDEXNOW_KEY' )  ? trim( (string) EMIFREE_INDEXNOW_KEY )  : '';
	$emifree_host = defined( 'EMIFREE_INDEXNOW_HOST' ) ? trim( (string) EMIFREE_INDEXNOW_HOST ) : '';

	if ( '' === $emifree_key || '' === $emifree_host ) {
		return false;
	}
	if ( empty( $emifree_urls ) ) {
		return false;
	}

	$emifree_clean_urls = array_values(
		array_unique(
			array_filter(
				array_map(
					static function ( $emifree_url ) {
						return esc_url_raw( (string) $emifree_url );
					},
					$emifree_urls
				)
			)
		)
	);

	if ( empty( $emifree_clean_urls ) ) {
		return false;
	}

	$emifree_payload = array(
		'host'        => $emifree_host,
		'key'         => $emifree_key,
		'keyLocation' => 'https://' . $emifree_host . '/' . $emifree_key . '.txt',
		'urlList'     => $emifree_clean_urls,
	);

	$emifree_response = wp_remote_post(
		'https://api.indexnow.org/indexnow',
		array(
			'timeout'     => 5,
			'blocking'    => false, // fire-and-forget — don't slow save_post.
			'headers'     => array( 'Content-Type' => 'application/json; charset=utf-8' ),
			'body'        => wp_json_encode( $emifree_payload ),
			'data_format' => 'body',
		)
	);

	if ( defined( 'WP_DEBUG' ) && WP_DEBUG && is_wp_error( $emifree_response ) ) {
		error_log( '[emifree-indexnow] submit failed: ' . $emifree_response->get_error_message() ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log — debug-only.
	}

	return ! is_wp_error( $emifree_response );
}

/**
 * Collect the static URL set (homepages, legal, blog indexes) that
 * IndexNow should always have on hand. Mirrors the static URL list in
 * inc/sitemap.php's emifree_collect_sitemap_static_urls() so the two
 * surfaces stay in sync. Kept separate from the sitemap helper so
 * the IndexNow dependency on inc/sitemap.php is one-directional.
 */
function emifree_collect_indexable_urls_for_indexnow() {
	return array(
		home_url( '/en/' ),
		home_url( '/de/' ),
		home_url( '/impressum/' ),
		home_url( '/privacy/' ),
		home_url( '/terms/' ),
		home_url( '/de/impressum/' ),
		home_url( '/de/datenschutz/' ),
		home_url( '/de/agb/' ),
		home_url( '/blog/' ),
		home_url( '/de/blog/' ),
	);
}

/**
 * Hook: fire an IndexNow submission on every blog_post save.
 *
 * Hooked at save_post_blog_post priority 20, after
 * emifree_save_blog_meta_box (priority 10) so post meta is final.
 *
 * Guards:
 *   - Not an autosave (DOING_AUTOSAVE constant)
 *   - Not a revision (wp_is_post_revision returns true)
 *   - Post type is blog_post (other post types aren't indexable here)
 *   - Post status is publish (don't ping drafts/private posts)
 *   - Skip WP-CLI unless EMIFREE_INDEXNOW_ALLOW_CLI is explicitly true
 */
function emifree_indexnow_on_blog_post_save( $emifree_post_id, $emifree_post ) {
	if ( ! $emifree_post || empty( $emifree_post->post_type ) ) {
		return;
	}
	if ( 'blog_post' !== $emifree_post->post_type ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( wp_is_post_revision( $emifree_post_id ) ) {
		return;
	}
	if ( 'publish' !== $emifree_post->post_status ) {
		return;
	}
	if ( defined( 'WP_CLI' ) && WP_CLI && ! ( defined( 'EMIFREE_INDEXNOW_ALLOW_CLI' ) && EMIFREE_INDEXNOW_ALLOW_CLI ) ) {
		return;
	}

	$emifree_urls = emifree_collect_indexable_urls_for_indexnow();

	// Always include the just-saved post's EN + DE URLs. Slug parity
	// is guaranteed by emifree_mirror_slug_to_sibling() in
	// inc/cpt-blog.php, so both language siblings are reachable at
	// the same slug.
	$emifree_post_slug = $emifree_post->post_name;
	if ( $emifree_post_slug ) {
		$emifree_urls[] = home_url( '/blog/' . $emifree_post_slug . '/' );
		$emifree_urls[] = home_url( '/de/blog/' . $emifree_post_slug . '/' );
	}

	emifree_indexnow_submit_urls( $emifree_urls );
}
add_action( 'save_post_blog_post', 'emifree_indexnow_on_blog_post_save', 20, 2 );
