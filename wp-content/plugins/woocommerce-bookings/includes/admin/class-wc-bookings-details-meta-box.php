<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class WC_Bookings_Details_Meta_Box {
	public $id;
	public $title;
	public $context;
	public $priority;
	public $post_types;

	public function __construct() {
		$this->id = 'woocommerce-booking-data';
		$this->title = __( 'Booking Details', 'woocommerce-bookings' );
		$this->context = 'normal';
		$this->priority = 'high';
		$this->post_types = array( 'wc_booking' );

		add_action( 'save_post', array( $this, 'meta_box_save' ), 10, 1 );
	}

	public function meta_box_inner( $post ) {
		wp_nonce_field( 'wc_bookings_details_meta_box', 'wc_bookings_details_meta_box_nonce' );

		// Scripts.
		if ( version_compare( WOOCOMMERCE_VERSION, '2.3', '<' ) ) {
			wp_enqueue_script( 'ajax-chosen' );
			wp_enqueue_script( 'chosen' );
		} else {
			wp_enqueue_script( 'wc-enhanced-select' );
		}
		wp_enqueue_script( 'jquery-ui-datepicker' );

		$customer_id = get_post_meta( $post->ID, '_booking_customer_id', true );
		$order_parent_id = apply_filters( 'woocommerce_order_number', _x( '#', 'hash before order number', 'woocommerce-bookings' ) . $post->post_parent, $post->post_parent );

		// Sanity check saved dates
		$start_date = get_post_meta( $post->ID, '_booking_start', true );
		$end_date   = get_post_meta( $post->ID, '_booking_end', true );
		$product_id = get_post_meta( $post->ID, '_booking_product_id', true );

		if ( $start_date && strtotime( $start_date ) > strtotime( '+ 2 year', current_time( 'timestamp' ) ) ) {
			echo '<div class="updated highlight"><p>' . __( 'This booking is scheduled over 2 years into the future. Please ensure this is correct.', 'woocommerce-bookings' ) . '</p></div>';
		}
		if ( $product_id && ( $product = get_product( $product_id ) ) && ( $max = $product->get_max_date() ) ) {
			$max_date = strtotime( "+{$max['value']} {$max['unit']}", current_time( 'timestamp' ) );
			if ( strtotime( $start_date ) > $max_date || strtotime( $end_date ) > $max_date ) {
				echo '<div class="updated highlight"><p>' . sprintf( __( 'This booking is scheduled over the products allowed max booking date (%s). Please ensure this is correct.', 'woocommerce-bookings' ), date_i18n( wc_date_format(), $max_date ) ) . '</p></div>';
			}
		}
		if ( strtotime( $start_date ) && strtotime( $end_date ) && strtotime( $start_date ) > strtotime( $end_date ) ) {
			echo '<div class="error"><p>' . __( 'This booking has an end date set before the start date.', 'woocommerce-bookings' ) . '</p></div>';
		}
		?>
		<style type="text/css">
			#post-body-content, #titlediv, #major-publishing-actions, #minor-publishing-actions, #visibility, #submitdiv { display:none }
		</style>
		<div class="panel-wrap woocommerce">
			<div id="booking_data" class="panel">

			<h2><?php _e( 'Booking Details', 'woocommerce-bookings' ); ?></h2>
			<p class="booking_number"><?php

				printf( __( 'Booking number: #%s.', 'woocommerce-bookings' ), esc_html( $post->ID ) );

				if ( $post->post_parent ) {
					$order = new WC_Order( $post->post_parent );
					printf( ' ' . __( 'Order number: %s.', 'woocommerce-bookings' ), '<a href="' . admin_url( 'post.php?post=' . absint( $post->post_parent ) . '&action=edit' ) . '">#' . esc_html( $order->get_order_number() ) . '</a>' );
				}

			?></p>

			<div class="booking_data_column_container">
				<div class="booking_data_column">

					<h4><?php _e( 'General Details', 'woocommerce-bookings' ); ?></h4>

					<p class="form-field form-field-wide">
						<label for="_booking_order_id"><?php _e( 'Order ID:', 'woocommerce-bookings' ); ?></label>
						<?php
						$order_string = '';
						if ( ! empty( $post->post_parent ) ) {
							$order_string = $order_parent_id . ' &ndash; ' . esc_html( get_the_title( $post->post_parent ) );
						}
						if ( version_compare( WOOCOMMERCE_VERSION, '2.3', '<' ) ) : ?>
							<select id="_booking_order_id" name="_booking_order_id" data-placeholder="<?php _e( 'Select an order&hellip;', 'woocommerce-bookings' ); ?>">
								<option value=""><?php _e( 'N/A', 'woocommerce-bookings' ); ?></option>
								<?php
									if ( $post->post_parent ) {
										echo '<option value="' . esc_attr( $post->post_parent ) . '" ' . selected( 1, 1, false ) . '>' . esc_attr( $order_string ) . '</option>';
									}
								?>
							</select>
						<?php else : ?>
							<input type="hidden" id="_booking_order_id" name="_booking_order_id" data-placeholder="<?php _e( 'N/A', 'woocommerce-bookings' ); ?>" data-selected="<?php echo esc_attr( $order_string ); ?>" value="<?php echo esc_attr( $post->post_parent ? $post->post_parent : '' ); ?>" data-allow_clear="true" />
						<?php endif; ?>
					</p>

					<p class="form-field form-field-wide"><label for="booking_date"><?php _e( 'Date created:', 'woocommerce-bookings' ); ?></label>
						<input type="text" class="date-picker-field" name="booking_date" id="booking_date" maxlength="10" value="<?php echo date_i18n( 'Y-m-d', strtotime( $post->post_date ) ); ?>" pattern="[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])" /> @ <input type="number" class="hour" placeholder="<?php _e( 'h', 'woocommerce-bookings' ); ?>" name="booking_date_hour" id="booking_date_hour" maxlength="2" size="2" value="<?php echo date_i18n( 'H', strtotime( $post->post_date ) ); ?>" pattern="\-?\d+(\.\d{0,})?" />:<input type="number" class="minute" placeholder="<?php _e( 'm', 'woocommerce-bookings' ); ?>" name="booking_date_minute" id="booking_date_minute" maxlength="2" size="2" value="<?php echo date_i18n( 'i', strtotime( $post->post_date ) ); ?>" pattern="\-?\d+(\.\d{0,})?" />
					</p>

					<?php
						$statuses = array(
							'unpaid'               => __( 'unpaid', 'woocommerce-bookings' ),
							'in-cart'              => __( 'in-cart', 'woocommerce-bookings' ),
							'pending-confirmation' => __( 'pending-confirmation', 'woocommerce-bookings' ),
							'confirmed'            => __( 'confirmed', 'woocommerce-bookings' ),
							'paid'                 => __( 'paid', 'woocommerce-bookings' ),
							'cancelled'            => __( 'cancelled', 'woocommerce-bookings' ),
							'complete'             => __( 'complete', 'woocommerce-bookings' )
						);
					?>

					<p class="form-field form-field-wide">
						<label for="_booking_status"><?php _e( 'Booking Status:', 'woocommerce-bookings' ); ?></label>
						<select id="_booking_status" name="_booking_status" class="wc-enhanced-select">
							<?php
								foreach ( $statuses as $key => $value ) {
									echo '<option value="' . esc_attr( $key ) . '" ' . selected( $key, $post->post_status, false ) . '>' . esc_html__( $value, 'woocommerce-bookings' ) . '</option>';
								}
							?>
						</select>
					</p>

					<p class="form-field form-field-wide">
						<label for="_booking_customer_id"><?php _e( 'Customer:', 'woocommerce-bookings' ); ?></label>
						<?php
						$user_string = '';
						if ( ! empty( $customer_id ) ) {
							$user        = get_user_by( 'id', $customer_id );
							$user_string = esc_html( $user->display_name ) . ' (#' . absint( $user->ID ) . ' &ndash; ' . esc_html( $user->user_email );
						}
						if ( version_compare( WOOCOMMERCE_VERSION, '2.3', '<' ) ) : ?>
							<select id="_booking_customer_id" name="_booking_customer_id" class="ajax_chosen_select_customer">
								<option value=""><?php _e( 'Guest', 'woocommerce-bookings' ); ?></option>
								<?php
									if ( $customer_id ) {
										$user = get_user_by( 'id', $customer_id );
										echo '<option value="' . esc_attr( $customer_id ) . '" ' . selected( 1, 1, false ) . '>' . esc_html( $user_string ) . ')</option>';
									}
								?>
							</select>
						<?php else : ?>
							<input type="hidden" class="wc-customer-search" id="_booking_customer_id" name="_booking_customer_id" data-placeholder="<?php _e( 'Guest', 'woocommerce-bookings' ); ?>" data-selected="<?php echo esc_attr( $user_string ); ?>" value="<?php echo $customer_id; ?>" data-allow_clear="true" />
						<?php endif; ?>
					</p>

					<?php do_action( 'woocommerce_admin_booking_data_after_booking_details', $post->ID ); ?>

				</div>
				<div class="booking_data_column">

					<h4><?php _e( 'Booking Specification', 'woocommerce-bookings' ); ?></h4>

					<?php

					$bookable_products = array( '' => __( 'N/A', 'woocommerce-bookings' ) );

					$products = WC_Bookings_Admin::get_booking_products();

					foreach ( $products as $product ) {
						$bookable_products[ $product->ID ] = $product->post_title;

						$resources = wc_booking_get_product_resources( $product->ID );

						foreach ( $resources as $resource ) {
							$bookable_products[ $product->ID . '=>' . $resource->ID ] = '&nbsp;&nbsp;&nbsp;' . $resource->post_title;
						}
					}

					$product_id  = get_post_meta( $post->ID, '_booking_product_id', true );
					$resource_id = get_post_meta( $post->ID, '_booking_resource_id', true );

					woocommerce_wp_select( array( 'id' => 'product_or_resource_id', 'class' => 'wc-enhanced-select', 'label' => __( 'Booked Product', 'woocommerce-bookings' ), 'options' => $bookable_products, 'value' => ( $resource_id ? $product_id . '=>' . $resource_id : $product_id ) ) );

					woocommerce_wp_text_input( array( 'id' => '_booking_parent_id', 'label' => __( 'Parent Booking ID', 'woocommerce-bookings' ), 'placeholder' => 'N/A' ) );

					$saved_persons = get_post_meta( $post->ID, '_booking_persons', true );
					$product = wc_get_product( $product_id );
					$person_types = $product->get_person_types();

					if ( ! empty( $person_types ) && is_array( $person_types ) ) {
						echo '<br class="clear" />';
						echo '<h4>' . __( 'Person(s)', 'woocommerce-bookings' ) . '</h4>';

						foreach ( $person_types as $person_type ) {
							$person_count = ( isset( $saved_persons[ $person_type->ID ] ) ? $saved_persons[ $person_type->ID ] : 0 );
							woocommerce_wp_text_input( array( 'id' => '_booking_person_' . $person_type->ID, 'label' => $person_type->post_title, 'type' => 'number', 'placeholder' => '0', 'value' => $person_count, 'wrapper_class' => 'booking-person' ) );
						}
					} else if ( empty( $person_types ) && ! empty( $saved_persons ) && is_array( $saved_persons ) ) {
						echo '<br class="clear" />';
						echo '<h4>' . __( 'Person(s)', 'woocommerce-bookings' ) . '</h4>';

						foreach ( $saved_persons as $person_id => $person_count ) {
							woocommerce_wp_text_input( array( 'id' => '_booking_person_' . $person_id, 'label' => get_the_title( $person_id ), 'type' => 'number', 'placeholder' => '0', 'value' => $person_count, 'wrapper_class' => 'booking-person' ) );
						}
					}
					?>
				</div>
				<div class="booking_data_column">

					<h4><?php _e( 'Booking Date/Time', 'woocommerce-bookings' ); ?></h4>

					<?php

					woocommerce_wp_text_input( array( 'id' => 'booking_start_date', 'label' => __( 'Start Date', 'woocommerce-bookings' ), 'placeholder' => 'yyyy-mm-dd', 'value' => date( 'Y-m-d', strtotime( get_post_meta( $post->ID, '_booking_start', true ) ) ), 'class' => 'date-picker-field' ) );

					woocommerce_wp_text_input( array( 'id' => 'booking_end_date', 'label' => __( 'End Date', 'woocommerce-bookings' ), 'placeholder' => 'yyyy-mm-dd', 'value' => date( 'Y-m-d', strtotime( get_post_meta( $post->ID, '_booking_end', true ) ) ), 'class' => 'date-picker-field' ) );

					woocommerce_wp_checkbox( array( 'id' => '_booking_all_day', 'label' => __( 'All Day', 'woocommerce-bookings' ), 'description' => __( 'Check this box if the booking is for all day.', 'woocommerce-bookings' ), 'value' => get_post_meta( $post->ID, '_booking_all_day', true ) ? 'yes' : 'no' ) );

					woocommerce_wp_text_input( array( 'id' => 'booking_start_time', 'label' => __( 'Start Time', 'woocommerce-bookings' ), 'placeholder' => 'hh:mm', 'value' => date( 'H:i', strtotime( get_post_meta( $post->ID, '_booking_start', true ) ) ), 'class' => 'datepicker' ) );

					woocommerce_wp_text_input( array( 'id' => 'booking_end_time', 'label' => __( 'End Time', 'woocommerce-bookings' ), 'placeholder' => 'hh:mm', 'value' => date( 'H:i', strtotime( get_post_meta( $post->ID, '_booking_end', true ) ) ) ) );

					?>

				</div>
			</div>
			<div class="clear"></div>
		</div>

		<?php
			if ( version_compare( WOOCOMMERCE_VERSION, '2.3', '<' ) ) {
				wc_enqueue_js( "
					$( 'select#_booking_status' ).chosen({
						disable_search: true
					});
					$( 'select#product_or_resource_id' ).chosen();
					$( 'select#_booking_order_id' ).ajaxChosen({
						method:         'GET',
						url:            '" . admin_url( 'admin-ajax.php' ) . "',
						dataType:       'json',
						afterTypeDelay: 100,
						minTermLength:  1,
						data: {
							action:   'wc_bookings_json_search_order',
							security: '" . wp_create_nonce( 'search-booking-order' ) . "'
						}
					}, function ( data ) {

						var orders = {};

						$.each( data, function ( i, val ) {
							orders[i] = val;
						});

						return orders;
					});
					$( 'select.ajax_chosen_select_customer' ).ajaxChosen({
						method:         'GET',
						url:            '" . admin_url( 'admin-ajax.php' ) . "',
						dataType:       'json',
						afterTypeDelay: 100,
						minTermLength:  1,
						data: {
							action:   'woocommerce_json_search_customers',
							security: '" . wp_create_nonce( 'search-customers' ) . "'
						}
					}, function ( data ) {

						var terms = {};

						$.each( data, function ( i, val ) {
							terms[i] = val;
						});

						return terms;
					});
				" );
			} else {
				wc_enqueue_js( "
					$( '#_booking_order_id' ).filter( ':not(.enhanced)' ).each( function() {
						var select2_args = {
							allowClear:  true,
							placeholder: $( this ).data( 'placeholder' ),
							minimumInputLength: $( this ).data( 'minimum_input_length' ) ? $( this ).data( 'minimum_input_length' ) : '3',
							escapeMarkup: function( m ) {
								return m;
							},
							ajax: {
						        url:         '" . admin_url( 'admin-ajax.php' ) . "',
						        dataType:    'json',
						        quietMillis: 250,
						        data: function( term, page ) {
						            return {
										term:     term,
										action:   'wc_bookings_json_search_order',
										security: '" . wp_create_nonce( 'search-booking-order' ) . "'
						            };
						        },
						        results: function( data, page ) {
						        	var terms = [];
							        if ( data ) {
										$.each( data, function( id, text ) {
											terms.push( { id: id, text: text } );
										});
									}
						            return { results: terms };
						        },
						        cache: true
						    }
						};
						select2_args.multiple = false;
						select2_args.initSelection = function( element, callback ) {
							var data = {id: element.val(), text: element.attr( 'data-selected' )};
							return callback( data );
						};
						$( this ).select2( select2_args ).addClass( 'enhanced' );
					});
				" );
			}
			wc_enqueue_js( "
				$( '#_booking_all_day' ).change( function () {
					if ( $( this ).is( ':checked' ) ) {
						$( '#booking_start_time, #booking_end_time' ).closest( 'p' ).hide();
					} else {
						$( '#booking_start_time, #booking_end_time' ).closest( 'p' ).show();
					}
				}).change();
				$( '.date-picker-field' ).datepicker({
					dateFormat: 'yy-mm-dd',
					numberOfMonths: 1,
					showButtonPanel: true,
				});
			" );
	}

	public function meta_box_save( $post_id ) {
		if ( ! isset( $_POST['wc_bookings_details_meta_box_nonce'] ) || ! wp_verify_nonce( $_POST['wc_bookings_details_meta_box_nonce'], 'wc_bookings_details_meta_box' ) ) {
			return $post_id;
		}

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return $post_id;
		}

		if ( ! in_array( $_POST['post_type'], $this->post_types ) ) {
			return $post_id;
		}

		global $wpdb, $post;

		// Save simple fields
		$booking_order_id = absint( $_POST['_booking_order_id'] );
		$booking_status   = wc_clean( $_POST['_booking_status'] );
		$customer_id      = absint( $_POST['_booking_customer_id'] );
		$product_id       = wc_clean( $_POST['product_or_resource_id'] );
		$parent_id        = absint( $_POST['_booking_parent_id'] );
		$all_day          = isset( $_POST['_booking_all_day'] ) ? '1' : '0';

		// Update post_parent and status via query to prevent endless loops
		$wpdb->update( $wpdb->posts, array( 'post_parent' => $booking_order_id ), array( 'ID' => $post_id ) );
		$wpdb->update( $wpdb->posts, array( 'post_status' => $booking_status ), array( 'ID' => $post_id ) );

		// Trigger actions manually
		$old_status = $post->post_status;
		do_action( 'woocommerce_booking_' . $booking_status, $post_id );
		do_action( 'woocommerce_booking_' . $old_status . '_to_' . $booking_status, $post_id );
		clean_post_cache( $post_id );

		// Note in the order
		if ( $booking_order_id && function_exists( 'wc_get_order' ) && ( $order = wc_get_order( $booking_order_id ) ) ) {
			$order->add_order_note( sprintf( __( 'Booking #%d status changed manually from "%s" to "%s', 'woocommerce-bookings' ), $post_id, $old_status, $booking_status ) );
		}

		// Save product and resource
		if ( strstr( $product_id, '=>' ) ) {
			list( $product_id, $resource_id ) = explode( '=>', $product_id );
		} else {
			$resource_id = 0;
		}

		update_post_meta( $post_id, '_booking_resource_id', $resource_id );
		update_post_meta( $post_id, '_booking_product_id', $product_id );

		// Update meta
		update_post_meta( $post_id, '_booking_customer_id', $customer_id );
		update_post_meta( $post_id, '_booking_parent_id', $parent_id );
		update_post_meta( $post_id, '_booking_all_day', $all_day );

		// Persons
		$saved_persons = get_post_meta( $post_id, '_booking_persons', true );
		$product = wc_get_product( $product_id );
		$person_types = $product->get_person_types();

		if ( ! empty( $person_types ) && is_array( $person_types ) ) {
			$booking_persons = array();
			foreach ( $person_types as $person_type ) {
				if ( ! empty( $_POST['_booking_person_' . $person_type->ID ] ) ) {
					$booking_persons[ $person_type->ID ] = absint( $_POST[ '_booking_person_' . $person_type->ID ] );
				}
			}
			update_post_meta( $post_id, '_booking_persons', $booking_persons );
		} else if ( empty( $person_types ) && ! empty( $saved_persons ) && is_array( $saved_persons ) ) {
			$booking_persons = array();
			foreach ( array_keys( $saved_persons ) as $person_id ) {
				$booking_persons[ $person_id ] = absint( $_POST[ '_booking_person_' . $person_id ] );
			}
			update_post_meta( $post_id, '_booking_persons', $booking_persons );
		}

		// Update date
		if ( empty( $_POST['booking_date'] ) ) {
			$date = current_time('timestamp');
		} else {
			$date = strtotime( $_POST['booking_date'] . ' ' . (int) $_POST['booking_date_hour'] . ':' . (int) $_POST['booking_date_minute'] . ':00' );
		}

		$date = date_i18n( 'Y-m-d H:i:s', $date );

		$wpdb->query( $wpdb->prepare( "UPDATE $wpdb->posts SET post_date = %s, post_date_gmt = %s WHERE ID = %s", $date, get_gmt_from_date( $date ), $post_id ) );

		// Do date and time magic and save them in one field
		$start_date = explode( '-', wc_clean( $_POST['booking_start_date'] ) );
		$end_date   = explode( '-', wc_clean( $_POST['booking_end_date'] ) );
		$start_time = explode( ':', wc_clean( $_POST['booking_start_time'] ) );
		$end_time   = explode( ':', wc_clean( $_POST['booking_end_time'] ) );

		$start = mktime( $start_time[0], $start_time[1], 0, $start_date[1], $start_date[2], $start_date[0] );
		$end   = mktime( $end_time[0], $end_time[1], 0, $end_date[1], $end_date[2], $end_date[0] );

		update_post_meta( $post_id, '_booking_start', date( 'YmdHis', $start ) );
		update_post_meta( $post_id, '_booking_end', date( 'YmdHis', $end ) );

		if ( $order && $booking_order_id ) {
			// Update order metas
			foreach ( $order->get_items() as $item_id => $item ) {
				if ( 'line_item' != $item['type'] || ! in_array( $product_id, $item['item_meta']['_product_id'] ) ) {
					continue;
				}

				$product    = wc_get_product( $product_id );
				$is_all_day = isset( $_POST['_booking_all_day'] ) && $_POST['_booking_all_day'] == 'yes';

				// Update date
				if ( metadata_exists( 'order_item', $item_id, __( 'Booking Date', 'woocommerce-bookings' ) ) ) {
					$date = mktime( 0, 0, 0, $start_date[1], $start_date[2], $start_date[0] );
					wc_update_order_item_meta( $item_id, __( 'Booking Date', 'woocommerce-bookings' ), date_i18n( wc_date_format(), $date ) );
				}

				// Update time
				if ( ! $is_all_day ) {
					if ( metadata_exists( 'order_item', $item_id, __( 'Booking Time', 'woocommerce-bookings' ) ) ) {
						$time = mktime( $start_time[0], $start_time[1], 0, $start_date[1], $start_date[2], $start_date[0] );
						wc_update_order_item_meta( $item_id, __( 'Booking Time', 'woocommerce-bookings' ), date_i18n( wc_time_format(), $time ) );
					}
				}

				// Update resource
				if ( metadata_exists( 'order_item', $item_id, __( 'Booking Type', 'woocommerce-bookings' ) ) ) {
					$resource = wc_booking_get_product_resource( $product_id, $resource_id );
					wc_update_order_item_meta( $item_id, __( 'Booking Type', 'woocommerce-bookings' ), $resource->get_title() );
				}

				// Update persons
				if ( $product->has_persons() ) {
					if ( $product->has_person_types() ) {
						$person_types = $product->get_person_types();

						foreach( $person_types as $type ) {
							if ( isset( $_POST['_booking_person_' . $type->ID ] ) ) {
								$persons = $_POST['_booking_person_' . $type->ID ];
								wc_update_order_item_meta( $item_id, $type->post_title, $persons );
							}
						}
					} else {
						// The product does not use person types, the ID will be always 0 and the title "Persons"
						if ( metadata_exists( 'order_item', $item_id, __( 'Persons', 'woocommerce-bookings' ) ) ) {
							$persons = $_POST['_booking_person_0'];
							wc_update_order_item_meta( $item_id, __( 'Persons', 'woocommerce-bookings' ), $persons );
						}
					}
				}

				// Update duration
				if ( metadata_exists( 'order_item', $item_id, __( 'Duration', 'woocommerce-bookings' ) ) ) {
					$start_diff = wc_clean( $_POST['booking_start_date'] );
					$end_diff   = wc_clean( $_POST['booking_end_date'] );

					if ( ! $is_all_day ) {
						$start_diff .= ' ' . wc_clean( $_POST['booking_start_time'] );
						$end_diff   .= ' ' . wc_clean( $_POST['booking_end_time'] );
					}

					$start = new DateTime( $start_diff );
					$end   = new DateTime( $end_diff );

					// Add one day because DateTime::diff does not include the last day
					if ( $is_all_day ) {
						$end->modify( '+1 day' );
					}

					$diffs = $end->diff( $start );

					$duration = array();
					foreach ( $diffs as $type => $diff ) {
						if ( $diff != 0 ) {
							switch( $type ) {
								case 'y': $duration[] = _n( '%y year', '%y years', $diff, 'woocommerce-bookings' );     break;
								case 'm': $duration[] = _n( '%m month', '%m months', $diff, 'woocommerce-bookings' );   break;
								case 'd': $duration[] = _n( '%d day', '%d days', $diff, 'woocommerce-bookings' );       break;
								case 'h': $duration[] = _n( '%h hour', '%h hours', $diff, 'woocommerce-bookings' );     break;
								case 'i': $duration[] = _n( '%i minute', '%i minutes', $diff, 'woocommerce-bookings' ); break;
							}
						}
					}

					$duration = implode( ', ', $duration );
					$duration = $diffs->format( $duration );

					wc_update_order_item_meta( $item_id, __( 'Duration', 'woocommerce-bookings' ), $duration );
				}
			}
		}

		WC_Cache_Helper::get_transient_version( 'bookings', true );

		do_action( 'woocommerce_booking_process_meta', $post_id );
	}
}

return new WC_Bookings_Details_Meta_Box();
