<?php
/**
 * The template for displaying product archives content, including the main shop page which is a post type archive.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $nm_theme_options, $nm_globals;

nm_add_page_include( 'products' );

// Product taxonomy
$is_product_taxonomy = false;
$show_category_description = false;
if ( is_product_taxonomy() ) {
	$is_product_taxonomy = true;
	
	// Display category description?
	if ( $nm_theme_options['shop_category_description'] ) {
		$show_category_description = true;
	}
}

// Is shop-page content hidden via query-string?
if ( isset( $_GET['shop_page'] ) && $_GET['shop_page'] === '0' ) {
	$show_shop_page = false;
} else {
	// Display shop-page content outside home-page?
	$show_shop_page = ( $nm_theme_options['shop_content_home'] && ! is_front_page() ) ? false : true;
}

get_header(); ?>
	
    <?php if ( $is_product_taxonomy ) : ?>
	
    <div class="nm-shop-hidden-taxonomy-content">
        <h1><?php woocommerce_page_title(); ?></h1>
        <?php
			// Hidden category description
            if ( ! $show_category_description ) {
				do_action( 'woocommerce_archive_description' );
			}
        ?>
    </div>
	
	<?php
		endif;
		
		// Note: Keep below "get_header()" (page not loading properly in some cases otherwise)
		$shop_page = ( $show_shop_page ) ? get_post( $nm_globals['shop_page_id'] ) : false;
		
		if ( $shop_page ) :
	?>
	
    <div class="nm-page-full">
        <div class="entry-content">
            <?php echo do_shortcode( $shop_page->post_content ); ?>
        </div>
    </div>
    
	<?php endif; ?>
        
    <div id="nm-shop" class="nm-shop">
        
        <?php 
			// Shop header
			if ( $nm_theme_options['shop_header'] ) {
				wc_get_template_part( 'content', 'product_nm_header' );
			}
		?>
        
        <?php nm_print_shop_notices(); // Note: Don't remove (WooCommerce will output multiple messages otherwise) ?>
        
        <div id="nm-shop-products" class="nm-shop-products">
            <div class="nm-row">
                <div class="col-xs-12">
                    <div id="nm-shop-products-overlay" class="nm-loader"></div>
                    <div id="nm-shop-browse-wrap" class="term-description-<?php echo esc_attr( $nm_theme_options['shop_category_description_layout'] ); ?>">
						<?php
                            // Results bar/button
                            wc_get_template_part( 'content', 'product_nm_results_bar' );
                        ?>
                        
                        <?php
							// Category description
							if ( $show_category_description ) {
								/**
								 * woocommerce_archive_description hook
								 *
								 * @hooked woocommerce_taxonomy_archive_description - 10
								 * @hooked woocommerce_product_archive_description - 10
								 */
								do_action( 'woocommerce_archive_description' );
							}
						?>
                        
						<?php if ( have_posts() ) : ?>
                        
                            <?php 
                                global $woocommerce_loop;
                                
                                // Set column sizes (large column is set via theme setting)
                                $woocommerce_loop['columns_small'] = '2';
                                $woocommerce_loop['columns_medium'] = '3';
                                
                                woocommerce_product_loop_start();
                            ?>
                
                                <?php while ( have_posts() ) : the_post(); ?>
                                    
                                    <?php
                                        // Note: Don't place in another template (image lazy-loading is only used in the shop and WooCommerce shortcodes can use the other product templates)
                                        global $nm_globals;
                                        $nm_globals['shop_image_lazy_loading'] = ( $nm_theme_options['product_image_lazy_loading'] ) ? true : false;
                                        
                                        wc_get_template_part( 'content', 'product' );
                                    ?>
                
                                <?php endwhile; // end of the loop. ?>
                
                            <?php woocommerce_product_loop_end(); ?>
                            
                            <?php
                                /**
                                 * woocommerce_after_shop_loop hook
                                 *
                                 * @hooked woocommerce_pagination - 10
                                 */
                                do_action( 'woocommerce_after_shop_loop' );
                            ?>
                            
                            <?php
                                /**
                                 * woocommerce_after_main_content hook
                                 *
                                 * @hooked woocommerce_output_content_wrapper_end - 10 (outputs closing divs for the content)
                                 */
                                do_action( 'woocommerce_after_main_content' );
                            ?>
                            
                        <?php elseif ( ! woocommerce_product_subcategories( array( 'before' => woocommerce_product_loop_start( false ), 'after' => woocommerce_product_loop_end( false ) ) ) ) : ?>
                    
                            <?php wc_get_template( 'loop/no-products-found.php' ); ?>
                        
                        <?php endif; ?>
                    </div>
                </div>
            </div>	
        </div>
        
	</div>
    
    <?php
		/**
		 * nm_after_shop hook
		 */
		do_action( 'nm_after_shop' );
	?>
    
<?php get_footer(); ?>
