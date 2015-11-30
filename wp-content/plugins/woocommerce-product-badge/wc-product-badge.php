<?php
/*
Plugin Name: WooCommerce Product Badge
Plugin URI: http://terrytsang.com/shop/shop/woocommerce-product-badge/
Description: Displays 'new', 'sale' and 'featured' badge on WooCommerce products.
Version: 1.0.4
Author: Terry Tsang
Author URI: http://shop.terrytsang.com
*/

/*  Copyright 2012-2014 Terry Tsang (email: terrytsang811@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

if ( ! defined( 'ABSPATH' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit;
}

// Define plugin name
define('wc_plugin_name_product_badge', 'WooCommerce Product Badge');

// Define plugin version
define('wc_version_product_badge', '1.0.4');


// Checks if the WooCommerce plugins is installed and active.
if(in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))){
	if(!class_exists('WooCommerce_Product_Badge')){
		class WooCommerce_Product_Badge{

			var $textdomain;
			var $options_product_badge;
			var $saved_options_product_badge;

			/**
			 * Gets things started by adding an action to initialize this plugin once
			 * WooCommerce is known to be active and initialized
			 */
			public function __construct(){
				load_plugin_textdomain('wc-product-badge', false, dirname(plugin_basename(__FILE__)) . '/languages/');
				
				$this->textdomain = 'wc-product-badge';
				
				$this->options_product_badge = array(
					'product_badge_enabled' => '',
					'product_badge_display_position' => '',
					'product_badge_enabled_new' => '',
					'product_badge_new_days' => '30',
					'product_badge_enabled_sale' => '',
					'product_badge_enabled_featured' => ''
				);

				$this->display_positions = array( 'default' => __( 'Default', $this->textdomain ), 'after_title' => __( 'After Product Title', $this->textdomain ), 'after_price' => __( 'After Product Price', $this->textdomain ), 'after_short_desc' => __( 'After Short Desc', $this->textdomain ), 'after_meta' => __( 'After SKU,Categories & Tags', $this->textdomain ));

				$this->saved_options_product_badge = array();
				
				add_action('woocommerce_init', array(&$this, 'init'));

				// load admin css
				add_action( 'admin_enqueue_scripts', array(&$this, 'wpb_load_admin_css' ) );

				// load admin css
				add_action( 'wp_enqueue_scripts', array(&$this, 'wpb_load_frontend_css') );
			}

			/**
			 * Initialize extension when WooCommerce is active
			 */
			public function init(){
				global $product;
				
				//add menu link for the plugin (backend)
				add_action( 'admin_menu', array( &$this, 'add_menu_product_badge' ) );
				
				if(get_option('product_badge_enabled'))
				{
					//add_action( 'woocommerce_before_shop_loop_item', array( &$this, 'woocommerce_show_product_loop_badge_new' ), 30 ); 	// The new badge function
					//add_action( 'woocommerce_before_shop_loop_item_title', array( &$this, 'woocommerce_show_product_loop_badge_new' ), 30 ); 	// The new badge function
					//add_action( 'woocommerce_after_shop_loop_item', array( &$this, 'woocommerce_show_product_loop_badge_new' ), 30 ); 	// The new badge function

					//add sharing media at product summary page 
					$default_position = get_option('product_badge_display_position');
					
					$position_index = 101;
					switch($default_position)
					{
						case 'default':
							$position_index = 101;
							break;
						case 'after_title':
							$position_index = 8;
							break;
						case 'after_price':
							$position_index = 15;
							break; 
						case 'after_short_desc':
							$position_index = 30;
							break;
						case 'after_meta':
							$position_index = 45;
							break;
					}

					if( get_option('product_badge_enabled_new') ){
						add_action( 'woocommerce_before_shop_loop_item', array( &$this, 'woocommerce_show_product_loop_badge_new' ), 30 ); 	// The new badge function
						add_action( 'woocommerce_single_product_summary', array(&$this, 'woocommerce_show_product_loop_badge_new' ), $position_index );
					}

					if( get_option('product_badge_enabled_featured') ){
						add_action( 'woocommerce_before_shop_loop_item', array( &$this, 'woocommerce_show_product_loop_badge_featured' ), 35 ); 	// The featured badge function
						add_action( 'woocommerce_single_product_summary', array(&$this, 'woocommerce_show_product_loop_badge_featured' ), $position_index );
					}

					if( get_option('product_badge_enabled_sale') ){
						add_action( 'woocommerce_before_shop_loop_item', array( &$this, 'woocommerce_show_product_loop_badge_sale' ), 20 ); 
						add_action( 'woocommerce_single_product_summary', array(&$this, 'woocommerce_show_product_loop_badge_sale' ), $position_index );	// The sale badge function
					}

					
				}
			}

			/**
			* Load admin stylesheets
			*/
			function wpb_load_admin_css() {
				wp_register_style( 'wc-product-badge-admin-stylesheet', plugins_url('/assets/css/admin-styles.css', __FILE__) );
				wp_enqueue_style( 'wc-product-badge-admin-stylesheet' );
			}

			/**
			* Load frontend stylesheets
			*/
			function wpb_load_frontend_css() {
				wp_register_style( 'wc-product-badge-stylesheet', plugins_url('/assets/css/style.css', __FILE__) );
				wp_enqueue_style( 'wc-product-badge-stylesheet' );
			}
			
			// Display the New badge
			function woocommerce_show_product_loop_badge_new() {
				$dateposted			= get_the_time( 'Y-m-d' );			// Post date
				$timestampposted 	= strtotime( $dateposted );
				$new_days 			= get_option( 'product_badge_new_days' ); 
					//echo 'new days'.$new_days;
				if( (time() - ( 60 * 60 * 24 * $new_days ) ) < $timestampposted ){
					echo '<h3 class="product-badge product-badge-new">'.__( 'New', $this->textdomain ).'</h3>';
					//echo '<div class="ribbon-wrapper-green"><div class="ribbon-green">NEW</div></div>';
				} 
			}

			// Display the Featured badge
			function woocommerce_show_product_loop_badge_featured() {
				global $product;
				if($product->is_featured())
					echo '<h3 class="product-badge product-badge-featured">'.__( 'Featured', $this->textdomain ).'</h3>';
			}

			// Display the Sale badge
			function woocommerce_show_product_loop_badge_sale() {
				global $product;
				if($product->sale_price != ''){
					$sale_price = $product->sale_price;
					$regular_price = $product->regular_price;

					$save_percentage = round((($regular_price - $sale_price) / $regular_price) * 100);
					if($regular_price != '' && $save_percentage != '')
						echo '<h3 class="product-badge product-badge-sale">'.__( 'Sale', $this->textdomain ).' ('.__( 'Save', $this->textdomain ).' '.$save_percentage.'%)</h3>';
				}
			}
			
			/**
			 * Add a menu link to the woocommerce section menu
			 */
			function add_menu_product_badge() {
				$wc_page = 'woocommerce';
				$comparable_settings_page = add_submenu_page( $wc_page , __( 'Product Badge', $this->textdomain ), __( 'Product Badge', $this->textdomain ), 'manage_options', 'wc-product-badge', array(
						&$this,
						'settings_page_product_badge'
				));
			}
			
			/**
			 * Create the settings page content
			 */
			public function settings_page_product_badge() {
			
				// If form was submitted
				if ( isset( $_POST['submitted'] ) )
				{
					check_admin_referer( $this->textdomain );
	
					$this->saved_options_product_badge['product_badge_enabled'] = ! isset( $_POST['product_badge_enabled'] ) ? '1' : $_POST['product_badge_enabled'];
					$this->saved_options_product_badge['product_badge_display_position'] = ! isset( $_POST['product_badge_display_position'] ) ? '' : $_POST['product_badge_display_position'];
					$this->saved_options_product_badge['product_badge_enabled_new'] = ! isset( $_POST['product_badge_enabled_new'] ) ? '1' : $_POST['product_badge_enabled_new'];
					$this->saved_options_product_badge['product_badge_new_days'] = ! isset( $_POST['product_badge_new_days'] ) ? '30' : $_POST['product_badge_new_days'];
					$this->saved_options_product_badge['product_badge_enabled_sale'] = ! isset( $_POST['product_badge_enabled_sale'] ) ? '1' : $_POST['product_badge_enabled_sale'];
					$this->saved_options_product_badge['product_badge_enabled_featured'] = ! isset( $_POST['product_badge_enabled_featured'] ) ? '1' : $_POST['product_badge_enabled_featured'];
						
					foreach($this->options_product_badge as $field => $value)
					{
						$option_product_badge = get_option( $field );
			
						if($option_product_badge != $this->saved_options_product_badge[$field])
							update_option( $field, $this->saved_options_product_badge[$field] );
					}
						
					// Show message
					echo '<div id="message" class="updated fade"><p>' . __( 'You have saved WooCommerce Product Badge options.', $this->textdomain ) . '</p></div>';
				}
			
				$product_badge_enabled			= get_option( 'product_badge_enabled' );
				$product_badge_display_position	= get_option( 'product_badge_display_position' );
				$product_badge_enabled_new		= get_option( 'product_badge_enabled_new' );
				$product_badge_new_days	= get_option( 'product_badge_new_days' ) ? get_option( 'product_badge_new_days' ) : '30';
				$product_badge_enabled_sale	= get_option( 'product_badge_enabled_sale' );
				$product_badge_enabled_featured	= get_option( 'product_badge_enabled_featured' );
				
				$checked_enabled = '';
				$checked_enabled_new  = '';
				$checked_enabled_sale = '';
				$checked_enabled_featured = '';
			
				if($product_badge_enabled)
					$checked_enabled = 'checked="checked"';
				
				if($product_badge_enabled_new)
					$checked_enabled_new = 'checked="checked"';

				if($product_badge_enabled_sale)
					$checked_enabled_sale = 'checked="checked"';

				if($product_badge_enabled_featured)
					$checked_enabled_featured = 'checked="checked"';
			
				$actionurl = $_SERVER['REQUEST_URI'];
				$nonce = wp_create_nonce( $this->textdomain );

				$this->options = $this->get_options();
			
			
				// Configuration Page
			
				?>
				<div class="wrap">
					<div class="plugin-container">
						<div class="plugin-column plugin-primary">

							<h2>WooCommerce Product Badge</h2>
							<h4></h4>


						<form id="plugin_settings" method="post" action="<?php echo $actionurl; ?>">
							<?php settings_fields( 'plugin_product_badge' ); ?>
					
							<h3><?php _e('Settings'); ?></h3>

							<table class="form-table">

								<tr valign="top">
									<th scope="row" width="35%"><?php _e( 'Enable', $this->textdomain ); ?></td>
									<td>
										<input class="checkbox" name="product_badge_enabled" id="product_badge_enabled" value="0" type="hidden">
										<input class="checkbox" name="product_badge_enabled" id="product_badge_enabled" value="1" type="checkbox" <?php echo $checked_enabled; ?>>
									</td>
								</tr>
								<tr valign="top">
									<th scope="row" width="35%"><?php _e( 'Display Position (Product Page)', $this->textdomain ); ?></td>
									<td>
										<select name="product_badge_display_position">
										<?php foreach($this->display_positions as $option => $option_name): ?>
											<?php if($option == $product_badge_display_position): ?>
												<option selected="selected" value="<?php echo $option; ?>"><?php echo $option_name; ?></option>
											<?php else: ?>
												<option value="<?php echo $option; ?>"><?php echo $option_name; ?></option>
											<?php endif; ?>
										<?php endforeach; ?>
										</select>
									</td>
								</tr>
								<tr valign="top">
									<th scope="row" width="35%"><?php _e( 'Show "New" Badge', $this->textdomain ); ?></td>
									<td>
										<input class="checkbox" name="product_badge_enabled_new" id="product_badge_enabled_new" value="0" type="hidden">
										<input class="checkbox" name="product_badge_enabled_new" id="product_badge_enabled_new" value="1" type="checkbox" <?php echo $checked_enabled_new; ?>>
									</td>
								</tr>
								<tr valign="top">
									<th scope="row" width="35%"><?php _e( 'New Product with How Many Days?', $this->textdomain ); ?></td>
									<td>
										<input name="product_badge_new_days" type="text" id="product_badge_new_days" value="<?php echo $product_badge_new_days; ?>" />
									</td>
								</tr>
								<tr valign="top">
									<th scope="row" width="35%"><?php _e( 'Show "Sale" Badge', $this->textdomain ); ?></td>
									<td>
										<input class="checkbox" name="product_badge_enabled_sale" id="product_badge_enabled_sale" value="0" type="hidden">
										<input class="checkbox" name="product_badge_enabled_sale" id="product_badge_enabled_sale" value="1" type="checkbox" <?php echo $checked_enabled_sale; ?>>
									</td>
								</tr>
								<tr valign="top">
									<th scope="row" width="35%"><?php _e( 'Show "Featured" Badge', $this->textdomain ); ?></td>
									<td>
										<input class="checkbox" name="product_badge_enabled_featured" id="product_badge_enabled_featured" value="0" type="hidden">
										<input class="checkbox" name="product_badge_enabled_featured" id="product_badge_enabled_featured" value="1" type="checkbox" <?php echo $checked_enabled_featured; ?>>
									</td>
								</tr>

								

							</table>

							<input class="button-primary" type="submit" name="Save" value="<?php _e('Save Options', $this->textdomain); ?>" id="submitbutton" />
							<input type="hidden" name="submitted" value="1" /> 
							<input type="hidden" id="_wpnonce" name="_wpnonce" value="<?php echo $nonce; ?>" />

						</form>

					</div>

					<!-- Start plugin Sidebar -->
					<div class="plugin-column plugin-sidebar">

						<div class="plugin-box">
							<h3 class="plugin-title"><?php _e( 'Donate $10, $20 or $30', $this->textdomain ); ?></h3>
							<p><?php _e( 'If you like this plugin and find it is helpful, consider supporting it by donating a token of your appreciation.', $this->textdomain ); ?></p>

							<div class="plugin-donate" style="text-align:center">
								<form class="donate" action="https://www.paypal.com/cgi-bin/webscr" method="post">
									<input type="hidden" name="cmd" value="_donations">
									<input type="hidden" name="business" value="terry@terrytsang.com">
									<input type="hidden" name="lc" value="US">
									<input type="hidden" name="item_name" value="WordPress Plugin Development by Terry Tsang">
									<input type="hidden" name="item_number" value="woocommerce-product-badge">
									<input type="hidden" name="currency_code" value="USD">
									<input type="hidden" name="bn" value="PP-DonationsBF:btn_donateCC_LG.gif:NonHosted">
									<button name="submit" class="button-primary"><?php _e( 'Donate with PayPal', $this->textdomain ); ?></button>
									<img alt="" border="0" src="https://www.paypalobjects.com/nl_NL/i/scr/pixel.gif" width="1" height="1">
								</form>
							</div>

							<br /><div style="text-align:center;"><b>OR</b></div>

							<p align="center"><a href="http://terrytsang.com/shop/shop/woocommerce-product-badge-pro/" target="_blank" title="WooCommerce Product Badge PRO"><img src="<?php echo plugins_url( 'assets/images/pro-version.png', __FILE__ ); ?>" border="0" /></a></p>
						
						
							<p><?php _e( 'Some other ways to support this plugin', $this->textdomain ); ?></p>
							<ul class="ul-square">
								<li><a href="http://wordpress.org/support/view/plugin-reviews/woocommerce-product-badge?rate=5#postform" target="_blank"><?php printf( __( 'Leave a %s review on WordPress.org', $this->textdomain ), '&#9733;&#9733;&#9733;&#9733;&#9733;' ); ?></a></li>
								<li><a href="http://shop.terrytsang.com" target="_blank"><?php _e( 'Link to the plugin page from your blog', $this->textdomain ); ?></a></li>
								<li><a href="http://twitter.com/intent/tweet/?text=<?php echo urlencode('I am using WooCommerce Product Badge free plugin to show "New", "Sale" or "Featured" badge on my product page. It\'s great!'); ?>&via=terrytsang811&url=<?php echo urlencode('http://wordpress.org/plugins/woocommerce-product-badge/'); ?>" target="_blank">Tweet about WooCommerce Product Badge </a></li>
								<li><a href="http://wordpress.org/plugins/woocommerce-product-badge/#compatibility"><?php _e( 'Vote "works" on the WordPress.org plugin page', $this->textdomain ); ?></a></li>
							</ul>
						</div>

						<div class="plugin-box">
							<h3 class="plugin-title"><?php _e( 'Looking for support?', $this->textdomain ); ?></h3>
							<p><?php printf( __( 'Please use the %splugin support forums%s on WordPress.org.', $this->textdomain ), '<a href="http://wordpress.org/support/plugin/woocommerce-product-badge">', '</a>' ); ?></p>
						</div>

						<div class="plugin-box">

							<div class="plugin-box">
								<h3 class="plugin-title">About the developer</h3>
								<p>My name is <a href="http://terrytsang.com/">Terry Tsang</a>. I love developing WordPress plugins which help your business to grow.</p>
								<p>Take a look at my other <a href="http://shop.terrytsang.com/">plugins for WordPress & WooCommerce</a> or <em>like</em> my Facebook page to stay updated.</p>
								<p><iframe src="//www.facebook.com/plugins/like.php?href=http%3A%2F%2Fwww.facebook.com%2FecommercePlugins&amp;width&amp;layout=standard&amp;action=like&amp;show_faces=true&amp;share=false&amp;appId=225994527565061" scrolling="no" frameborder="0" style="border:none; width: 100%; overflow:hidden; height: 80px;" allowTransparency="true"></iframe></p>
								<p>You can also follow me on twitter <a href="http://twitter.com/terrytsang811">here</a>.</p>
							</div>
						</div>
						<!-- End plugin Sidebar -->

						<br style="clear:both; " />
					</div>
				</div>
				
			<?php
			}
			
			/**
			 * Get the setting options
			 */
			function get_options() {
				
				foreach($this->options_product_badge as $field => $value)
				{
					$array_options[$field] = get_option( $field );
				}
					
				return $array_options;
			}

			
		}//end class
			
	}//if class does not exist
	
	$woocommerce_product_badge = new WooCommerce_Product_Badge();
}
else{
	add_action('admin_notices', 'wc_product_badge_error_notice');
	function wc_product_badge_error_notice(){
		global $current_screen;
		if($current_screen->parent_base == 'plugins'){
			echo '<div class="error"><p>'.__(wc_plugin_name_product_badge.' requires <a href="http://www.woothemes.com/woocommerce/" target="_blank">WooCommerce</a> to be activated in order to work. Please install and activate <a href="'.admin_url('plugin-install.php?tab=search&type=term&s=WooCommerce').'" target="_blank">WooCommerce</a> first.').'</p></div>';
		}
	}
}

?>