<?php
/**
 * Edit address form
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.1.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $woocommerce, $current_user;

$page_title = ( $load_address == 'billing' ) ? __( 'Billing Address', THB_THEME_NAME ) : __( 'Shipping Address', THB_THEME_NAME );

get_currentuserinfo();

?>

<?php wc_print_notices()  ?>
<div class="woocommerce">
<div class="row">
<div class="three columns">

	<ul id="my-account-nav">
		<li><a href="<?php echo get_permalink( get_option('woocommerce_myaccount_page_id') ); ?>">
			<?php _e('Back to My Account', THB_THEME_NAME); ?>
		</a></li>
	</ul>

</div>

<div class="nine columns">
	<div class="tab-pane active">
		<?php if (!$load_address) : ?>
		
			<?php woocommerce_get_template('myaccount/my-address.php'); ?>
		
		<?php else : ?>
			
			<form method="post" class="edit-address-form">
		
				<div class="largetitle"><?php echo apply_filters( 'woocommerce_my_account_edit_address_title', $page_title ); ?></div>
				<?php
				foreach ($address as $key => $field) :
					woocommerce_form_field( $key, $field, ! empty( $_POST[ $key ] ) ? wc_clean( $_POST[ $key ] ) : $field['value'] );
				endforeach;
				?>
		
				<p>
					<input type="submit" class="button" name="save_address" value="<?php _e( 'Save Address', THB_THEME_NAME ); ?>" />
					<?php wp_nonce_field( 'woocommerce-edit_address' ); ?>
					<input type="hidden" name="action" value="edit_address" />
				</p>
		
			</form>
			
		<?php endif; ?>
	</div>
</div>
</div>