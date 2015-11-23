<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 *	NM Widget: Price Filter List
 *
 *	Note: This is a modified version of the "WooCommerce Price Filer" widget - All custom code is placed within "//NM" comments
 */
 class NM_WC_Widget_Price_Filter extends WC_Widget {

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->widget_cssclass    = 'nm_widget nm_widget_price_filter woocommerce widget_price_filter';
		$this->widget_description = __( 'Shows a price filter list in a widget which lets you narrow down the list of shown products when viewing product categories.', 'woocommerce' );
		$this->widget_id          = 'nm_woocommerce_price_filter';
		$this->widget_name        = __( 'WooCommerce Price Filter List', 'woocommerce' );
		$this->settings           = array(
			'title' => array(
				'type'  => 'text',
				'std'   => __( 'Filter by price', 'woocommerce' ),
				'label' => __( 'Title', 'woocommerce' )
			),
			'price_range_size' => array(
				'type'  => 'number',
				'step'  => 1,
				'min'   => 1,
				'max'   => '',
				'std'   => 50,
				'label' => __( 'Price range size', 'nm-framework-admin' )
			),
			'max_price_ranges' => array(
				'type'  => 'number',
				'step'  => 1,
				'min'   => 1,
				'max'   => '',
				'std'   => 10,
				'label' => __( 'Max price ranges', 'nm-framework-admin' )
			),
			'hide_empty_ranges' => array(
				'type'  => 'checkbox',
				'std'   => 1,
				'label' => __( 'Hide empty price ranges', 'nm-framework-admin' )
			)
		);
		
		parent::__construct();
	}

	/**
	 * widget function.
	 *
	 * @see WP_Widget
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {
		global $_chosen_attributes, $wpdb, $wp;

		extract( $args );

		if ( ! is_post_type_archive( 'product' ) && ! is_tax( get_object_taxonomies( 'product' ) ) ) {
			return;
		}

		if ( sizeof( WC()->query->unfiltered_product_ids ) == 0 ) {
			return; // None shown - return
		}

		$min_price = isset( $_GET['min_price'] ) ? esc_attr( $_GET['min_price'] ) : '';
		$max_price = isset( $_GET['max_price'] ) ? esc_attr( $_GET['max_price'] ) : '';
		
		// NM
		//wp_enqueue_script( 'wc-price-slider' );
		// /NM

		$title  = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base );

		// Remember current filters/search
		// NM
		//$fields = '';
		// /NM
		
		// NM (copied from line 201)
		if ( get_option( 'permalink_structure' ) == '' ) {
			$link = remove_query_arg( array( 'page', 'paged' ), add_query_arg( $wp->query_string, '', home_url( $wp->request ) ) );
		} else {
			$link = preg_replace( '%\/page/[0-9]+%', '', home_url( $wp->request ) );
		}
		// /NM
		
		if ( get_search_query() ) {
			// NM
			//$fields .= '<input type="hidden" name="s" value="' . get_search_query() . '" />';
			$link = add_query_arg( 's', get_search_query(), $link );
			// /NM
		}

		if ( ! empty( $_GET['post_type'] ) ) {
			// NM
			//$fields .= '<input type="hidden" name="post_type" value="' . esc_attr( $_GET['post_type'] ) . '" />';
			$link = add_query_arg( 'post_type', esc_attr( $_GET['post_type'] ), $link );
			// /NM
		}

		if ( ! empty ( $_GET['product_cat'] ) ) {
			// NM
			//$fields .= '<input type="hidden" name="product_cat" value="' . esc_attr( $_GET['product_cat'] ) . '" />';
			$link = add_query_arg( 'product_cat', esc_attr( $_GET['product_cat'] ), $link );
			// /NM
		}

		if ( ! empty( $_GET['product_tag'] ) ) {
			// NM
			//$fields .= '<input type="hidden" name="product_tag" value="' . esc_attr( $_GET['product_tag'] ) . '" />';
			$link = add_query_arg( 'product_tag', esc_attr( $_GET['product_tag'] ), $link );
			// /NM
		}

		if ( ! empty( $_GET['orderby'] ) ) {
			// NM
			//$fields .= '<input type="hidden" name="orderby" value="' . esc_attr( $_GET['orderby'] ) . '" />';
			$link = add_query_arg( 'orderby', esc_attr( $_GET['orderby'] ), $link );
			// /NM
		}

		if ( $_chosen_attributes ) {
			foreach ( $_chosen_attributes as $attribute => $data ) {
				$taxonomy_filter = 'filter_' . str_replace( 'pa_', '', $attribute );
	
				// NM
				//$fields .= '<input type="hidden" name="' . esc_attr( $taxonomy_filter ) . '" value="' . esc_attr( implode( ',', $data['terms'] ) ) . '" />';
				$link = add_query_arg( esc_attr( $taxonomy_filter ), esc_attr( implode( ',', $data['terms'] ) ), $link );
				// /NM
				
				if ( 'or' == $data['query_type'] ) {
					// NM
					//$fields .= '<input type="hidden" name="' . esc_attr( str_replace( 'pa_', 'query_type_', $attribute ) ) . '" value="or" />';
					$link = add_query_arg( esc_attr( str_replace( 'pa_', 'query_type_', $attribute ) ), 'or', $link );
					// /NM
				}
			}
		}

		$min = $max = 0;
		$post_min = $post_max = '';
		
		if ( 0 === sizeof( WC()->query->layered_nav_product_ids ) ) {
			$min = floor( $wpdb->get_var( "
				SELECT min(meta_value + 0)
				FROM {$wpdb->posts} as posts
				LEFT JOIN {$wpdb->postmeta} as postmeta ON posts.ID = postmeta.post_id
				WHERE meta_key IN ('" . implode( "','", array_map( 'esc_sql', apply_filters( 'woocommerce_price_filter_meta_keys', array( '_price', '_min_variation_price' ) ) ) ) . "')
				AND meta_value != ''
			" ) );
			$max = ceil( $wpdb->get_var( "
				SELECT max(meta_value + 0)
				FROM {$wpdb->posts} as posts
				LEFT JOIN {$wpdb->postmeta} as postmeta ON posts.ID = postmeta.post_id
				WHERE meta_key IN ('" . implode( "','", array_map( 'esc_sql', apply_filters( 'woocommerce_price_filter_meta_keys', array( '_price' ) ) ) ) . "')
			" ) );
		} else {
			$min = floor( $wpdb->get_var( "
				SELECT min(meta_value + 0)
				FROM {$wpdb->posts} as posts
				LEFT JOIN {$wpdb->postmeta} as postmeta ON posts.ID = postmeta.post_id
				WHERE meta_key IN ('" . implode( "','", array_map( 'esc_sql', apply_filters( 'woocommerce_price_filter_meta_keys', array( '_price', '_min_variation_price' ) ) ) ) . "')
				AND meta_value != ''
				AND (
					posts.ID IN (" . implode( ',', array_map( 'absint', WC()->query->layered_nav_product_ids ) ) . ")
					OR (
						posts.post_parent IN (" . implode( ',', array_map( 'absint', WC()->query->layered_nav_product_ids ) ) . ")
						AND posts.post_parent != 0
					)
				)
			" ) );
			$max = ceil( $wpdb->get_var( "
				SELECT max(meta_value + 0)
				FROM {$wpdb->posts} as posts
				LEFT JOIN {$wpdb->postmeta} as postmeta ON posts.ID = postmeta.post_id
				WHERE meta_key IN ('" . implode( "','", array_map( 'esc_sql', apply_filters( 'woocommerce_price_filter_meta_keys', array( '_price' ) ) ) ) . "')
				AND (
					posts.ID IN (" . implode( ',', array_map( 'absint', WC()->query->layered_nav_product_ids ) ) . ")
					OR (
						posts.post_parent IN (" . implode( ',', array_map( 'absint', WC()->query->layered_nav_product_ids ) ) . ")
						AND posts.post_parent != 0
					)
				)
			" ) );
		}

		if ( $min == $max ) {
			return;
		}

		echo $before_widget . $before_title . $title . $after_title;
		
		// NM
		/*if ( get_option( 'permalink_structure' ) == '' ) {
			$form_action = remove_query_arg( array( 'page', 'paged' ), add_query_arg( $wp->query_string, '', home_url( $wp->request ) ) );
		} else {
			$form_action = preg_replace( '%\/page/[0-9]+%', '', home_url( $wp->request ) );
		}*/
		// /NM
		
		// NM
		/*echo '<form method="get" action="' . esc_attr( $form_action ) . '">
			<div class="price_slider_wrapper">
				<div class="price_slider" style="display:none;"></div>
				<div class="price_slider_amount">
					<input type="text" id="min_price" name="min_price" value="' . esc_attr( $min_price ) . '" data-min="'.esc_attr( apply_filters( 'woocommerce_price_filter_widget_amount', $min ) ).'" placeholder="'.__('Min price', 'woocommerce' ).'" />
					<input type="text" id="max_price" name="max_price" value="' . esc_attr( $max_price ) . '" data-max="'.esc_attr( apply_filters( 'woocommerce_price_filter_widget_amount', $max ) ).'" placeholder="'.__( 'Max price', 'woocommerce' ).'" />
					<button type="submit" class="button">'.__( 'Filter', 'woocommerce' ).'</button>
					<div class="price_label" style="display:none;">
						'.__( 'Price:', 'woocommerce' ).' <span class="from"></span> &mdash; <span class="to"></span>
					</div>
					' . $fields . '
					<div class="clear"></div>
				</div>
			</div>
		</form>';*/
		$count = 0;
		$range_size = intval( $instance['price_range_size'] );
		$max_ranges = ( intval( $instance['max_price_ranges'] ) - 1 );
		
		$output = '<ul class="nm-price-filter">';
		        
		if ( strlen( $min_price ) > 0 ) {
			$output .= '<li><a href="' . esc_url( $link ) . '">' . esc_html__( 'All', 'nm-framework' ) . '</a></li>';
		} else {
            $output .= '<li class="current">' . esc_html__( 'All', 'nm-framework' ) . '</li>';
		}
		
		for ( $range_min = 0; $range_min < ( $max + $range_size ); $range_min += $range_size ) {
			$range_max = $range_min + $range_size;
			
			// Hide empty price ranges?
			if ( intval( $instance['hide_empty_ranges'] ) ) {
				// Are there products in this price range?
				if ( $min > $range_max || ( $max + $range_size ) < $range_max ) {
					continue;
				}
			}
			
			$count++;
			
			$min_price_output = wc_price( $range_min );
			
			if ( $count == $max_ranges ) {
				$price_output = $min_price_output . '+';
				
				if ( $range_min != $min_price ) {
					$url = add_query_arg( array( 'min_price' => $range_min, 'max_price' => $max ), $link );
					$output .= '<li><a href="' . esc_url( $url ) . '">' . $price_output . '</a></li>';
				} else {
					$output .= '<li class="current">' . $price_output . '</li>';
				}
				
				break; // Max price ranges limit reached, break loop
			} else {
				$price_output = $min_price_output . ' - ' . wc_price( $range_min + $range_size );
				
				if ( $range_min != $min_price || $range_max != $max_price ) {
					$url = add_query_arg( array( 'min_price' => $range_min, 'max_price' => $range_max ), $link );
					$output .= '<li><a href="' . esc_url( $url ) . '">' . $price_output . '</a></li>';
				} else {
					$output .= '<li class="current">' . $price_output . '</li>';
				}
			}
		}
		
		echo $output . '</ul>';
		// /NM

		echo $after_widget;
	}
}
