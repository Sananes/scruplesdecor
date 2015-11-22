<?php
/**
 * The template for displaying product content in the single-product.php template
 *
 * Override this template by copying it to yourtheme/woocommerce/content-single-product.php
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

?>

<?php
	/**
	 * woocommerce_before_single_product hook
	 *
	 * @hooked wc_print_notices - 10
	 */
	 do_action( 'woocommerce_before_single_product' );

	 if ( post_password_required() ) {
	 	echo get_the_password_form();
	 	return;
	 }
?>

<div itemscope itemtype="<?php echo woocommerce_get_product_schema(); ?>" id="product-<?php the_ID(); ?>" <?php post_class(); ?>>

	<div class="row white-row">
	
		<div class="col-sm-6">
			
			<div class="product-left-info">
				
			<?php
				/**
				 * woocommerce_before_single_product_summary hook
				 *
				 * @hooked woocommerce_show_product_sale_flash - 10
				 * @hooked woocommerce_show_product_images - 20
				 */
				do_action( 'woocommerce_before_single_product_summary' );
			?>
			
			</div>
		
		</div>
	
		<div class="col-sm-6">
		
			<div class="summary entry-summary">
				
				<div class="product-top-nav">
				<?php
				
				if(get_data('shop_single_next_prev')): 
				
					$prev_post = get_adjacent_post(false, '', false); 
					$next_post = get_adjacent_post(false, '', true);
					
					?>
					<div class="nav-links">
						<a href="<?php echo get_permalink($prev_post); ?>" title="<?php echo esc_attr(get_the_title($prev_post)); ?>" class="prev<?php echo ! $prev_post instanceof WP_Post ? ' disable' : ''; ?>">
							<i class="entypo-left-open-mini"></i>
						</a>
						<a href="<?php echo get_permalink($next_post); ?>" title="<?php echo esc_attr(get_the_title($next_post)); ?>" class="next<?php echo ! $next_post instanceof WP_Post ? ' disable' : ''; ?>">
							<i class="entypo-right-open-mini"></i>
						</a>
					</div>
					<?php
					
				endif;
				?>
					
					<?php woocommerce_template_single_rating(); ?>
					
				</div>
		
				<?php
					/**
					 * woocommerce_single_product_summary hook
					 *
					 * @hooked woocommerce_template_single_title - 5
					 * @hooked woocommerce_template_single_rating - 10
					 * @hooked woocommerce_template_single_price - 10
					 * @hooked woocommerce_template_single_excerpt - 20
					 * @hooked woocommerce_template_single_add_to_cart - 30
					 * @hooked woocommerce_template_single_meta - 40
					 * @hooked woocommerce_template_single_sharing - 50
					 */
					do_action( 'woocommerce_single_product_summary' );
				?>
		
			</div><!-- .summary -->
			
		</div>

		<div class="clear"></div>
		
		<div class="col-md-12">
		<?php
			/**
			 * woocommerce_after_single_product_summary hook
			 *
			 * @hooked woocommerce_output_product_data_tabs - 10
			 * @hooked woocommerce_output_related_products - 20
			 */
			do_action( 'woocommerce_after_single_product_summary' );
		?>
		</div>
		
	</div>
	

	<meta itemprop="url" content="<?php the_permalink(); ?>" />

</div><!-- #product-<?php the_ID(); ?> -->

<?php do_action( 'woocommerce_after_single_product' ); ?>


<?php woocommerce_upsell_display(); ?>

<?php woocommerce_output_related_products(); ?>
