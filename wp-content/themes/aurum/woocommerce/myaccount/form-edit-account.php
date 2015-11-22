<?php
/**
 * Edit account form
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.2.7
 */

/* Note: This file has been altered by Laborator */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

# start: modified by Arlind Nushi
include THEMEDIR . 'tpls/woocommerce-account-tabs-before.php';
# end: modified by Arlind Nushi
?>

<div class="page-title">
	<h2>
		<?php _e('Edit Account <small>Change your name, email or password</small>', TD); ?>
	</h2>
</div>

<form action="" method="post">

	<?php do_action( 'woocommerce_edit_account_form_start' ); ?>

	<fieldset>
		<legend class="no-top-margin"><?php _e( 'Name and Email', TD ); ?></legend>

		<p class="form-row form-row-first form-group">
			<input type="text" class="input-text form-control" name="account_first_name" id="account_first_name" value="<?php echo esc_attr( $user->first_name ); ?>" placeholder="<?php _e( 'First name', 'woocommerce' ); ?> *" />
		</p>
		<p class="form-row form-row-last form-group">
			<input type="text" class="input-text form-control" name="account_last_name" id="account_last_name" value="<?php echo esc_attr( $user->last_name ); ?>" placeholder="<?php _e( 'Last name', 'woocommerce' ); ?> *" />
		</p>
		<div class="clear"></div>

		<p class="form-row form-row-wide form-group">
			<input type="email" class="input-text form-control" name="account_email" id="account_email" value="<?php echo esc_attr( $user->user_email ); ?>" placeholder="<?php _e( 'Email address', 'woocommerce' ); ?> *" />
		</p>
	</fieldset>

	<fieldset>
		<legend><?php _e( 'Password Change', 'woocommerce' ); ?></legend>

		<p class="form-row form-row-wide form-group">
			<input type="password" class="input-text form-control" name="password_current" id="password_current" placeholder="<?php _e( 'Current Password (leave blank to leave unchanged)', 'woocommerce' ); ?>" />
		</p>
		<p class="form-row form-row-wide form-group">
			<input type="password" class="input-text form-control" name="password_1" id="password_1" placeholder="<?php _e( 'New Password (leave blank to leave unchanged)', 'woocommerce' ); ?>" />
		</p>
		<p class="form-row form-row-wide form-group">
			<input type="password" class="input-text form-control" name="password_2" id="password_2" placeholder="<?php _e( 'Confirm New Password', 'woocommerce' ); ?>" />
		</p>
	</fieldset>
	<div class="clear"></div>

	<?php do_action( 'woocommerce_edit_account_form' ); ?>

	<p>
		<?php wp_nonce_field( 'save_account_details' ); ?>
		<input type="submit" class="button btn btn-primary" name="save_account_details" value="<?php _e( 'Save changes', 'woocommerce' ); ?>" />
		<input type="hidden" name="action" value="save_account_details" />
	</p>

	<?php do_action( 'woocommerce_edit_account_form_end' ); ?>

</form>
<?php

# start: modified by Arlind Nushi
include THEMEDIR . 'tpls/woocommerce-account-tabs-after.php';
# end: modified by Arlind Nushi