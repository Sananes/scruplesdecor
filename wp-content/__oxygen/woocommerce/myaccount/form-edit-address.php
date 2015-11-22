<?php
/**
 * Edit address form
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

global $current_user;

# start: modified by Arlind Nushi
do_action('laborator_woocommerce_before_wrapper');

$active_tab = 'my-address';

$order_count = wc_processing_order_count();
$order_count = 'all' == $order_count ? -1 : ($order_count+1);
# end: modified by Arlind Nushi

$page_title = ( $load_address === 'billing' ) ? __( 'Billing Address', 'woocommerce' ) : __( 'Shipping Address', 'woocommerce' );

get_currentuserinfo();
?>

<?php wc_print_notices(); ?>


<div class="row myaccount-env">
	<div class="col-md-12">
	
		<div class="white-block block-pad">
			
			<div class="row spread-2">
				
				<div class="col-md-3">
					
					<?php wc_get_template('myaccount/nav-tabs.php', array('active' => $active_tab, 'order_count' => $order_count)); ?>
					
				</div>
				
				<div class="col-md-9 tab-sep-container">
				
					<div class="tab-separator"></div>
					
					<div class="myaccount-tab" id="my-downloads">
						<?php wc_get_template( 'myaccount/my-downloads.php' ); ?>
					</div>
					
					<div class="myaccount-tab" id="my-orders">
						<?php wc_get_template( 'myaccount/my-orders.php', array('order_count' => $order_count)); ?>
					</div>
					
					<div class="myaccount-tab<?php echo $active_tab == 'my-wishlists' ? ' current' : ''; ?>" id="my-wishlists">
						<?php wc_get_template( 'myaccount/my-wishlists.php' ); ?>
					</div>
					
					<div class="myaccount-tab current" id="my-address">
						
						<?php if ( ! $load_address ) : ?>
						
							<?php wc_get_template( 'myaccount/my-address.php' ); ?>
						
						<?php else : ?>
						
							<form method="post" class="checkout-form-fields">
						
								<h4 class="with-divider"><?php echo apply_filters( 'woocommerce_my_account_edit_address_title', $page_title ); ?></h4>
						
								<?php foreach ( $address as $key => $field ) : ?>
						
									<?php woocommerce_form_field( $key, $field, ! empty( $_POST[ $key ] ) ? wc_clean( $_POST[ $key ] ) : $field['value'] ); ?>
						
								<?php endforeach; ?>
						
								<p>
									<input type="submit" class="button btn btn-default up" name="save_address" value="<?php _e( 'Save Address', 'woocommerce' ); ?>" />
									<?php wp_nonce_field( 'woocommerce-edit_address' ); ?>
									<input type="hidden" name="action" value="edit_address" />
								</p>
						
							</form>
						
						<?php endif; ?>
						
					</div>
					
				</div>
				
			</div>
			
		</div>
		
	</div>
</div>


<?php

# start: modified by Arlind Nushi
do_action('laborator_woocommerce_after_wrapper');
# end: modified by Arlind Nushi

?>