<?php
/**
 * Display single product reviews (comments)
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.1.0
 */
global $woocommerce, $product;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

?>
<?php if ( comments_open() ) : ?><div id="reviews"><?php

	echo '<div id="comments">';
	
	$commenter = wp_get_current_commenter();
	
	
		echo '<aside id="add_review"><a class="close" href="#">'. __("close", THB_THEME_NAME ) .'</a>';

	
		$comment_form = array(
			'title_reply' => false,
			'comment_notes_before' => '',
			'comment_notes_after' => '',
			'fields' => array(
				'author' => '<div class="row"><div class="six columns">' . '<label for="author">' . __( 'Name', THB_THEME_NAME ) . ' <span class="required">*</span></label> ' .
				            '<input id="author" name="author" type="text" value="' . esc_attr( $commenter['comment_author'] ) . '" size="30" aria-required="true" /></div>',
				'email'  => '<div class="six columns"><label for="email">' . __( 'Email', THB_THEME_NAME ) . ' <span class="required">*</span></label> ' .
				            '<input id="email" name="email" type="text" value="' . esc_attr(  $commenter['comment_author_email'] ) . '" size="30" aria-required="true" /></div></div>',
			),
			'label_submit' => __( 'Submit Review', THB_THEME_NAME ),
			'logged_in_as' => '',
			'comment_field' => ''
		);
	
		if ( get_option('woocommerce_enable_review_rating') == 'yes' ) {
	
			$comment_form['comment_field'] = '<p class="comment-form-rating"><label for="rating">' . __( 'Rating', THB_THEME_NAME ) .'</label><select name="rating" id="rating">
				<option value="">'.__( 'Rate&hellip;', THB_THEME_NAME ).'</option>
				<option value="5">'.__( 'Perfect', THB_THEME_NAME ).'</option>
				<option value="4">'.__( 'Good', THB_THEME_NAME ).'</option>
				<option value="3">'.__( 'Average', THB_THEME_NAME ).'</option>
				<option value="2">'.__( 'Not that bad', THB_THEME_NAME ).'</option>
				<option value="1">'.__( 'Very Poor', THB_THEME_NAME ).'</option>
			</select></p>';
	
		}
	
		$comment_form['comment_field'] .= '<div class="row"><div class="twelve columns"><label for="comment">' . __( 'Your Review', THB_THEME_NAME ) . '</label><textarea id="comment" name="comment" cols="45" rows="22" aria-required="true"></textarea></div></div>' . wp_nonce_field( 'woocommerce-comment_rating', '_wpnonce', true, false );
	
		
		comment_form( apply_filters( 'woocommerce_product_review_comment_form_args', $comment_form ) );
	
		echo '</aside>';

		echo '<a href="#" id="add_review_button" class="btn grey small">'.__( 'Add a Review', THB_THEME_NAME ).'</a>';
	
	

	

	if ( have_comments() ) :

		echo '<ol class="commentlist">';

		wp_list_comments( apply_filters( 'woocommerce_product_review_list_args', array( 'callback' => 'woocommerce_comments' ) ) );

		echo '</ol>';

		if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : ?>
			<div class="navigation">
				<div class="nav-previous"><?php previous_comments_link( __( '<span class="meta-nav">&larr;</span> Previous', THB_THEME_NAME ) ); ?></div>
				<div class="nav-next"><?php next_comments_link( __( 'Next <span class="meta-nav">&rarr;</span>', THB_THEME_NAME ) ); ?></div>
			</div>
		<?php endif;

	endif;


?></div></div>
<?php endif; ?>