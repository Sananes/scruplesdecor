<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class WC_Additional_Variation_Images_Frontend {
	private static $_this;

	/**
	 * init
	 *
	 * @since 1.0.0
	 * @return bool
	 */
	public function __construct() {
		self::$_this = $this;

		if ( ! is_admin() ) {
			add_action( 'wp_enqueue_scripts', array( $this, 'load_scripts' ) );
		}

		if ( is_admin() ) {
			add_action( 'wp_ajax_wc_additional_variation_images_load_frontend_images_ajax', array( $this, 'load_images_ajax' ) );
			add_action( 'wp_ajax_nopriv_wc_additional_variation_images_load_frontend_images_ajax', array( $this, 'load_images_ajax' ) );
		}

    	return true;
	}

	/**
	 * public function to get instance
	 *
	 * @since 1.1.1
	 * @return instance object
	 */
	public function get_instance() {
		return self::$_this;
	}

	/**
	 * load frontend scripts
	 *
	 * @since 1.0.0
	 * @return bool
	 */
	public function load_scripts() {
		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		wp_enqueue_script( 'wc_additional_variation_images_script', plugins_url( 'assets/js/frontend' . $suffix . '.js' , dirname( __FILE__ ) ), array( 'jquery' ) );

		$localized_vars = array(
			'ajaxurl'            => admin_url( 'admin-ajax.php' ),
			'ajaxImageSwapNonce' => wp_create_nonce( '_wc_additional_variation_images_nonce' ),
			'gallery_images_class' => apply_filters( 'wc_additional_variation_images_gallery_images_class', '.product .images .thumbnails' ),
			'main_images_class' => apply_filters( 'wc_additional_variation_images_main_images_class', '.product .images > a' ),
			'custom_swap' => apply_filters( 'wc_additional_variation_images_custom_swap', false ),
			'custom_original_swap' => apply_filters( 'wc_additional_variation_images_custom_original_swap', false ),
			'custom_reset_swap' => apply_filters( 'wc_additional_variation_images_custom_reset_swap', false ),
		);
		
		wp_localize_script( 'wc_additional_variation_images_script', 'wc_additional_variation_images_local', $localized_vars );

		return true;
	}

	/**
	 * checks if cloud zoom plugin exists
	 *
	 * @since 1.3.0
	 * @return boolean
	 */
	public function cloud_zoom_exists() {
		if ( class_exists( 'woocommerce_professor_cloud' ) ) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * load variation images frontend ajax
	 *
	 * @since 1.0.0
	 * @return html
	 */
	public function load_images_ajax() {
		$nonce = $_POST['ajaxImageSwapNonce'];

		// bail if nonce don't check out
		if ( ! wp_verify_nonce( $nonce, '_wc_additional_variation_images_nonce' ) ) {
		     die ( 'error' );		
		 }

		// bail if no ids submitted
		if ( ! isset( $_POST['variation_id'] ) ) {
			die( 'error' );
		}

		// sanitize
		$variation_id = absint( $_POST['variation_id'] );
		$post_id = absint( $_POST['post_id'] );

		// get post meta
		$image_ids = get_post_meta( $variation_id, '_wc_additional_variation_images', true );

		$image_ids = explode( ',', $image_ids );

		$main_images = '';
		$gallery_images = '';

		$loop = 0;
		$columns = (int) apply_filters( 'woocommerce_product_thumbnails_columns', 3 );

		if ( 0 < count( $image_ids ) ) {

			if ( apply_filters( 'wc_additional_variation_images_get_first_image', false ) ) {
				array_unshift( $image_ids, (string) $variation_id );
			}

			// build html
			foreach( $image_ids as $id ) {
				$attachment = wp_get_attachment_image_src( $id );

				$classes = array( 'zoom' );

				if ( $loop == 0 || $loop % $columns == 0 ) {
					$classes[] = 'first';
				}

				if ( ( $loop + 1 ) % $columns == 0 ) {
					$classes[] = 'last';
				}

				$image_link = wp_get_attachment_url( $id );

				if ( ! apply_filters( 'wc_additional_variation_images_get_first_image', false ) ) {
					if ( ! $image_link ) {
						continue;
					}
				}

				$gallery_image = wp_get_attachment_image( $id, apply_filters( 'single_product_small_thumbnail_size', 'shop_thumbnail' ) );
				$main_image = wp_get_attachment_image( $id, apply_filters( 'single_product_large_thumbnail_size', 'shop_single' ) );

				$image_title = esc_attr( get_the_title( $id ) );

				// support for cloud zoom plugin
				if ( $this->cloud_zoom_exists() ) {
					$image_class = esc_attr( implode( ' ', $classes ) . ' ' . 'cloud-zoom-gallery' );
					$prettyPhoto = 'rel="prettyPhoto"';
					$cloudmediumimage = wp_get_attachment_image_src( $id, 'shop_single' );
					$cloudzoom = 'cloud="useZoom:\'zoom1\',smallImage:\'' . $cloudmediumimage[0] . '\'"';
				} else {
					$image_class = esc_attr( implode( ' ', $classes ) );
					$prettyPhoto = 'data-rel="prettyPhoto[product-gallery]"';
					$cloudzoom = '';
				}
				
				// see if we need to get the first image of the variation
				// only run one time
				if ( apply_filters( 'wc_additional_variation_images_get_first_image', false ) && $loop === 0 ) {
					$main_image_title = esc_attr( get_the_title( get_post_thumbnail_id( $id ) ) );
					$main_image_link  = wp_get_attachment_url( get_post_thumbnail_id( $id ) );
					$main_image       = get_the_post_thumbnail( $id, apply_filters( 'single_product_large_thumbnail_size', 'shop_single' ), array(
						'title' => $main_image_title
						) );

					$main_images .= apply_filters( 'woocommerce_single_product_image_html', sprintf( '<a href="%s" itemprop="image" class="woocommerce-main-image zoom" title="%s">%s</a>', $main_image_link, $main_image_title, $main_image ), $id );

					$gallery_image_title = esc_attr( get_the_title( get_post_thumbnail_id( $id ) ) );
					$gallery_image_link  = wp_get_attachment_url( get_post_thumbnail_id( $id ) );
					$gallery_image       = get_the_post_thumbnail( $id, apply_filters( 'single_product_large_thumbnail_size', 'shop_single' ), array(
						'title' => $gallery_image_title
						) );

					$gallery_images .= apply_filters( 'woocommerce_single_product_image_thumbnail_html', sprintf( '<a href="%s" class="%s" title="%s" %s %s>%s</a>', $gallery_image_link, $image_class, $gallery_image_title, $prettyPhoto, $cloudzoom, $gallery_image ), $id, $post_id, $image_class );

					$loop++;
					continue;
				}

				// build the list of variations as main images in case a custom theme has flexslider type lightbox				
				$main_images .= apply_filters( 'woocommerce_single_product_image_html', sprintf( '<a href="%s" itemprop="image" class="woocommerce-main-image zoom" title="%s">%s</a>', $image_link, $image_title, $main_image ), $post_id );

				$gallery_images .= apply_filters( 'woocommerce_single_product_image_thumbnail_html', sprintf( '<a href="%s" class="%s" title="%s" %s %s>%s</a>', $image_link, $image_class, $image_title, $prettyPhoto, $cloudzoom, $gallery_image ), $id, $post_id, $image_class );

				$loop++;
			}
		}

		echo json_encode( array( 'main_images' => $main_images, 'gallery_images' => $gallery_images ) );
		exit;
	}
}

new WC_Additional_Variation_Images_Frontend();