<?php
/**
 * My Account page
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

wc_print_notices(); ?>

<div class="nm-myaccount nm-row">
	<div class="col-xs-12">
        <div class="myaccount_user">
            <h2>
				<?php 
					printf( esc_html__( 'Hello %s.', 'nm-framework' ), 
						esc_html( $current_user->display_name )
					);
				?>
			</h2>
            
            <a href="<?php echo wp_logout_url( get_permalink( wc_get_page_id( 'myaccount' ) ) ); ?>"><?php _e( 'Logout', 'nm-framework' ); ?></a>
            
            <p>
				<?php
                printf( __( 'From your account dashboard you can view your recent orders, manage your shipping and billing addresses and <a href="%s">edit your password and account details</a>.', 'woocommerce' ),
                    wc_customer_edit_account_url()
                );
                ?>
            </p>
        </div>
    </div>
    
    <div class="col-md-push-2 col-sm-push-1 col-xs-push-0 col-md-8 col-sm-10 col-xs-12">
        <?php do_action( 'woocommerce_before_my_account' ); ?>
        
        <?php wc_get_template( 'myaccount/my-downloads.php' ); ?>
        
        <?php wc_get_template( 'myaccount/my-orders.php', array( 'order_count' => $order_count ) ); ?>
        
        <?php wc_get_template( 'myaccount/my-address.php' ); ?>
        
        <?php do_action( 'woocommerce_after_my_account' ); ?>
    </div>
</div>
