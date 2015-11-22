<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Class WAS_Post_Type.
 *
 * Initialize the was post type.
 *
 * @class		WAS_post_type
 * @author		Jeroen Sormani
 * @package		WooCommerce Advanced Shipping
 * @version		1.0.0
 */
class WAS_Post_Type {


	/**
	 * Constructor.
 	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		// Register post type
		add_action( 'init', array( $this, 'was_register_post_type' ) );

		// Add/save meta boxes
		add_action( 'add_meta_boxes', array( $this, 'was_post_type_meta_box' ) );
		add_action( 'save_post', array( $this, 'was_save_meta' ) );
		add_action( 'save_post', array( $this, 'was_save_condition_meta' ) );

		// Edit user notices
		add_filter( 'post_updated_messages', array( $this, 'was_custom_post_type_messages' ) );

		// Redirect after delete
		add_action('load-edit.php', array( $this, 'was_redirect_after_trash' ) );

	}


	/**
	 * Post type.
	 *
	 * Register the 'was' post type.
	 *
	 * @since 1.0.0
	 */
	public function was_register_post_type() {

		$labels = array(
			'name' 					=> __( 'Advanced Shipping methods', 'woocommerce-advanced-shipping' ),
			'singular_name' 		=> __( 'Advanced Shipping method', 'woocommerce-advanced-shipping' ),
			'add_new' 				=> __( 'Add New', 'woocommerce-advanced-shipping' ),
			'add_new_item' 			=> __( 'Add New Advanced Shipping method', 'woocommerce-advanced-shipping' ),
			'edit_item' 			=> __( 'Edit Advanced Shipping method', 'woocommerce-advanced-shipping' ),
			'new_item' 				=> __( 'New Advanced Shipping method', 'woocommerce-advanced-shipping' ),
			'view_item' 			=> __( 'View Advanced Shipping method', 'woocommerce-advanced-shipping' ),
			'search_items' 			=> __( 'Search Advanced Shipping methods', 'woocommerce-advanced-shipping' ),
			'not_found' 			=> __( 'No Advanced Shipping methods', 'woocommerce-advanced-shipping' ),
			'not_found_in_trash'	=> __( 'No Advanced Shipping methods found in Trash', 'woocommerce-advanced-shipping' ),
		);

		register_post_type( 'was', array(
			'label' 				=> 'was',
			'show_ui' 				=> true,
			'show_in_menu' 			=> false,
			'capability_type' 		=> 'post',
			'map_meta_cap' 			=> true,
			'rewrite' 				=> array( 'slug' => 'was', 'with_front' => true ),
			'_builtin' 				=> false,
			'query_var' 			=> true,
			'supports' 				=> array( 'title' ),
			'labels' 				=> $labels,
		) );

	}


	/**
	 * Messages.
	 *
	 * Modify the notice messages text for the 'was' post type.
	 *
	 * @since 1.0.0
	 *
	 * @param 	array $messages Existing list of messages.
	 * @return 	array			Modified list of messages.
	 */
	function was_custom_post_type_messages( $messages ) {

		$post 				= get_post();
		$post_type			= get_post_type( $post );
		$post_type_object	= get_post_type_object( $post_type );

		$messages['was'] = array(
			0 => '',
			1 => __( 'Advanced shipping method updated.', 'woocommerce-advanced-shipping' ),
			2 => __( 'Custom field updated.', 'woocommerce-advanced-shipping' ),
			3 => __( 'Custom field deleted.', 'woocommerce-advanced-shipping' ),
			4 => __( 'Advanced shipping method updated.', 'woocommerce-advanced-shipping' ),
			5 => isset( $_GET['revision'] ) ?
				sprintf( __( 'Advanced shipping method restored to revision from %s', 'woocommerce-advanced-shipping' ), wp_post_revision_title( (int) $_GET['revision'], false ) )
				: false,
			6 => __( 'Advanced shipping method published.', 'woocommerce-advanced-shipping' ),
			7 => __( 'Advanced shipping method saved.', 'woocommerce-advanced-shipping' ),
			8 => __( 'Advanced shipping method submitted.', 'woocommerce-advanced-shipping' ),
			9 => sprintf(
				__( 'Advanced shipping method scheduled for: <strong>%1$s</strong>.', 'woocommerce-advanced-shipping' ),
				date_i18n( __( 'M j, Y @ G:i', 'woocommerce-advanced-shipping' ), strtotime( $post->post_date ) )
			),
			10 => __( 'Advanced shipping method draft updated.', 'woocommerce-advanced-shipping' ),
		);

		$permalink = admin_url( '/admin.php?page=wc-settings&tab=shipping&section=was_advanced_shipping_method' );
		$overview_link = sprintf( ' <a href="%s">%s</a>', esc_url( $permalink ), __( 'Return to overview.', 'woocommerce-advanced-shipping' ) );
		$messages['was'][1] .= $overview_link;
		$messages['was'][6] .= $overview_link;
		$messages['was'][9] .= $overview_link;
		$messages['was'][8] .= $overview_link;
		$messages['was'][10] .= $overview_link;

		return $messages;

	}


	/**
	 * Meta boxes.
	 *
	 * Add two meta boxes to the 'was' post type.
	 *
	 * @since 1.0.0
	 */
	public function was_post_type_meta_box() {

		add_meta_box( 'was_conditions', __( 'Advanced Shipping conditions', 'woocommerce-advanced-shipping' ), array( $this, 'render_was_conditions' ), 'was', 'normal' );
		add_meta_box( 'was_settings', __( 'Shipping settings', 'woocommerce-advanced-shipping' ), array( $this, 'render_was_settings' ), 'was', 'normal' );

	}


	/**
	 * Render meta box.
	 *
	 * Get conditions meta box contents.
	 *
	 * @since 1.0.0
	 */
	public function render_was_conditions() {

		/**
		 * Load meta box conditions view
		 */
		require_once plugin_dir_path( __FILE__ ) . 'admin/settings/meta-box-conditions.php';

	}


	/**
	 * Render meta box.
	 *
	 * Get settings meta box contents.
	 *
	 * @since 1.0.0
	 */
	public function render_was_settings() {

		/**
		 * Load meta box settings view
		 */
		require_once plugin_dir_path( __FILE__ ) . 'admin/settings/meta-box-settings.php';

	}


	/**
	 * Save meta.
	 *
	 * Validate and save post meta. This value contains all
	 * the normal shipping method settings (no conditions).
	 *
	 * @since 1.0.0
	 *
	 * @param int/numberic $post_id ID of the post being saved.
	 */
	public function was_save_meta( $post_id ) {

		if ( !isset( $_POST['was_settings_meta_box_nonce'] ) )
			return $post_id;

		$nonce = $_POST['was_settings_meta_box_nonce'];

		if ( ! wp_verify_nonce( $nonce, 'was_settings_meta_box' ) )
			return $post_id;

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
			return $post_id;

		if ( ! current_user_can( 'manage_woocommerce' ) )
			return $post_id;

		$shipping_method = $_POST['_was_shipping_method'];
		$shipping_method['shipping_title'] 	= sanitize_text_field( $shipping_method['shipping_title'] );
		$shipping_method['shipping_cost'] 	= wc_format_decimal( $shipping_method['shipping_cost'] );
		$shipping_method['handling_fee'] 	= wc_format_decimal( $shipping_method['handling_fee'] );
		$shipping_method['cost_per_weight'] = wc_format_decimal( $shipping_method['cost_per_weight'] );
		$shipping_method['cost_per_item'] 	= wc_format_decimal( $shipping_method['cost_per_item'] );
		$shipping_method['tax'] 			= 'taxable' == $shipping_method['tax'] ? 'taxable' : 'not_taxable';

		update_post_meta( $post_id, '_was_shipping_method', $shipping_method );

		do_action( 'was_save_shipping_settings', $post_id );

	}


	/**
	 * Save meta.
	 *
	 * Validate and save condition meta box conditions meta. This
	 * value contains all the shipping method conditions.
	 *
	 * @since 1.0.0
	 *
	 * @param int/numberic $post_id ID of the post being saved.
	 */
	public function was_save_condition_meta( $post_id ) {

		if ( ! isset( $_POST['was_conditions_meta_box_nonce'] ) ) :
			return $post_id;
		endif;

		$nonce = $_POST['was_conditions_meta_box_nonce'];

		if ( ! wp_verify_nonce( $nonce, 'was_conditions_meta_box' ) ) :
			return $post_id;
		endif;

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) :
			return $post_id;
		endif;

		if ( ! current_user_can( 'manage_woocommerce' ) ) :
			return $post_id;
		endif;

		$shipping_method_conditions = $_POST['_was_shipping_method_conditions'];

		update_post_meta( $post_id, '_was_shipping_method_conditions', $shipping_method_conditions );

		do_action( 'was_save_shipping_conditions', $post_id );

	}


	/**
	 * Redirect trash.
	 *
	 * Redirect user after trashing a WAS post.
	 *
	 * @since 1.0.0
	 */
	public function was_redirect_after_trash() {

		$screen = get_current_screen();

		if ( 'edit-was' == $screen->id ) :

			if ( isset( $_GET['trashed'] ) && intval( $_GET['trashed'] ) > 0 ) :

				$redirect = admin_url( '/admin.php?page=wc-settings&tab=shipping&section=was_advanced_shipping_method' );
				wp_redirect( $redirect );
				exit();

			endif;

		endif;


	}

}

/**
 * Load condition object
 */
require_once plugin_dir_path( __FILE__ ) . 'admin/settings/conditions/class-was-condition.php';
