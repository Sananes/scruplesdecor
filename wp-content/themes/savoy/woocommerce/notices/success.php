<?php
/**
 * Show messages
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
			<?php foreach ( $messages as $message ) : ?>
                <div class="nm-shop-notice woocommerce-message"><span><i class="nm-font nm-font-check"></i><?php echo wp_kses_post( $message ); ?></span></div>
            <?php endforeach; ?>
		</div>
	</div>
</div>
