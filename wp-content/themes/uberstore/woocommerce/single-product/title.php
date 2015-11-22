<?php
/**
 * Single Product title
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
global $post, $product;
?>
<header class="post-title">
<?php
	$size = sizeof( get_the_terms( $post->ID, 'product_cat' ) );
	echo $product->get_categories( ', ', '<aside class="post_categories">BACK TO' . _n( '', '', $size, THB_THEME_NAME ) . ' ', '</aside>' );
?>
<h1 itemprop="name" class="entry-title"><?php the_title(); ?></h1>
</header>