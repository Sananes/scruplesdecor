<?php 
	$parse_uri = explode( 'wp-content', $_SERVER['SCRIPT_FILENAME'] );
	require_once( $parse_uri[0] . 'wp-load.php' );
	
	Header("Content-type: text/css");
	error_reporting(0);
	$thb_fontlist = array();
	function thb_google_webfont($font, $default = false) {
			global $thb_fontlist;
	
			$fontbase = 'http://fonts.googleapis.com/css?family=';
			$import='';	
			
			if ($font) {
				$otfont = ot_get_option($font);
				if ($otfont['font-family']) {
					$otfontfamily = $otfont['font-family'];
				} else {
					$otfontfamily = $default;
				}
				if (!in_array($otfontfamily, $thb_fontlist)) {
					array_push($thb_fontlist, $otfontfamily);
					if ($otfontfamily) {
						$cssfont = str_replace(' ', '+', $otfontfamily);
						$import = '@import "'.$fontbase.$cssfont .':200,300,400,400,600,700";';
		
						return $import;
					}
				}
			}
		}
	function thb_typeecho($array, $important = false, $default = false) {
		
		if ($array['font-family']) { 
			echo "font-family: " . $array['font-family'] . ";\n";
		} else if ($default) {
			echo "font-family: " . $default . ";\n";
		}
		if ($array['font-color']) { 
			echo "color: " . $array['font-color'] . ";\n";
		}
		if ($array['font-style']) { 
			echo "font-style: " . $array['font-style'] . ";\n";
		}
		if ($array['font-variant']) { 
			echo "font-variant: " . $array['font-variant'] . ";\n";
		}
		if ($array['font-weight']) { 
			echo "font-weight: " . $array['font-weight'] . ";\n";
		}
		if ($array['font-size']) { 
			
			if ($important) {
				echo "font-size: " . $array['font-size'] . " !important;\n";
			} else {
				echo "font-size: " . $array['font-size'] . ";\n";
			}
		}
		if ($array['text-decoration']) { 
				echo "text-decoration: " . $array['text-decoration'] . " !important;\n";
		}
		if ($array['text-transform']) { 
				echo "text-transform: " . $array['text-transform'] . " !important;\n";
		}
		if ($array['line-height']) { 
				echo "line-height: " . $array['line-height'] . " !important;\n";
		}
		if ($array['letter-spacing']) { 
				echo "letter-spacing: " . $array['letter-spacing'] . " !important;\n";
		}
	}
	function thb_bgecho($array) {
		if ($array['background-color']) { 
			echo "background-color: " . $array['background-color'] . " !important;\n";
		}
		if ($array['background-image']) { 
			echo "background-image: url(" . $array['background-image'] . ") !important;\n";
		}
		if ($array['background-repeat']) { 
			echo "background-repeat: " . $array['background-repeat'] . " !important;\n";
		}
		if ($array['background-attachment']) { 
			echo "background-attachment: " . $array['background-attachment'] . " !important;\n";
		}
		if ($array['background-position']) { 
			echo "background-position: " . $array['background-position'] . " !important;\n";
		}
	}
	function thb_measurementecho($array) {
			echo $array[0] . $array[1];
	}
	echo thb_google_webfont('logo_type') . "\n";
	echo thb_google_webfont('post_title_type') . "\n";
	echo thb_google_webfont('body_type') . "\n";
	echo thb_google_webfont('menu_type') . "\n";
	echo thb_google_webfont('submenu_type') . "\n";
	echo thb_google_webfont('widget_title_type') . "\n";
	echo thb_google_webfont('footer_widget_title_type') . "\n";
	echo thb_google_webfont('footer_type') . "\n";
	echo thb_google_webfont('heading_h1_type') . "\n";
	echo thb_google_webfont('heading_h2_type') . "\n";
	echo thb_google_webfont('heading_h3_type') . "\n";
	echo thb_google_webfont('heading_h4_type') . "\n";
	echo thb_google_webfont('heading_h5_type') . "\n";
	echo thb_google_webfont('heading_h6_type') . "\n";
	
	function thb_hex2rgb($hex) {
	
	   $hex = str_replace("#", "", $hex);
	
		if(strlen($hex) == 3) {
	
	      $r = hexdec(substr($hex,0,1).substr($hex,0,1));
	      $g = hexdec(substr($hex,1,1).substr($hex,1,1));
	      $b = hexdec(substr($hex,2,1).substr($hex,2,1));
	
	   } else {
	
	      $r = hexdec(substr($hex,0,2));
	      $g = hexdec(substr($hex,2,2));
	      $b = hexdec(substr($hex,4,2));
	
	   }
	
	   $rgb = array($r, $g, $b);
	
	   return implode(",", $rgb); // returns the rgb values separated by commas
	
	
	}
?>
/* Options set in the admin page */

body { 
	<?php thb_typeecho(ot_get_option('body_type')); ?>
	color: <?php echo ot_get_option('text_color'); ?>;
}
#header {
	<?php thb_bgecho(ot_get_option('header_bg')); ?>
}
#footer {
	<?php thb_bgecho(ot_get_option('footer_bg')); ?>
}
#subfooter {
	<?php thb_bgecho(ot_get_option('subfooter_bg')); ?>
}
<?php if(ot_get_option('title_type')) { ?>
h1,h2,h3,h4,h5,h6 {
	<?php thb_typeecho(ot_get_option('title_type')); ?>	
}
<?php } ?>
/* Accent Color */
<?php if (ot_get_option('accent_color')) { ?>
#nav .sf-menu > li > a:hover,
#nav .sf-menu > li.menu-item-has-children:hover > a, 
#nav .sf-menu > li.menu-item-has-children > a.active,
.style3 #nav .sf-menu > li > a:hover, 
.style3 #nav .sf-menu > li > a.active,
ul.accordion > li > div.title,
dl.tabs dd.active a,
dl.tabs li.active a,
ul.tabs dd.active a,
ul.tabs li.active a,
dl.tabs dd.active a:hover,
dl.tabs li.active a:hover,
ul.tabs dd.active a:hover,
ul.tabs li.active a:hover,
.toggle .title.toggled,
.iconbox.top.type2 span,
.iconbox.left.type3 span,
.iconbox.right.type3 span,
.counter span,
.testimonials small {
  color: <?php echo ot_get_option('accent_color'); ?>;
}
#nav .dropdown,
#my-account-nav li.active a, 
#my-account-nav li.current-menu-item a,
.widget ul.menu li.active a,
.widget ul.menu li.current-menu-item a,
.pull-nine .widget ul.menu li.current-menu-item a,
.wpb_tour dl.tabs dd.active,
.wpb_tour dl.tabs li.active, 
.wpb_tour ul.tabs dd.active,
.wpb_tour ul.tabs li.active,
.iconbox.top.type2 span {
  border-color: <?php echo ot_get_option('accent_color'); ?>;
}
#nav .dropdown:after {
  border-color: transparent transparent <?php echo ot_get_option('accent_color'); ?> transparent;
}
#quick_cart .float_count,
.filters li a.active,
.badge.onsale,
.price_slider .ui-slider-range,
.btn:hover,
.button:hover,
input[type=submit]:hover,
.comment-reply-link:hover,
.btn.black:hover,
.button.black:hover,
input[type=submit].black:hover,
.comment-reply-link.black:hover,
.iconbox span,
.progress_bar .bar span,
.dropcap.boxed {
	background: <?php echo ot_get_option('accent_color'); ?>;	
}
<?php } ?>
/* Extra CSS */
<?php echo ot_get_option('extra_css'); ?>