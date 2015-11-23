<?php
/**
 *	NM: The template for displaying AJAX loaded pages
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $nm_theme_options;

$show_category_description = ( $nm_theme_options['shop_category_description'] && is_product_category() ) ? true : false;
?> 

<?php 
	// Shop header
	if ( $nm_theme_options['shop_header'] === '1' ) {
		wc_get_template_part( 'content', 'product_nm_header' );
	}
?>

<div id="nm-shop-browse-wrap" class="term-description-<?php echo esc_attr( $nm_theme_options['shop_category_description_layout'] ); ?>">
<?php
	// Results bar/button
	wc_get_template_part( 'content', 'product_nm_results_bar' );
	
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
	
	if ( have_posts() ) {

		global $woocommerce_loop;
		
		// Set column sizes (large column is set via theme setting)
		$woocommerce_loop['columns_small'] = '2';
		$woocommerce_loop['columns_medium'] = '3';
		
		woocommerce_product_loop_start();

		while ( have_posts() ) {
			the_post();
			wc_get_template_part( 'content', 'product' );
		}

		woocommerce_product_loop_end();
	
		/**
		 * woocommerce_after_shop_loop hook
		 *
		 * @hooked woocommerce_pagination - 10
		 */
		do_action( 'woocommerce_after_shop_loop' );
	
		/**
		 * woocommerce_after_main_content hook
		 *
		 * @hooked woocommerce_output_content_wrapper_end - 10 (outputs closing divs for the content)
		 */
		do_action( 'woocommerce_after_main_content' );
	
	} elseif ( ! woocommerce_product_subcategories( array( 'before' => woocommerce_product_loop_start( false ), 'after' => woocommerce_product_loop_end( false ) ) ) ) {

		wc_get_template( 'loop/no-products-found.php' );

	}
?>
</div>
