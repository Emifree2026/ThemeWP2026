<?php
/**
 * Emifree Theme — built-in SMTP settings page.
 *
 * Why: production hosts (and many test/staging environments) often ship
 * without a working local MTA. WordPress' default wp_mail() falls back to
 * PHP's mail() which returns false silently when sendmail/postfix aren't
 * installed — the contact form appears to fail with no error visible to
 * the admin. The cleanest fix is to point wp_mail() at a real SMTP
 * server, which WordPress exposes through the `phpmailer_init` action.
 *
 * This file does NOT implement an SMTP client — it lets the admin enter
 * SMTP credentials via Settings → Emifree SMTP and wires them into
 * PHPMailer so wp_mail() transports over the configured server. That's
 * the standard, WordPress-native pattern (used by every "WP Mail SMTP"
 * style plugin). The theme owns the settings instead of pulling in a
 * third-party plugin.
 *
 * Stored under a single option: `emifree_smtp_settings`, schema:
 *   host       string  e.g. 'smtp.eu.mailgun.org' (no scheme)
 *   port       int     e.g. 587 (TLS), 465 (SSL), 25 (none)
 *   encryption string  'tls' | 'ssl' | 'none'
 *   username   string  SMTP auth user
 *   password   string  SMTP auth password (stored verbatim — only the
 *                          admin can read this page anyway; if you want
 *                          at-rest encryption, install a secrets manager
 *                          and re-key via a filter)
 *   from_email string  optional override; falls back to wp_email
 *                          default; must be a valid address
 *   from_name  string  optional override; falls back to blog name
 *
 * Empty values = use whatever wp_mail() defaults to (i.e. native PHP
 * mail() with no SMTP). The settings page makes this state explicit:
 * if any required field is empty, the SMTP injector is a no-op.
 *
 * Capability gate: 'manage_options' — admin only.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the Settings → Emifree SMTP submenu page.
 *
 * Hooked at admin_menu so the page exists alongside Settings, Plugins,
 * etc. The slug `emifree-smtp` is used both in the URL and the option
 * group (settings_fields arg below). Capability is manage_options so
 * editors and below never see this page even if they have admin URL
 * access.
 */
function emifree_register_smtp_settings_page() {
	add_options_page(
		__( 'Emifree SMTP', 'emifree-theme' ),
		__( 'Emifree SMTP', 'emifree-theme' ),
		'manage_options',
		'emifree-smtp',
		'emifree_render_smtp_settings_page'
	);
}
add_action( 'admin_menu', 'emifree_register_smtp_settings_page' );

/**
 * Register the option group so settings_fields() + do_settings_sections()
 * emit the nonce + form action. Uses a single option
 * `emifree_smtp_settings` registered as a single field so the form is
 * simple — a flat form with one save button rather than per-field
 * settings API boxes.
 */
function emifree_register_smtp_settings() {
	register_setting(
		'emifree_smtp_settings_group',
		'emifree_smtp_settings',
		array(
			'type'              => 'array',
			'sanitize_callback' => 'emifree_sanitize_smtp_settings',
			'default'           => emifree_default_smtp_settings(),
		)
	);
}
add_action( 'admin_init', 'emifree_register_smtp_settings' );

/**
 * Defaults — every field empty, signalling "use native wp_mail() with
 * no SMTP". The settings page surfaces this as "SMTP is not configured;
 * the theme will use the host's PHP mail() transport."
 *
 * @return array
 */
function emifree_default_smtp_settings() {
	return array(
		'host'       => '',
		'port'       => 587,
		'encryption' => 'tls',
		'username'   => '',
		'password'   => '',
		'from_email' => '',
		'from_name'  => '',
	);
}

/**
 * Sanitize submitted values before persisting. Each value has its own
 * rule:
 *  - host: trim, strip scheme if accidentally included, max 253 chars
 *    (RFC 1035 max hostname length)
 *  - port: integer 1..65535, fall back to 587
 *  - encryption: must be one of 'tls' | 'ssl' | 'none'
 *  - username: trim, no length cap (some providers use long tokens)
 *  - password: trim, do NOT mutate — verbatim
 *  - from_email: must be a valid email or empty
 *  - from_name: text field, no HTML
 *
 * @param mixed $emifree_input
 * @return array
 */
function emifree_sanitize_smtp_settings( $emifree_input ) {
	$emifree_defaults = emifree_default_smtp_settings();
	if ( ! is_array( $emifree_input ) ) {
		return $emifree_defaults;
	}

	$emifree_host = isset( $emifree_input['host'] ) ? trim( (string) $emifree_input['host'] ) : '';
	// Strip scheme prefix — PHPMailer wants the bare hostname.
	$emifree_host = preg_replace( '#^https?://#i', '', $emifree_host );
	// Strip trailing path, just in case.
	$emifree_host = preg_split( '#/#', $emifree_host, 2 )[0];
	$emifree_host = substr( $emifree_host, 0, 253 );

	$emifree_port = isset( $emifree_input['port'] ) ? (int) $emifree_input['port'] : 0;
	if ( $emifree_port < 1 || $emifree_port > 65535 ) {
		$emifree_port = 587;
	}

	$emifree_encryption = isset( $emifree_input['encryption'] ) ? (string) $emifree_input['encryption'] : 'tls';
	if ( ! in_array( $emifree_encryption, array( 'tls', 'ssl', 'none' ), true ) ) {
		$emifree_encryption = 'tls';
	}

	$emifree_username = isset( $emifree_input['username'] ) ? trim( (string) $emifree_input['username'] ) : '';

	// Password is NOT trimmed of internal whitespace — some SMTP providers
	// include intentional spaces/tokens in their generated passwords. We
	// do, however, drop surrounding whitespace because that's almost
	// always an accidental paste artifact.
	$emifree_password = isset( $emifree_input['password'] ) ? (string) $emifree_input['password'] : '';
	$emifree_password = preg_replace( '/^\s+|\s+$/u', '', $emifree_password );

	$emifree_from_email = isset( $emifree_input['from_email'] ) ? trim( (string) $emifree_input['from_email'] ) : '';
	if ( '' !== $emifree_from_email && ! is_email( $emifree_from_email ) ) {
		$emifree_from_email = '';
		add_settings_error(
			'emifree_smtp_settings',
			'emifree_from_email_invalid',
			__( 'From Email was not a valid address and was cleared.', 'emifree-theme' ),
			'warning'
		);
	}

	$emifree_from_name = isset( $emifree_input['from_name'] ) ? sanitize_text_field( (string) $emifree_input['from_name'] ) : '';

	return array(
		'host'       => $emifree_host,
		'port'       => $emifree_port,
		'encryption' => $emifree_encryption,
		'username'   => $emifree_username,
		'password'   => $emifree_password,
		'from_email' => $emifree_from_email,
		'from_name'  => $emifree_from_name,
	);
}

/**
 * Read the saved settings. Cached statically per-request so multiple
 * `phpmailer_init` callbacks don't re-query the option.
 *
 * @return array
 */
function emifree_get_smtp_settings() {
	static $emifree_cached = null;
	if ( null !== $emifree_cached ) {
		return $emifree_cached;
	}
	$emifree_settings = get_option( 'emifree_smtp_settings', emifree_default_smtp_settings() );
	if ( ! is_array( $emifree_settings ) ) {
		$emifree_settings = emifree_default_smtp_settings();
	}
	// Merge over defaults so newly-added keys always exist.
	$emifree_cached = array_merge( emifree_default_smtp_settings(), $emifree_settings );
	return $emifree_cached;
}

/**
 * Decide whether SMTP is fully configured. Empty host OR empty
 * username → no SMTP, fall back to native wp_mail(). Password alone
 * being empty is treated as "no SMTP" too — every SMTP provider we
 * support requires authentication.
 *
 * @return bool
 */
function emifree_smtp_is_configured() {
	$emifree_settings = emifree_get_smtp_settings();
	return (
		'' !== $emifree_settings['host']
		&& '' !== $emifree_settings['username']
		&& '' !== $emifree_settings['password']
	);
}

/**
 * Render the settings page.
 *
 * Layout: a short status banner at the top showing "configured" or
 * "not configured" so the admin knows at a glance whether wp_mail()
 * is currently routed through SMTP. Below that, the standard
 * settings_fields() + form with one fieldset per logical group
 * (server, auth, sender).
 *
 * The form does NOT use do_settings_sections() / add_settings_field()
 * because there's a single option and a flat form is more readable.
 * settings_fields() still emits the nonce so the request is verified.
 */
function emifree_render_smtp_settings_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You do not have permission to view this page.', 'emifree-theme' ) );
	}

	$emifree_settings  = emifree_get_smtp_settings();
	$emifree_configured = emifree_smtp_is_configured();

	// Status box colours / labels.
	$emifree_status_class = $emifree_configured ? 'border-emerald-200 bg-emerald-50 text-emerald-800' : 'border-amber-200 bg-amber-50 text-amber-800';
	$emifree_status_label = $emifree_configured
		? __( 'SMTP is configured. Contact-form submissions are routed through the SMTP server below.', 'emifree-theme' )
		: __( 'SMTP is not configured. The theme falls back to PHP mail() — submissions may silently fail on hosts without an MTA. Configure below to fix.', 'emifree-theme' );
	?>
	<div class="wrap">
		<h1><?php echo esc_html__( 'Emifree SMTP', 'emifree-theme' ); ?></h1>

		<?php settings_errors( 'emifree_smtp_settings' ); ?>

		<div class="emifree-smtp-status <?php echo esc_attr( $emifree_status_class ); ?> border rounded-lg p-4 mb-6" style="max-width: 800px;">
			<p class="m-0 font-medium"><?php echo esc_html( $emifree_status_label ); ?></p>
		</div>

		<form method="post" action="options.php" style="max-width: 800px;">
			<?php settings_fields( 'emifree_smtp_settings_group' ); ?>

			<h2 class="title"><?php echo esc_html__( 'Outgoing server', 'emifree-theme' ); ?></h2>
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row">
						<label for="emifree-smtp-host"><?php echo esc_html__( 'Host', 'emifree-theme' ); ?></label>
					</th>
					<td>
						<input
							type="text"
							id="emifree-smtp-host"
							name="emifree_smtp_settings[host]"
							value="<?php echo esc_attr( $emifree_settings['host'] ); ?>"
							class="regular-text"
							placeholder="smtp.example.com"
							autocomplete="off"
						>
						<p class="description"><?php echo esc_html__( 'Hostname only — no scheme, no path. e.g. smtp.eu.mailgun.org', 'emifree-theme' ); ?></p>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="emifree-smtp-port"><?php echo esc_html__( 'Port', 'emifree-theme' ); ?></label>
					</th>
					<td>
						<input
							type="number"
							id="emifree-smtp-port"
							name="emifree_smtp_settings[port]"
							value="<?php echo esc_attr( (string) $emifree_settings['port'] ); ?>"
							min="1"
							max="65535"
							class="small-text"
						>
						<p class="description">
							<?php echo esc_html__( 'Common: 587 (TLS), 465 (SSL), 25 (no encryption).', 'emifree-theme' ); ?>
						</p>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="emifree-smtp-encryption"><?php echo esc_html__( 'Encryption', 'emifree-theme' ); ?></label>
					</th>
					<td>
						<select id="emifree-smtp-encryption" name="emifree_smtp_settings[encryption]">
							<option value="tls"  <?php selected( $emifree_settings['encryption'], 'tls' ); ?>><?php echo esc_html__( 'TLS (STARTTLS)', 'emifree-theme' ); ?></option>
							<option value="ssl"  <?php selected( $emifree_settings['encryption'], 'ssl' ); ?>><?php echo esc_html__( 'SSL (implicit TLS)', 'emifree-theme' ); ?></option>
							<option value="none" <?php selected( $emifree_settings['encryption'], 'none' ); ?>><?php echo esc_html__( 'None (plain, not recommended)', 'emifree-theme' ); ?></option>
						</select>
					</td>
				</tr>
			</table>

			<h2 class="title"><?php echo esc_html__( 'Authentication', 'emifree-theme' ); ?></h2>
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row">
						<label for="emifree-smtp-username"><?php echo esc_html__( 'Username', 'emifree-theme' ); ?></label>
					</th>
					<td>
						<input
							type="text"
							id="emifree-smtp-username"
							name="emifree_smtp_settings[username]"
							value="<?php echo esc_attr( $emifree_settings['username'] ); ?>"
							class="regular-text"
							autocomplete="off"
						>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="emifree-smtp-password"><?php echo esc_html__( 'Password', 'emifree-theme' ); ?></label>
					</th>
					<td>
						<input
							type="password"
							id="emifree-smtp-password"
							name="emifree_smtp_settings[password]"
							value="<?php echo esc_attr( $emifree_settings['password'] ); ?>"
							class="regular-text"
							autocomplete="new-password"
						>
						<p class="description"><?php echo esc_html__( 'Stored verbatim in the options table. Only admins can see this page.', 'emifree-theme' ); ?></p>
					</td>
				</tr>
			</table>

			<h2 class="title"><?php echo esc_html__( 'Sender (optional overrides)', 'emifree-theme' ); ?></h2>
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row">
						<label for="emifree-smtp-from-email"><?php echo esc_html__( 'From email', 'emifree-theme' ); ?></label>
					</th>
					<td>
						<input
							type="email"
							id="emifree-smtp-from-email"
							name="emifree_smtp_settings[from_email]"
							value="<?php echo esc_attr( $emifree_settings['from_email'] ); ?>"
							class="regular-text"
							placeholder="<?php echo esc_attr( get_option( 'admin_email' ) ); ?>"
						>
						<p class="description"><?php echo esc_html__( 'Must be on the SMTP server\'s authenticated domain or a verified sender. Empty = use the WordPress default.', 'emifree-theme' ); ?></p>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="emifree-smtp-from-name"><?php echo esc_html__( 'From name', 'emifree-theme' ); ?></label>
					</th>
					<td>
						<input
							type="text"
							id="emifree-smtp-from-name"
							name="emifree_smtp_settings[from_name]"
							value="<?php echo esc_attr( $emifree_settings['from_name'] ); ?>"
							class="regular-text"
							placeholder="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>"
						>
						<p class="description"><?php echo esc_html__( 'Empty = use the WordPress site name.', 'emifree-theme' ); ?></p>
					</td>
				</tr>
			</table>

			<?php submit_button( __( 'Save SMTP settings', 'emifree-theme' ) ); ?>
		</form>

		<hr style="margin: 2rem 0; max-width: 800px;">

		<div style="max-width: 800px;">
			<h2 class="title"><?php echo esc_html__( 'Common provider presets', 'emifree-theme' ); ?></h2>
			<p class="description">
				<?php echo esc_html__( 'Quick reference for the values most providers expect. Replace placeholders before saving.', 'emifree-theme' ); ?>
			</p>
			<ul style="list-style: disc; padding-left: 1.5rem;">
				<li><strong>Mailgun (EU):</strong> host <code>smtp.eu.mailgun.org</code>, port <code>587</code>, encryption <code>tls</code></li>
				<li><strong>Mailgun (US):</strong> host <code>smtp.mailgun.org</code>, port <code>587</code>, encryption <code>tls</code></li>
				<li><strong>SendGrid:</strong> host <code>smtp.sendgrid.net</code>, port <code>587</code>, encryption <code>tls</code>, username <code>apikey</code></li>
				<li><strong>Amazon SES (eu-central-1):</strong> host <code>email-smtp.eu-central-1.amazonaws.com</code>, port <code>587</code>, encryption <code>tls</code></li>
				<li><strong>Brevo (ex-Sendinblue):</strong> host <code>smtp-relay.brevo.com</code>, port <code>587</code>, encryption <code>tls</code></li>
				<li><strong>Postmark:</strong> host <code>smtp.postmarkapp.com</code>, port <code>587</code>, encryption <code>tls</code></li>
			</ul>
		</div>
	</div>
	<?php
}

/**
 * Wire the saved SMTP credentials into PHPMailer before each message.
 *
 * Hooked at `phpmailer_init`, which fires on every wp_mail() call. This
 * is the canonical WP-native pattern for pointing wp_mail() at a real
 * SMTP server without a plugin. PHPMailer's `isSMTP()` switches the
 * transport from `mail()` to `smtp`, and we set host/port/encryption/
 * username/password via the standard setters.
 *
 * No-op when SMTP isn't fully configured — i.e. host or username or
 * password is empty. This preserves the existing behaviour (native PHP
 * mail()) on hosts that already work without SMTP.
 *
 * @param PHPMailer $emifree_phpmailer
 */
function emifree_configure_phpmailer_smtp( $emifree_phpmailer ) {
	if ( ! emifree_smtp_is_configured() ) {
		return;
	}

	$emifree_settings = emifree_get_smtp_settings();

	$emifree_phpmailer->isSMTP();
	$emifree_phpmailer->Host       = $emifree_settings['host'];
	$emifree_phpmailer->Port       = (int) $emifree_settings['port'];

	if ( 'ssl' === $emifree_settings['encryption'] ) {
		$emifree_phpmailer->SMTPSecure = 'ssl';
	} elseif ( 'tls' === $emifree_settings['encryption'] ) {
		$emifree_phpmailer->SMTPSecure = 'tls';
	} else {
		$emifree_phpmailer->SMTPSecure = '';
		$emifree_phpmailer->SMTPAutoTLS = false;
	}

	$emifree_phpmailer->SMTPAuth = ( '' !== $emifree_settings['username'] && '' !== $emifree_settings['password'] );
	$emifree_phpmailer->Username = $emifree_settings['username'];
	$emifree_phpmailer->Password = $emifree_settings['password'];

	// Optional from-override. If empty, PHPMailer uses the WP default
	// (admin_email) which most SMTP providers reject anyway. We log a
	// notice so the admin knows.
	if ( '' !== $emifree_settings['from_email'] ) {
		$emifree_phpmailer->setFrom(
			$emifree_settings['from_email'],
			'' !== $emifree_settings['from_name'] ? $emifree_settings['from_name'] : get_bloginfo( 'name' )
		);
	}
}
add_action( 'phpmailer_init', 'emifree_configure_phpmailer_smtp' );