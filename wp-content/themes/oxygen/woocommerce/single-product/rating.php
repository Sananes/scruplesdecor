<?php
/**
 * Single Product Rating
 *
 * @author      WooThemes
 * @package     WooCommerce/Templates
 * @version     2.3.2
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $product;

if ( get_option( 'woocommerce_enable_review_rating' ) === 'no' ) {
	return;
}

$rating_count = $product->get_rating_count();
$review_count = $product->get_review_count();
$average      = $product->get_average_rating();

if ( $rating_count > 0 ) : ?>

	<div class="woocommerce-product-rating<?php echo ! get_data('shop_single_next_prev') ? ' pull-left' : ''; ?> rating filled-<?php echo absint($average); echo $average - intval($average) > .49 ? ' and-half' : ''; ?>" itemprop="aggregateRating" itemscope itemtype="http://schema.org/AggregateRating" title="<?php printf( __( 'Rated %s out of 5', 'woocommerce' ), $average ); ?>">

		<span class="glyphicon glyphicon-star star-1"></span>
		<span class="glyphicon glyphicon-star star-2"></span>
		<span class="glyphicon glyphicon-star star-3"></span>
		<span class="glyphicon glyphicon-star star-4"></span>
		<span class="glyphicon glyphicon-star star-5"></span>

	</div>

<?php endif; ?>
