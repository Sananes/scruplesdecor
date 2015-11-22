<?php
/**
 * The template for displaying product category thumbnails within loops.
 *
 * Override this template by copying it to yourtheme/woocommerce/content-product_cat.php
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     1.6.4
 */

/* Note: This file has been altered by Laborator */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $woocommerce_loop;

// Store loop count we're currently on
if ( empty( $woocommerce_loop['loop'] ) )
	$woocommerce_loop['loop'] = 0;

// Store column count for displaying the grid
if ( empty( $woocommerce_loop['columns'] ) )
	$woocommerce_loop['columns'] = apply_filters( 'loop_shop_columns', 4 );

// Increase loop count
$woocommerce_loop['loop']++;

# start: modified by Arlind Nushi
$category_columns = get_data('shop_category_columns');
$item_colums = '';

switch($category_columns)
{
	case 4:
		$item_colums = 'col-lg-3 col-md-3 col-sm-6';
		break;

	case 3:
		$item_colums = 'col-lg-4 col-md-4 col-sm-6';
		break;

	case 2:
		$item_colums = 'col-xs-6';
		break;
}
# end: modified by Arlind Nushi
?>
<div class="<?php echo $item_colums; ?>">
	<div class="product-category product<?php
	    if ( ( $woocommerce_loop['loop'] - 1 ) % $woocommerce_loop['columns'] == 0 || $woocommerce_loop['columns'] == 1 )
	        echo ' first';
		if ( $woocommerce_loop['loop'] % $woocommerce_loop['columns'] == 0 )
			echo ' last';

		?>">

		<?php do_action( 'woocommerce_before_subcategory', $category ); ?>

		<a href="<?php echo get_term_link( $category->slug, 'product_cat' ); ?>">

			<?php
				/**
				 * woocommerce_before_subcategory_title hook
				 *
				 * @hooked woocommerce_subcategory_thumbnail - 10
				 */
				do_action( 'woocommerce_before_subcategory_title', $category );

				# start: modified by Arlind Nushi
				$thumbnail_id = get_woocommerce_term_meta( $category->term_id, 'thumbnail_id', true  );
				$thumbnail_url = wc_placeholder_img_src();

				if($thumbnail_id)
				{
					$thumbnail_url_custom = wp_get_attachment_image_src( $thumbnail_id, 'shop-category-thumb' );

					if($thumbnail_url_custom)
						$thumbnail_url = $thumbnail_url_custom[0];
				}

				echo '<img src="'.$thumbnail_url.'" alt="category-shop" />';
				# end: modified by Arlind Nushi
			?>

			<h3>
				<?php
					echo $category->name;

					if ( $category->count > 0 && get_data('shop_category_count') )
						echo apply_filters( 'woocommerce_subcategory_count_html', ' <mark class="count">(' . $category->count . ')</mark>', $category );
				?>
			</h3>

			<?php
				/**
				 * woocommerce_after_subcategory_title hook
				 */
				do_action( 'woocommerce_after_subcategory_title', $category );
			?>

		</a>

		<?php do_action( 'woocommerce_after_subcategory', $category ); ?>

	</div>
</div>