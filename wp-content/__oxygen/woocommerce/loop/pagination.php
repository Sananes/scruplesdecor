<?php
/**
 * Pagination - Show numbered pagination for catalog pages.
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.2.2
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $wp_query;

if ( $wp_query->max_num_pages <= 1 || defined("PAGINATION_SHOWN"))
	return;

define("PAGINATION_SHOWN", true);
?>
<div class="clear"></div>

<div class="row spread-2<?php echo SHOPSIDEBAR ? '' : ' shop-no-sidebar'; ?>">
	<div class="col-md-12">
		<nav class="woocommerce-pagination loop-pagination">
			
			<?php
				echo paginate_links( apply_filters( 'woocommerce_pagination_args', array(
					'base'         => esc_url_raw( str_replace( 999999999, '%#%', esc_url( remove_query_arg( 'add-to-cart', get_pagenum_link( 999999999, false ) ) ) ) ),
					'format'       => '',
					'add_args'     => '',
					'current'      => max( 1, get_query_var( 'paged' ) ),
					'total'        => $wp_query->max_num_pages,
					'prev_text'    => __('Previous', 'oxygen'),
					'next_text'    => __('Next', 'oxygen'),
					'type'         => 'list',
					'end_size'     => 3,
					'mid_size'     => 3
				) ) );
			?>

		</nav>
	</div>
</div>