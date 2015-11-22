<?php
/**
 *	Oxygen WordPress Theme
 *	
 *	Laborator.co
 *	www.laborator.co 
 */

?>
		<!-- Sidebar Menu -->
		<div class="main-sidebar<?php echo get_data('header_menu_search') ? ' has-search' : ''; ?>">
		
			<div class="sidebar-inner">
			
				<?php get_template_part('tpls/logo'); ?>
				
				<div class="sidebar-menu<?php echo get_data('sidebar_menu_links_display') == 'Collapsed' ? ' collapsed-subs' : ''; ?>">
				<?php
					$args = array(
						'theme_location' => 'main-menu',
						'container' => '',
						'menu_class' => 'nav',
						'walker' => new Main_Menu_Walker()
					);
					
					wp_nav_menu($args);
					
					if( get_data('top_menu_social') )
					{
						echo do_shortcode('[lab_social_networks]');
					}
				?>
				</div>
				
			</div><!-- /sidebar-inner -->
			
			
			<?php if(get_data('header_menu_search')): ?>
			<form action="<?php echo home_url(); ?>" method="get" class="search" enctype="application/x-www-form-urlencoded">
				<input type="text" class="search_input" name="s" alt="" placeholder="<?php _e('Search...', 'oxygen'); ?>" value="<?php echo esc_attr(get('s')); ?>" /> 
				<span class="glyphicon glyphicon-search float_right"></span>
			</form>
			<?php endif; ?>
		
		</div><!-- /sidebar -->
		
