<?php
/**
 *	Oxygen WordPress Theme
 *
 *	Laborator.co
 *	www.laborator.co
 */

global $use_uploaded_logo, $custom_logo_image, $custom_logo_image_responsive, $custom_logo_max_width, $has_responsive_image;

$nav_menu_locations = get_theme_mod('nav_menu_locations');

$top_menu_args = array(
	'theme_location'   => 'top-menu',
	'container'        => '',
	'menu_class'       => 'sec-nav-menu',
	'depth'            => 1,
	'echo'             => false
);

$main_menu_args = array(
	'theme_location'   => 'main-menu',
	'container'        => '',
	'menu_class'       => 'nav',
	'walker'           => new Main_Menu_Walker(),
	'echo'             => false
);

$main_menu  = wp_nav_menu($main_menu_args);
$top_menu   = wp_nav_menu($top_menu_args);

$has_megamenu = class_exists('UberMenuStandard');

if($has_megamenu)
{
	$has_megamenu = in_array('main-menu', get_option('wp-mega-menu-nav-locations'));
}

if( ! isset($nav_menu_locations['main-menu']) || $nav_menu_locations['main-menu'] == 0)
	$main_menu = '';

$top_menu_social = get_data('top_menu_social');

if($top_menu_social)
{
	$top_menu .= '<div class="top-menu-social">
	' . do_shortcode('[lab_social_networks]') . '
	</div>';
}
?>

<?php if(HEADER_TYPE == 2): ?>
<div class="top-menu">

	<div class="main">

		<div class="row">

			<div class="col-sm-12">

				<div class="tl-header with-cart-ribbon">

					<?php get_template_part('tpls/logo'); ?>

					<nav class="sec-nav">

						<?php echo $top_menu; ?>

					</nav>

					<?php get_template_part('tpls/cart-ribbon'); ?>

				</div>

			</div>

		</div>

	</div>

</div>

<div class="main-menu-top<?php echo HAS_SLIDER ? ' has-slider' : ''; echo ! defined("GRAY_MENU") ? ' white-menu' : ''; ?>">

	<div class="main">

		<div class="row">

			<div class="col-md-12">

				<nav class="main-menu-env top-menu-type-2">

					<?php if(get_data('header_sticky_menu')): ?>
					<a href="<?php echo home_url(); ?>" class="logo-sticky<?php echo $use_uploaded_logo ? ' image-logo' : ''; ?>">
						<span>
						<?php if($use_uploaded_logo && $has_responsive_image): ?>
							<img class="hidden-sm hidden-xs" src="<?php echo $custom_logo_image; ?>" alt="<?php echo esc_attr(get_bloginfo('name')); ?>" />
							<img class="visible-sm visible-xs" src="<?php echo $custom_logo_image_responsive; ?>" alt="<?php echo esc_attr(get_bloginfo('name')); ?>"<?php if(isset($custom_logo_image_responsive_size)): ?> width="<?php echo absint($custom_logo_image_responsive_size[0]/2); ?>"<?php endif; ?> />
						<?php elseif($use_uploaded_logo): ?>
							<img src="<?php echo $custom_logo_image; ?>" alt="<?php echo esc_attr(get_bloginfo('name')); ?>" />
						<?php else: ?>
							<?php echo get_data('logo_text'); ?>
						<?php endif; ?>
						</span>
					</a>
					<?php endif; ?>

												<?php
								$args = array(
								  'taxonomy' => 'product_cat',
								  'show_option_none' => __('No Menu Items.'),
								  'echo' => 1,
								  'depth' => 2,
								  'wrap_class' => 'product-categories',
								  'level_class' => 'has-sub',
								  'current_class' => 'current-menu-item'
								);
								custom_list_categories( $args );
								?>

					<?php if($has_megamenu == false && get_data('header_menu_search')): ?>
					<form action="<?php echo home_url(); ?>" method="get" class="search-form" enctype="application/x-www-form-urlencoded">

						<a href="#">
							<span class="glyphicon glyphicon-search"></span>
						</a>

						<div class="search-input-env<?php echo trim(get('s')) ? ' visible' : ''; ?>">
							<input type="text" class="search-input" name="s" placeholder="<?php _e('Search...', 'oxygen'); ?>" value="<?php echo esc_attr(get('s')); ?>">
							<input type="hidden" name="post_type" value="product" />
						</div>

					</form>
					<?php endif; ?>

				</nav>

			</div>

		</div>

	</div>

</div>
<?php endif; # END OF: Header Type 2 ?>


<?php if(HEADER_TYPE == 3): ?>
<div class="top-menu main-menu-top<?php echo HAS_SLIDER ? ' has-slider' : ''; ?>">

	<div class="main">

		<div class="row">

			<div class="col-sm-12">

				<div class="tl-header with-cart-ribbon">

					<?php get_template_part('tpls/logo'); ?>

					<div class="sec-nav">

						<?php echo $top_menu; ?>

						<nav class="main-menu-env">

								<?php
								$args = array(
								  'taxonomy' => 'product_cat',
								  'show_option_none' => __('No Menu Items.'),
								  'echo' => 1,
								  'depth' => 2,
								  'wrap_class' => 'product-categories',
								  'level_class' => 'has-sub',
								  'current_class' => 'current-menu-item'
								);
								custom_list_categories( $args );
								?>
							<?php if(get_data('header_menu_search')): ?>
							<form action="<?php echo home_url(); ?>" method="get" class="search-form" enctype="application/x-www-form-urlencoded">

								<a href="#">
									<span class="glyphicon glyphicon-search"></span>
								</a>

								<div class="search-input-env<?php echo trim(get('s')) && HEADER_TYPE == 2 ? ' visible' : ''; ?>">
									<input type="text" class="search-input" name="s" alt="" placeholder="<?php _e('Search...', 'oxygen'); ?>" value="<?php echo esc_attr(get('s')); ?>" />
									<input type="hidden" name="post_type" value="product" />
								</div>

							</form>
							<?php endif; ?>

						</nav>

					</div>

					<?php get_template_part('tpls/cart-ribbon'); ?>

				</div>

			</div>

		</div>

	</div>

</div>
<?php endif; # END OF: Header Type 3 ?>


<?php if(HEADER_TYPE == 4): ?>
<div class="top-menu-centered<?php echo HAS_SLIDER ? ' has-slider' : ''; ?>">

	<div class="main">

		<div class="row">

			<div class="col-sm-12">

				<div class="tl-header with-cart-ribbon">

					<?php get_template_part('tpls/logo'); ?>

					<div class="navs">

						<nav class="main-menu-env">

							<?php if(get_data('header_menu_search')): ?>
							<form action="<?php echo home_url(); ?>" method="get" class="search-form" enctype="application/x-www-form-urlencoded">

								<a href="#">
									<span class="glyphicon glyphicon-search"></span>
								</a>

								<div class="search-input-env<?php echo trim(get('s')) && HEADER_TYPE == 2 ? ' visible' : ''; ?>">
									<input type="text" class="search-input" name="s" alt="" placeholder="<?php _e('Search...', 'oxygen'); ?>" value="<?php echo esc_attr(get('s')); ?>" />
									<input type="hidden" name="post_type" value="product" />
								</div>

							</form>
							<?php endif; ?>

							<?php echo $main_menu; ?>
						</nav>

						<br />

						<?php echo $top_menu; ?>

					</div>

					<?php get_template_part('tpls/cart-ribbon'); ?>

				</div>

			</div>

		</div>

	</div>

</div>
<?php endif; # END OF: Header Type 4 ?>
