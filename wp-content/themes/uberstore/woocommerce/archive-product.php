<?php
/**
 * The Template for displaying product archives, including the main shop page which is a post type archive.
 *
 * Override this template by copying it to yourtheme/woocommerce/archive-product.php
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

get_header('shop'); 

?>

<div class="row">

<?php
	/**
	 * woocommerce_before_main_content hook
	 * @hooked woocommerce_output_content_wrapper - 10 (outputs opening divs for the content)
	 */
	do_action('woocommerce_before_main_content');
?>
<?php if (isset($_GET['sidebar'])) { $sidebar_pos = htmlspecialchars($_GET['sidebar']); } else { $sidebar_pos = ot_get_option('shop_sidebar'); }  ?>
<?php if($sidebar_pos != 'no') { ?>
    <section class="nine columns <?php if ($sidebar_pos == 'left')  { echo 'push-three'; } ?>" id="shop-page">
<?php } else { ?>
		<section class="twelve columns" id="shop-page">
<?php } ?>
	

		<?php 
			if (is_shop()) {
				echo do_shortcode(ot_get_option('shop_header'));
			} else if (is_product_category()) {
				// GET CUSTOM HEADER CONTENT FOR CATEGORY
				if(function_exists('get_term_meta')){
					$queried_object = get_queried_object(); 
					
					if (isset($queried_object->term_id)){
				
						$term_id = $queried_object->term_id;  
						$content = get_term_meta($term_id, 'cat_meta');
				
						if(isset($content[0]['cat_header'])){
							echo do_shortcode($content[0]['cat_header']);
						}
					}
				}
			}
		?>
		<?php do_action( 'woocommerce_before_shop_loop' ); ?>
		

		<?php if ( have_posts() ) : ?>

			<?php woocommerce_product_loop_start(); ?>

				<?php woocommerce_product_subcategories(); ?>

				<?php while ( have_posts() ) : the_post(); ?>

					<?php woocommerce_get_template_part( 'content', 'product' ); ?>
					
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

		<?php elseif ( ! woocommerce_product_subcategories( array( 'before' => woocommerce_product_loop_start( false ), 'after' => woocommerce_product_loop_end( false ) ) ) ) : ?>

			<?php woocommerce_get_template( 'loop/no-products-found.php' ); ?>

		<?php endif; ?>

	<?php
		/**
		 * woocommerce_after_main_content hook
		 *
		 * @hooked woocommerce_output_content_wrapper_end - 10 (outputs closing divs for the content)
		 */
		do_action('woocommerce_after_main_content');
	?>


                      
 </section>
 
<?php if($sidebar_pos != 'no') { ?>
    <?php get_sidebar('shop'); ?>
<?php } ?>

</div><!-- end row -->

<?php get_footer('shop'); ?>