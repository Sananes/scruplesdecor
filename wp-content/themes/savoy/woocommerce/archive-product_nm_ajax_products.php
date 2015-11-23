<?php
/**
 *	NM: The template for displaying AJAX loaded products
 */
 
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( have_posts() ) {
		
	echo '<ul class="nm-products">';

	while ( have_posts() ) { 
		the_post();
		wc_get_template_part( 'content', 'product' );
	}

	echo '</ul>';
			
	?>
	<div class="nm-infload-link"><?php next_posts_link( '&nbsp;' ); ?></div>
	<?php

}
