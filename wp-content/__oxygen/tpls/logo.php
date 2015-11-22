<?php
/**
 *	Oxygen WordPress Theme
 *
 *	Laborator.co
 *	www.laborator.co
 */


global $use_uploaded_logo, $custom_logo_image, $custom_logo_image_responsive, $custom_logo_max_width, $has_responsive_image;

$use_uploaded_logo              = get_data('use_uploaded_logo');
$has_responsive_image			= false;

$custom_logo_image              = get_data('custom_logo_image');
$custom_logo_image_responsive   = get_data('custom_logo_image_responsive');

$custom_logo_max_width			= absint(get_data('custom_logo_max_width'));


if($use_uploaded_logo)
{
	$custom_logo_image_relative    = str_replace(site_url('/'), '', $custom_logo_image);
	#$use_uploaded_logo             = file_exists(ABSPATH . $custom_logo_image_relative);
}

# Responsive Image
if(get_data('use_uploaded_logo') && $custom_logo_image_responsive)
{
	$custom_logo_image_responsive_relative = str_replace(site_url('/'), '', $custom_logo_image_responsive);
	#$has_responsive_image = file_exists(ABSPATH . $custom_logo_image_responsive_relative);

	if($has_responsive_image && ! $custom_logo_max_width)
		$custom_logo_image_responsive_size = getimagesize(ABSPATH . $custom_logo_image_responsive_relative);

	if($has_responsive_image && ! $custom_logo_image)
		$custom_logo_image = $custom_logo_image_responsive_relative;
}

if($use_uploaded_logo)
{
	if(is_ssl())
	{
		if($custom_logo_image)
		{
			$custom_logo_image = str_replace('http:', 'https:', $custom_logo_image);
		}
		
		if($custom_logo_image_responsive)
		{
			$custom_logo_image_responsive = str_replace('http:', 'https:', $custom_logo_image_responsive);
		}
	}
}


?>
<!-- logo -->
<div class="logo<?php echo $use_uploaded_logo ? ' logo-image' : ''; ?>">

	<?php if($custom_logo_image && $custom_logo_max_width): ?>
	<style>.logo.logo-image img { max-width: <?php echo $custom_logo_max_width; ?>px } </style>
	<?php endif; ?>
	<h3>
		<a href="<?php echo home_url(); ?>">
		<?php if($use_uploaded_logo && $has_responsive_image): ?>
			<img class="hidden-sm hidden-xs" src="<?php echo $custom_logo_image; ?>" alt="<?php echo esc_attr(get_bloginfo('name')); ?>" />
			<img class="visible-sm visible-xs" src="<?php echo $custom_logo_image_responsive; ?>" alt="<?php echo esc_attr(get_bloginfo('name')); ?>"<?php if(isset($custom_logo_image_responsive_size)): ?> width="<?php echo absint($custom_logo_image_responsive_size[0]/2); ?>"<?php endif; ?> />
		<?php elseif($use_uploaded_logo): ?>
			<img src="<?php echo $custom_logo_image; ?>" alt="<?php echo esc_attr(get_bloginfo('name')); ?>" />
		<?php else: ?>
			<?php echo get_data('logo_text'); ?>
		<?php endif; ?>
		</a>
	</h3>

	<?php #if(HEADER_TYPE != 1): ?>
	<div class="mobile-menu-link">
		<a href="#">
			<i class="glyphicon glyphicon-align-justify"></i>
		</a>
	</div>
	<?php #endif; ?>

	<div class="divider"></div>
</div>
<!-- /logo -->

<!-- mobile menu -->
<div class="mobile-menu hidden">

	<?php  if(get_data('header_menu_search')): ?>
	<form action="<?php echo home_url(); ?>" method="get" class="search-form" enctype="application/x-www-form-urlencoded">

		<a href="#">
			<span class="glyphicon glyphicon-search"></span>
		</a>

		<div class="search-input-env<?php echo trim(get('s')) ? ' visible' : ''; ?>">
			<input type="text" class="search-input" name="s" placeholder="<?php _e('Search...', 'oxygen'); ?>" value="<?php echo esc_attr(get('s')); ?>">
		</div>

	</form>
	<?php endif; ?>

<?php
	// wp_nav_menu(array(
	// 	'theme_location'  => 'main-menu',
	// 	'container'       => '',
	// 	'menu_class'      => 'nav'
	// ));
	?>

								<?php
								$args = array(
								  'taxonomy' => 'product_cat',
								  'show_option_none' => __('No Menu Items.'),
								  'echo' => 1,
								  'depth' => 5,
								  'wrap_class' => 'product-categories',
								  'level_class' => 'menu-item-has-children',
								  'current_class' => 'current-menu-item'
								);
								custom_list_categories( $args );
								?>
	<?php
	if(get_data('cart_ribbon_show') && function_exists('WC')):

		?>
		<a href="<?php echo WC()->cart->get_cart_url(); ?>" class="cart-items">
			<span><?php echo WC()->cart->cart_contents_count; ?></span>
			<?php _e('Cart', 'oxygen'); ?>
		</a>
		<?php

	endif;
	?>
	
	<?php if(get_data('social_mobile_menu')): ?>
	<div class="social-networks-mobile">
		<?php echo do_shortcode( '[lab_social_networks]' ); ?>
	</div>
	<?php endif; ?>
</div>
<!-- / mobile menu -->

