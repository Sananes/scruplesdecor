<?php
/**
 * Show options for ordering
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.2.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $woocommerce, $wp_query;

if ( 1 == $wp_query->found_posts || ! woocommerce_products_will_display() )
	return;

# Shop URL
$current_url = SHOPURL;

if(is_product_category())
	$current_url = get_term_link($wp_query->queried_object, 'product_cat');
elseif(is_product_tag())
	$current_url = get_term_link($wp_query->queried_object, 'product_tag');

if(strstr($current_url, '?'))
{
	if(preg_match("/\?.+/i", $current_url))
		$current_url .= '&amp;';
}
else
{
	$current_url .= '?';
}

# start: modified by Arlind Nushi
$catalog_orderby = apply_filters( 'woocommerce_catalog_orderby', array(
	'menu_order' => __( 'Default sorting', 'woocommerce' ),
	'popularity' => __( 'Sort by popularity', 'woocommerce' ),
	'rating'     => __( 'Sort by average rating', 'woocommerce' ),
	'date'       => __( 'Sort by newness', 'woocommerce' ),
	'price'      => __( 'Sort by price: low to high', 'woocommerce' ),
	'price-desc' => __( 'Sort by price: high to low', 'woocommerce' )
) );

if ( get_option( 'woocommerce_enable_review_rating' ) === 'no' )
	unset( $catalog_orderby['rating'] );


# Selected Ordering parameter
$selected_sorting = __('Default sorting', 'woocommerce');

foreach ( $catalog_orderby as $id => $name )
{
	if($id == get('orderby'))
		$selected_sorting = $name;
}
?>
<div class="btn-group">
	<button type="button" class="btn btn-white up" data-toggle="dropdown">
		<?php echo $selected_sorting; ?> 
		<span class="caret"></span>
	</button>
	
	<ul class="dropdown-menu btn-white sorting-dropdown" role="menu">
	<?php
	foreach ( $catalog_orderby as $id => $name ):
		
		?>
		<li<?php echo $id == get('orderby') ? ' class="active"' : ''; ?>>
			<a href="<?php echo $current_url; ?>orderby=<?php echo esc_attr( $id ); ?>"><?php echo esc_attr( $name ); ?></a>
		</li>
		<?php
		
	endforeach;
	?>
	</ul>
</div>
<?php 
# end: modified by Arlind Nushi 
?>