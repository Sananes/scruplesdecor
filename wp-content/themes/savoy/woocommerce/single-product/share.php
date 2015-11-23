<?php
/**
 * Single Product Share
 *
 * Sharing plugins can hook into here or you can add your own code directly.
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $nm_theme_options, $nm_globals, $post;

$esc_permalink = esc_url( get_permalink() );
$product_image = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), false, '' );

?>

<div class="nm-product-share-wrap">
	<?php if ( $nm_globals['wishlist_enabled'] ) : ?>
    <div class="nm-product-wishlist-button-wrap">
		<?php nm_wishlist_button(); ?>
    </div>
    <?php endif; ?>
    
    <div class="nm-product-share">
        <a href="//www.facebook.com/sharer.php?u=<?php echo $esc_permalink; ?>" target="_blank" title="<?php esc_html_e( 'Share on Facebook', 'nm-framework' ); ?>"><i class="nm-font nm-font-facebook"></i></a>
        <a href="//twitter.com/share?url=<?php echo $esc_permalink; ?>" target="_blank" title="<?php esc_html_e( 'Share on Twitter', 'nm-framework' ); ?>"><i class="nm-font nm-font-twitter"></i></a>
        <a href="//pinterest.com/pin/create/button/?url=<?php echo $esc_permalink; ?>&amp;media=<?php echo esc_url( $product_image[0] ); ?>&amp;description=<?php echo urlencode( get_the_title() ); ?>" target="_blank" title="<?php esc_html_e( 'Pin in Pinterest', 'nm-framework' ); ?>"><i class="nm-font nm-font-pinterest"></i></a>
    </div>
</div>

<?php do_action( 'woocommerce_share' ); // Sharing plugins can hook into here ?>
