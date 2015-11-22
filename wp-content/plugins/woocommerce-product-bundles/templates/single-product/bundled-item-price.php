<?php
/**
 * Bundled Item Price.
 * @version 4.7.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( $bundled_item->is_priced_per_product() ) {
	?><p class="price"><?php echo $bundled_item->product->get_price_html(); ?></p><?php
}
