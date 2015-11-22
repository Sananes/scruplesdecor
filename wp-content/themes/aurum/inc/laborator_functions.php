<?php
/**
 *	Aurum WordPress Theme
 *
 *	Laborator.co
 *	www.laborator.co
 */


# GET/POST getter
function get($var)
{
	return isset($_GET[$var]) ? $_GET[$var] : (isset($_REQUEST[$var]) ? $_REQUEST[$var] : '');
}

function post($var)
{
	return isset($_POST[$var]) ? $_POST[$var] : null;
}

function cookie($var)
{
	return isset($_COOKIE[$var]) ? $_COOKIE[$var] : null;
}


# Generate From-To numbers borders
function generate_from_to($from, $to, $current_page, $max_num_pages, $numbers_to_show = 5)
{
	if($numbers_to_show > $max_num_pages)
		$numbers_to_show = $max_num_pages;


	$add_sub_1 = round($numbers_to_show/2);
	$add_sub_2 = round($numbers_to_show - $add_sub_1);

	$from = $current_page - $add_sub_1;
	$to = $current_page + $add_sub_2;

	$limits_exceeded_l = FALSE;
	$limits_exceeded_r = FALSE;

	if($from < 1)
	{
		$from = 1;
		$limits_exceeded_l = TRUE;
	}

	if($to > $max_num_pages)
	{
		$to = $max_num_pages;
		$limits_exceeded_r = TRUE;
	}


	if($limits_exceeded_l)
	{
		$from = 1;
		$to = $numbers_to_show;
	}
	else
	if($limits_exceeded_r)
	{
		$from = $max_num_pages - $numbers_to_show + 1;
		$to = $max_num_pages;
	}
	else
	{
		$from += 1;
	}

	if($from < 1)
		$from = 1;

	if($to > $max_num_pages)
	{
		$to = $max_num_pages;
	}

	return array($from, $to);
}

# Laborator Pagination
function laborator_show_pagination($current_page, $max_num_pages, $from, $to, $pagination_position = 'full', $numbers_to_show = 5)
{
	$current_page = $current_page ? $current_page : 1;

	?>
	<div class="clear"></div>

	<!-- pagination -->
	<ul class="pagination<?php echo $pagination_position ? " pagination-{$pagination_position}" : ''; ?>">

	<?php if($current_page > 1): ?>
		<li class="first_page"><a href="<?php echo get_pagenum_link(1); ?>"><?php _e('&laquo; First', TD); ?></a></li>
	<?php endif; ?>

	<?php if($current_page > 2): ?>
		<li class="first_page"><a href="<?php echo get_pagenum_link($current_page - 1); ?>"><?php _e('Previous', TD); ?></a></li>
	<?php endif; ?>

	<?php

	if($from > floor($numbers_to_show / 2))
	{
		?>
		<li><a href="<?php echo get_pagenum_link(1); ?>"><?php echo 1; ?></a></li>
		<li class="dots"><span>...</span></li>
		<?php
	}

	for($i=$from; $i<=$to; $i++):

		$link_to_page = get_pagenum_link($i);
		$is_active = $current_page == $i;

	?>
		<li<?php echo $is_active ? ' class="active"' : ''; ?>><a href="<?php echo $link_to_page; ?>"><?php echo $i; ?></a></li>
	<?php
	endfor;


	if($max_num_pages > $to)
	{
		if($max_num_pages != $i):
		?>
			<li class="dots"><span>...</span></li>
		<?php
		endif;

		?>
		<li><a href="<?php echo get_pagenum_link($max_num_pages); ?>"><?php echo $max_num_pages; ?></a></li>
		<?php
	}
	?>

	<?php if($current_page + 1 <= $max_num_pages): ?>
		<li class="last_page"><a href="<?php echo get_pagenum_link($current_page + 1); ?>"><?php _e('Next', TD); ?></a></li>
	<?php endif; ?>

	<?php if($current_page < $max_num_pages): ?>
		<li class="last_page"><a href="<?php echo get_pagenum_link($max_num_pages); ?>"><?php _e('Last &raquo;', TD); ?></a></li>
	<?php endif; ?>
	</ul>
	<!-- end: pagination -->
	<?php

	# Deprecated (the above function displays pagination)
	if(false):

		posts_nav_link();

	endif;
}



# Get SMOF data
$data_cached            = array();
$smof_filters           = array();
$data                   = function_exists('of_get_options') ? of_get_options() : array();
$data_iteration_count   = 0;

function get_data($var = '')
{
	global $data, $data_cached, $data_iteration_count;

	$data_iteration_count++;

	if( ! function_exists('of_get_options'))
		return null;

	if(isset($data_cached[$var]))
	{
		return apply_filters("get_data_{$var}", $data_cached[$var]);
	}

	if( ! empty($var) && isset($data[$var]))
	{
		$data_cached[$var] = $data[$var];

		return apply_filters("get_data_{$var}", $data[$var]);
	}

	return null;
}


# Compress Text Function
function compress_text($buffer)
{
	/* remove comments */
	$buffer = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $buffer);
	/* remove tabs, spaces, newlines, etc. */
	$buffer = str_replace(array("\r\n", "\r", "\n", "\t", '	', '	', '	'), '', $buffer);
	return $buffer;
}


# Load Font Style
function laborator_load_font_style()
{
	global $custom_css;

	$api_url           = 'http://fonts.googleapis.com/css?family=';

	$font_variants 	   = '300italic,400italic,700italic,300,400,700';

	$primary_font      = 'Roboto:' . $font_variants;
	$secondary_font    = 'Roboto+Condensed:' . $font_variants;

	# Custom Font
	$_font_primary      = get_data('font_primary');
	$_font_secondary    = get_data('font_secondary');

	$primary_font_replaced = $secondary_font_replaced = 0;

	if($_font_primary && $_font_primary != 'none' && $_font_primary != 'Use default')
	{
		$primary_font_replaced = 1;
		$primary_font = $_font_primary . ':' . $font_variants . '';
	}

	if($_font_secondary && $_font_secondary != 'none' && $_font_secondary != 'Use default')
	{
		$secondary_font_replaced = 1;
		$secondary_font = $_font_secondary . ':' . $font_variants;
	}

	$custom_primary_font_url   = get_data('custom_primary_font_url');
	$custom_primary_font_name  = get_data('custom_primary_font_name');

	$custom_heading_font_url   = get_data('custom_heading_font_url');
	$custom_heading_font_name  = get_data('custom_heading_font_name');

	if($custom_primary_font_url && $custom_primary_font_name)
	{
		$primary_font_replaced    = 2;
		$primary_font             = $custom_primary_font_url;
		$_font_primary            = $custom_primary_font_name;
	}

	if($custom_heading_font_url && $custom_heading_font_name)
	{
		$secondary_font_replaced    = 2;
		$secondary_font             = $custom_heading_font_url;
		$_font_secondary            = $custom_heading_font_name;
	}

	wp_enqueue_style('primary-font', strstr($primary_font, "://") ? $primary_font : ($api_url . $primary_font));
	wp_enqueue_style('heading-font', strstr($secondary_font, "://") ? $secondary_font : ($api_url . $secondary_font));

	ob_start();

	if($primary_font_replaced):
	?>
	.primary-font,
	body,
	p,
	.view-cart td .btn,
	.shop .cart-bottom-details .shipping_calculator .shipping-calculator-button {
		font-family: <?php echo $primary_font_replaced == 1 ? "'{$_font_primary}', sans-serif" : $_font_primary; ?>;
	}
	<?php
	endif;

	if($secondary_font_replaced):
	?>
	.heading-font,
	header.site-header,
	header.site-header .logo.text-logo a,
	header.mobile-menu .mobile-logo .logo.text-logo a,
	footer.site-footer,
	footer.site-footer .footer-widgets .sidebar.widget_search #searchsubmit.btn-bordered,
	.contact-page .contact-form label,
	.view-cart th,
	.view-cart td,
	.view-cart td.price,
	.login-button,
	.coupon-env .coupon-enter,
	.my-account .my-account-tabs,
	.shop .shop-item .item-info span,
	.shop .quantity.buttons_added input.input-text,
	.shop-item-single .item-details-single.product-type-external .single_add_to_cart_button.button.btn-bordered,
	.shop-item-single .item-info.summary .variations .label,
	.shop-item-single .item-info.summary .variations div.variation-select,
	.shop-item-single .item-info.summary input.add-to-cart,
	.shop-item-single .item-info.summary .price,
	.shop-item-single .item-info.summary form.cart .button,
	.shop-item-single .item-info.summary .product_meta > span,
	.shop-item-single .item-info.summary .product_meta .wcml_currency_switcher,
	.your-order .order-list li,
	section.blog .post .comments .comment + .comment-respond #cancel-comment-reply-link,
	section.blog .post .comments .comment-respond label,
	section.blog .post .comments .comment-respond #submit.btn-bordered,
	section.blog .post-password-form label,
	section.blog .post-password-form input[type="submit"].btn-bordered,
	.sidebar .sidebar-entry,
	.sidebar .sidebar-entry select,
	.sidebar .sidebar-entry.widget_search #searchsubmit.btn-bordered,
	.sidebar .sidebar-entry.widget_product_search #searchsubmit.btn-bordered,
	.sidebar .sidebar-entry.widget_wysija .wysija-submit.btn-bordered,
	.sidebar .sidebar-entry .product_list_widget li > .quantity,
	.sidebar .sidebar-entry .product_list_widget li > .amount,
	.sidebar .sidebar-entry .product_list_widget li .variation,
	.sidebar .sidebar-entry .product_list_widget li .star-rating,
	.sidebar .sidebar-entry.widget_shopping_cart .total,
	.sidebar .sidebar-entry.widget_shopping_cart .buttons .button.btn-bordered,
	.sidebar .sidebar-entry .price_slider_wrapper .price_slider_amount .button.btn-bordered,
	.sidebar .sidebar-list li,
	.bordered-block .lost-password,
	h1,
	h2,
	h3,
	h4,
	h5,
	h6,
	.btn.btn-bordered,
	.dropdown-menu,
	.nav-tabs > li > a,
	.alert,
	.form-control,
	.banner .button_outer .button_inner .banner-content strong,
	.table > thead > tr > th,
	.tooltip-inner,
	.search .search-header,
	.page-container .wpb_content_element.wpb_tabs .ui-tabs .wpb_tabs_nav li a,
	.page-container .wpb_content_element.wpb_tour .wpb_tabs_nav li a,
	.page-container .wpb_content_element.lab_wpb_image_banner .banner-text-content,
	.page-container .wpb_content_element.alert p,
	.page-container .wpb_content_element.lab_wpb_products_carousel .products-loading,
	.page-container .wpb_content_element.lab_wpb_testimonials .testimonials-inner .testimonial-entry .testimonial-blockquote,
	.page-container .feature-tab .title,
	.page-container .vc_progress_bar .vc_single_bar .vc_label,
	.top-menu div.lang-switcher #lang_sel a,
	.top-menu div.currency-switcher .wcml_currency_switcher li,
	.pagination > a,
	.pagination > span,
	.breadcrumb span,
	.shop .page-title small p,
	.shop .commentlist .comment_container .comment-details .meta,
	.shop #review_form_wrapper .comment-form-rating label,
	.shop #review_form_wrapper .form-submit [type="submit"].btn-bordered,
	.shop .shop_attributes th,
	.shop .shop_attributes td,
	.shop dl.variation dt,
	.shop dl.variation dd,
	.shop .cart_totals table tr td,
	.shop .cart_totals table tr th,
	.shop .cross-sells .product-item .product-details .price,
	.shop .order-details-list li,
	.shop .bacs_details li,
	.shop .digital-downloads li .count,
	.shop legend,
	.shop .yith-wcwl-add-to-wishlist .yith-wcwl-add-button .add_to_wishlist.btn-bordered,
	.shop .yith-wcwl-add-to-wishlist .yith-wcwl-wishlistexistsbrowse a.btn-bordered,
	.shop .yith-wcwl-add-to-wishlist .yith-wcwl-wishlistaddedbrowse a.btn-bordered,
	.wishlist_table tbody tr .product-stock-status span,
	.wishlist_table tbody tr .add_to_cart.btn-bordered,
	#yith-wcwl-popup-message,
	.shop-empty-cart-page .cart-empty-title p a,
	.woocommerce-message,
	.woocommerce-error,
	.woocommerce-info,
	.woocommerce-message .button.btn-bordered,
	.woocommerce-error .button.btn-bordered,
	.woocommerce-info .button.btn-bordered,
	.header-menu .lab-mini-cart .total {
		font-family: <?php echo $secondary_font_replaced == 1 ? "'{$_font_secondary}', sans-serif" : $_font_secondary; ?>;
	}
	<?php
	endif;
	$custom_css = ob_get_clean();

	if($custom_css)
	{
		$custom_css = compress_text("<style>{$custom_css}</style>");
		add_action('wp_print_scripts', create_function('', 'global $custom_css; echo $custom_css;'));
	}
}


# Show Header Top Bar (depended by position)
function laborator_display_header_top_bar($widget)
{
	global $current_user;

	if(preg_match("/^menu-([0-9]+)/i", $widget, $matches))
	{
		$menu_id = $matches[1];

		wp_nav_menu(
			array(
				'menu'       	=> $menu_id,
				'container'     => 'nav',
				'menu_class'    => '',
				'items_wrap'    => '%3$s'
			)
		);
	}
	else
	if($widget == 'laborator_cart_totals' && function_exists('WC'))
	{
		?>
		<nav>
			<li>
				<a href="<?php echo WC()->cart->get_cart_url(); ?>"><?php echo sprintf(__('Cart totals: <span id="cart-totals">%s</span>', TD), WC()->cart->get_cart_total()); ?></a>
			</li>
		</nav>
		<?php
	}
	else
	if($widget == 'laborator_social_networks')
 	{
		echo do_shortcode('[lab_social_networks]');
	}
	else
	if($widget == 'laborator_account_links_and_date' && function_exists('WC'))
	{
		$registration_enabled = get_option('woocommerce_enable_myaccount_registration') == 'yes';
		$account_link = get_permalink(wc_get_page_id('myaccount'));

		?>
		<nav>
			<li class="single-entry">
				<?php if($current_user->ID > 0): ?>
					<?php echo sprintf(__('<a href="%1$s">My Account Details</a>', TD), $account_link); ?>
				<?php elseif($registration_enabled): ?>
					<?php echo sprintf(__('<a href="%1$s">Login</a> or <a href="%1$s">Register</a>', TD), $account_link); ?>
				<?php else: ?>
					<?php echo sprintf(__('<a href="%1$s">Customer Login</a>', TD), $account_link); ?>
				<?php endif; ?>

				<span class="sep">|</span> <span><?php echo date_i18n(get_option('date_format')); ?></span>
			</li>
		</nav>
		<?php
	}
	elseif($widget == 'laborator_current_date' && function_exists('WC'))
	{
		?>
		<nav>
			<li class="single-entry">
				<span class="up"><?php echo date_i18n(get_option('date_format')); ?></span>

			</li>
		</nav>
		<?php
	}
	else
	if($widget == 'wpml_lang_currency_switcher')
	{
		wp_enqueue_script('bootstrap-select');

		?>
		<div class="top-ctr">
			<div class="lang-switcher">
				<?php echo do_action('icl_language_selector'); ?>
			</div>
			<div class="currency-switcher">
				<?php echo do_shortcode('[currency_switcher]'); ?>
			</div>
		</div>
		<?php
	}
	else
	if($widget == 'wpml_lang_switcher')
	{
		?>
		<div class="lang-switcher">
			<?php echo do_action('icl_language_selector'); ?>
		</div>
		<?php
	}
	else
	if($widget == 'wpml_currency_switcher')
	{
		wp_enqueue_script('bootstrap-select');

		?>
		<div class="currency-switcher">
			<?php echo do_shortcode('[currency_switcher]'); ?>
		</div>
		<?php
	}
	else
	if($widget == 'navxt_breadcrubms')
	{
		if(function_exists('bcn_display'))
		{
			echo '<div class="breadcrumb">';
		    bcn_display();
			echo '</div>';
		}
	}
}



# Share Network Story
function share_story_network_link($network, $id, $simptips = true)
{
	global $post;


	$networks = array(
		'fb' => array(
			'url'		=> 'http://www.facebook.com/sharer.php?m2w&s=100&p&#91;url&#93;=' . get_permalink() . '&p&#91;title&#93;=' . esc_attr( get_the_title() ),
			'tooltip'	=> __('Facebook', TD),
			'icon'		=> 'facebook'
		),

		'tw' => array(
			'url'		=> 'http://twitter.com/home?status=' . esc_attr( get_the_title() ) . ' ' . get_permalink(),
			'tooltip'	=> __('Twitter', TD),
			'icon'		 => 'twitter'
		),

		'gp' => array(
			'url'		=> 'https://plus.google.com/share?url=' . get_permalink(),
			'tooltip'	=> __('Google+', TD),
			'icon'		 => 'gplus'
		),

		'tlr' => array(
			'url'		=> 'http://www.tumblr.com/share/link?url=' . get_permalink() . '&name=' . esc_attr( get_the_title() ) . '&description=' . esc_attr( get_the_excerpt() ),
			'tooltip'	=> __('Tumblr', TD),
			'icon'		 => 'tumblr'
		),

		'lin' => array(
			'url'		=> 'http://linkedin.com/shareArticle?mini=true&amp;url=' . get_permalink() . '&amp;title=' . esc_attr( get_the_title() ),
			'tooltip'	=> __('LinkedIn', TD),
			'icon'		 => 'linkedin'
		),

		'pi' => array(
			'url'		=> 'http://pinterest.com/pin/create/button/?url=' . get_permalink() . '&amp;description=' . esc_attr( get_the_title() ) . '&amp;' . ($id ? ('media=' . wp_get_attachment_url( get_post_thumbnail_id($id) )) : ''),
			'tooltip'	=> __('Pinterest', TD),
			'icon'	 	 => 'pinterest'
		),

		'vk' => array(
			'url'		=> 'http://vkontakte.ru/share.php?url=' . get_permalink(),
			'tooltip'	=> __('VKontakte', TD),
			'icon'	 	 => 'vkontakte'
		),

		'em' => array(
			'url'		=> 'mailto:?subject=' . esc_attr( get_the_title() ) . '&amp;body=' . get_permalink(),
			'tooltip'	=> __('Email', TD),
			'icon'		 => 'email'
		),
	);

	$network_entry = $networks[ $network ];

	?>
	<a class="<?php echo $network_entry['icon']; ?>" href="<?php echo $network_entry['url']; ?>" target="_blank">
		<?php echo $network_entry['tooltip']; ?>
	</a>
	<?php
}


# Page Path
function laborator_page_path($post)
{
	$page_path = array(__('Home', TD));

	$page_hierarchy = array($post->post_title);

	if($post->post_parent)
	{
		laborator_page_path_recursive($post, $page_hierarchy);
	}

	$page_hierarchy = array_reverse($page_hierarchy);
	$page_path = array_merge($page_path, $page_hierarchy);

	return implode(' &raquo ', $page_path);
}

function laborator_page_path_recursive($post, & $hierarchy)
{
	$parent = get_post($post->post_parent);

	array_push($hierarchy, $parent->post_title);

	if($parent->post_parent)
		laborator_page_path_recursive($parent, $hierarchy);
}


# In case when GET_FIELD function doesn't exists
if( ! in_array('advanced-custom-fields/acf.php', apply_filters('active_plugins', get_option('active_plugins'))) && ! is_admin())
{
	function get_field($field_id, $post_id = null)
	{
		global $post;

		if(is_numeric($post_id))
			$post = get_post($post_id);

		return $post->{$field_id};
	}
}


# Has transparent header
function has_transparent_header()
{
	return get_field('enable_transparent_header');
}



# Get SVG
function lab_get_svg($svg_path, $id = null, $size = array(24, 24), $is_asset = true)
{
	if($is_asset)
		$svg_path = get_template_directory() . '/assets/' .  $svg_path;

	if( ! $id)
		$id = sanitize_title(basename($svg_path));

	if(is_numeric($size))
		$size = array($size, $size);

	ob_start();

	echo file_get_contents($svg_path);

	$svg = ob_get_clean();

	$svg = preg_replace(
		array(
			'/^.*<svg/s',
			'/id=".*?"/i',
			'/width=".*?"/',
			'/height=".*?"/'
		),
		array(
			'<svg', 'id="'.$id.'"',
			'width="'.$size[0].'px"',
			'height="'.$size[0].'px"'
		),
		$svg
	);

	return $svg;
}



# Check if page is fullwidth
add_action('wp', 'lab_check_fullwidth_page');

function lab_check_fullwidth_page()
{
	global $post;

	if($post && $post->post_type == 'page')
	{
		$is_fullwidth = false;

		if(in_array($post->page_template, array('full-width-page.php')))
			$is_fullwidth = true;
		elseif(get_field('fullwidth_page'))
			$is_fullwidth = true;

		if($is_fullwidth)
		{
			define("IS_FULLWIDTH_PAGE", true);
		}
	}
}

function is_fullwidth_page()
{
	return defined('IS_FULLWIDTH_PAGE');
}