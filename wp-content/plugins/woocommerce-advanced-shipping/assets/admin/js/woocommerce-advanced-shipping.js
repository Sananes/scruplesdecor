jQuery( function( $ ) {

	var loading_icon = '<span class="loading-icon"><img src="images/wpspin_light.gif"/></span>';

	// Add condition
	$( '#was_conditions' ).on( 'click', '.condition-add', function() {

		var data = { action: 'was_add_condition', group: $( this ).attr( 'data-group' ) };

		$( '.condition-group-' + data.group ).append( loading_icon ).children( ':last' );

		$.post( ajaxurl, data, function( response ) {
			$( '.condition-group-' + data.group ).append( response ).children( ':last' ).hide().fadeIn( 'normal' );
			$( '.condition-group-' + data.group + ' .loading-icon' ).children( ':first' ).remove();
		});

	});

	// Delete condition
	$( '#was_conditions' ).on( 'click', '.condition-delete', function() {

		if ( $( this ).closest( '.condition-group' ).children( '.was-condition-wrap' ).length == 1 ) {
			$( this ).closest( '.condition-group' ).fadeOut( 'normal', function() { $( this ).remove();	});

		} else {
			$( this ).closest( '.was-condition-wrap' ).fadeOut( 'normal', function() { $( this ).remove(); });
		}

	});

	// Add condition group
	$( '#was_conditions' ).on( 'click', '.condition-group-add', function() {

		// Display loading icon
		$( '.was_conditions' ).append( loading_icon ).children( ':last' );

		var data = {
			action: 'was_add_condition_group',
			group: 	parseInt( $( '.condition-group' ).last().attr( 'data-group') ) + 1
		};

		// Insert condition group
		$.post( ajaxurl, data, function( response ) {
			$( '.condition-group ~ .loading-icon' ).last().remove();
			$( '.was_conditions' ).append( response ).children( ':last' ).hide().fadeIn( 'normal' );
		});

	});

	// Update condition values
	$( '#was_conditions' ).on( 'change', '.was-condition', function () {

		var data = {
			action: 		'was_update_condition_value',
			id:				$( this ).attr( 'data-id' ),
			group:			$( this ).attr( 'data-group' ),
			condition: 		$( this ).val()
		};

		var replace = '.was-value-wrap-' + data.id;

		$( replace ).html( loading_icon );

		$.post( ajaxurl, data, function( response ) {
			$( replace ).replaceWith( response );
		});

		// Update condition description
		var description = {
			action:		'was_update_condition_description',
			condition: 	data.condition
		};

		$.post( ajaxurl, description, function( description_response ) {
			$( replace + ' ~ .was-description' ).replaceWith( description_response );
		})

	});

	// Sortable
	$( '.was-table tbody' ).sortable({
		items:					'tr',
		handle:					'.sort',
		cursor:					'move',
		axis:					'y',
		scrollSensitivity:		40,
		forcePlaceholderSize: 	true,
		helper: 				'clone',
		opacity: 				0.65,
		placeholder: 			'wc-metabox-sortable-placeholder',
		start:function(event,ui){
			ui.item.css( 'background-color','#f6f6f6' );
		},
		stop:function(event,ui){
			ui.item.removeAttr( 'style' );
		},
		update: function(event, ui) {

			$table 	= $( this ).closest( 'table' );
			$table.block({ message: null, overlayCSS: { background: '#fff', opacity: 0.6 } });
			// Update shipping method order
			var data = {
				action:	'save_method_order',
				form: 	$( this ).closest( 'form' ).serialize()
			};

			$.post( ajaxurl, data, function( response ) {
				$( '.was-table tbody tr:even' ).addClass( 'alternate' );
				$( '.was-table tbody tr:odd' ).removeClass( 'alternate' );
				$table.unblock();
			})
		}
	});

});