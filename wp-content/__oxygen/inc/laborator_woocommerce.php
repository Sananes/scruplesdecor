<?php
/**
 *	WooCommerce
 *
 *	Laborator.co
 *	www.laborator.co
 */

if( ! function_exists('woocommerce_get_page_id'))
	return;

# Constants
define('SHOPSIDEBAR', in_array(get_data('shop_sidebar'), array('Show Sidebar on Left', 'Show Sidebar on Right')));
define('SHOPSIDEBARALIGN', get_data('shop_sidebar') == 'Show Sidebar on Left' ? 'left' : 'right');

define('SHOPSINGLESIDEBAR', in_array(get_data('shop_single_sidebar'), array('Show Sidebar on Left', 'Show Sidebar on Right')));
define('SHOPSINGLESIDEBARALIGN', get_data('shop_single_sidebar') == 'Show Sidebar on Left' ? 'left' : 'right');

define('SHOPCOLUMNS', apply_filters( 'lab_wc_shop_columns', (SHOPSIDEBAR ? 3 : 4 )));

define('SHOPURL', get_permalink(translate_id(woocommerce_get_page_id('shop'))));
define('MYACCOUNTURL', get_permalink(translate_id(woocommerce_get_page_id('myaccount'))));
define('CHECKOUTURL', get_permalink(translate_id(woocommerce_get_page_id('checkout' ))));
define('CARTURL', get_permalink(translate_id(woocommerce_get_page_id('cart'))));


add_filter('woocommerce_show_page_title', 'laborator_woocommerce_show_page_title');
add_filter('option_woocommerce_enable_lightbox', create_function('', 'return false;'));
add_filter('single_product_large_thumbnail_size', create_function('', 'return "original";'));
add_filter('woocommerce_output_related_products_args', 'laborator_output_related_products_args');
add_filter('loop_shop_per_page', 'laborator_loop_shop_per_page');

#add_filter( 'single_product_large_thumbnail_size', create_function('', 'return "shop-thumb-4";') );
add_filter( 'single_product_small_thumbnail_size', create_function('', 'return "' . apply_filters('oxygen_shop_single_thumb', 'shop-thumb-4') . '";') );
add_filter( 'woocommerce_single_product_image_thumbnail_html', 'laborator_single_product_image_thumbnail_html' );

# Remove Some Actions
remove_action('woocommerce_before_main_content', 'woocommerce_breadcrumb', 20, 0);
remove_action('woocommerce_before_shop_loop', 'woocommerce_result_count', 20);
remove_action('woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30);
remove_action('woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10);
remove_action('woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5);
remove_action('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10);

remove_action('woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 15);
remove_action('woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20);


# Change Product Meta Position
remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40);
remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_price', 10);
remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_rating', 10);

add_action('woocommerce_single_product_summary', 'woocommerce_template_single_price', 25);
add_action('woocommerce_single_product_summary', 'woocommerce_template_single_meta', 35);

add_action('woocommerce_after_main_content', 'laborator_shop_sidebar_footer');

# Laborator WooCommerce Wrapper Incorporated
add_action('laborator_woocommerce_before_wrapper', 'laborator_before_wrapper');
add_action('laborator_woocommerce_after_wrapper', 'laborator_after_wrapper');


function laborator_woocommerce_show_page_title()
{
	return get_data('shop_title_show');
}

function laborator_before_wrapper()
{
	?>
	<div class="laborator-woocommerce shop">
	<?php
}

function laborator_after_wrapper()
{
	?>
	</div>
	<?php
}

function laborator_output_related_products_args($args)
{
	$args['posts_per_page'] = get_data('shop_related_products_per_page');

	return $args;
}


# Archive Product header
add_action('woocommerce_before_shop_loop', 'laborator_woocommerce_before_shop_loop');

function laborator_woocommerce_before_shop_loop()
{
	get_template_part('tpls/shop-resultscount');
}


function laborator_loop_shop_per_page()
{
	$rows_count = absint( preg_replace("/[^0-9]+/", "", get_data('shop_products_per_page')) );
	$rows_count = $rows_count > 0 ? $rows_count : 4;

	return SHOPCOLUMNS * $rows_count;
}





/* WooCommerce AJAX Actions */
add_action('wp_ajax_lab_add_to_cart', 'laborator_add_item_to_cart');
add_action('wp_ajax_nopriv_lab_add_to_cart', 'laborator_add_item_to_cart');

function laborator_add_item_to_cart()
{
	$resp = array(
		'success' => false
	);

	ob_start();

	$product_id		= apply_filters( 'woocommerce_add_to_cart_product_id', absint( $_POST['product_id'] ) );
	$quantity		  = empty( $_POST['quantity'] ) ? 1 : wc_stock_amount( $_POST['quantity'] );
	$passed_validation = apply_filters( 'woocommerce_add_to_cart_validation', true, $product_id, $quantity );

	if ( $passed_validation && WC()->cart->add_to_cart( $product_id, $quantity ) ) {

		do_action( 'woocommerce_ajax_added_to_cart', $product_id );

		$resp['success'] = true;

	} else {

		$resp['error_msg'] = wc_get_notices('error');
		wc_clear_notices();

	}

	$resp['cart_items'] = WC()->cart->get_cart_contents_count();
	$resp['cart_subtotal'] = WC()->cart->get_cart_subtotal();
	$resp['cart_html'] = laborator_get_mini_cart_contents();
	$resp['cart_html_frag'] = laborator_get_mini_cart_frag_contents();

	echo json_encode($resp);
	die();
}

function laborator_get_mini_cart_contents()
{	
	ob_start();

	$cart = array_reverse(WC()->cart->get_cart());

	foreach($cart as $cart_item_key => $cart_item):

		$product_id = $cart_item['product_id'];
		$product = new WC_Product($product_id);
		$_product     = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
		$permalink = get_permalink($product->post);

		$quantity = $cart_item['quantity'];
	?>
	<div class="col-sm-3">

		<div class="cart-item">

			<a href="<?php echo $permalink; ?>">
				<?php
				$image = '';

				if(has_post_thumbnail($product_id)):

					#echo laborator_show_img($product_id, 'shop-thumb-2');
					if($_product && method_exists($_product, 'get_image'))
						$image = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image('shop-thumb-2'), $cart_item, $cart_item_key );
					else
						$image = wp_get_attachment_image(get_post_thumbnail_id($product_id), 'shop-thumb-2');

				else:

					$attachment_ids = $product->get_gallery_attachment_ids();

					if(count($attachment_ids))
					{
						$first_img = reset($attachment_ids);
						$first_img_link = wp_get_attachment_url( $first_img );

						#echo laborator_show_img($first_img_link, 'shop-thumb-2');
						$image = wp_get_attachment_image($first_img, 'shop-thumb-2');
					}
					else
					{
						$image = laborator_show_img(wc_placeholder_img_src(), 'shop-thumb-2');
					}
				endif;

				echo apply_filters('woocommerce_cart_item_thumbnail', $image, $cart_item, $cart_item_key);
				?>
			</a>

			<div class="details">
				<a href="<?php echo $permalink; ?>" class="title"><?php echo get_the_title($product->post); ?></a>

				<div class="price-quantity">
					<?php if ( $price_html = $product->get_price_html() ) : ?>
					<span class="price"><?php echo apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key ); ?></span>
					<?php endif; ?>

					<span class="quantity"><?php _e( sprintf("Q: %d", $quantity) ); ?></span>
				</div>
			</div>
		</div>

	</div>
	<?php
	endforeach;

	return ob_get_clean();
}

function laborator_get_mini_cart_frag_contents()
{
	ob_start();
	
	$defaults = array(
		'list_class' => ''
	);

	$args = wp_parse_args( $args, $defaults );

	wc_get_template( 'cart/mini-cart.php', $args );
	
	$html = ob_get_clean();
	
	return $html;
}



# Update Shipping method [Cart]
add_action('wp_ajax_laborator_update_shipping_method', 'laborator_update_shipping_method');
add_action('wp_ajax_nopriv_laborator_update_shipping_method', 'laborator_update_shipping_method');

function laborator_update_shipping_method()
{
	check_ajax_referer( 'update-shipping-method', 'security' );

	if ( ! defined('WOOCOMMERCE_CART') ) define( 'WOOCOMMERCE_CART', true );

	$chosen_shipping_methods = WC()->session->get( 'chosen_shipping_methods' );

	if ( isset( $_POST['shipping_method'] ) && is_array( $_POST['shipping_method'] ) ) {
		foreach ( $_POST['shipping_method'] as $i => $value ) {
			$chosen_shipping_methods[ $i ] = wc_clean( $value );
		}
	}

	WC()->session->set( 'chosen_shipping_methods', $chosen_shipping_methods );

	WC()->cart->calculate_totals();

	ob_start();
	wc_cart_totals_subtotal_html();
	$cart_subtotal = ob_get_clean();

	ob_start();
	wc_cart_totals_order_total_html();
	$cart_total = ob_get_clean();

	ob_start();
	wc_cart_totals_taxes_total_html();
	$cart_vat_total = ob_get_clean();

	$resp = array(
		'subtotal' => $cart_subtotal,
		'total' => $cart_total,
		'vat_total' => $cart_vat_total
	);

	echo json_encode($resp);

	die();
}



# Update Shipping Method [Checkout]
add_action('wp_ajax_laborator_update_order_review', 'laborator_update_order_review');
add_action('wp_ajax_nopriv_laborator_update_order_review', 'laborator_update_order_review');

function laborator_update_order_review()
{
	check_ajax_referer( 'update-order-review', 'security' );

	if ( ! defined( 'WOOCOMMERCE_CHECKOUT' ) )
		define( 'WOOCOMMERCE_CHECKOUT', true );

	if ( sizeof( WC()->cart->get_cart() ) == 0 ) {
		echo '<div class="woocommerce-error">' . __( 'Sorry, your session has expired.', 'woocommerce' ) . ' <a href="' . home_url() . '" class="wc-backward">' . __( 'Return to homepage', 'woocommerce' ) . '</a></div>';
		die();
	}

	do_action( 'woocommerce_checkout_update_order_review', $_POST['post_data'] );

	$chosen_shipping_methods = WC()->session->get( 'chosen_shipping_methods' );

	if ( isset( $_POST['shipping_method'] ) && is_array( $_POST['shipping_method'] ) )
		foreach ( $_POST['shipping_method'] as $i => $value )
			$chosen_shipping_methods[ $i ] = wc_clean( $value );

	WC()->session->set( 'chosen_shipping_methods', $chosen_shipping_methods );
	WC()->session->set( 'chosen_payment_method', empty( $_POST['payment_method'] ) ? '' : $_POST['payment_method'] );

	if ( isset( $_POST['country'] ) )
		WC()->customer->set_country( $_POST['country'] );

	if ( isset( $_POST['state'] ) )
		WC()->customer->set_state( $_POST['state'] );

	if ( isset( $_POST['postcode'] ) )
		WC()->customer->set_postcode( $_POST['postcode'] );

	if ( isset( $_POST['city'] ) )
		WC()->customer->set_city( $_POST['city'] );

	if ( isset( $_POST['address'] ) )
		WC()->customer->set_address( $_POST['address'] );

	if ( isset( $_POST['address_2'] ) )
		WC()->customer->set_address_2( $_POST['address_2'] );

	if ( "yes" == get_option( 'woocommerce_ship_to_billing_address_only' ) ) {

		if ( isset( $_POST['country'] ) )
			WC()->customer->set_shipping_country( $_POST['country'] );

		if ( isset( $_POST['state'] ) )
			WC()->customer->set_shipping_state( $_POST['state'] );

		if ( isset( $_POST['postcode'] ) )
			WC()->customer->set_shipping_postcode( $_POST['postcode'] );

		if ( isset( $_POST['city'] ) )
			WC()->customer->set_shipping_city( $_POST['city'] );

		if ( isset( $_POST['address'] ) )
			WC()->customer->set_shipping_address( $_POST['address'] );

		if ( isset( $_POST['address_2'] ) )
			WC()->customer->set_shipping_address_2( $_POST['address_2'] );
	} else {

		if ( isset( $_POST['s_country'] ) )
			WC()->customer->set_shipping_country( $_POST['s_country'] );

		if ( isset( $_POST['s_state'] ) )
			WC()->customer->set_shipping_state( $_POST['s_state'] );

		if ( isset( $_POST['s_postcode'] ) )
			WC()->customer->set_shipping_postcode( $_POST['s_postcode'] );

		if ( isset( $_POST['s_city'] ) )
			WC()->customer->set_shipping_city( $_POST['s_city'] );

		if ( isset( $_POST['s_address'] ) )
			WC()->customer->set_shipping_address( $_POST['s_address'] );

		if ( isset( $_POST['s_address_2'] ) )
			WC()->customer->set_shipping_address_2( $_POST['s_address_2'] );
	}

	WC()->cart->calculate_totals();


	ob_start();
	wc_cart_totals_subtotal_html();
	$cart_subtotal = ob_get_clean();

	ob_start();
	wc_cart_totals_order_total_html();
	$cart_total = ob_get_clean();

	ob_start();
	wc_cart_totals_taxes_total_html();
	$cart_vat_total = ob_get_clean();

	$resp = array(
		'subtotal' => $cart_subtotal,
		'total' => $cart_total,
		'vat_total' => $cart_vat_total
	);

	echo json_encode($resp);

	die();

	do_action( 'woocommerce_checkout_order_review' ); // Display review order table

	die();
}


# Share
add_action('woocommerce_share', 'laborator_woocommerce_share');

function laborator_woocommerce_share()
{
	global $product;

	if( ! get_data('shop_share_product'))
		return;

	?>
	<ul class="share-product">
	<?php
	$share_product_networks = get_data('shop_share_product_networks');

	if(is_array($share_product_networks)):

		foreach($share_product_networks['visible'] as $network_id => $network):

			if($network_id == 'placebo')
				continue;

			?>
			<li>
				<?php share_story_network_link($network_id, $product->id, false); ?>
			</li>
			<?php

		endforeach;

	endif;
	?>
	</ul>
	<?php
}


# WishList Supported
function is_wishlist_supported()
{
	return function_exists('woocommerce_wishlists_get_template');
}


# YITH Wishlist Supported
function is_yith_wishlist_supported()
{
	return function_exists('yith_wishlist_constructor');
}



# Wishlist add lists to JS
add_action('woocommerce_after_shop_loop', 'laborator_woocommerce_wishlists_js');

function laborator_woocommerce_wishlists_js()
{
	if(is_wishlist_supported())
	{
		$lists = WC_Wishlists_User::get_wishlists();
		$lists_js = array();

		foreach($lists as $list)
		{
			$lists_js[] = array(
				'id'	=> $list->post->ID,
				'title' => $list->post->post_title,
				'type'  => $list->post->_wishlist_sharing
			);
		}

?>
<script type="text/javascript">
window.wishlists_list = <?php echo json_encode($lists_js); ?>;
</script>
<?php

		wc_get_template('add-to-wishlist-modal.php');
	}
}



# Custom YITH Wishlist Button for Oxygen theme
function oxygen_yith_wcwl_add_to_wishlist()
{
	global $yith_wcwl, $product;

	$product_id	   = $product->id;
	$url           = $yith_wcwl->get_wishlist_url();
	$product_type  = $product->product_type;
	$exists        = $yith_wcwl->is_product_in_wishlist( $product->id );

	$url_to_add    = $yith_wcwl->get_addtowishlist_url();

	?>
	<div class="wish-list<?php echo $exists ? ' wishlisted' : ''; ?>">
		<a href="#" class="yith-add-to-wishlist add_to_wishlist glyphicon glyphicon-heart<?php echo $exists ? ' wishlisted' : ''; ?>" data-product-id="<?php echo $product_id ?>" data-product-type="<?php echo $product_type?>"></a>
	</div>
	<?php
}






# Shop Sidebar Footer
function laborator_shop_sidebar_footer()
{

	if(get_data('shop_sidebar_footer')):
	?>
	<div class="row shop_sidebar shop-footer-sidebar">

		<?php dynamic_sidebar('shop_footer_sidebar'); ?>

	</div>
	<?php
	endif;
}


# Added in v1.2
remove_action('woocommerce_before_subcategory_title', 'woocommerce_subcategory_thumbnail', 10);
add_action('woocommerce_before_subcategory_title', 'laborator_woocommerce_subcategory_thumbnail', 10);

function laborator_woocommerce_subcategory_thumbnail( $category )
{
	global $category_image;

	$small_thumbnail_size  	= apply_filters( 'single_product_small_thumbnail_size', 'shop_catalog' );
	$dimensions				= wc_get_image_size( $small_thumbnail_size );
	$thumbnail_id  			= get_woocommerce_term_meta( $category->term_id, 'thumbnail_id', true  );

	if ( $thumbnail_id ) {
		$image = wp_get_attachment_image_src( $thumbnail_id, $small_thumbnail_size  );
		$image = $image[0];
	} else {
		$image = wc_placeholder_img_src();
	}

	if ( $image ) {
		// Prevent esc_url from breaking spaces in urls for image embeds
		// Ref: http://core.trac.wordpress.org/ticket/23605
		$image = str_replace( ' ', '%20', $image );

		echo '<img src="' . esc_url( $image ) . '" alt="' . esc_attr( $category->name ) . '" width="' . esc_attr( $dimensions['width'] ) . '" height="' . esc_attr( $dimensions['height'] ) . '" />';
	}

	$category_image = $image;
}


# Catalog Mode check
if(is_catalog_mode())
{
	remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
	remove_action( 'woocommerce_simple_add_to_cart', 'woocommerce_simple_add_to_cart', 30 );
	remove_action( 'woocommerce_grouped_add_to_cart', 'woocommerce_grouped_add_to_cart', 30 );
	remove_action( 'woocommerce_variable_add_to_cart', 'woocommerce_variable_add_to_cart', 30 );
	remove_action( 'woocommerce_external_add_to_cart', 'woocommerce_external_add_to_cart', 30 );

	if(catalog_mode_hide_prices())
	{
		remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10 );
		remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );
	}
}




function laborator_single_product_image_thumbnail_html($html)
{
	$html = str_replace('data-rel="prettyPhoto[product-gallery]"', 'data-lightbox-gallery="main-images"', $html);
	return $html;
}


# Get Cart Info
add_action('wp_ajax_lab_get_cart_info', 'laborator_woocommerce_get_cart_info');
add_action('wp_ajax_nopriv_lab_get_cart_info', 'laborator_woocommerce_get_cart_info');

function laborator_woocommerce_get_cart_info()
{
	$count	 = WC()->cart->cart_contents_count;
	$cart_html = laborator_get_mini_cart_contents();
	$cart_html_frag = laborator_get_mini_cart_frag_contents();

	if( ! $count)
	{
		$cart_html = '<div class="no-items">' . __('Your cart is empty!', 'oxygen') . '</div>';
	}

	$resp = array(
		'count'		   => $count,
		'cart_html'	   => $cart_html,
		'cart_html_frag' => $cart_html_frag, 
		'cart_subtotal'   => WC()->cart->get_cart_subtotal()
	);

	echo json_encode($resp);
	die();
}

# Payment Method title
add_action('woocommerce_review_order_before_payment', 'laborator_woocommerce_review_order_before_payment');
add_action('woocommerce_review_order_after_payment', 'laborator_woocommerce_review_order_after_payment');

function laborator_woocommerce_review_order_before_payment()
{
	?>
	<div class="white-block block-pad cart-env checkout-cart-env">
	<h4 id="order_review_payment_method" class="with-divider"><?php _e( 'Payment method', 'oxygen' ); ?></h4>
	<?php
}

function laborator_woocommerce_review_order_after_payment()
{
	?>
	</div>
	<?php
}


# Remove WooCommerce styles and scripts.
function laborator_woocommerce_remove_lightboxes()
{
	// Styles
	wp_dequeue_style( 'woocommerce_prettyPhoto_css' );

	// Scripts
	wp_dequeue_script( 'prettyPhoto' );
	wp_dequeue_script( 'prettyPhoto-init' );
	wp_dequeue_script( 'fancybox' );
	wp_dequeue_script( 'enable-lightbox' );
}

add_action( 'wp_enqueue_scripts', 'laborator_woocommerce_remove_lightboxes', 99 );