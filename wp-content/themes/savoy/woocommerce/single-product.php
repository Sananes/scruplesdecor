<?php
/**
 * The Template for displaying all single products.
 *
 * Override this template by copying it to yourtheme/woocommerce/single-product.php
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

// Action: woocommerce_before_main_content
remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20, 0 );

get_header( 'shop' ); ?>

<?php
	/**
	 * woocommerce_before_main_content hook
	 *
	 * @hooked woocommerce_output_content_wrapper - 10 (outputs opening divs for the content)
	 */
	do_action( 'woocommerce_before_main_content' );
?>

<?php while ( have_posts() ) : the_post(); ?>

	<?php wc_get_template_part( 'content', 'single-product' ); ?>

<?php endwhile; // end of the loop. ?>

<?php
	/**
	 * woocommerce_after_main_content hook
	 *
	 * @hooked woocommerce_output_content_wrapper_end - 10 (outputs closing divs for the content)
	 */
	do_action( 'woocommerce_after_main_content' );
?>

<?php get_footer( 'shop' ); ?>

<!-- PhotoSwipe -->
<div id="pswp" class="pswp" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="pswp__bg"></div>
    
    <div class="pswp__scroll-wrap">
        <div class="pswp__container">
        	<div class="pswp__item"></div><div class="pswp__item"></div><div class="pswp__item"></div>
        </div>
		
        <div class="pswp__ui pswp__ui--hidden">
        	<div class="pswp__top-bar">
                <button class="pswp__button pswp__button--close nm-font nm-font-close2" title="Close (Esc)"></button>
                
                <!-- Element will get class "pswp__preloader--active" when preloader is running (Demo: http://codepen.io/dimsemenov/pen/yyBWoR) -->
                <div class="pswp__preloader">
                	<div class="pswp__preloader__icn">
                		<div class="pswp__preloader__cut">
                			<div class="pswp__preloader__donut"></div>
                		</div>
                	</div>
                </div>
            </div>
			
            <button class="pswp__button pswp__button--arrow--left nm-font nm-font-play flip" title="Previous (arrow button)"></button>
            <button class="pswp__button pswp__button--arrow--right nm-font nm-font-play" title="Next (arrow right)"></button>
        </div>
    </div>
</div>
