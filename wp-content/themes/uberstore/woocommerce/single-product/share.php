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

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
global $post;
?>


<?php $image = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ) ); ?>
<aside id="product_share" data-img="<?php echo $image[0]; ?>">
	<div class="row">
		<div class="one mobile-one columns">
			<?php _e( 'Share', THB_THEME_NAME ); ?>
		</div>
		<div class="eleven mobile-three columns">
			<span class="placeholder" data-url="<?php the_permalink(); ?>" data-text="<?php the_title();?>"></span>
		</div>
	</div>
</aside>
<?php do_action('woocommerce_share'); // Sharing plugins can hook into here ?>