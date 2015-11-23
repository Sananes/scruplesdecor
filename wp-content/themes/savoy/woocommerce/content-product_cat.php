<?php
/**
 * The template for displaying product category thumbnails within loops.
 *
 * Override this template by copying it to yourtheme/woocommerce/content-product_cat.php
 *
 * @author 	WooThemes
 * @package WooCommerce/Templates
 * @version 2.4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/*global $woocommerce_loop;

// Store loop count we're currently on
if ( empty( $woocommerce_loop['loop'] ) ) {
	$woocommerce_loop['loop'] = 0;
}

// Store column count for displaying the grid
if ( empty( $woocommerce_loop['columns'] ) ) {
	$woocommerce_loop['columns'] = apply_filters( 'loop_shop_columns', 4 );
}

// Increase loop count
$woocommerce_loop['loop']++;*/

$thumbnail_id = get_woocommerce_term_meta( $category->term_id, 'thumbnail_id', true  );

if ( $thumbnail_id ) {
	$category_image = wp_get_attachment_image_src( $thumbnail_id, 'full' );
	$category_image_src = $category_image[0];
} else {
	$category_image = false;
}

$category_link = get_term_link( $category->slug, 'product_cat' );

// Get description from custom "Categories Grid" field
$category_description = get_option( 'nm_taxonomy_product_cat_' . $category->term_id . '_description' );

if ( $category_description ) {
	$show_link = true;
	$category_heading = $category_description;
} else {
	$show_link = false;
	$category_heading = $category->name;
}

?>
<li <?php wc_product_cat_class(); ?>>
	
    <div class="nm-product-category-inner">
		<?php do_action( 'woocommerce_before_subcategory', $category ); ?>
    	
        <a href="<?php echo esc_url( $category_link ); ?>">
        <?php
            /**
             * woocommerce_before_subcategory_title hook
             *
             * @hooked woocommerce_subcategory_thumbnail - 10
             */
            //do_action( 'woocommerce_before_subcategory_title', $category );
			if ( $category_image ) {
				// Prevent esc_url from breaking spaces in urls for image embeds
				// Ref: http://core.trac.wordpress.org/ticket/23605
				$category_image_src = str_replace( ' ', '%20', $category_image_src );
				
				echo '<img src="' . esc_url( $category_image_src ) . '" alt="' . esc_attr( $category->name ) . '" width="' . esc_attr( $category_image[1] ) . '" height="' . esc_attr( $category_image[2] ) . '" />';
			} else {
				echo '<img src="' . esc_url( wc_placeholder_img_src() ) . '" />';
			}
        ?>
        </a>
        
        <div class="nm-product-category-text">
            <h2><?php echo $category_heading; ?></h2>
        
            <?php
                /**
                 * woocommerce_after_subcategory_title hook
                 */
                do_action( 'woocommerce_after_subcategory_title', $category );
            ?>
            
            <?php if ( $show_link ) : ?>
            <a href="<?php echo esc_url( $category_link ); ?>" class="invert-color"><?php echo esc_html( $category->name ); ?></a>
            <?php endif; ?>
        </div>
    
        <?php do_action( 'woocommerce_after_subcategory', $category ); ?>
    </div>

</li>