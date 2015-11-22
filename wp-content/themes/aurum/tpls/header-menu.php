<?php
/**
 *	Aurum WordPress Theme
 *
 *	Laborator.co
 *	www.laborator.co
 */


$menu = wp_nav_menu(
	array(
		'theme_location'  => 'main-menu',
		'container'       => '',
		'menu_class'      => 'nav',
		'echo'            => false
	)
);

?>
<nav class="main-menu" role="navigation">
	<?php echo $menu; ?>
</nav>