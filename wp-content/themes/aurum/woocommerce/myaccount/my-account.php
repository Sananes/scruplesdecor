<?php
/**
 * My Account page
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.0.0
 */

/* Note: This file has been altered by Laborator */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

include THEMEDIR . 'tpls/woocommerce-account-tabs-before.php';

?>
<div class="content-pane active" id="my-orders">
	<?php wc_get_template( 'myaccount/my-orders.php', array( 'order_count' => $order_count ) ); ?>
</div>

<div class="content-pane" id="my-addresses">
	<?php wc_get_template( 'myaccount/my-address.php' ); ?>
</div>

<div class="content-pane" id="my-downloads">
	<?php wc_get_template( 'myaccount/my-downloads.php' ); ?>
</div>
<?php

include THEMEDIR . 'tpls/woocommerce-account-tabs-after.php';