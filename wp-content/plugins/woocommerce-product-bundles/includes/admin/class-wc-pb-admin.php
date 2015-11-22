<?php
/**
 * Product Bundles Admin Class.
 *
 * Loads admin tabs and adds related hooks / filters.
 *
 * @class WC_PB_Admin
 * @version 4.11.4
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WC_PB_Admin {

	/**
	 * Setup admin class
	 */
	public function __construct() {

		// Admin jquery
		add_action( 'admin_enqueue_scripts', array( $this, 'woo_bundles_admin_scripts' ), 11 );

		// Creates the admin panel tab 'Bundled Products'
		add_action( 'woocommerce_product_write_panel_tabs', array( $this, 'woo_bundles_product_write_panel_tab' ) );

		// Creates the panel for selecting bundled product options
		add_action( 'woocommerce_product_write_panels', array( $this, 'woo_bundles_product_write_panel' ) );
		add_action( 'woocommerce_product_options_stock', array( $this, 'woo_bundles_stock_group' ) );

		add_filter( 'product_type_options', array( $this, 'woo_bundles_type_options' ) );

		// Processes and saves the necessary post metas from the selections made above
		add_action( 'woocommerce_process_product_meta_bundle', array( $this, 'woo_bundles_process_bundle_meta' ) );

		// Allows the selection of the 'bundled product' type
		add_filter( 'product_type_selector', array( $this, 'woo_bundles_product_selector_filter' ) );

		// Template override scan path
		add_filter( 'woocommerce_template_overrides_scan_paths', array( $this, 'woo_bundles_template_scan_path' ) );

		// Ajax add bundled product
		add_action( 'wp_ajax_woocommerce_add_bundled_product', array( $this, 'ajax_add_bundled_product' ) );

		// Bundled product config options
		add_action( 'woocommerce_bundled_product_admin_config_html', array( $this, 'bundled_product_admin_config_html' ), 10, 4 );
	}

	/**
	 * Add bundled product config options.
	 *
	 * @param  int   $loop
	 * @param  int   $product_id
	 * @param  array $item_data
	 * @param  int   $post_id
	 * @return void
	 */
	function bundled_product_admin_config_html( $loop, $product_id, $item_data, $post_id ) {

		$bundled_product = wc_get_product( $product_id );

		if ( $bundled_product->product_type == 'variable' ) {

			$allowed_variations = isset( $item_data[ 'allowed_variations' ] ) ? $item_data[ 'allowed_variations' ] : '';
			$default_attributes = isset( $item_data[ 'bundle_defaults' ] ) ? $item_data[ 'bundle_defaults' ] : '';

			$filter_variations = isset( $item_data[ 'filter_variations' ] ) ? $item_data[ 'filter_variations' ] : '';
			$override_defaults = isset( $item_data[ 'override_defaults' ] ) ? $item_data[ 'override_defaults' ] : '';

			?><div class="filtering">
				<div class="form-field filter_variations">
					<label for="filter_variations">
						<?php echo __( 'Filter Variations', 'woocommerce-product-bundles' ); ?>
					</label>
					<input type="checkbox" class="checkbox"<?php echo ( $filter_variations == 'yes' ? ' checked="checked"' : '' ); ?> name="bundle_data[<?php echo $loop; ?>][filter_variations]" <?php echo ( $filter_variations == 'yes' ? 'value="1"' : '' ); ?>/>
					<img class="help_tip" data-tip="<?php echo __( 'Check to enable only a subset of the available variations.', 'woocommerce-product-bundles' ); ?>" src="<?php echo WC()->plugin_url(); ?>/assets/images/help.png" />
				</div>
			</div>


			<div class="bundle_variation_filters">
				<div class="form-field">
					<select multiple="multiple" name="bundle_data[<?php echo $loop; ?>][allowed_variations][]" style="width: 95%;" data-placeholder="<?php _e( 'Choose variations&hellip;', 'woocommerce-product-bundles' ); ?>" class="<?php echo WC_PB_Core_Compatibility::is_wc_version_gte_2_3() ? 'wc-enhanced-select' : 'chosen_select'; ?>" > <?php

					$variations = WC_PB()->helpers->get_product_variations( $product_id );
					$attributes = maybe_unserialize( get_post_meta( $product_id, '_product_attributes', true ) );

					// filtered variation attributes
					$filtered_attributes = array();

					foreach ( $variations as $variation ) {

						$description    = '';
						$variation_data = array();

						// sweep the post meta for attributes
						if ( WC_PB_Core_Compatibility::is_wc_version_gte_2_4() ) {
							$variation_data = wc_get_product_variation_attributes( $variation );
						} else {
							$post_meta = get_post_meta( $variation );

							foreach ( $post_meta as $field => $value ) {

								if ( ! strstr( $field, 'attribute_' ) ) {
									continue;
								}

								$variation_data[ $field ] = $value[0];
							}
						}

						foreach ( $attributes as $attribute ) {

							// Only deal with attributes that are variations
							if ( ! $attribute[ 'is_variation' ] ) {
								continue;
							}

							// Get current value for variation (if set)
							$variation_selected_value = isset( $variation_data[ 'attribute_' . sanitize_title( $attribute[ 'name' ] ) ] ) ? $variation_data[ 'attribute_' . sanitize_title( $attribute[ 'name' ] ) ] : '';

							// Name will be something like attribute_pa_color
							$description_name  = esc_html( wc_attribute_label( $attribute[ 'name' ] ) );
							$description_value = __( 'Any', 'woocommerce' ) . ' ' . $description_name;

							// Get terms for attribute taxonomy or value if its a custom attribute
							if ( $attribute[ 'is_taxonomy' ] ) {

								$post_terms = wp_get_post_terms( $product_id, $attribute[ 'name' ] );

								foreach ( $post_terms as $term ) {

									if ( $variation_selected_value == $term->slug ) {
										$description_value = apply_filters( 'woocommerce_variation_option_name', esc_html( $term->name ) );
									}

									if ( $variation_selected_value == $term->slug || $variation_selected_value == '' ) {
										if ( $filter_variations == 'yes' && is_array( $allowed_variations ) && in_array( $variation, $allowed_variations ) ) {
											if ( ! isset( $filtered_attributes[ $attribute[ 'name' ] ] ) ) {
												$filtered_attributes[ $attribute[ 'name' ] ][] = $variation_selected_value;
											} elseif ( ! in_array( $variation_selected_value, $filtered_attributes[ $attribute[ 'name' ] ] ) ) {
												$filtered_attributes[ $attribute[ 'name' ] ][] = $variation_selected_value;
											}
										}
									}

								}

							} else {

								$options = array_map( 'trim', explode( WC_DELIMITER, $attribute[ 'value' ] ) );

								foreach ( $options as $option ) {
									if ( sanitize_title( $variation_selected_value ) == sanitize_title( $option ) ) {
										$description_value = esc_html( apply_filters( 'woocommerce_variation_option_name', $option ) );
									}

									if ( sanitize_title( $variation_selected_value ) == sanitize_title( $option ) || $variation_selected_value == '' ) {
										if ( $filter_variations == 'yes' && is_array( $allowed_variations ) && in_array( $variation, $allowed_variations ) ) {
											if ( ! isset( $filtered_attributes[ $attribute[ 'name' ] ] ) ) {
												$filtered_attributes[ $attribute[ 'name' ] ][] = sanitize_title( $variation_selected_value );
											} elseif ( ! in_array( sanitize_title( $variation_selected_value ), $filtered_attributes[ $attribute[ 'name' ] ] ) ) {
												$filtered_attributes[ $attribute[ 'name' ] ][] = sanitize_title( $variation_selected_value );
											}
										}
									}

								}

							}

							$description .= $description_name . ': ' . $description_value . ', ';
						}

						if ( is_array( $allowed_variations ) && in_array( $variation, $allowed_variations ) ) {
							$selected = 'selected="selected"';
						} else {
							$selected = '';
						}

						echo '<option value="' . $variation . '" ' . $selected . '>#' . $variation . ' - ' . rtrim( $description, ', ') . '</option>';
					}

					?></select>
				</div>
			</div>

			<div class="defaults">
				<div class="form-field override_defaults">
					<label for="override_defaults"><?php echo __( 'Override Default Selections', 'woocommerce-product-bundles' ) ?></label>
					<input type="checkbox" class="checkbox"<?php echo ( $override_defaults == 'yes' ? ' checked="checked"' : '' ); ?> name="bundle_data[<?php echo $loop; ?>][override_defaults]" <?php echo ( $override_defaults == 'yes' ? 'value="1"' : '' ); ?>/>
					<img class="help_tip" data-tip="<?php echo __( 'In effect for this bundle only. The available options are in sync with the filtering settings above. Always save any changes made above before configuring this section.', 'woocommerce-product-bundles' ); ?>" src="<?php echo WC()->plugin_url(); ?>/assets/images/help.png" />
				</div>
			</div>

			<div class="bundle_selection_defaults">
				<div class="form-field"><?php

					foreach ( $attributes as $attribute ) {

						// Only deal with attributes that are variations
						if ( ! $attribute[ 'is_variation' ] ) {
							continue;
						}

						// Get current value for variation (if set)
						$variation_selected_value = ( isset( $default_attributes[ sanitize_title( $attribute[ 'name' ] ) ] ) ) ? $default_attributes[ sanitize_title( $attribute[ 'name' ] ) ] : '';

						// Name will be something like attribute_pa_color
						echo '<select name="bundle_data[' . $loop . '][default_attributes][' . sanitize_title( $attribute[ 'name' ] ) .']"><option value="">' . __( 'No default', 'woocommerce' ) . ' ' . wc_attribute_label( $attribute[ 'name' ] ) . '&hellip;</option>';

						// Get terms for attribute taxonomy or value if its a custom attribute
						if ( $attribute[ 'is_taxonomy' ] ) {

							$post_terms = wp_get_post_terms( $product_id, $attribute[ 'name' ] );

							sort( $post_terms );
							foreach ( $post_terms as $term ) {
								if ( $filter_variations === 'yes' && isset( $filtered_attributes[ $attribute[ 'name' ] ] ) && ! in_array( '', $filtered_attributes[ $attribute[ 'name' ] ] ) ) {
									if ( ! in_array( $term->slug, $filtered_attributes[ $attribute[ 'name' ] ] ) ) {
										continue;
									}
								}
								echo '<option ' . selected( $variation_selected_value, $term->slug, false ) . ' value="' . esc_attr( $term->slug ) . '">' . apply_filters( 'woocommerce_variation_option_name', esc_html( $term->name ) ) . '</option>';
							}

						} else {

							$options = array_map( 'trim', explode( WC_DELIMITER, $attribute[ 'value' ] ) );

							sort( $options );
							foreach ( $options as $option ) {
								if ( $filter_variations === 'yes' && isset( $filtered_attributes[ $attribute[ 'name' ] ] ) && ! in_array( '', $filtered_attributes[ $attribute[ 'name' ] ] ) ) {
									if ( ! in_array( sanitize_title( $option ), $filtered_attributes[ $attribute[ 'name' ] ] ) ) {
										continue;
									}
								}
								echo '<option ' . selected( sanitize_title( $variation_selected_value ), sanitize_title( $option ), false ) . ' value="' . esc_attr( sanitize_title( $option ) ) . '">' . esc_html( apply_filters( 'woocommerce_variation_option_name', $option ) ) . '</option>';
							}

						}

						echo '</select>';
					}
				?></div>
			</div><?php
		}

		$item_quantity        = isset( $item_data[ 'bundle_quantity' ] ) ? absint( $item_data[ 'bundle_quantity' ] ) : 1;
		$item_quantity_max    = ! empty( $item_data[ 'bundle_quantity_max' ] ) ? absint( $item_data[ 'bundle_quantity_max' ] ) : $item_quantity;

		$per_product_pricing  = get_post_meta( $post_id, '_per_product_pricing_active', true ) == 'yes' ? true : false;

		$item_discount        = isset( $item_data[ 'bundle_discount' ] ) ? $item_data[ 'bundle_discount' ] : '';
		$is_optional          = isset( $item_data[ 'optional' ] ) ? $item_data[ 'optional' ] : '';
		$visibility           = isset( $item_data[ 'visibility' ] ) ? $item_data[ 'visibility' ] : '';
		$hide_thumbnail       = isset( $item_data[ 'hide_thumbnail' ] ) ? $item_data[ 'hide_thumbnail' ] : '';
		$override_title       = isset( $item_data[ 'override_title' ] ) ? $item_data[ 'override_title' ] : '';
		$override_description = isset( $item_data[ 'override_description' ] ) ? $item_data[ 'override_description' ] : '';


		?><div class="optional">
			<div class="form-field optional">
				<label for="optional"><?php echo __( 'Optional', 'woocommerce-product-bundles' ) ?></label>
				<input type="checkbox" class="checkbox"<?php echo ( $is_optional == 'yes' ? ' checked="checked"' : '' ); ?> name="bundle_data[<?php echo $loop; ?>][optional]" <?php echo ( $is_optional == 'yes' ? 'value="1"' : '' ); ?>/>
				<img class="help_tip" data-tip="<?php echo __( 'Check this option to mark the bundled product as optional.', 'woocommerce-product-bundles' ); ?>" src="<?php echo WC()->plugin_url(); ?>/assets/images/help.png" />
			</div>
		</div>

		<div class="quantity">
			<div class="form-field">
				<label><?php echo __( 'Quantity Min', 'woocommerce' ); ?></label>
				<input type="number" class="bundle_quantity" size="6" name="bundle_data[<?php echo $loop; ?>][bundle_quantity]" value="<?php echo $item_quantity; ?>" step="any" min="0" />
				<img class="help_tip" data-tip="<?php echo __( 'The minumum/default quantity of this bundled product.', 'woocommerce-product-bundles' ); ?>" src="<?php echo WC()->plugin_url(); ?>/assets/images/help.png" />
			</div>
		</div>

		<div class="max_quantity">
			<div class="form-field">
				<label><?php echo __( 'Quantity Max', 'woocommerce-product-bundles' ); ?></label>
				<input type="number" class="bundle_quantity" size="6" name="bundle_data[<?php echo $loop; ?>][bundle_quantity_max]" value="<?php echo $item_quantity_max; ?>" step="any" min="0" />
				<img class="help_tip" data-tip="<?php echo __( 'The maximum quantity of this bundled product.', 'woocommerce-product-bundles' ); ?>" src="<?php echo WC()->plugin_url(); ?>/assets/images/help.png" />
			</div>
		</div>

		<div class="discount">
			<div class="form-field">
				<label><?php echo __( 'Discount %', 'woocommerce' ); ?></label>
				<input type="text" <?php echo $per_product_pricing ? '' : 'disabled="disabled"'; ?> class="input-text bundle_discount wc_input_decimal" size="5" name="bundle_data[<?php echo $loop; ?>][bundle_discount]" value="<?php echo $item_discount; ?>" />
				<img class="help_tip" data-tip="<?php echo __( 'Discount applied to the regular price of this bundled product when Per-Item Pricing is active. If a Discount is applied to a bundled product which has a sale price defined, the sale price will be overridden.', 'woocommerce-product-bundles' ); ?>" src="<?php echo WC()->plugin_url(); ?>/assets/images/help.png" />
			</div>
		</div>

		<div class="item_visibility">
			<div class="form-field">
				<label for="item_visibility"><?php _e( 'Visibility', 'woocommerce-product-bundles' ); ?></label>
				<select name="bundle_data[<?php echo $loop; ?>][visibility]"><?php

					$visible = $visibility === 'hidden' ? false : true;

					echo '<option ' . selected( $visibility, 'visible', false ) . ' value="visible">' . __( 'Visible', 'woocommerce-product-bundles' ) . '</option>';
					echo '<option ' . selected( $visibility, 'hidden', false ) . ' value="hidden">' . __( 'Hidden in bundle template', 'woocommerce-product-bundles' ) . '</option>';

					if ( WC_PB_Core_Compatibility::is_wc_version_gte_2_3() ) {
						echo '<option ' . selected( $visibility, 'secret', false ) . ' value="secret">' . __( 'Hidden in bundle, cart, order and e-mail templates', 'woocommerce-product-bundles' ) . '</option>';
					}

				?></select>
				<img class="help_tip" data-tip="<?php echo __( 'Controls the visibility of this bundled product. Not recommended for variable products, unless default attribute selections (or default selection overrides) have been set.', 'woocommerce-product-bundles' ); ?>" src="<?php echo WC()->plugin_url(); ?>/assets/images/help.png" />
			</div>
		</div>

		<div class="images">
			<div class="form-field hide_thumbnail">
				<label for="hide_thumbnail"><?php echo __( 'Hide Thumbnail', 'woocommerce-product-bundles' ) ?></label>
				<input type="checkbox" class="checkbox"<?php echo ( $hide_thumbnail == 'yes' ? ' checked="checked"' : '' ); ?> name="bundle_data[<?php echo $loop; ?>][hide_thumbnail]" <?php echo ( $hide_thumbnail == 'yes' ? 'value="1"' : '' ); ?>/>
				<img class="help_tip" data-tip="<?php echo __( 'Check this option to hide the thumbnail image of this bundled product.', 'woocommerce-product-bundles' ); ?>" src="<?php echo WC()->plugin_url(); ?>/assets/images/help.png" />
			</div>
		</div>

		<div class="override_title">
			<div class="form-field override_title">
				<label for="override_title"><?php echo __( 'Override Title', 'woocommerce-product-bundles' ) ?></label>
				<input type="checkbox" class="checkbox"<?php echo ( $override_title == 'yes' ? ' checked="checked"' : '' ); ?> name="bundle_data[<?php echo $loop; ?>][override_title]" <?php echo ( $override_title == 'yes' ? 'value="1"' : '' ); ?>/>
				<img class="help_tip" data-tip="<?php echo __( 'Check this option to override the default product title.', 'woocommerce-product-bundles' ); ?>" src="<?php echo WC()->plugin_url(); ?>/assets/images/help.png" />
			</div>
		</div>

		<div class="custom_title">
			<div class="form-field product_title"><?php

				$title = isset( $item_data[ 'product_title' ] ) ? $item_data[ 'product_title' ] : '';

				?><textarea name="bundle_data[<?php echo $loop; ?>][product_title]" placeholder="" rows="2" cols="20"><?php echo esc_textarea( $title ); ?></textarea>
			</div>
		</div>

		<div class="override_description">
			<div class="form-field override_description">
				<label for="override_description"><?php echo __( 'Override Short Description', 'woocommerce-product-bundles' ) ?></label>
				<input type="checkbox" class="checkbox"<?php echo ( $override_description == 'yes' ? ' checked="checked"' : '' ); ?> name="bundle_data[<?php echo $loop; ?>][override_description]" <?php echo ( $override_description == 'yes' ? 'value="1"' : '' ); ?>/>
				<img class="help_tip" data-tip="<?php echo __( 'Check this option to override the default short product description.', 'woocommerce-product-bundles' ); ?>" src="<?php echo WC()->plugin_url(); ?>/assets/images/help.png" />
			</div>
		</div>

		<div class="custom_description">
			<div class="form-field product_description"><?php

				$description = isset( $item_data[ 'product_description' ] ) ? $item_data[ 'product_description' ] : '';

				?><textarea name="bundle_data[<?php echo $loop; ?>][product_description]" placeholder="" rows="2" cols="20"><?php echo esc_textarea( $description ); ?></textarea>
			</div>
		</div><?php
	}

	/**
	 * Admin writepanel scripts.
	 *
	 * @return void
	 */
	function woo_bundles_admin_scripts() {

		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		if ( WC_PB_Core_Compatibility::is_wc_version_gte_2_2() ) {
			$writepanel_dependency = 'wc-admin-meta-boxes';
		} else {
			$writepanel_dependency = 'woocommerce_admin_meta_boxes';
		}

		wp_register_script( 'woo_bundles_writepanel', WC_PB()->woo_bundles_plugin_url() . '/assets/js/bundled-product-write-panels' . $suffix . '.js', array( 'jquery', 'jquery-ui-datepicker', $writepanel_dependency ), WC_PB()->version );
		wp_register_style( 'woo_bundles_css', WC_PB()->woo_bundles_plugin_url() . '/assets/css/bundles-write-panels.css', array( 'woocommerce_admin_styles' ), WC_PB()->version );
		wp_register_style( 'woo_bundles_edit_order_css', WC_PB()->woo_bundles_plugin_url() . '/assets/css/bundles-edit-order.css', array( 'woocommerce_admin_styles' ), WC_PB()->version );

		// Get admin screen id
		$screen = get_current_screen();

		// WooCommerce admin pages
		if ( in_array( $screen->id, array( 'product' ) ) ) {
			wp_enqueue_script( 'woo_bundles_writepanel' );

			$params = array(
				'add_bundled_product_nonce' => wp_create_nonce( 'wc_bundles_add_bundled_product' ),
				'is_wc_version_gte_2_3'     => WC_PB_Core_Compatibility::is_wc_version_gte_2_3() ? 'yes' : 'no',
				'i18n_matches_1'            => _x( 'One result is available, press enter to select it.', 'enhanced select', 'woocommerce' ),
				'i18n_matches_n'            => _x( '%qty% results are available, use up and down arrow keys to navigate.', 'enhanced select', 'woocommerce' ),
				'i18n_no_matches'           => _x( 'No matches found', 'enhanced select', 'woocommerce' ),
				'i18n_ajax_error'           => _x( 'Loading failed', 'enhanced select', 'woocommerce' ),
				'i18n_input_too_short_1'    => _x( 'Please enter 1 or more characters', 'enhanced select', 'woocommerce' ),
				'i18n_input_too_short_n'    => _x( 'Please enter %qty% or more characters', 'enhanced select', 'woocommerce' ),
				'i18n_input_too_long_1'     => _x( 'Please delete 1 character', 'enhanced select', 'woocommerce' ),
				'i18n_input_too_long_n'     => _x( 'Please delete %qty% characters', 'enhanced select', 'woocommerce' ),
				'i18n_selection_too_long_1' => _x( 'You can only select 1 item', 'enhanced select', 'woocommerce' ),
				'i18n_selection_too_long_n' => _x( 'You can only select %qty% items', 'enhanced select', 'woocommerce' ),
				'i18n_load_more'            => _x( 'Loading more results&hellip;', 'enhanced select', 'woocommerce' ),
				'i18n_searching'            => _x( 'Searching&hellip;', 'enhanced select', 'woocommerce' ),
			);

			wp_localize_script( 'woo_bundles_writepanel', 'wc_bundles_admin_params', $params );
		}

		if ( in_array( $screen->id, array( 'edit-product', 'product' ) ) ) {
			wp_enqueue_style( 'woo_bundles_css' );
		}

		if ( in_array( $screen->id, array( 'shop_order', 'edit-shop_order' ) ) ) {
			wp_enqueue_style( 'woo_bundles_edit_order_css' );
		}
	}

	/**
	 * Add Bundled Products write panel tab.
	 *
	 * @return void
	 */
	function woo_bundles_product_write_panel_tab() {

		echo '<li class="bundled_product_tab show_if_bundle bundled_product_options linked_product_options"><a href="#bundled_product_data">'.__( 'Bundled Products', 'woocommerce-product-bundles' ).'</a></li>';
	}

	/**
	 * Write panel for Product Bundles.
	 *
	 * @return void
	 */
	function woo_bundles_product_write_panel() {

		global $post, $wpdb;

		$bundle_data = maybe_unserialize( get_post_meta( $post->ID, '_bundle_data', true ) );

		$bundled_variable_num = 0;

		?><div id="bundled_product_data" class="panel woocommerce_options_panel">

			<div class="options_group wc-metaboxes-wrapper wc-bundle-metaboxes-wrapper">

				<div id="wc-bundle-metaboxes-wrapper-inner">

					<p class="toolbar">
						<a href="#" class="close_all"><?php _e('Close all', 'woocommerce'); ?></a>
						<a href="#" class="expand_all"><?php _e('Expand all', 'woocommerce'); ?></a>
					</p>

					<div class="wc-bundled-items wc-metaboxes"><?php

						if ( ! empty( $bundle_data ) ) {

							$loop = 0;

							foreach ( $bundle_data as $item_id => $item_data ) {

								$sep        = explode( '_', $item_id );
								$product_id = $item_data[ 'product_id' ];

								$suffix = (string) $product_id != (string) $item_id ? '#' . $sep[1] : '';
								$title  = WC_PB()->helpers->get_product_title( $product_id, $suffix );

								if ( ! $title ) {
									continue;
								}

								?><div class="wc-bundled-item wc-metabox closed" rel="<?php echo $loop; ?>">
									<h3>
										<button type="button" class="remove_row button"><?php echo __( 'Remove', 'woocommerce' ); ?></button>
										<div class="handlediv" title="<?php echo __( 'Click to toggle', 'woocommerce' ); ?>"></div>
										<strong class="item-title"><?php echo $title . ' &ndash; #'. $product_id; ?></strong>
									</h3>
									<div class="item-data wc-metabox-content">
										<input type="hidden" name="bundle_data[<?php echo $loop; ?>][bundle_order]" class="bundled_item_position" value="<?php echo $loop; ?>" />
										<input type="hidden" name="bundle_data[<?php echo $loop; ?>][item_id]" class="item_id" value="<?php echo $item_id; ?>" />
										<input type="hidden" name="bundle_data[<?php echo $loop; ?>][product_id]" class="product_id" value="<?php echo $product_id; ?>" /><?php

										do_action( 'woocommerce_bundled_product_admin_config_html', $loop, $product_id, $item_data, $post->ID );

									?></div>
								</div><?php

								$loop++;
							}
						}
					?></div>
				</div>

			</div><!-- options group -->

			<p class="bundled_products_toolbar toolbar">
				<span class="bundled_products_toolbar_wrapper">
					<span class="bundled_product_selector"><?php

						if ( WC_PB_Core_Compatibility::is_wc_version_gte_2_3() ) {

							?><input type="hidden" class="wc-product-search" style="width: 250px;" id="bundled_product" name="bundled_product" data-placeholder="<?php _e( 'Search for a product&hellip;', 'woocommerce' ); ?>" data-action="woocommerce_json_search_products" data-multiple="false" data-selected="" value="" /><?php

						} else {

							?><select id="bundled_product" name="bundled_product" class="ajax_chosen_select_products" data-placeholder="<?php _e( 'Search for a product&hellip;', 'woocommerce' ); ?>">
								<option></option>
							</select><?php
						}

					?></span>
					<button type="button" class="button button-primary add_bundled_product"><?php _e( 'Add Product', 'woocommerce-product-bundles' ); ?></button>
				</span>
			</p>

		</div><?php
	}

	/**
	 * Add Bundled Products stock note.
	 *
	 * @return void
	 */
	function woo_bundles_stock_group() {

		global $post;

		?><p class="form-field show_if_bundle bundle_stock_msg">
			<label><?php _e( 'Note', 'woocommerce-product-bundles' ); ?></label>
			<span class="note"><?php _e( 'Use these settings to enable stock management at bundle level.' ); echo '<img class="help_tip" data-tip="' . __( 'By default, the sale of a product within a bundle has the same effect on its stock as an individual sale. There are no separate inventory settings for bundled items. However, this pane can be used to enable stock management at bundle level. This can be very useful for allocating bundle stock quota, or for keeping track of bundled item sales.', 'woocommerce-product-bundles' ) . '" src="' . WC()->plugin_url() . '/assets/images/help.png" />'; ?></span>
		</p><?php

	}

	/**
	 * Product bundle options for post-1.6.2 product data section.
	 *
	 * @param  array    $options    product options
	 * @return array                modified product options
	 */
	function woo_bundles_type_options( $options ) {

		$options[ 'per_product_shipping_active' ] = array(
			'id' 			=> '_per_product_shipping_active',
			'wrapper_class' => 'show_if_bundle',
			'label' 		=> __( 'Non-Bundled Shipping', 'woocommerce-product-bundles' ),
			'description' 	=> __( 'If your bundle consists of items that are assembled or packaged together, leave the box un-checked and just define the shipping properties of the product bundle below. If, however, the bundled items are shipped individually, their shipping properties must be retained. In this case, the box must be checked. \'Non-Bundled Shipping\' should also be selected when the bundle consists of virtual items, which are not shipped.', 'woocommerce-product-bundles' ),
			'default'		=> 'no'
		);

		$options[ 'per_product_pricing_active' ] = array(
			'id' 			=> '_per_product_pricing_active',
			'wrapper_class' => 'show_if_bundle bundle_pricing',
			'label' 		=> __( 'Per-Item Pricing', 'woocommerce-product-bundles' ),
			'description' 	=> __( 'When enabled, the bundle will be priced per-item, based on standalone item prices and tax rates.', 'woocommerce-product-bundles' ),
			'default'		=> 'no'
		);

		return $options;
	}

	/**
	 * Process, verify and save bundle type product data.
	 *
	 * @param  int    $post_id    the product post id
	 * @return void
	 */
	function woo_bundles_process_bundle_meta( $post_id ) {

		// Per-Item Pricing

		if ( isset( $_POST[ '_per_product_pricing_active' ] ) ) {
			update_post_meta( $post_id, '_per_product_pricing_active', 'yes' );
			update_post_meta( $post_id, '_regular_price', '' );
			update_post_meta( $post_id, '_sale_price', '' );
			update_post_meta( $post_id, '_price', '' );
		} else {
			update_post_meta( $post_id, '_per_product_pricing_active', 'no' );
		}

		// Shipping
		// Non-Bundled (per-item) Shipping

		if ( isset( $_POST[ '_per_product_shipping_active' ] ) ) {
			update_post_meta( $post_id, '_per_product_shipping_active', 'yes' );
			update_post_meta( $post_id, '_virtual', 'yes' );
			update_post_meta( $post_id, '_weight', '' );
			update_post_meta( $post_id, '_length', '' );
			update_post_meta( $post_id, '_width', '' );
			update_post_meta( $post_id, '_height', '' );
		} else {
			update_post_meta( $post_id, '_per_product_shipping_active', 'no' );
			update_post_meta( $post_id, '_virtual', 'no' );
			update_post_meta( $post_id, '_weight', stripslashes( $_POST[ '_weight' ] ) );
			update_post_meta( $post_id, '_length', stripslashes( $_POST[ '_length' ] ) );
			update_post_meta( $post_id, '_width', stripslashes( $_POST[ '_width' ] ) );
			update_post_meta( $post_id, '_height', stripslashes( $_POST[ '_height' ] ) );
		}

		$posted_bundle_data = isset( $_POST[ 'bundle_data' ] ) ? $_POST[ 'bundle_data' ] : false;

		if ( ! $posted_bundle_data || false === $processed_bundle_data = $this->build_bundle_config( $post_id, $posted_bundle_data ) ) {

			delete_post_meta( $post_id, '_bundle_data' );

			$this->add_admin_error( __( 'Please add at least one product to the bundle before publishing. To add products, click on the Bundled Products tab.', 'woocommerce-product-bundles' ) );

			global $wpdb;
			$wpdb->update( $wpdb->posts, array( 'post_status' => 'draft' ), array( 'ID' => $post_id ) );

			return;

		} else {

			update_post_meta( $post_id, '_bundle_data', $processed_bundle_data );
		}

		// Delete no longer used meta
		delete_post_meta( $post_id, '_min_bundle_price' );
		delete_post_meta( $post_id, '_max_bundle_price' );
	}

	/**
	 * Update bundle post_meta on save.
	 *
	 * @return 	mixed     bundle data array configuration or false if unsuccessful
	 */
	function build_bundle_config( $post_id, $posted_bundle_data ) {

		// Process Bundled Product Configuration
		$bundle_data         = array();
		$ordered_bundle_data = array();

		$bundle_data_old     = get_post_meta( $post_id, '_bundle_data', true );

		$bundled_subs = 0;

		// Now start saving new data
		$times         = array();
		$save_defaults = array();
		$ordering      = array();

		if ( ! empty( $posted_bundle_data ) ) {

			foreach ( $posted_bundle_data as $val => $data ) {

				$id = isset( $data[ 'product_id' ] ) ? $data[ 'product_id' ] : false;

				if ( ! $id ) {
					continue;
				}

				$terms        = get_the_terms( $id, 'product_type' );
				$product_type = ! empty( $terms ) && isset( current( $terms )->name ) ? sanitize_title( current( $terms )->name ) : 'simple';

				$is_sub = class_exists( 'WC_Subscriptions' ) && WC_PB()->compatibility->is_subscription( $id );

				if ( ( $id && $id > 0 ) && ( $product_type === 'simple' || $product_type === 'variable' || $is_sub ) && ( $post_id != $id ) ) {

					// only allow saving 1 sub
					if ( $is_sub ) {

						if ( $bundled_subs > 0 && version_compare( WC_Subscriptions::$version, '2.0.0', '<' ) ) {

							$this->add_admin_error( sprintf( __( '\'%1$s\' (#%2$s) was not saved. Only one simple Subscription per Bundle is supported.', 'woocommerce-product-bundles' ), get_the_title( $id ), $id ) );
							continue;

						} else {
							$bundled_subs++;
						}
					}

					// allow bundling the same item id multiple times by adding a suffix
					if ( ! isset( $times[ $id ] ) ) {

						$times[ $id ] 	= 1;
						$val 			= $id;

					} else {

						// only allow multiple instances of non-sold-individually items
						if ( get_post_meta( $id, '_sold_individually', true ) == 'yes' ) {

							$this->add_admin_error( sprintf( __( '\'%1$s\' (#%2$s) is sold individually and cannot be bundled more than once.', 'woocommerce-product-bundles' ), get_the_title( $id ), $id ) );
							continue;

						}

						$times[ $id ] += 1;
						$val = isset( $data[ 'item_id' ] ) ? $data[ 'item_id' ] : $id . '_' . $times[ $id ];
					}

					$bundle_data[ $val ] = array();

					$bundle_data[ $val ][ 'product_id' ] = $id;

					// Save thumbnail preferences first
					if ( isset( $data[ 'hide_thumbnail' ] ) ) {
						$bundle_data[ $val ][ 'hide_thumbnail' ] = 'yes';
					} else {
						$bundle_data[ $val ][ 'hide_thumbnail' ] = 'no';
					}

					// Save title preferences
					if ( isset( $data[ 'override_title' ] ) ) {
						$bundle_data[ $val ][ 'override_title' ] = 'yes';
						$bundle_data[ $val ][ 'product_title' ] = isset( $data[ 'product_title' ] ) ? $data[ 'product_title' ] : '';
					} else {
						$bundle_data[ $val ][ 'override_title' ] = 'no';
					}

					// Save description preferences
					if ( isset( $data[ 'override_description' ] ) ) {
						$bundle_data[ $val ][ 'override_description' ] = 'yes';
						$bundle_data[ $val ][ 'product_description' ] = isset( $data[ 'product_description' ] ) ? wp_kses_post( stripslashes( $data[ 'product_description' ] ) ) : '';
					} else {
						$bundle_data[ $val ][ 'override_description' ] = 'no';
					}

					// Save optional
					if ( isset( $data[ 'optional' ] ) ) {
						$bundle_data[ $val ][ 'optional' ] = 'yes';
					} else {
						$bundle_data[ $val ][ 'optional' ] = 'no';
					}

					// Save quantity data
					if ( isset( $data[ 'bundle_quantity' ] ) ) {

						if ( is_numeric( $data[ 'bundle_quantity' ] ) ) {

							$quantity = absint( $data[ 'bundle_quantity' ] );

							if ( $quantity >= 0 && $data[ 'bundle_quantity' ] - $quantity == 0 ) {

								if ( $quantity !== 1 && ( get_post_meta( $id, '_sold_individually', true ) === 'yes' || ( get_post_meta( $id, '_downloadable', true ) === 'yes' && get_post_meta( $id, '_virtual', true ) === 'yes' && get_option( 'woocommerce_limit_downloadable_product_qty' ) === 'yes' ) ) ) {

									$this->add_admin_error( sprintf( __( '\'%1$s\' (#%2$s) is sold individually and cannot be bundled with a minimum quantity higher than 1.', 'woocommerce-product-bundles' ), get_the_title( $id ), $id ) );
									$bundle_data[ $val ][ 'bundle_quantity' ] = 1;

								} else {
									$bundle_data[ $val ][ 'bundle_quantity' ] = $quantity;
								}

							} else {

								$this->add_admin_error( sprintf( __( 'The quantity you entered for \'%1$s%2$s\' (#%3$s) was not valid and has been reset. Please enter a non-negative integer value.', 'woocommerce-product-bundles' ), get_the_title( $id ), ( $id != $val ? ' #' . $times[ $id ] : '' ), $id ) );
							}
						}

					} else {

						$bundle_data[ $val ][ 'bundle_quantity' ] = 1;
					}

					$quantity_min = $bundle_data[ $val ][ 'bundle_quantity' ];

					// Save max quantity data
					if ( isset( $data[ 'bundle_quantity_max' ] ) ) {

						if ( is_numeric( $data[ 'bundle_quantity_max' ] ) ) {

							$quantity = absint( $data[ 'bundle_quantity_max' ] );

							if ( $quantity > 0 && $quantity >= $quantity_min && $data[ 'bundle_quantity_max' ] - $quantity == 0 ) {

								if ( $quantity !== 1 && ( get_post_meta( $id, '_sold_individually', true ) === 'yes' || ( get_post_meta( $id, '_downloadable', true ) === 'yes' && get_post_meta( $id, '_virtual', true ) === 'yes' && get_option( 'woocommerce_limit_downloadable_product_qty' ) === 'yes' ) ) ) {

									$this->add_admin_error( sprintf( __( '\'%1$s\' (#%2$s) is sold individually and cannot be bundled with a maximum quantity higher than 1.', 'woocommerce-product-bundles' ), get_the_title( $id ), $id ) );
									$bundle_data[ $val ][ 'bundle_quantity_max' ] = 1;

								} else {
									$bundle_data[ $val ][ 'bundle_quantity_max' ] = $quantity;
								}

							} else {

								$this->add_admin_error( sprintf( __( 'The maximum product quantity that you entered for \'%1$s%2$s\' (#%3$s) was not valid and has been reset. Please enter a positive integer value, at least as high as the minimum quantity.', 'woocommerce-product-bundles' ), get_the_title( $id ), ( $id != $val ? ' #' . $times[ $id ] : '' ), $id ) );
							}
						}

					} else {

						$bundle_data[ $val ][ 'bundle_quantity_max' ] = $quantity_min;
					}


					// Save sale price data
					if ( isset( $data[ 'bundle_discount' ] ) ) {

						if ( is_numeric( $data[ 'bundle_discount' ] ) ) {

							$discount = ( float ) wc_format_decimal( $data[ 'bundle_discount' ] );

							if ( $discount < 0 || $discount > 100 ) {

								$this->add_admin_error( sprintf( __( 'The discount value you entered for \'%1$s%2$s\' (#%3$s) was not valid and has been reset. Please enter a positive number between 0-100.', 'woocommerce-product-bundles' ), get_the_title( $id ), ( $id != $val ? ' #' . $times[$id] : '' ), $id ) );
								$bundle_data[ $val ][ 'bundle_discount' ] = '';

							} else {
								$bundle_data[ $val ][ 'bundle_discount' ] = $discount;
							}
						} else {
							$bundle_data[ $val ][ 'bundle_discount' ] = '';
						}
					} else {
						$bundle_data[ $val ][ 'bundle_discount' ] = '';
					}

					// Save data related to variable items
					if ( $product_type === 'variable' ) {

						// Save variation filtering options
						if ( isset( $data[ 'filter_variations' ] ) ) {

							if ( isset( $data[ 'allowed_variations' ] ) && count( $data[ 'allowed_variations' ] ) > 0 ) {

								$bundle_data[ $val ][ 'filter_variations' ] = 'yes';

								$bundle_data[ $val ][ 'allowed_variations' ] = $data[ 'allowed_variations' ];

								if ( isset( $data[ 'hide_filtered_variations' ] ) )
									$bundle_data[ $val ][ 'hide_filtered_variations' ] = 'yes';
								else
									$bundle_data[ $val ][ 'hide_filtered_variations' ] = 'no';
							}
							else {
								$bundle_data[ $val ][ 'filter_variations' ] = 'no';
								$this->add_admin_error( __( 'Please select at least one variation for each bundled product you want to filter.', 'woocommerce-product-bundles' ) );
							}
						} else {
							$bundle_data[ $val ][ 'filter_variations' ] = 'no';
						}

						// Save defaults options
						if ( isset( $data[ 'override_defaults' ] ) ) {

							if ( isset( $data[ 'default_attributes' ] ) ) {

								// if filters are set, check that the selections are valid

								if ( isset( $data[ 'filter_variations' ] ) && ! empty( $data[ 'allowed_variations' ] ) ) {

									$allowed_variations = $data[ 'allowed_variations' ];

									// the array to store all valid attribute options of the iterated product
									$filtered_attributes = array();

									// populate array with valid attributes
									foreach ( $allowed_variations as $variation ) {

										$variation_data = array();

										// sweep the post meta for attributes
										if ( WC_PB_Core_Compatibility::is_wc_version_gte_2_4() ) {
											$variation_data = wc_get_product_variation_attributes( $variation );
										} else {
											$post_meta = get_post_meta( $variation );

											foreach ( $post_meta as $field => $value ) {

												if ( ! strstr( $field, 'attribute_' ) ) {
													continue;
												}

												$variation_data[ $field ] = $value[0];
											}
										}

										foreach ( $variation_data as $name => $value ) {

											$attribute_name  = substr( $name, strlen( 'attribute_' ) );
											$attribute_value = sanitize_title( $value );

											// ( populate array )
											if ( ! isset( $filtered_attributes[ sanitize_title( $attribute_name ) ] ) ) {
												$filtered_attributes[ sanitize_title( $attribute_name ) ][] = $attribute_value;
											} elseif ( ! in_array( $attribute_value, $filtered_attributes[ sanitize_title( $attribute_name ) ] ) ) {
												$filtered_attributes[ sanitize_title( $attribute_name ) ][] = $attribute_value;
											}
										}

									}

									// check validity
									foreach ( $data[ 'default_attributes' ] as $sanitized_name => $value ) {

										if ( $value === '' ) {
											continue;
										}

										if ( ! in_array( sanitize_title( $value ), $filtered_attributes[ $sanitized_name ] ) && ! in_array( '', $filtered_attributes[ $sanitized_name ] ) ) {

											// set option to "Any"
											$data[ 'default_attributes' ][ $sanitized_name ] = '';

											// throw an error
											$this->add_admin_error( sprintf( __( 'The defaults that you selected for \'%1$s%2$s\' (#%3$s) are inconsistent with the set of active variations. Always double-check your preferences before saving, and always save any changes made to the variation filters before choosing new defaults.', 'woocommerce-product-bundles' ), get_the_title( $id ), ( $id != $val ? ' #' . $times[$id] : '' ), $id ) );

											continue;
										}
									}
								}

								// save
								foreach ( $data[ 'default_attributes' ] as $sanitized_name => $value ) {
									$bundle_data[ $val ][ 'bundle_defaults' ][ $sanitized_name ] = $value;
								}

								$bundle_data[ $val ][ 'override_defaults' ] = 'yes';
							}

						} else {

							$bundle_data[ $val ][ 'override_defaults' ] = 'no';
						}
					}

					// Save visibility preferences
					if ( isset( $data[ 'visibility' ] ) ) {

						if ( $data[ 'visibility' ] === 'visible' ) {

							$bundle_data[ $val ][ 'visibility' ] = 'visible';

						} elseif ( in_array( $data[ 'visibility' ], array( 'secret', 'hidden' ) ) ) {

							if ( $product_type === 'variable' ) {

								if ( $bundle_data[ $val ][ 'override_defaults' ] == 'yes' ) {

									if ( isset( $data[ 'default_attributes' ] ) ) {

										foreach ( $data[ 'default_attributes' ] as $default_name => $default_value ) {

											if ( ! $default_value ) {

												$data[ 'visibility' ] = 'visible';
												$this->add_admin_error( sprintf( __( '\'%1$s%2$s\' (#%s) cannot be hidden unless all default options of the product are defined.', 'woocommerce-product-bundles' ), get_the_title( $id ), ( $id != $val ? ' #' . $times[$id] : '' ), $id ) );
												break;
											}
										}

										$bundle_data[ $val ][ 'visibility' ] = $data[ 'visibility' ];

									} else {

										$bundle_data[ $val ][ 'visibility' ] = 'visible';
									}

								} else {

									$this->add_admin_error( sprintf( __( '\'%1$s%2$s\' (#%3$s) cannot be hidden unless all default options of the product are defined.', 'woocommerce-product-bundles' ), get_the_title( $id ), ( $id != $val ? ' #' . $times[$id] : '' ), $id ) );
									$bundle_data[ $val ][ 'visibility' ] = 'visible';
								}

							} else {

								$bundle_data[ $val ][ 'visibility' ] = $data[ 'visibility' ];
							}

						}

					} else {

						$bundle_data[ $val ][ 'visibility' ] = 'visible';
					}

					// Save position data
					if ( isset( $data[ 'bundle_order' ] ) ) {
						$ordering[ (int) $data[ 'bundle_order' ] ] = $val;
					} else {
						$ordering[ count( $ordering ) ] = $val;
					}

					$bundle_data[ $val ] = apply_filters( 'woocommerce_bundles_process_bundled_item_admin_data', $bundle_data[ $val ], $data, $val, $post_id );
				}
			}

			// Check empty
			if ( empty( $bundle_data ) ) {
				return false;
			}

			// Sorting
			ksort( $ordering );
			$ordered_bundle_data = array();

			foreach ( $ordering as $item_id ) {
			    $ordered_bundle_data[ $item_id ] = $bundle_data[ $item_id ];
			}

			return $ordered_bundle_data;

		} else {

			return false;
		}
	}

	/**
	 * Add the 'bundle' product type to the product type dropdown.
	 *
	 * @param  array    $options    product types array
	 * @return array                modified product types array
	 */
	function woo_bundles_product_selector_filter( $options ) {

		$options[ 'bundle' ] = __( 'Product bundle', 'woocommerce-product-bundles' );

		return $options;
	}

	/**
	 * Handles adding bundled products via ajax.
	 *
	 * @return void
	 */
	function ajax_add_bundled_product() {

		check_ajax_referer( 'wc_bundles_add_bundled_product', 'security' );

		$loop       = intval( $_POST[ 'id' ] );
		$post_id    = intval( $_POST[ 'post_id' ] );
		$product_id = intval( $_POST[ 'product_id' ] );

		$title      = WC_PB()->helpers->get_product_title( $product_id );
		$product    = wc_get_product( $product_id );

		$response   = array();

		$response[ 'markup' ]  = '';
		$response[ 'message' ] = '';

		if ( $title && $product ) {

			if ( in_array( $product->product_type, array( 'simple', 'variable', 'subscription' ) ) ) {

				ob_start();
				include( 'html-bundled-product-admin.php' );
				$response[ 'markup' ] = ob_get_clean();

			} else {

				$response[ 'message' ] = __( 'The selected product cannot be bundled. Please select a simple product, a variable product, or a simple subscription.', 'woocommerce-product-bundles' );
			}

		} else {
			$response[ 'message' ] = __( 'The selected product is invalid.', 'woocommerce-product-bundles' );
		}

		header( 'Content-Type: application/json; charset=utf-8' );
		echo json_encode( $response );

		die();
	}

	/**
	 * Support scanning for template overrides in extension.
	 *
	 * @param  array   $paths paths to check
	 * @return array          modified paths to check
	 */
	function woo_bundles_template_scan_path( $paths ) {

		$paths[ 'WooCommerce Product Bundles' ] = WC_PB()->woo_bundles_plugin_path() . '/templates/';

		return $paths;
	}

	/**
	 * Add admin errors.
	 *
	 * @param  string $error
	 * @return string
	 */
	public function add_admin_error( $error ) {

		WC_Admin_Meta_Boxes::add_error( $error );
	}
}
