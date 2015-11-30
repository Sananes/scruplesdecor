/* =========================================================
 * composer-view.js v0.2.1
 * =========================================================
 * Copyright 2013 Wpbakery
 *
 * Visual composer backbone/underscore version
 * ========================================================= */
(function ( $ ) {
	var i18n = window.i18nLocale,
		store = vc.storage,
		Shortcodes = vc.shortcodes;
	vc.templateOptions = {
		default: {
			evaluate: /<%([\s\S]+?)%>/g,
			interpolate: /<%=([\s\S]+?)%>/g,
			escape: /<%-([\s\S]+?)%>/g
		},
		custom: {
			evaluate: /<#([\s\S]+?)#>/g,
			interpolate: /\{\{\{([\s\S]+?)\}\}\}/g,
			escape: /\{\{([^\}]+?)\}\}(?!\})/g
		}

	};
	vc.builder = {
		toString: function ( model, type ) {
			var params = model.get( 'params' ),
				content = _.isString( params.content ) ? params.content : '';
			return wp.shortcode.string( {
				tag: model.get( 'shortcode' ),
				attrs: _.omit( params, 'content' ),
				content: content,
				type: _.isString( type ) ? type : ''
			} );
		}
	};
	/**
	 * Default view for shortcode as block inside Visual composer design mode.
	 * @type {*}
	 */
	vc.clone_index = 1;
	vc.saved_custom_css = false;
	var ShortcodeView = vc.shortcode_view = Backbone.View.extend( {
		tagName: 'div',
		$content: '',
		use_default_content: false,
		params: {},
		events: {
			'click .column_delete,.vc_control-btn-delete': 'deleteShortcode',
			'click .column_add,.vc_control-btn-prepend': 'addElement',
			'click .column_edit,.vc_control-btn-edit, .column_edit_trigger': 'editElement',
			'click .column_clone,.vc_control-btn-clone': 'clone',
			'mousemove': 'checkControlsPosition'
		},
		removeView: function () {
			vc.closeActivePanel( this.model );
			this.remove();
		},
		checkControlsPosition: function () {
			if ( ! this.$controls_buttons ) {
				return;
			}
			var window_top, element_position_top, new_position,
				element_height = this.$el.height(),
				window_height = $( window ).height();
			if ( element_height > window_height ) {
				window_top = $( window ).scrollTop();
				element_position_top = this.$el.offset().top;
				new_position = (window_top - element_position_top) + $( window ).height() / 2;
				if ( 40 < new_position && new_position < element_height ) {
					this.$controls_buttons.css( 'top', new_position );
				} else if ( new_position > element_height ) {
					this.$controls_buttons.css( 'top', element_height - 40 );
				} else {
					this.$controls_buttons.css( 'top', 40 );
				}
			}
		},
		initialize: function () {
			this.model.bind( 'destroy', this.removeView, this );
			this.model.bind( 'change:params', this.changeShortcodeParams, this );
			this.model.bind( 'change_parent_id', this.changeShortcodeParent, this );
			this.createParams();
		},
		/**
		 * @deprecated since 4.8 vc_user_access should be used
		 * @returns {boolean}
		 */
		hasUserAccess: function () {
			var shortcodeTag;

			shortcodeTag = this.model.get( 'shortcode' );
			if ( - 1 < _.indexOf( [
					"vc_row",
					"vc_column",
					"vc_row_inner",
					"vc_column_inner"
				], shortcodeTag ) ) {
				return true; // we cannot block controls for these shortcodes;
			}

			if ( ! _.every( vc.roles.current_user, function ( role ) {
					return ! (! _.isUndefined( vc.roles[ role ] ) && ! _.isUndefined( vc.roles[ role ][ 'shortcodes' ] ) && _.isUndefined( vc.roles[ role ][ 'shortcodes' ][ shortcodeTag ] ));
				} ) ) {
				return false;
			}
			return true;
		},
		/**
		 * Check does current user have a access to shortcode via vc_roles.
		 *
		 * @since 4.8
		 * @param action,
		 */
		canCurrentUser: function ( action ) {
			var tag, result = false;
			tag = this.model.get( 'shortcode' );
			if ( undefined === action || 'all' === action ) {
				result = vc_user_access().shortcodeAll( tag );
			} else {
				result = vc_user_access().shortcodeEdit( tag );
			}
			return result;
		},
		createParams: function () {
			var tag, settings, params;

			tag = this.model.get( 'shortcode' );
			settings = _.isObject( vc.map[ tag ] ) && _.isArray( vc.map[ tag ].params ) ? vc.map[ tag ].params : [];
			params = this.model.get( 'params' );
			this.params = {};
			_.each( settings, function ( param ) {
				this.params[ param.param_name ] = param;
			}, this );
		},
		setContent: function () {
			this.$content = this.$el.find( '> .wpb_element_wrapper > .vc_container_for_children,'
				+ ' > .vc_element-wrapper > .vc_container_for_children' );
		},
		setEmpty: function () {
		},
		unsetEmpty: function () {

		},
		checkIsEmpty: function () {
			if ( this.model.get( 'parent_id' ) ) {
				vc.app.views[ this.model.get( 'parent_id' ) ].checkIsEmpty();
			}
		},

		/**
		 * Convert html into correct element
		 * @param html
		 */
		html2element: function ( html ) {
			var attributes = {},
				$template;
			if ( _.isString( html ) ) {
				this.template = _.template( html );
				$template = $( this.template( this.model.toJSON(), vc.templateOptions.default ).trim() );
			} else {
				this.template = html;
				$template = html;
			}
			_.each( $template.get( 0 ).attributes, function ( attr ) {
				attributes[ attr.name ] = attr.value;
			} );
			this.$el.attr( attributes ).html( $template.html() );
			this.setContent();
			this.renderContent();

		},
		render: function () {
			var $shortcode_template_el = $( '#vc_shortcode-template-' + this.model.get( 'shortcode' ) );
			if ( $shortcode_template_el.is( 'script' ) ) {
				this.html2element( _.template( $shortcode_template_el.html(),
					this.model.toJSON(),
					vc.templateOptions.default ) );
			} else {
				var params = this.model.get( 'params' );
				$.ajax( {
					type: 'POST',
					url: window.ajaxurl,
					data: {
						action: 'wpb_get_element_backend_html',
						data_element: this.model.get( 'shortcode' ),
						data_width: _.isUndefined( params.width ) ? '1/1' : params.width,
						_vcnonce: window.vcAdminNonce
					},
					dataType: 'html',
					context: this
				} ).done( function ( html ) {
					this.html2element( html );
				} );
			}
			this.model.view = this;
			this.$controls_buttons = this.$el.find( '.vc_controls > :first' );
			return this;
		},
		renderContent: function () {
			this.$el.attr( 'data-model-id', this.model.get( 'id' ) );
			this.$el.data( 'model', this.model );
			return this;
		},
		changedContent: function ( view ) {
		},
		_loadDefaults: function () {
			var tag,
				hasChilds;

			tag = this.model.get( 'shortcode' );
			hasChilds = ! ! vc.shortcodes.where( { parent_id: this.model.get( 'id' ) } ).length;
			if ( ! hasChilds && true === this.use_default_content && _.isObject( vc.map[ tag ] ) && _.isString( vc.map[ tag ].default_content ) && vc.map[ tag ].default_content.length ) {
				this.use_default_content = false;
				Shortcodes.createFromString( vc.map[ tag ].default_content, this.model );
			}
		},
		_callJsCallback: function () {
			//Fire INIT callback if it is defined
			var tag = this.model.get( 'shortcode' );
			if ( _.isObject( vc.map[ tag ] ) && _.isObject( vc.map[ tag ].js_callback ) && ! _.isUndefined( vc.map[ tag ].js_callback.init ) ) {
				var fn = vc.map[ tag ].js_callback.init;
				window[ fn ]( this.$el );
			}
		},
		ready: function ( e ) {
			this._loadDefaults();
			this._callJsCallback();
			if ( this.model.get( 'parent_id' ) && _.isObject( vc.app.views[ this.model.get( 'parent_id' ) ] ) ) {
				vc.app.views[ this.model.get( 'parent_id' ) ].changedContent( this );
			}
			_.defer( _.bind( function () {
				vc.events.trigger( 'shortcodeView:ready', this );
				vc.events.trigger( 'shortcodeView:ready:' + this.model.get( 'shortcode' ), this );
			}, this ) );
			return this;
		},
		// View utils {{
		addShortcode: function ( view, method ) {
			var before_shortcode;
			before_shortcode = _.last( vc.shortcodes.filter( function ( shortcode ) {
				return shortcode.get( 'parent_id' ) === this.get( 'parent_id' ) && parseFloat( shortcode.get( 'order' ) ) < parseFloat( this.get( 'order' ) );
			}, view.model ) );
			if ( before_shortcode ) {
				view.render().$el.insertAfter( '[data-model-id=' + before_shortcode.id + ']' );
			} else if ( 'append' === method ) {
				this.$content.append( view.render().el );
			} else {
				this.$content.prepend( view.render().el );
			}
		},
		changeShortcodeParams: function ( model ) {
			var tag,
				params,
				settings,
				view;
			// Triggered when shortcode being updated
			tag = model.get( 'shortcode' );
			params = model.get( 'params' );
			settings = vc.map[ tag ];
			_.defer( function () {
				vc.events.trigger( 'backend.shortcodeViewChangeParams:' + tag );
			} );

			if ( _.isArray( settings.params ) ) {
				_.each( settings.params, function ( param_settings ) {
					var name,
						value,
						$wrapper,
						label_value,
						$admin_label;

					name = param_settings.param_name;
					value = params[ name ];
					$wrapper = this.$el.find( '> .wpb_element_wrapper, > .vc_element-wrapper' );
					label_value = value;
					$admin_label = $wrapper.children( '.admin_label_' + name );

					if ( _.isObject( vc.atts[ param_settings.type ] ) && _.isFunction( vc.atts[ param_settings.type ].render ) ) {
						value = vc.atts[ param_settings.type ].render.call( this, param_settings, value );
					}
					if ( $wrapper.children( '.' + param_settings.param_name ).is( 'input,textarea,select' ) ) {
						$wrapper.children( '[name=' + param_settings.param_name + ']' ).val( value );
					} else if ( $wrapper.children( '.' + param_settings.param_name ).is( 'iframe' ) ) {
						$wrapper.children( '[name=' + param_settings.param_name + ']' ).attr( 'src', value );
					} else if ( $wrapper.children( '.' + param_settings.param_name ).is( 'img' ) ) {
						var $img;

						$img = $wrapper.children( '[name=' + param_settings.param_name + ']' );
						if ( value && value.match( /^\d+$/ ) ) {
							$.ajax( {
								type: 'POST',
								url: window.ajaxurl,
								data: {
									action: 'wpb_single_image_src',
									content: value,
									size: 'thumbnail',
									_vcnonce: window.vcAdminNonce
								},
								dataType: 'html',
								context: this
							} ).done( function ( url ) {
								$img.attr( 'src', url );
							} );
						} else if ( value ) {
							$img.attr( 'src', value );
						}
					} else {
						$wrapper.children( '[name=' + param_settings.param_name + ']' ).html( value ? value : '' );
					}
					if ( $admin_label.length ) {
						var inverted_value;

						if ( '' === value || _.isUndefined( value ) ) {
							$admin_label.hide().addClass( 'hidden-label' );
						} else {
							if ( _.isObject( param_settings.value ) && ! _.isArray( param_settings.value ) && 'checkbox' === param_settings.type ) {
								inverted_value = _.invert( param_settings.value );
								label_value = _.map( value.split( /[\s]*\,[\s]*/ ), function ( val ) {
									return _.isString( inverted_value[ val ] ) ? inverted_value[ val ] : val;
								} ).join( ', ' );
							} else if ( _.isObject( param_settings.value ) && ! _.isArray( param_settings.value ) ) {
								inverted_value = _.invert( param_settings.value );
								label_value = _.isString( inverted_value[ value ] ) ? inverted_value[ value ] : value;
							}
							$admin_label.html( '<label>' + $admin_label.find( 'label' ).text() + '</label>: ' + label_value );
							$admin_label.show().removeClass( 'hidden-label' );
						}
					}
				}, this );
			}
			view = vc.app.views[ model.get( 'parent_id' ) ];
			if ( false !== model.get( 'parent_id' ) && _.isObject( view ) ) {
				view.checkIsEmpty();
			}
		},
		changeShortcodeParent: function ( model ) {
			if ( false === this.model.get( 'parent_id' ) ) {
				return model;
			}
			var $parent_view = $( '[data-model-id=' + this.model.get( 'parent_id' ) + ']' ),
				view = vc.app.views[ this.model.get( 'parent_id' ) ];
			this.$el.appendTo( $parent_view.find( '> .wpb_element_wrapper > .wpb_column_container,'
				+ ' > .vc_element-wrapper > .wpb_column_container' ) );
			view.checkIsEmpty();
		},
		// }}
		// Event Actions {{
		deleteShortcode: function ( e ) {
			if ( _.isObject( e ) ) {
				e.preventDefault();
			}
			var answer = confirm( i18n.press_ok_to_delete_section );
			if ( true === answer ) {
				this.model.destroy();
			}
		},
		addElement: function ( e ) {
			_.isObject( e ) && e.preventDefault();
			vc.add_element_block_view.render( this.model,
				! _.isObject( e ) || ! $( e.currentTarget ).closest( '.bottom-controls' ).hasClass( 'bottom-controls' ) );
		},
		editElement: function ( e ) {
			if ( _.isObject( e ) ) {
				e.preventDefault();
			}
			if ( ! vc.active_panel || ! vc.active_panel.model || ! this.model || ( vc.active_panel.model && this.model && vc.active_panel.model.get( 'id' ) != this.model.get( 'id' ) ) ) {
				vc.closeActivePanel();
				vc.edit_element_block_view.render( this.model );
			}
		},
		clone: function ( e ) {
			if ( _.isObject( e ) ) {
				e.preventDefault();
			}
			vc.clone_index /= 10;
			return this.cloneModel( this.model, this.model.get( 'parent_id' ) );
		},
		cloneModel: function ( model, parent_id, save_order ) {
			var new_order,
				model_clone,
				params,
				tag;

			new_order = _.isBoolean( save_order ) && true === save_order ? model.get( 'order' ) : parseFloat( model.get( 'order' ) ) + vc.clone_index;
			params = _.extend( {}, model.get( 'params' ) );
			tag = model.get( 'shortcode' );

			model_clone = Shortcodes.create( {
				shortcode: tag,
				id: window.vc_guid(),
				parent_id: parent_id,
				order: new_order,
				cloned: true,
				cloned_from: model.toJSON(),
				params: params
			} );

			_.each( Shortcodes.where( { parent_id: model.id } ), function ( shortcode ) {
				this.cloneModel( shortcode, model_clone.get( 'id' ), true );
			}, this );
			return model_clone;
		}
	} );

	var VisualComposer = vc.visualComposerView = Backbone.View.extend( {
		el: $( '#wpb_visual_composer' ),
		views: {},
		disableFixedNav: false,
		events: {
			"click #wpb-add-new-row": 'createRow',
			'click #vc_post-settings-button': 'editSettings',
			'click #vc_add-new-element, [data-vc-element="add-element-action"]': 'addElement',
			'click [data-vc-element="add-text-block-action"]': 'addTextBlock',
			'click .wpb_switch-to-composer': 'switchComposer',
			'click #vc_templates-editor-button': 'openTemplatesWindow',
			'click #vc_templates-more-layouts': 'openTemplatesWindow',
			'click .vc_template[data-template_unique_id] > .wpb_wrapper': 'loadDefaultTemplate',
			'click #wpb-save-post': 'save',
			'click .vc_control-preview': 'preview'
		},
		initializeAccessPolicy: function () {
			this.accessPolicy = {
				be_editor: vc_user_access().editor( 'backend_editor' ),
				fe_editor: vc_frontend_enabled && vc_user_access().editor( 'frontend_editor' ),
				classic_editor: ! vc_user_access().check( 'backend_editor', 'disabled_ce_editor', undefined, true )
			};
		},
		accessPolicyActions: function () {
			var front = '', back = '';

			if ( this.accessPolicy.fe_editor ) {
				front = '<span class="vc_spacer"></span><a class="wpb_switch-to-front-composer" href="' + $( '#wpb-edit-inline' ).attr( 'href' ) + '">' + window.i18nLocale.main_button_title_frontend_editor + '</a>';
			}

			if ( this.accessPolicy.classic_editor ) {
				if ( this.accessPolicy.be_editor ) {
					back = '<span class="vc_spacer"></span><a class="wpb_switch-to-composer" href="#">' + window.i18nLocale.main_button_title_backend_editor + '</a>';
				}
			} else {
				$( '#postdivrich' ).hide();
				if ( this.accessPolicy.be_editor ) {
					var _this = this;

					_.defer( function () {
						_this.show();
						_this.status = 'shown';
					} );
				}
			}

			if ( front || back ) {
				this.$buttonsContainer = $( '<div class="composer-switch"><span class="logo-icon"></span>' + back + front + '</div>' ).insertAfter( 'div#titlediv' );
				if ( this.accessPolicy.classic_editor ) {
					this.$switchButton = this.$buttonsContainer.find( '.wpb_switch-to-composer' );
					this.$switchButton.click( this.switchComposer );
				}
			}
		},
		initialize: function () {
			_.bindAll( this,
				'switchComposer',
				'dropButton',
				'processScroll',
				'updateRowsSorting',
				'updateElementsSorting' );
			this.initializeAccessPolicy();
			this.accessPolicyActions();
			if ( ! this.accessPolicy.be_editor && ! this.accessPolicy.fe_editor ) {
				return false;
			}
			this.buildRelevance();
			vc.events.on( 'shortcodes:add', vcAddShortcodeDefaultParams, this );
			vc.events.on( 'shortcodes:add', vc.atts.addShortcodeIdParam, this ); // update vc_grid_id on shortcode adding
			vc.events.on( 'shortcodes:add', this.addShortcode, this );
			vc.events.on( 'shortcodes:destroy', this.checkEmpty, this );
			Shortcodes.on( 'change:params', this.changeParamsEvents, this );
			Shortcodes.on( 'reset', this.addAll, this );
			this.render();
		},
		changeParamsEvents: function ( model ) {
			vc.events.triggerShortcodeEvents( 'update', model );
		},
		render: function () {
			// Find required elemnts of the view.
			this.$vcStatus = $( '#wpb_vc_js_status' );
			this.$metablock_content = $( '.metabox-composer-content' );
			this.$content = $( "#visual_composer_content" );
			this.$post = $( '#postdivrich' );
			this.$loading_block = $( '#vc_logo' );

			vc.add_element_block_view = new vc.AddElementUIPanelBackendEditor( { el: '#vc_ui-panel-add-element' } );
			vc.edit_element_block_view = new vc.EditElementUIPanel( { el: '#vc_ui-panel-edit-element' } );

			vc.templates_panel_view = new vc.TemplateWindowUIPanelBackendEditor( { el: '#vc_ui-panel-templates' } );
			vc.post_settings_view = new vc.PostSettingsUIPanelBackendEditor( { el: '#vc_ui-panel-post-settings' } );
			this.setSortable();
			this.setDraggable();
			vc.is_mobile = 0 < $( 'body.mobile' ).length;
			vc.saved_custom_css = $( '#wpb_custom_post_css_field' ).val();
			vc.updateSettingsBadge();
			/**
			 * @since 4.5
			 */
			_.defer( function () {
				vc.events.trigger( 'app.render' );
			} );
			return this;
		},
		addAll: function () {
			this.views = {};
			this.$content.removeClass( 'loading' ).empty();
			this.addChild( false );
			this.checkEmpty();
			this.$loading_block.removeClass( 'vc_ajax-loading' );
			this.$metablock_content.removeClass( 'vc_loading-shortcodes' );
			_.defer( function () {
				vc.events.trigger( 'app.addAll' );
			} );
		},
		addChild: function ( parent_id ) {
			_.each( vc.shortcodes.where( { parent_id: parent_id } ), function ( shortcode ) {
				this.appendShortcode( shortcode );
				this.setSortable();
				this.addChild( shortcode.get( 'id' ) );
			}, this );
		},
		getView: function ( model ) {
			var view;
			if ( _.isObject( vc.map[ model.get( 'shortcode' ) ] ) && _.isString( vc.map[ model.get( 'shortcode' ) ].js_view ) && vc.map[ model.get( 'shortcode' ) ].js_view.length && ! _.isUndefined( window[ window.vc.map[ model.get( 'shortcode' ) ].js_view ] ) ) {
				view = new window[ window.vc.map[ model.get( 'shortcode' ) ].js_view ]( { model: model } );
			} else {
				view = new ShortcodeView( { model: model } );
			}
			model.set( { view: view } );
			return view;
		},
		setDraggable: function () {
			$( '#wpb-add-new-element, #wpb-add-new-row' ).draggable( {
				helper: function () {
					return $( '<div id="drag_placeholder"></div>' ).appendTo( 'body' );
				},
				zIndex: 99999,
				// cursorAt: { left: 10, top : 20 },
				cursor: "move",
				// appendTo: "body",
				revert: "invalid",
				start: function ( event, ui ) {
					$( "#drag_placeholder" ).addClass( "column_placeholder" ).html( window.i18nLocale.drag_drop_me_in_column );
				}
			} );
			this.$content.droppable( {
				greedy: true,
				accept: ".dropable_el,.dropable_row",
				hoverClass: "wpb_ui-state-active",
				drop: this.dropButton
			} );
		},
		dropButton: function ( event, ui ) {
			if ( ui.draggable.is( '#wpb-add-new-element' ) ) {
				this.addElement();
			} else if ( ui.draggable.is( '#wpb-add-new-row' ) ) {
				this.createRow();
			}
		},
		appendShortcode: function ( model ) {
			var view, parentModelView, params;
			view = this.getView( model );
			params = _.extend( vc.getDefaults( model.get( 'shortcode' ) ), model.get( 'params' ) );
			model.set( 'params', params, { silent: true } );
			parentModelView = false !== model.get( 'parent_id' ) ?
				this.views[ model.get( 'parent_id' ) ] : false;
			this.views[ model.id ] = view;
			if ( model.get( 'parent_id' ) ) {
				var parentView;
				parentView = this.views[ model.get( 'parent_id' ) ];
				parentView.unsetEmpty();
			}
			if ( parentModelView ) {
				parentModelView.addShortcode( view, 'append' );
			} else {
				this.$content.append( view.render().el );
			}
			view.ready();
			view.changeShortcodeParams( model ); // Refactor
			view.checkIsEmpty();
			this.setNotEmpty();
		},
		addShortcode: function ( model ) {
			var view, parentModelView, params;
			params = _.extend( vc.getDefaults( model.get( 'shortcode' ) ), model.get( 'params' ) );
			model.set( 'params', params, { silent: true } );
			view = this.getView( model );
			parentModelView = false !== model.get( 'parent_id' ) ?
				this.views[ model.get( 'parent_id' ) ] : false;
			view.use_default_content = true !== model.get( 'cloned' );
			this.views[ model.id ] = view;
			if ( parentModelView ) {
				parentModelView.addShortcode( view );
				parentModelView.checkIsEmpty();
				var self;
				self = this;
				_.defer( function () {
					view.changeShortcodeParams && view.changeShortcodeParams( model );
					view.ready();
					self.setSortable();
					self.setNotEmpty();
				} );

			} else {
				this.addRow( view );
				_.defer( function () {
					view.changeShortcodeParams && view.changeShortcodeParams( model );
				} );
			}
		},
		addRow: function ( view ) {
			var before_shortcode;
			before_shortcode = _.last( vc.shortcodes.filter( function ( shortcode ) {
				return false === shortcode.get( 'parent_id' ) && parseFloat( shortcode.get( 'order' ) ) < parseFloat( this.get( 'order' ) );
			}, view.model ) );
			if ( before_shortcode ) {
				view.render().$el.insertAfter( '[data-model-id=' + before_shortcode.id + ']' );
			} else {
				this.$content.append( view.render().el );
			}
		},
		addTextBlock: function ( e ) {
			var row, column, params;

			e.preventDefault();

			row = Shortcodes.create( {
				shortcode: 'vc_row'
			} );

			column = Shortcodes.create( {
				shortcode: 'vc_column',
				params: { width: '1/1' },
				parent_id: row.id,
				root_id: row.id
			} );

			params = vc.getDefaults( 'vc_column_text' );
			if ( 'undefined' !== typeof(window.vc_settings_presets[ 'vc_column_text' ]) ) {
				params = _.extend( params, window.vc_settings_presets[ 'vc_column_text' ] );
			}

			return Shortcodes.create( {
				shortcode: 'vc_column_text',
				parent_id: column.id,
				root_id: row.id,
				params: params
			} );
		},
		/**
		 * Create row
		 */
		createRow: function () {
			var row = Shortcodes.create( { shortcode: 'vc_row' } );
			Shortcodes.create( {
				shortcode: 'vc_column',
				params: { width: '1/1' },
				parent_id: row.id,
				root_id: row.id
			} );
			return row;
		},
		/**
		 * Add Element with a help of modal view.
		 */
		addElement: function ( e ) {
			_.isObject( e ) && e.preventDefault();
			vc.add_element_block_view.render( false );
		},
		openTemplatesWindow: function ( e ) {
			e && e.preventDefault();
			if ( $( e.currentTarget ).is( '#vc_templates-more-layouts' ) ) {
				vc.templates_panel_view.once( 'show', function () {
					$( '[data-vc-ui-element-target="[data-tab=default_templates]"]' ).click();
				} );
			}
			vc.templates_panel_view.render().show();
		},
		loadDefaultTemplate: function ( e ) {
			e && e.preventDefault();
			vc.templates_panel_view.loadTemplate( e );
		},
		editSettings: function ( e ) {
			e && e.preventDefault();
			vc.post_settings_view.render().show();
		},
		sortingStarted: function ( event, ui ) {
			$( '#visual_composer_content' ).addClass( 'vc_sorting-started' );
		},
		sortingStopped: function ( event, ui ) {
			$( '#visual_composer_content' ).removeClass( 'vc_sorting-started' );
		},
		updateElementsSorting: function ( event, ui ) {
			_.defer( function ( app, event, ui ) {
				var $current_container = ui.item.parent().closest( '[data-model-id]' ),
					parent = $current_container.data( 'model' ),
					model = ui.item.data( 'model' ),
					models = app.views[ parent.id ].$content.find( '> [data-model-id]' ),
					i = 0;
				// Change parent if block moved to another container.
				if ( ! _.isNull( ui.sender ) ) {
					var old_parent_id = model.get( 'parent_id' );
					store.lock();
					model.save( { parent_id: parent.id } );
					app.views[ old_parent_id ].checkIsEmpty();
					app.views[ parent.id ].checkIsEmpty();
				}
				models.each( function () {
					var shortcode = $( this ).data( 'model' );
					store.lock();
					shortcode.save( { 'order': i ++ } );
				} );
				model.save();
			}, this, event, ui );

		},
		updateRowsSorting: function () {
			_.defer( function ( app ) {
				var $rows = app.$content.find( app.rowSortableSelector );
				$rows.each( function () {
					var index = $( this ).index();
					if ( $rows.length - 1 > index ) {
						store.lock();
					}
					$( this ).data( 'model' ).save( { 'order': index } );
				} );
			}, this );
		},
		renderPlaceholder: function ( event, element ) {
			var tag = $( element ).data( 'element_type' );
			var is_container = _.isObject( vc.map[ tag ] ) && ( ( _.isBoolean( vc.map[ tag ].is_container ) && true === vc.map[ tag ].is_container ) || ! _.isEmpty( vc.map[ tag ].as_parent ) );
			var $helper = $( '<div class="vc_helper vc_helper-' + tag + '"><i class="vc_general vc_element-icon' +
				( vc.map[ tag ].icon ? ' ' + vc.map[ tag ].icon : '' ) +
				'"' +
				( is_container ? ' data-is-container="true"' : '' ) +
				'></i> ' + vc.map[ tag ].name + '</div>' ).prependTo( 'body' );
			return $helper;
		},
		rowSortableSelector: "> .wpb_vc_row",
		setSortable: function () {
			// 1st level sorting (rows). work also in wp41.
			$( '.wpb_main_sortable' ).sortable( {
				forcePlaceholderSize: true,
				placeholder: "widgets-placeholder",
				cursor: "move",
				items: this.rowSortableSelector, // wpb_sortablee
				handle: '.column_move',
				cancel: '.vc-non-draggable-row',
				distance: 0.5,
				start: this.sortingStarted,
				stop: this.sortingStopped,
				update: this.updateRowsSorting,
				over: function ( event, ui ) {
					ui.placeholder.css( { maxWidth: ui.placeholder.parent().width() } );
				}
			} );
			// 2st level sorting (elements).
			$( '.wpb_column_container' ).sortable( {
				forcePlaceholderSize: true,
				forceHelperSize: false,
				connectWith: ".wpb_column_container",
				placeholder: "vc_placeholder",
				items: "> div.wpb_sortable,> div.vc-non-draggable", //wpb_sortablee
				helper: this.renderPlaceholder,
				distance: 3,
				cancel: '.vc-non-draggable',
				scroll: true,
				scrollSensitivity: 70,
				cursor: 'move',
				cursorAt: { top: 20, left: 16 },
				tolerance: 'intersect', // this helps with dragging textblock into tabs
				start: function () {
					$( '#visual_composer_content' ).addClass( 'vc_sorting-started' );
					$( '.vc_not_inner_content' ).addClass( 'dragging_in' );
				},
				stop: function ( event, ui ) {
					$( '#visual_composer_content' ).removeClass( 'vc_sorting-started' );
					$( '.dragging_in' ).removeClass( 'dragging_in' );
					var tag = ui.item.data( 'element_type' ),
						parent_tag = ui.item.parent().closest( '[data-element_type]' ).data( 'element_type' ),
						allowed_container_element = ! _.isUndefined( vc.map[ parent_tag ].allowed_container_element ) ? vc.map[ parent_tag ].allowed_container_element : true;
					if ( ! vc.check_relevance( parent_tag, tag ) ) {
						$( this ).sortable( 'cancel' );
					}
					var is_container = _.isObject( vc.map[ tag ] ) && ( ( _.isBoolean( vc.map[ tag ].is_container ) && true === vc.map[ tag ].is_container ) || ! _.isEmpty( vc.map[ tag ].as_parent ) );
					if ( is_container && ! (true === allowed_container_element || allowed_container_element === ui.item.data( 'element_type' ).replace( /_inner$/,
							'' )) ) {
						$( this ).sortable( 'cancel' );
					}
					$( '.vc_sorting-empty-container' ).removeClass( 'vc_sorting-empty-container' );
				},
				update: this.updateElementsSorting,
				over: function ( event, ui ) {
					var tag = ui.item.data( 'element_type' ),
						parent_tag = ui.placeholder.closest( '[data-element_type]' ).data( 'element_type' ),
						allowed_container_element = ! _.isUndefined( vc.map[ parent_tag ].allowed_container_element ) ? vc.map[ parent_tag ].allowed_container_element : true;
					if ( ! vc.check_relevance( parent_tag, tag ) ) {
						ui.placeholder.addClass( 'vc_hidden-placeholder' );
						return false;
					}
					var is_container = _.isObject( vc.map[ tag ] ) && ( ( _.isBoolean( vc.map[ tag ].is_container ) && true === vc.map[ tag ].is_container ) || ! _.isEmpty( vc.map[ tag ].as_parent ) );
					if ( is_container && ! (true === allowed_container_element || allowed_container_element === ui.item.data( 'element_type' ).replace( /_inner$/,
							'' )) ) {
						ui.placeholder.addClass( 'vc_hidden-placeholder' );
						return false;
					}
					if ( ! _.isNull( ui.sender ) && ui.sender.length && ! ui.sender.find( '[data-element_type]:visible' ).length ) {
						ui.sender.addClass( 'vc_sorting-empty-container' );
					}
					ui.placeholder.removeClass( 'vc_hidden-placeholder' );
					ui.placeholder.css( { maxWidth: ui.placeholder.parent().width() } );
				}
			} );
			$( '.wpb_column_container' ).disableSelection();
			return this;
		},
		setNotEmpty: function () {
			$( '#vc_no-content-helper' ).addClass( 'vc_not-empty' );
		},
		setIsEmpty: function () {
			$( '#vc_no-content-helper' ).removeClass( 'vc_not-empty' )
		},
		checkEmpty: function ( model ) {
			if ( _.isObject( model ) && false !== model.get( 'parent_id' ) && model.get( 'parent_id' ) != model.id ) {
				var parent_view = this.views[ model.get( 'parent_id' ) ];
				parent_view.checkIsEmpty();
			}
			if ( 0 === Shortcodes.length ) {
				this.setIsEmpty();
			} else {
				this.setNotEmpty();
			}
		},
		switchComposer: function ( e ) {
			// @todo need to remove it separate js view and all logic should be removed from be editor.
			if ( _.isObject( e ) ) {
				e.preventDefault();
			}
			if ( ! this.accessPolicy.be_editor ) {
				return false;
			}
			if ( 'shown' === this.status ) {
				if ( this.accessPolicy.classic_editor ) {
					! _.isUndefined( this.$switchButton ) && this.$switchButton.text( window.i18nLocale.main_button_title_backend_editor );
					! _.isUndefined( this.$buttonsContainer ) && this.$buttonsContainer.removeClass( 'vc_backend-status' );
				}
				this.close();
				this.status = 'closed';
			} else {
				if ( this.accessPolicy.classic_editor ) {
					! _.isUndefined( this.$switchButton ) && this.$switchButton.text( window.i18nLocale.main_button_title_revert );
					! _.isUndefined( this.$buttonsContainer ) && this.$buttonsContainer.addClass( 'vc_backend-status' );
				}
				this.show();
				this.status = 'shown';
			}
		},
		show: function () {
			this.$el.show();
			this.$post.hide();
			this.$vcStatus.val( "true" );
			this.navOnScroll();
			if ( vc.storage.isContentChanged() ) {
				vc.app.setLoading();
				vc.app.views = {};
				// @todo 4.5 why setTimeout not defer?
				window.setTimeout( function () {
					Shortcodes.fetch( { reset: true } );
					vc.events.trigger( 'backendEditor.show' );
				}, 100 );
			}
		},
		setLoading: function () {
			this.setNotEmpty();
			this.$loading_block.addClass( 'vc_ajax-loading' );
			this.$metablock_content.addClass( 'vc_loading-shortcodes' );
		},
		close: function () {
			this.$vcStatus.val( "false" );
			this.$el.hide();
			if ( _.isObject( window.editorExpand ) ) {
				_.defer( function () {
					window.editorExpand.on();
					window.editorExpand.on(); // double call fixes "space" in height
				} );
			}
			this.$post.show();
			_.defer( function () {
				vc.events.trigger( 'backendEditor.close' );
			} );
		},
		checkVcStatus: function () {
			if ( this.accessPolicy.be_editor && ( ! this.accessPolicy.classic_editor || 'true' === this.$vcStatus.val() ) ) {
				this.switchComposer();
			}
		},
		setNavTop: function () {
			this.navTop = this.$nav.length && this.$nav.offset().top - 28;
		},
		save: function () {
			$( '#wpb-save-post' ).text( window.i18nLocale.loading );
			$( '#publish' ).click();
		},
		preview: function () {
			$( '#post-preview' ).click();
		},
		navOnScroll: function () {
			var $win = $( window );
			this.$nav = $( '#vc_navbar' );
			this.setNavTop();
			this.processScroll();
			$win.unbind( 'scroll.composer' ).on( 'scroll.composer', this.processScroll );
		},
		processScroll: function ( e ) {
			if ( true === this.disableFixedNav ) {
				this.$nav.removeClass( 'vc_subnav-fixed' );
				return;
			}
			if ( ! this.navTop || 0 > this.navTop ) {
				this.setNavTop();
			}
			this.scrollTop = $( window ).scrollTop() + 80;
			if ( 0 < this.navTop && this.scrollTop >= this.navTop && ! this.isFixed ) {
				this.isFixed = 1;
				this.$nav.addClass( 'vc_subnav-fixed' );
			} else if ( this.scrollTop <= this.navTop && this.isFixed ) {
				this.isFixed = 0;
				this.$nav.removeClass( 'vc_subnav-fixed' );
			}
		},
		buildRelevance: function () {
			vc.shortcode_relevance = {};
			_.map( vc.map, function ( object ) {
				if ( _.isObject( object.as_parent ) && _.isString( object.as_parent.only ) ) {
					vc.shortcode_relevance[ 'parent_only_' + object.base ] = object.as_parent.only.replace( /\s/,
						'' ).split( ',' );
				}
				if ( _.isObject( object.as_parent ) && _.isString( object.as_parent.except ) ) {
					vc.shortcode_relevance[ 'parent_except_' + object.base ] = object.as_parent.except.replace( /\s/,
						'' ).split( ',' );
				}
				if ( _.isObject( object.as_child ) && _.isString( object.as_child.only ) ) {
					vc.shortcode_relevance[ 'child_only_' + object.base ] = object.as_child.only.replace( /\s/,
						'' ).split( ',' );
				}
				if ( _.isObject( object.as_child ) && _.isString( object.as_child.except ) ) {
					vc.shortcode_relevance[ 'child_except_' + object.base ] = object.as_child.except.replace( /\s/,
						'' ).split( ',' );
				}
			} );
			/**
			 * Check parent/children relationship between two tags
			 * @param tag
			 * @param related_tag
			 * @return boolean - Returns true if relevance is positive
			 */
			vc.check_relevance = function ( tag, related_tag ) {
				if ( _.isArray( vc.shortcode_relevance[ 'parent_only_' + tag ] ) && ! _.contains( vc.shortcode_relevance[ 'parent_only_' + tag ],
						related_tag ) ) {
					return false;
				}
				if ( _.isArray( vc.shortcode_relevance[ 'parent_except_' + tag ] ) && _.contains( vc.shortcode_relevance[ 'parent_except_' + tag ],
						related_tag ) ) {
					return false;
				}
				if ( _.isArray( vc.shortcode_relevance[ 'child_only_' + related_tag ] ) && ! _.contains( vc.shortcode_relevance[ 'child_only_' + related_tag ],
						tag ) ) {
					return false;
				}
				if ( _.isArray( vc.shortcode_relevance[ 'child_except_' + related_tag ] ) && _.contains( vc.shortcode_relevance[ 'child_except' + related_tag ],
						tag ) ) {
					return false;
				}
				return true;
			};
		}
	} );
	$( function () {
		if ( $( '#wpb_visual_composer' ).is( 'div' ) ) {
			var app = vc.app = new VisualComposer();
			if ( app.accessPolicy.be_editor ) {
				'no' !== app.accessPolicy && vc.app.checkVcStatus();
			} else {
				app.$el.remove();
			}
		}
	} );
	/**
	 * Called when initial content rendered or when content changed in tinymce
	 */
	Shortcodes.on( 'sync', function ( collection ) {
		if ( _.isObject( collection ) && ! _.isEmpty( collection.models ) ) {
			_.each( collection.models, function ( model ) {
				vc.events.triggerShortcodeEvents( 'sync', model );
			} );
		}
	} );
	/**
	 * Called when shortcode created
	 */
	Shortcodes.on( 'add', function ( model ) {
		if ( _.isObject( model ) ) {
			vc.events.triggerShortcodeEvents( 'add', model );
		}
	} );
})( window.jQuery );
