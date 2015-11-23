<?php
/**
 * Show error messages
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! $messages ) {
	return;
}
?>

<div class="nm-shop-notice-wrap">
    <div class="nm-row">
        <div class="col-xs-12">
            <ul class="nm-shop-notice woocommerce-error">
                <?php foreach ( $messages as $message ) : ?>
                    <li><span><i class="nm-font nm-font-close"></i><?php echo wp_kses_post( $message ); ?></span></li>
                <?php endforeach; ?>
			</ul>
		</div>
	</div>
</div>