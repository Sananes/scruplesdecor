<?php
/**
 * The template for displaying product content within loops.
 *
 * Override this template by copying it to yourtheme/woocommerce/content-product.php
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     1.6.4
 */

/* Note: This file has been altered by Laborator */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $product, $woocommerce_loop;

// Store loop count we're currently on
if ( empty( $woocommerce_loop['loop'] ) )
	$woocommerce_loop['loop'] = 0;

// Store column count for displaying the grid
if ( empty( $woocommerce_loop['columns'] ) )
	$woocommerce_loop['columns'] = apply_filters( 'loop_shop_columns', 4 );

// Ensure visibility
if ( ! $product || ! $product->is_visible() )
	return;

// Increase loop count
$woocommerce_loop['loop']++;

// Extra post classes
$classes = array();
if ( 0 == ( $woocommerce_loop['loop'] - 1 ) % $woocommerce_loop['columns'] || 1 == $woocommerce_loop['columns'] )
	$classes[] = 'first';
if ( 0 == $woocommerce_loop['loop'] % $woocommerce_loop['columns'] )
	$classes[] = 'last';

# start: modified by Arlind Nushi
$classes[] = 'shop-item';

switch(get_data('shop_item_preview_type'))
{
	case "fade":
		$classes[] = 'hover-effect-1';
		break;

	case "slide":
		$classes[] = 'hover-effect-1 image-slide';
		break;

	case "gallery":
		$classes[] = 'hover-effect-2 image-slide';
		break;
}

$item_colums = '';
$shop_columns = SHOP_COLUMNS;

global $is_related_products, $products_columns, $i;

$do_clear = false;

if($products_columns > 0)
	$shop_columns = $products_columns;

if($is_related_products)
	$shop_columns = 4;


if($i >= 0)
{
	$do_clear = ($i+1) % $shop_columns == 0;
}

switch($shop_columns)
{
	case 6:
		$item_colums = 'col-lg-2 col-md-2 col-sm-2';
		break;

	case 4:
		$item_colums = 'col-lg-3 col-md-3 col-sm-6';
		break;

	case 3:
		$item_colums = 'col-lg-4 col-md-4 col-sm-6';
		break;

	case 2:
		$item_colums = 'col-sm-6 col-xs-12';
		break;

	case 1:
		$item_colums = 'col-xs-12';
		break;
}

if($products_columns == -1)
	$item_colums = '';
# end: modified by Arlind Nushi
?>
<?php if($item_colums): ?>
<div class="<?php echo $item_colums; ?>">
<?php endif; ?>

	<div <?php post_class( $classes ); ?>>

		<?php do_action( 'woocommerce_before_shop_loop_item' ); ?>

		<a href="<?php the_permalink(); ?>">

			<?php
				/**
				 * woocommerce_before_shop_loop_item_title hook
				 *
				 * @hooked woocommerce_show_product_loop_sale_flash - 10
				 * @hooked woocommerce_template_loop_product_thumbnail - 10
				 */
				do_action( 'woocommerce_before_shop_loop_item_title' );
			?>

			<div class="item-image">
				<?php get_template_part('tpls/woocommerce-item-thumbnail'); ?>

				<?php if(get_data('shop_item_preview_type') != 'none'): ?>
				<div class="bounce-loader">
					<div class="loading loading-0"></div>
					<div class="loading loading-1"></div>
					<div class="loading loading-2"></div>
				</div>
				<?php endif; ?>
			</div>

		</a>


		<div class="item-info">
			<h3<?php echo ! get('shop_add_to_cart_listing') ? ' class="no-right-margin"' : ''; ?>>
				<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
			</h3>

			<?php if(get_data('shop_product_category_listing')): ?>
			<span class="product-terms">
				<?php the_terms($id, 'product_cat'); ?>
			</span>
			<?php endif; ?>

			<?php
				/**
				 * woocommerce_after_shop_loop_item_title hook
				 *
				 * @hooked woocommerce_template_loop_rating - 5
				 * @hooked woocommerce_template_loop_price - 10
				 */
				do_action( 'woocommerce_after_shop_loop_item_title' );
			?>

		<?php do_action( 'woocommerce_after_shop_loop_item' ); ?>
		</div>

	</div>

<?php if($item_colums): ?>
</div>

<?php
if($do_clear):

	switch($shop_columns):

		case 3: ?><div class="clear-md"></div><?php break;
		default: ?><div class="clear"></div><?php break;

	endswitch;

endif;
?>

<?php endif; ?>