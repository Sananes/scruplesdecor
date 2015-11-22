<?php
/**
 * Login Form
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.2.6
 */

/* Note: This file has been altered by Laborator */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

# start: modified by Arlind Nushi
$has_registration_form = get_option( 'woocommerce_enable_myaccount_registration' ) === 'yes';
# end: modified by Arlind Nushi
?>

<?php wc_print_notices(); ?>

<div class="row">
	<div class="col-lg-12">
		<div class="page-title">
			<h1>
				<?php _e('Log In', TD); ?>
				<small><?php _e('Manage your account and see your orders', TD); ?></small>
			</h1>
		</div>
	</div>
</div>

<div class="row form-login-env">
<?php do_action( 'woocommerce_before_customer_login_form' ); ?>

	<div class="col-sm-6">

		<div class="bordered-block">

			<h2><?php _e( 'Login', 'woocommerce' ); ?></h2>

			<form method="post" class="login">

				<?php do_action( 'woocommerce_login_form_start' ); ?>

				<p class="form-row form-row-wide form-group">
					<input type="text" class="input-text form-control" name="username" id="username" value="<?php if ( ! empty( $_POST['username'] ) ) echo esc_attr( $_POST['username'] ); ?>" placeholder="<?php _e( 'Username or email address', 'woocommerce' ); ?>" />
				</p>
				<p class="form-row form-row-wide form-group">
					<input class="input-text form-control" type="password" name="password" id="password" placeholder="<?php _e( 'Password', 'woocommerce' ); ?>" />
				</p>

				<?php do_action( 'woocommerce_login_form' ); ?>

				<p class="form-row form-group">
					<input name="rememberme" type="checkbox" id="rememberme" value="forever" class="replaced-checkboxes" />

					<label for="rememberme" class="inline">
						<?php _e( 'Remember me', 'woocommerce' ); ?>
					</label>
				</p>

				<p class="form-row form-group">
					<?php wp_nonce_field( 'woocommerce-login' ); ?>
					<input type="submit" class="button btn btn-primary" name="login" value="<?php _e( 'Login', 'woocommerce' ); ?>" />

					<a href="<?php echo esc_url( wc_lostpassword_url() ); ?>" class="lost-password pull-right"><?php _e( 'Lost your password?', 'woocommerce' ); ?></a>
				</p>

				<?php do_action( 'woocommerce_login_form_end' ); ?>

			</form>

		</div>

	</div>

<?php if ($has_registration_form) : ?>

	<div class="col-sm-6 form-register-env">

		<div class="bordered-block">

			<h2><?php _e( 'Register', 'woocommerce' ); ?></h2>

			<form method="post" class="register">

				<?php do_action( 'woocommerce_register_form_start' ); ?>

				<?php if ( 'no' === get_option( 'woocommerce_registration_generate_username' ) ) : ?>

					<p class="form-row form-row-wide form-group">
						<input type="text" class="input-text form-control" name="username" id="reg_username" value="<?php if ( ! empty( $_POST['username'] ) ) echo esc_attr( $_POST['username'] ); ?>" placeholder="<?php _e( 'Username', 'woocommerce' ); ?> *" />
					</p>

				<?php endif; ?>

				<p class="form-row form-row-wide form-group">
					<input type="email" class="input-text form-control" name="email" id="reg_email" value="<?php if ( ! empty( $_POST['email'] ) ) echo esc_attr( $_POST['email'] ); ?>" placeholder="<?php _e( 'Email address', 'woocommerce' ); ?> *" />
				</p>

				<?php if ( 'no' === get_option( 'woocommerce_registration_generate_password' ) ) : ?>

					<p class="form-row form-row-wide form-group">
						<input type="password" class="input-text form-control" name="password" id="reg_password" placeholder="<?php _e( 'Password', 'woocommerce' ); ?> *" />
					</p>

				<?php endif; ?>

				<!-- Spam Trap -->
				<div style="<?php echo ( ( is_rtl() ) ? 'right' : 'left' ); ?>: -999em; position: absolute;"><label for="trap"><?php _e( 'Anti-spam', 'woocommerce' ); ?></label><input type="text" name="email_2" id="trap" tabindex="-1" /></div>

				<?php do_action( 'woocommerce_register_form' ); ?>
				<?php do_action( 'register_form' ); ?>

				<p class="form-row">
					<?php wp_nonce_field( 'woocommerce-register' ); ?>
					<input type="submit" class="button btn btn-primary" name="register" value="<?php _e( 'Register', 'woocommerce' ); ?>" />
				</p>

				<?php do_action( 'woocommerce_register_form_end' ); ?>

			</form>

		</div>

	</div>
<?php endif; ?>

</div>

<?php do_action( 'woocommerce_after_customer_login_form' ); ?>
