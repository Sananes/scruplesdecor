<?php
/**
 * Edit address form
 *
 * @author      WooThemes
 * @package     WooCommerce/Templates
 * @version     2.1.0
 */

/* Note: This file has been altered by Laborator */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $current_user;

$page_title = ( $load_address === 'billing' ) ? __( 'Billing Address', 'woocommerce' ) : __( 'Shipping Address', 'woocommerce' );

get_currentuserinfo();

# start: modified by Arlind Nushi
include THEMEDIR . 'tpls/woocommerce-account-tabs-before.php';
# end: modified by Arlind Nushi
?>

<?php if ( ! $load_address ) : ?>

	<?php wc_get_template( 'myaccount/my-address.php' ); ?>

<?php else : ?>

	<form method="post">

		<div class="page-title">
			<h2>
				<?php echo apply_filters( 'woocommerce_my_account_edit_address_title', $page_title ); ?>
				<?php _e('<small>Edit address information</small>', TD); ?>
			</h2>
		</div>

		<?php do_action( "woocommerce_before_edit_address_form_{$load_address}" ); ?>

		<?php foreach ( $address as $key => $field ) : ?>

			<?php
			# start: modified by Arlind Nushi
			$field['class'][] = 'form-group';
			$field['input_class'] = array('form-control');
			$field['placeholder'] = (isset($field['label']) ? $field['label'] : '') . (isset($field['required']) && $field['required'] ? ' *' : '');
			$field['label_class'] = 'hidden';
			# end: modified by Arlind Nushi
			?>

			<?php woocommerce_form_field( $key, $field, ! empty( $_POST[ $key ] ) ? wc_clean( $_POST[ $key ] ) : $field['value'] ); ?>

		<?php endforeach; ?>

		<?php do_action( "woocommerce_after_edit_address_form_{$load_address}" ); ?>

		<p>
			<input type="submit" class="button btn btn-primary" name="save_address" value="<?php _e( 'Save Address', 'woocommerce' ); ?>" />
			<?php wp_nonce_field( 'woocommerce-edit_address' ); ?>
			<input type="hidden" name="action" value="edit_address" />
		</p>

	</form>

<?php endif; ?>


<?php

# start: modified by Arlind Nushi
include THEMEDIR . 'tpls/woocommerce-account-tabs-after.php';
# end: modified by Arlind Nushi