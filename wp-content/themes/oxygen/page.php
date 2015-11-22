<?php
/**
 *	Oxygen WordPress Theme
 *	
 *	Laborator.co
 *	www.laborator.co 
 */

the_post();

get_header();

# Analyze page content
$content = get_the_content();
$is_vc_page = preg_match("/(\[.*?\])/", $content);


?>
<div class="page-container">

	<?php if($is_vc_page === false): ?>
		<div class="col-md-12">
			<div class="white-block block-pad">
				<h1 class="single-page-title"><?php echo the_title(); ?></h1>
				
				<div class="post-content">
					<?php the_content(); ?>
				</div>
				
			</div>
		</div>
	<?php else: ?>
		<?php the_content(); ?>
	<?php endif; ?>
	
</div>
<?php

get_footer();