<?php
/**
 * Single Product title
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     1.6.4
 */

global $product, $show_rating_below_title, $is_quickview;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$stars = '';

if( ! $product)
	return;

if($show_rating_below_title)
{
	$count   = $product->get_rating_count();
	$average = $product->get_average_rating();

	if($count)
	{
		$stars = "<div class=\"rating rating-inline filled-" . absint($average) . ($average - intval($average) > .49 ? ' and-half' : '') . "\" itemprop=\"aggregateRating\" itemscope itemtype=\"http://schema.org/AggregateRating\" title=\"" . sprintf( __( 'Rated %s out of 5', 'woocommerce' ), $average ) . "\">

		<span class=\"glyphicon glyphicon-star star-1\"></span>
		<span class=\"glyphicon glyphicon-star star-2\"></span>
		<span class=\"glyphicon glyphicon-star star-3\"></span>
		<span class=\"glyphicon glyphicon-star star-4\"></span>
		<span class=\"glyphicon glyphicon-star star-5\"></span>

	</div>";
	}
}
?>
<?php if( ! $is_quickview): ?>
<h2 itemprop="name" class="product_title entry-title"><?php the_title(); ?></h2>
<?php else: ?>
<h1 itemprop="name" class="product_title entry-title"><?php the_title(); ?></h1>
<?php endif; ?>

<?php if(get_data('shop_single_product_category')): ?>

	<?php echo $product->get_categories( ', ', '<span class="posted_in">', $stars . '</span>' ); ?>

<?php else: ?>

	<br />

<?php endif; ?>