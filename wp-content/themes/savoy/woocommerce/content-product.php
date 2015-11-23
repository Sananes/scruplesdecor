<?php
/**
 * The template for displaying product content within loops.
 *
 * Override this template by copying it to yourtheme/woocommerce/content-product.php
 *
 * @author 	WooThemes
 * @package WooCommerce/Templates
 * @version 2.4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $product, $woocommerce_loop, $nm_theme_options, $nm_globals;

nm_add_page_include( 'products' );

// Action: woocommerce_before_shop_loop_item_title
remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10 );

// Action: woocommerce_after_shop_loop_item_title
remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5 );

// Store loop count we're currently on
if ( empty( $woocommerce_loop['loop'] ) ) {
	$woocommerce_loop['loop'] = 0;
}

// Store column count for displaying the grid
if ( empty( $woocommerce_loop['columns'] ) ) {
	$woocommerce_loop['columns'] = apply_filters( 'loop_shop_columns', 4 );
}

// Ensure visibility
if ( ! $product || ! $product->is_visible() ) {
	return;
}

// Increase loop count
$woocommerce_loop['loop']++;

// Extra post classes
$classes = array();
/*if ( 0 == ( $woocommerce_loop['loop'] - 1 ) % $woocommerce_loop['columns'] || 1 == $woocommerce_loop['columns'] ) {
	$classes[] = 'first';
}
if ( 0 == $woocommerce_loop['loop'] % $woocommerce_loop['columns'] ) {
	$classes[] = 'last';
}*/
	
// Hover image
$image_swap = ( $nm_theme_options['product_hover_image_global'] ) ? true : get_post_meta( $product->id, 'nm_product_image_swap', true );
$hover_image = '';
if ( $image_swap ) {
	$gallery_image_ids = $product->get_gallery_attachment_ids();
	
	if ( $gallery_image_ids ) {
		$hover_image_id = reset( $gallery_image_ids ); // Get first gallery image id
		$hover_image_src = wp_get_attachment_image_src( $hover_image_id, 'shop_catalog' );
		
		// Make sure the first image is found (deleted image id's can can still be assigned to the gallery)
		if ( $hover_image_src ) {
			$hover_image = '<img src="' . esc_url( NM_THEME_URI . '/img/transparent.gif' ) . '" data-src="' . esc_url( $hover_image_src[0] ) . '" width="' . esc_attr( $hover_image_src[1] ) . '" height="' . esc_attr( $hover_image_src[2] ) . '" class="attachment-shop-catalog hover-image" />';
		}
		
		$classes[] = 'hover-image-load';
	}
}
?>
<li <?php post_class( $classes ); ?>>

	<?php do_action( 'woocommerce_before_shop_loop_item' ); ?>
	
    <div class="nm-shop-loop-thumbnail nm-loader">
        <a href="<?php esc_url( the_permalink() ); ?>">
            <?php
				/**
				 * woocommerce_before_shop_loop_item_title hook
				 *
				 * @hooked woocommerce_show_product_loop_sale_flash - 10
				 */
				do_action( 'woocommerce_before_shop_loop_item_title' );
			?>
                        
			<?php
				$placeholder_image = NM_THEME_URI . '/img/placeholder.gif';
			
				if ( has_post_thumbnail() ) {
                    $product_thumbnail = wp_get_attachment_image_src( get_post_thumbnail_id(), 'shop_catalog' );
					
					if ( $nm_globals['shop_image_lazy_loading'] ) {
						echo '<img src="' . esc_url( $placeholder_image ) . '" data-src="' . esc_url( $product_thumbnail[0] ) . '" width="' . esc_attr( $product_thumbnail[1] ) . '" height="' . esc_attr( $product_thumbnail[2] ) . '" class="attachment-shop-catalog unveil-image" />';
					} else {
						echo '<img src="' . esc_url( $product_thumbnail[0] ) . '" width="' . esc_attr( $product_thumbnail[1] ) . '" height="' . esc_attr( $product_thumbnail[2] ) . '" class="attachment-shop-catalog" />';
					}
                } else if ( woocommerce_placeholder_img_src() ) {
					echo '<img src="' . esc_url( $placeholder_image ) . '" class="attachment-shop-catalog" />';
                }
				
				// Hover image
				echo $hover_image;
			?>
        </a>
    </div>
	
    <div class="nm-shop-loop-details">
    	<?php if ( $nm_globals['wishlist_enabled'] ) : ?>
        <div class="nm-shop-loop-wishlist-button"><?php nm_wishlist_button(); ?></div>
        <?php endif; ?>
    
        <h3><a href="<?php esc_url( the_permalink() ); ?>"><?php the_title(); ?></a></h3>
        
        <div class="nm-shop-loop-after-title <?php echo esc_attr( $nm_theme_options['product_action_link'] ); ?>">
			<div class="nm-shop-loop-price">
                <?php
					/**
					 * woocommerce_after_shop_loop_item_title hook
					 *
					 * @hooked woocommerce_template_loop_price - 10
					 */
					do_action( 'woocommerce_after_shop_loop_item_title' );
				?>
            </div>
            
            <div class="nm-shop-loop-actions">
				<?php
                    if ( $nm_theme_options['product_quickview'] ) {
						echo '<a href="' . esc_url( get_permalink() ) . '" data-product_id="' . esc_attr( $product->id ) . '" class="nm-quickview-btn product_type_' . esc_attr( $product->product_type ) . '">' . esc_html__( 'Show more', 'nm-framework' ) . '</a>';
					} else {
						/**
						 * woocommerce_after_shop_loop_item hook
						 *
						 * @hooked woocommerce_template_loop_add_to_cart - 10
						 */
                        do_action( 'woocommerce_after_shop_loop_item' );
					}
                ?>
            </div>
        </div>
    </div>

</li>
