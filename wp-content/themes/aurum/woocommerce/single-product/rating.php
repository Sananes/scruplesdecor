<?php
/**
 * Single Product Rating
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.1.0
 */

/* Note: This file has been altered by Laborator */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $product;

if ( get_option( 'woocommerce_enable_review_rating' ) === 'no' || get_data('shop_single_rating') == false)
	return;

$count   = $product->get_rating_count();
$average = $product->get_average_rating();

if ( $count > 0 ) : ?>

	<div class="woocommerce-product-rating" itemprop="aggregateRating" itemscope itemtype="http://schema.org/AggregateRating">
		<div class="star-rating" title="<?php printf( __( 'Rated %s out of 5', 'woocommerce' ), $average ); ?>">

			<meta itemprop="ratingValue" content="<?php echo esc_html( $average ); ?>" />
		    <meta itemprop="ratingCount" content="<?php echo $count; ?>" />

			<div class="star-rating-icons" class="tooltip" data-toggle="tooltip" data-placement="<?php echo ! is_rtl() ? 'right' : 'left'; ?>" title="<?php echo esc_html( $average ); ?> <?php _e( 'out of 5', 'woocommerce' ); ?>">
				<?php for($i=1; $i<=5; $i++): ?>
				<i class="entypo-star<?php echo $average >= $i || ($average > 0 && intval($average) == $i - 1 && ($average - intval($average) > 0.49)) ? ' filled' : ''; ?>"></i>
				<?php endfor; ?>
			</div>
		</div>
		<a href="#reviews" class="woocommerce-review-link" rel="nofollow">(<?php printf( _n( '%s customer review', '%s customer reviews', $count, 'woocommerce' ), '<span itemprop="ratingCount" class="count">' . $count . '</span>' ); ?>)</a>
	</div>

<?php endif; ?>