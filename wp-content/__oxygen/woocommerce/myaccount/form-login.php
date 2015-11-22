<?php
/**
 * Login Form
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.2.6
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>

<?php
# start: modified by Arlind Nushi
wp_enqueue_script('icheck');
wp_enqueue_style('icheck');

do_action('laborator_woocommerce_before_wrapper');
# end: modified by Arlind Nushi
?>

<?php do_action( 'woocommerce_before_customer_login_form' ); ?>

<?php
# start: modified by Arlind Nushi
?>
<div class="row">
	<div class="col-lg-12">
	    <div class="white-block block-pad log-in">
	        <h1><?php _e('My Account', 'oxygen'); ?></h1>
	    </div>
	</div>
</div>

<?php wc_print_notices(); ?>

<?php
# end: modified by Arlind Nushi
?>

<?php if ( get_option( 'woocommerce_enable_myaccount_registration' ) === 'yes' ) : ?>

<div class="col2-set" id="customer_login">

	<div class="col-lg-6">
<?php else : ?>
	<div class="col-lg-6"><!-- start for login only -->
<?php endif; ?>

		<div class="white-block block-pad">

			<form method="post" class="login">

				<div class="block_title">
					<h4>
						<?php _e('Login', 'oxygen'); ?>
						<span class="glyphicon glyphicon-log-in pull-right"></span>
					</h4>
				</div>

				<?php do_action( 'woocommerce_login_form_start' ); ?>

				<p class="form-row form-row-wide">
					<label for="username">
						<?php _e( 'Username or email address', 'woocommerce' ); ?>
						<span class="required red">*</span>
					</label>

					<input type="text" class="input-text form-control" name="username" id="username" />
				</p>

				<p class="form-row form-row-wide">
					<label for="password">
						<?php _e( 'Password', 'woocommerce' ); ?>
						<span class="required red">*</span>
					</label>

					<input class="input-text form-control" type="password" name="password" id="password" />
				</p>

				<?php do_action( 'woocommerce_login_form' ); ?>

				<p class="form-row">
					<?php wp_nonce_field( 'woocommerce-login' ); ?>
					<input type="submit" class="button btn-default btn full-width-btn up" name="login" value="<?php _e( 'Login', 'woocommerce' ); ?>" />

					<label for="rememberme" class="inline pull-right">
						<input name="rememberme" type="checkbox" id="rememberme" value="forever" /> <?php _e( 'Remember me', 'woocommerce' ); ?>
					</label>

					<div class="clearfix"></div>

					<a href="<?php echo esc_url( wc_lostpassword_url() ); ?>" class="lost-password"><?php _e( 'Lost your password?', 'woocommerce' ); ?></a>
				</p>



				<?php do_action( 'woocommerce_login_form_end' ); ?>

			</form>
		</div>

<?php if ( get_option( 'woocommerce_enable_myaccount_registration' ) === 'yes' ) : ?>

	</div>

	<div class="col-2 col-lg-6">

		<div class="block-pad white-block">

			<div class="block_title">
				<h4>
					<?php _e( 'Register', 'woocommerce' ); ?>
					<span class="glyphicon glyphicon-user pull-right"></span>
				</h4>
			</div>

			<form method="post" class="register">

				<?php do_action( 'woocommerce_register_form_start' ); ?>

				<?php if ( 'no' === get_option( 'woocommerce_registration_generate_username' ) ) : ?>

					<p class="form-row form-row-wide">
						<label for="reg_username">
							<?php _e( 'Username', 'woocommerce' ); ?>
							<span class="required red">*</span>
						</label>

						<input type="text" class="input-text form-control" name="username" id="reg_username" value="<?php if ( ! empty( $_POST['username'] ) ) echo esc_attr( $_POST['username'] ); ?>" />
					</p>

				<?php endif; ?>

				<p class="form-row form-row-wide">
					<label for="reg_email">
						<?php _e( 'Email address', 'woocommerce' ); ?>
						<span class="required red">*</span>
					</label>

					<input type="email" class="input-text form-control" name="email" id="reg_email" value="<?php if ( ! empty( $_POST['email'] ) ) echo esc_attr( $_POST['email'] ); ?>" />
				</p>

				<?php if ( 'no' === get_option( 'woocommerce_registration_generate_password' ) ) : ?>

					<p class="form-row form-row-wide">
						<label for="reg_password">
							<?php _e( 'Password', 'woocommerce' ); ?>
							<span class="required red">*</span>
						</label>

						<input type="password" class="input-text form-control" name="password" id="reg_password" value="<?php if ( ! empty( $_POST['password'] ) ) echo esc_attr( $_POST['password'] ); ?>" />
					</p>

				<?php endif; ?>

				<!-- Spam Trap -->
				<div style="left:-999em; position:absolute;">
					<label for="trap"><?php _e( 'Anti-spam', 'woocommerce' ); ?></label>
					<input type="text" name="email_2" id="trap" tabindex="-1" />
				</div>

				<?php do_action( 'woocommerce_register_form' ); ?>
				<?php do_action( 'register_form' ); ?>

				<p class="form-row">
					<?php wp_nonce_field( 'woocommerce-register' ); ?>
					<input type="submit" class="button btn-default btn full-width-btn up" name="register" value="<?php _e( 'Register', 'woocommerce' ); ?>" />
				</p>

				<?php do_action( 'woocommerce_register_form_end' ); ?>

			</form>

		</div>

	</div>

</div>
<?php else : ?>
</div><!-- end for login only -->

<?php endif; ?>

<div class="clear"></div>

<?php do_action( 'woocommerce_after_customer_login_form' ); ?>

<?php
# start: modified by Arlind Nushi
do_action('laborator_woocommerce_after_wrapper');
# end: modified by Arlind Nushi
?>