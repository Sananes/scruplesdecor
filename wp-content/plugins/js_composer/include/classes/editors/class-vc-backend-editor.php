<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * WPBakery Visual Composer admin editor
 *
 * @package WPBakeryVisualComposer
 *
 */

/**
 * VC backend editor.
 *
 * This editor is available on default Wp post/page admin edit page. ON admin_init callback adds meta box to
 * edit page.
 *
 * @since 4.2
 */
class Vc_Backend_Editor implements Vc_Editor_Interface {

	/**
	 * @var
	 */
	protected $layout;
	/**
	 * @var
	 */
	public $post_custom_css;
	/**
	 * @var bool|string $post - stores data about post.
	 */
	public $post = false;

	/**
	 * This method is called by Vc_Manager to register required action hooks for VC backend editor.
	 *
	 * @since  4.2
	 * @access public
	 */
	public function addHooksSettings() {
		// @todo - fix_roles do this only if be editor is enabled.
		add_action( 'wp_ajax_wpb_get_element_backend_html', array(
			&$this,
			'elementBackendHtml',
		) );
		// load backend editor
		if ( function_exists( 'add_theme_support' ) ) {
			add_theme_support( 'post-thumbnails' ); // @todo check is it needed?
		}
		add_action( 'admin_init', array( &$this, 'render' ), 5 );
		add_action( 'admin_print_scripts-post.php', array(
			&$this,
			'printScriptsMessages',
		) );
		add_action( 'admin_print_scripts-post-new.php', array(
			&$this,
			'printScriptsMessages',
		) );

	}

	/**
	 *    Calls add_meta_box to create Editor block. Block is rendered by WPBakeryVisualComposerLayout.
	 *
	 * @see WPBakeryVisualComposerLayout
	 * @since  4.2
	 * @access public
	 */
	public function render() {
		// @todo fix_roles bc for post_types, maybe initialize ajax hooks also when we are inside vc_editor_post_types?
		global $pagenow;
		if ( 'post.php' === $pagenow ) {
			do_action( 'vc_backend_editor_before_render' );
			// we editing existing entity
			$id = (int) vc_request_param( 'post' );
			// @todo add check if vc is enabled for this post_type
			$type = get_post_type( $id );
			$valid = vc_check_post_type( $type );
			if ( $valid ) {
				add_meta_box( 'wpb_visual_composer', __( 'Visual Composer', 'js_composer' ), array(
					&$this,
					'renderEditor',
				), $type, 'normal', 'high' );
			}
		} elseif ( 'post-new.php' === $pagenow ) {
			// we creating new entitiy
			$type = sanitize_text_field( vc_request_param( 'post_type' ) );
			$type = empty( $type ) ? 'post' : $type;
			$valid = vc_check_post_type( $type );
			if ( $valid ) {
				add_meta_box( 'wpb_visual_composer', __( 'Visual Composer', 'js_composer' ), array(
					&$this,
					'renderEditor',
				), $type, 'normal', 'high' );
			}
		}
	}

	/**
	 * Output html for backend editor meta box.
	 *
	 * @param null|Wp_Post $post
	 *
	 * @return bool
	 */
	public function renderEditor( $post = null ) {
		/**
		 * TODO: setter/getter for $post
		 */
		if ( ! is_object( $post ) || 'WP_Post' !== get_class( $post ) || ! isset( $post->ID ) ) {
			return false;
		}
		$this->post = $post;
		$this->post_custom_css = get_post_meta( $post->ID, '_wpb_post_custom_css', true );
		vc_include_template( 'editors/backend_editor.tpl.php', array(
			'editor' => $this,
			'post' => $this->post,
		) );
		add_action( 'admin_footer', array( &$this, 'renderEditorFooter' ) );
		do_action( 'vc_backend_editor_render' );

		return true;
	}

	/**
	 * Output required html and js content for VC editor.
	 *
	 * Here comes panels, modals and js objects with data for mapped shortcodes.
	 */
	public function renderEditorFooter() {
		vc_include_template( 'editors/partials/backend_editor_footer.tpl.php', array(
			'editor' => $this,
			'post' => $this->post,
		) );
		do_action( 'vc_backend_editor_footer_render' );
	}

	/**
	 * Check is post type is valid for rendering VC backend editor.
	 *
	 * @return bool
	 */
	public function isValidPostType() {
		return vc_check_post_type( get_post_type() );
	}

	/**
	 * Enqueue required javascript libraries and css files.
	 *
	 * This method also setups reminder about license activation.
	 *
	 * @since  4.2
	 * @access public
	 */
	public function printScriptsMessages() {
		if ( $this->isValidPostType() ) {
			if ( vc_user_access()
				->wpAny( 'manage_options' )
				->part( 'settings' )
				->can( 'vc-updater-tab' )
				->get()
			) {
				vc_license()->setupReminder();
			}
			$this->enqueueEditorScripts();
		}
	}

	/**
	 * Enqueue required javascript libraries and css files.
	 *
	 * @since  4.8
	 * @access public
	 */
	public function enqueueEditorScripts() {
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script( 'wp-color-picker' );
		wp_enqueue_style( 'farbtastic' );
		wp_enqueue_style( 'ui-custom-theme' );
		wp_enqueue_style( 'isotope-css' );
		wp_enqueue_style( 'animate-css' );
		wp_enqueue_style( 'font-awesome' );
		wp_enqueue_style( 'js_composer' );
		wp_enqueue_style( 'wpb_jscomposer_autosuggest' );
		//wp_enqueue_style( 'js_composer_settings', vc_asset_url( 'css/js_composer_settings.min.css' ), array(), WPB_VC_VERSION, false );
		WPBakeryShortCodeFishBones::enqueueCss();

		wp_enqueue_script( 'jquery-ui-tabs' );
		wp_enqueue_script( 'jquery-ui-sortable' );
		wp_enqueue_script( 'jquery-ui-droppable' );
		wp_enqueue_script( 'jquery-ui-draggable' );
		wp_enqueue_script( 'jquery-ui-accordion' );
		wp_enqueue_script( 'jquery-ui-autocomplete' );
		wp_enqueue_script( 'jquery-ui-resizable' );
		wp_enqueue_script( 'farbtastic' );
		wp_enqueue_script( 'isotope' );
		$bootstrap_version = '3.0.2';
		wp_enqueue_script( 'vc_bootstrap_js_1', vc_asset_url( 'lib/bower/bootstrap3/js/modal.js' ), array( 'jquery' ), $bootstrap_version, true );
		wp_enqueue_script( 'vc_bootstrap_js_2', vc_asset_url( 'lib/bower/bootstrap3/js/dropdown.js' ), array( 'jquery' ), $bootstrap_version, true );
		wp_enqueue_script( 'vc_bootstrap_js_11', vc_asset_url( 'lib/bower/bootstrap3/js/transition.js' ), array( 'jquery' ), $bootstrap_version, true );
		wp_enqueue_script( 'wpb_scrollTo_js' );
		wp_enqueue_script( 'wpb_php_js' );
		wp_enqueue_script( 'wpb_js_composer_js_sortable' );
		wp_enqueue_script( 'wpb_json-js' );
		wp_enqueue_script( 'ace-editor' );
		wp_enqueue_script( 'webfont', '//ajax.googleapis.com/ajax/libs/webfont/1.4.7/webfont.js' ); // Google Web Font CDN
		wp_enqueue_script( 'wpb_js_composer_js_tools' );
		wp_enqueue_script( 'wpb_js_composer_js_storage' );
		wp_enqueue_script( 'wpb_js_composer_js_models' );
		wp_enqueue_script( 'wpb_js_composer_js_view' );
		wp_enqueue_script( 'wpb_js_composer_js_custom_views' );
		/**
		 * Enqueue deprecated
		 * @since 4.4 removed
		 */
		wp_enqueue_script( 'wpb_js_composer_js_backbone' );
		wp_enqueue_script( 'wpb_jscomposer_composer_js' );
		wp_enqueue_script( 'wpb_jscomposer_shortcode_js' );
		wp_enqueue_script( 'wpb_jscomposer_modal_js' );
		wp_enqueue_script( 'wpb_jscomposer_templates_js' );
		wp_enqueue_script( 'wpb_jscomposer_stage_js' );
		wp_enqueue_script( 'wpb_jscomposer_layout_js' );
		wp_enqueue_script( 'wpb_jscomposer_row_js' );
		wp_enqueue_script( 'wpb_jscomposer_settings_js' );
		wp_enqueue_script( 'wpb_jscomposer_media_editor_js' );
		wp_enqueue_script( 'wpb_jscomposer_autosuggest_js' );
		wp_enqueue_script( 'wpb_js_composer_js' );
		/**
		 * @since 4.4
		 */
		do_action( 'vc_backend_editor_enqueue_js_css' );
		WPBakeryShortCodeFishBones::enqueueJs();
	}

	/**
	 * Save generated shortcodes, html and visual composer status in posts meta.
	 *
	 * @deprecated 4.4
	 * @remove @todo remove this. comment added in 4.8
	 * @unused.
	 * @since  3.0
	 * @access public
	 *
	 * @param $post_id - current post id
	 *
	 * @return void
	 */
	public function save( $post_id ) {
		visual_composer()->postAdmin()->save( $post_id );
	}

	/**
	 * Create shortcode's string.
	 *
	 * @since  3.0
	 * @access public
	 * @deprecated
	 */
	public function elementBackendHtml() {
		vc_user_access()
			->checkAdminNonce()
			->validateDie()
			->wpAny( 'edit_posts', 'edit_pages' )
			->validateDie()
			->part( 'backend_editor' )
			->can() // checks is backend_editor enabled( !== false )
			->validateDie();

		$data_element = vc_post_param( 'data_element' );

		if ( 'vc_column' === $data_element && null !== vc_post_param( 'data_width' ) ) {
			$output = do_shortcode( '[vc_column width="' . vc_post_param( 'data_width' ) . '"]' );
			echo $output;
		} elseif ( 'vc_row' === $data_element || 'vc_row_inner' === $data_element ) {
			$output = do_shortcode( '[' . $data_element . ']' );
			echo $output;
		} else {
			$output = do_shortcode( '[' . $data_element . ']' );
			echo $output;
		}
		die();
	}

	/**
	 * @deprecated since 4.8
	 * @return string
	 */
	public function showRulesValue() {
		global $current_user;
		get_currentuserinfo();
		/** @var $settings - get use group access rules */
		$settings = vc_settings()->get( 'groups_access_rules' );
		$role = is_object( $current_user ) && isset( $current_user->roles[0] ) ? $current_user->roles[0] : '';

		return isset( $settings[ $role ]['show'] ) ? $settings[ $role ]['show'] : '';
	}
}
