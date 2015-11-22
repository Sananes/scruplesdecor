<?php
/**
 *	Oxygen WordPress Theme
 *	
 *	Laborator.co
 *	www.laborator.co 
 */

if(in_array(HEADER_TYPE, array(2,3,4)))
	return;
	
$menu_locations = get_nav_menu_locations();
$has_top_menu = isset($menu_locations['top-menu']) && $menu_locations['top-menu'] > 0;
?>
<!-- breadcrumb -->
<div class="top-first">

	<div class="row">
	
		<div class="col-lg-<?php echo $has_top_menu ? 5 : 11; ?>">
		
			<div class="left-widget">
			<?php 
				function_exists('is_shop') && (is_shop() || is_product()) ? 
					woocommerce_breadcrumb(array(
						'wrap_before' => '<div class="breadcrumbs">', 
						'wrap_after' => '</div>',
						'before' => '<span>',
						'after' => '</span>',
						'delimiter' => ''
					)) 
						: 
					dimox_breadcrumbs(true, true)
				;
			?>
			</div>
			
			<?php /*<div class="breadcrumbs">
				<a href="#">Home</a> 
				<a href="#">Soap</a> 
				<a class="active" href="#">Men</a>
			</div>*/ ?>
			
		</div>
		
		<?php if($has_top_menu): ?>
		<div class="col-lg-7">	
		
			<div class="right-widget">
				<div class="breadcrumb-menu">
					<?php 
					wp_nav_menu(array(
						'theme_location' => 'top-menu',
						'container' => '',
						'menu_class' => 'nav',
						'depth' => 1
						
					)); 
					?>
				</div>
			</div>
		</div>
		<?php endif; ?>
		
		
		<?php get_template_part('tpls/cart-ribbon'); ?>
		
	</div>
	
</div>
<!-- / end breadcrumb -->