<?php
/**
 * Bundled Item Short Description.
 * @version 4.2.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( $description === '' ){
	return;
}

?><div class="bundled_product_excerpt product_excerpt"><?php
		echo $description;
?></div>
