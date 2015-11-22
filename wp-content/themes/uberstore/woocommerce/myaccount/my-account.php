<?php
/**
 * My Account page
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $woocommerce, $yith_wcwl;

wc_print_notices()  ?>
<div class="woocommerce">
<div class="row">
<div class="three columns">

	<ul id="my-account-nav">
		<li class="active"><a href="#my-account"><?php _e("My Account", THB_THEME_NAME); ?></a></li>
	  <li><a href="#my-orders"><?php _e("My Orders", THB_THEME_NAME); ?></a></li>
	  <?php if ( $downloads = $woocommerce->customer->get_downloadable_products() ) { ?>
	  <li><a href="#my-downloads"><?php _e("My Downloads", THB_THEME_NAME); ?></a></li>
	  <?php } ?>
	  <?php if ( class_exists( 'YITH_WCWL_UI' ) ) { ?>
	  <li><a href="<?php echo $yith_wcwl->get_wishlist_url(); ?>"><?php _e("My Wishlist", THB_THEME_NAME); ?></a></li>
	  <?php } ?>
	  <li><a href="#address-book"><?php _e("My Addresses", THB_THEME_NAME); ?></a></li>
	  <li><a href="<?php echo wc_customer_edit_account_url(); ?>" id="changeit"><?php _e("Edit Account", THB_THEME_NAME); ?></a></li>
	  <li><a href="<?php echo wp_logout_url(); ?>"><?php _e("Log Out", THB_THEME_NAME); ?></a></li>
	</ul>

</div>

<div class="nine columns">
	
	<?php do_action( 'woocommerce_before_my_account' ); ?>
	
	<div class="tab-pane active" id="my-account">
	
	<?php woocommerce_get_template( 'myaccount/my-account-home.php', array( 'order_count' => $order_count ) ); ?>
	
	</div>
	
	<div class="tab-pane" id="my-orders">
	
	<?php woocommerce_get_template( 'myaccount/my-orders.php', array( 'order_count' => $order_count ) ); ?>
	
	</div>
	
	<?php if ( $downloads = $woocommerce->customer->get_downloadable_products() ) { ?>
	
	<div class="tab-pane" id="my-downloads">
	
	<?php woocommerce_get_template( 'myaccount/my-downloads.php' ); ?>
	
	</div>
	
	<?php } ?>
	
	<div class="tab-pane" id="address-book">
	
	<?php woocommerce_get_template( 'myaccount/my-address.php' ); ?>
	
	</div>	
	
	<?php do_action( 'woocommerce_after_my_account' ); ?>
	
</div>
</div>
</div>