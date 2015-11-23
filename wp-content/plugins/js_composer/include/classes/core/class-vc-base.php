<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * WPBakery Visual Composer main class.
 *
 * @package WPBakeryVisualComposer
 * @since   4.2
 */

/**
 * Visual Composer basic class.
 * @since 4.2
 */
class Vc_Base {
	/**
	 * Shortcode's edit form.
	 *
	 * @since  4.2
	 * @access protected
	 * @var bool|Vc_Shortcode_Edit_Form
	 */
	protected $shortcode_edit_form = false;

	/**
	 * Templates management panel.
	 * @deprecated 4.4 updated to $templates_panel_editor, use Vc_Base::setTemplatesPanelEditor
	 * @since  4.2
	 * @access protected
	 * @var bool|Vc_Templates_Editor
	 */
	protected $templates_editor = false;
	/**
	 * Templates management panel editor.
	 * @since  4.4
	 * @access protected
	 * @var bool|Vc_Templates_Panel_Editor
	 */
	protected $templates_panel_editor = false;
	/**
	 * Post object for VC in Admin.
	 *
	 * @since  4.4
	 * @access protected
	 * @var bool|Vc_Post_Admin
	 */
	protected $post_admin = false;
	/**
	 * Post object for VC.
	 *
	 * @since  4.4.3
	 * @access protected
	 * @var bool|Vc_Post_Admin
	 */
	protected $post = false;
	/**
	 * List of shortcodes map to VC.
	 *
	 * @since  4.2
	 * @access public
	 * @var array WPBakeryShortCodeFishBones
	 */
	protected $shortcodes = array();

	/**
	 * @deprecated 4.4 due to autoload logic
	 * @var Vc_Vendors_Manager $vendor_manager
	 */
	protected $vendor_manager;

	/**
	 * Load default object like shortcode parsing.
	 *
	 * @since  4.2
	 * @access public
	 */
	public function init() {
		do_action( 'vc_before_init_base' );
		if ( is_admin() ) {
			$this->postAdmin()->init();
		}
		add_filter( 'body_class', array( &$this, 'bodyClass' ) );
		add_filter( 'the_excerpt', array( &$this, 'excerptFilter' ) );
		add_action( 'wp_head', array( &$this, 'addMetaData' ) );
		if ( is_admin() ) {
			$this->initAdmin();
		} else {
			$this->initPage();
		}
		do_action( 'vc_after_init_base' );
	}

	/**
	 * Post object for interacting with Current post data.
	 * @since 4.4
	 * @return Vc_Post_Admin
	 */
	public function postAdmin() {
		if ( false === $this->post_admin ) {
			require_once vc_path_dir( 'CORE_DIR', 'class-vc-post-admin.php' );
			$this->post_admin = new Vc_Post_Admin();
		}

		return $this->post_admin;
	}

	/**
	 * Build VC for frontend pages.
	 *
	 * @since  4.2
	 * @access public
	 */
	public function initPage() {
		do_action( 'vc_build_page' );
		add_action( 'template_redirect', array( &$this, 'frontCss' ) );
		add_action( 'wp_head', array( &$this, 'addFrontCss' ), 1000 );
		add_action( 'wp_head', array( &$this, 'addNoScript' ), 1000 );
		add_action( 'template_redirect', array( &$this, 'frontJsRegister' ) );
		add_filter( 'the_content', array( &$this, 'fixPContent' ), 11 );
	}

	/**
	 * Load admin required modules and elements
	 *
	 * @since  4.2
	 * @access public
	 */
	public function initAdmin() {
		do_action( 'vc_build_admin_page' );
		// Build settings for admin page;
		$this->registerAdminJavascript();
		$this->registerAdminCss();
		$this->editForm()->init();
		$this->templatesPanelEditor()
		     ->init(); // new Templates editor @since 4.4
		add_action( 'edit_post', array( &$this, 'save' ) );
		add_action( 'wp_ajax_wpb_single_image_src', array(
			&$this,
			'singleImageSrc',
		) ); // @todo move it
		add_action( 'wp_ajax_wpb_gallery_html', array(
			&$this,
			'galleryHTML',
		) ); // @todo move it
		add_filter( 'plugin_action_links', array(
			&$this,
			'pluginActionLinks',
		), 10, 2 );
	}

	/**
	 * Setter for edit form.
	 * @since 4.2
	 *
	 * @param Vc_Shortcode_Edit_Form $form
	 */
	public function setEditForm( Vc_Shortcode_Edit_Form $form ) {
		$this->shortcode_edit_form = $form;
	}

	/**
	 * Get Shortcodes Edit form object.
	 *
	 * @see    Vc_Shortcode_Edit_Form::__construct
	 * @since  4.2
	 * @access public
	 * @return Vc_Shortcode_Edit_Form
	 */
	public function editForm() {
		return $this->shortcode_edit_form;
	}

	/**
	 * Setter for Templates editor.
	 * @deprecated 4.4 updated to panel editor see Vc_Templates_Panel_Editor::__construct
	 * @use setTemplatesPanelEditor
	 * @since 4.2
	 *
	 * @param Vc_Templates_Editor $editor
	 */
	public function setTemplatesEditor( Vc_Templates_Editor $editor ) {
		_deprecated_function( 'Vc_Base::setTemplatesEditor', '4.4', 'Vc_Base::setTemplatesPanelEditor' );
		$this->templates_editor = $editor;
	}

	/**
	 * Setter for Templates editor.
	 * @since 4.4
	 *
	 * @param Vc_Templates_Panel_Editor $editor
	 */
	public function setTemplatesPanelEditor( Vc_Templates_Panel_Editor $editor ) {
		$this->templates_panel_editor = $editor;
	}

	/**
	 * Get templates manager.
	 * @deprecated updated to panel editor see Vc_Templates_Panel_Editor::__construct
	 * @see    Vc_Templates_Editor::__construct
	 * @since  4.2
	 * @access public
	 * @return bool|Vc_Templates_Editor
	 */
	public function templatesEditor() {
		_deprecated_function( 'Vc_Base::templatesEditor', '4.4', 'Vc_Base::templatesPanelEditor' );

		return $this->templates_editor;
	}

	/**
	 * Get templates manager.
	 * @see    Vc_Templates_Panel_Editor::__construct
	 * @since  4.4
	 * @access public
	 * @return bool|Vc_Templates_Panel_Editor
	 */
	public function templatesPanelEditor() {
		return $this->templates_panel_editor;
	}

	/**
	 * Save method for edit_post action.
	 *
	 * @since  4.2
	 * @access public
	 *
	 * @param null $post_id
	 */
	public function save( $post_id = null ) {
		// @todo fix_roles maybe we also need to check if vc_enabled?
		if ( ! vc_user_access()
			->wpAny( array( 'edit_post', $post_id ) )
			->get()
		) {
			return;
		}
		/**
		 * vc_filter: vc_base_save_post_custom_css
		 * @since 4.4
		 */
		$post_custom_css = apply_filters( 'vc_base_save_post_custom_css',
		vc_post_param( 'vc_post_custom_css' ) );
		if ( null !== $post_custom_css && empty( $post_custom_css ) ) {
			delete_post_meta( $post_id, '_wpb_post_custom_css' );
		} elseif ( null !== $post_custom_css ) {
			update_post_meta( $post_id, '_wpb_post_custom_css', $post_custom_css );
		}
		visual_composer()->buildShortcodesCustomCss( $post_id );
	}

	/**
	 * Add new shortcode to Visual composer.
	 *
	 * @see    WPBMap::map
	 * @since  4.2
	 * @access public
	 *
	 * @param array $shortcode - array of options.
	 */
	public function addShortCode( array $shortcode ) {
		require_once vc_path_dir( 'SHORTCODES_DIR', 'shortcodes.php' );
		$this->shortcodes[ $shortcode['base'] ] = new WPBakeryShortCodeFishBones( $shortcode );
	}

	/**
	 * Get shortcode class instance.
	 *
	 * @see    WPBakeryShortCodeFishBones
	 * @since  4.2
	 * @access public
	 *
	 * @param string $tag
	 *
	 * @return WPBakeryShortCodeFishBones|null
	 */
	public function getShortCode( $tag ) {
		return isset( $this->shortcodes[ $tag ] ) ? $this->shortcodes[ $tag ] : null;
	}

	/**
	 * Remove shortcode from shortcodes list of VC.
	 *
	 * @since  4.2
	 * @access public
	 *
	 * @param $tag - shortcode tag
	 */
	public function removeShortCode( $tag ) {
		remove_shortcode( $tag );
	}

	/**
	 * @todo move it
	 * @since 4.2
	 */
	public function singleImageSrc() {
		// @todo again, this method should be moved (comment added on 4.8)
		vc_user_access()
			->checkAdminNonce()
			->validateDie()
			->wpAny( 'edit_posts', 'edit_pages' )
			->validateDie();

		$image_id = (int) vc_post_param( 'content' );
		$params = vc_post_param( 'params' );
		$post_id = vc_post_param( 'post_id' );
		$img_size = vc_post_param( 'size' );
		$img = '';

		if ( ! empty( $params['source'] ) ) {
			$source = $params['source'];
		} else {
			$source = 'media_library';
		}

		switch ( $source ) {
			case 'media_library':
			case 'featured_image':

				if ( 'featured_image' === $source ) {
					if ( $post_id && has_post_thumbnail( $post_id ) ) {
						$img_id = get_post_thumbnail_id( $post_id );
					} else {
						$img_id = 0;
					}
				} else {
					$img_id = preg_replace( '/[^\d]/', '', $image_id );
				}

				if ( ! $img_size ) {
					$img_size = 'thumbnail';
				}

				if ( $img_id ) {
					$img = wp_get_attachment_image_src( $img_id, $img_size );
					if ( $img ) {
						$img = $img[0];
					}
				}

				break;

			case 'external_link':
				if ( ! empty( $params['custom_src'] ) ) {
					$img = $params['custom_src'];
				}
				break;
		}

		die( $img );
	}

	/**
	 * @todo move it
	 * @since 4.2
	 */
	public function galleryHTML() {
		// @todo again, this method should be moved (comment added on 4.8)
		vc_user_access()
			->checkAdminNonce()
			->validateDie()
			->wpAny( 'edit_posts', 'edit_pages' )
			->validateDie();

		$images = vc_post_param( 'content' );
		if ( ! empty( $images ) ) {
			echo fieldAttachedImages( explode( ',', $images ) );
		}
		die();
	}

	/**
	 * Rewrite code or name
	 * @since 4.2
	 */
	public function createShortCodes() {
		remove_all_shortcodes();
		foreach ( WPBMap::getShortCodes() as $sc_base => $el ) {
			$this->shortcodes[ $sc_base ] = new WPBakeryShortCodeFishBones( $el );
		}
	}

	/**
	 * Set or modify new settings for shortcode.
	 *
	 * This function widely used by WPBMap class methods to modify shortcodes mapping
	 *
	 * @since 4.3
	 *
	 * @param $tag
	 * @param $name
	 * @param $value
	 */
	public function updateShortcodeSetting( $tag, $name, $value ) {
		$this->shortcodes[ $tag ]->setSettings( $name, $value );
	}

	/**
	 * Build custom css styles for page from shortcodes attributes created by VC editors.
	 *
	 * Called by save method, which is hooked by edit_post action.
	 * Function creates meta data for post with the key '_wpb_shortcodes_custom_css'
	 * and value as css string, which will be added to the footer of the page.
	 *
	 * @since  4.2
	 * @access public
	 *
	 * @param $post_id
	 */
	public function buildShortcodesCustomCss( $post_id ) {
		$post = get_post( $post_id );

		/**
		 * vc_filter: vc_base_build_shortcodes_custom_css
		 * @since 4.4
		 */
		$css = apply_filters( 'vc_base_build_shortcodes_custom_css',
		$this->parseShortcodesCustomCss( $post->post_content ) );
		if ( empty( $css ) ) {
			delete_post_meta( $post_id, '_wpb_shortcodes_custom_css' );
		} else {
			update_post_meta( $post_id, '_wpb_shortcodes_custom_css', $css );
		}
	}

	/**
	 * Parse shortcodes custom css string.
	 *
	 * This function is used by self::buildShortcodesCustomCss and creates css string from shortcodes attributes
	 * like 'css_editor'.
	 *
	 * @see    WPBakeryVisualComposerCssEditor
	 * @since  4.2
	 * @access public
	 *
	 * @param $content
	 *
	 * @return string
	 */
	public function parseShortcodesCustomCss( $content ) {
		$css = '';
		if ( ! preg_match( '/\s*(\.[^\{]+)\s*\{\s*([^\}]+)\s*\}\s*/', $content ) ) {
			return $css;
		}
		preg_match_all( '/' . get_shortcode_regex() . '/', $content, $shortcodes );
		foreach ( $shortcodes[2] as $index => $tag ) {
			$shortcode = WPBMap::getShortCode( $tag );
			$attr_array = shortcode_parse_atts( trim( $shortcodes[3][ $index ] ) );
			if ( isset( $shortcode['params'] ) && ! empty( $shortcode['params'] ) ) {
				foreach ( $shortcode['params'] as $param ) {
					if ( 'css_editor' === $param['type'] && isset( $attr_array[ $param['param_name'] ] ) ) {
						$css .= $attr_array[ $param['param_name'] ];
					}
				}
			}
		}
		foreach ( $shortcodes[5] as $shortcode_content ) {
			$css .= $this->parseShortcodesCustomCss( $shortcode_content );
		}

		return $css;
	}

	/**
	 * Hooked class method by wp_head WP action to output post custom css.
	 *
	 * Method gets post meta value for page by key '_wpb_post_custom_css' and if it is not empty
	 * outputs css string wrapped into style tag.
	 *
	 * @since  4.2
	 * @access public
	 *
	 * @param int $id
	 */
	public function addPageCustomCss( $id = null ) {
		if ( ! is_singular() ) {
			return;
		}
		if ( ! $id ) {
			$id = get_the_ID();
		}
		if ( $id ) {
			$post_custom_css = get_post_meta( $id, '_wpb_post_custom_css', true );
			if ( ! empty( $post_custom_css ) ) {
				echo '<style type="text/css" data-type="vc_custom-css">';
				echo $post_custom_css;
				echo '</style>';
			}
		}
	}

	/**
	 * Hooked class method by wp_footer WP action to output shortcodes css editor settings from page meta data.
	 *
	 * Method gets post meta value for page by key '_wpb_shortcodes_custom_css' and if it is not empty
	 * outputs css string wrapped into style tag.
	 *
	 * @since  4.2
	 * @access public
	 *
	 * @param int $id
	 *
	 */
	public function addShortcodesCustomCss( $id = null ) {
		if ( ! is_singular() ) {
			return;
		}
		if ( ! $id ) {
			$id = get_the_ID();
		}

		if ( $id ) {
			$shortcodes_custom_css = get_post_meta( $id, '_wpb_shortcodes_custom_css', true );
			if ( ! empty( $shortcodes_custom_css ) ) {
				echo '<style type="text/css" data-type="vc_shortcodes-custom-css">';
				echo $shortcodes_custom_css;
				echo '</style>';
			}
		}
	}

	/**
	 * Add css styles for current page and elements design options added w\ editor.
	 */
	public function addFrontCss() {
		get_post();
		$this->addPageCustomCss();
		$this->addShortcodesCustomCss();
	}

	public function addNoScript() {
		echo '<noscript>';
		echo '<style type="text/css">';
		echo ' .wpb_animate_when_almost_visible { opacity: 1; }';
		echo '</style>';
		echo '</noscript>';
	}

	/**
	 * Register front css styles.
	 *
	 * Calls wp_register_style for required css libraries files.
	 *
	 * @since  3.1
	 * @access public
	 */
	public function frontCss() {
		wp_register_style( 'flexslider', vc_asset_url( 'lib/bower/flexslider/flexslider.css' ), false, WPB_VC_VERSION, 'screen' );
		wp_register_style( 'nivo-slider-css', vc_asset_url( 'lib/bower/nivoslider/nivo-slider.css' ), false, WPB_VC_VERSION, 'screen' );
		wp_register_style( 'nivo-slider-theme', vc_asset_url( 'lib/bower/nivoslider/themes/default/default.css' ), array( 'nivo-slider-css' ), WPB_VC_VERSION, 'screen' );
		wp_register_style( 'prettyphoto', vc_asset_url( 'lib/prettyphoto/css/prettyPhoto.css' ), false, WPB_VC_VERSION, 'screen' );
		wp_register_style( 'isotope-css', vc_asset_url( 'css/lib/isotope.css' ), false, WPB_VC_VERSION, 'all' );
		wp_register_style( 'font-awesome', vc_asset_url( 'lib/bower/font-awesome/css/font-awesome.min.css' ), false, WPB_VC_VERSION, 'screen' );

		$front_css_file = vc_asset_url( 'css/js_composer.min.css' );
		$upload_dir = wp_upload_dir();
		if ( '1' === vc_settings()->get( 'use_custom' ) && is_file( $upload_dir['basedir'] . '/' . vc_upload_dir() . '/js_composer_front_custom.css' ) ) {
			$front_css_file = $upload_dir['baseurl'] . '/' . vc_upload_dir() . '/js_composer_front_custom.css';
			// fix @since 4.4, TODO: review it.
			$front_css_file = str_replace( array(
				'http://',
				'https://',
			), '//', $front_css_file );
		}
		wp_register_style( 'js_composer_front', $front_css_file, false, WPB_VC_VERSION, 'all' );
		$custom_css_path = $upload_dir['basedir'] . '/' . vc_upload_dir() . '/custom.css';
		if ( is_file( $upload_dir['basedir'] . '/' . vc_upload_dir() . '/custom.css' ) ) {

			$custom_css_url = $upload_dir['baseurl'] . '/' . vc_upload_dir() . '/custom.css';
			// TODO: fix file_get_content()
			if ( strlen( trim( vc_file_get_contents( $custom_css_path ) ) ) > 0 ) {
				$custom_css_url = str_replace( array(
					'http://',
					'https://',
				), '//', $custom_css_url );
				wp_register_style( 'js_composer_custom_css', $custom_css_url, array(), WPB_VC_VERSION, 'screen' );
			}
		}
		add_action( 'wp_enqueue_scripts', array( &$this, 'enqueueStyle' ) );

		/**
		 * @since 4.4
		 */
		do_action( 'vc_base_register_front_css' );
	}

	/**
	 * Enqueue base css class for VC elements and enqueue custom css if exists.
	 */
	public function enqueueStyle() {
		$post = get_post();
		if ( $post && preg_match( '/vc_row/', $post->post_content ) ) {
			wp_enqueue_style( 'js_composer_front' );
		}
		wp_enqueue_style( 'js_composer_custom_css' );
	}

	/**
	 * Register front javascript libs.
	 *
	 * Calls wp_register_script for required css libraries files.
	 *
	 * @since  3.1
	 * @access public
	 */
	public function frontJsRegister() {
		wp_register_script( 'tweet', vc_asset_url( 'lib/jquery.tweet/jquery.tweet.js' ), array( 'jquery' ), WPB_VC_VERSION, true );
		wp_register_script( 'jcarousellite', vc_asset_url( 'lib/jcarousellite/jcarousellite_1.0.1.min.js' ), array( 'jquery' ), WPB_VC_VERSION, true );
		wp_register_script( 'prettyphoto', vc_asset_url( 'lib/prettyphoto/js/jquery.prettyPhoto.js' ), array( 'jquery' ), WPB_VC_VERSION, true );
		wp_register_script( 'waypoints', vc_asset_url( 'lib/waypoints/waypoints.min.js' ), array( 'jquery' ), WPB_VC_VERSION, true );

		wp_register_script( 'jquery_ui_tabs_rotate', vc_asset_url( 'lib/bower/jquery-ui-tabs-rotate/jquery-ui-tabs-rotate.js' ), array(
			'jquery',
			'jquery-ui-tabs',
		), WPB_VC_VERSION, true );
		wp_register_script( 'isotope', vc_asset_url( 'lib/bower/isotope/dist/isotope.pkgd.min.js' ), array( 'jquery' ), WPB_VC_VERSION, true );
		wp_register_script( 'twbs-pagination', vc_asset_url( 'lib/bower/twbs-pagination/jquery.twbsPagination.min.js' ), array( 'jquery' ), WPB_VC_VERSION, true );
		wp_register_script( 'nivo-slider', vc_asset_url( 'lib/bower/nivoslider/jquery.nivo.slider.pack.js' ), array( 'jquery' ), WPB_VC_VERSION, true );
		wp_register_script( 'flexslider', vc_asset_url( 'lib/bower/flexslider/jquery.flexslider-min.js' ), array( 'jquery' ), WPB_VC_VERSION, true );
		wp_register_script( 'vc_accordion_script', vc_asset_url( 'lib/vc_accordion/vc-accordion.js' ), array( 'jquery' ), WPB_VC_VERSION, true );
		wp_register_script( 'vc_tabs_script', vc_asset_url( 'lib/vc_tabs/vc-tabs.js' ), array( 'jquery' ), WPB_VC_VERSION, true );
		wp_register_script( 'vc_tta_autoplay_script', vc_asset_url( 'lib/vc-tta-autoplay/vc-tta-autoplay.js' ), array( 'vc_accordion_script' ), WPB_VC_VERSION, true );
		wp_register_script( 'wpb_composer_front_js', vc_asset_url( 'js/js_composer_front.js' ), array( 'jquery' ), WPB_VC_VERSION, true );
		/**
		 * @since 4.4
		 */
		do_action( 'vc_base_register_front_js' );
	}

	/**
	 * Register admin javascript libs.
	 *
	 * Calls wp_register_script for required css libraries files for Admin dashboard.
	 *
	 * @since  3.1
	 * vc_filter: vc_i18n_locale_composer_js_view, since 4.4 - override localization for js
	 * @access public
	 */
	public function registerAdminJavascript() {
		/**
		 * TODO: REFACTOR
		 * Save register only core js files and check for backend or front
		 */
		wp_register_script( 'wpb_php_js', vc_asset_url( 'lib/php.default/php.default.min.js' ), array( 'jquery' ), WPB_VC_VERSION, true );

		wp_register_script( 'isotope', vc_asset_url( 'lib/bower/isotope/dist/isotope.pkgd.min.js' ), array( 'jquery' ), WPB_VC_VERSION, true );
		wp_register_script( 'wpb_scrollTo_js', vc_asset_url( 'lib/bower/scrollTo/jquery.scrollTo.min.js' ), array( 'jquery' ), WPB_VC_VERSION, true );
		wp_register_script( 'wpb_json-js', vc_asset_url( 'lib/bower/json-js/json2.js' ), false, WPB_VC_VERSION, true );

		wp_register_script( 'wpb_js_composer_js_listeners', vc_asset_url( 'js/lib/events.js' ), array(
			'jquery',
			'backbone',
			'wpb_json-js',
		), WPB_VC_VERSION, true );
		wp_register_script( 'vc_ui-extend-backbone-js', vc_asset_url( 'js/editors/ui/vc_ui-extend-backbone.js' ), array( 'wpb_js_composer_js_listeners' ), WPB_VC_VERSION, true );

		wp_localize_script( 'wpb_js_composer_js_listeners', 'vcData', apply_filters( 'vc_global_js_data', array(
			'version' => WPB_VC_VERSION,
			'debug' => wpb_debug(),
		) ) );

		wp_register_script( 'wpb_js_composer_js_tools', vc_asset_url( 'js/backend/composer-tools.js' ), array(
			'jquery',
			'backbone',
			'wpb_json-js',
			'wpb_js_composer_js_listeners',
			'vc_ui-extend-backbone-js',
		), WPB_VC_VERSION, true );
		wp_register_script( 'wpb_js_composer_settings', vc_asset_url( 'js/backend/composer-settings-page.js' ), array(
			'jquery',
			'wpb_js_composer_js_tools',
		), WPB_VC_VERSION, true );
		wp_register_script( 'wpb_jscomposer_media_editor_js', vc_asset_url( 'js/backend/media-editor.js' ), array(
			'media-views',
			'media-editor',
			'mce-view',
			'wpb_js_composer_js_view',
		), WPB_VC_VERSION, true );
		wp_register_script( 'wpb_js_composer_js_atts', vc_asset_url( 'js/params/composer-atts.js' ), array(
			'wp-color-picker',
			'wpb_js_composer_js_tools',
		), WPB_VC_VERSION, true );

		wp_register_script( 'vc_accordion_script', vc_asset_url( 'lib/vc_accordion/vc-accordion.js' ), array( 'jquery' ), WPB_VC_VERSION, true );
		wp_register_script( 'vc_ui-tabs-line-js', vc_asset_url( 'js/editors/ui/vc_ui-tabs-line.js' ), array(
			'jquery',
			'vc_accordion_script',
		), WPB_VC_VERSION, true );

		wp_register_script( 'wpb_js_composer_editor_panels', vc_asset_url( 'js/editors/panels.js' ), array( 'wpb_js_composer_js_models' ), WPB_VC_VERSION, true );

		wp_register_script( 'vc_ui-helper-panel-view-header-footer-js', vc_asset_url( 'js/editors/ui/vc_ui-helper-panel-view-header-footer.js' ), array(
			'jquery',
			'underscore',
			'backbone',
			'wpb_js_composer_editor_panels',
		), WPB_VC_VERSION, true );
		wp_register_script( 'vc_ui-helper-templates-panel-search-js', vc_asset_url( 'js/editors/ui/vc_ui-helper-templates-panel-search.js' ), array(
			'jquery',
			'underscore',
			'backbone',
			'wpb_js_composer_editor_panels',
		), WPB_VC_VERSION, true );
		wp_register_script( 'vc_ui-helper-panel-view-resizable-js', vc_asset_url( 'js/editors/ui/vc_ui-helper-panel-view-resizable.js' ), array(
			'jquery',
			'underscore',
			'backbone',
			'wpb_js_composer_editor_panels',
			'vc_ui-tabs-line-js',
		), WPB_VC_VERSION, true );
		wp_register_script( 'vc_ui-helper-panel-view-draggable-js', vc_asset_url( 'js/editors/ui/vc_ui-helper-panel-view-draggable.js' ), array(
			'jquery',
			'underscore',
			'backbone',
			'wpb_js_composer_editor_panels',
			'vc_ui-tabs-line-js',
		), WPB_VC_VERSION, true );
		wp_register_script( 'vc_editors-templates-preview-js', vc_asset_url( 'js/editors/templates-preview.js' ), array(
			'jquery',
			'wpb_js_composer_js_listeners',
		), WPB_VC_VERSION, true );
		wp_register_script( 'vc_ui-panel-add-element-js', vc_asset_url( 'js/editors/ui/vc_ui-panel-add-element.js' ), array(
			'vc_ui-helper-panel-view-draggable-js',
			'vc_ui-helper-panel-view-header-footer-js',
			//'isotope',
		), WPB_VC_VERSION, true );
		wp_register_script( 'vc_ui-panel-edit-element-js', vc_asset_url( 'js/editors/ui/vc_ui-panel-edit-element.js' ), array(
			'vc_ui-helper-panel-view-resizable-js',
			'vc_ui-helper-panel-view-header-footer-js',
		), WPB_VC_VERSION, true );
		wp_register_script( 'vc_ui-panel-post-settings-js', vc_asset_url( 'js/editors/ui/vc_ui-panel-post-settings.js' ), array(
			'vc_ui-helper-panel-view-resizable-js',
			'vc_ui-helper-panel-view-header-footer-js',
		), WPB_VC_VERSION, true );
		wp_register_script( 'vc_ui-panel-row-layout-js', vc_asset_url( 'js/editors/ui/vc_ui-panel-row-layout.js' ), array(
			'vc_ui-helper-panel-view-resizable-js',
			'vc_ui-helper-panel-view-header-footer-js',
		), WPB_VC_VERSION, true );

		wp_register_script( 'vc_ui-panel-template-window-js', vc_asset_url( 'js/editors/ui/vc_ui-panel-template-window.js' ), array(
			'vc_ui-helper-panel-view-draggable-js',
			'vc_ui-helper-panel-view-header-footer-js',
			'vc_ui-helper-templates-panel-search-js',
		), WPB_VC_VERSION, true );

		wp_register_script( 'wpb_js_composer_js_storage', vc_asset_url( 'js/backend/composer-storage.js' ), array( 'wpb_js_composer_js_atts' ), WPB_VC_VERSION, true );
		wp_register_script( 'wpb_js_composer_js_models', vc_asset_url( 'js/backend/composer-models.js' ), array( 'wpb_js_composer_js_storage' ), WPB_VC_VERSION, true );

		wp_register_script( 'wpb_js_composer_js_view', vc_asset_url( 'js/backend/composer-view.js' ), array(
			'wpb_js_composer_js_tools',
			'vc_ui-panel-add-element-js',
			'vc_ui-panel-edit-element-js',
			'vc_ui-panel-post-settings-js',
			'vc_ui-panel-row-layout-js',
			'vc_ui-panel-template-window-js',
		), WPB_VC_VERSION, true );

		wp_register_script( 'wpb_js_composer_js_custom_views', vc_asset_url( 'js/backend/composer-custom-views.js' ), array( 'wpb_js_composer_js_view' ), WPB_VC_VERSION, true );
		wp_register_script( 'wpb_jscomposer_autosuggest_js', vc_asset_url( 'lib/autosuggest/jquery.autoSuggest.js' ), array( 'wpb_js_composer_js_view' ), WPB_VC_VERSION, true );
		wp_register_script( 'wpb_jscomposer_teaser_js', vc_asset_url( 'js/backend/composer-teaser.js' ), array(), WPB_VC_VERSION, true );
		if ( ! vc_is_as_theme() || ( vc_is_as_theme() && 'admin_settings_page' !== vc_mode() ) ) {
			wp_register_script( 'ace-editor', vc_asset_url( 'lib/bower/ace-builds/src-min-noconflict/ace.js' ), array( 'jquery' ), WPB_VC_VERSION, true );
		}
		/**
		 * vc_filter: vc_i18n_locale_composer_js_view - @since 4.4
		 */
		wp_localize_script( 'wpb_js_composer_js_view', 'i18nLocale', apply_filters( 'vc_i18n_locale_composer_js_view', array(
			'add_remove_picture' => __( 'Add/remove picture', 'js_composer' ),
			'finish_adding_text' => __( 'Finish Adding Images', 'js_composer' ),
			'add_image' => __( 'Add Image', 'js_composer' ),
			'add_images' => __( 'Add Images', 'js_composer' ),
			'settings' => __( 'Settings', 'js_composer' ),
			'main_button_title' => __( 'Visual Composer', 'js_composer' ),
			'main_button_title_backend_editor' => __( 'BACKEND EDITOR', 'js_composer' ),
			'main_button_title_frontend_editor' => __( 'FRONTEND EDITOR', 'js_composer' ),
			'main_button_title_revert' => __( 'CLASSIC MODE', 'js_composer' ),
			'please_enter_templates_name' => __( 'Enter template name you want to save.', 'js_composer' ),
			'confirm_deleting_template' => __( 'Confirm deleting "{template_name}" template, press Cancel to leave. This action cannot be undone.', 'js_composer' ),
			'press_ok_to_delete_section' => __( 'Press OK to delete section, Cancel to leave', 'js_composer' ),
			'drag_drop_me_in_column' => __( 'Drag and drop me in the column', 'js_composer' ),
			'press_ok_to_delete_tab' => __( 'Press OK to delete "{tab_name}" tab, Cancel to leave', 'js_composer' ),
			'slide' => __( 'Slide', 'js_composer' ),
			'tab' => __( 'Tab', 'js_composer' ),
			'section' => __( 'Section', 'js_composer' ),
			'please_enter_new_tab_title' => __( 'Please enter new tab title', 'js_composer' ),
			'press_ok_delete_section' => __( 'Press OK to delete "{tab_name}" section, Cancel to leave', 'js_composer' ),
			'section_default_title' => __( 'Section', 'js_composer' ),
			'please_enter_section_title' => __( 'Please enter new section title', 'js_composer' ),
			'error_please_try_again' => __( 'Error. Please try again.', 'js_composer' ),
			'if_close_data_lost' => __( 'If you close this window all shortcode settings will be lost. Close this window?', 'js_composer' ),
			'header_select_element_type' => __( 'Select element type', 'js_composer' ),
			'header_media_gallery' => __( 'Media gallery', 'js_composer' ),
			'header_element_settings' => __( 'Element settings', 'js_composer' ),
			'add_tab' => __( 'Add tab', 'js_composer' ),
			'are_you_sure_convert_to_new_version' => __( 'Are you sure you want to convert to new version?', 'js_composer' ),
			'loading' => __( 'Loading...', 'js_composer' ),
			// Media editor
			'set_image' => __( 'Set Image', 'js_composer' ),
			'are_you_sure_reset_css_classes' => __( 'Are you sure that you want to remove all your data?', 'js_composer' ),
			'loop_frame_title' => __( 'Loop settings', 'js_composer' ),
			'enter_custom_layout' => __( 'Custom row layout', 'js_composer' ),
			'wrong_cells_layout' => __( 'Wrong row layout format! Example: 1/2 + 1/2 or span6 + span6.', 'js_composer' ),
			'row_background_color' => __( 'Row background color', 'js_composer' ),
			'row_background_image' => __( 'Row background image', 'js_composer' ),
			'column_background_color' => __( 'Column background color', 'js_composer' ),
			'column_background_image' => __( 'Column background image', 'js_composer' ),
			'guides_on' => __( 'Guides ON', 'js_composer' ),
			'guides_off' => __( 'Guides OFF', 'js_composer' ),
			'template_save' => __( 'New template successfully saved.', 'js_composer' ),
			'template_added' => __( 'Template added to the page.', 'js_composer' ),
			'template_added_with_id' => __( 'Template added to the page. Template has ID attributes, make sure that they are not used more than once on the same page.', 'js_composer' ),
			'template_removed' => __( 'Template successfully removed.', 'js_composer' ),
			'template_is_empty' => __( 'Template is empty: There is no content to be saved as a template.', 'js_composer' ),
			'template_save_error' => __( 'Error while saving template.', 'js_composer' ),
			'css_updated' => __( 'Page settings updated!', 'js_composer' ),
			'update_all' => __( 'Update all', 'js_composer' ),
			'confirm_to_leave' => __( 'The changes you made will be lost if you navigate away from this page.', 'js_composer' ),
			'inline_element_saved' => __( '%s saved!', 'js_composer' ),
			'inline_element_deleted' => __( '%s deleted!', 'js_composer' ),
			'inline_element_cloned' => __( '%s cloned. <a href="#" class="vc_edit-cloned" data-model-id="%s">Edit now?</a>', 'js_composer' ),
			'gfonts_loading_google_font_failed' => __( 'Loading Google Font failed', 'js_composer' ),
			'gfonts_loading_google_font' => __( 'Loading Font...', 'js_composer' ),
			'gfonts_unable_to_load_google_fonts' => __( 'Unable to load Google Fonts', 'js_composer' ),
			'no_title_parenthesis' => sprintf( '(%s)', __( 'no title', 'js_composer' ) ),
			'error_while_saving_image_filtered' => __( 'Error while applying filter to the image. Check your server and memory settings.', 'js_composer' ),
			'ui_saved' => sprintf( '<i class="vc_ui-icon-pixel vc_ui-icon-pixel-check"></i> %s', __( 'Saved!', 'js_composer' ) ),
			'delete_preset_confirmation' => __( 'You are about to delete this preset. This action can not be undone.', 'js_composer' ),
		) ) );

		/**
		 * @since 4.4
		 */
		do_action( 'vc_base_register_admin_js' );

	}

	/**
	 * Register admin css styles.
	 *
	 * Calls wp_register_style for required css libraries files for admin dashboard.
	 *
	 * @since  3.1
	 * @access public
	 */
	public function registerAdminCss() {
		wp_register_style( 'ui-custom-theme', vc_asset_url( 'css/ui-custom-theme/jquery-ui-less.custom.css' ), false, WPB_VC_VERSION, false );
		wp_register_style( 'isotope-css', vc_asset_url( 'css/lib/isotope.css' ), false, WPB_VC_VERSION, 'screen' );
		wp_register_style( 'animate-css', vc_asset_url( 'lib/bower/animate-css/animate.min.css' ), false, WPB_VC_VERSION, 'screen' );
		wp_register_style( 'font-awesome', vc_asset_url( 'lib/bower/font-awesome/css/font-awesome.min.css' ), false, WPB_VC_VERSION, 'screen' );
		$backend_default_css = 'css/js_composer_backend_editor.min.css';
		wp_register_style( 'js_composer', vc_asset_url( $backend_default_css ), array(
			'isotope-css',
			'animate-css',
		), WPB_VC_VERSION, false );

		wp_register_style( 'wpb_jscomposer_autosuggest', vc_asset_url( 'lib/autosuggest/jquery.autoSuggest.css' ), false, WPB_VC_VERSION, false );

		/**
		 * @since 4.4
		 */
		do_action( 'vc_base_register_admin_css' );
	}

	/**
	 * Add Settings link in plugin's page
	 * @since 4.2
	 *
	 * @param $links
	 * @param $file
	 *
	 * @return array
	 */
	public function pluginActionLinks( $links, $file ) {
		if ( plugin_basename( vc_path_dir( 'APP_DIR', '/js_composer.php' ) ) == $file ) {
			$title = __( 'Visual Composer Settings', 'js_composer' );
			$html = esc_html__( 'Settings', 'js_composer' );
			if ( ! vc_user_access()
				->part( 'settings' )
				->can( 'vc-general-tab' )
				->get()
			) {
				$title = __( 'About Visual Composer', 'js_composer' );
				$html = esc_html__( 'About', 'js_composer' );
			}
			$link = '<a title="' . esc_attr( $title ) . '" href="' . esc_url( $this->getSettingsPageLink() ) . '">' . $html . '</a>';
			array_unshift( $links, $link ); // Add to top
		}

		return $links;
	}

	/**
	 * Get settings page link
	 * @since 4.2
	 * @return string url to settings page
	 */
	public function getSettingsPageLink() {
		$page = 'vc-general';
		if ( ! vc_user_access()
			->part( 'settings' )
			->can( 'vc-general-tab' )
			->get()
		) {
			$page = 'vc-welcome';
		}
		return add_query_arg( array( 'page' => $page ), admin_url( 'admin.php' ) );
	}

	/**
	 * Hooked class method by wp_head WP action.
	 * Also add fix for IE8 bootstrap styles from WPExplorer
	 * @since  4.2
	 * @access public
	 */
	public function addMetaData() {
		echo '<meta name="generator" content="Powered by Visual Composer - drag and drop page builder for WordPress."/>' . "\n";
		// Add IE8 compatibility from WPExplorer: https://github.com/wpexplorer/visual-composer-ie8
		echo '<!--[if lte IE 9]><link rel="stylesheet" type="text/css" href="' . vc_asset_url( 'css/vc_lte_ie9.min.css' ) . '" media="screen"><![endif]-->';
		echo '<!--[if IE  8]><link rel="stylesheet" type="text/css" href="' . vc_asset_url( 'css/vc-ie8.min.css' ) . '" media="screen"><![endif]-->';
	}

	/**
	 * Method adds css class to body tag.
	 *
	 * Hooked class method by body_class WP filter. Method adds custom css class to body tag of the page to help
	 * identify and build design specially for VC shortcodes.
	 *
	 * @since  4.2
	 * @access public
	 *
	 * @param $classes
	 *
	 * @return array
	 */
	public function bodyClass( $classes ) {
		return js_composer_body_class( $classes );
	}

	/**
	 * Builds excerpt for post from content.
	 *
	 * Hooked class method by the_excerpt WP filter. When user creates content with VC all content is always wrapped by
	 * shortcodes. This methods calls do_shortcode for post's content and then creates a new excerpt.
	 *
	 * @since  4.2
	 * @access public
	 *
	 * @param $output
	 *
	 * @return string
	 */
	public function excerptFilter( $output ) {
		global $post;
		if ( empty( $output ) && ! empty( $post->post_content ) ) {
			$text = strip_tags( do_shortcode( $post->post_content ) );
			$excerpt_length = apply_filters( 'excerpt_length', 55 );
			$excerpt_more = apply_filters( 'excerpt_more', ' ' . '[...]' );
			$text = wp_trim_words( $text, $excerpt_length, $excerpt_more );

			return $text;
		}

		return $output;
	}

	/**
	 * Remove unwanted wraping with p for content.
	 *
	 * Hooked by 'the_content' filter.
	 * @since 4.2
	 *
	 * @param null $content
	 *
	 * @return string|null
	 */
	public function fixPContent( $content = null ) {
		if ( $content ) {
			$s = array(
				'/' . preg_quote( '</div>', '/' ) . '[\s\n\f]*' . preg_quote( '</p>', '/' ) . '/i',
				'/' . preg_quote( '<p>', '/' ) . '[\s\n\f]*' . preg_quote( '<div ', '/' ) . '/i',
				'/' . preg_quote( '<p>', '/' ) . '[\s\n\f]*' . preg_quote( '<section ', '/' ) . '/i',
				'/' . preg_quote( '</section>', '/' ) . '[\s\n\f]*' . preg_quote( '</p>', '/' ) . '/i',
			);
			$r = array( '</div>', '<div ', '<section ', '</section>' );
			$content = preg_replace( $s, $r, $content );

			return $content;
		}

		return null;
	}

	/**
	 * @todo remove this (comment added on 4.8) also remove helpers
	 * Set manger for custom third-party plugins.
	 * @deprecated due to autoload logic 4.4
	 * @since 4.3
	 *
	 * @param Vc_Vendors_Manager $vendor_manager
	 */
	public function setVendorsManager( Vc_Vendors_Manager $vendor_manager ) {
		_deprecated_function( 'Vc_Base::setVendorsManager', '4.4', 'autoload logic' );

		$this->vendor_manager = $vendor_manager;
	}

	/**
	 * @todo remove this (comment added on 4.8) also remove helpers
	 * Get vendors manager.
	 * @deprecated due to autoload logic from 4.4
	 * @since 4.3
	 * @return bool|Vc_Vendors_Manager
	 */
	public function vendorsManager() {
		_deprecated_function( 'Vc_Base::vendorsManager', '4.4', 'autoload logic' );

		return $this->vendor_manager;
	}
}

/**
 * @todo remove this (comment added on 4.8) also remove helpers
 * VC backward capability.
 * @deprecated @since 4.3
 */
class WPBakeryVisualComposer extends Vc_Base {

	/**
	 * @deprecated since 4.3
	 */
	function __construct() {
		_deprecated_function( 'WPBakeryVisualComposer class', '4.3', 'Vc_Base class' );
	}

	/**
	 * @param $template
	 *
	 * @todo remove this (comment added on 4.8) also remove helpers
	 * @deprecated 4.3
	 * @return string
	 */
	public static function getUserTemplate( $template ) {
		_deprecated_function( 'WPBakeryVisualComposer getUserTemplate', '4.3', 'Vc_Base getShortcodesTemplateDir' );

		return vc_manager()->getShortcodesTemplateDir( $template );
	}
}
