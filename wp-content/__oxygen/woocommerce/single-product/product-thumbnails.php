<?php
/**
 * Single Product Thumbnails
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.3.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $post, $product, $woocommerce, $shown_id;

$attachment_ids = $product->get_gallery_attachment_ids();

if ( $attachment_ids ) {
	$loop 		= 0;
	$columns 	= apply_filters( 'woocommerce_product_thumbnails_columns', 3 );

	wp_enqueue_script('owl-carousel');
	wp_enqueue_style('owl-carousel-theme');

	if($shown_id)
	{
		$first_attachment_link = wp_get_attachment_url( reset($attachment_ids) );
		$thumbnail_attachment_link = wp_get_attachment_url($shown_id);

		if($first_attachment_link != $thumbnail_attachment_link)
			$attachment_ids = array_merge(array($shown_id), $attachment_ids);
	}
	?>
	<div class="thumbnails" id="image-thumbnails-carousel">
		<div class="row">
		<?php

			$loop = 0;
			$columns = apply_filters( 'woocommerce_product_thumbnails_columns', 3 );

			foreach ( $attachment_ids as $attachment_id ) {

				$classes = array('product-thumb', 'zoom', 'col-md-3' );

				if ( $loop == 0 || $loop % $columns == 0 )
					$classes[] = 'first';

				if ( ( $loop + 1 ) % $columns == 0 )
					$classes[] = 'last';

				#$image_link = wp_get_attachment_url( $attachment_id );
				$image = wp_get_attachment_image_src( $attachment_id, apply_filters( 'single_product_small_thumbnail_size', 'shop-thumb-5' ) );

				if ( ! $image )
					continue;

				#$image       = wp_get_attachment_image_src( $attachment_id, apply_filters( 'single_product_small_thumbnail_size', 'shop_thumbnail' ) );

				# start: modified by Arlind Nushi
				#$image = laborator_show_img($image_link, 'shop-thumb-5');
				#$image = laborator_img($image_link, 'shop-thumb-5');

				$image = wp_get_attachment_image_src( $attachment_id, apply_filters( 'single_product_small_thumbnail_size', 'shop-thumb-5' ) );

				$image = '<img class="lazyOwl" data-src="'.$image[0].'" />';

				$image_link = wp_get_attachment_image_src( $attachment_id, 'original' );

				if(is_array($image_link))
					$image_link = $image_link[0];
				# end: modified by Arlind Nushi

				$image_class = esc_attr( implode( ' ', $classes ) );
				$image_title = esc_attr( get_the_title( $attachment_id ) );


				echo apply_filters( 'woocommerce_single_product_image_thumbnail_html', sprintf( '<a href="%s" class="%s" title="%s" data-index="%d"><span>%s</span></a>', $image_link, $image_class, $image_title, $loop, $image ), $attachment_id, $post->ID, $image_class );

				$loop++;
			}

		?>
		</div>
	</div>

	<script type="text/javascript">
	<?php
	$auto_play = absint(get_data('shop_single_auto_rotate_image')) * 1000;
	?>
	jQuery(document).ready(function($)
	{
		if($(".yith_magnifier_thumbnail").length > 0)
		{
			return;
		}
		
		// Main Image
		var $mis = $("#main-image-slider");

		$mis.find(".hidden").removeClass("hidden");

		var misOptions = {
			items: 1,
			navigation: true,
			pagination: false,
			singleItem: true,
			autoHeight: true,
			autoPlay: <?php echo $auto_play == 0 ? 'false' : $auto_play; ?>,
			stopOnHover: true,
			slideSpeed: 400
		};

		$mis.owlCarousel(misOptions);

		$mis.find(".woocommerce-main-image").nivoLightbox({
			effect: 'fadeScale'
		});


		// Thumbnails
		var $thumbs = $("#image-thumbnails-carousel .row");

		$thumbs.owlCarousel({
			items: 4,
			lazyLoad: true,
			navigation: true,
			pagination: false,
			itemsMobile: [479,4],
			itemsTablet: [768,4]
		});

		var owl = $mis.data('owlCarousel');

		$("#image-thumbnails-carousel .product-thumb").each(function(i, el)
		{
			var index = $(el).data('index');

			$(el).hoverIntent({
				over: function(){ owl.goTo( index ); },
				out: function(){},
				interval: 420
			});

			$(el).click(function(ev)
			{
				ev.preventDefault();

				var _owl = $mis.data('owlCarousel'),
					extra_index = _owl.itemsAmount - $thumbs.data('owlCarousel').itemsAmount;

				_owl.stop();
				_owl.goTo( index + extra_index );
			});
		});


		// Main Image Zoom
		$mis.find('.zoom-image').on('click', function(ev)
		{
			ev.preventDefault();

			var $this = $(this);

			launchFullscreen(document.documentElement);
		});


		$mis.find('a').data('is-general', true);

		// Variations selector
		jQuery(window).load(function()
		{

			$( 'form.variations_form' )
			.on('found_variation', function(ev, variation){

				showVariation(variation);
			})
			.on('wc_additional_variation_images_frontend_lightbox', function(a)
			{
				var html = $(".images .thumbnails").html();

				showVariation(null, html);

			})
			.on('reset_image', function(){
				showVariation();
			});

			var showVariation = function(variation, extra)
			{
				$mis.data('owlCarousel').destroy();

				// Remove Non-general images
				if( ! extra)
				{
					$mis.find('a').each(function(i, el)
					{
						var $el = $(el);

						if( ! $el.data('is-general'))
						{
							$el.remove();
						}
					});
				}

				if(variation)
				{
					// Variation has Image
					if( variation.image_src )
					{
						var $a = $('<a />'),
							$img = $('<img />');

						$a.attr({
							'href': variation.image_src,
							'title': variation.image_title,
							'data-lightbox-gallery': 'shop-gallery'
						}).addClass('woocommerce-main-image');

						$img.attr({
							src: variation.image_src
						});

						$a.append($img);

						<?php if(get_data('shop_single_fullscreen')): ?>
						$a.append('<span class="zoom-image"><i class="glyphicon glyphicon-fullscreen"></i></span>');
						<?php endif; ?>

						$mis.prepend($a)
					}
				}

				if(extra)
				{
					$mis.find('a').first().after(extra);


					<?php if(get_data('shop_single_fullscreen')): ?>
					$mis.find('a:not(a:has(.zoom-image))').addClass('woocommerce-main-image').append('<span class="zoom-image"><i class="glyphicon glyphicon-fullscreen"></i></span>');
					<?php endif; ?>
				}

				if($.isFunction($.fn.nivoLightbox))
				{
					$mis.find('a').nivoLightbox({
						effect: 'fade',
						theme: 'default',
					});
				}

				$mis.owlCarousel(misOptions);
			};

		});
	});
	</script>
	<?php
}
else
if(has_post_thumbnail())
{
?>
<script type="text/javascript">

	jQuery(document).ready(function($)
	{
		// Main Image
		var $mis = $("#main-image-slider");

		$mis.find(".woocommerce-main-image").nivoLightbox({
			effect: 'fadeScale'
		});
	});

</script>
<?php
}