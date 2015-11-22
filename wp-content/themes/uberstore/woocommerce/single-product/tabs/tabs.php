<?php
/**
 * Single Product tabs / and sections
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Filter tabs and allow third parties to add their own
 *
 * Each tab is an array containing title, callback and priority.
 * @see woocommerce_default_product_tabs()
 */
$tabs = apply_filters( 'woocommerce_product_tabs', array() ); 
?>

<div class="woocommerce-tabs">
	<ul class="accordion">
		<?php foreach ( $tabs as $key => $tab ) : ?>

			<li class="<?php echo $key ?>_tab">
				<div class="title"><?php echo apply_filters( 'woocommerce_product_' . $key . '_tab_title', $tab['title'], $key ) ?></div>
				<div class="content"><?php call_user_func( $tab['callback'], $key, $tab ) ?></div>
			</li>

		<?php endforeach; ?>
	</ul>
</div>

