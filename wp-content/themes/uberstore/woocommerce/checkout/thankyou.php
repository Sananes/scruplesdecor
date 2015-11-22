<?php
/**
 * Thankyou page
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $woocommerce; ?>

<div class="row">
	<div class="twelve columns">
		<ul id="shippingsteps">
			<li class="first"><span>1</span><a href="#"><?php _e('Checkout Method', THB_THEME_NAME); ?></a></li>
			<li><span>2</span><a href="#"><?php _e('Billing &amp; Shipping', THB_THEME_NAME); ?></a></li>
			<li><span>3</span><a href="#"><?php _e('Your Order &amp; Payment', THB_THEME_NAME); ?></a></li>
			<li class="active"><span>4</span><a href="#"><?php _e('Confirmation', THB_THEME_NAME); ?></a></li>
	</div> 
</div>

<?php if ( $order ) : ?>
	<div class="row">
	<?php if ( in_array( $order->status, array( 'failed' ) ) ) : ?>
		<div class="three columns">
			<p><?php _e( 'Unfortunately your order cannot be processed as the originating bank/merchant has declined your transaction.', THB_THEME_NAME ); ?></p>
	
			<p><?php
				if ( is_user_logged_in() )
					_e( 'Please attempt your purchase again or go to your account page.', THB_THEME_NAME );
				else
					_e( 'Please attempt your purchase again.', THB_THEME_NAME );
			?></p>
	
			<p>
				<a href="<?php echo esc_url( $order->get_checkout_payment_url() ); ?>" class="button pay"><?php _e( 'Pay', THB_THEME_NAME ) ?></a>
				<?php if ( is_user_logged_in() ) : ?>
				<a href="<?php echo esc_url( get_permalink( wc_get_page_id( 'myaccount' ) ) ); ?>" class="button pay"><?php _e( 'My Account', THB_THEME_NAME ); ?></a>
				<?php endif; ?>
			</p>
		</div>
	<?php else : ?>
		<div class="three columns">
			<p><?php _e( 'Thank you. Your order has been received.', THB_THEME_NAME ); ?></p>
	
			<ul class="order_details">
				<li class="order">
					<?php _e( 'Order:', THB_THEME_NAME ); ?>
					<strong><?php echo $order->get_order_number(); ?></strong>
				</li>
				<li class="date">
					<?php _e( 'Date:', THB_THEME_NAME ); ?>
					<strong><?php echo date_i18n( get_option( 'date_format' ), strtotime( $order->order_date ) ); ?></strong>
				</li>
				<li class="total">
					<?php _e( 'Total:', THB_THEME_NAME ); ?>
					<strong><?php echo $order->get_formatted_order_total(); ?></strong>
				</li>
				<?php if ( $order->payment_method_title ) : ?>
				<li class="method">
					<?php _e( 'Payment method:', THB_THEME_NAME ); ?>
					<strong><?php echo $order->payment_method_title; ?></strong>
				</li>
				<?php endif; ?>
			</ul>
		</div>
	<?php endif; ?>

		<div class="nine columns">
			<?php // do_action( 'woocommerce_thankyou_' . $order->payment_method, $order->id ); ?>
		
			<?php do_action( 'woocommerce_thankyou', $order->id ); ?>
	
		</div>
	</div>
<?php else : ?>

	<p><?php _e( 'Thank you. Your order has been received.', THB_THEME_NAME ); ?></p>

<?php endif; ?>