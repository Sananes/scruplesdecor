jQuery( function( $ ) {

	var wc_bookings_date_picker = {
		init: function() {
			$( 'body' ).on( 'change', '#wc_bookings_field_duration, #wc_bookings_field_resource', this.date_picker_init );
			$( 'body' ).on( 'click', '.wc-bookings-date-picker legend small.wc-bookings-date-picker-choose-date', this.toggle_calendar );
			$( 'body' ).on( 'input', '.booking_date_year, .booking_date_month, .booking_date_day', this.input_date_trigger );
			$( 'body' ).on( 'change', '.booking_to_date_year, .booking_to_date_month, .booking_to_date_day', this.input_date_trigger );
			$( '.wc-bookings-date-picker legend small.wc-bookings-date-picker-choose-date' ).show();
			$( '.wc-bookings-date-picker' ).each( function() {
				var form     = $( this ).closest( 'form' ),
				    picker   = form.find( '.picker' ),
					fieldset = $( this ).closest( 'fieldset' );

				wc_bookings_date_picker.date_picker_init( picker );

				if ( picker.data( 'display' ) == 'always_visible' ) {
					$( '.wc-bookings-date-picker-date-fields', fieldset ).hide();
					$( '.wc-bookings-date-picker-choose-date', fieldset ).hide();
				} else {
					picker.hide();
				}

				if ( picker.data( 'is_range_picker_enabled' ) ) {
					form.find( 'p.wc_bookings_field_duration' ).hide();
					form.find( '.wc_bookings_field_start_date legend span.label' ).text( 'always_visible' !== picker.data( 'display' ) ? booking_form_params.i18n_dates : booking_form_params.i18n_start_date );
				}
			} );
		},
		calc_duration: function( picker ) {
			var form     = picker.closest('form');
			var fieldset = picker.closest('fieldset');
			setTimeout( function() {
				var days    = 1;
				var e_year  = parseInt( fieldset.find( 'input.booking_to_date_year' ).val() );
				var e_month = parseInt( fieldset.find( 'input.booking_to_date_month' ).val() );
				var e_day   = parseInt( fieldset.find( 'input.booking_to_date_day' ).val() );
				var s_year  = parseInt( fieldset.find( 'input.booking_date_year' ).val() );
				var s_month = parseInt( fieldset.find( 'input.booking_date_month' ).val() );
				var s_day   = parseInt( fieldset.find( 'input.booking_date_day' ).val() );

				if ( e_year && e_month >= 0 && e_day && s_year && s_month >= 0 && s_day ) {
					var s_date = new Date( s_year, s_month - 1, s_day );
					var e_date = new Date( e_year, e_month - 1, e_day );

					days = Math.floor( ( e_date.getTime() - s_date.getTime() ) / ( 1000*60*60*24 ) ) + 1;
				}

				form.find( '#wc_bookings_field_duration' ).val( days).change();
			} );

		},
		toggle_calendar: function() {
			$picker = $( this ).closest( 'fieldset' ).find( '.picker:eq(0)' );
			wc_bookings_date_picker.date_picker_init( $picker );
			$picker.slideToggle();
		},
		input_date_trigger: function() {
			var $fieldset = $(this).closest('fieldset');
			var $picker   = $fieldset.find( '.picker:eq(0)' );
			var $form     = $(this).closest('form');

			var year      = parseInt( $fieldset.find( 'input.booking_date_year' ).val(), 10 );
			var month     = parseInt( $fieldset.find( 'input.booking_date_month' ).val(), 10 );
			var day       = parseInt( $fieldset.find( 'input.booking_date_day' ).val(), 10 );

			if ( year && month && day ) {
				var date = new Date( year, month - 1, day );
				$picker.datepicker( "setDate", date );

				if ( $picker.data( 'is_range_picker_enabled' ) ) {
					var to_year      = parseInt( $fieldset.find( 'input.booking_to_date_year' ).val(), 10 );
					var to_month     = parseInt( $fieldset.find( 'input.booking_to_date_month' ).val(), 10 );
					var to_day       = parseInt( $fieldset.find( 'input.booking_to_date_day' ).val(), 10 );

					var to_date = new Date( to_year, to_month - 1, to_day );

					if ( ! to_date || to_date < date ) {
						$fieldset.find( 'input.booking_to_date_year' ).val( '' ).addClass( 'error' );
						$fieldset.find( 'input.booking_to_date_month' ).val( '' ).addClass( 'error' );
						$fieldset.find( 'input.booking_to_date_day' ).val( '' ).addClass( 'error' );
					} else {
						$fieldset.find( 'input' ).removeClass( 'error' );
						wc_bookings_date_picker.calc_duration( $picker );
					}
				}
				$form.find( '.wc-bookings-booking-form').triggerHandler( 'date-selected', date );
			}
		},
		select_date_trigger: function( date ) {
			var fieldset          = $( this ).closest('fieldset');
			var picker            = fieldset.find( '.picker:eq(0)' );
			var form              = $( this ).closest( 'form' );
			var parsed_date       = date.split( '-' );
			var start_or_end_date = picker.data( 'start_or_end_date' );

			if ( ! picker.data( 'is_range_picker_enabled' ) || ! start_or_end_date ) {
				start_or_end_date = 'start';
			}

			// End date selected
			if ( start_or_end_date === 'end' ) {

				// Set min date to default
				picker.data( 'min_date', 'o_min_date' )

				// Set fields
				fieldset.find( 'input.booking_to_date_year' ).val( parsed_date[0] );
				fieldset.find( 'input.booking_to_date_month' ).val( parsed_date[1] );
				fieldset.find( 'input.booking_to_date_day' ).val( parsed_date[2] ).change();

				// Calc duration
				if ( picker.data( 'is_range_picker_enabled' ) ) {
					wc_bookings_date_picker.calc_duration( picker );
				}

				// Next click will be start date
				picker.data( 'start_or_end_date', 'start' );

				if ( picker.data( 'is_range_picker_enabled' ) ) {
					form.find( '.wc_bookings_field_start_date legend span.label' ).text( 'always_visible' !== picker.data( 'display' ) ? booking_form_params.i18n_dates : booking_form_params.i18n_start_date );
				}

				if ( 'always_visible' !== picker.data( 'display' ) ) {
					$( this ).hide();
				}

			// Start date selected
			} else {
				// Set min date to today
				if ( picker.data( 'is_range_picker_enabled' ) ) {
					picker.data( 'o_min_date', 'min_date' )
					picker.data( 'min_date', date )
				}

				// Set fields
				fieldset.find( 'input.booking_to_date_year' ).val( '' );
				fieldset.find( 'input.booking_to_date_month' ).val( '' );
				fieldset.find( 'input.booking_to_date_day' ).val( '' );

				fieldset.find( 'input.booking_date_year' ).val( parsed_date[0] );
				fieldset.find( 'input.booking_date_month' ).val( parsed_date[1] );
				fieldset.find( 'input.booking_date_day' ).val( parsed_date[2] ).change();

				// Calc duration
				if ( picker.data( 'is_range_picker_enabled' ) ) {
					wc_bookings_date_picker.calc_duration( picker );
				}

				// Next click will be end date
				picker.data( 'start_or_end_date', 'end' );

				if ( picker.data( 'is_range_picker_enabled' ) ) {
					form.find( '.wc_bookings_field_start_date legend span.label' ).text( booking_form_params.i18n_end_date );
				}

				if ( 'always_visible' !== picker.data( 'display' ) && ! picker.data( 'is_range_picker_enabled' ) ) {
					$( this ).hide();
				}
			}

			form.find( '.wc-bookings-booking-form' ).triggerHandler( 'date-selected', date, start_or_end_date );
		},
		date_picker_init: function( element ) {
			if ( $( element ).is( '.picker' ) ) {
				var $picker = $( element );
			} else {
				var $picker = $( this ).closest('form').find( '.picker:eq(0)' );
			}

			$picker.empty().removeClass('hasDatepicker').datepicker({
				dateFormat: $.datepicker.ISO_8601,
				showWeek: false,
				showOn: false,
				beforeShowDay: wc_bookings_date_picker.is_bookable,
				onSelect: wc_bookings_date_picker.select_date_trigger,
				minDate: $picker.data( 'min_date' ),
				maxDate: $picker.data( 'max_date' ),
				defaultDate: $picker.data( 'default_date'),
				numberOfMonths: 1,
				showButtonPanel: false,
				showOtherMonths: true,
				selectOtherMonths: true,
				closeText: wc_bookings_booking_form.closeText,
				currentText: wc_bookings_booking_form.currentText,
				monthNames: wc_bookings_booking_form.monthNames,
				monthNamesShort: wc_bookings_booking_form.monthNamesShort,
				dayNames: wc_bookings_booking_form.dayNames,
				dayNamesShort: wc_bookings_booking_form.dayNamesShort,
				dayNamesMin: wc_bookings_booking_form.dayNamesMin,
				firstDay: wc_bookings_booking_form.firstDay,
				gotoCurrent: true
			});

			$( '.ui-datepicker-current-day' ).removeClass( 'ui-datepicker-current-day' );

			var form  = $picker.closest( 'form' );
			var year  = parseInt( form.find( 'input.booking_date_year' ).val(), 10 );
			var month = parseInt( form.find( 'input.booking_date_month' ).val(), 10 );
			var day   = parseInt( form.find( 'input.booking_date_day' ).val(), 10 );

			if ( year && month && day ) {
				var date = new Date( year, month - 1, day );
				$picker.datepicker( "setDate", date );
			}
		},
		get_input_date: function( fieldset, where ) {
			var year  = fieldset.find( 'input.booking_' + where + 'date_year' ),
				month = fieldset.find( 'input.booking_' + where + 'date_month' ),
				day   = fieldset.find( 'input.booking_' + where + 'date_day' );

			if ( 0 !== year.val().length && 0 !== month.val().length && 0 !== day.val().length ) {
				return year.val() + '-' + month.val() + '-' + day.val();
			} else {
				return '';
			}
		},
		is_bookable: function( date ) {
			var $form                      = $( this ).closest('form');
			var $picker                    = $form.find( '.picker:eq(0)' );
			var availability               = $( this ).data( 'availability' );
			var default_availability       = $( this ).data( 'default-availability' );
			var fully_booked_days          = $( this ).data( 'fully-booked-days' );
			var partially_booked_days      = $( this ).data( 'partially-booked-days' );
			var check_availability_against = wc_bookings_booking_form.check_availability_against;
			var css_classes                = '';

			// Get selected resource
			if ( $form.find('select#wc_bookings_field_resource').val() > 0 ) {
				var resource_id = $form.find('select#wc_bookings_field_resource').val();
			} else {
				var resource_id = 0;
			}

			// Get days needed for block - this affects availability
			var duration = wc_bookings_booking_form.booking_duration;
			var the_date = new Date( date );
			var year     = the_date.getFullYear();
			var month    = the_date.getMonth() + 1;
			var day      = the_date.getDate();

			// Fully booked?
			if ( fully_booked_days[ year + '-' + month + '-' + day ] ) {
				if ( fully_booked_days[ year + '-' + month + '-' + day ][0] || fully_booked_days[ year + '-' + month + '-' + day ][ resource_id ] ) {
					return [ false, 'fully_booked', booking_form_params.i18n_date_unavailable ];
				}
			}

			if ( '' + year + month + day < wc_bookings_booking_form.current_time ) {
				return [ false, 'not_bookable', '' ];
			}

			// Partially booked?
			if ( partially_booked_days && partially_booked_days[ year + '-' + month + '-' + day ] ) {
				if ( partially_booked_days[ year + '-' + month + '-' + day ][0] || partially_booked_days[ year + '-' + month + '-' + day ][ resource_id ] ) {
					css_classes = css_classes + 'partial_booked ';
				}
			}

			if ( $form.find('#wc_bookings_field_duration').size() > 0 && wc_bookings_booking_form.duration_unit != 'minute' && wc_bookings_booking_form.duration_unit != 'hour' && ! $picker.data( 'is_range_picker_enabled' ) ) {
				var user_duration = $form.find('#wc_bookings_field_duration').val();
				var days_needed   = duration * user_duration;
			} else {
				var days_needed   = duration;
			}

			if ( days_needed < 1 || check_availability_against == 'start' ) {
				days_needed = 1;
			}

			var bookable = default_availability;

			// Loop all the days we need to check for this block
			for ( var i = 0; i < days_needed; i++ ) {
				var the_date     = new Date( date );
				the_date.setDate( the_date.getDate() + i );

				var year        = the_date.getFullYear();
				var month       = the_date.getMonth() + 1;
				var day         = the_date.getDate();
				var day_of_week = the_date.getDay();
				var week        = $.datepicker.iso8601Week( the_date );

				// Reset bookable for each day being checked
				bookable = default_availability;

				// Sunday is 0, Monday is 1, and so on.
				if ( day_of_week == 0 ) {
					day_of_week = 7;
				}

				$.each( availability[ resource_id ], function( index, rule ) {
					var type  = rule[0];
					var rules = rule[1];
					try {
						switch ( type ) {
							case 'months':
								if ( typeof rules[ month ] != 'undefined' ) {
									bookable = rules[ month ];

									return false;
								}
							break;
							case 'weeks':
								if ( typeof rules[ week ] != 'undefined' ) {
									bookable = rules[ week ];

									return false;
								}
							break;
							case 'days':
								if ( typeof rules[ day_of_week ] != 'undefined' ) {
									bookable = rules[ day_of_week ];

									return false;
								}
							break;
							case 'custom':
								if ( typeof rules[ year ][ month ][ day ] != 'undefined' ) {
									bookable = rules[ year ][ month ][ day ];

									return false;
								}
							break;
						}
					} catch( err ) {}

					return true;
				});

				// Fully booked in entire block?
				if ( fully_booked_days[ year + '-' + month + '-' + day ] ) {
					if ( fully_booked_days[ year + '-' + month + '-' + day ][0] || fully_booked_days[ year + '-' + month + '-' + day ][ resource_id ] ) {
						bookable = false;
					}
				}

				if ( ! bookable ) {
					break;
				}
			}

			if ( ! bookable ) {
				return [ bookable, 'not_bookable', '' ];
			} else {
				if ( $picker.data( 'is_range_picker_enabled' ) ) {
					var fieldset     = $(this).closest( 'fieldset' ),
						start_date   = $.datepicker.parseDate( $.datepicker.ISO_8601, wc_bookings_date_picker.get_input_date( fieldset, '' ) ),
						end_date     = $.datepicker.parseDate( $.datepicker.ISO_8601, wc_bookings_date_picker.get_input_date( fieldset, 'to_' ) );

					return [ bookable, start_date && ( ( date.getTime() === start_date.getTime() ) || ( end_date && date >= start_date && date <= end_date ) ) ? css_classes + 'bookable-range' : css_classes + 'bookable', '' ];
				} else {
					return [ bookable, css_classes + 'bookable', '' ];
				}
			}
		}
	};

	wc_bookings_date_picker.init();
});