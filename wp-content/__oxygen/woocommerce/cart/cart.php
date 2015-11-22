<?php
/**
 * Cart Page
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.3.8
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

# start: modified by Arlind Nushi
$cart_contents_count = WC()->cart->cart_contents_count;

do_action('laborator_woocommerce_before_wrapper');

?>
<div class="view-cart">

	<div class="row">
		<div class="col-lg-12">
			<div class="white-block block-pad">
				<h1><?php _e('Shopping Cart', 'oxygen'); ?></h1>
				<span><?php echo sprintf(_n('%d item', '%d items', $cart_contents_count, 'oxygen'), $cart_contents_count); ?></span>
			</div>
		</div>
	</div>

</div>
<?php
# end: modified by Arlind Nushi

wc_print_notices();

do_action( 'woocommerce_before_cart' ); ?>

<?php
# start: modified by Arlind Nushi
?>
<div class="cart-env">

	<div class="row">
		<div class="col-md-8">


			<form action="<?php echo esc_url( WC()->cart->get_cart_url() ); ?>" method="post" class="cart-form">

			<?php do_action( 'woocommerce_before_cart_table' ); ?>

				<!-- Cart -->
				<div class="row spread cart-header-row cart-item-row">

					<div class="col-xs-7 col-sm-5 up"><?php _e('Product', 'oxygen'); ?></div>
					<div class="col-xs-2 up hide-sm"><?php _e('Price', 'oxygen'); ?></div>
					<div class="col-xs-3 up text-center-sm"><?php _e('Quantity', 'oxygen'); ?></div>
					<div class="col-xs-2 up text-right-sm"><?php _e('Total', 'oxygen'); ?></div>

				</div>
					<?php do_action( 'woocommerce_before_cart_contents' ); ?>

					<?php
					foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
						$_product     = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
						$product_id   = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );

						if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
							?>
							<div class="row cart-item-row <?php echo esc_attr( apply_filters( 'woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key ) ); ?>">

								<div class="col col-sm-5 col-thumb-name">
									<?php
										echo apply_filters( 'woocommerce_cart_item_remove_link', sprintf( '<a href="%s" class="remove" title="%s"></a>', esc_url( WC()->cart->get_remove_url( $cart_item_key ) ), __( 'Remove this item', 'woocommerce' ) ), $cart_item_key );
									?>

									<div class="item-thumb hide-xs">
										<a href="<?php echo $_product->get_permalink( $cart_item ); ?>">
											<?php
											if(has_post_thumbnail($product_id)):

												$thumbnail = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image('shop-thumb-2'), $cart_item, $cart_item_key );
												echo apply_filters('woocommerce_cart_item_thumbnail', $thumbnail, $cart_item, $cart_item_key);


											else:

												$attachment_ids = $_product->get_gallery_attachment_ids();

												if(count($attachment_ids))
												{
													$first_img = reset($attachment_ids);
													$first_img_link = wp_get_attachment_url( $first_img );

													echo laborator_show_thumbnail($first_img, 'shop-thumb-2');
												}
												else
												{
													echo laborator_show_img(wc_placeholder_img_src(), 'shop-thumb-2');
												}
											endif;
											?>
										</a>
									</div>

									<div class="item-name">

										<span class="item-name-span"><?php
											if ( ! $_product->is_visible() )
												echo apply_filters( 'woocommerce_cart_item_name', $_product->get_title(), $cart_item, $cart_item_key );
											else
												echo apply_filters( 'woocommerce_cart_item_name', sprintf( '<a href="%s">%s</a>', $_product->get_permalink( $cart_item ), $_product->get_title() ), $cart_item, $cart_item_key );
										?></span>

									<?php
									# Rating
									if ( get_option( 'woocommerce_enable_review_rating' ) != 'no' && $_product->get_rating_count() ):
									?>

									<div class="rating filled-<?php echo intval($_product->get_average_rating());  echo $_product->get_average_rating() - intval($_product->get_average_rating()) > .49 ? ' and-half' : ''; ?>">
										<span class="glyphicon glyphicon-star star-1"></span>
										<span class="glyphicon glyphicon-star star-2"></span>
										<span class="glyphicon glyphicon-star star-3"></span>
										<span class="glyphicon glyphicon-star star-4"></span>
										<span class="glyphicon glyphicon-star star-5"></span>
									</div>
									<?php endif; ?>

									<?php

										// Meta data
										echo WC()->cart->get_item_data( $cart_item );

					       				// Backorder notification
					       				if ( $_product->backorders_require_notification() && $_product->is_on_backorder( $cart_item['quantity'] ) )
					       					echo '<p class="backorder_notification">' . __( 'Available on backorder', 'woocommerce' ) . '</p>';
									?>
									</div>

								</div>
								<div class="col col-sm-2 col-subtotal hide-sm">
									<?php
										echo apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key );
									?>
								</div>
								<div class="col col-sm-3">
									<?php
										if ( $_product->is_sold_individually() ) {
											$product_quantity = sprintf( '1 <input type="hidden" name="cart[%s][qty]" value="1" />', $cart_item_key );
										} else {
											$product_quantity = woocommerce_quantity_input( array(
												'input_name'  => "cart[{$cart_item_key}][qty]",
												'input_value' => $cart_item['quantity'],
												'max_value'   => $_product->backorders_allowed() ? '' : $_product->get_stock_quantity(),
											), $_product, false );
										}

										echo apply_filters( 'woocommerce_cart_item_quantity', $product_quantity, $cart_item_key );
									?>
								</div>
								<div class="col col-sm-2 col-total">
									<?php
										echo apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ), $cart_item, $cart_item_key );
									?>
								</div>

							</div>
							<?php
						}
					}

					do_action( 'woocommerce_cart_contents' );
					?>

					<div class="cart-buttons-hidden">
						<input type="submit" class="button" name="update_cart" value="<?php _e( 'Update Cart', 'woocommerce' ); ?>" />
						<input type="submit" class="checkout-button button alt wc-forward" name="proceed" value="<?php _e( 'Proceed to Checkout', 'woocommerce' ); ?>" />

						<?php do_action( 'woocommerce_proceed_to_checkout' ); ?>
						<?php do_action( 'woocommerce_cart_actions' ); ?>

						<?php wp_nonce_field( 'woocommerce-cart' ); ?>

						<input type="text" name="coupon_code" />
						<input type="submit" class="button" name="apply_coupon" value="<?php _e( 'Apply Coupon', 'oxygen' ); ?>" />

						<?php do_action('woocommerce_cart_coupon'); ?>
					</div>

			<?php do_action( 'woocommerce_after_cart_table' ); ?>

			</form>

			<?php woocommerce_shipping_calculator(); ?>

		</div>

		<div class="clear-sm"></div>

		<div class="col-md-4">

			<?php #do_action( 'woocommerce_cart_contents' ); ?>

			<span class="up cart-totals-title"><?php _e('Cart Totals', 'oxygen'); ?></span>

			<ul class="cart-totals">
				<?php
				foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
					$_product     = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
					$product_id   = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );

					if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_cart_item_visible', true, $cart_item, $cart_item_key ) )
					{
						?>
					<li>
						<div class="name"><?php echo $_product->get_title(); ?></div>
						<div class="value">
							<?php echo apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ), $cart_item, $cart_item_key ); ?>
						</div>
					</li>
					<?php
					}
				}
				?>

				<?php do_action( 'woocommerce_cart_collaterals' ); ?>

				<?php #woocommerce_cart_totals(); ?>
			</ul>


			<div class="cart-main-buttons">

				<div class="button-env">
					<button type="button" class="button btn btn-default update-cart-btn" name="update_cart">
						<i class="entypo-pencil"></i>
						<?php _e( 'Update Cart', 'woocommerce' ); ?>
					</button>

					<button type="button" class="checkout-button button alt wc-forward btn btn-black" name="proceed">
						<i class="entypo-basket"></i>
						<?php _e( 'Checkout', 'woocommerce' ); ?>
					</button>

				</div>

				<div class="button-env proc2ck">
				<?php do_action( 'woocommerce_proceed_to_checkout' ); ?>
				<?php wp_nonce_field( 'woocommerce-cart' ); ?>
				</div>

				<script type="text/javascript">
					jQuery(document).ready(function($)
					{
						$(".cart-main-buttons .update-cart-btn").click(function(ev)
						{
							ev.preventDefault();

							$(".cart-buttons-hidden input[name='update_cart']").trigger('click');
						});

						$(".cart-main-buttons .checkout-button").click(function(ev)
						{
							ev.preventDefault();

							$(".cart-buttons-hidden input[name='proceed']").trigger('click');
						});
					});
				</script>

			</div>

			<?php if ( WC()->cart->coupons_enabled() ) { ?>
				<div class="coupon">

					<div class="cutar-sep">
						<img src="<?php echo THEMEASSETS; ?>images/cutar_sep.png" />
					</div>

					<label for="coupon_code up"><?php _e( 'Have a Coupon?', 'oxygen' ); ?>:</label>

					<form method="post" id="coupon-code-form">

						<div class="input-group">
							<input type="text" name="coupon_code" class="input-text form-control" value="" placeholder="<?php _e( 'Coupon code', 'oxygen' ); ?>">

							<span class="input-group-btn">
								<button type="submit" name="apply_coupon" class="btn btn-black"><?php _e('Apply', 'oxygen'); ?></button>
							</span>
						</div>

					</form>

					<script type="text/javascript">
						jQuery(document).ready(function($)
						{
							var $txt = $("#coupon-code-form input[name='coupon_code']");
							$("#coupon-code-form").submit(function(ev)
							{
								ev.preventDefault();

								if($txt.val().length)
								{
									$(".cart-form input[name='coupon_code']").val( $txt.val() );
									$(".cart-form input[name='apply_coupon']").click();
								}
								else
								{
									$txt.focus();
								}
							});
						});
					</script>
					<?php do_action('woocommerce_cart_coupon'); ?>

				</div>
			<?php } ?>

			<?php do_action( 'woocommerce_after_cart_contents' ); ?>

		</div>
	</div>
</div>


<?php
do_action('laborator_woocommerce_after_wrapper');
# end: modified by Arlind Nushi
