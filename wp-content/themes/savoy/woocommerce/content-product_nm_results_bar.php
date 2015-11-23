<?php
/**
 *	Template for displaying shop results bar/button
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Is search query set and not empty?
$is_search = ( ! empty( $_REQUEST['s'] ) ) ? true : false;

if ( $is_search || is_product_taxonomy() ) :

	// Is this a product category?
	$is_category = ( $is_search ) ? false : is_product_category();
	
	// Results bar class
	$results_bar_class = ( $is_category ) ? ' is-category' : '';
	
	// Get shop page URL
	$shop_page_url = get_permalink( wc_get_page_id( 'shop' ) );

?>

    <div class="nm-shop-results-bar btn<?php echo esc_attr( $results_bar_class ); ?>">
        <a href="#" data-shop-url="<?php echo esc_url( $shop_page_url ); ?>" id="nm-shop-results-reset">
            <i class="nm-font nm-font-close2"></i>
            <?php
                if ( $is_search ) {
                    printf( esc_html__( 'Search results for %s', 'nm-framework' ), '<span>"' . $_REQUEST['s'] . '"</span>' );
                } else {
                    $current_term = $GLOBALS['wp_query']->get_queried_object();
                    
                    if ( $is_category ) {
                        printf( esc_html__( 'Showing &ldquo;%s&rdquo;', 'nm-framework' ), '<span>' . $current_term->name . '</span>' );
                    } else {
                        printf( esc_html__( 'Products tagged &ldquo;%s&rdquo;', 'woocommerce' ), '<span>' . $current_term->name . '</span>' );
                    }
                }
            ?>
        </a>
    </div>

<?php endif; ?>
