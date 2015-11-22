<?php
/**
 *	Aurum WordPress Theme
 *
 *	Laborator.co
 *	www.laborator.co
 */

if( ! in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins'))))
	return;

# Shop Constants
define("SHOP_SIDEBAR", get_data('shop_sidebar') != 'hide');

$shop_columns = SHOP_SIDEBAR ? 3 : 4;

switch(get_data('shop_product_columns'))
{
	case "six":
		$shop_columns = 6;
		break;

	case "five":
		$shop_columns = 5;
		break;

	case "four":
		$shop_columns = 4;
		break;

	case "three":
		$shop_columns = 3;
		break;

	case "two":
		$shop_columns = 2;
		break;
}
define("SHOP_COLUMNS", $shop_columns);

define("SHOP_SINGLE_SIDEBAR", get_data('shop_single_sidebar') != 'hide');


# Remove Actions
remove_action( 'woocommerce_before_main_content', 			'woocommerce_breadcrumb', 20, 0 );
remove_action( 'woocommerce_before_shop_loop', 				'woocommerce_result_count', 20 );
remove_action( 'woocommerce_before_shop_loop', 				'woocommerce_catalog_ordering', 30 );
remove_action( 'woocommerce_before_shop_loop_item_title', 	'woocommerce_template_loop_product_thumbnail', 10 );
remove_action( 'woocommerce_after_shop_loop', 				'woocommerce_pagination', 10 );
remove_action( 'woocommerce_after_single_product_summary', 	'woocommerce_upsell_display', 15 );
remove_action( 'woocommerce_after_single_product_summary', 	'woocommerce_output_related_products', 20 );
remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_sale_flash', 10 );
remove_action( 'woocommerce_before_subcategory_title', 		'woocommerce_subcategory_thumbnail', 10 );

remove_action( 'woocommerce_before_checkout_form', 'woocommerce_checkout_login_form', 10 );
remove_action( 'woocommerce_before_checkout_form', 'woocommerce_checkout_coupon_form', 10 );
remove_action( 'woocommerce_before_shop_loop', 'wc_print_notices', 10 );

add_action( 'woocommerce_before_main_content', 'wc_print_notices', 10 );


# Custom Filters & Actions
add_filter( 'loop_shop_per_page', 'laborator_woocommerce_loop_shop_per_page' );

add_filter( 'single_product_large_thumbnail_size', create_function('', 'return "shop-thumb-main";') );
add_filter( 'single_product_small_thumbnail_size', create_function('', 'return "shop-thumb-main";') );
add_filter( 'option_woocommerce_enable_lightbox', create_function('', 'return "no";') );
add_filter( 'woocommerce_single_product_image_thumbnail_html', 'laborator_single_product_image_thumbnail_html' );

add_filter( 'woocommerce_product_review_list_args', 'laborator_woocommerce_reviews_list_comments_arr' );

	# Move Price Below description
	remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );
	add_filter( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 25 );

	# Remove add to cart on "Catalog mode"
	if(get_data('shop_catalog_mode'))
		remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30);

	# Remove product meta
	if(get_data('shop_single_meta_show') == false)
		remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40);

	# Share Item
	if(get_data('shop_share_product'))
		add_action('woocommerce_share', 'laborator_woocommerce_share');

	# Related Products
	add_filter('woocommerce_output_related_products_args', 'laborator_woocommerce_related_products_args');


# Wrapping Custom Pages
add_action('woocommerce_before_template_part', 'laborator_before_template_part');
add_action('woocommerce_after_template_part', 'laborator_after_template_part');


# Before Wrapper
function laborator_woocommerce_before()
{
	?>
	<section class="shop<?php echo is_single() ? ' shop-item-single' : ''; ?>">
		<div class="container">

	<?php
}

# After Wrapper
function laborator_woocommerce_after()
{
	?>
		</div>
	</section>
	<?php
}

# Products per Page
function laborator_woocommerce_loop_shop_per_page()
{
	$rows_count = absint(get_data('shop_products_per_page'));
	$rows_count = $rows_count > 0 ? $rows_count : 4;

	return SHOP_COLUMNS * $rows_count;
}


# AJAX add to cart
add_action('wp_ajax_lab_wc_add_to_cart', 'laborator_woocommerce_ajax_add_to_cart');
add_action('wp_ajax_nopriv_lab_wc_add_to_cart', 'laborator_woocommerce_ajax_add_to_cart');

function laborator_woocommerce_ajax_add_to_cart()
{
	$resp = array(
		'success' => false
	);

	ob_start();

	$product_id        = apply_filters( 'woocommerce_add_to_cart_product_id', absint( $_POST['product_id'] ) );
	$quantity          = empty( $_POST['quantity'] ) ? 1 : wc_stock_amount( $_POST['quantity'] );
	$passed_validation = apply_filters( 'woocommerce_add_to_cart_validation', true, $product_id, $quantity );

	if ( $passed_validation && WC()->cart->add_to_cart( $product_id, $quantity ) ) {

		do_action( 'woocommerce_ajax_added_to_cart', $product_id );

		$resp['success'] = true;

	} else {

		$resp['error_msg'] = wc_get_notices('error');
		wc_clear_notices();
	}


	$resp['cart_items']    = WC()->cart->get_cart_contents_count();
	$resp['cart_subtotal'] = WC()->cart->get_cart_subtotal();
	$resp['cart_html']     = laborator_woocommerce_get_mini_cart_contents();

	echo json_encode($resp);

	die();
}


function laborator_woocommerce_get_mini_cart_contents()
{
	ob_start();

	get_template_part('tpls/woocommerce-mini-cart');

	return ob_get_clean();
}


# Review List Comments Array
function laborator_woocommerce_reviews_list_comments_arr($args)
{
	$args['end-callback'] = 'laborator_woocommerce_reviews_list_comments_end_cb';
	return $args;
}

function laborator_woocommerce_reviews_list_comments_end_cb()
{
	echo '</div>';
}





# Share Product Item
function laborator_woocommerce_share()
{
	global $product;

	?>
	<div class="share-post">
		<h3><?php _e('Share this item:', TD); ?></h3>
		<div class="share-product share-post-links list-unstyled list-inline">
		<?php
		$share_product_networks = get_data('shop_share_product_networks');

		if(is_array($share_product_networks)):

			foreach($share_product_networks['visible'] as $network_id => $network):

				if($network_id == 'placebo')
					continue;

				share_story_network_link($network_id, $product->id, false);

			endforeach;

		endif;
		?>
		</div>
	</div>
	<?php
}



# Related Product Counts
function laborator_woocommerce_related_products_args($args)
{
	$args['posts_per_page']    = get_data('shop_related_products_per_page');
	$args['columns']           = $args['posts_per_page'];

	return $args;
}



# Content Wrappers
global $laborator_woocommerce_wrap_pages;

$laborator_woocommerce_wrap_pages = array(
	'cart/cart.php',
	'checkout/form-checkout.php',
	'myaccount/form-login.php',
	'myaccount/my-account.php',
	'myaccount/form-edit-address.php',
	'myaccount/form-edit-account.php',
	'myaccount/form-lost-password.php',
	'myaccount/view-order.php',
	'checkout/thankyou.php',
	'order/form-tracking.php',
	'order/tracking.php',
);

function laborator_before_template_part($template_name)
{
	global $laborator_woocommerce_wrap_pages;

	foreach($laborator_woocommerce_wrap_pages as $template)
	{
		if($template == $template_name)
		{
			laborator_woocommerce_before();
		}
	}
}

function laborator_after_template_part($template_name)
{
	global $laborator_woocommerce_wrap_pages;

	foreach($laborator_woocommerce_wrap_pages as $template)
	{
		if($template == $template_name)
		{
			laborator_woocommerce_after();
		}
	}
}


function laborator_single_product_image_thumbnail_html($html)
{
	$html = str_replace('data-rel="prettyPhoto[product-gallery]"', 'data-lightbox-gallery="shop-gallery"', $html);
	return $html;
}