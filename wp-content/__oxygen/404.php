<?php
/**
 *	Oxygen WordPress Theme
 *	
 *	Laborator.co
 *	www.laborator.co 
 */



get_header();

?>
<div class="wrapper">

	<div class="center">
	
		<div class="col-lg-5">
			<a href="<?php echo home_url(); ?>">
				<img class="404-image" src="<?php echo THEMEASSETS; ?>img/404.png" />
			</a>
		</div>
		
		<div class="col-lg-7">
			
			<h2><?php _e('This page does not exist!', 'oxygen'); ?></h2>
			
			<a href="<?php echo home_url(); ?>">
				<span>&laquo;</span> 
				<?php _e('Go back to home page', 'oxygen'); ?>
			</a>
			
		</div>
		
	</div>
	
</div>

<?php

get_footer();
