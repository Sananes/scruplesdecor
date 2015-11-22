<?php function thb_product_categories( $atts, $content = null ) {
    extract(shortcode_atts(array(
       	'carousel' => 'no',
       	'columns' => '4',
       	'cat' => ''
    ), $atts));
	
	global $woocommerce, $woocommerce_loop;
			
	$args = $product_categories = array();
	
	$cats = explode(",", $cat);
	
	foreach ( $cats as $cat ) {
		$c = get_term_by('slug',$cat,'product_cat');
		array_push($product_categories, $c);
	}
	
	
  $woocommerce_loop['columns'] = apply_filters( 'loop_shop_columns', $columns ); 
 	
 	ob_start();
 	
	?>
	   
		<?php if ($carousel == "yes") { ?>
			
			<div class="carousel-container">
				<div class="carousel products owl row" data-columns="<?php echo $columns; ?>" data-navigation="true">	
					<?php
						if ( $product_categories ) {
							foreach ( $product_categories as $category ) {
								woocommerce_get_template( 'content-product_cat.php', array(
									'category' => $category
								) );
							}
						}
						woocommerce_reset_loop();  
					?>			
				</div>
			</div>
			
		<?php } else {  ?> 
			<?php switch($columns) {
				case 2:
					$col = 'two';
					break;
				case 3:
					$col = 'three';
					break;
				case 4:
					$col = 'four';
					break;
			} ?>
		<div class="products row <?php echo $col;?>-columns" data-equal=".product-category">
			<?php
				if ( $product_categories ) {
					foreach ( $product_categories as $category ) {
						woocommerce_get_template( 'content-product_cat.php', array(
							'category' => $category
						) );
					}
				}
				woocommerce_reset_loop();  
			?>
		</div>
		
		<?php } ?>
	   
	<?php 
	     
   $out = ob_get_contents();
   if (ob_get_contents()) ob_end_clean();
   
	   
  return $out;
}
add_shortcode('thb_product_categories', 'thb_product_categories');
