<?php
/**
 * Review Comments Template
 *
 * Closing li is left out on purpose!
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.1.0
 */

/* Note: This file has been altered by Laborator */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

# start: modified by Arlind Nushi
$comment_ID 			= $comment->comment_ID;
$comment_author 		= $comment->comment_author;
$comment_author_url		= $comment->comment_author_url;
$comment_author_email	= $comment->comment_author_email;
$comment_date 			= $comment->comment_date;
$comment_parent_ID 		= $comment->comment_parent;

$avatar = get_avatar( $comment, apply_filters( 'woocommerce_review_gravatar_size', '60' ), '', get_comment_author() );
$avatar = preg_replace("/\s?(height='[0-9]+'|width='[0-9]+')/", "", $avatar);

$comment_classes = array();

if($depth > 3)
	$comment_classes[] = 'col-md-offset-3';
elseif($depth > 2)
	$comment_classes[] = 'col-md-offset-2';
elseif($depth > 1)
	$comment_classes[] = 'col-md-offset-1';
# end: modified by Arlind Nushi

$rating = intval( get_comment_meta( $comment->comment_ID, 'rating', true ) );
?>
<div itemprop="reviews" itemscope itemtype="http://schema.org/Review" <?php comment_class($comment_classes); ?> id="li-comment-<?php comment_ID() ?>">

	<div id="comment-<?php comment_ID(); ?>" class="comment_container">

		<div class="avatar">
			<?php echo $avatar; ?>
		</div>

		<div class="comment-details">

			<?php if ( $rating && get_option( 'woocommerce_enable_review_rating' ) == 'yes' ) : ?>

				<div itemprop="reviewRating" itemscope itemtype="http://schema.org/Rating" class="star-rating" data-toggle="tooltip" data-placement="<?php echo is_rtl() ? 'right' : 'left'; ?>" title="<?php echo sprintf( __( 'Rated %d out of 5', 'woocommerce' ), $rating ) ?>">
					<!-- <span style="width:<?php echo ( $rating / 5 ) * 100; ?>%"><strong itemprop="ratingValue"><?php echo $rating; ?></strong> <?php _e( 'out of 5', 'woocommerce' ); ?></span> -->
					<?php for($i=1; $i<=5; $i++): ?>
						<i class="entypo-star<?php echo $rating >= $i ? ' filled' : ''; ?>"></i>
					<?php endfor; ?>
				</div>

			<?php endif; ?>

			<?php if ( $comment->comment_approved == '0' ) : ?>

				<p class="meta"><em><?php _e( 'Your comment is awaiting approval', 'woocommerce' ); ?></em></p>

			<?php else : ?>

				<p class="meta">
					<strong itemprop="author"><?php comment_author(); ?></strong> <?php

						if ( get_option( 'woocommerce_review_rating_verification_label' ) === 'yes' )
							if ( wc_customer_bought_product( $comment->comment_author_email, $comment->user_id, $comment->comment_post_ID ) )
								echo '<em class="verified">' . __( 'verified owner', 'woocommerce' ) . '</em> ';

					?>

					<time itemprop="datePublished" datetime="<?php echo get_comment_date( 'c' ); ?>"><?php echo get_comment_date( __( get_option( 'date_format' ), 'woocommerce' ) ); ?></time>
				</p>

			<?php endif; ?>

			<div itemprop="description" class="description"><?php comment_text(); ?></div>
		</div>
	</div>
