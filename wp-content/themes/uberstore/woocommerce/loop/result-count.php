<?php
/**
 * Result Count
 *
 * Shows text: Showing x - x of x results
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $woocommerce, $wp_query;

if ( ! woocommerce_products_will_display() )
	return;
?>
<div class="woocommerce-count-wrap">
	<div class="row">
	<div class="eight columns">
		<p class="woocommerce-result-count">
			<?php
			$paged    = max( 1, $wp_query->get( 'paged' ) );
			$per_page = $wp_query->get( 'posts_per_page' );
			$total    = $wp_query->found_posts;
			$first    = ( $per_page * $paged ) - $per_page + 1;
			$last     = min( $total, $wp_query->get( 'posts_per_page' ) * $paged );
		
			if ( 1 == $total ) {
				_e( 'Showing the single product', THB_THEME_NAME );
			} elseif ( $total <= $per_page ) {
				printf( __( 'Showing all <strong>%d</strong> products', THB_THEME_NAME ), $total );
			} else {
				printf( _x( 'Showing <strong>%1$d–%2$d</strong> of <strong>%3$d</strong> products', '%1$d = first, %2$d = last, %3$d = total', THB_THEME_NAME ), $first, $last, $total );
			}
			?>
		</p>
		<?php if (is_product_category() || is_shop()) { ?>
			<?php if (is_product_category()) { 
				$queried_object = get_queried_object(); 
				$link = get_term_link($queried_object->slug, 'product_cat');
				
				$thelink_24 = add_query_arg(array ('show_products' =>'24'), $link);
				$thelink_48 = add_query_arg(array ('show_products' =>'48'), $link);
				$thelink_all = add_query_arg(array ('show_products' =>'all'), $link);
			} else {
				$link = get_permalink( woocommerce_get_page_id( 'shop' ) );
				$thelink_24 = add_query_arg(array ('show_products' =>'24'), $link);
				$thelink_48 = add_query_arg(array ('show_products' =>'48'), $link);
				$thelink_all = add_query_arg(array ('show_products' =>'all'), $link);
			} ?>
			<p class="woocommerce-show-products hide-for-small">
				<span><?php _e("Products Per Page", THB_THEME_NAME); ?> </span>
				<a class="show-products-link" href="<?php echo $thelink_24; ?>">24</a> <a class="show-products-link" href="<?php echo $thelink_48; ?>">48</a> <a  class="show-products-link" href="<?php echo $thelink_all; ?>">All</a>
			</p>
		<?php } ?>
	</div>