<?php $text = get_post_meta($post->ID, 'post_link_text', true); ?>
<?php $url = get_post_meta($post->ID, 'post_link_url', true); ?>
<div class="post-gallery link">
	<?php if($text && $url) { echo '<strong>'. $text.'</strong><a href="'.$url.'" title="'.$text.'" target="_blank">'. $url.'</a>'; } ?>
</div>