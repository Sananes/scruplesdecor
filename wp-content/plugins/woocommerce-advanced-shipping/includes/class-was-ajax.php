<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Class WAS_Ajax.
 *
 * Initialize the AJAX class.
 *
 * @class		WAS_Ajax
 * @author		Jeroen Sormani
 * @package		WooCommerce Advanced Shipping
 * @version		1.0.0
 */
class WAS_Ajax {


	/**
	 * Constructor.
	 *
	 * Add ajax actions in order to work.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		// Add elements
		add_action( 'wp_ajax_was_add_condition', array( $this, 'was_add_condition' ) );
		add_action( 'wp_ajax_was_add_condition_group', array( $this, 'was_add_condition_group' ) );

		// Update elements
		add_action( 'wp_ajax_was_update_condition_value', array( $this, 'was_update_condition_value' ) );
		add_action( 'wp_ajax_was_update_condition_description', array( $this, 'was_update_condition_description' ) );

		// Save shipping method ordering
		add_action( 'wp_ajax_save_method_order', array( $this, 'ajax_save_method_order' ) );

	}


	/**
	 * Add condition.
	 *
	 * Create a new WAS_Condition class and render.
	 *
	 * @since 1.0.0
	 */
	public function was_add_condition() {

		new WAS_Condition( null, $_POST['group'] );
		die();

	}


	/**
	 * Condition group.
	 *
	 * Render new condition group.
	 *
	 * @since 1.0.0
	 */
	public function was_add_condition_group() {

		?><div class='condition-group condition-group-<?php echo $_POST['group']; ?>' data-group='<?php echo $_POST['group']; ?>'>

			<p class='or-match'><?php _e( 'Or match all of the following rules to allow this shipping method:', 'woocommerce-advanced-shipping' );?></p><?php

			new was_Condition( null, $_POST['group'] );

		?></div>

		<p class='or-text'><strong><?php _e( 'Or', 'woocommerce-advanced-shipping' ); ?></strong></p><?php

		die();

	}


	/**
	 * Update values.
	 *
	 * Retreive and render the new condition values according to the condition key.
	 *
	 * @since 1.0.0
	 */
	public function was_update_condition_value() {

		was_condition_values( $_POST['id'], $_POST['group'], $_POST['condition'] );
		die();

	}


	/**
	 * Update description.
	 *
	 * Render the corresponding description for the condition key.
	 *
	 * @since 1.0.0
	 */
	public function was_update_condition_description() {

		was_condition_description( $_POST['condition'] );
		die();

	}


	/**
	 * Save order.
	 *
	 * Save the shipping method order.
	 *
	 * @since 1.0.4
	 */
	public function ajax_save_method_order() {

		global $wpdb;

		$args = wp_parse_args( $_POST['form'] );

		$menu_order = 0;
		foreach ( $args['sort'] as $sort ) :

			$wpdb->update(
				$wpdb->posts,
				array(
					'menu_order' => $menu_order
				),
				array( 'ID' => $sort ),
				array( '%d' ),
				array( '%d' )
			);

			$menu_order++;

		endforeach;

		die;

	}


}
