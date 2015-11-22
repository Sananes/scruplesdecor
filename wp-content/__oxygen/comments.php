<?php
/**
 *	Oxygen WordPress Theme
 *	
 *	Laborator.co
 *	www.laborator.co 
 */

global $authordata, $comments_count;

if( ! (have_comments() || comments_open()))
	return;

$list_args = array(
	'callback' => 'laborator_list_comments_open', 
	'end-callback' => 'laborator_list_comments_close'
);

$form_args = array(
	'title_reply' 			=> '<h1 class="title">' . __('Leave a Comment', 'oxygen') . '</h1>',
	'title_reply_to' 		=> '<h3>' . __('Leave a Reply to %s', 'oxygen') . '</h3>',
	
	'comment_notes_before' 	=> '',
	'comment_notes_after' 	=> '',
	
	'label_submit'			=> __('Comment', 'oxygen'),
	
	'comment_field'			=> '
	<div class="row">
		<div class="col-lg-12 mobile-padding">
			<label for="comment">' . __('Message', 'oxygen') . ' <span class="red">*</span></label>
			<textarea id="comment" name="comment" class="autogrow" placeholder="' . __('Message:', 'oxygen') . '" rows="3" aria-required="true"></textarea>
		</div>	
	</div>'
);

add_filter('comment_form_default_fields', 'laborator_comment_fields');

add_action('comment_form', 'laborator_commenting_rules');
add_action('comment_form_before_fields', 'laborator_comment_before_fields');
add_action('comment_form_after_fields', 'laborator_comment_after_fields');
?>
<!-- comments --> 
<div class="comments" id="comments">
	
	<?php if(have_comments()): ?>
	<h1>
		<?php _e('Comments', 'oxygen'); ?> 
		<span>(<?php echo $comments_count; ?>)</span>
	</h1>
	
	
	<?php wp_list_comments($list_args); ?>
	
	<?php	
		$comments_pagination = paginate_comments_links(array('echo' => false));
		
		if($comments_pagination)
		{
			?>
			<div class="row">
				<div class="large-9 large-offset-3 medium-9 medium-offset-3 columns">
					<div class="comments-pagination">
						<?php echo $comments_pagination; ?>
					</div>
				</div>
			</div>
			<?php
		}
	?>
	
	<?php endif; ?>
	
	<?php if(comments_open()): ?>
	<!-- / reply form-->	
	<div class="reply_form<?php echo ! have_comments() ? ' form-only' : ''; ?>">
		
		<?php echo comment_form($form_args); ?>
		
	</div>
	<!-- / reply form end-->
	<?php endif; ?>
	
</div>