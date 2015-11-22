<?php

global $woocommerce;

if (is_user_logged_in()) return;
?>
<form method="post" class="row">
	<?php if ($message) echo wpautop(wptexturize($message)); ?>

	<div class="eight columns">
		<label for="username"><?php _e('Username or email', THB_THEME_NAME); ?></label>
		<input type="text" class="input-text" name="username" id="username" />
	</div>
	
  <div class="eight columns">
    <label for="password"><?php _e('Password', THB_THEME_NAME); ?></label>
		<input class="input-text" type="password" name="password" id="password" />
	</div>
	<div class="eight columns">
		<input name="rememberme" type="checkbox" id="rememberme" value="forever" class="custom_check" /> 
		<label for="rememberme" class="custom_label">
			<?php _e( 'Remember me', THB_THEME_NAME ); ?>
		</label>
	</div>
	<div class="twelve columns">
		<?php wp_nonce_field( 'woocommerce-login' ); ?>
		
		<input type="submit" class="button_checkout_login button small" name="login" value="<?php _e('Login', THB_THEME_NAME); ?>" />
		<a class="lost_password" href="<?php echo esc_url( wc_lostpassword_url() ); ?>"><?php _e('Lost Password?', THB_THEME_NAME); ?></a>
    <input type="hidden" name="redirect" value="<?php echo esc_url( $redirect ) ?>" />
	</div>
</form>