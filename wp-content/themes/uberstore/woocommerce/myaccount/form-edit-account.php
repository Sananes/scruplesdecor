<?php
/**
 * Edit account form
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>

<?php wc_print_notices(); ?>
<div class="woocommerce">
<div class="row">
<div class="three columns">
	<div class="widget">
	<ul id="my-account-nav" class="menu">
		<li><a href="<?php echo get_permalink( get_option('woocommerce_myaccount_page_id') ); ?>">
			<?php _e('Back to My Account', THB_THEME_NAME); ?>
		</a></li>
	</ul>
	</div>
</div>

<div class="nine columns">
	<div class="tab-pane active">
		<h2><?php _e( 'Edit Account', THB_THEME_NAME ); ?></h2>
		<form action="" method="post">
			<div class="row">
				<div class="small-12 medium-6 columns">
					<label for="account_first_name"><?php _e( 'First name', THB_THEME_NAME ); ?> <span class="required">*</span></label>
					<input type="text" class="input-text full" name="account_first_name" id="account_first_name" value="<?php echo esc_attr( $user->first_name ); ?>" />
				</div>
				<div class="small-12 medium-6 columns">
					<label for="account_last_name"><?php _e( 'Last name', THB_THEME_NAME ); ?> <span class="required">*</span></label>
					<input type="text" class="input-text full" name="account_last_name" id="account_last_name" value="<?php echo esc_attr( $user->last_name ); ?>" />
				</div>
				<div class="small-12 columns">
					<label for="account_email"><?php _e( 'Email address', THB_THEME_NAME ); ?> <span class="required">*</span></label>
					<input type="email" class="input-text full" name="account_email" id="account_email" value="<?php echo esc_attr( $user->user_email ); ?>" />
				</div>
		
				<div class="small-12 columns">
					<h3><?php _e( 'Password Change', THB_THEME_NAME ); ?></h3>
				
					<p>
						<label for="password_current"><?php _e( 'Current Password (leave blank to leave unchanged)', THB_THEME_NAME ); ?></label>
						<input type="password" class="input-text full" name="password_current" id="password_current" />
					</p>
					<p>
						<label for="password_1"><?php _e( 'New Password (leave blank to leave unchanged)', THB_THEME_NAME ); ?></label>
						<input type="password" class="input-text full" name="password_1" id="password_1" />
					</p>
					<p>
						<label for="password_2"><?php _e( 'Confirm New Password', THB_THEME_NAME ); ?></label>
						<input type="password" class="input-text full" name="password_2" id="password_2" />
					</p>
				</div>
				<div class="small-12 columns">
					<p><input type="submit" class="button" name="save_account_details" value="<?php _e( 'Save changes', THB_THEME_NAME ); ?>" /></p>
				
					<?php wp_nonce_field( 'save_account_details' ); ?>
					<input type="hidden" name="action" value="save_account_details" />
				</div>
			</div>
		</form>
	</div>
</div>