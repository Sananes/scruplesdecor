<?php
/**
 * My Account page
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

# start: modified by Arlind Nushi
do_action('laborator_woocommerce_before_wrapper');

?>
<div class="row">
    <div class="col-lg-12">
        <div class="white-block block-pad my-account">
            <h1><?php _e('My Account', 'oxygen'); ?></h1>

            <p class="myaccount_user">
				<?php
				printf(
					__( 'Hello <strong>%1$s</strong> (not %1$s? <a href="%2$s">Sign out</a>).', 'woocommerce' ) . ' ',
					$current_user->display_name,
					laborator_logout_url( get_permalink( wc_get_page_id( 'myaccount' ) ) )
				);

				printf( __( 'From your account dashboard you can view your recent orders, manage your shipping and billing addresses and <a href="%s">edit your password and account details</a>.', 'woocommerce' ),
					wc_customer_edit_account_url()
				);
				?>
			</p>
        </div>
    </div>
</div>

<?php

$active_tab = 'my-orders';

if ( $downloads = WC()->customer->get_downloadable_products() )
	$active_tab = 'my-downloads';
# end: modified by Arlind Nushi

wc_print_notices(); ?>



<div class="row myaccount-env">
	<div class="col-md-12">

		<?php do_action( 'woocommerce_before_my_account' ); ?>

		<div class="white-block block-pad">

			<div class="row spread-2">

				<div class="col-md-3">

					<?php wc_get_template('myaccount/nav-tabs.php', array('active' => $active_tab, 'order_count' => $order_count)); ?>

				</div>

				<div class="col-md-9 tab-sep-container">

					<div class="tab-separator"></div>

					<div class="myaccount-tab<?php echo $active_tab == 'my-downloads' ? ' current' : ''; ?>" id="my-downloads">
						<?php wc_get_template( 'myaccount/my-downloads.php' ); ?>
					</div>

					<div class="myaccount-tab<?php echo $active_tab == 'my-orders' ? ' current' : ''; ?>" id="my-orders">
						<?php wc_get_template( 'myaccount/my-orders.php', array( 'order_count' => $order_count ) ); ?>
					</div>

					<div class="myaccount-tab<?php echo $active_tab == 'my-wishlists' ? ' current' : ''; ?>" id="my-wishlists">
						<?php wc_get_template( 'myaccount/my-wishlists.php' ); ?>
					</div>

					<div class="myaccount-tab" id="my-address">
						<?php wc_get_template( 'myaccount/my-address.php' ); ?>
					</div>

					<?php do_action( 'woocommerce_after_my_account' ); ?>

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