<?php $quote = get_post_meta($post->ID, 'post_quote', true); ?>
<?php $author = get_post_meta($post->ID, 'post_quote_author', true); ?>
<div class="post-gallery quote">
	<blockquote>
		<?php if($quote) { echo '<p>'.$quote.'</p>'; } else { echo 'Please enter a quote using the metaboxes'; } ?>
		<?php if($author) { echo '<cite>'. $author.'</cite>'; } ?>
	</blockquote>
</div>