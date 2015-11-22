<?php
/**
 *	Oxygen WordPress Theme
 *
 *	Laborator.co
 *	www.laborator.co
 */

global $is_quickview, $quickview_enabled, $show_rating_below_title, $quickview_wp_query, $wp_query;

$is_quickview = true;

if( ! get_data('shop_quickview'))
	return;

if(! is_shop() && ! is_product_category() && ! is_product_tag() && ! $quickview_enabled)
	return;

global $show_rating_below_title;

$show_rating_below_title = true;

wp_enqueue_script('owl-carousel');
wp_enqueue_style('owl-carousel-theme');

wp_reset_query();

if( isset($quickview_wp_query))
{
	#query_posts($quickview_enabled);
	$wp_query = $quickview_wp_query;
}

?>
<script type="text/javascript">
	jQuery(document).ready(function($){ $(".slideshow.notrans").removeClass('notrans'); });
</script>
<section class="slideshow notrans">

	<ul class="quickview-list">

		<?php
		while ( have_posts() ) : the_post();

			global $post;

			$id          = $post->ID;
			$post_copy   = $post;
			$product     = new WC_Product( $post );
			$post        = $post_copy;

			$attachment_ids = $product->get_gallery_attachment_ids();

			if(has_post_thumbnail($id))
			{
				$shown_id = get_post_thumbnail_id($id);

				$first_attachment_link = wp_get_attachment_url( reset($attachment_ids) );
				$thumbnail_attachment_link = wp_get_attachment_url($shown_id);

				if($first_attachment_link != $thumbnail_attachment_link)
					$attachment_ids = array_merge(array($shown_id), $attachment_ids);
			}


			?>
			<li class="quickview-entry product-single">

				<div class="quickview-wrapper">

					<div class="row spread-2">

						<div class="col col-md-6">

							<div class="product-gallery-env">

								<?php if( $product->is_in_stock() == false && ! ($product->is_type('variable') && $product->get_total_stock() > 0)) : ?>
									<div class="ribbon out-of-stock">
										<div class="ribbon-content">
											<?php echo apply_filters( 'woocommerce_sale_flash', '<span class="outofstock">' . __( 'Out of Stock', 'woocommerce' ) . '</span>', $post, $product ); ?>
										</div>
									</div>
								<?php elseif( $product->is_on_sale() ) : ?>
									<div class="ribbon">
										<div class="ribbon-content">
											<?php echo apply_filters( 'woocommerce_sale_flash', '<span class="onsale">' . __( 'Sale', 'oxygen' ) . '</span>', $post, $product ); ?>
										</div>
									</div>
								<?php elseif($product->is_featured() && get_data('shop_featured_product_ribbon_show')): ?>
									<div class="ribbon product-featured">
										<div class="ribbon-content">
											<?php echo apply_filters( 'woocommerce_sale_flash', '<span class="featured">' . __( 'Featured', 'oxygen' ) . '</span>', $post, $product ); ?>
										</div>
									</div>
								<?php endif; ?>

								<div class="product-gallery">
								<?php
								$i = 0;
								foreach($attachment_ids as $attachment_id):

									$image_link = wp_get_attachment_image( $attachment_id, 'shop-thumb-6');

									if ( ! $image_link )
										continue;

									?>
									<div class="gallery-image<?php echo $i > 0 ? ' hidden' : ''; ?>">

										<a href="<?php the_permalink(); ?>">
											<?php #echo laborator_show_img($image_link, 'shop-thumb-6'); ?>
											<?php echo $image_link; ?>
										</a>

									</div>
									<?php

									$i++;

								endforeach;

								?>
								</div>

								<?php
								if(is_yith_wishlist_supported())
									oxygen_yith_wcwl_add_to_wishlist();
								?>
							</div>

						</div>

						<div class="col col-md-6">

							<div class="entry-summary">

								<?php wc_get_template('single-product/title.php'); ?>

								<?php wc_get_template('single-product/short-description.php'); ?>

								<?php wc_get_template('single-product/price.php'); ?>

								<a href="<?php the_permalink(); ?>" class="btn btn-default view-more">
									<i class="entypo-eye"></i>
									<?php _e('View Product', 'oxygen'); ?>
								</a>

								<?php wc_get_template('single-product/share.php'); ?>

							</div>

						</div>

					</div>

				</div>

			</li>
			<?php

		endwhile;
		?>

	</ul>

	<nav>
		<span class="nav-prev"></span>
		<span class="nav-next"></span>
		<span class="nav-close"></span>
	</nav>

</section>