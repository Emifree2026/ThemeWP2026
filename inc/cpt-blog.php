<?php
/**
 * Blog Custom Post Type — blog_post.
 *
 * Authoring surface for weekly posts so we can publish from wp-admin
 * without redeploying the theme. The four existing legacy posts in
 * data/posts/*.php stay in place; CPT entries are merged into the
 * index feed via emifree_get_all_blog_posts_merged() in
 * inc/knowledge.php and resolved per-request by the page-blog-post*.php
 * shims.
 *
 * Why the CPT is invisible to the front end:
 *   - rewrite => false           → no /blog_post/<slug>/ permastruct
 *   - publicly_queryable => false → no front-end query for ?blog_post=
 *   - has_archive => false        → no /blog_post/ archive
 *   - exclude_from_search => true → does not leak into the WP search loop
 * The existing ^blog/([^/]+)/?$ rewrite in functions.php stays
 * top-priority, so /blog/{slug}/ still maps to the same query var.
 *
 * Each post is authored in EN + DE as a SEPARATE CPT entry (not WPML).
 * Slug parity is maintained by emifree_mirror_slug_to_sibling() on save:
 * when post A has emifree_translation_of = B, saving A copies A's slug
 * onto B (and vice versa). The author sets the translation pointer in
 * the meta box after creating both posts.
 *
 * Loaded from functions.php via require_once.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* -------------------------------------------------------------------------
 * 1. CPT registration
 * ------------------------------------------------------------------------- */

if ( ! function_exists( 'emifree_register_blog_cpt' ) ) :
	function emifree_register_blog_cpt() {
		register_post_type(
			'blog_post',
			array(
				'labels'              => array(
					'name'               => __( 'Blog Posts', 'emifree-theme' ),
					'singular_name'      => __( 'Blog Post', 'emifree-theme' ),
					'menu_name'          => __( 'Blog Posts', 'emifree-theme' ),
					'add_new'            => __( 'Add New', 'emifree-theme' ),
					'add_new_item'       => __( 'Add New Blog Post', 'emifree-theme' ),
					'edit_item'          => __( 'Edit Blog Post', 'emifree-theme' ),
					'new_item'           => __( 'New Blog Post', 'emifree-theme' ),
					'view_item'          => __( 'View Blog Post', 'emifree-theme' ),
					'search_items'       => __( 'Search Blog Posts', 'emifree-theme' ),
					'not_found'          => __( 'No blog posts found', 'emifree-theme' ),
					'not_found_in_trash' => __( 'No blog posts found in trash', 'emifree-theme' ),
					'all_items'          => __( 'All Blog Posts', 'emifree-theme' ),
				),
				'public'              => false,
				'show_ui'             => true,
				'show_in_rest'        => true,
				'show_in_menu'        => true,
				'menu_position'       => 6,
				'menu_icon'           => 'dashicons-admin-post',
				'capability_type'     => 'post',
				'supports'            => array( 'title', 'editor', 'excerpt', 'thumbnail', 'custom-fields', 'revisions', 'author' ),
				'has_archive'         => false,
				'exclude_from_search' => true,
				'rewrite'             => false,
				'publicly_queryable'  => false,
				'query_var'           => false,
			)
		);
	}
endif;
add_action( 'init', 'emifree_register_blog_cpt' );

/* -------------------------------------------------------------------------
 * 2. Meta registration — exposed in REST for the Gutenberg sidebar
 * ------------------------------------------------------------------------- */

if ( ! function_exists( 'emifree_register_blog_cpt_meta' ) ) :
	function emifree_register_blog_cpt_meta() {
		// Common args reused for every editor-facing meta field.
		$emifree_text_meta_args = function ( $sanitize_cb ) {
			return array(
				'object_subtype'    => 'blog_post',
				'type'              => 'string',
				'single'            => true,
				'show_in_rest'      => array(
					'schema' => array(
						'type' => 'string',
					),
				),
				'auth_callback'     => function () {
					return current_user_can( 'edit_posts' );
				},
				'sanitize_callback' => $sanitize_cb,
			);
		};

		register_meta(
			'post',
			'emifree_category',
			$emifree_text_meta_args( 'sanitize_text_field' )
		);

		register_meta(
			'post',
			'emifree_read_time',
			$emifree_text_meta_args( 'sanitize_text_field' )
		);

		register_meta(
			'post',
			'emifree_author_role',
			$emifree_text_meta_args( 'sanitize_text_field' )
		);

		// Language — restricted to en / de by the sanitize callback.
		register_meta(
			'post',
			'emifree_language',
			$emifree_text_meta_args(
				function ( $value ) {
					$value = strtolower( (string) $value );
					return in_array( $value, array( 'en', 'de' ), true ) ? $value : '';
				}
			)
		);

		// Translation pointer — int cast, REST-invisible (set by sidebar,
		// not by Gutenberg itself).
		register_meta(
			'post',
			'emifree_translation_of',
			array(
				'object_subtype'    => 'blog_post',
				'type'              => 'integer',
				'single'            => true,
				'show_in_rest'      => false,
				'auth_callback'     => function () {
					return current_user_can( 'edit_posts' );
				},
				'sanitize_callback' => function ( $value ) {
					$value = (int) $value;
					return $value > 0 ? $value : 0;
				},
			)
		);
	}
endif;
add_action( 'init', 'emifree_register_blog_cpt_meta' );

/* -------------------------------------------------------------------------
 * 3. Meta box — sidebar "Blog Post Meta" panel
 * ------------------------------------------------------------------------- */

if ( ! function_exists( 'emifree_blog_meta_box_cb' ) ) :
	function emifree_blog_meta_box_cb( $post ) {
		$emifree_category      = get_post_meta( $post->ID, 'emifree_category', true );
		$emifree_read_time     = get_post_meta( $post->ID, 'emifree_read_time', true );
		$emifree_author_role   = get_post_meta( $post->ID, 'emifree_author_role', true );
		$emifree_language      = get_post_meta( $post->ID, 'emifree_language', true );
		$emifree_translation_of = (int) get_post_meta( $post->ID, 'emifree_translation_of', true );

		wp_nonce_field( 'emifree_blog_meta_box', 'emifree_blog_meta_box_nonce' );
		?>
		<p>
			<label for="emifree_category"><strong><?php esc_html_e( 'Category', 'emifree-theme' ); ?></strong></label><br>
			<input
				type="text"
				id="emifree_category"
				name="emifree_category"
				value="<?php echo esc_attr( $emifree_category ); ?>"
				class="widefat"
				placeholder="Technical Guide"
			>
		</p>
		<p>
			<label for="emifree_read_time"><strong><?php esc_html_e( 'Read time', 'emifree-theme' ); ?></strong></label><br>
			<input
				type="text"
				id="emifree_read_time"
				name="emifree_read_time"
				value="<?php echo esc_attr( $emifree_read_time ); ?>"
				class="widefat"
				placeholder="5 min read"
			>
		</p>
		<p>
			<label for="emifree_author_role"><strong><?php esc_html_e( 'Author role', 'emifree-theme' ); ?></strong></label><br>
			<input
				type="text"
				id="emifree_author_role"
				name="emifree_author_role"
				value="<?php echo esc_attr( $emifree_author_role ); ?>"
				class="widefat"
				placeholder="Product Manager, Emifree GmbH"
			>
		</p>
		<p>
			<label for="emifree_language"><strong><?php esc_html_e( 'Language', 'emifree-theme' ); ?></strong></label><br>
			<select id="emifree_language" name="emifree_language" class="widefat">
				<option value=""  <?php selected( $emifree_language, '' );  ?>><?php esc_html_e( '— unset —', 'emifree-theme' ); ?></option>
				<option value="en" <?php selected( $emifree_language, 'en' ); ?>><?php esc_html_e( 'English', 'emifree-theme' ); ?></option>
				<option value="de" <?php selected( $emifree_language, 'de' ); ?>><?php esc_html_e( 'German', 'emifree-theme' ); ?></option>
			</select>
		</p>
		<p>
			<label for="emifree_translation_of"><strong><?php esc_html_e( 'Translation of (post ID)', 'emifree-theme' ); ?></strong></label><br>
			<input
				type="number"
				id="emifree_translation_of"
				name="emifree_translation_of"
				value="<?php echo esc_attr( $emifree_translation_of ); ?>"
				class="widefat"
				min="0"
				step="1"
				placeholder="0"
			>
			<span class="description"><?php esc_html_e( 'Post ID of the sibling in the other language. Slug parity is auto-maintained on save.', 'emifree-theme' ); ?></span>
		</p>
		<?php
	}
endif;

if ( ! function_exists( 'emifree_register_blog_meta_box' ) ) :
	function emifree_register_blog_meta_box() {
		add_meta_box(
			'emifree_blog_meta',
			__( 'Blog Post Meta', 'emifree-theme' ),
			'emifree_blog_meta_box_cb',
			'blog_post',
			'side',
			'default'
		);
	}
endif;
add_action( 'add_meta_boxes_blog_post', 'emifree_register_blog_meta_box' );

/* -------------------------------------------------------------------------
 * 4. Meta save — sanitize + persist from the sidebar meta box
 * ------------------------------------------------------------------------- */

if ( ! function_exists( 'emifree_save_blog_meta_box' ) ) :
	function emifree_save_blog_meta_box( $post_id, $post ) {
		// Nonce check.
		if ( ! isset( $_POST['emifree_blog_meta_box_nonce'] ) ) {
			return;
		}
		$nonce = sanitize_text_field( wp_unslash( $_POST['emifree_blog_meta_box_nonce'] ) );
		if ( ! wp_verify_nonce( $nonce, 'emifree_blog_meta_box' ) ) {
			return;
		}

		// Autosave guard.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		// Capability guard.
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		// Only act on our CPT.
		if ( 'blog_post' !== $post->post_type ) {
			return;
		}

		$emifree_fields = array(
			'emifree_category'        => 'sanitize_text_field',
			'emifree_read_time'       => 'sanitize_text_field',
			'emifree_author_role'     => 'sanitize_text_field',
			'emifree_language'        => function ( $v ) {
				$v = strtolower( (string) $v );
				return in_array( $v, array( 'en', 'de' ), true ) ? $v : '';
			},
			'emifree_translation_of'  => function ( $v ) {
				$v = (int) $v;
				return $v > 0 ? $v : 0;
			},
		);

		foreach ( $emifree_fields as $emifree_key => $emifree_cb ) {
			if ( ! isset( $_POST[ $emifree_key ] ) ) {
				delete_post_meta( $post_id, $emifree_key );
				continue;
			}
			$emifree_value = call_user_func( $emifree_cb, wp_unslash( $_POST[ $emifree_key ] ) );
			update_post_meta( $post_id, $emifree_key, $emifree_value );
		}
	}
endif;
add_action( 'save_post_blog_post', 'emifree_save_blog_meta_box', 10, 2 );

/* -------------------------------------------------------------------------
 * 5. Slug mirroring — keep EN/DE siblings in sync
 *
 * When post A is saved and has emifree_translation_of = B, copy A's
 * post_name onto B. Reverse direction also covered (saving B copies B's
 * slug back onto A so the pointer is symmetric). The recursive save is
 * guarded with a static $emifree_mirroring flag.
 * ------------------------------------------------------------------------- */

if ( ! function_exists( 'emifree_mirror_slug_to_sibling' ) ) :
	function emifree_mirror_slug_to_sibling( $new_status, $old_status, $post ) {
		// Only act on our CPT.
		if ( ! $post || 'blog_post' !== $post->post_type ) {
			return;
		}
		// Recursion guard.
		static $emifree_mirroring = false;
		if ( $emifree_mirroring ) {
			return;
		}

		$emifree_sibling_id = (int) get_post_meta( $post->ID, 'emifree_translation_of', true );
		if ( $emifree_sibling_id <= 0 || $emifree_sibling_id === $post->ID ) {
			return;
		}
		$emifree_sibling = get_post( $emifree_sibling_id );
		if ( ! $emifree_sibling || 'blog_post' !== $emifree_sibling->post_type ) {
			return;
		}

		// Only sync when the sibling's slug actually differs.
		if ( $emifree_sibling->post_name === $post->post_name ) {
			return;
		}

		$emifree_mirroring = true;
		wp_update_post(
			array(
				'ID'        => $emifree_sibling->ID,
				'post_name' => sanitize_title( $post->post_name ),
			)
		);
		$emifree_mirroring = false;
	}
endif;
add_action( 'wp_transition_post_status', 'emifree_mirror_slug_to_sibling', 10, 3 );
