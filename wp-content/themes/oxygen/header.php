<!DOCTYPE html>
<!--[if lt IE 7 ]><html class="ie ie6" <?php language_attributes(); ?>> <![endif]-->
<!--[if IE 7 ]><html class="ie ie7" <?php language_attributes(); ?>> <![endif]-->
<!--[if IE 8 ]><html class="ie ie8" <?php language_attributes(); ?>> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!--><html <?php language_attributes(); ?>> <!--<![endif]-->
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />

	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">

	<title><?php wp_title('|', true, 'right'); ?></title>

	<?php wp_head(); ?>


	<!--[if lt IE 9]><script src="<?php echo THEMEASSETS; ?>js/ie8-responsive-file-warning.js"></script><![endif]-->
	<!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->

	<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
	<!--[if lt IE 9]>
	<script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
	<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
	<![endif]-->

</head>
<body <?php body_class(); ?>>

	<?php if(function_exists('WC')): ?>
		<?php get_template_part('tpls/header-cart'); ?>
	<?php endif; ?>

	<?php if( ! defined("NO_HEADER_MENU")): ?>
	<div class="wrapper">

		<?php

		define("HAS_SLIDER", in_array('revslider/revslider.php', apply_filters('active_plugins', get_option( 'active_plugins'))) && function_exists("register_field_group") && ($revslider_id = get_field('revslider_id')));

		# Menu
		if(HEADER_TYPE == 1)
		{
			get_sidebar('menu');
		}
		else
		if(in_array(HEADER_TYPE, array(2,3,4)))
		{
			get_sidebar('menu-top');

			# Slider
			if(HAS_SLIDER)
			{
				if(is_search())
					echo "<div style='height: 30px;'></div>";
				else
					echo putRevSlider($revslider_id);
			}
		}
		?>

		<div class="main<?php echo HEADER_TYPE == 1 && HAS_SLIDER ? ' hide-breadcrumb' : ''; ?>">

			<?php get_template_part('tpls/breadcrumb'); ?>

			<?php
			# Slider
			if(HEADER_TYPE == 1 && HAS_SLIDER):

				?>
				<div class="rev-slider-container row">
					<?php echo putRevSlider($revslider_id); ?>
				</div>
				<?php

			endif;
			?>

	<?php endif; ?>


