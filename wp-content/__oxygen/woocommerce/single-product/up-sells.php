<?php
/**
 * Single Product Up-Sells
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $product, $woocommerce_loop;

$upsells = $product->get_upsells();

if ( sizeof( $upsells ) == 0 ) return;

$meta_query = WC()->query->get_meta_query();

$args = array(
	'post_type'           => 'product',
	'ignore_sticky_posts' => 1,
	'no_found_rows'       => 1,
	'posts_per_page'      => $posts_per_page,
	'orderby'             => $orderby,
	'post__in'            => $upsells,
	'post__not_in'        => array( $product->id ),
	'meta_query'          => $meta_query
);

$products = new WP_Query( $args );

$woocommerce_loop['columns'] = $columns;

if ( $products->have_posts() ) : ?>

	<div class="upsells products">

		<div class="row">
			<div class="col-md-12">
				<h2 class="middle-title"><?php _e( 'You may also like&hellip;', 'woocommerce' ) ?></h2>
			</div>
		</div>

		<?php woocommerce_product_loop_start(); ?>

			<?php $i = 1; $shop_columns = SHOPSINGLESIDEBAR ? 3 : 4; while ( $products->have_posts() ) : $products->the_post(); ?>

				<?php wc_get_template_part( 'content', 'product' ); ?>
				
				<?php
				# start: modified by Arlind Nushi
				echo $i % $shop_columns == 0 ? '<div class="clear"></div>' : '';
				# end: modified by Arlind Nushi
				?>
				
			<?php $i++; endwhile; // end of the loop. ?>

		<?php woocommerce_product_loop_end(); ?>

	</div>

<?php endif;

wp_reset_postdata();
