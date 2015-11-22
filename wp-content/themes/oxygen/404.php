<?php
/**
 *	Oxygen WordPress Theme
 *	
 *	Laborator.co
 *	www.laborator.co 
 */

define("NO_HEADER_MENU", true);
define("NO_FOOTER_MENU", true);

add_filter('body_class', create_function('$classes', '$classes[] = "not-found"; return $classes;'));

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