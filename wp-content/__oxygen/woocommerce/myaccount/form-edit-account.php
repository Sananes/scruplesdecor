<?php
/**
 * Edit account form
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.2.7
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

# start: modified by Arlind Nushi
do_action('laborator_woocommerce_before_wrapper');

$active_tab = 'edit-account';

$order_count = wc_processing_order_count();
$order_count = 'all' == $order_count ? -1 : ($order_count+1);
# end: modified by Arlind Nushi
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
						<?php wc_get_template( 'myaccount/my-orders.php', array( 'order_count' => $order_count ) ); ?>
					</div>

					<div class="myaccount-tab<?php echo $active_tab == 'my-wishlists' ? ' current' : ''; ?>" id="my-wishlists">
						<?php wc_get_template( 'myaccount/my-wishlists.php' ); ?>
					</div>

					<div class="myaccount-tab" id="my-address">
						<?php wc_get_template( 'myaccount/my-address.php' ); ?>
					</div>

					<div class="myaccount-tab current" id="edit-account">

						<form action="" method="post" class="checkout-form-fields">

							<p class="form-row form-row-first">
								<label for="account_first_name"><?php _e( 'First name', 'woocommerce' ); ?> <span class="required">*</span></label>
								<input type="text" class="input-text" name="account_first_name" id="account_first_name" value="<?php esc_attr_e( $user->first_name ); ?>" />
							</p>
							<p class="form-row form-row-last">
								<label for="account_last_name"><?php _e( 'Last name', 'woocommerce' ); ?> <span class="required">*</span></label>
								<input type="text" class="input-text" name="account_last_name" id="account_last_name" value="<?php esc_attr_e( $user->last_name ); ?>" />
							</p>
							<p class="form-row form-row-wide">
								<label for="account_email"><?php _e( 'Email address', 'woocommerce' ); ?> <span class="required">*</span></label>
								<input type="email" class="input-text" name="account_email" id="account_email" value="<?php esc_attr_e( $user->user_email ); ?>" />
							</p>
							<p class="form-row form-row-thirds">
								<label for="password_current"><?php _e( 'Current Password (leave blank to leave unchanged)', 'woocommerce' ); ?></label>
								<input type="password" class="input-text" name="password_current" id="password_current" />
							</p>
							<p class="form-row form-row-first">
								<label for="password_1"><?php _e( 'Password (leave blank to leave unchanged)', 'woocommerce' ); ?></label>
								<input type="password" class="input-text" name="password_1" id="password_1" />
							</p>
							<p class="form-row form-row-last">
								<label for="password_2"><?php _e( 'Confirm new password', 'oxygen' ); ?></label>
								<input type="password" class="input-text" name="password_2" id="password_2" />
							</p>
							<div class="clear"></div>

							<p><input type="submit" class="button btn btn-default up" name="save_account_details" value="<?php _e( 'Save changes', 'woocommerce' ); ?>" /></p>

							<?php wp_nonce_field( 'save_account_details' ); ?>
							<input type="hidden" name="action" value="save_account_details" />
						</form>

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