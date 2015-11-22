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
<li itemprop="reviews" itemscope itemtype="http://schema.org/Review" <?php comment_class(); ?> id="li-comment-<?php comment_ID() ?>">
	<div id="comment-<?php comment_ID(); ?>" class="comment-inner">
			<figure class="vcard">
				<?php echo get_avatar( $comment, apply_filters( 'woocommerce_review_gravatar_size', '60' ), '', get_comment_author() ); ?>
			</figure>
			<div class="comment-container">
			<?php if ( $rating && get_option( 'woocommerce_enable_review_rating' ) == 'yes' ) : ?>
				<div itemprop="reviewRating" itemscope itemtype="http://schema.org/Rating" class="star-rating" title="<?php echo sprintf( __( 'Rated %d out of 5', THB_THEME_NAME ), $rating ) ?>">
					<span style="width:<?php echo ( $rating / 5 ) * 100; ?>%"><strong itemprop="ratingValue"><?php echo $rating; ?></strong> <?php _e( 'out of 5', THB_THEME_NAME ); ?></span>
				</div>
			<?php endif; ?>
               
			<div class="commentmeta">
			    <strong itemprop="author"><?php comment_author(); ?></strong>
			</div>
			<div itemprop="description" class="comment-text">
				<?php comment_text(); ?>
				<div class="commentmeta">
				    <span class="authorname">
				        <time itemprop="datePublished" datetime="<?php echo get_comment_date('c'); ?>"><?php echo get_comment_date(__( get_option('date_format'), THB_THEME_NAME )); ?></time>
				    </span>
				</div>
				<?php if ( $comment->comment_approved == '0' ) : ?>
				    <em class="awaiting_moderation"><?php _e('Your comment is awaiting moderation.', THB_THEME_NAME) ?></em>
				<?php endif; ?>
			</div>
		</div>
	</div><!-- .comment-inner -->
