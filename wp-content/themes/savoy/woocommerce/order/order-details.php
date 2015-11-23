<?php
/**
 * Order details
 *
 * @author  WooThemes
 * @package WooCommerce/Templates
 * @version 2.4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$order = wc_get_order( $order_id );
?>
<div class="nm-order-details">
    <h2><?php _e( 'Order Details', 'woocommerce' ); ?></h2>
    <ul class="order_details">
        <?php
			foreach( $order->get_items() as $item_id => $item ) {	
                wc_get_template( 'order/order-details-item.php', array(
					'order'   => $order,
					'item_id' => $item_id,
					'item'    => $item,
					'product' => apply_filters( 'woocommerce_order_item_product', $order->get_product_from_item( $item ), $item )
				) );
			}
    	?>
        <?php do_action( 'woocommerce_order_items_table', $order ); ?>
        <ul class="nm-order-details-foot">
        	<?php
				foreach ( $order->get_order_item_totals() as $key => $total ) {
					?>
					<li>
						<div class="col-th col-xs-6"><?php echo $total['label']; ?></div>
						<div class="col-td col-xs-6"><?php echo $total['value']; ?></div>
					</li>
					<?php
				}
			?>
        </ul>
    </ul>
    
    <div class="clearfix"></div>
    
    <?php do_action( 'woocommerce_order_details_after_order_table', $order ); ?>
    
    <?php wc_get_template( 'order/order-details-customer.php', array( 'order' =>  $order ) ); ?>
    
</div>
