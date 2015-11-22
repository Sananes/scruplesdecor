<?php
/**
 * Admin Add Bundled Product markup.
 * @version 4.8.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?><div class="wc-bundled-item wc-metabox open" rel="<?php echo $loop; ?>">
	<h3>
		<button type="button" class="remove_row button"><?php echo __( 'Remove', 'woocommerce' ); ?></button>
		<div class="handlediv" title="<?php echo __( 'Click to toggle', 'woocommerce' ); ?>"></div>
		<strong class="item-title"><?php echo $title . ' &ndash; #'. $product_id; ?></strong>
	</h3>
	<div class="item-data wc-metabox-content">
		<input type="hidden" name="bundle_data[<?php echo $loop; ?>][bundle_order]" class="bundled_item_position" value="<?php echo $loop; ?>" />
		<input type="hidden" name="bundle_data[<?php echo $loop; ?>][product_id]" class="product_id" value="<?php echo $product_id; ?>" /><?php

		do_action( 'woocommerce_bundled_product_admin_config_html', $loop, $product_id, array(), $post_id );

	?></div>
</div>
