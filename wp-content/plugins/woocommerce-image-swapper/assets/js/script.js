/*-----------------------------------------------------------------------------------*/
/* Plugin Name: WooCommerce Products Image swapper */
/*-----------------------------------------------------------------------------------*/
jQuery(document).on('hover', 'ul.products li.wcpis-has-gallery a:first-child', function(e){
     if(e.type == 'mouseenter') {		 
            var img = jQuery(this).find('img.secondary-image');
            if ( img.length > 0 ) img.css({'display':'block', opacity:0}).animate({opacity:1},trans_speed);

        } else if ( e.type == 'mouseleave' ) {
            var img = jQuery(this).find('img.secondary-image');
            if ( img.length > 0 ) img.animate({opacity:0},trans_speed);
        }
    });

