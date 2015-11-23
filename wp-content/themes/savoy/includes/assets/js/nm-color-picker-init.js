// Thanks: https://core.trac.wordpress.org/attachment/ticket/25809/color-picker-widget.php
(function($) {
	
	'use strict';
	
	function initColorPicker(widget) {
		widget.find('.nm-widget-color-picker').wpColorPicker(/*{
			change: _.throttle(function() { // For Customizer
				$(this).trigger('change');
			}, 3000)
		}*/);
	};
	
	function onFormUpdate(event, widget) {
		initColorPicker(widget);
	};
	
	var $document = $(document);
	
	$document.on('widget-added widget-updated', onFormUpdate);
	
	$document.ready(function() {
		$('#widgets-right .widget:has(.nm-widget-color-picker)').each(function() {
			initColorPicker($(this));
		});
	});
} (jQuery));
