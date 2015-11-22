<?php
/**
 * Lost password form
 *
 * @author  WooThemes
 * @package WooCommerce/Templates
 * @version 2.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

?>

<?php
# start: modified by Arlind Nushi
do_action('laborator_woocommerce_before_wrapper');
?>

<div class="col-lg-12">
    <div class="white-block block-pad log-in">
        <h1><?php _e('My Account', 'oxygen'); ?></h1>
    </div>
</div>

<?php
# end: modified by Arlind Nushi
?>

<?php wc_print_notices(); ?>


<div class="col-md-6">

	<div class="vspacer v3"></div>

    <div class="white-block block-pad">

	    <div class="block_title">
			<h4>
				<?php _e('Forgot Password', 'oxygen'); ?>
				<span class="entypo-lock pull-right"></span>
			</h4>
		</div>

		<form method="post" class="lost_reset_password">

			<?php if( 'lost_password' == $args['form'] ) : ?>

				<p><?php echo apply_filters( 'woocommerce_lost_password_message', __( 'Lost your password? Please enter your username or email address. You will receive a link to create a new password via email.', 'woocommerce' ) ); ?></p>

				<p class="form-row form-row-first">
					<label for="user_login"><?php _e( 'Username or email', 'woocommerce' ); ?></label>
					<input class="input-text form-control" type="text" name="user_login" id="user_login" />
				</p>

			<?php else : ?>

				<p><?php echo apply_filters( 'woocommerce_reset_password_message', __( 'Enter a new password below.', 'woocommerce') ); ?></p>

				<p class="form-row form-row-first">
					<label for="password_1"><?php _e( 'New password', 'woocommerce' ); ?> <span class="required">*</span></label>
					<input type="password" class="input-text form-control" name="password_1" id="password_1" />
				</p>
				<p class="form-row form-row-last">
					<label for="password_2"><?php _e( 'Re-enter new password', 'woocommerce' ); ?> <span class="required">*</span></label>
					<input type="password" class="input-text form-control" name="password_2" id="password_2" />
				</p>

				<input type="hidden" name="reset_key" value="<?php echo isset( $args['key'] ) ? $args['key'] : ''; ?>" />
				<input type="hidden" name="reset_login" value="<?php echo isset( $args['login'] ) ? $args['login'] : ''; ?>" />

			<?php endif; ?>

			<div class="clear"></div>

			<p class="form-row">
				<input type="hidden" name="wc_reset_password" value="true" />
				<input type="submit" class="button btn btn-default full-width-btn up" value="<?php echo 'lost_password' == $args['form'] ? __( 'Reset Password', 'woocommerce' ) : __( 'Save', 'woocommerce' ); ?>" />
			</p>

			<?php wp_nonce_field( $args['form'] ); ?>

		</form>

	</div>
</div>


<?php
# start: modified by Arlind Nushi
do_action('laborator_woocommerce_after_wrapper');
# end: modified by Arlind Nushi
?>