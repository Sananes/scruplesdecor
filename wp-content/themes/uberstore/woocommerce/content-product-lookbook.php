<?php
/**
 * The template for displaying lookbook product style content within loops.
 *
 * Override this template by copying it to yourtheme/woocommerce/content-product.php
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     1.6.4
 */

global $product, $woocommerce_loop;

$attachment_ids = $product->get_gallery_attachment_ids();

// Store loop count we're currently on
if ( empty( $woocommerce_loop['loop'] ) )
	$woocommerce_loop['loop'] = 0;

// Store column count for displaying the grid
if ( empty( $woocommerce_loop['columns'] ) )
	$woocommerce_loop['columns'] = apply_filters( 'loop_shop_columns', 4 );

// Ensure visibilty
if ( ! $product->is_visible() )
	return;

// Increase loop count
$woocommerce_loop['loop']++;

?>

<article <?php post_class('columns'); ?>>

<?php do_action( 'woocommerce_before_shop_loop_item' ); ?>

	<figure>
		<?php if ( has_post_thumbnail() ) : 
			$image_id = get_post_thumbnail_id();
			$image_link = wp_get_attachment_image_src($image_id,'full');
			$image = aq_resize( $image_link[0], 370, 520, true, false);
		?>
			<img  src="<?php echo $image[0]; ?>" width="<?php echo $image[1]; ?>" height="<?php echo $image[2]; ?>" />
		<?php endif; ?>
		<div class="overlay">
			<div class="post-title">
				<?php
					$size = sizeof( get_the_terms( $post->ID, 'product_cat' ) );
					echo $product->get_categories( ', ', '<aside class="post_categories">' . _n( '', '', $size, THB_THEME_NAME ) . ' ', '</aside>' );
				?>
				<h1><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h1>
				<a href="<?php the_permalink(); ?>" class="btn white"><?php _e('Shop The Look', THB_THEME_NAME); ?></a>
			</div>
		</div>
	</figure>
</article><!-- end product -->