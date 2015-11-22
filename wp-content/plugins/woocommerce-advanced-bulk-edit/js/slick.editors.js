/***
 * Contains basic SlickGrid editors.
 * @module Editors
 * @namespace Slick
 */

(function ($) {
  // register namespace
  $.extend(true, window, {
    "Slick": {
      "Editors": {
        "Text": TextEditor,
        "Integer": IntegerEditor,
        "Date": DateEditor,
        "YesNoSelect": YesNoSelectEditor,
		"CustomDate": customDateEditor,
        "Checkbox": CheckboxEditor,
        "PercentComplete": PercentCompleteEditor,
        "LongText": LongTextEditor,
		"TextArea": TextAreaEditor,
		"Image": ImageEditor,
		"Gallery": ImageGalleryEditor,
		"Select": SelectCellEditor,
		"Category": CategoryEditor,
		"DefAttrs": DefAttrsEditor
      }
    }
  });
  
   function DefAttrsEditor(args) {
    var $input, $wrapper;
    var defaultValue;
    var scope = this;
	var newvalue = "";
    this.init = function () {
      var $container = $("body");
	  
      $wrapper = $("<DIV style='z-index:10000;position:absolute;background:white;padding:25px;padding-top:12px;padding-bottom:12px;border:3px solid gray; -moz-border-radius:10px; border-radius:10px;min-width:150px;max-width:300px;overflow:auto;'/>")
          .appendTo($container);

      $input = $("<div style='max-height:350px;min-height:100px;overflow:auto;' class='editorcats'></div>")
          .appendTo($wrapper);
      
	        
	 
	  $("<DIV style='text-align:right'><BUTTON>Save</BUTTON><BUTTON>Cancel</BUTTON></DIV>")
          .appendTo($wrapper);
      $wrapper.find("button:first").bind("click", this.save);
      $wrapper.find("button:last").bind("click", this.cancel);
      $input.bind("keydown", this.handleKeyDown);
	   scope.position(args.position);
	  var W3Ex = window.W3Ex || {};
	  var selitem = args.item;
	  var addsel = "";
	  for (var prop in selitem) {
		  if (selitem.hasOwnProperty(prop)) 
		  {
//		  	   addsel = "";
		  	  if(prop.indexOf('attribute_pa_') === 0 && prop.indexOf('_ids') === -1 && prop.indexOf('_visiblefp') === -1)
			  {
			  	 var attr_slug = prop.replace('attribute_pa_','');
				 addsel+= '<tr><td>'+attr_slug+'</td><td><select data-slug="pa_'+attr_slug+'"><option value="" selected>No default</option>';
				 if(W3Ex.attributes !== undefined)
				 {
				 	for(var j=0; j<W3Ex.attributes.length;j++)
					{
						var attr = W3Ex.attributes[j];
						if(attr === undefined) continue;
						if(attr.attr === attr_slug)
						{
							if(selitem[prop + '_ids'] !== undefined)
							{
								var attr_ids = selitem[prop + '_ids'];
								var ids_arr = attr_ids.split(',');
								for(var g=0; g < ids_arr.length; g++)
								{
									if(attr.id == ids_arr[g])
									{
										addsel+= '<option value="'+attr.value+'">'+attr.name+'</option>';
										break;
									}
								}
							}
						}
					}
				 }
				 addsel+= "</select></td></tr>";
			  }
			 
		  }
	}
	 if(addsel !== "")
	  {
		addsel = '<table id="defattrsholder">' + addsel + '</table>';
	  	$input.append(addsel);
	  }
	/*  var cols = {};
			cols = W3Ex.attr_cols;
			for (var key in cols) {
			  if (cols.hasOwnProperty(key)) {
			  	   var customobj = cols[key];*/
	  if(args.item[args.column.field] !== undefined)
	  {
	  	  var attrs = args.item[args.column.field].split(';');
		  if(attrs instanceof Array)
		  {
		  	  for(var i=0; i < attrs.length; i++)
			  {
			  	 var arr_item =  attrs[i];
				 arr_item = $.trim(arr_item);
			  	 var cur_attr = arr_item.split(',');
				 if(cur_attr instanceof Array && cur_attr.length == 2)
				 {
//				 	alert($('#defattrsholder select[data-slug="'+cur_attr[0]+'"]').length);
				 	$('#defattrsholder select[data-slug="'+cur_attr[0]+'"]').val(cur_attr[1]);
				 }
			  }
		  }
		 
//		return;
	  }
     
//      $input.focus().select();
    };

    this.handleKeyDown = function (e) {
      if (e.which == $.ui.keyCode.ENTER && e.ctrlKey) {
        scope.save();
      } else if (e.which == $.ui.keyCode.ESCAPE) {
        e.preventDefault();
        scope.cancel();
      } else if (e.which == $.ui.keyCode.TAB && e.shiftKey) {
        e.preventDefault();
        args.grid.navigatePrev();
      } else if (e.which == $.ui.keyCode.TAB) {
        e.preventDefault();
        args.grid.navigateNext();
      }
    };

    this.save = function () {
	  var W3Ex = window.W3Ex || {};
	  W3Ex.bdontgodown = true;
      args.commitChanges();
    };

    this.cancel = function () {
//      $input.val(defaultValue);
      args.cancelChanges();
    };

    this.hide = function () {
      $wrapper.hide();
    };

    this.show = function () {
      $wrapper.show();
    };

    this.position = function (position) {
      $wrapper
          .css("top", position.top - 85)
          .css("left", position.left - 75)
    };

    this.destroy = function () {
      $wrapper.remove();
	  var W3Ex = window.W3Ex || {};
	  W3Ex.invalidateselected = true;
    };

    this.focus = function () {
//      $input.focus();
    };

    this.loadValue = function (item) {
//      $input.val(defaultValue = item[args.column.field]);
    };

    this.serializeValue = function () {
      return '';
    };

    this.applyValue = function (item, state) {
	     item[args.column.field] = newvalue;
    };

    this.isValueChanged = function () {
	  $('#defattrsholder select').each(function(){
	  	 var attr_slug = $(this).attr('data-slug');
		 var attr_def = $(this).val();
		 if(attr_def !== "")
		 {
		 	if(newvalue === "")
			{
				newvalue = attr_slug +','+attr_def;
			}else
			{
				newvalue+= ' ;'+attr_slug +','+attr_def;
			}
		 }
	  })
   	  if(newvalue != args.item[args.column.field])
	  {
	  	  return true;
	  }
	  return false;
    };

    this.validate = function () {
      return {
        valid: true,
        msg: null
      };
    };

    this.init();
  }
  
   function CategoryEditor(args) {
    var $input, $wrapper;
    var defaultValue;
    var scope = this;
	var catnames = "";
	var sel_ids = [];
	var ischecked = false;
	var oldvalue = 0;
	var bonlyvisible = false;
    this.init = function () {
      var $container = $("body");
	  
      $wrapper = $("<DIV style='z-index:10000;position:absolute;background:white;padding:25px;padding-top:12px;padding-bottom:12px;border:3px solid gray; -moz-border-radius:10px; border-radius:10px;min-width:150px;'/>")
          .appendTo($container);

      $input = $("<div style='max-height:350px;overflow:auto;' class='editorcats'></div>")
          .appendTo($wrapper);
	  $input.html($('#categoriesdialog .' + args.column.field).html());
      
	  if(args.column.field.indexOf("attribute_pa_") !== -1)
	  {
	  	 if(args.item['post_type'] == 'product_variation')
	  		$('.editorcats .categorychecklist').addClass('clearothersattr');
		 else
		 {
		 	if(args.item[args.column.field + '_ids'] === undefined)
//				args.item[args.column.field  + '_ids'] = "";
//			if(args.item[args.column.field  + '_ids'] === "")
			{
				if(args.item[args.column.field + '_visiblefp'] !== undefined)
				  delete args.item[args.column.field + '_visiblefp'];
			}
		 	if(args.item[args.column.field + '_visiblefp'] === undefined)
				args.item[args.column.field + '_visiblefp'] = 0;
		 	oldvalue = parseInt(args.item[args.column.field + '_visiblefp']);
//		 	if(args.item[args.column.field + '_visiblefp'] !== undefined)
	  		{
				if(args.item[args.column.field + '_visiblefp'] & 1)
				{
					ischecked = true;
					$('<hr><label style="white-space: nowrap;"><input id="ifvisibleproductpage" type="checkbox" checked=checked>Visible on the product<br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; page</label><hr>').appendTo($wrapper);
				}else
				{
					ischecked = false;
					$('<hr><label style="white-space: nowrap;"><input id="ifvisibleproductpage" type="checkbox">Visible on the product<br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; page</label><hr>').appendTo($wrapper);
				}
				if(args.item['product_type'] === 'variable')
				{
					if(args.item[args.column.field + '_visiblefp'] & 2)
					{
						ischecked = true;
						$('<label style="white-space: nowrap;"><input id="ifusedforvars" type="checkbox" checked=checked>Used for variations</label><hr>').appendTo($wrapper);
					}else
					{
						ischecked = false;
						$('<label style="white-space: nowrap;"><input id="ifusedforvars" type="checkbox">Used for variations</label><hr>').appendTo($wrapper);
					}
				}
				
			}
		 }
	  }
	  $("<DIV style='text-align:right'><BUTTON>Save</BUTTON><BUTTON>Cancel</BUTTON></DIV>")
          .appendTo($wrapper);
      $wrapper.find("button:first").bind("click", this.save);
      $wrapper.find("button:last").bind("click", this.cancel);
      $input.bind("keydown", this.handleKeyDown);
	  if(args.item[args.column.field + '_ids'] !== undefined)
	  {
	  	  var ids = args.item[args.column.field + '_ids'].split(',');
		  $('.editorcats input').each(function ()
			{
				var val = $(this).attr('value');
				if(ids.indexOf(val) >= 0)
				{
					$(this).prop('checked','checked');
				}
			})
//		return;
	  }
      scope.position(args.position);
//      $input.focus().select();
    };

    this.handleKeyDown = function (e) {
      if (e.which == $.ui.keyCode.ENTER && e.ctrlKey) {
        scope.save();
      } else if (e.which == $.ui.keyCode.ESCAPE) {
        e.preventDefault();
        scope.cancel();
      } else if (e.which == $.ui.keyCode.TAB && e.shiftKey) {
        e.preventDefault();
        args.grid.navigatePrev();
      } else if (e.which == $.ui.keyCode.TAB) {
        e.preventDefault();
        args.grid.navigateNext();
      }
    };

    this.save = function () {
	  var W3Ex = window.W3Ex || {};
	  W3Ex.bdontgodown = true;
      args.commitChanges();
    };

    this.cancel = function () {
//      $input.val(defaultValue);
      args.cancelChanges();
    };

    this.hide = function () {
      $wrapper.hide();
    };

    this.show = function () {
      $wrapper.show();
    };

    this.position = function (position) {
      $wrapper
          .css("top", position.top - 85)
          .css("left", position.left - 75)
    };

    this.destroy = function () {
      $wrapper.remove();
	  var W3Ex = window.W3Ex || {};
	  W3Ex.invalidateselected = true;
    };

    this.focus = function () {
//      $input.focus();
    };

    this.loadValue = function (item) {
//      $input.val(defaultValue = item[args.column.field]);
    };

    this.serializeValue = function () {
      return '';
    };

    this.applyValue = function (item, state) {
	if(!bonlyvisible)
	{
		item[args.column.field] = catnames;
	 	item[args.column.field + '_ids'] = sel_ids.join();
	}
     
	  if(args.item['post_type'] == 'product' &&  args.column.field.indexOf("attribute_pa_") !== -1 && $('#ifvisibleproductpage').length > 0)
	  {
	  	  if(args.item[args.column.field + '_visiblefp'] === undefined)
		  	 args.item[args.column.field + '_visiblefp'] = 0;
//		  oldvalue = parseInt(args.item[args.column.field + '_visiblefp']);
		  var newvalue = 0;
	  	  if($('#ifvisibleproductpage').is(':checked'))
		  {
		  	  newvalue|= 1;
		  }else
		  {
		  	   newvalue&= ~1;
		  }
		  if($('#ifusedforvars').length > 0)
		  {
		  	 if($('#ifusedforvars').is(':checked'))
			  {
			  	  newvalue|= 2;
			  }else
			  {
			  	  newvalue&= ~2;
			  }
		  }
		   args.item[args.column.field + '_visiblefp'] = newvalue;
	  }
    };

    this.isValueChanged = function () {
		
   		$('.editorcats input:checked').each(function ()
		{
			sel_ids.push($(this).attr('value'));
			var cattext = $(this).parent().text();
			cattext = $.trim(cattext);
			if(catnames === "")
			{
				catnames = cattext;
			}else
			{
				catnames = catnames + ', ' + cattext;
			}
			
		})
      if(args.item[args.column.field + '_ids'] !== undefined && args.item[args.column.field + '_ids'] !== "")
	  {
	  	  if(sel_ids.length == 0) return true;
	  	  var ids = args.item[args.column.field + '_ids'].split(',');
		  if(sel_ids.length !== ids.length) return true;
		  for(var i=0; i < ids.length; i++)
		  {
		  	  if(sel_ids.indexOf(ids[i]) === -1)
			  {
			  	   return true;
			  }
		  }
	  }else
	  {
	  	  if(sel_ids.length > 0) return true;
	  }
	  if(args.item['post_type'] == 'product' &&  args.column.field.indexOf("attribute_pa_") !== -1 && $('#ifvisibleproductpage').length > 0)
	  {
	  	  var newvalue = 0;
	  	  if($('#ifvisibleproductpage').is(':checked'))
		  {
		  	  newvalue|= 1;
		  }else
		  {
		  	   newvalue&= ~1;
		  }
		  if($('#ifusedforvars').is(':checked'))
		  {
		  	  newvalue|= 2;
		  }else
		  {
		  	  newvalue&= ~2;
		  }
	  	  if(newvalue !== oldvalue)
		  {
		  	 bonlyvisible = true;
		  	 return true;
		  }
	  }
	  return false;
    };

    this.validate = function () {
      return {
        valid: true,
        msg: null
      };
    };

    this.init();
  }
  
   function ImageGalleryEditor(args) {
    var $input, $wrapper,$ul,$aremove,$aset;
    var defaultValue;
    var scope = this;
	var file_frame;
	var set_to_post_id = -1;
	var attachment_id = -1;
	var bapplied = false;
	var bcandestroy = false;
	var W3Ex = window.W3Ex || {};
	 W3Ex.imageeditor = true;
    this.init = function () {
      var $container = $("body");
	 
	 
      $wrapper = $("<DIV style='z-index:100;position:absolute;background:white;padding:15px;padding-bottom:10px;border:3px solid gray; -moz-border-radius:5px; border-radius:5px;'/>")
          .appendTo($container);
	  $('<div id="w3exabe-note">Note! - save updates the database immediately.</div>')
	   .appendTo($wrapper);
      $input = $("<div class='galleryholder'></div>")
          .appendTo($wrapper);
	  $ul= $('<ul id="sortable"></ul>').appendTo($input);
	  $('<div style="clear:both;"></div>').appendTo($input);
	  $aset =  $("<a href='#' class='setimage'> Add product gallery images</a>")
          .appendTo($input);
	  
//	  $aremove = $("<a href='#' class='removeimage'>Remove product image</a>")
//          .appendTo($wrapper);
	   var data = args.grid.getData();
	  if(args.grid.getActiveCell() !== null)
	  {
	  	 var iRow = args.grid.getActiveCell().row;
		 var selectedRows = args.grid.getSelectedRows();
		 if(selectedRows.length > 0)
		 {
		 	for(var irow=0; irow < selectedRows.length; irow++)
			{
				var rowid = selectedRows[irow];
				if(rowid === undefined) continue;
				if(data[rowid] === undefined) continue;
				if(rowid === iRow)
				{//clicked on selected
					$('<br/><br/><label><input id="ifapplyaction" type="checkbox">Apply action to all<br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; selected products</label>').appendTo($wrapper);
					break;
				}
			}
		}
	  }
      $("<DIV style='text-align:right;position:relative;'><BUTTON>Save</BUTTON><BUTTON>Cancel</BUTTON></DIV>")
          .appendTo($wrapper);
	 
      $wrapper.find("button:first").bind("click", this.save);
	  $wrapper.find("button:last").bind("click", this.cancel);
//	  $aremove.bind("click", this.removeImage);
	  $aset.bind("click", this.setImage);
      $input.bind("keydown", this.handleKeyDown);
//	  $input.bind("click", this.handleClick);
      scope.position(args.position);
      $input.focus().select();
    };
	 
	 this.setImage = function (e) 
	 {
    	 if ( file_frame ) {
		// Set the post ID to what we want
			
			// Open frame
			file_frame.open();
			return;
		}
		 
		// Create the media frame.
		file_frame = wp.media.frames.file_frame = wp.media({
			title: 'Add images to product gallery',
			button: {
				text:  'Add images',
			},
			attachment:true,
			multiple: true // Set to true to allow multiple files to be selected
		});
		 
		// When an image is selected, run a callback.
		file_frame.on( 'select', function() {
			// We set multiple to false so only get one image from the uploader
			 var selection = file_frame.state().get('selection');
 
		    if (selection) {
		         // iterate through selected elements
			    selection.each(function(attachment) {
			        var url = attachment.attributes.url;
			         var insert = '<li class="ui-state-default" data-id="'+attachment.attributes.id+'"  data-image="'+ url +'" style="background:url('+ url +') top left no-repeat;background-size: 90px auto;"><img class="delete" src="' + W3Ex.imagepath + 'images/gallerydel.png"></li>';
					 $ul.append(insert);
			    });
			   }
 
   
			return;
			// Do something with attachment.id and/or attachment.url here
			// Restore the main post ID
//			alert('asd');
//			wp.media.model.settings.post.id = wp_media_post_id;
		});
		file_frame.on('open', function () {
			var attachment,
				selection = file_frame.state().get('selection');
			if(attachment_id !== -1)
			{
				attachment = wp.media.attachment(attachment_id);
				attachment.fetch();
				selection.add(attachment ? [ attachment ] : []);	
			}
		});
		
		// Finally, open the modal
		file_frame.open();
     };
	 
	 this.removeImage = function (e) 
	 { 
	     var prodids = "";
	 	 if($('#ifapplyaction').length > 0)
		 {
		 	if($('#ifapplyaction').is(':checked'))
			{
				var data = args.grid.getData();
				if(args.grid.getActiveCell() !== null)
				{
					var iRow = args.grid.getActiveCell().row;
					var selectedRows = args.grid.getSelectedRows();
					if(selectedRows.length > 0)
					{
						for(var irow=0; irow < selectedRows.length; irow++)
						{
							var rowid = selectedRows[irow];
							if(rowid === undefined) continue;
							if(data[rowid] === undefined) continue;
							data[rowid]._thumbnail_id = "";
							data[rowid]._thumbnail_id_val = "";
							if(prodids == "")
							{
								prodids = data[rowid].ID.toString() + ',';
							}else
							{
								prodids+= ',' + data[rowid].ID.toString();
							}
							
						}
					}
				}
			}
		 }
	 	
    	var ajaxarr = {};
		ajaxarr.action = 'wpmelon_adv_bulk_edit';
		ajaxarr.type = 'removethumb';
		ajaxarr.nonce = window.W3ExABE.nonce;
		var _arrData = [];
		if(prodids !== "")
		{
			_arrData[0] = prodids;
			bapplied = true;
		}
		else
			_arrData[0] = args.item.ID;
		ajaxarr.data = _arrData;
		$wrapper.append('<div class="showajax"></div>');
		$('.showajax').css({
			left:'200px',
			top:'100px'
		});
		jQuery.ajax({
		     type : "post",
		     dataType : "json",
		     url : W3ExABE.ajaxurl,
		     data : ajaxarr,
		     success: function(response) {
			 	$img.css("display","none");
				$aremove.css("display","none");
				$aset.css("display","inline");
				set_to_post_id = -1;
				attachment_id = -1;
				args.item._thumbnail_id = "";
				args.item._thumbnail_id_val = "";
		     },
			 complete:function (args)
			 {
			 	 $('.showajax').remove();
			 },
			 async : false
		  }) ;
     };
		
	 this.handleClick = function (e) {
     if ( file_frame ) {
		// Set the post ID to what we want
		if(set_to_post_id !== -1)
			file_frame.uploader.uploader.param( 'post_id', set_to_post_id );
			
			// Open frame
			file_frame.open();
			return;
		} else {
		// Set the wp.media post id so the uploader grabs the ID we want when initialised
			if(set_to_post_id !== -1)
				wp.media.model.settings.post.id = set_to_post_id;
		}
		 
		// Create the media frame.
		file_frame = wp.media.frames.file_frame = wp.media({
			title: 'Set product image',
			button: {
				text:  'Set product image',
			},
			attachment:true,
			multiple: false // Set to true to allow multiple files to be selected
		});
		 
		// When an image is selected, run a callback.
		file_frame.on( 'select', function() {
			// We set multiple to false so only get one image from the uploader
			attachment = file_frame.state().get('selection').first().toJSON();
		 	attachment_id = attachment.id;
			set_to_post_id = args.item.ID;
			args.item._thumbnail_id = attachment.id;
			args.item._thumbnail_id_val = attachment.url;
			var ajaxarr = {};
			ajaxarr.action = 'wpmelon_adv_bulk_edit';
			ajaxarr.type = 'setthumb';
			ajaxarr.nonce = window.W3ExABE.nonce;
			var _arrData = [];
			 var prodids = "";
		 	 if($('#ifapplyaction').length > 0)
			 {
			 	if($('#ifapplyaction').is(':checked'))
				{
					var data = args.grid.getData();
					if(args.grid.getActiveCell() !== null)
					{
						var iRow = args.grid.getActiveCell().row;
						var selectedRows = args.grid.getSelectedRows();
						if(selectedRows.length > 0)
						{
							for(var irow=0; irow < selectedRows.length; irow++)
							{
								var rowid = selectedRows[irow];
								if(rowid === undefined) continue;
								if(data[rowid] === undefined) continue;
								data[rowid]._thumbnail_id = attachment.id;
								data[rowid]._thumbnail_id_val = attachment.url;
								if(prodids == "")
								{
									prodids = data[rowid].ID.toString() + ',';
								}else
								{
									prodids+= ',' + data[rowid].ID.toString();
								}
								
							}
						}
					}
				}
			 }
			if(prodids !== "")
			{
				_arrData[0] = prodids;
				bapplied = true;
			}
			else
				_arrData[0] = args.item.ID;
			_arrData[1] = attachment.id;
			ajaxarr.data = _arrData;
			$wrapper.append('<div class="showajax"></div>');
			$('.showajax').css({
				left:'200px',
				top:'100px'
			});
			jQuery.ajax({
			     type : "post",
			     dataType : "json",
			     url : W3ExABE.ajaxurl,
			     data : ajaxarr,
			     success: function(response) {
				 	$img.css("display","inline");
					$img.attr("src", attachment.url);
					$aset.css("display","none");
					$aremove.css("display","inline");
			     },
				  complete:function (args)
				 {
				 	 $('.showajax').remove();
				 },
				 async : false
			  }) ;
			
			// Do something with attachment.id and/or attachment.url here
			// Restore the main post ID
//			alert('asd');
//			wp.media.model.settings.post.id = wp_media_post_id;
		});
		file_frame.on('open', function () {
			var attachment,
				selection = file_frame.state().get('selection');
			if(attachment_id !== -1)
			{
				attachment = wp.media.attachment(attachment_id);
				attachment.fetch();
				selection.add(attachment ? [ attachment ] : []);	
			}
		});
		// Finally, open the modal
		file_frame.open();
    };
	
    this.handleKeyDown = function (e) {
      if (e.which == $.ui.keyCode.ENTER && e.ctrlKey) {
        scope.save();
      } else if (e.which == $.ui.keyCode.ESCAPE) {
	  	bcandestroy = true;
        e.preventDefault();
        scope.cancel();
//		this.destroy();
      } else if (e.which == $.ui.keyCode.TAB && e.shiftKey) {
        e.preventDefault();
        args.grid.navigatePrev();
      } else if (e.which == $.ui.keyCode.TAB) {
        e.preventDefault();
        args.grid.navigateNext();
      }
    };

    this.save = function () {
		bcandestroy = true;
	  var W3Ex = window.W3Ex || {};
	  W3Ex.bdontgodown = true;
	   var ids = "";
	   var images = "";
	  $ul.find('li').each(function ()
	  {
	  	 var $item = $(this);
		 if(ids == "")
		 {
		 	ids = $item.attr('data-id');
		 }else
		 {
		 	ids+= "," + $item.attr('data-id');
		 }
		 if(images == "")
		 {
		 	images = $item.attr('data-image');
		 }else
		 {
		 	images+= "|" + $item.attr('data-image');
		 }
			
	  })
	  args.item['_product_image_gallery_val'] = images;
	  args.item['_product_image_gallery_original'] = images;
	  args.item['_product_image_gallery'] = ids;
	  var ajaxarr = {};
			ajaxarr.action = 'wpmelon_adv_bulk_edit';
			ajaxarr.type = 'setgallery';
			ajaxarr.nonce = window.W3ExABE.nonce;
			var _arrData = [];
			 var prodids = "";
		 	 if($('#ifapplyaction').length > 0)
			 {
			 	if($('#ifapplyaction').is(':checked'))
				{
					var data = args.grid.getData();
					if(args.grid.getActiveCell() !== null)
					{
						var iRow = args.grid.getActiveCell().row;
						var selectedRows = args.grid.getSelectedRows();
						if(selectedRows.length > 0)
						{
							for(var irow=0; irow < selectedRows.length; irow++)
							{
								var rowid = selectedRows[irow];
								if(rowid === undefined) continue;
								if(data[rowid] === undefined) continue;
								if(data[rowid].post_type == 'product_variation') continue;
								data[rowid]._product_image_gallery_val = images;
								data[rowid]._product_image_gallery_original = images;
	 							data[rowid]._product_image_gallery = ids;
								if(prodids == "")
								{
									prodids = data[rowid].ID.toString() + ',';
								}else
								{
									prodids+= ',' + data[rowid].ID.toString();
								}
								
							}
						}
					}
				}
			 }
			if(prodids !== "")
			{
				_arrData[0] = prodids;
				bapplied = true;
			}
			else
				_arrData[0] = args.item.ID;
			_arrData[1] = ids;
			ajaxarr.data = _arrData;
			$wrapper.find("button:first").parent().append('<div class="showajax"></div>');
			$('.showajax').css({
				left:'170px',
				top:'5px'
			});
			var upargs = args;
			jQuery.ajax({
			     type : "post",
			     dataType : "json",
			     url : W3ExABE.ajaxurl,
			     data : ajaxarr,
			     success: function(response) {
				 	
			     },
				  complete:function (args)
				 {
				 	 $('.showajax').remove();
					 upargs.cancelChanges();
				 },
				 async : false
			  }) ;
	  
    };

    this.cancel = function () {
	   bcandestroy = true;
       args.cancelChanges();
    };

    this.hide = function () {
      $wrapper.hide();
    };

    this.show = function () {
      $wrapper.show();
    };

    this.position = function (position) {
      $wrapper
          .css("top", position.top - 170)
          .css("left", position.left - 185)
    };

    this.destroy = function () {
//		if(!bcandestroy) return;
      $wrapper.remove();
//	   alert('s');
		{
			var W3Ex = window.W3Ex || {};
			 W3Ex.imageeditor = false;
		}
		
	  if(bapplied)
	  {
 		var W3Ex = window.W3Ex || {};
	  	W3Ex.invalidateselected = true;
	  }
    };

    this.focus = function () {
//      $input.focus();
    };

    this.loadValue = function (item) {
	 if( item['_product_image_gallery_val'] !== undefined && item['_product_image_gallery_val'] !== "")
	 {
	 	 var images = item['_product_image_gallery_val'];
		 if(images.indexOf('|') === -1)
		 {
		 	 attachment_id = parseInt(item['_product_image_gallery']);
		 	 var insert = '<li class="ui-state-default" data-id="'+attachment_id+'" data-image="'+ images +'" style="background:url('+ images +') top left no-repeat;background-size: 90px auto;"><img class="delete" src="' + W3Ex.imagepath + 'images/gallerydel.png"></li>';
			 $ul.append(insert);
			
			 set_to_post_id =  parseInt(item['ID']);
//			 $aset.css("display","none");
		 }else
		 {
		 	var res = images.split('|');
			if(item['_product_image_gallery'] !== undefined)
			{
				var ids = item['_product_image_gallery'].split(',');
				if(res instanceof Array && ids instanceof Array && res.length === ids.length)
				{
					for(var i=0;i< res.length; i++)
					{
						var insert = '<li class="ui-state-default" data-id="'+ids[i]+'" data-image="'+ res[i] +'" style="background:url('+ res[i] +') top left no-repeat;background-size: 90px auto;"><img class="delete" src="' + W3Ex.imagepath + 'images/gallerydel.png"></li>';
						$ul.append(insert);
					}
				}
			}
			
		 }
	 }
	 else
	 {
		 attachment_id = -1;
		 set_to_post_id = -1;
	 }
	 $( "#sortable" ).sortable();
	 $( "#sortable" ).disableSelection();
    };

    this.serializeValue = function () {
      return $input.val();
    };

    this.applyValue = function (item, state) {
//		if(bapplied)
//		{
//			var data = args.grid.getData();
//			var selectedRows = args.grid.getSelectedRows();
//			if(selectedRows.length > 0)
//			{
//				for(var irow=0; irow < selectedRows.length; irow++)
//				{
//					var rowid = selectedRows[irow];
//					if(rowid === undefined) continue;
//					if(data[rowid] === undefined) continue;
//					data[rowid][args.column.field] = state;
//				}
//			}
//		}else
      		item[args.column.field] = state;
    };

    this.isValueChanged = function () {
		return false;
//      return (!($input.val() == "" && defaultValue == null)) && ($input.val() != defaultValue);
    };

    this.validate = function () {
      return {
        valid: true,
        msg: null
      };
    };

    this.init();
  }
  
   function ImageEditor(args) {
    var $input, $wrapper,$img,$aremove,$aset;
    var defaultValue;
    var scope = this;
	var file_frame;
	var set_to_post_id = -1;
	var attachment_id = -1;
	var bapplied = false;
	var W3Ex = window.W3Ex || {};
	 W3Ex.imageeditor = true;
    this.init = function () {
      var $container = $("body");
	 
	 
      $wrapper = $("<DIV style='z-index:100;position:absolute;background:white;padding:15px;padding-bottom:10px;border:3px solid gray; -moz-border-radius:5px; border-radius:5px;'/>")
          .appendTo($container);
	  $('<div id="w3exabe-note">Note! - image changes are saved immediately.</div>')
	   .appendTo($wrapper);
      $input = $("<div class='imageholder'></div>")
          .appendTo($wrapper);
	  $img = $("<img src='' width='150px' height='150px' style='cursor:pointer;'>").appendTo($input);
	  $aset =  $("<a href='#' class='setimage' style='position:absolute;left:45px;top:100px;'>Set product image</a>")
          .appendTo($input);
	  
	  $aremove = $("<a href='#' class='removeimage'>Remove product image</a>")
          .appendTo($wrapper);
	   var data = args.grid.getData();
	  if(args.grid.getActiveCell() !== null)
	  {
	  	 var iRow = args.grid.getActiveCell().row;
		 var selectedRows = args.grid.getSelectedRows();
		 if(selectedRows.length > 0)
		 {
		 	for(var irow=0; irow < selectedRows.length; irow++)
			{
				var rowid = selectedRows[irow];
				if(rowid === undefined) continue;
				if(data[rowid] === undefined) continue;
				if(rowid === iRow)
				{//clicked on selected
					$('<br/><br/><label><input id="ifapplyaction" type="checkbox">Apply action to all<br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; selected products</label>').appendTo($wrapper);
					break;
				}
			}
		}
	  }
      $("<DIV style='text-align:right'><BUTTON>Close</BUTTON></DIV>")
          .appendTo($wrapper);
	 
      $wrapper.find("button:first").bind("click", this.cancel);
	  $aremove.bind("click", this.removeImage);
	  $aset.find("a .setimage").bind("click", this.setImage);
      $input.bind("keydown", this.handleKeyDown);
	  $input.bind("click", this.handleClick);
      scope.position(args.position);
      $input.focus().select();
    };
	 
	 this.setImage = function (e) 
	 {
    
     };
	 
	 this.removeImage = function (e) 
	 { 
	     var prodids = "";
	 	 if($('#ifapplyaction').length > 0)
		 {
		 	if($('#ifapplyaction').is(':checked'))
			{
				var data = args.grid.getData();
				if(args.grid.getActiveCell() !== null)
				{
					var iRow = args.grid.getActiveCell().row;
					var selectedRows = args.grid.getSelectedRows();
					if(selectedRows.length > 0)
					{
						for(var irow=0; irow < selectedRows.length; irow++)
						{
							var rowid = selectedRows[irow];
							if(rowid === undefined) continue;
							if(data[rowid] === undefined) continue;
							data[rowid]._thumbnail_id = "";
							data[rowid]._thumbnail_id_val = "";
							if(prodids == "")
							{
								prodids = data[rowid].ID.toString() + ',';
							}else
							{
								prodids+= ',' + data[rowid].ID.toString();
							}
							
						}
					}
				}
			}
		 }
	 	
    	var ajaxarr = {};
		ajaxarr.action = 'wpmelon_adv_bulk_edit';
		ajaxarr.type = 'removethumb';
		ajaxarr.nonce = window.W3ExABE.nonce;
		var _arrData = [];
		if(prodids !== "")
		{
			_arrData[0] = prodids;
			bapplied = true;
		}
		else
			_arrData[0] = args.item.ID;
		ajaxarr.data = _arrData;
		$wrapper.append('<div class="showajax"></div>');
		$('.showajax').css({
			left:'200px',
			top:'100px'
		});
		jQuery.ajax({
		     type : "post",
		     dataType : "json",
		     url : W3ExABE.ajaxurl,
		     data : ajaxarr,
		     success: function(response) {
			 	$img.css("display","none");
				$aremove.css("display","none");
				$aset.css("display","inline");
				set_to_post_id = -1;
				attachment_id = -1;
				args.item._thumbnail_id = "";
				args.item._thumbnail_id_val = "";
		     },
			 complete:function (args)
			 {
			 	 $('.showajax').remove();
			 },
			 async : false
		  }) ;
     };
		
	 this.handleClick = function (e) {
     if ( file_frame ) {
		// Set the post ID to what we want
		if(set_to_post_id !== -1)
			file_frame.uploader.uploader.param( 'post_id', set_to_post_id );
			
			// Open frame
			file_frame.open();
			return;
		} else {
		// Set the wp.media post id so the uploader grabs the ID we want when initialised
			if(set_to_post_id !== -1)
				wp.media.model.settings.post.id = set_to_post_id;
		}
		 
		// Create the media frame.
		file_frame = wp.media.frames.file_frame = wp.media({
			title: 'Set product image',
			button: {
				text:  'Set product image',
			},
			attachment:true,
			multiple: false // Set to true to allow multiple files to be selected
		});
		 
		// When an image is selected, run a callback.
		file_frame.on( 'select', function() {
			// We set multiple to false so only get one image from the uploader
			attachment = file_frame.state().get('selection').first().toJSON();
		 	attachment_id = attachment.id;
			set_to_post_id = args.item.ID;
			args.item._thumbnail_id = attachment.id;
			args.item._thumbnail_id_val = attachment.url;
			var ajaxarr = {};
			ajaxarr.action = 'wpmelon_adv_bulk_edit';
			ajaxarr.type = 'setthumb';
			ajaxarr.nonce = window.W3ExABE.nonce;
			var _arrData = [];
			 var prodids = "";
		 	 if($('#ifapplyaction').length > 0)
			 {
			 	if($('#ifapplyaction').is(':checked'))
				{
					var data = args.grid.getData();
					if(args.grid.getActiveCell() !== null)
					{
						var iRow = args.grid.getActiveCell().row;
						var selectedRows = args.grid.getSelectedRows();
						if(selectedRows.length > 0)
						{
							for(var irow=0; irow < selectedRows.length; irow++)
							{
								var rowid = selectedRows[irow];
								if(rowid === undefined) continue;
								if(data[rowid] === undefined) continue;
								data[rowid]._thumbnail_id = attachment.id;
								data[rowid]._thumbnail_id_val = attachment.url;
								if(prodids == "")
								{
									prodids = data[rowid].ID.toString() + ',';
								}else
								{
									prodids+= ',' + data[rowid].ID.toString();
								}
								
							}
						}
					}
				}
			 }
			if(prodids !== "")
			{
				_arrData[0] = prodids;
				bapplied = true;
			}
			else
				_arrData[0] = args.item.ID;
			_arrData[1] = attachment.id;
			ajaxarr.data = _arrData;
			$wrapper.append('<div class="showajax"></div>');
			$('.showajax').css({
				left:'200px',
				top:'100px'
			});
			jQuery.ajax({
			     type : "post",
			     dataType : "json",
			     url : W3ExABE.ajaxurl,
			     data : ajaxarr,
			     success: function(response) {
				 	$img.css("display","inline");
					$img.attr("src", attachment.url);
					$aset.css("display","none");
					$aremove.css("display","inline");
			     },
				  complete:function (args)
				 {
				 	 $('.showajax').remove();
				 },
				 async : false
			  }) ;
			
			// Do something with attachment.id and/or attachment.url here
			// Restore the main post ID
//			alert('asd');
//			wp.media.model.settings.post.id = wp_media_post_id;
		});
		file_frame.on('open', function () {
			var attachment,
				selection = file_frame.state().get('selection');
			if(attachment_id !== -1)
			{
				attachment = wp.media.attachment(attachment_id);
				attachment.fetch();
				selection.add(attachment ? [ attachment ] : []);	
			}
		});
		// Finally, open the modal
		file_frame.open();
    };
	
    this.handleKeyDown = function (e) {
      if (e.which == $.ui.keyCode.ENTER && e.ctrlKey) {
        scope.save();
      } else if (e.which == $.ui.keyCode.ESCAPE) {
        e.preventDefault();
        scope.cancel();
      } else if (e.which == $.ui.keyCode.TAB && e.shiftKey) {
        e.preventDefault();
        args.grid.navigatePrev();
      } else if (e.which == $.ui.keyCode.TAB) {
        e.preventDefault();
        args.grid.navigateNext();
      }
    };

    this.save = function () {
	  var W3Ex = window.W3Ex || {};
	  W3Ex.bdontgodown = true;
    };

    this.cancel = function () {
      args.cancelChanges();
    };

    this.hide = function () {
      $wrapper.hide();
    };

    this.show = function () {
      $wrapper.show();
    };

    this.position = function (position) {
      $wrapper
          .css("top", position.top - 170)
          .css("left", position.left - 185)
    };

    this.destroy = function () {
      $wrapper.remove();
//	   alert('s');
		{
			var W3Ex = window.W3Ex || {};
			W3Ex.imageeditor = false;
		}
	  if(bapplied)
	  {
 		var W3Ex = window.W3Ex || {};
	  	W3Ex.invalidateselected = true;
	  }
    };

    this.focus = function () {
//      $input.focus();
    };

    this.loadValue = function (item) {
	 if( item['_thumbnail_id_val'] !== undefined && item['_thumbnail_id_val'] !== "")
	 {
	     $input.find('img').attr("src", item['_thumbnail_id_val']);
		 attachment_id = parseInt(item['_thumbnail_id']);
		 set_to_post_id =  parseInt(item['ID']);
		 $aset.css("display","none");
	 }
	 else
	 {
	 	 $img.css("display","none");
		 $aremove.css("display","none");
		 attachment_id = -1;
		 set_to_post_id = -1;
	 }
    };

    this.serializeValue = function () {
      return $input.val();
    };

    this.applyValue = function (item, state) {
//		if(bapplied)
//		{
//			var data = args.grid.getData();
//			var selectedRows = args.grid.getSelectedRows();
//			if(selectedRows.length > 0)
//			{
//				for(var irow=0; irow < selectedRows.length; irow++)
//				{
//					var rowid = selectedRows[irow];
//					if(rowid === undefined) continue;
//					if(data[rowid] === undefined) continue;
//					data[rowid][args.column.field] = state;
//				}
//			}
//		}else
      		item[args.column.field] = state;
    };

    this.isValueChanged = function () {
		return false;
//      return (!($input.val() == "" && defaultValue == null)) && ($input.val() != defaultValue);
    };

    this.validate = function () {
      return {
        valid: true,
        msg: null
      };
    };

    this.init();
  }
  
  function TextAreaEditor(args) {
    var $input, $wrapper;
    var defaultValue;
    var scope = this;
	var W3Ex = window.W3Ex || {};
	var width = args.column.field + 'width';
	var height = args.column.field + 'height';
    this.init = function () {
      var $container = $("body");
	  
      $wrapper = $("<DIV style='z-index:10000;position:absolute;background:white;padding:5px;border:3px solid gray; -moz-border-radius:10px; border-radius:10px;'/>")
          .appendTo($container);

      $input = $("<TEXTAREA hidefocus rows=5 style='backround:white;width:250px;height:80px;border:0;outline:0'>")
          .appendTo($wrapper);

      $("<DIV style='text-align:right'><BUTTON>Save</BUTTON><BUTTON>Cancel</BUTTON></DIV>")
          .appendTo($wrapper);
	  if(W3Ex[width] !== undefined && W3Ex[height] !== undefined)
	  {
	  	   $input.css({
		  	  'width':W3Ex[width],
			  'height':W3Ex[height]
		  })
	  }
	 
      $wrapper.find("button:first").bind("click", this.save);
      $wrapper.find("button:last").bind("click", this.cancel);
      $input.bind("keydown", this.handleKeyDown);

      scope.position(args.position);
      $input.focus().select();
    };

    this.handleKeyDown = function (e) {
      if (e.which == $.ui.keyCode.ENTER && e.ctrlKey) {
        scope.save();
      } else if (e.which == $.ui.keyCode.ESCAPE) {
        e.preventDefault();
        scope.cancel();
      } else if (e.which == $.ui.keyCode.TAB && e.shiftKey) {
        e.preventDefault();
        args.grid.navigatePrev();
      } else if (e.which == $.ui.keyCode.TAB) {
        e.preventDefault();
        args.grid.navigateNext();
      }
    };

    this.save = function () {
	  var W3Ex = window.W3Ex || {};
	  W3Ex.bdontgodown = true;
      args.commitChanges();
    };

    this.cancel = function () {
      $input.val(defaultValue);
      args.cancelChanges();
    };

    this.hide = function () {
      $wrapper.hide();
    };

    this.show = function () {
      $wrapper.show();
    };

    this.position = function (position) {
      $wrapper
          .css("top", position.top - 5)
          .css("left", position.left - 55)
    };

    this.destroy = function () {
	  W3Ex[width] = $input.css('width');
	  W3Ex[height] = $input.css('height');
      $wrapper.remove();
    };

    this.focus = function () {
      $input.focus();
    };

    this.loadValue = function (item) {
      $input.val(defaultValue = item[args.column.field]);
//      $input.select();
    };

    this.serializeValue = function () {
      return $input.val();
    };

    this.applyValue = function (item, state) {
      item[args.column.field] = state;
    };

    this.isValueChanged = function () {
      return (!($input.val() == "" && defaultValue == null)) && ($input.val() != defaultValue);
    };

    this.validate = function () {
      return {
        valid: true,
        msg: null
      };
    };

    this.init();
  }

	function customDateEditor(args) {
		var $input;
		var defaultValue;
		var scope = this;
		var calendarOpen = false;

		this.init = function () {
			$input = $("<INPUT type=text class='editor-text' />");
			$input.appendTo(args.container);
			$input.focus().select();
			$input.datepicker({
				dateFormat: "yy-mm-dd",
				showOn: "button",
				buttonImageOnly: true,
				buttonImage: W3Ex.imagepath + 'images/calendar.gif',
				beforeShow: function () {
					calendarOpen = true;
				},
				onClose: function () {
					calendarOpen = false;
				}
			});
			$input.width($input.width() - 18);
			if($('.w3exabedeleditor').length === 0 )
			 	$('.ui-datepicker').wrap('<div class="w3exabe w3exabedeleditor" />');
		};

		this.destroy = function () {
			$.datepicker.dpDiv.stop(true, true);
			$input.datepicker("hide");
			$input.datepicker("destroy");
			$input.remove();
			$(".w3exabedeleditor").contents().unwrap();
		};

		this.show = function () {
			
			if (calendarOpen) {
				$.datepicker.dpDiv.stop(true, true).show();
			}
		};

		this.hide = function () {
			if (calendarOpen) {
				$.datepicker.dpDiv.stop(true, true).hide();
			}
		};

		this.position = function (position) {
			if (!calendarOpen) {
				return;
			}
			$.datepicker.dpDiv
					.css("top", position.top + 30)
					.css("left", position.left);
		};

		this.focus = function () {
			$input.focus();
		};

		this.loadValue = function (item) {
			defaultValue = item[args.column.field];
			$input.val(defaultValue);
			$input[0].defaultValue = defaultValue;
			$input.select();
		};

		this.serializeValue = function () {
			return $input.val();
		};

		this.applyValue = function (item, state) {
			item[args.column.field] = state;
		};

		this.isValueChanged = function () {
			return (!($input.val() == "" && defaultValue == null)) && ($input.val() != defaultValue);
		};

		this.validate = function () {
			return {
				valid: true,
				msg: null
			};
		};

		this.init();
	}
		
    function SelectCellEditor(args) {
	var $select;
	var defaultValue;
	var scope = this;

	this.init = function() {
		var opt_values;
	    if(args.column.options){
	      opt_values = args.column.options.split(',');
	    }else{
	      opt_values ="yes,no".split(',');
	    }
	    var option_str = "";
		if(args.column.field == "_visibility")
		{
			 for( i in opt_values ){
		      var v = opt_values[i];
			  if(v == args.item._visibility)
		      	option_str += "<OPTION value='"+v+"' selected='selected'>"+v+"</OPTION>";
			  else 
			  	option_str += "<OPTION value='"+v+"'>"+v+"</OPTION>";
			  
	    	}
		}else if(args.column.field == "_download_type")
		{
			 for( i in opt_values ){
		      var v = opt_values[i];
//			  if(args.item.download_type != null)
			  if(v == args.item._download_type)
		      	option_str += "<OPTION value='"+v+"' selected='selected'>"+v+"</OPTION>";
			  else 
			  	option_str += "<OPTION value='"+v+"'>"+v+"</OPTION>";
			  
	    	}
		}else if(args.column.field == "post_status")
		{
			 for( i in opt_values ){
		      var v = opt_values[i];
			  if(v == args.item.post_status)
		      	option_str += "<OPTION value='"+v+"' selected='selected'>"+v+"</OPTION>";
			  else 
			  	option_str += "<OPTION value='"+v+"'>"+v+"</OPTION>";
			  
	    	}
		}else
		{
			 for( i in opt_values ){
		      var v = opt_values[i];
			  if(v == args.item[args.column.field])
		      	option_str += "<OPTION value='"+v+"' selected='selected'>"+v+"</OPTION>";
			  else 
			  	option_str += "<OPTION value='"+v+"'>"+v+"</OPTION>";
			  
	    	}
		}
	   
	    $select = $("<SELECT tabIndex='0' class='editor-select'>"+ option_str +"</SELECT>");
	    $select.appendTo(args.container);
	    $select.focus();
	};

	this.destroy = function() {
	    $select.remove();
	};

	this.focus = function() {
	    $select.focus();
	};

	this.loadValue = function(item) {
	    defaultValue = item[args.column.field];
	    $select.val(defaultValue);
	};

	this.serializeValue = function() {
	    if(args.column.options){
	      return $select.val();
	    }else{
	      return ($select.val() == "yes");
	    }
	};

	this.applyValue = function(item,state) {
		if(state != null)
	    	item[args.column.field] = state;
	};

	this.isValueChanged = function() {
	    return ($select.val() != defaultValue);
	};

	this.validate = function() {
	    return {
	        valid: true,
	        msg: null
	    };
	};

	this.init();
	}
			
  function TextEditor(args) {
    var $input;
    var defaultValue;
    var scope = this;

    this.init = function () {
      $input = $("<INPUT type=text class='editor-text' />")
          .appendTo(args.container)
          .bind("keydown.nav", function (e) {
            if (e.keyCode === $.ui.keyCode.LEFT || e.keyCode === $.ui.keyCode.RIGHT) {
              e.stopImmediatePropagation();
            }
          })
          .focus()
          .select();
    };

    this.destroy = function () {
      $input.remove();
    };

    this.focus = function () {
      $input.focus();
    };

    this.getValue = function () {
      return $input.val();
    };

    this.setValue = function (val) {
      $input.val(val);
    };

    this.loadValue = function (item) {
      defaultValue = item[args.column.field] || "";
      $input.val(defaultValue);
      $input[0].defaultValue = defaultValue;
      $input.select();
    };

    this.serializeValue = function () {
      return $input.val();
    };

    this.applyValue = function (item, state) {
      item[args.column.field] = state;
    };

    this.isValueChanged = function () {
      return (!($input.val() == "" && defaultValue == null)) && ($input.val() != defaultValue);
    };

    this.validate = function () {
      if (args.column.validator) {
        var validationResults = args.column.validator($input.val());
        if (!validationResults.valid) {
          return validationResults;
        }
      }

      return {
        valid: true,
        msg: null
      };
    };

    this.init();
  }

  function IntegerEditor(args) {
    var $input;
    var defaultValue;
    var scope = this;

    this.init = function () {
      $input = $("<INPUT type=text class='editor-text' />");

      $input.bind("keydown.nav", function (e) {
        if (e.keyCode === $.ui.keyCode.LEFT || e.keyCode === $.ui.keyCode.RIGHT) {
          e.stopImmediatePropagation();
        }
      });

      $input.appendTo(args.container);
      $input.focus().select();
    };

    this.destroy = function () {
      $input.remove();
    };

    this.focus = function () {
      $input.focus();
    };

    this.loadValue = function (item) {
      defaultValue = item[args.column.field];
      $input.val(defaultValue);
      $input[0].defaultValue = defaultValue;
      $input.select();
    };

    this.serializeValue = function () {
      return parseInt($input.val(), 10) || 0;
    };

    this.applyValue = function (item, state) {
      item[args.column.field] = state;
    };

    this.isValueChanged = function () {
      return (!($input.val() == "" && defaultValue == null)) && ($input.val() != defaultValue);
    };

    this.validate = function () {
      if (isNaN($input.val())) {
        return {
          valid: false,
          msg: "Please enter a valid integer"
        };
      }

      return {
        valid: true,
        msg: null
      };
    };

    this.init();
  }

  function DateEditor(args) {
    var $input;
    var defaultValue;
    var scope = this;
    var calendarOpen = false;

    this.init = function () {
      $input = $("<INPUT type=text class='editor-text' />");
      $input.appendTo(args.container);
      $input.focus().select();
      $input.datepicker({
        showOn: "button",
        buttonImageOnly: true,
        buttonImage: "../images/calendar.gif",
        beforeShow: function () {
          calendarOpen = true
        },
        onClose: function () {
          calendarOpen = false
        }
      });
      $input.width($input.width() - 18);
    };

    this.destroy = function () {
      $.datepicker.dpDiv.stop(true, true);
      $input.datepicker("hide");
      $input.datepicker("destroy");
      $input.remove();
    };

    this.show = function () {
      if (calendarOpen) {
        $.datepicker.dpDiv.stop(true, true).show();
      }
    };

    this.hide = function () {
      if (calendarOpen) {
        $.datepicker.dpDiv.stop(true, true).hide();
      }
    };

    this.position = function (position) {
      if (!calendarOpen) {
        return;
      }
      $.datepicker.dpDiv
          .css("top", position.top + 30)
          .css("left", position.left);
    };

    this.focus = function () {
      $input.focus();
    };

    this.loadValue = function (item) {
      defaultValue = item[args.column.field];
      $input.val(defaultValue);
      $input[0].defaultValue = defaultValue;
      $input.select();
    };

    this.serializeValue = function () {
      return $input.val();
    };

    this.applyValue = function (item, state) {
      item[args.column.field] = state;
    };

    this.isValueChanged = function () {
      return (!($input.val() == "" && defaultValue == null)) && ($input.val() != defaultValue);
    };

    this.validate = function () {
      return {
        valid: true,
        msg: null
      };
    };

    this.init();
  }

  function YesNoSelectEditor(args) {
    var $select;
    var defaultValue;
    var scope = this;

    this.init = function () {
      $select = $("<SELECT tabIndex='0' class='editor-yesno'><OPTION value='yes'>Yes</OPTION><OPTION value='no'>No</OPTION></SELECT>");
      $select.appendTo(args.container);
      $select.focus();
    };

    this.destroy = function () {
      $select.remove();
    };

    this.focus = function () {
      $select.focus();
    };

    this.loadValue = function (item) {
      $select.val((defaultValue = item[args.column.field]) ? "yes" : "no");
      $select.select();
    };

    this.serializeValue = function () {
      return ($select.val() == "yes");
    };

    this.applyValue = function (item, state) {
      item[args.column.field] = state;
    };

    this.isValueChanged = function () {
      return ($select.val() != defaultValue);
    };

    this.validate = function () {
      return {
        valid: true,
        msg: null
      };
    };

    this.init();
  }

  function CheckboxEditor(args) {
    var $select;
    var defaultValue;
    var scope = this;

    this.init = function () {
      $select = $("<INPUT type=checkbox value='true' class='editor-checkbox' hideFocus>");
      $select.appendTo(args.container);
      $select.focus();
    };

    this.destroy = function () {
      $select.remove();
    };

    this.focus = function () {
      $select.focus();
    };

    this.loadValue = function (item) {
      defaultValue = item[args.column.field];
	  if(args.column.field == "_stock_status")
	  {
	  	  if(defaultValue == "instock") {
	        $select.prop('checked', true);
	      } else {
	        $select.prop('checked', false);
	      }
	  }else
	  {
	  	 if(defaultValue == "yes") {
	        $select.prop('checked', true);
	      } else {
	        $select.prop('checked', false);
	      }
	  }
      
    };

    this.serializeValue = function () {
      return $select.prop('checked');
    };

    this.applyValue = function (item, state) {
		if(args.column.field == "_stock_status")
		{
		  	 if(state == false)
		 	 	item[args.column.field] = "outofstock";  //<-- remove that line, add lines below
		  	 else
		 	 	item[args.column.field] = "instock";  
		}else
		{
		  if(state == false)
		 	 item[args.column.field] = "no";  //<-- remove that line, add lines below
		  else
		 	 item[args.column.field] = "yes";  
		}
//		  if (state == 'checked') {
//		      item[args.column.field] = true;
//		  } else {
//		      item[args.column.field] = false;  
//		  }
    };

    this.isValueChanged = function () {
      return (this.serializeValue() !== defaultValue);
    };

    this.validate = function () {
      return {
        valid: true,
        msg: null
      };
    };

    this.init();
  }

  function PercentCompleteEditor(args) {
    var $input, $picker;
    var defaultValue;
    var scope = this;

    this.init = function () {
      $input = $("<INPUT type=text class='editor-percentcomplete' />");
      $input.width($(args.container).innerWidth() - 25);
      $input.appendTo(args.container);

      $picker = $("<div class='editor-percentcomplete-picker' />").appendTo(args.container);
      $picker.append("<div class='editor-percentcomplete-helper'><div class='editor-percentcomplete-wrapper'><div class='editor-percentcomplete-slider' /><div class='editor-percentcomplete-buttons' /></div></div>");

      $picker.find(".editor-percentcomplete-buttons").append("<button val=0>Not started</button><br/><button val=50>In Progress</button><br/><button val=100>Complete</button>");

      $input.focus().select();

      $picker.find(".editor-percentcomplete-slider").slider({
        orientation: "vertical",
        range: "min",
        value: defaultValue,
        slide: function (event, ui) {
          $input.val(ui.value)
        }
      });

      $picker.find(".editor-percentcomplete-buttons button").bind("click", function (e) {
        $input.val($(this).attr("val"));
        $picker.find(".editor-percentcomplete-slider").slider("value", $(this).attr("val"));
      })
    };

    this.destroy = function () {
      $input.remove();
      $picker.remove();
    };

    this.focus = function () {
      $input.focus();
    };

    this.loadValue = function (item) {
      $input.val(defaultValue = item[args.column.field]);
      $input.select();
    };

    this.serializeValue = function () {
      return parseInt($input.val(), 10) || 0;
    };

    this.applyValue = function (item, state) {
      item[args.column.field] = state;
    };

    this.isValueChanged = function () {
      return (!($input.val() == "" && defaultValue == null)) && ((parseInt($input.val(), 10) || 0) != defaultValue);
    };

    this.validate = function () {
      if (isNaN(parseInt($input.val(), 10))) {
        return {
          valid: false,
          msg: "Please enter a valid positive number"
        };
      }

      return {
        valid: true,
        msg: null
      };
    };

    this.init();
  }

  /*
   * An example of a "detached" editor.
   * The UI is added onto document BODY and .position(), .show() and .hide() are implemented.
   * KeyDown events are also handled to provide handling for Tab, Shift-Tab, Esc and Ctrl-Enter.
   */
  function LongTextEditor(args) {
    var $input,$input_val, $wrapper, $table;
	var input_val;
    var defaultValue;
    var scope = this;
	var downloadable_file_frame;
	var file_path_field;
	var newtr;
	
    this.init = function () {
      var $container = $("body");

      $wrapper = $("<DIV style='z-index:10000;position:absolute;background:white;padding:5px;border:3px solid gray; -moz-border-radius:10px; border-radius:10px;width:400px;'/>")
          .appendTo($container);

      $input = $("<TEXTAREA hidefocus rows=1 style='backround:white;width:0px;height:0px;border:0;outline:0;display:none;'>")
          .appendTo($wrapper);
//	  $input_val = $("<TEXTAREA hidefocus rows=1 style='backround:white;width:0px;height:0px;border:0;outline:0;display:none;'>")
//          .appendTo($wrapper);
	  newtr = '<tr> \
						<td> \
						<input style="width:80px;" class="input_text filename" placeholder="File Name" name="_wc_file_names[]" value="" type="text">\
						</td>\
						<td> \
						<input style="width:160px;" class="input_text filepath" placeholder="File Path" name="_wc_file_path[]" value="" type="text">\
						</td>\
						<td style="width:20%;"> \
						<a href="#" class="button upload_file_button choosefile" data-choose="Choose file" data-update="Insert file URL">Choose&nbsp;file</a>\
						</td>\
						<td style="width:10%;"> \
						<a href="#" class="deletefile">Del</a>\
						</td>\
					</tr>';
	  $table = $('<table style="max-width:390px;"></table>')
          .appendTo($wrapper);
	   $('<a href="#" class="button insert" style="margin-left:10px;">Add File</a>')
          .appendTo($wrapper);
      $("<DIV style='text-align:right'><BUTTON>Save</BUTTON><BUTTON>Cancel</BUTTON></DIV>")
          .appendTo($wrapper);

      $wrapper.find("button:first").bind("click", this.save);
      $wrapper.find("button:last").bind("click", this.cancel);
	  jQuery(document).on( 'click', '.choosefile',this.loadFile);
	  jQuery(document).on( 'click', '.deletefile',this.deleteFile);
	  $wrapper.find("a.insert").bind("click",function(e){$table.append(newtr);e.preventDefault();});
      $input.bind("keydown", this.handleKeyDown);

      scope.position(args.position);
      $input.focus().select();
	 
    };
	
	 this.save = function () {
	  var fieldtext = "";
	  var fieldtext_val = "";
	  $table.find('tr').each(function(){
			 var path = $(this).find('.filepath').val();
			 if(!(!path || !/\S/.test(path)))
			 {
			 	 
			 	 var name = $(this).find('.filename').val();
	//			 if(!(!name || !/\S/.test(name)))
				 {
				 	fieldtext = fieldtext + " Name:" + name + " URL:" + path;
					if(fieldtext_val == "")
						fieldtext_val =  name + "#####" + path;
					else
						fieldtext_val =  fieldtext_val + "*****" + name + "#####" + path;
				 }
			 }
		}
	  )
	  $input.val(fieldtext);
	  input_val = fieldtext_val;
	  var W3Ex = window.W3Ex || {};
	  W3Ex.bdontgodown = true;
      args.commitChanges();
    };
	
	 this.loadValue = function (item) {
      $input.val(defaultValue = item[args.column.field]);
      $input.select();
	  var value_val = item[args.column.field+'_val'];
	  if(value_val !== undefined && value_val !== "")
	  {
	  	  var cols = value_val.split('*****');
		  for(var i = 0; i < cols.length; i++)
		  {
		  	  var itemsarr = cols[i];
			  if(itemsarr === undefined || itemsarr === "") continue;
		  	  var items =  itemsarr.split('#####');
			  var bappend  = false;
			  for(var j = 0; j < items.length; j++)
		  	  {
			      var item = items[j];	
				  if(item === undefined || item === "") continue;
				  if(!bappend)
				  {
					  $table.append(newtr);
					  bappend = true;
				  }
				  if(j == 0)
				  {//name
				  	   $table.find(".filename:last").val(item);
				  }else
				  {//url
				  	   $table.find(".filepath:last").val(item);
				  }
			  }
		  }
	  }
    };
	
    this.handleKeyDown = function (e) {
      if (e.which == $.ui.keyCode.ENTER && e.ctrlKey) {
        scope.save();
      } else if (e.which == $.ui.keyCode.ESCAPE) {
        e.preventDefault();
        scope.cancel();
      } else if (e.which == $.ui.keyCode.TAB && e.shiftKey) {
        e.preventDefault();
        args.grid.navigatePrev();
      } else if (e.which == $.ui.keyCode.TAB) {
        e.preventDefault();
        args.grid.navigateNext();
      }
    };
	this.loadFile = function(event) {
//		$(this).closest('tr').find('td .filepath').val('asd');
//		$(this).closest('tr').find('td .filename').val('qwe');
	
		var $el = $(this);

		file_path_field = $el.closest('tr').find('td .filepath');

		event.preventDefault();

		// If the media frame already exists, reopen it.
		if ( downloadable_file_frame ) {
			downloadable_file_frame.open();
			return;
		}

		var downloadable_file_states = [
			// Main states.
			new wp.media.controller.Library({
				library:   wp.media.query(),
				multiple:  true,
				title:     $el.data('choose'),
				priority:  20,
				filterable: 'uploaded',
			})
		];

		// Create the media frame.
		downloadable_file_frame = wp.media.frames.downloadable_file = wp.media({
			// Set the title of the modal.
			title: $el.data('choose'),
			library: {
				type: ''
			},
			button: {
				text: $el.data('update'),
			},
			multiple: true,
			states: downloadable_file_states,
		});

		// When an image is selected, run a callback.
		downloadable_file_frame.on( 'select', function() {

			var file_path = '';
			var selection = downloadable_file_frame.state().get('selection');

			selection.map( function( attachment ) {

				attachment = attachment.toJSON();

				if ( attachment.url )
					file_path = attachment.url

			} );

			file_path_field.val( file_path );
		});

		// Set post to 0 and set our custom type
		downloadable_file_frame.on( 'ready', function() {
			downloadable_file_frame.uploader.options.uploader.params = {
				type: 'downloadable_product'
			};
		});

		// Finally, open the modal.
		downloadable_file_frame.open();
	};
	
	this.deleteFile = function(event) {
		var $el = $(this);
		$el.closest('tr').remove();
		event.preventDefault();
	};
	
   

    this.cancel = function () {
      $input.val(defaultValue);
      args.cancelChanges();
    };

    this.hide = function () {
      $wrapper.hide();
    };

    this.show = function () {
      $wrapper.show();
    };

    this.position = function (position) {
      $wrapper
          .css("top", position.top - 5)
          .css("left", position.left - 255);
    };

    this.destroy = function () {
	  $wrapper.find("button:first").unbind();
      $wrapper.find("button:last").unbind();
	  jQuery(document).off( 'click', '.choosefile');
	  jQuery(document).off( 'click', '.deletefile');
	  $wrapper.find("a.insert").unbind();
      $wrapper.remove();
    };

    this.focus = function () {
      $input.focus();
    };

   

    this.serializeValue = function () {
      return $input.val();
    };

    this.applyValue = function (item, state) {
      item[args.column.field] = state;
	  item[args.column.field+'_val'] = input_val;
    };

    this.isValueChanged = function () {
      return (!($input.val() == "" && defaultValue == null)) && ($input.val() != defaultValue);
    };

    this.validate = function () {
      return {
        valid: true,
        msg: null
      };
    };

    this.init();
  }
})(jQuery);
