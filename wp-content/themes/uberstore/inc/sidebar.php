<?php
if ( function_exists('register_sidebar') ){
	register_sidebar(array('name' => 'Blog', 'id' => 'blog', 'description' => 'The sidebar that shows up in your blog', 'before_widget' => '<div id="%1$s" class="widget cf %2$s">', 'after_widget' => '</div>', 'before_title' => '<div class="title">', 'after_title' => '</div>'));

	register_sidebar(array('name' => 'Article Sidebar', 'id' => 'single', 'description' => 'The sidebar next to articles', 'before_widget' => '<div id="%1$s" class="widget cf %2$s">', 'after_widget' => '</div>', 'before_title' => '<div class="title">', 'after_title' => '</div>'));
	
	register_sidebar(array('name' => 'Shop Sidebar', 'id' => 'shop', 'description' => 'The sidebar visible in the shop page, if its enabled in theme options', 'before_widget' => '<div id="%1$s" class="widget cf %2$s">', 'after_widget' => '</div>', 'before_title' => '<div class="title">', 'after_title' => '</div>'));
	
	register_sidebar(array('name' => 'Footer Column 1', 'id' => 'footer1', 'description' => 'Footer - first column', 'before_widget' => '<div id="%1$s" class="widget cf %2$s">', 'after_widget' => '</div>', 'before_title' => '<div class="title">', 'after_title' => '</div>'));

	register_sidebar(array('name' => 'Footer Column 2', 'id' => 'footer2', 'description' => 'Footer - second column', 'before_widget' => '<div id="%1$s" class="widget cf %2$s">', 'after_widget' => '</div>', 'before_title' => '<div class="title">', 'after_title' => '</div>'));

	register_sidebar(array('name' => 'Footer Column 3', 'id' => 'footer3', 'description' => 'Footer - third column', 'before_widget' => '<div id="%1$s" class="widget cf %2$s">', 'after_widget' => '</div>', 'before_title' => '<div class="title">', 'after_title' => '</div>'));

	register_sidebar(array('name' => 'Footer Column 4', 'id' => 'footer4', 'description' => 'Footer - forth column', 'before_widget' => '<div id="%1$s" class="widget cf %2$s">', 'after_widget' => '</div>', 'before_title' => '<div class="title">', 'after_title' => '</div>'));
	
	register_sidebar(array('name' => 'Footer Column 5', 'id' => 'footer5', 'description' => 'Footer - fifth column', 'before_widget' => '<div id="%1$s" class="widget cf %2$s">', 'after_widget' => '</div>', 'before_title' => '<div class="title">', 'after_title' => '</div>'));
	
	register_sidebar(array('name' => 'Footer Column 6', 'id' => 'footer6', 'description' => 'Footer - sixth column', 'before_widget' => '<div id="%1$s" class="widget cf %2$s">', 'after_widget' => '</div>', 'before_title' => '<div class="title">', 'after_title' => '</div>'));
}

function thb_sidebar_setup() {
	$sidebars = ot_get_option('sidebars');
	if(!empty($sidebars)) {
		foreach($sidebars as $sidebar) {
			register_sidebar( array(
				'name' => $sidebar['title'],
				'id' => $sidebar['id'],
				'description' => '',
				'before_widget' => '<div id="%1$s" class="widget cf %2$s">',
				'after_widget' => '</div>',
				'before_title' => '<div class="title">',
				'after_title' => '</div>',
			));
		}
	}
}
add_action( 'after_setup_theme', 'thb_sidebar_setup' );
?>