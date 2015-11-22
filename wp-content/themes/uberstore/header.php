<?php global $woocommerce, $yith_wcwl; ?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<title><?php wp_title( '|', true, 'right' ); ?></title>
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<meta name="viewport" content="width=device-width, user-scalable=no">
	<meta name="apple-mobile-web-app-capable" content="yes">
	<meta name="apple-touch-fullscreen" content="yes">
	<meta http-equiv="cleartype" content="on">
	<meta name="HandheldFriendly" content="True">
	<?php if( $favicon = ot_get_option('favicon')){ ?>
	<link rel="shortcut icon" href="<?php echo $favicon; ?>">
	<?php } else {?>
	<link rel="shortcut icon" href="<?php echo THB_THEME_ROOT; ?>/assets/img/favicon.ico">
	<?php } ?>
	<?php $blank = is_page_template('template-blank.php'); ?>
	<?php if (isset($_GET['boxed'])) { $boxed = htmlspecialchars($_GET['boxed']); } else { $boxed = ot_get_option('boxed'); }  ?>
	<?php
		$class = array();
		if($boxed == 'yes') { 
			array_push($class, 'boxed');
	 	}
	 	if ($blank) {
	 		array_push($class, 'thb-blank');
	 	}
	?>
	<?php 
		/* Always have wp_head() just before the closing </head>
		 * tag of your theme, or you will break many plugins, which
		 * generally use this hook to add elements to <head> such
		 * as styles, scripts, and meta tags.
		 */
		wp_head(); 
	?>
</head>
<body <?php body_class($class); ?> data-url="<?php echo home_url(); ?>" data-cart-count="<?php echo $woocommerce->cart->cart_contents_count; ?>" data-sharrreurl="<?php echo THB_THEME_ROOT; ?>/inc/sharrre.php">
<?php if (!$blank) { ?>
<!-- Start Mobile Menu -->
<section id="sidr-main">
	<a href="#" id="sidr-close"></a>
	<?php get_search_form(); ?>
	<?php if(has_nav_menu('nav-menu')) { ?>
	  <?php wp_nav_menu( array( 'theme_location' => 'nav-menu', 'depth' => 3, 'container' => false, 'menu_class' => 'mobile-menu' ) ); ?>
	<?php } else { ?>
		<ul class="sf-menu">
					<li><a href="<?php echo get_admin_url().'nav-menus.php'; ?>">Please assign a menu from Appearance -> Menus</a></li>
				</ul>
	<?php } ?>
</section>
<!-- End Mobile Menu -->

<?php } // Blank page check ?>
<div id="wrapper">
<?php if (!$blank) { ?>

<!-- Start Header -->
<?php if (isset($_GET['header_style'])) { $header_style = htmlspecialchars($_GET['header_style']); } else { $header_style = ot_get_option('header_style'); }  ?>

<?php if( $header_style == 'style3' ) {  ?>
<header id="header" class="style3">
	<div class="row">
		<div class="four columns logo">
			<div class="row">
				<div class="ten mobile-two columns">
					<?php if (ot_get_option('logo')) { $logo = ot_get_option('logo'); } else { $logo = THB_THEME_ROOT. '/assets/img/logo-light.png'; } ?>
					
					<a href="<?php echo home_url(); ?>" class="logolink <?php if(ot_get_option('logo_mobile')) { ?>hide-logo<?php } ?>"><img src="<?php echo $logo; ?>" class="logoimg" alt="<?php bloginfo('name'); ?>"/></a>
					<?php if(ot_get_option('logo_mobile')) { ?>
						<a href="<?php echo home_url(); ?>" class="show-logo logolink"><img src="<?php echo ot_get_option('logo_mobile'); ?>" alt="<?php bloginfo('name'); ?>" /></a>
					<?php } ?>
				</div>
				<div class="two mobile-two columns show-for-small">
					<?php if (ot_get_option('header_cart') != 'off') { ?>
					<a href="<?php if($woocommerce) { echo $woocommerce->cart->get_cart_url(); }?>" title="<?php _e('View your shopping cart',THB_THEME_NAME); ?>" id="mobile-cart">
					</a>
					<?php } ?>
					<a href="#mobile-toggle" id="mobile-toggle">
						<i class="fa fa-bars"></i>
					</a>
				</div>
			</div>
		</div>
		<div class="eight columns">
			<aside class="mainbox">
				<nav id="subnav">
					<ul>
						<li>
							<a href="#searchpopup" rel="inline" data-class="searchpopup">
								<?php _e('<i class="fa fa-search"></i>',THB_THEME_NAME); ?>
							</a>
						</li>
						<li>
								<a href="<?php echo get_permalink( get_option('woocommerce_myaccount_page_id') ); ?>">
									<?php _e('<i class="fa fa-user"></i>', THB_THEME_NAME); ?>
								</a>
						</li>
						<?php if ($yith_wcwl) { ?>
						<li>
							<a href="<?php echo $yith_wcwl->get_wishlist_url(); ?>" title="<?php _e('Wishlist', THB_THEME_NAME); ?>"><i class="fa fa-heart-o"></i></a>
						</li>
						<?php } ?>
						<?php if (ot_get_option('header_cart') != 'off') { ?>
						<li>
							<?php if(in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) { ?>
								<div id="quick_cart">
									<a id="mycartbtn" href="<?php echo $woocommerce->cart->get_cart_url(); ?>" title="<?php _e('View your shopping cart',THB_THEME_NAME); ?>"> <span class="float_count"><?php echo $woocommerce->cart->cart_contents_count; ?></span></a>
										<div class="cart_holder">
										<ul class="cart_details">
											<?php if (sizeof($woocommerce->cart->cart_contents)>0) : foreach ($woocommerce->cart->cart_contents as $cart_item_key => $cart_item) :
											    $_product = $cart_item['data'];                                            
											    if ($_product->exists() && $cart_item['quantity']>0) :?>
												<li>
													<figure>
														<?php   echo '<a class="cart_list_product_img" href="'.get_permalink($cart_item['product_id']).'">' . $_product->get_image().'</a>'; ?>
													</figure>
													
													<?php echo apply_filters( 'woocommerce_cart_item_remove_link', sprintf('<a href="%s" class="remove" title="%s">×</a>', esc_url( $woocommerce->cart->get_remove_url( $cart_item_key ) ), __('Remove this item', THB_THEME_NAME) ), $cart_item_key ); ?>
													
													<div class="list_content">
														<?php 
														 $product_title = $_product->get_title();
													       echo '<h5><a href="'.get_permalink($cart_item['product_id']).'">' . apply_filters('woocommerce_cart_widget_product_title', $product_title, $_product) . '</a></h5>';
													       echo '<div class="quantity">'.$cart_item['quantity'].'</div>';
													       echo '<div class="price">'.woocommerce_price($_product->get_price()).'</div>';
														?>
													</div>
												</li>
											<?php endif; endforeach; ?>
												<div class="subtotal">                                        
												    <?php _e('subtotal', THB_THEME_NAME); ?><span><?php echo $woocommerce->cart->get_cart_total(); ?></span>                                   
												</div>
												
												<a href="<?php echo esc_url( $woocommerce->cart->get_cart_url() ); ?>" class="btn large grey"><?php _e('View Shopping Bag', THB_THEME_NAME); ?></a>   
												
												<a href="<?php echo esc_url( $woocommerce->cart->get_checkout_url() ); ?>" class="btn large"><?php _e('Checkout', THB_THEME_NAME); ?></a>
												
											<?php else: echo '<p class="empty">'.__('You have no products in your shopping bag.',THB_THEME_NAME).'</p>'; endif; ?>
										</ul>
										</div>
								</div>
							<?php } ?>
						</li>
						<?php } ?>
					</ul>
				</nav>
			</aside>
			<nav id="nav">
				<?php if(has_nav_menu('nav-menu')) { ?>
				  <?php wp_nav_menu( array( 'theme_location' => 'nav-menu', 'depth' => 3, 'container' => false, 'menu_class' => 'sf-menu', 'walker'          => new UberStoreNavDropdown  ) ); ?>
				<?php } else { ?>
					<ul class="sf-menu">
						<li><a href="<?php echo get_admin_url().'nav-menus.php'; ?>">Please assign a menu from Appearance -> Menus</a></li>
					</ul>
				<?php } ?>
			</nav>
		</div>
	</div>
</header>
<?php } else if( $header_style == 'style2' ) {  ?>
<div id="subheader" class="hide-for-small">
	<div class="row">
		<div class="four columns social">
			<?php if (ot_get_option('fb_link')) { ?>
			<a href="<?php echo ot_get_option('fb_link'); ?>" class="facebook icon-1x"><i class="fa fa-facebook"></i></a>
			<?php } ?>
			<?php if (ot_get_option('pinterest_link')) { ?>
			<a href="<?php echo ot_get_option('pinterest_link'); ?>" class="pinterest icon-1x"><i class="fa fa-pinterest"></i></a>
			<?php } ?>
			<?php if (ot_get_option('twitter_link')) { ?>
			<a href="<?php echo ot_get_option('twitter_link'); ?>" class="twitter icon-1x"><i class="fa fa-twitter"></i></a>
			<?php } ?>
			<?php if (ot_get_option('googleplus_link')) { ?>
			<a href="<?php echo ot_get_option('googleplus_link'); ?>" class="google-plus icon-1x"><i class="fa fa-google-plus"></i></a>
			<?php } ?>
			<?php if (ot_get_option('linkedin_link')) { ?>
			<a href="<?php echo ot_get_option('linkedin_link'); ?>" class="linkedin icon-1x"><i class="fa fa-linkedin"></i></a>
			<?php } ?>
			<?php if (ot_get_option('instragram_link')) { ?>
			<a href="<?php echo ot_get_option('instragram_link'); ?>" class="instagram icon-1x"><i class="fa fa-instagram"></i></a>
			<?php } ?>
			<?php if (ot_get_option('xing_link')) { ?>
			<a href="<?php echo ot_get_option('xing_link'); ?>" class="xing icon-1x"><i class="fa fa-xing"></i></a>
			<?php } ?>
			<?php if (ot_get_option('tumblr_link')) { ?>
			<a href="<?php echo ot_get_option('tumblr_link'); ?>" class="tumblr icon-1x"><i class="fa fa-tumblr"></i></a>
			<?php } ?>
		</div>
		<div class="eight columns style2">
			<nav id="subnav">
				<ul>
					<?php if (ot_get_option('header_cart') != 'off') { ?>
					<li>
						<a class="smallcartbtn" href="<?php if($woocommerce) { echo $woocommerce->cart->get_cart_url(); }?>" title="<?php _e('View your shopping cart',THB_THEME_NAME); ?>">
							(<?php if($woocommerce) { echo $woocommerce->cart->cart_contents_count; } ?> )
						</a>
					</li>
					<?php } ?>
					<li>
						<a href="<?php if($woocommerce) { echo $woocommerce->cart->get_checkout_url(); }?>">
							<?php _e('Checkout', THB_THEME_NAME); ?>
						</a>
					</li>
					<?php if ($yith_wcwl) { ?>
					<li>
						<a href="<?php echo $yith_wcwl->get_wishlist_url(); ?>" title="<?php _e('Wishlist', THB_THEME_NAME); ?>"><i class="fa fa-heart-o"></i> <?php _e('Wishlist', THB_THEME_NAME); ?></a>
					</li>
					<?php } ?>
					<li>
						<?php
							if ( is_user_logged_in() ) { ?> 
							<a href="<?php echo get_permalink( get_option('woocommerce_myaccount_page_id') ); ?>">
								<?php _e('My Account', THB_THEME_NAME); ?>
							</a>
							<?php } else { ?>
							<a href="<?php echo get_permalink( get_option('woocommerce_myaccount_page_id') ); ?>"><?php _e('Login', THB_THEME_NAME); ?></a>
						<?php } ?>
					</li>
					<li>
						<a href="#searchpopup" rel="inline" data-class="searchpopup">
							<?php _e('Search',THB_THEME_NAME); ?>
						</a>
					</li>
				</ul>
			</nav>
		</div>
	</div>
</div>
<header id="header" class="style2">
	<div class="row">
		<div class="four columns logo">
			<div class="row">
				<div class="ten mobile-two columns">
					<?php if (ot_get_option('logo')) { $logo = ot_get_option('logo'); } else { $logo = THB_THEME_ROOT. '/assets/img/logo-dark.png'; } ?>
					
					<a href="<?php echo home_url(); ?>" class="logolink <?php if(ot_get_option('logo_mobile')) { ?>hide-logo<?php } ?>"><img src="<?php echo $logo; ?>" class="logoimg" alt="<?php bloginfo('name'); ?>"/></a>
					<?php if(ot_get_option('logo_mobile')) { ?>
						<a href="<?php echo home_url(); ?>" class="show-logo logolink"><img src="<?php echo ot_get_option('logo_mobile'); ?>" alt="<?php bloginfo('name'); ?>" /></a>
					<?php } ?>
				</div>
				<div class="two mobile-two columns show-for-small">
					<?php if (ot_get_option('header_cart') != 'off') { ?>
					<a href="<?php if($woocommerce) { echo $woocommerce->cart->get_cart_url(); }?>" title="<?php _e('View your shopping cart',THB_THEME_NAME); ?>" id="mobile-cart">
					</a>
					<?php } ?>
					<a href="#mobile-toggle" id="mobile-toggle">
						<i class="fa fa-bars"></i>
					</a>
				</div>
			</div>
		</div>
		<div class="eight columns">
			<nav id="nav">
				<div class="row">
					<?php if(has_nav_menu('nav-menu')) { ?>
					  <?php wp_nav_menu( array( 'theme_location' => 'nav-menu', 'depth' => 3, 'container' => false, 'menu_class' => 'sf-menu', 'walker'          => new UberStoreNavDropdown  ) ); ?>
					<?php } else { ?>
						<ul class="sf-menu">
							<li><a href="<?php echo get_admin_url().'nav-menus.php'; ?>">Please assign a menu from Appearance -> Menus</a></li>
						</ul>
					<?php } ?>
				</div>
			</nav>
		</div>
	</div>
</header>
<?php } else {  ?>
<header id="header" class="style1">
	<div class="row">
		<div class="four columns logo">
			<div class="row">
				
				<div class="ten mobile-two columns">
					<?php if (ot_get_option('logo')) { $logo = ot_get_option('logo'); } else { $logo = THB_THEME_ROOT. '/assets/img/logo-light.png'; } ?>
					
					<a href="<?php echo home_url(); ?>" class="logolink <?php if(ot_get_option('logo_mobile')) { ?>hide-logo<?php } ?>"><img src="<?php echo $logo; ?>" class="logoimg" alt="<?php bloginfo('name'); ?>"/></a>
					<?php if(ot_get_option('logo_mobile')) { ?>
						<a href="<?php echo home_url(); ?>" class="show-logo logolink"><img src="<?php echo ot_get_option('logo_mobile'); ?>" alt="<?php bloginfo('name'); ?>" /></a>
					<?php } ?>
				</div>
				<div class="two mobile-two columns show-for-small">
					<?php if (ot_get_option('header_cart') != 'off') { ?>
					<a href="<?php if($woocommerce) { echo $woocommerce->cart->get_cart_url(); }?>" title="<?php _e('View your shopping cart',THB_THEME_NAME); ?>" id="mobile-cart">
					</a>
					<?php } ?>
					<a href="#mobile-toggle" id="mobile-toggle">
						<i class="fa fa-bars"></i>
					</a>
				</div>
			</div>
		</div>
		<div class="eight columns">
			<aside class="mainbox">
				<?php if(in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) )  &&  (ot_get_option('header_cart') != 'off') ) { ?>
					<div id="quick_cart">
						<a id="mycartbtn" href="<?php echo $woocommerce->cart->get_cart_url(); ?>" title="<?php _e('View your shopping cart',THB_THEME_NAME); ?>"> <span class="float_count"><?php echo $woocommerce->cart->cart_contents_count; ?></span></a>
							<div class="cart_holder">
							<ul class="cart_details">
								<?php if (sizeof($woocommerce->cart->cart_contents)>0) : foreach ($woocommerce->cart->cart_contents as $cart_item_key => $cart_item) :
								    $_product = $cart_item['data'];                                            
								    if ($_product->exists() && $cart_item['quantity']>0) :?>
									<li>
										<figure>
											<?php   echo '<a class="cart_list_product_img" href="'.get_permalink($cart_item['product_id']).'">' . $_product->get_image().'</a>'; ?>
										</figure>
										
										<?php echo apply_filters( 'woocommerce_cart_item_remove_link', sprintf('<a href="%s" class="remove" title="%s">×</a>', esc_url( $woocommerce->cart->get_remove_url( $cart_item_key ) ), __('Remove this item', THB_THEME_NAME) ), $cart_item_key ); ?>
										
										<div class="list_content">
											<?php 
											 $product_title = $_product->get_title();
										       echo '<h5><a href="'.get_permalink($cart_item['product_id']).'">' . apply_filters('woocommerce_cart_widget_product_title', $product_title, $_product) . '</a></h5>';
										       echo '<div class="quantity">'.$cart_item['quantity'].'</div>';
										       echo '<div class="price">'.woocommerce_price($_product->get_price()).'</div>';
											?>
										</div>
									</li>
								<?php endif; endforeach; ?>
									<div class="subtotal">                                        
									    <?php _e('subtotal', THB_THEME_NAME); ?><span><?php echo $woocommerce->cart->get_cart_total(); ?></span>                                   
									</div>
									
									<a href="<?php echo esc_url( $woocommerce->cart->get_cart_url() ); ?>" class="btn large grey"><?php _e('View Shopping Bag', THB_THEME_NAME); ?></a>   
									
									<a href="<?php echo esc_url( $woocommerce->cart->get_checkout_url() ); ?>" class="btn large"><?php _e('Checkout', THB_THEME_NAME); ?></a>
									
								<?php else: echo '<p class="empty">'.__('You have no products in your shopping bag.',THB_THEME_NAME).'</p>'; endif; ?>
							</ul>
							</div>
					</div>
				<?php } ?>
				<div class="navholder">
					<nav id="subnav">
						<ul>
							<li>
								<a href="<?php if($woocommerce) { echo $woocommerce->cart->get_checkout_url(); }?>">
									<?php _e('Checkout', THB_THEME_NAME); ?>
								</a>
							</li>
							<?php if ($yith_wcwl) { ?>
							<li>
								<a href="<?php echo $yith_wcwl->get_wishlist_url(); ?>" title="<?php _e('Wishlist', THB_THEME_NAME); ?>"><i class="fa fa-heart-o"></i> <?php _e('Wishlist', THB_THEME_NAME); ?></a>
							</li>
							<?php } ?>
							<li>
								<?php
									if ( is_user_logged_in() ) { ?> 
									<a href="<?php echo get_permalink( get_option('woocommerce_myaccount_page_id') ); ?>">
										<?php _e('My Account', THB_THEME_NAME); ?>
									</a>
									<?php } else { ?>
									<a href="<?php echo get_permalink( get_option('woocommerce_myaccount_page_id') ); ?>"><?php _e('Login', THB_THEME_NAME); ?></a>
								<?php } ?>
							</li>
						</ul>
					</nav>
					<div class="header_line"><?php echo ot_get_option('header_line', 'Please add text from Appearance -> Theme Options'); ?></div>
				</div>
			</aside>
		</div>
	</div>
</header>
<nav id="nav">
	<div class="row">
		<div class="nine columns">
			<?php if(has_nav_menu('nav-menu')) { ?>
			  <?php wp_nav_menu( array( 'theme_location' => 'nav-menu', 'depth' => 3, 'container' => false, 'menu_class' => 'sf-menu', 'walker'          => new UberStoreNavDropdown  ) ); ?>
			<?php } else { ?>
				<ul class="sf-menu">
					<li><a href="<?php echo get_admin_url().'nav-menus.php'; ?>">Please assign a menu from Appearance -> Menus</a></li>
				</ul>
			<?php } ?>
		</div>
		<div class="three columns">
			<?php get_search_form(); ?>
		</div>
	</div>
</nav>
<?php }  ?>
<!-- End Header -->
<?php if (is_page()) {
		$rev_slider_alias = get_post_meta($post->ID, 'rev_slider_alias', true);
		if ($rev_slider_alias) {?>
<div id="home-slider">
	<?php putRevSlider($rev_slider_alias); ?>
</div>
<?php  }
	}
?>
<?php get_template_part('template-breadcrumbs'); ?>
<?php } // Blank page check ?>
<div role="main">