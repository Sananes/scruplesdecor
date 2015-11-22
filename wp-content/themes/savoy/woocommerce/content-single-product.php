<?php
/**
 * The template for displaying product content in the single-product.php template
 *
 * Override this template by copying it to yourtheme/woocommerce/content-single-product.php
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $nm_theme_options, $nm_globals;


/* Function: Product summary - Opening tags */
function nm_product_summary_open() {
	echo '
		<div class="nm-product-summary-inner-col col-lg-12 col-xs-6">
			<div class="nm-product-summary-title">';
}
/* Function: Product summary - Divider tags */
function nm_product_summary_divider() {
	echo '
			</div>
		</div>
		<div class="nm-product-summary-inner-col col-lg-12 col-xs-6">';
}
/* Function: Product summary - Closing tags */
function nm_product_summary_close() {
	echo '</div>';
}


/* Function: Set up-sells product limit */
function nm_woocommerce_output_upsells() { 
	global $nm_theme_options;
	woocommerce_upsell_display( intval( $nm_theme_options['shop_columns'] ), 1 ); // Args: ( $products_per_row, $rows )
}


// Action: woocommerce_before_single_product
remove_action( 'woocommerce_before_single_product', 'wc_print_notices', 10 );

// Action: woocommerce_before_single_product_summary
remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_sale_flash', 10 );

// Action: woocommerce_single_product_summary
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_rating', 10 );
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40 );
add_action( 'woocommerce_single_product_summary', 'nm_product_summary_open', 3 );
add_action( 'woocommerce_single_product_summary', 'nm_product_summary_divider', 15 );
add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_rating', 21 );
add_action( 'woocommerce_single_product_summary', 'nm_product_summary_close', 55 );

// Action: woocommerce_after_single_product_summary
remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 15 );
remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );

// Action: woocommerce_after_single_product_tabs
add_action( 'woocommerce_after_single_product_tabs', 'woocommerce_template_single_meta', 5 );
add_action( 'woocommerce_after_single_product_tabs', 'nm_woocommerce_output_upsells', 10 );
add_action( 'woocommerce_after_single_product_tabs', 'woocommerce_output_related_products', 15 ); // Note: Change products-per-page in: "../framework/woocommerce/woocommerce.php"

$post_class = 'nm-single-product';
$background_style = '';

// Background color
if ( isset( $_GET['nobg'] ) ) {
	$post_class .= ' no-bg';
	$background_style = ' style="background-color:#fff;"';
}
						
// Is the shop displaying on the home-page?
$shop_on_homepage = ( $nm_globals['shop_page_id'] == intval( get_option('page_on_front') ) );

// Product-summary column size
$summary_column_size = ( isset( $nm_theme_options['product_image_column_size'] ) ) ? 10 - intval( $nm_theme_options['product_image_column_size'] ) : '4';

?>

<?php
	/**
	 * woocommerce_before_single_product hook
	 */
	 do_action( 'woocommerce_before_single_product' );

	 if ( post_password_required() ) {
	 	echo get_the_password_form();
	 	return;
	 }
?>

<div itemscope itemtype="<?php echo woocommerce_get_product_schema(); ?>" id="product-<?php the_ID(); ?>" <?php post_class( $post_class ); ?>>
    
    <div class="nm-single-product-bg"<?php echo $background_style; ?>>
        <div class="nm-single-product-top">
            <div class="nm-row">
                <div class="col-xs-9">
                    <?php
						/* Breadcrumb */
						woocommerce_breadcrumb( array(
							'delimiter'   	=> '<span class="delimiter">/</span>',
							'wrap_before'	=> '<nav id="nm-breadcrumb" class="woocommerce-breadcrumb" ' . ( is_single() ? 'itemprop="breadcrumb"' : '' ) . '>',
							'wrap_after'	=> '</nav>',
							'home'			=> ( $shop_on_homepage ) ? _x( 'Shop', 'breadcrumb', 'nm-framework' ) : _x( 'Home', 'breadcrumb', 'woocommerce' )
						) );
					?>
                </div>
                
                <div class="col-xs-3">
                    <div class="nm-single-product-menu">
						<?php
							/* Prev/next product buttons */
							previous_post_link( '%link', '<i class="nm-font nm-font-play flip"></i>' );
							next_post_link( '%link', '<i class="nm-font nm-font-play"></i>' );
						?>
                    </div>
                </div>
            </div>
        </div>
		        
        <?php nm_print_shop_notices(); ?>
        
        <div class="nm-single-product-showcase">
            <div class="nm-row">
                <?php
                    /**
                     * woocommerce_before_single_product_summary hook
                     *
                     * @hooked woocommerce_show_product_images - 20
                     */
                    do_action( 'woocommerce_before_single_product_summary' );
                ?>
				
                <div class="nm-product-summary-col col-lg-<?php echo esc_attr( $summary_column_size ); ?> col-md-10 col-xs-12">
                    <div id="nm-product-summary" class="product-summary">
						<div class="nm-row">
							<?php
                                /**
                                 * woocommerce_single_product_summary hook
                                 *
                                 * @hooked nm_product_summary_open - 3
                                 * @hooked woocommerce_template_single_title - 5
                                 * @hooked woocommerce_template_single_price - 10
                                 * @hooked nm_product_summary_divider - 15
                                 * @hooked woocommerce_template_single_excerpt - 20
                                 * @hooked woocommerce_template_single_rating - 21
                                 * @hooked woocommerce_template_single_add_to_cart - 30
                                 * @hooked woocommerce_template_single_sharing - 50
                                 * @hooked nm_product_summary_close - 55
                                 */
                                do_action( 'woocommerce_single_product_summary' );
                            ?>
                        </div>
					</div>
                </div>
                
                <div class="nm-single-product-right-col col-xs-1">
                	&nbsp;
				</div>
            </div>
        </div>
    </div>
        
	<?php
        /**
         * woocommerce_after_single_product_summary hook
         *
         * @hooked woocommerce_output_product_data_tabs - 10
         */
        do_action( 'woocommerce_after_single_product_summary' );
    ?>
        
	<?php
        /**
         * woocommerce_after_single_product_tabs hook
         *
         * @hooked woocommerce_template_single_meta - 5
		 * @hooked woocommerce_upsell_display - 10
         * @hooked woocommerce_output_related_products - 15
         */
		do_action( 'woocommerce_after_single_product_tabs' );
    ?>

	<meta itemprop="url" content="<?php the_permalink(); ?>" />

</div> <!-- #product-<?php the_ID(); ?> -->

<?php do_action( 'woocommerce_after_single_product' ); ?>
