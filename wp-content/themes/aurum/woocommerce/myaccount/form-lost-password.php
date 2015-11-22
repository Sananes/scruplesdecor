<?php
/**
 * Lost password form
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version	 2.0.0
 */

/* Note: This file has been altered by Laborator */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<?php wc_print_notices(); ?>

<div class="row">
	<div class="col-sm-6">
		<div class="bordered-block form-forgot-passwd-env">

			<h2>
				<?php if( 'lost_password' == $args['form'] ) : ?>
					<?php _e('Forgot Password', TD); ?>
				<?php else : ?>
					<?php _e('Set New Password', TD); ?>
				<?php endif; ?>
			</h2>

			<form method="post" class="lost_reset_password">

				<?php if( 'lost_password' == $args['form'] ) : ?>

					<p><?php echo apply_filters( 'woocommerce_lost_password_message', __( 'Lost your password? Please enter your username or email address. You will receive a link to create a new password via email.', 'woocommerce' ) ); ?></p>

					<p class="form-row form-row-first form-group">
						<input class="input-text form-control" type="text" name="user_login" id="user_login" placeholder="<?php _e( 'Username or email', 'woocommerce' ); ?>" />
					</p>

				<?php else : ?>

					<p><?php echo apply_filters( 'woocommerce_reset_password_message', __( 'Enter a new password below.', 'woocommerce') ); ?></p>

					<p class="form-row form-row-first form-group">
						<input type="password" class="input-text form-control" name="password_1" id="password_1" placeholder="<?php _e( 'New password', 'woocommerce' ); ?>" />
					</p>
					<p class="form-row form-row-last form-group">
						<input type="password" class="input-text form-control" name="password_2" id="password_2" placeholder="<?php _e( 'Re-enter new password', 'woocommerce' ); ?>" />
					</p>

					<input type="hidden" name="reset_key" value="<?php echo isset( $args['key'] ) ? $args['key'] : ''; ?>" />
					<input type="hidden" name="reset_login" value="<?php echo isset( $args['login'] ) ? $args['login'] : ''; ?>" />

				<?php endif; ?>

				<div class="clear"></div>

				<p class="form-row form-group">
					<input type="submit" class="button btn btn-primary" name="wc_reset_password" value="<?php echo 'lost_password' == $args['form'] ? __( 'Reset Password', 'woocommerce' ) : __( 'Save', 'woocommerce' ); ?>" />
				</p>
				<?php wp_nonce_field( $args['form'] ); ?>

			</form>

		</div>
	</div>
</div>