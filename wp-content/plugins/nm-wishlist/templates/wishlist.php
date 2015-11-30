<?php
/**
 * NM - Wishlist template
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $nm_wishlist_ids, $product;

$wishlist_empty_class = '';

if ( ! empty( $nm_wishlist_ids ) ) {
	$args = array(
		'post_type'		 => 'product',
		'order'			 => 'DESC',
		'orderby' 		 => 'post__in',
		'posts_per_page' => -1, // No limit
		'post__in'		 => array_keys( $nm_wishlist_ids )
	);
			
	$wishlist_loop = new WP_Query( $args );
} else {
	$wishlist_loop = false;
	$wishlist_empty_class = ' class="show"';
}

?>

<?php if ( $wishlist_loop && $wishlist_loop->have_posts() ) : ?>

<div id="nm-wishlist">
	<div class="nm-row">
        <div class="col-lg-3 col-xs-12">
            <h1><?php esc_html_e( 'Wishlist', 'nm-wishlist' ); ?></h1>
        </div>
        
        <div class="col-lg-9 col-xs-12">
            <table id="nm-wishlist-table" class="products" cellspacing="0">
                <thead>
                    <tr>
                        <th class="title" colspan="2"><span><?php esc_html_e( 'Product', 'nm-wishlist' ); ?></span></th>
                        <th class="price-stock" colspan="2"><span><?php esc_html_e( 'Price & Stock', 'nm-wishlist' ); ?></span></th>
                    </tr>
                </thead>
                <tbody>
					<?php 
                        while ( $wishlist_loop->have_posts() ) : $wishlist_loop->the_post(); 
                            
                        global $product;
                    ?>
                    <tr data-product-id="<?php echo $product->id; ?>">
                        <td class="thumbnail">
                            <a href="<?php the_permalink(); ?>"><?php echo $product->get_image(); ?></a>
                        </td>
                        <td class="title">
                            <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                            <a href="#" class="nm-wishlist-remove invert-color"><?php esc_html_e( 'Remove', 'nm-wishlist' ); ?></a>
                        </td>
                        <td class="price-stock">
                            <?php
								// Price
								woocommerce_template_loop_price();
								
								// Stock note
								$availability = $product->get_availability();
								
								if ( $availability['class'] !== '' ) {
									echo '<span class="stock-note ' . $availability['class'] . '">' . $availability['availability'] . '</span>';
								}
							?>
                        </td>
                        <td class="actions">
							<?php
								printf( '<a href="%s" rel="nofollow" data-product_id="%s" data-product_sku="%s" data-quantity="%s" class="%s product_type_%s button"><i class="nm-font nm-font-plus"></i><span>%s</span></a>',
									esc_url( $product->add_to_cart_url() ),
									esc_attr( $product->id ),
									esc_attr( $product->get_sku() ),
									esc_attr( isset( $quantity ) ? $quantity : 1 ),
									$product->is_purchasable() && $product->is_in_stock() ? 'add_to_cart_button' : '',
									esc_attr( $product->product_type ),
									esc_html( $product->add_to_cart_text() )
								);
							?>
                        </td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
             </table>
         </div>
     </div>
</div>

<?php endif; ?>

<div id="nm-wishlist-empty"<?php echo $wishlist_empty_class; ?>>
    <div class="nm-row">
        <div class="col-xs-12">
            <p class="icon"><i class="nm-font nm-font-close2"></i></p>
            <h1><?php _e( 'The wishlist is currently empty.', 'nm-wishlist' ); ?></h1>
            <p class="note"><?php printf( __( 'Click the %s icons to add products', 'nm-wishlist' ), '<i class="nm-font nm-font-heart-o"></i>' ); ?></p>
            <p><a href="<?php echo esc_url( get_permalink( woocommerce_get_page_id( 'shop' ) ) ); ?>" class="button"><?php _e( 'Return to Shop', 'nm-wishlist' ); ?></a></p>
        </div>
    </div>
</div>
