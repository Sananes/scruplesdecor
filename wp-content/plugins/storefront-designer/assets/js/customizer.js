/**
 * Theme Customizer enhancements for a better user experience.
 *
 * Contains handlers to make Theme Customizer preview reload changes asynchronously.
 */

( function( $ ) {
	// Background color
	if ( jQuery(window).width() > 768 ) {

		wp.customize( 'sd_fixed_width', function( value ) {
			value.bind( function() {
				$( 'body' ).toggleClass( 'sd-fixed-width' );
			} );
		} );

		wp.customize( 'sd_max_width', function( value ) {
			value.bind( function() {
				$( 'body' ).toggleClass( 'sd-max-width' );
			} );
		} );

		wp.customize( 'sd_scale', function( value ) {
			value.bind( function( to ) {
				$( 'body' ).removeClass( 'sd-scale-smaller' ).removeClass( 'sd-scale-larger' );
				$( 'body' ).addClass( 'sd-scale-' + to );
			} );
		} );

		wp.customize( 'sd_button_flat', function( value ) {
			value.bind( function( to ) {
				if ( to == true ) {
					$( 'body' ).addClass( 'sd-buttons-flat' );
				} else {
					$( 'body' ).removeClass( 'sd-buttons-flat' );
				}
			} );
		} );

	}
} )( jQuery );