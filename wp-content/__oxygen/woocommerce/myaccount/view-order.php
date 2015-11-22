<?php
/**
 * View Order
 *
 * Shows the details of a particular order on the account page 
 *
 * @author    WooThemes
 * @package   WooCommerce/Templates
 * @version   2.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

# start: modified by Arlind Nushi
do_action('laborator_woocommerce_before_wrapper');
# end: modified by Arlind Nushi

?>

<?php wc_print_notices(); ?>

<div class="row">
	<div class="col-md-12">
		<p class="order-info no-margin woocommerce-info"><?php printf( __( 'Order <mark class="order-number">%s</mark> was placed on <mark class="order-date">%s</mark> and is currently <mark class="order-status">%s</mark>.', 'woocommerce' ), $order->get_order_number(), date_i18n( get_option( 'date_format' ), strtotime( $order->order_date ) ), wc_get_order_status_name( $order->get_status() ) ); ?></p>
		
		<br />
<?php if ( $notes = $order->get_customer_order_notes() ) :
	?>
	
	<div class="white-block block-pad">
		<h4 class="with-divider"><?php _e( 'Order Updates', 'woocommerce' ); ?></h4>
		
		<ol class="commentlist notes">
			<?php foreach ( $notes as $note ) : ?>
			<li class="comment note">
				<div class="comment_container">
					<div class="comment-text">
						<p class="meta"><?php echo date_i18n( __( 'l jS \o\f F Y, h:ia', 'woocommerce' ), strtotime( $note->comment_date ) ); ?></p>
						<div class="description">
							<?php echo wpautop( wptexturize( $note->comment_content ) ); ?>
						</div>
		  				<div class="clear"></div>
		  			</div>
					<div class="clear"></div>
				</div>
			</li>
			<?php endforeach; ?>
		</ol>
	</div>
	<?php
endif;

do_action( 'woocommerce_view_order', $order_id );

?>
	</div>
</div>
<?php

# start: modified by Arlind Nushi
do_action('laborator_woocommerce_after_wrapper');
# end: modified by Arlind Nushi