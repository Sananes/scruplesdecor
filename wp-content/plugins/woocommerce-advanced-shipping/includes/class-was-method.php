<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( class_exists( 'WAS_Advanced_Shipping_Method' ) ) return; // Stop if the class already exists

/**
 * Class WAS_Advanced_Shipping_Method.
 *
 * WooCommerce Advanced Shipping method class.
 *
 * @class		WAS_Advanced_Shipping_Method
 * @author		Jeroen Sormani
 * @package		WooCommerce Advanced Shipping
 * @version		1.0.0
 */
class WAS_Advanced_Shipping_Method extends WC_Shipping_Method {


	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		$this->id					= 'advanced_shipping';
		$this->title				= __( 'Shipping <small>(may change at user configuration)</small>', 'woocommerce-advanced-shipping' );
		$this->method_title			= __( 'Advanced Shipping', 'woocommerce-advanced-shipping' );
		$this->method_description 	= __( 'Configure WooCommerce Advanced Shipping', 'woocommerce-advanced-shipping' );

		$this->init();

		do_action( 'woocommerce_advanced_shipping_method_init' );

	}


	/**
	 * Init.
	 *
	 * Initialize WAS shipping method.
	 *
	 * @since 1.0.0
	 */
	function init() {

		$this->init_form_fields();
		$this->init_settings();

		$this->enabled 			= $this->get_option( 'enabled' );
		$this->hide_shipping 	= $this->get_option( 'hide_other_shipping_when_available' );

		// Save settings in admin if you have any defined
		add_action( 'woocommerce_update_options_shipping_' . $this->id, array( $this, 'process_admin_options' ) );

		// Hide shipping methods
		if ( version_compare( WC()->version, '2.1', '<' ) ) :
			add_filter( 'woocommerce_available_shipping_methods', array( $this, 'hide_all_shipping_when_free_is_available' ) );
		else :
			add_filter( 'woocommerce_package_rates', array( $this, 'hide_all_shipping_when_free_is_available' ) );
		endif;

	}


	/**
	 * Match methods.
	 *
	 * Checks all created WAS shipping methods have a matching condition group.
	 *
	 * @since 1.0.0
	 *
	 * @param 	array	$package	List of shipping package data.
	 * @return 	array 				List of all matched shipping methods.
	 */
	public function was_match_methods( $package ) {

		$matched_methods = '';
		$methods = get_posts( array( 'posts_per_page' => '-1', 'post_type' => 'was', 'orderby' => 'menu_order', 'order' => 'ASC' ) );

		foreach ( $methods as $method ) :

			$condition_groups = get_post_meta( $method->ID, '_was_shipping_method_conditions', true );

			// Check if method conditions match
			$match = $this->was_match_conditions( $condition_groups, $package );

			// Add match to array
			if ( true == $match ) :
				$matched_methods[] = $method->ID;
			endif;

		endforeach;

		return $matched_methods;

	}


	/**
	 * Match conditions.
	 *
	 * Check if conditions match, if all conditions in one condition group
	 * matches it will return TRUE and the shipping method will display.
	 *
	 * @since 1.0.0
	 *
	 * @param 	array 	$conditiong_groups 	List of condition groups containing their conditions.
	 * @param 	array	$package			List of shipping package data.
	 * @return 	BOOL 						TRUE if all the conditions in one of the condition groups matches true.
	 */
	public function was_match_conditions( $condition_groups = array(), $package = array() ) {

		if ( empty( $condition_groups ) ) return false;

		foreach ( $condition_groups as $condition_group => $conditions ) :

			$match_condition_group = true;

			foreach ( $conditions as $condition ) :

				$condition 	= apply_filters( 'was_match_condition_values', $condition );
				$match 		= apply_filters( 'was_match_condition_' . $condition['condition'], false, $condition['operator'], $condition['value'], $package );

				if ( false == $match ) :
					$match_condition_group = false;
				endif;

			endforeach;

			// return true if one condition group matches
			if ( true == $match_condition_group ) :
				return true;
			endif;

		endforeach;

		return false;

	}


	/**
	 * Init fields.
	 *
	 * Add fields to the WAS shipping settings page.
	 *
	 * @since 1.0.0
	 */
	public function init_form_fields() {

		$this->form_fields = array(
			'enabled' => array(
				'title' 		=> __( 'Enable/Disable', 'woocommerce' ),
				'type' 			=> 'checkbox',
				'label' 		=> __( 'Enable Advanced Shipping', 'woocommerce-advanced-shipping' ),
				'default' 		=> 'yes'
			),
			'hide_other_shipping_when_available' => array(
				'title' 		=> __( 'Hide other shipping', 'woocommerce-advanced-shipping' ),
				'type' 			=> 'checkbox',
				'label' 		=> __( 'Hide other shipping methods when free shipping is available', 'woocommerce-advanced-shipping' ),
				'default' 		=> 'no'
			),
			'conditions' => array(
				'type' 			=> 'conditions_table',
			),
		);


	}


	/**
	 * Settings tab table.
	 *
	 * Load and render the table on the Advanced Shipping settings tab.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function generate_conditions_table_html() {

		ob_start();

			/**
			 * Load conditions table file
			 */
			require plugin_dir_path( __FILE__ ) . 'admin/views/conditions-table.php';

		return ob_get_clean();

	}


	/**
	 * Validate table.
	 *
	 * Condition table does not need validation, so always return false.
	 *
	 * @since 1.0.0
	 *
	 * @param 	mixed $key 	Key.
	 * @return 	bool		Validation.
	 */
	public function validate_additional_conditions_table_field( $key ) {
		return false;
	}


	/**
	 * Item cost.
	 *
	 * Calculate the costs per item.
	 *
	 * @since 1.0.0
	 *
	 * @param 	mixed $package 	List containing all products for this method.
	 * @return 	float			Shipping costs.
	 */
	public function calculate_cost_per_item( $package ) {

		$cost = '';

		// Shipping per item
		foreach ( $package['contents'] as $item_id => $values ) :

			$_product = $values['data'];

			if ( $values['quantity'] > 0 && $_product->needs_shipping() ) :

				if ( strstr( $this->cost_per_item, '%' ) ) :
					$cost += ( $values['line_total'] / 100 ) * str_replace( '%', '', $this->cost_per_item );
				else :
					$cost += $values['quantity'] * $this->cost_per_item;
				endif;

			endif;

		endforeach;

		return $cost;

	}


	/**
	 * Weight cost.
	 *
	 * Calculate the costs per weight.
	 *
	 * @since 1.0.0
	 *
	 * @param 	mixed $package 	List containing all products for this method.
	 * @return 	float			Shipping costs.
	 */
	public function calculate_cost_per_weight( $package ) {

		$cost = '';

		// Weight per item
		foreach ( $package['contents'] as $item_id => $values ) :

			$_product = $values['data'];

			if ( $values['quantity'] > 0 && $_product->needs_shipping() && $_product->get_weight() ) :

				$cost += ( ( $values['quantity'] * $_product->get_weight() ) * $this->cost_per_weight );

			endif;

		endforeach;

		return $cost;

	}


	/**
	 * Calculate costs.
	 *
	 * Calculate the shipping costs for this method.
	 *
	 * @since 1.0.0
	 *
	 * @param	mixed $package 	List containing all products for this method.
	 * @return 	float 			Shipping costs.
	 */
	public function calculate_shipping_cost( $package, $method_id ) {

		$cost = $this->cost;
		$cost += $this->get_fee( $this->fee, $package['contents_cost'] );
		$cost += $this->calculate_cost_per_item( $package );
		$cost += $this->calculate_cost_per_weight( $package );

		return apply_filters( 'was_calculate_shipping_costs', $cost, $package, $method_id, $this );

	}


	/**
	 * Calculate shipping.
	 *
	 * Calculate the shipping and set settings.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed $package List containing all products for this method.
	 */
	public function calculate_shipping( $package ) {

		$this->matched_methods	= $this->was_match_methods( $package );

		if ( false == $this->matched_methods || ! is_array( $this->matched_methods ) || 'no' == $this->enabled ) return;

		foreach ( $this->matched_methods as $method_id ) :

			$match_details 			= get_post_meta( $method_id, '_was_shipping_method', true );
			@$label 				= $match_details['shipping_title'];
			@$this->fee 			= $match_details['handling_fee'];
			@$this->cost			= $match_details['shipping_cost'];
			@$this->cost_per_item	= $match_details['cost_per_item'];
			@$this->cost_per_weight	= $match_details['cost_per_weight'];
			@$this->taxable			= $match_details['tax'];
			$this->shipping_costs 	= $this->calculate_shipping_cost( $package, $method_id );

			$rate = apply_filters( 'was_shipping_rate', array(
				'id'		=> $method_id,
				'label'		=> ( null == $label ) ? __( 'Shipping', 'woocommerce-advanced-shipping' ) : $label,
				'cost'		=> $this->shipping_costs,
				'taxes'		=> ( 'taxable' == $this->taxable ) ? '' : false,
				'calc_tax'	=> 'per_order',
			), $package, $this );

			$this->add_rate( $rate );

		endforeach;

	}


	/**
	 * Hide shipping.
	 *
	 * Hide Shipping methods when regular or
	 * advanced shipping free shipping is available.
	 *
	 * @since 1.0.0
	 * @since 1.0.7 - Show all free shipping rates
	 *
	 * @param array $available_methods
	 * @return array
	 */
	public function hide_all_shipping_when_free_is_available( $available_methods ) {

		if ( 'no' == $this->hide_shipping ) :
			return $available_methods;
		endif;

		foreach ( $available_methods as $key => $method ) :

			if ( 0 != $method->cost ) :
				unset( $available_methods[ $key ] );
			endif;

		endforeach;

		return $available_methods;

	}


}
