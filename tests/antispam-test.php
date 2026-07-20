<?php
/**
 * Emifree Tier-1 antispam — self-contained browser-runnable test.
 *
 * Runs the same checks that emifree_check_contact_antispam() performs
 * for the live contact form, but in isolation, without sending mail,
 * without needing the AJAX round-trip, without needing the user to
 * type a message. The harness boots WP itself (via wp-load.php) and
 * walks through:
 *
 *   Case 1  Happy path           — clean POST + valid timestamp + empty
 *                                  honeypot + fresh IP → expect true.
 *   Case 2  Honeypot filled      — POST website_url='...' → expect
 *                                  WP_Error code 'honeypot'.
 *   Case 3  Instant-fire         — timestamp == now → expect
 *                                  WP_Error code 'ts_out_of_range'.
 *   Case 4  Stale form           — timestamp == now − 7200 → expect
 *                                  WP_Error code 'ts_out_of_range'.
 *   Case 5  Missing timestamp    — no emifree_ts at all → expect
 *                                  WP_Error code 'ts_missing'.
 *   Case 6  Rate limit           — 3 distinct IPs pass; 4th submission
 *                                  on a shared IP rejected with code
 *                                  'rate_limited' (status 429).
 *
 * How to run (in the browser, after starting the local WP site):
 *
 *   http://localhost:10004/wp-content/themes/emifree-theme/tests/antispam-test.php
 *   http://your-local-domain.example/wp-content/themes/emifree-theme/tests/antispam-test.php
 *
 * The exact host:port depends on your LocalWP site config — find it
 * in LocalWP's "Open Site" button.
 *
 * The file refuses to run unless EMIFREE_ALLOW_ANTISPAM_TEST is defined
 * and truthy in wp-config.php — this is the safety gate so a misconfigured
 * production deploy doesn't expose this dashboard. Add during local dev:
 *
 *   define( 'EMIFREE_ALLOW_ANTISPAM_TEST', true );
 *
 * Each case prints PASS/FAIL with the exact error code (when rejected)
 * or with success=true + the count of transients incremented. Output
 * is plain <pre> HTML so it works without CSS / a logged-in session.
 */

if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __DIR__, 4 ) . '/' );
}

require_once ABSPATH . 'wp-load.php';

header( 'Content-Type: text/html; charset=utf-8' );

if ( ! defined( 'EMIFREE_ALLOW_ANTISPAM_TEST' ) || ! EMIFREE_ALLOW_ANTISPAM_TEST ) {
	status_header( 403 );
	echo '<h1>403 — antispam test disabled</h1>';
	echo '<p>Define <code>EMIFREE_ALLOW_ANTISPAM_TEST</code> as truthy in wp-config.php to enable.</p>';
	exit;
}

// Pin predictable tunables for the duration of the run.
if ( ! defined( 'EMIFREE_CONTACT_MIN_SECONDS' ) )  define( 'EMIFREE_CONTACT_MIN_SECONDS', 3 );
if ( ! defined( 'EMIFREE_CONTACT_MAX_SECONDS' ) )  define( 'EMIFREE_CONTACT_MAX_SECONDS', 3600 );
if ( ! defined( 'EMIFREE_CONTACT_RATE_MAX' ) )     define( 'EMIFREE_CONTACT_RATE_MAX', 3 );
if ( ! defined( 'EMIFREE_CONTACT_RATE_WINDOW' ) )  define( 'EMIFREE_CONTACT_RATE_WINDOW', HOUR_IN_SECONDS );

echo "<!doctype html><meta charset='utf-8'><title>Emifree antispam test</title>\n";
echo "<style>body{font-family:ui-monospace,monospace;padding:20px;background:#0f172a;color:#e2e8f0}pre{background:#020617;padding:12px;border-radius:6px}table{border-collapse:collapse;margin-top:16px}td,th{padding:6px 12px;border:1px solid #334155;text-align:left}.ok{color:#4ade80}.bad{color:#f87171;font-weight:bold}h1{color:#f8fafc}</style>\n";
echo "<h1>Emifree Tier-1 antispam test</h1>\n";

$emifree_failures = 0;
$emifree_now       = time();

/**
 * Run one case. Saves and restores $_POST + $_SERVER around the call.
 *
 * When the caller passes more than one case on the same IP — e.g. the
 * rate-limit sub-cases 6d/6e/6f/6g all pretending to be IP D — set
 * $emifree_reset_slot to false on every call AFTER the first so the
 * rate-limit transient is allowed to accumulate. Default true wipes
 * the slot so unrelated cases stay isolated.
 *
 * @return array{ok:bool, code:?string, status:?int, detail:string}
 */
function emifree_run_case( $emifree_label, $emifree_post, $emifree_ip, $emifree_reset_slot = true ) {
	$emifree_post_save       = $_POST;
	$emifree_rem_ip_save     = $_SERVER['REMOTE_ADDR'];
	$emifree_key             = 'emifree_contact_ip_' . hash( 'sha256', $emifree_ip );
	if ( $emifree_reset_slot ) {
		delete_transient( $emifree_key );
	}

	$_POST                = $emifree_post;
	$_SERVER['REMOTE_ADDR'] = $emifree_ip;

	$emifree_result = emifree_check_contact_antispam();

	$_POST                = $emifree_post_save;
	$_SERVER['REMOTE_ADDR'] = $emifree_rem_ip_save;

	if ( true === $emifree_result ) {
		$emifree_count = (int) get_transient( $emifree_key );
		return array(
			'ok'      => true,
			'code'    => null,
			'status'  => null,
			'detail'  => 'passed (rate-limit transient count for this IP = ' . $emifree_count . ')',
		);
	}
	if ( is_wp_error( $emifree_result ) ) {
		$emifree_data = $emifree_result->get_error_data();
		$emifree_st   = is_array( $emifree_data ) && isset( $emifree_data['status'] )
			? (int) $emifree_data['status'] : null;
		return array(
			'ok'      => false,
			'code'    => $emifree_result->get_error_code(),
			'status'  => $emifree_st,
			'detail'  => $emifree_result->get_error_message(),
		);
	}
	return array(
		'ok'      => false,
		'code'    => 'unknown',
		'status'  => null,
		'detail'  => 'function returned non-true non-WP_Error: ' . var_export( $emifree_result, true ),
	);
}

function emifree_check( $emifree_label, $emifree_actual, $emifree_expected_passed, $emifree_expected_code = null, $emifree_expected_status = null ) {
	global $emifree_failures;

	$emifree_ok = ( $emifree_actual['ok'] === $emifree_expected_passed )
		&& ( ! $emifree_expected_code || $emifree_actual['code'] === $emifree_expected_code )
		&& ( ! $emifree_expected_status || $emifree_actual['status'] === $emifree_expected_status );

	$emifree_status_label = $emifree_ok
		? '<span class="ok">PASS</span>'
		: '<span class="bad">FAIL</span>';

	echo '<tr>';
	echo '<td>' . $emifree_status_label . '</td>';
	echo '<td>' . esc_html( $emifree_label ) . '</td>';
	echo '<td>' . esc_html( (string) ( $emifree_expected_passed ? 'pass' : 'fail-' . (string) $emifree_expected_code ) ) . '</td>';
	echo '<td>' . esc_html( (string) ( $emifree_actual['ok'] ? 'pass' : 'fail-' . (string) $emifree_actual['code'] ) ) . '</td>';
	echo '<td>' . esc_html( (string) ( $emifree_actual['status'] ?? 'n/a' ) ) . '</td>';
	echo '<td>' . esc_html( $emifree_actual['detail'] ) . '</td>';
	echo '</tr>' . "\n";

	if ( ! $emifree_ok ) {
		$emifree_failures++;
	}
}

echo "<table>\n";
echo '<tr><th></th><th>Case</th><th>Expected</th><th>Actual</th><th>Status</th><th>Detail</th></tr>' . "\n";

// Use distinct IPs per case so the rate-limit transient doesn't bleed.

$emifree_ip_happy  = '203.0.113.10';
$emifree_ip_honey  = '203.0.113.20';
$emifree_ip_insta  = '203.0.113.30';
$emifree_ip_stale  = '203.0.113.40';
$emifree_ip_miss   = '203.0.113.50';
$emifree_ip_a      = '203.0.113.60';
$emifree_ip_b      = '203.0.113.61';
$emifree_ip_c      = '203.0.113.62';
$emifree_ip_d      = '203.0.113.63';

$emifree_valid_ts  = $emifree_now - 30; // 30 s ago.

$emifree_base = array(
	'name'        => 'Test User',
	'email'       => 'test@example.com',
	'company'     => 'Acme Inc',
	'message'     => 'message body long enough',
	'website_url' => '',
	'emifree_ts'  => (string) $emifree_valid_ts,
);

// Case 1 — happy path.
emifree_check(
	'1. Happy path',
	emifree_run_case( 'happy', $emifree_base, $emifree_ip_happy ),
	true
);

// Case 2 — honeypot filled.
$emifree_honey                = $emifree_base;
$emifree_honey['website_url'] = 'http://spam.example';
emifree_check(
	'2. Honeypot filled',
	emifree_run_case( 'honeypot', $emifree_honey, $emifree_ip_honey ),
	false,
	'honeypot',
	400
);

// Case 3 — instant-fire (elapsed = 0).
$emifree_insta               = $emifree_base;
$emifree_insta['emifree_ts'] = (string) $emifree_now;
emifree_check(
	'3. Instant-fire bot (elapsed=0)',
	emifree_run_case( 'instant', $emifree_insta, $emifree_ip_insta ),
	false,
	'ts_out_of_range',
	400
);

// Case 4 — stale form replay (elapsed = 7200).
$emifree_stale               = $emifree_base;
$emifree_stale['emifree_ts'] = (string) ( $emifree_now - 7200 );
emifree_check(
	'4. Stale form replay (elapsed=7200)',
	emifree_run_case( 'stale', $emifree_stale, $emifree_ip_stale ),
	false,
	'ts_out_of_range',
	400
);

// Case 5 — timestamp missing.
$emifree_miss = $emifree_base;
unset( $emifree_miss['emifree_ts'] );
emifree_check(
	'5. Timestamp field missing',
	emifree_run_case( 'missing', $emifree_miss, $emifree_ip_miss ),
	false,
	'ts_missing',
	400
);

// Case 6 — rate limit. 3 distinct IPs each pass; 4th attempt on one
// IP sees the transient count and gets rate_limited.
emifree_check(
	'6a. Rate-limit pass on IP A (count → 1)',
	emifree_run_case( 'rate_a_1', $emifree_base, $emifree_ip_a, true ),
	true
);
emifree_check(
	'6b. Rate-limit pass on IP B (count → 1, fresh IP)',
	emifree_run_case( 'rate_b', $emifree_base, $emifree_ip_b, true ),
	true
);
emifree_check(
	'6c. Rate-limit pass on IP C (count → 1, fresh IP)',
	emifree_run_case( 'rate_c', $emifree_base, $emifree_ip_c, true ),
	true
);
// 4 hits on the same IP D should be: 1st passes (count → 1),
// 2nd passes (count → 2), 3rd passes (count → 3), 4th rejected (count=3 → 429).
// First call on IP D resets the slot; later calls keep the slot so the
// rate limiter can see the accumulated count.
emifree_check(
	'6d. Rate-limit pass #1 on IP D (count → 1)',
	emifree_run_case( 'rate_d_1', $emifree_base, $emifree_ip_d, true ),
	true
);
emifree_check(
	'6e. Rate-limit pass #2 on IP D (count → 2)',
	emifree_run_case( 'rate_d_2', $emifree_base, $emifree_ip_d, false ),
	true
);
emifree_check(
	'6f. Rate-limit pass #3 on IP D (count → 3)',
	emifree_run_case( 'rate_d_3', $emifree_base, $emifree_ip_d, false ),
	true
);
emifree_check(
	'6g. Rate-limit reject #4 on IP D (429 rate_limited)',
	emifree_run_case( 'rate_d_4', $emifree_base, $emifree_ip_d, false ),
	false,
	'rate_limited',
	429
);

echo "</table>\n";

echo '<h2 style="margin-top:24px">' . ( 0 === $emifree_failures
	? '<span class="ok">All checks passed.</span>'
	: '<span class="bad">' . $emifree_failures . ' check(s) failed.</span>'
) . '</h2>';

echo '<p>Delete the <code>EMIFREE_ALLOW_ANTISPAM_TEST</code> constant (or set to false) on production deploys.</p>';
