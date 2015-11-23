/* =========================================================
 * templates-preview.js v1.0.0
 * =========================================================
 * Copyright 2015 WPBakery
 *
 * Visual composer template preview
 * ========================================================= */
/* global vc */
(function ( $ ) {
	'use strict';
	vc.events.on( 'app.addAll', function () {
		if ( parent && parent.vc ) {
			parent.vc.templates_panel_view.setTemplatePreviewSize();
		}
	} );
	$(window ).resize(function(){
		parent.vc.templates_panel_view.setTemplatePreviewSize();
	});
})( window.jQuery );