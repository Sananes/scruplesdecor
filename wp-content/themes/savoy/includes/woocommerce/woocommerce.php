<?php
	
	/* WooCommerce
	=============================================================== */
	
	global $nm_theme_options;
	
	
	
	/* Disable default WooCommerce styles */
	add_filter( 'woocommerce_enqueue_styles', '__return_false' );
	
	
	
	/*
	 *	Set image dimensions on theme activation
	 */
	if ( ! function_exists( 'nm_woocommerce_set_image_dimensions' ) ) {
		function nm_woocommerce_set_image_dimensions() {
			if ( ! get_option( 'nm_shop_image_sizes_set' ) ) {
				$catalog = array(
					'width' 	=> '350',	// px
					'height'	=> '',		// px
					'crop'		=> 0 		// no-crop
				);
				
				$single = array(
					'width' 	=> '595',	// px
					'height'	=> '',		// px
					'crop'		=> 0 		// no-crop
				);
				
				$thumbnail = array(
					'width' 	=> '',		// px
					'height'	=> '127',	// px
					'crop'		=> 0 		// no-crop
				);
				
				// Image sizes
				update_option( 'shop_catalog_image_size', $catalog ); 		// Product category thumbs
				update_option( 'shop_single_image_size', $single ); 		// Single product image
				update_option( 'shop_thumbnail_image_size', $thumbnail ); 	// Image gallery thumbs
				
				// Set "images size set" option
				add_option( 'nm_shop_image_sizes_set', '1' );
			}
		}
	}
	// Theme activation hook
	add_action( 'after_switch_theme', 'nm_woocommerce_set_image_dimensions', 1 );
	// Additional hook for when WooCommerce is installed/activated after the theme
	add_action( 'admin_init', 'nm_woocommerce_set_image_dimensions', 1000 );
	
	
	
	/*
	 *	Set image dimensions on theme activation
	 */
	if ( ! function_exists( 'nm_woocommerce_add_image_sizes' ) ) {
		function nm_woocommerce_add_image_sizes() {
			// Add image size: Quick view
			add_image_size( 'nm_quick_view', 680, '', true );
		}
	}
	add_action( 'after_setup_theme', 'nm_woocommerce_add_image_sizes' );
	
	
	
	/* Default checkout page */
	if ( defined( 'NM_SHOP_DEFAULT_CHECKOUT' ) ) {
		/*
		 *	Disable custom template path
		 */
		function nm_woocommerce_disable_template_path() {
			// Returning an invalid template-path will ensure the default WooCommerce templates are used
			return 'woocommerce-disable/';
		}
		
		/*
		 *	Checkout: Disable custom checkout templates
		 */
		function nm_woocommerce_disable_custom_checkout_templates() {
			if ( is_checkout() ) {
				add_filter( 'woocommerce_template_path', 'nm_woocommerce_disable_template_path' );
			}
		}
		add_action( 'wp', 'nm_woocommerce_disable_custom_checkout_templates' );
	}
	
	
	
	/*
	 *	AJAX "add to cart" redirect: Include custom template
	 */
	function nm_ajax_add_to_cart_redirect_template() {
		if ( isset( $_REQUEST['nm-ajax-add-to-cart'] ) ) {
			wc_get_template( 'ajax-add-to-cart-fragments.php' );
			exit;
		}
	}
	add_action( 'wp', 'nm_ajax_add_to_cart_redirect_template', 1000 );
	
	
	
	if ( get_option( 'woocommerce_cart_redirect_after_add' ) != 'yes' ) { // Only show cart panel if redirect is disabled
		/*
		 *	After static add-to-cart, add body class so the cart panel will show after page-load/redirect
		 */
		function nm_add_to_cart_class() {
			// Add a class to the <body> tag so it can be checked with JS
			global $nm_body_class;
			$nm_body_class .= ' nm-added-to-cart';
		}
		add_action( 'woocommerce_add_to_cart', 'nm_add_to_cart_class' );
	}
	
	
	
	/* WooCommerce 3.8 removed the 'Trailing Zeros' setting. Note: add a theme setting? */
	add_filter( 'woocommerce_price_trim_zeros', '__return_false' );
	
	
	
	/* Products per page */
	$products_per_page = ( strlen( $nm_theme_options['products_per_page'] ) > 0 ) ? intval( $nm_theme_options['products_per_page'] ) : 12;
	add_filter( 'loop_shop_per_page', create_function( '$cols', 'return ' . $products_per_page . ';' ), 20 );
	
	
	
	/*
	 *	Get cart contents count
	 */
	function nm_get_cart_contents_count() {
		$cart_count = intval( WC()->cart->cart_contents_count );
		
		return '<span class="nm-menu-cart-count count">' . $cart_count . '</span>';
	}
	
	
	
	/*
	 *	Show shop notices
	 */
	function nm_print_shop_notices() {
		echo '<div id="nm-shop-notices-wrap">';
		wc_print_notices();
		echo '</div>';
	}
	
	
	
	/*
	 *	Related products per page (single product)
	 */
	function nm_related_products_args( $args ) {
		global $nm_theme_options;
		
		$args['posts_per_page'] = intval( $nm_theme_options['shop_columns'] );
		$args['columns'] = 1;
		//$args['orderby'] = 'rand'; // Note: Use to change product order
		return $args;
	}
	add_filter( 'woocommerce_output_related_products_args', 'nm_related_products_args' );
	
	
	
	/*
	 *	Get updated header cart fragment
	 */
	if ( ! function_exists( 'nm_header_add_to_cart_fragment' ) ) {
		function nm_header_add_to_cart_fragment( $fragments ) {
			global $woocommerce;
			
			ob_start(); ?>
			<span class="nm-menu-cart-count count"><?php echo WC()->cart->cart_contents_count; ?></span>
			<?php
			$fragments['.nm-menu-cart-count'] = ob_get_clean();
			
			return $fragments;
		}
	}
	add_filter( 'add_to_cart_fragments', 'nm_header_add_to_cart_fragment' ); // Ensure cart contents update when products are added to the cart via Ajax
	
	
	
	/*
	 *	Mini cart - AJAX: Remove product from cart
	 */
	function nm_mini_cart_remove_product() {
		$cart_item_key = $_POST['cart_item_key'];
		
		$cart = WC()->instance()->cart;
		$removed = $cart->remove_cart_item( $cart_item_key ); // Note: WP 2.3 >
		
		if ( $removed )	{
		   $json_array['status'] = '1';
		   $json_array['cart_count'] = $cart->get_cart_contents_count();
		   $json_array['cart_subtotal'] = $cart->get_cart_subtotal();
		} else {
			$json_array['status'] = '0';
		}
		
		echo json_encode( $json_array );
				
		exit;
	}
	add_action( 'wp_ajax_nm_mini_cart_remove_product' , 'nm_mini_cart_remove_product' );
	add_action( 'wp_ajax_nopriv_nm_mini_cart_remove_product', 'nm_mini_cart_remove_product' );
	
	
	
	/*
	 *	Category menu: Create single category list HTML 
	 */
	function nm_category_menu_create_list( $category, $current_cat_id, $categories_menu_divider ) {
		$output = '<li class="cat-item-' . $category->term_id;
						
		if ( $current_cat_id == $category->term_id ) {
			$output .= ' current-cat';
		}
		
		$output .=  '">' . $categories_menu_divider . '<a href="' . esc_url( get_term_link( (int) $category->term_id, 'product_cat' ) ) . '">' . esc_attr( $category->name ) . '</a></li>';
		
		return $output;
	}
	
	
	
	/*
	 *	Product category menu
	 */
	function nm_category_menu() {
		global $wp_query, $nm_theme_options;
		
		$current_cat_id = ( is_tax( 'product_cat' ) ) ? $wp_query->queried_object->term_id : '';
		$is_category = ( strlen( $current_cat_id ) > 0 ) ? true : false;
		
		// Should top-level categories be displayed?
		if ( $nm_theme_options['shop_categories_top_level'] == '0' && $is_category ) {
			nm_sub_category_menu_output( $current_cat_id );
		} else {
			nm_category_menu_output( $is_category, $current_cat_id );
		}
	}
	
		
	
	/*
	 *	Product category menu: Output
	 */
	function nm_category_menu_output( $is_category, $current_cat_id ) {
		global $wp_query, $nm_theme_options;
		
		$page_id = wc_get_page_id( 'shop' );
		$page_url = get_permalink( $page_id );
		$hide_sub = true;
		$all_categories_class = '';
		
		// Is this a category page?																
		if ( $is_category ) {
			$hide_sub = false;
			
			// Get current category's direct children
			$direct_children = get_terms( 'product_cat',
				array(
					'fields'       	=> 'ids',
					'parent'       	=> $current_cat_id,
					'hierarchical'	=> true,
					'hide_empty'   	=> false
				)
			);
			
			$category_has_children = ( empty( $direct_children ) ) ? false : true;
		} else {
			// No current category, set "All" as current (if not product tag archive or search)
			if ( ! is_product_tag() && ! isset( $_REQUEST['s'] ) ) {
				$all_categories_class = ' class="current-cat"';
			}
		}
		
		$output = '<li' . $all_categories_class . '><a href="' . esc_url ( $page_url ) . '">' . esc_html__( 'All', 'nm-framework' ) . '</a></li>';
		$sub_output = '';
		
		// Categories order
		$orderby = 'slug';
		$order = 'asc';
		if ( isset( $nm_theme_options['shop_categories_orderby'] ) ) {
			$orderby = $nm_theme_options['shop_categories_orderby'];
			$order = $nm_theme_options['shop_categories_order'];
		}
		
		$categories = get_categories( array(
			'type'			=> 'post',
			'orderby'		=> $orderby, // Note: 'name' sorts by product category "menu/sort order"
			'order'			=> $order,
			'hide_empty'	=> 0,
			'hierarchical'	=> 1,
			'taxonomy'		=> 'product_cat'
		) );
		
		// Categories menu divider
		$categories_menu_divider = apply_filters( 'nm_shop_categories_divider', '<span>&frasl;</span>' );
										
		foreach( $categories as $category ) {
			// Is this a sub-category?
			if ( $category->parent != '0' ) {
				// Should sub-categories be included?
				if ( $hide_sub ) {
					continue;
				} else {
					if ( 
						$category->term_id == $current_cat_id || // Include current sub-category
						$category->parent == $current_cat_id || // Include current sub-category's children
						! $category_has_children && $category->parent == $wp_query->queried_object->parent // Include categories with the same parent (if current sub-category doesn't have children)
					) {
						$sub_output .= nm_category_menu_create_list( $category, $current_cat_id, $categories_menu_divider );
					}
					continue;
				}
			}
			
			$output .= nm_category_menu_create_list( $category, $current_cat_id, $categories_menu_divider );
		}
		
		if ( strlen( $sub_output ) > 0 ) {
			$sub_output = '<ul class="nm-shop-sub-categories">' . $sub_output . '</ul>';
		}
		
		$output = $output . $sub_output;
		
		echo $output;
	}
	
	
	
	/*
	 *	Product category menu: Output sub-categories
	 */
	function nm_sub_category_menu_output( $current_cat_id ) {
		global $wp_query;
		
		// Categories menu divider
		$categories_menu_divider = apply_filters( 'nm_shop_categories_divider', '<span>&frasl;</span>' );
		
		$output_sub_categories = '';
		
		// Categories order
		$orderby = 'slug';
		$order = 'asc';
		if ( isset( $nm_theme_options['shop_categories_orderby'] ) ) {
			$orderby = $nm_theme_options['shop_categories_orderby'];
			$order = $nm_theme_options['shop_categories_order'];
		}
		
		$sub_categories = get_categories( array(
			'type'			=> 'post',
			'parent'       	=> $current_cat_id,
			'orderby'		=> $orderby, // Note: 'name' sorts by product category "menu/sort order"
			'order'			=> $order,
			'hide_empty'	=> 0,
			'hierarchical'	=> 1,
			'taxonomy'		=> 'product_cat'
		) );
		
		$has_sub_categories = ( empty( $sub_categories ) ) ? false : true;
		
		// Is there any sub-categories available
		if ( $has_sub_categories ) {
			$current_cat_name = __( 'All', 'nm-framework' );
			
			foreach( $sub_categories as $sub_category ) {
				$output_sub_categories .= nm_category_menu_create_list( $sub_category, $current_cat_id, $categories_menu_divider );
			}
		} else {
			$current_cat_name = $wp_query->queried_object->name;
		}
		
		// Parent category link
		$parent_cat_id = $wp_query->queried_object->parent;
		if ( $parent_cat_id ) {
			$parent_cat_url = get_term_link( (int) $parent_cat_id, 'product_cat' );
			$output_parent_cat = '<li class="nm-category-back-button"><a href="' . esc_url( $parent_cat_url ) . '"><i class="nm-font nm-font-arrow-left"></i> ' . esc_html__( 'Back', 'nm-framework' ) . '</a>' . $categories_menu_divider . '</li>';
		} else {
			$output_parent_cat = '';
		}
		
		// Current category link
		$current_cat_url = get_term_link( (int) $current_cat_id, 'product_cat' );
		$output_current_cat = '<li class="current-cat"><a href="' . esc_url( $current_cat_url ) . '">' . esc_html( $current_cat_name ) . '</a></li>';
		
		echo $output_parent_cat . $output_current_cat . $output_sub_categories;
	}
	
	
	
	/*
	 *	Product: Get sale percentage
	 */
	function nm_product_get_sale_percent( $product ) {
		if ( $product->product_type === 'variable' ) {
			// Get product variation prices (regular and sale)
			$product_variation_prices = $product->get_variation_prices();
			
			$highest_sale_percent = 0;
			
			foreach( $product_variation_prices['regular_price'] as $key => $regular_price ) {
				// Get sale price for current variation
				$sale_price = $product_variation_prices['sale_price'][$key];
				
				// Is product variation on sale?
				if ( $sale_price < $regular_price ) {
					$sale_percent = round( ( ( $regular_price - $sale_price ) / $regular_price ) * 100 );
					
					// Is current sale percent highest?
					if ( $sale_percent > $highest_sale_percent ) {
						$highest_sale_percent = $sale_percent;
					}
				}
			}
			
			// Return the highest product variation sale percent
			return $highest_sale_percent;
		} else {
			$sale_percent = 0;
			
			// Make sure the percentage value can be calculated
			if ( intval( $product->regular_price ) > 0 ) {
				$sale_percent = round( ( ( $product->regular_price - $product->sale_price ) / $product->regular_price ) * 100 );
			}
			
			return $sale_percent;
		}
	}
	
	
	
	/* Change the default WooCommerce 'spinner' image */
	/*function nm_custom_wc_spinner() {
		return '/img/loader-dots.gif';
	}
	add_filter( 'woocommerce_ajax_loader_url', 'nm_custom_wc_spinner' );*/
		
	
	
	/*
	 *	Add custom checkout field values
	 */
	/*function nm_custom_checkout_fields( $fields ) {
		$fields['billing']['billing_first_name']['placeholder']		= __( 'First name', 'nm-framework' );
		$fields['billing']['billing_last_name']['placeholder']		= __( 'Last name', 'nm-framework' );
		$fields['billing']['billing_company']['placeholder']		= __( 'Company name (optional)', 'nm-framework' );
		$fields['billing']['billing_address_2']['label']			= '&nbsp;';
		$fields['billing']['billing_email']['placeholder']			= __( 'Email address', 'nm-framework' );
		$fields['billing']['billing_phone']['placeholder']			= __( 'Phone', 'nm-framework' );
		
		$fields['shipping']['shipping_first_name']['placeholder']	= __( 'First name', 'nm-framework' );
		$fields['shipping']['shipping_last_name']['placeholder']	= __( 'Last name', 'nm-framework' );
		$fields['shipping']['shipping_company']['placeholder']		= __( 'Company name (optional)', 'nm-framework' );
		$fields['shipping']['shipping_address_2']['label']			= '&nbsp;';
		 
		return $fields;
	}
	add_filter( 'woocommerce_checkout_fields' , 'nm_custom_checkout_fields' );*/
	
	
	/*
	 *	Product tabs: Disable "Reviews" tab
	 */
	if ( ! $nm_theme_options['product_reviews'] ) {
		function nm_woocommerce_remove_reviews_tab( $tabs ) {
			unset( $tabs['reviews'] );
			return $tabs;
		}
		add_filter( 'woocommerce_product_tabs', 'nm_woocommerce_remove_reviews_tab', 98 );
	}
	
	
	/*
	 *	Product tabs: Change "Reviews" tab title
	 */
	function nm_woocommerce_reviews_tab_title( $title ) {
		$title = strtr( $title, array( 
			'(' => '<span>',
			')' => '</span>' 
		) );
		
		return $title;
	}
	add_filter( 'woocommerce_product_reviews_tab_title', 'nm_woocommerce_reviews_tab_title' );
	