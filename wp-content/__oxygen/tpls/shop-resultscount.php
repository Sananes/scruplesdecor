<?php
/**
 *	Oxygen WordPress Theme
 *	
 *	Laborator.co
 *	www.laborator.co 
 */

if( ! get_data('shop_sorting_show'))
	return false;

?>
<div class="row">

	<div class="result-filter">
		<div class="col-lg-12">
			
			<?php woocommerce_catalog_ordering(); ?>
			
			<?php woocommerce_result_count(); ?>
		</div>
	</div>
	
</div>