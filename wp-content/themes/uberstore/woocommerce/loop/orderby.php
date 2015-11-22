<?php
/**
 * Show options for ordering
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $woocommerce, $wp_query;

if ( ! woocommerce_products_will_display() )
	return;
?>
	<div class="four columns">
		<form class="woocommerce-ordering custom" method="get">
			<div class="select-wrapper">
				<select name="orderby" class="orderby">
					<?php
						$catalog_orderby = apply_filters( 'woocommerce_catalog_orderby', array(
							'menu_order' => __( 'Default sorting', THB_THEME_NAME ),
							'popularity' => __( 'Sort by popularity', THB_THEME_NAME ),
							'rating'     => __( 'Sort by average rating', THB_THEME_NAME ),
							'date'       => __( 'Sort by newness', THB_THEME_NAME ),
							'price'      => __( 'Sort by price: low to high', THB_THEME_NAME ),
							'price-desc' => __( 'Sort by price: high to low', THB_THEME_NAME )
						) );
			
						if ( get_option( 'woocommerce_enable_review_rating' ) == 'no' )
							unset( $catalog_orderby['rating'] );
			
						foreach ( $catalog_orderby as $id => $name )
							echo '<option value="' . esc_attr( $id ) . '" ' . selected( $orderby, $id, false ) . '>' . esc_attr( $name ) . '</option>';
					?>
				</select>
			</div>
			<?php
				// Keep query string vars intact
				foreach ( $_GET as $key => $val ) {
					if ( 'orderby' == $key )
						continue;
					
					if (is_array($val)) {
						foreach($val as $innerVal) {
							echo '<input type="hidden" name="' . esc_attr( $key ) . '[]" value="' . esc_attr( $innerVal ) . '" />';
						}
					
					} else {
						echo '<input type="hidden" name="' . esc_attr( $key ) . '" value="' . esc_attr( $val ) . '" />';
					}
				}
			?>
		</form>
	</div>
</div>
</div>