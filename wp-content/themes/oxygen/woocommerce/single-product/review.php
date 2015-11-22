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

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$rating = intval( get_comment_meta( $comment->comment_ID, 'rating', true ) );
?>
<li itemprop="reviews" itemscope itemtype="http://schema.org/Review" <?php comment_class("comment author_post"); ?> id="comment-<?php comment_ID(); ?>">

	<div class="comment_container comment-entry">

		<div class="comment-inner-body">
			
			<div class="comment-thumb">
				<div class="author_img"><?php echo get_avatar( $comment, apply_filters( 'woocommerce_review_gravatar_size', '80' ), '', get_comment_author() ); ?></div>
			</div>
	
			<div class="comment-details">
				
				<div class="author_about_part">
					
					<?php if ( $rating && get_option( 'woocommerce_enable_review_rating' ) == 'yes' ) : ?>
		
						<div itemprop="reviewRating" itemscope itemtype="http://schema.org/Rating" class="star-rating" title="<?php echo sprintf( __( 'Rated %d out of 5', 'woocommerce' ), $rating ) ?>">
						
							<div class="rating filled-<?php echo absint($rating); ?>">
								<span class="glyphicon glyphicon-star star-1"></span>
								<span class="glyphicon glyphicon-star star-2"></span>
								<span class="glyphicon glyphicon-star star-3"></span>
								<span class="glyphicon glyphicon-star star-4"></span>
								<span class="glyphicon glyphicon-star star-5"></span>
							</div>
							
						</div>
		
					<?php endif; ?>
					
					<time itemprop="datePublished" datetime="<?php echo get_comment_date( 'c' ); ?>"><?php echo get_comment_date( __( get_option( 'date_format' ), 'woocommerce' ) ); ?></time>
		
					<?php if ( $comment->comment_approved == '0' ) : ?>
		
						<p class="meta"><em><?php _e( 'Your comment is awaiting approval', 'woocommerce' ); ?></em></p>
		
					<?php else : ?>
		
						<p class="meta">
							<strong itemprop="author"><?php comment_author(); ?></strong> <?php
								
								if ( get_option( 'woocommerce_review_rating_verification_label' ) === 'yes' )
									if ( wc_customer_bought_product( $comment->comment_author_email, $comment->user_id, $comment->comment_post_ID ) )
										echo '<em class="verified">' . __( 'verified owner', 'woocommerce' ) . '</em> ';
		
							?> <?php _e('Says: ', 'oxygen'); ?>
						</p>
		
					<?php endif; ?>
		
					<div itemprop="description" class="description comment-content"><?php comment_text(); ?></div>
					
				</div>
				
			</div>
		</div>
	</div>
