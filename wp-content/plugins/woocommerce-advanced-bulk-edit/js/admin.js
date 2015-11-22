
var W3Ex = W3Ex || {};

jQuery(document).ready(function(){
	
W3Ex.abemodule = (function($){
//	"use strict";
	var _arrEdited = [];
	var _currentItem = {};
	var _conitems = 0;
	var _conitemsfin = 0;
	var _u;
	var _grid;
	var _data = [];
	var _seldata = [];
	var _scounter = 0;
	var _shouldinvalidate = false;
	var _totalrecords = 0;
	var _currentoffset = 1;
	var _recordslimit = 1000;
	var _gridData = [];
	var _shouldhandle = true;
	var _changedcustom = [];
	var _loadedgrouped = [];
	var _pagecats;
	var _pageattrs;
	var _pagepriceparam;
	var _pagesaleparam;
	var _pagetitleparam;
	var _pagedescparam;
	var _pageshortdescparam;
	var _pagecustomparam; 
	var _pageskuparam;
	var _pagetagsparam;
	var _pagecustsearchparam;
	var _hasnext = false;
	var _handledeletearea = true;
	var _cancontinueconfirm = false;
	var _deletetype = "0";
	var _addprodtype = "0";
	var _confirmationclick = "";
	var _varstocreate = {};
	var _hascreation = false;
	var _productstocreate = 1;
	var _selectedParent;
	var _duplicatenumber = 1;
	var options = {
		editable: true,
		enableCellNavigation: true,
		asyncEditorLoading: false,
		autoEdit: false
	};
	var _changed = {};
	var columns = [];
  
	

	  var checkboxSelector = new Slick.CheckboxSelectColumn({
      cssClass: "slick-cell-checkboxsel"
    });

	$("#addcustomfield").button({
       icons: {
          primary: "ui-icon-plusthick"
       }
    });
	$("#addok").button({
       icons: {
          primary: "ui-icon-check"
       }
    });
	$("#addcancel").button({
       icons: {
          primary: "ui-icon-closethick"
       }
    });
	
	$( "#pluginsettingstab" ).tabs();
	
	if(!isNaN($('#productlimit').val()))
		_recordslimit = parseInt($('#productlimit').val());
	
	$('#butprevious').prop("disabled",true);
	$('#gotopage').prop("disabled",true);
	$('#butnext').prop("disabled",true);
	
	$('body').on('mouseenter','#deletearea',function()
	{
		{
			$('.deleteextra').remove();
			var movet = "Move to Trash";
			var deletep = "Delete Permanently";
			var deletes = "Delete";
			if(W3Ex.trans_movetrash !== undefined && W3Ex.trans_movetrash !== "")
				movet = W3Ex.trans_movetrash;
			if(W3Ex.trans_delperm !== undefined && W3Ex.trans_delperm !== "")
				deletep = W3Ex.trans_delperm;
			if(W3Ex.trans_delete !== undefined && W3Ex.trans_delete !== "")
			{
				deletes = String(W3Ex.trans_delete);
				if(deletes.length > 0)
				{
					deletes = deletes.charAt(0).toUpperCase() + deletes.slice(1);
				}
			}
			$(this).append('<div class="deleteextra"> \
			<table cellpadding="4" cellspacing="0"> \
				<tr> \
					<td> \
						<label><input type="radio" value="0" checked name="deletewhat" id="radiodeletetrash">'+movet+'</label> \
					</td></tr><tr><td>\
						<label><input type="radio" value="1" name="deletewhat" id="radiodeleteperm">'+deletep+'</label> \
					</td> \
				</tr>  \
			</table>  \
			<input id="deletebutr" class="button" type="button" value="'+deletes+'"/> \
			</div>');
		}
		
		$('.deleteextra').show(100,function ()
		{
			var selectedRows = _grid.getSelectedRows();
			if(selectedRows.length == 0)
			{
				return;
			}
			var hasone = false;
			var parentid = 0;
		
			for(var irow=0; irow < selectedRows.length; irow++)
			{
				var rowid = selectedRows[irow];
				if(rowid === undefined) continue;
				if(_data[rowid] === undefined) continue;
				var selitem = _data[rowid];
				if(selitem.post_type == 'product')
				{
					return;
				}
			}
			//only variations
			$("#radiodeleteperm").prop("checked", true);
			$("#radiodeletetrash").prop("disabled", true);
		});
	});
	
	$('body').on('mouseenter','#addprodarea',function()
	{
		{
			$('.addprodextra').remove();
			var products = "Products";
			var variations = "Variations";
			var add = "Add";
			if(W3Ex.trans_products !== undefined && W3Ex.trans_products !== "")
				products = W3Ex.trans_products;
			if(W3Ex.trans_variations !== undefined && W3Ex.trans_variations !== "")
				variations = W3Ex.trans_variations;
			if(W3Ex.trans_add !== undefined && W3Ex.trans_add !== "")
			{
				add = String(W3Ex.trans_add);
				if(add.length > 0)
				{
					add = add.charAt(0).toUpperCase() + add.slice(1);
				}
			}
				
			$(this).append('<div class="addprodextra"> \
			<table cellpadding="4" cellspacing="0"> \
				<tr> \
					<td> \
						<label><input type="radio" value="0" checked name="addprodwhat">'+products+'</label> \
						&nbsp;&nbsp;<input id="addproductsnumber" type="text" value="1" style="width:20px !important;"/> \
					</td></tr><tr><td>\
						<label><input type="radio" id="addprodwhatv" value="1" name="addprodwhat">'+variations+'</label> \
					</td> \
				</tr>  \
			</table>  \
			<input id="addprodbutr" class="button" type="button" value="'+add+'"/> \
			</div>');
		}
		$('.addprodextra').show(100,function ()
		{
			var selectedRows = _grid.getSelectedRows();
			if(selectedRows.length == 0)
			{
				$('#addprodwhatv').prop("disabled",true);
				return;
			}
			var hasone = false;
			var parentid = 0;
			if($('#linkededit').is(':checked'))
				return;
			for(var irow=0; irow < selectedRows.length; irow++)
			{
				var rowid = selectedRows[irow];
				if(rowid === undefined) continue;
				if(_data[rowid] === undefined) continue;
				var selitem = _data[rowid];
				if(selitem.post_type == 'product')
				{
					if(hasone)
					{
						$('#addprodwhatv').prop("disabled",true);
				 		return;
					}
					if(parentid != 0 && selitem.post_parent != parentid)
					{
						$('#addprodwhatv').prop("disabled",true);
				 		return;
					}
					parentid = selitem.ID;
					hasone = true;
				}else
				{
					if(parentid == 0)
					{
						parentid = selitem.post_parent;
					}else
					{
						if(selitem.post_parent != parentid)
						{
							$('#addprodwhatv').prop("disabled",true);
				 			return;
						}
					}
				}
			}
		});
	});
	
	$('body').on('mouseenter','#duplicateprodarea',function()
	{
		$('.duplicateprodextra').remove();
		var duplicate = "Duplicate";
		var times = "Time(s)";
		if(W3Ex.trans_duplicate !== undefined && W3Ex.trans_duplicate !== "")
		{
			duplicate = String(W3Ex.trans_duplicate);
//			if(duplicate.length > 0)
//			{
//				duplicate = duplicate.charAt(0).toUpperCase() + duplicate.slice(1);
//			}
		}
		if(W3Ex.trans_times !== undefined && W3Ex.trans_times !== "")
		{
			times = String(W3Ex.trans_times);
		}
		
		$(this).append('<div class="duplicateprodextra"> \
		<table cellpadding="8" cellspacing="0"> \
			<tr> \
				<td><input id="addduplicatesnumber" type="text" value="1" style="width:20px !important;"/> '+times+' \
				</td></tr> \
			</tr>  \
		</table>  \
		<input id="duplicateprodbutr" class="button" type="button" value="'+duplicate+'"/> \
		</div>');
		
		$('.duplicateprodextra').show(100,function ()
		{
			
		});
	});
	
	$('body').on('mouseenter','.addedvariation',function()
	{
		$(this).find('img').css('visibility','visible');
	});
	
	$('body').on('mouseleave','.addedvariation',function()
	{
		$(this).find('img').css('visibility','hidden');
	});
	
	$('body').on('click','.addedvariation img',function()
	{
		$(this).parent().remove();
	});
	
	$('body').on('click','#addprodbutr',function()
	{
		_addprodtype = $('input[name=addprodwhat]:checked').val();
		var selectedRows = _grid.getSelectedRows();
		var parentid = 0;
		var found = false;
		if(_addprodtype == "1")
		{
			if(selectedRows.length == 0) 
			{
				$('.addprodextra').remove();
				return;
			}
			for(var irow=0; irow < selectedRows.length; irow++)
			{
				var rowid = selectedRows[irow];
				if(rowid === undefined) continue;
				if(_data[rowid] === undefined) continue;
				var selitem = _data[rowid];
				if(selitem.post_type == 'product')
				{
					_selectedParent = selitem;
					found = true;
					break;
				}else
				{
					parentid = selitem.post_parent;
				}
			}	 
			if(!found)
			{
				for(var ir=0; ir < _data.length; ir++)
				{
					if(_data[ir] === undefined) continue;
					var selitem = _data[ir];
					if(selitem.ID == parentid)
					{
						_selectedParent = selitem;
						break;
					}
				}	
			}
			$('.addprodextra').remove();
			$("#addproddialog").dialog("open");
			return;
		}
		_productstocreate = $('#addproductsnumber').val();
		_hascreation = true;
		$('.addprodextra').remove();
		SaveChanges('savechanges');
	});
	
	$('body').on('click','#duplicateprodbutr',function()
	{
		_duplicatenumber = $('#addduplicatesnumber').val();
		$('.duplicateprodextra').remove();
		DuplicateProducts();
		
	});
	
	$('body').on('mouseleave','#addprodarea',function()
	{
		$(this).find('div.addprodextra').remove();
	});
	
	$('body').on('mouseleave','#duplicateprodarea',function()
	{
		$(this).find('div.duplicateprodextra').remove();
	});
	
	$('body').on('click','.createbulkvar',function()
	{
		var w3exattrs =  W3Ex.attributes;
		$('#bulkvarsdialog .editorcats :checkbox:enabled:checked').each(function(){
			var instext = '';
			var attrid = $(this).val();
			var islinkedediting = $('#linkededit').is(':checked');
//			if($('#addproddialog .variationholder input[data-id="'+$(this).val()+'"]').length > 0)
//			{
			if( $('#linkededit').is(':checked'))
			{
				$('#attributeplaceholder .variationholder input').each(function ()
				{
					var attrslug = $(this).attr('data-id');
					if(attrslug === undefined) return true;
					var attrname = $(this).attr('data-name');
					if(_mapfield[attrslug] === undefined) return true;
	  	  			 var col = _idmap[_mapfield[attrslug]];
		  			 if(col === undefined) return true;
					 if(true === col.attribute)
					 {
						if(instext == "")
							instext = '<select data-id="'+attrslug+'">';
						else
							instext+= '<br/><select data-id="'+attrslug+'">';
						instext+=	'<option value="">Any '+attrname+'</option>';
						for (var atkey in w3exattrs) 
						{
				 			 if (w3exattrs.hasOwnProperty(atkey)) 
							 {
							 	var atribut = w3exattrs[atkey];
								if(atribut === undefined) continue;
								if(('attribute_pa_' + atribut.attr) !== attrslug) continue;
								if(atribut.term_id == attrid)
									instext+= '<option value="'+atribut.value+'" selected>'+atribut.name+'</option>';
								else
									instext+= '<option value="'+atribut.value+'">'+atribut.name+'</option>';
							 }
						}
						/*for(var i = 0; i < W3Ex.attributes.length; i++)
						{
							var atribut = W3Ex.attributes[i];
							if(atribut === undefined) continue;
							if(('attribute_pa_' + atribut.attr) !== attrslug) continue;
							if(atribut.term_id == attrid)
								instext+= '<option value="'+atribut.value+'" selected>'+atribut.name+'</option>';
							else
								instext+= '<option value="'+atribut.value+'">'+atribut.name+'</option>';
							
						}*/
						instext+= '<select>';
					 }	
				})
				 if(instext !== '')
				{
					$('#variationholder').append('<div class="addedvariation">'+instext+' \
						<img class="delete" src="' + W3Ex.imagepath + 'images/gallerydel.png"></div>');
				}
				return;
			}
			for (var key in _selectedParent) 
			{
	 			 if (_selectedParent.hasOwnProperty(key)) 
				 {
	  	  		 	 if(_mapfield[key] === undefined) continue;
	  	  			 var col = _idmap[_mapfield[key]];
		  			 if(col === undefined) continue;
					 if(true === col.attribute)
					 {
					 	if(_selectedParent[col.field + '_ids'] !== undefined && _selectedParent[col.field + '_ids'] != "")
						{
							if(instext == "")
								instext = '<select data-id="'+col.field+'">';
							else
								instext+= '<br/><select data-id="'+col.field+'">';
							instext+=	'<option value="">Any '+col.name.replace("(attr) ","")+'</option>';
							var ids = _selectedParent[col.field + '_ids'];
							ids = ids.split(',');
							for(var i = 0; i < ids.length; i++)
							{
								for (var atkey in w3exattrs) 
								{
						 			 if (w3exattrs.hasOwnProperty(atkey)) 
									 {
									 	var atribut = w3exattrs[atkey];
										if(atribut === undefined) continue;
										if(('attribute_pa_' + atribut.attr) !== col.field) continue;
										if(atribut.term_id == ids[i])
										{
											if(atribut.term_id == attrid)
												instext+= '<option value="'+atribut.value+'" selected>'+atribut.name+'</option>';
											else
												instext+= '<option value="'+atribut.value+'">'+atribut.name+'</option>';
										}
									 }
								}
							/*	for(var j = 0; j < W3Ex.attributes.length; j++)
								{
									var atribut = W3Ex.attributes[j];
									if(atribut === undefined) continue;
									if(('attribute_pa_' + atribut.attr) !== attrslug) continue;
									if(atribut.term_id == attrid)
										instext+= '<option value="'+atribut.value+'" selected>'+atribut.name+'</option>';
									else
										instext+= '<option value="'+atribut.value+'">'+atribut.name+'</option>';
									
								}*/
								/*if(W3Ex.attributes !== undefined && W3Ex.attributes[ids[i]] !== undefined)
								{
									var atribut = W3Ex.attributes[ids[i]];
									if(ids[i] == attrid)
										instext+= '<option value="'+atribut.value+'" selected>'+atribut.name+'</option>';
									else
										instext+= '<option value="'+atribut.value+'">'+atribut.name+'</option>';
								}*/
								
							}
							instext+= '<select>';
						}
					 }
				 }
			}
			if(instext !== '')
			{
				$('#variationholder').append('<div class="addedvariation">'+instext+' \
					<img class="delete" src="' + W3Ex.imagepath + 'images/gallerydel.png"></div>');
			}
		})
		
		$('#bulkvarsdialog').remove();
	})

	$('body').on('click','.showattributes',function()
	{
		var added = false;
		var hasold = false;
		$('#bulkvarsdialog .editorcats :checkbox:checked').each(function(){
			var attrslug = $(this).val();
			var attrname = $(this).attr('data-label')
			if($('#addproddialog .variationholder input[data-id="'+attrslug+'"]').length > 0)
			{
//				$('#addproddialog .variationholder input[data-id="'+$(this).val()+'"]').
				hasold = true;
				return true;
			}
			$("#attributeplaceholder").append('<div class="variationholder">' +attrname+' \
								<input data-id="'+attrslug+'" data-name="'+attrname+'" style="vertical-align:middle;" class="button addbulkvars" type="button" value="Bulk Add" /></div>' );
								
			var	instext = '<br/><select data-id="'+attrslug+'">';
			instext+=	'<option value="">Any '+attrname+'</option>';
			for (var atkey in  W3Ex.attributes) 
			{
	 			 if ( W3Ex.attributes.hasOwnProperty(atkey)) 
				 {
				 	var atribut =  W3Ex.attributes[atkey];
					if(atribut === undefined) continue;
					if(('attribute_pa_' + atribut.attr) !== attrslug) continue;
					instext+= '<option value="'+atribut.value+'">'+atribut.name+'</option>';
				 }
			}
			/*for(var i = 0; i < W3Ex.attributes.length; i++)
			{
				var atribut = W3Ex.attributes[i];
				if(atribut === undefined) continue;
				if(('attribute_pa_' + atribut.attr) !== attrslug) continue;
				instext+= '<option value="'+atribut.value+'">'+atribut.name+'</option>';
				
			}*/
			instext+= '<select>';
			$('.addedvariation').append(instext);
			added = true;
		})
		
		$('#bulkvarsdialog .editorcats :checkbox:not(:checked)').each(function(){
			if($('#addproddialog .variationholder input[data-id="'+$(this).val()+'"]').length > 0)
			{
				$('#addproddialog .variationholder input[data-id="'+$(this).val()+'"]').parent().remove();
				$('.addedvariation select[data-id="'+$(this).val()+'"]').remove();
			}
		})
		
		if(added)
		{
			if(!hasold && $('#variationholder').length ==0)
			{
				$("#addproddialog").append('<div><input id="addsinglevar" style="vertical-align:middle;" class="button" type="button" value="Add Single Variation" /></br></div>');
				$("#addproddialog").append('<div id="variationholder"></div>');
			}
		}else
		{
			if(!hasold)
			{
				$("#addsinglevar").parent().remove();
				$("#variationholder").remove();
			}
		}
		
		$('#bulkvarsdialog').remove();
	})
	
	$('body').on('click','.cancelbulkvar',function()
	{
		$('#bulkvarsdialog').remove();
	})
	
	$('body').on('click','#addsinglevar',function()
	{
		var instext = '';
		var w3exattrs =  W3Ex.attributes;
		if( $('#linkededit').is(':checked'))
		{
			$('#attributeplaceholder .variationholder input').each(function ()
			{
				var attrslug = $(this).attr('data-id');
				if(attrslug === undefined) return true;
				var attrname = $(this).attr('data-name');
				if(_mapfield[attrslug] === undefined) return true;
  	  			 var col = _idmap[_mapfield[attrslug]];
	  			 if(col === undefined) return true;
				 if(true === col.attribute)
				 {
					if(instext == "")
						instext = '<select data-id="'+attrslug+'">';
					else
						instext+= '<br/><select data-id="'+attrslug+'">';
					instext+=	'<option value="">Any '+attrname+'</option>';
					for (var atkey in w3exattrs) 
					{
			 			 if (w3exattrs.hasOwnProperty(atkey)) 
						 {
						 	var atribut = w3exattrs[atkey];
							if(atribut === undefined) continue;
							if(('attribute_pa_' + atribut.attr) !== attrslug) continue;
							instext+= '<option value="'+atribut.value+'">'+atribut.name+'</option>';
						 }
					}
					/*for(var i = 0; i < W3Ex.attributes.length; i++)
					{
						var atribut = W3Ex.attributes[i];
						if(atribut === undefined) continue;
						if(('attribute_pa_' + atribut.attr) !== attrslug) continue;
						instext+= '<option value="'+atribut.value+'">'+atribut.name+'</option>';
						
					}*/
					instext+= '<select>';
				 }	
			})
			 if(instext !== '')
			{
				$('#variationholder').append('<div class="addedvariation">'+instext+' \
					<img class="delete" src="' + W3Ex.imagepath + 'images/gallerydel.png"></div>');
			}
			return;
		}
		for (var key in _selectedParent) 
		{
 			 if (_selectedParent.hasOwnProperty(key)) 
			 {
  	  		 	 if(_mapfield[key] === undefined) continue;
  	  			 var col = _idmap[_mapfield[key]];
	  			 if(col === undefined) continue;
				 if(true === col.attribute)
				 {
				 	if(_selectedParent[col.field + '_ids'] !== undefined && _selectedParent[col.field + '_ids'] != "")
					{
						if(instext == "")
							instext = '<select data-id="'+col.field+'">';
						else
							instext+= '<br/><select data-id="'+col.field+'">';
						instext+=	'<option value="">Any '+col.name.replace("(attr) ","")+'</option>';
						var ids = _selectedParent[col.field + '_ids'];
						ids = ids.split(',');
						for(var i = 0; i < ids.length; i++)
						{
							/*if(W3Ex.attributes !== undefined && W3Ex.attributes[ids[i]] !== undefined)
							{
								var atribut = W3Ex.attributes[ids[i]];
								instext+= '<option value="'+atribut.value+'">'+atribut.name+'</option>';
							}*/
							/*for(var j = 0; j < W3Ex.attributes.length; j++)
							{
								var atribut = W3Ex.attributes[j];
								if(atribut === undefined) continue;
								if(atribut.term_id == ids[i])
								{
									instext+= '<option value="'+atribut.value+'">'+atribut.name+'</option>';
									break;
								}								
							}*/
							for (var atkey in w3exattrs) 
							{
					 			 if (w3exattrs.hasOwnProperty(atkey)) 
								 {
								 	var atribut = w3exattrs[atkey];
									if(atribut === undefined) continue;
									if(atribut.term_id == ids[i])
									{
										instext+= '<option value="'+atribut.value+'">'+atribut.name+'</option>';
										break;
									}	
								 }
							}
							
						}
						instext+= '<select>';
					}
				 }
			 }
		}
		
		$('#variationholder').append('<div class="addedvariation">'+instext+' \
		<img class="delete" src="' + W3Ex.imagepath + 'images/gallerydel.png"></div>');
	})
	
	$('body').on('click','.applytoall',function()
	{
		var ischecked = $(this).is(':checked');
//		$('#bulkvarsdialog').find(':checkbox:last').attr('disabled','disabled');
		$('#bulkvarsdialog').find(':checkbox:enabled').prop('checked',ischecked);
	})
	
	$('body').on('click','.addbulkvars',function()
	{
		$('#bulkvarsdialog').remove();
	  var $container = $(this).parent();
	  var attr_name = $(this).attr('data-id');
	  var posX = $(this).position().left,
          posY = $(this).position().top;
	var islinkedediting = $('#linkededit').is(':checked');
      $wrapper = $("<DIV style='z-index:300005;position:absolute;background:white;padding:25px;padding-top:12px;padding-bottom:12px;border:3px solid gray; -moz-border-radius:10px; border-radius:10px;min-width:150px;top:"+posY+"px;left:"+posX+"px;' id='bulkvarsdialog'/>")
          .appendTo($container);
	   $('<div><label><input type="checkbox" class="applytoall">check/uncheck all</label></div><hr>').appendTo($wrapper);
	   
      $input = $("<div style='max-height:350px;overflow:auto;' class='editorcats'></div>")
          .appendTo($wrapper);
	 
	  $input.html($('#categoriesdialog .' + attr_name).html());
     
	  $("<DIV style='text-align:right'><BUTTON class='createbulkvar'>Create</BUTTON><BUTTON class='cancelbulkvar'>Cancel</BUTTON></DIV>")
          .appendTo($wrapper);
		  
		 if(!islinkedediting)
			$('#bulkvarsdialog .editorcats input').prop('disabled',true);
		 else
		 	return;
			
		if(_selectedParent.post_type == 'product')
		{
			for (var key in _selectedParent) 
			{
	 			 if (_selectedParent.hasOwnProperty(key)) 
				 {
	  	  		 	 if(_mapfield[key] === undefined) continue;
	  	  			 var col = _idmap[_mapfield[key]];
		  			 if(col === undefined) continue;
					 if(true === col.attribute)
					 {
					 	if(_selectedParent[col.field + '_ids'] !== undefined && _selectedParent[col.field + '_ids'] != "")
						{
							var attrarray = _selectedParent[col.field + '_ids'].split(',');
							for(var i = 0; i < attrarray.length; i++)
							{
								$('#bulkvarsdialog input[value="'+attrarray[i]+'"]').prop("disabled",false);
							}
						}
					 }
				 }
			}
		}
	});
	
	function CreateProducts()
	{
		var ajaxarr = {};
		ajaxarr.action = 'wpmelon_adv_bulk_edit';
		ajaxarr.type = 'createproducts';
		ajaxarr.nonce = W3ExABE.nonce;
		ajaxarr.prodcount = 1;
		var prodcount = _productstocreate;
		var changeback = true;
		if(!isNaN(prodcount))
		{
			prodcount = parseInt(prodcount);
			if(prodcount >= 1 && prodcount <=100)
			{
				ajaxarr.prodcount = prodcount;
				changeback = false;
			}else
			{
				if(prodcount > 100)
				{
					ajaxarr.prodcount = 100;
				}
			}
		}
		if(changeback)
		{
			$('#addproductsnumber').val(ajaxarr.count);
			_productstocreate = 1;
		}
		
		$('#myGrid').prepend('<div id="dimgrid" style="position: absolute;top:0;left:0;width: 100%;height:100%;z-index:102;opacity:0.4;filter: alpha(opacity = 40);background-color:grey;"></div>');
		DisableAllControls(true);
		ajaxarr.data = "";
		jQuery.ajax({
		     type : "post",
		     dataType : "json",
		     url : W3ExABE.ajaxurl,
		     data : ajaxarr,
		     success: function(response) {
			 		$('#dimgrid').remove();
					DisableAllControls(false);
					$('.showajax').remove();
					if(response.products === undefined || response.products === null)
						return;
		 			_grid.setSelectedRows([]);
			 		var newvars = response.products;
					for(var ir=0; ir < newvars.length; ir++)
					{
						var selitem = newvars[ir];
						selitem.post_title = 'New Product';
					}
					
					var selindexes = [];
					if(_data.length === 0)
					{
						for(var i=0; i<newvars.length; i++) 
						{
					        _data[i] = newvars[i];
//							selindexes.push(i);
					    }
					}else
					{
						for(var ir=0; ir < _data.length; ir++)
						{
							if(_data[ir] === undefined) continue;
							var selitem = _data[ir];
						  	if(ir == 0)
							{
								for(var i=_data.length-1; i>=ir; i--) 
								{
							        _data[i + newvars.length] = _data[i];
							    }
								
							    for(var i=0; i<newvars.length; i++) 
								{
							        _data[i+ir] = newvars[i];
									selindexes.push(i+ir);
							    }
								break;
							}
						}
					}
					_grid.setSelectedRows(selindexes);
					var all = _data.length;
					var seltext = ' '+selindexes.length+' of ' + all;
					if(_totalrecords !== -1)
					{
						_totalrecords+= newvars.length;
						$('#totalrecords').text(_totalrecords);
					}
					GenerateGroupedItems();
					_shouldhandle = false;
					_grid.resetActiveCell();
					_grid.invalidate();
					_shouldhandle = true;	
					
		     },
			 complete:function (args)
			 {
			  	//uncomment to debug
				_hascreation = false;
//			    $('#debuginfo').html(args.responseText);
			 }, error:function (xhr, status, error) 
			  {
			  	//uncomment to debug
				  $('#dimgrid').remove();
				  $('.showajax').remove();
				  DisableAllControls(false);
				  $('#debuginfo').html(xhr.responseText);
			  }
		  }) ;
	}
	
	$('body').on('click','#selectattributes',function()
	{
		$('#bulkvarsdialog').remove();
	  var $container = $(this).parent();
//	  var attr_name = $(this).attr('data-id');
	  var posX = $(this).position().left,
          posY = $(this).position().top;
		
      $wrapper = $("<DIV style='z-index:300005;position:absolute;background:white;padding:25px;padding-top:12px;padding-bottom:12px;border:3px solid gray; -moz-border-radius:10px; border-radius:10px;min-width:150px;top:"+posY+"px;left:"+posX+"px;' id='bulkvarsdialog'/>")
          .appendTo($container);
//	   $('<div><label><input type="checkbox" class="applytoall">check/uncheck all</label></div><hr>').appendTo($wrapper);
	   
      $input = $("<div style='max-height:350px;overflow:auto;' class='editorcats'></div>")
          .appendTo($wrapper);
	 
	  $input.html($('#allattributeslist').html());
	  
      //check existing ones
	  $('#bulkvarsdialog .editorcats :checkbox').each(function(){
			if($('#addproddialog .variationholder input[data-id="'+$(this).val()+'"]').length > 0)
			{
				$(this).attr('checked',true);
			}
			
		})
	  
	  $("<DIV style='text-align:right'><BUTTON class='showattributes'>Show</BUTTON><BUTTON class='cancelbulkvar'>Cancel</BUTTON></DIV>")
          .appendTo($wrapper);
//		$('#bulkvarsdialog .editorcats input').attr('disabled','disabled');
		
	})
	
	function CreateVariations()
	{
		var ajaxarr = {};
		ajaxarr.action = 'wpmelon_adv_bulk_edit';
		ajaxarr.type = 'createvariations';
		ajaxarr.nonce = W3ExABE.nonce;
		
		var bcon = false;
		for (var key in _varstocreate) 
		{
		  if (_varstocreate.hasOwnProperty(key)) 
		  {
			  bcon = true;
			  break;
		  }
		}
		if(!bcon) return;
		
		$('#myGrid').prepend('<div id="dimgrid" style="position: absolute;top:0;left:0;width: 100%;height:100%;z-index:102;opacity:0.4;filter: alpha(opacity = 40);background-color:grey;"></div>');
		DisableAllControls(true);
		ajaxarr.data = _varstocreate;
		jQuery.ajax({
		     type : "post",
		     dataType : "json",
		     url : W3ExABE.ajaxurl,
		     data : ajaxarr,
		     success: function(response) {
			 		$('#dimgrid').remove();
					DisableAllControls(false);
					$('.showajax').remove();
		 			var islinkedediting = $('#linkededit').is(':checked');
			 		var newvars = response.products;
					for(var ir=0; ir < newvars.length; ir++)
					{
						var selitem = newvars[ir];
						if(selitem.post_type == 'product_variation')
							selitem.post_title = '(Var. of #'+selitem.post_parent+') '+selitem.post_title;
						else
							;
					}
					
					var parentids = [];
					
					if(islinkedediting)
					{
						var selectedRows = _grid.getSelectedRows();
						for(var irow=0; irow < selectedRows.length; irow++)
						{
							var rowid = selectedRows[irow];
							if(rowid === undefined) continue;
							if(_data[rowid] === undefined) continue;
							var selitem = _data[rowid];
							if(selitem.post_type == 'product_variation')
							{
								var hasitalready = false;
								for (var i = 0; i < parentids.length; i++) 
								{
							        if (parentids[i] == selitem.post_parent) 
									{
										hasitalready = true;
							            break;
							        }
							    }
								if(!hasitalready)
								{
									parentids.push(selitem.post_parent);
								}
								continue;
							}else
							{//maybe inserted from child ?
								var hasitalready = false;
								for (var i = 0; i < parentids.length; i++) 
								{
							        if (parentids[i] == selitem.ID) 
									{
										hasitalready = true;
							            break;
							        }
							    }
								if(!hasitalready)
								{
									parentids.push(selitem.ID);
								}
							}
						}
					}
					
					var selindexes = [];
					
					if(islinkedediting)
					{
						for(var ip=0; ip < parentids.length; ip++)
						{
							var parentid = parentids[ip];
							for(var ir=0; ir < _data.length; ir++)
							{
								if(_data[ir] === undefined) continue;
								var selitem = _data[ir];
								
								if(selitem.ID == parentid)
								{
									var countvars = 0;
									for(var r=0; r < newvars.length; r++) 
									{
								       var item = newvars[r];
									   if(item.post_parent == parentid)
									   {
									   	   item.post_title = selitem.post_title + ' ' + item.post_title;
									   	   countvars++;
									   }
								    }
									for(var i=_data.length-1; i>=ir+1; i--) 
									{
								        _data[i + countvars] = _data[i];
								    }
									var incounter = 0;
								    for(var i=0; i<newvars.length; i++) 
									{
										var initem = newvars[i];
										if(initem.post_parent == parentid)
										{
											_data[incounter+ir+1] = newvars[i];
											selindexes.push(incounter+ir+1);
											incounter++;
										}
								    }
									var idmaps = [];
									for(var i=0; i < _data.length; i++)
									{
										if(_data[i] === undefined) continue;
										var selitem = _data[i];
										idmaps[selitem.ID] = i;
									}
									for(var j=0; j < newvars.length; j++)
									{
										if(newvars[j] === undefined) continue;
										var selitem = newvars[j];
										if(selitem.post_type == 'product_variation') continue;
										if(idmaps[selitem.ID] !== undefined)
										{
											if(_data[idmaps[selitem.ID]] !== undefined)
											{
												var initem = _data[idmaps[selitem.ID]];
												for (var key in selitem) 
												{
												  if (selitem.hasOwnProperty(key)) 
												  {
													  if(key == 'ID' || key == 'post_parent')
													  	continue;
													if(key.indexOf('_visiblefp') !== -1)
													{
														if(initem[key] !== undefined)
														   initem[key]|= selitem[key];
														else
														   initem[key] = selitem[key];
													}else
													  initem[key] = selitem[key];
												  }
												}
											}
										}
									}
									while(idmaps.length > 0) 
									{
									    idmaps.pop();
									}
//									parentids.splice(ip,1);
									break;
								}
							}
					    }
					}else
					{
						for(var ir=0; ir < _data.length; ir++)
						{
							if(_data[ir] === undefined) continue;
							var selitem = _data[ir];
							
							if(selitem.ID == _selectedParent.ID)
							{
								var countvars = 0;
								for(var r=0; r < newvars.length; r++) 
								{
							       var item = newvars[r];
								   if(item.post_type == 'product_variation')
								   {
								   	   countvars++;
									   item.post_title = selitem.post_title + ' ' + item.post_title;
								   }else
								   {
								   	   for (var key in item) 
										{
										  if (item.hasOwnProperty(key)) 
										  {
											  if(key == 'ID' || key == 'post_parent')
											  	continue;
											if(key.indexOf('_visiblefp') !== -1)
											{
												if(selitem[key] !== undefined)
												   selitem[key]|= item[key];
												else
												   selitem[key] = item[key];
											}else
											  selitem[key] = item[key];
										  }
										}
								   }
							    }
								for(var i=_data.length-1; i>=ir+1; i--) 
								{
							        _data[i + countvars] = _data[i];
							    }
								var selindexes = [];
//							    for(var i=0; i<newvars.length; i++) 
//								{
//							        _data[i+ir+1] = newvars[i];
//									selindexes.push(i+ir+1);
//							    }
								var incounter = 0;
							    for(var i=0; i<newvars.length; i++) 
								{
									var initem = newvars[i];
									if(initem.post_type == 'product_variation')
									{
										_data[incounter+ir+1] = newvars[i];
										selindexes.push(incounter+ir+1);
										incounter++;
									}
							    }
								break;
							}
						}
					}
					
					_grid.setSelectedRows(selindexes);
//					_grid.setData(_data);
					var all = _data.length;
					var seltext = ' '+selindexes.length+' of ' + all;
					if(_totalrecords !== -1)
					{
						_totalrecords+= newvars.length;
						$('#totalrecords').text(_totalrecords);
					}
					
					_shouldhandle = false;
					_grid.resetActiveCell();
					_grid.invalidate();
					_shouldhandle = true;	

					for (var key in _varstocreate) 
					{
					  if (_varstocreate.hasOwnProperty(key)) 
					  {
						 delete _varstocreate[key];
					  }
					}
					
		     },
			 complete:function (args)
			 {
			  	//uncomment to debug
				_hascreation = false;
//			    $('#debuginfo').html(args.responseText);
			 }, error:function (xhr, status, error) 
			  {
			  	//uncomment to debug
				  $('#dimgrid').remove();
				  $('.showajax').remove();
				  DisableAllControls(false);
				  $('#debuginfo').html(xhr.responseText);
			  }
		  }) ;
	}

	function DuplicateProducts()
	{
		var selectedRows = _grid.getSelectedRows();
		if(selectedRows.length <= 0)
			return;
			
		var ajaxarr = {};
		ajaxarr.action = 'wpmelon_adv_bulk_edit';
		ajaxarr.type = 'duplicateproducts';
		ajaxarr.nonce = W3ExABE.nonce;
		
		var _arrData = [];
		var _arr = {};
		_arr['post_status'] = [];
		var _arrParents = [];
		for(var ir=0; ir < selectedRows.length; ir++)
		{
			var rowid = selectedRows[ir];
			if(rowid === undefined) continue;
			if(_data[rowid] === undefined) continue;
			var selitem = _data[rowid];
			if(selitem.post_type !== 'product')
				continue;
		  	_arr['post_status'].push(selitem.ID + '$#' + selitem.post_parent + '$#' + selitem.post_status);
		}
		var bcon = false;
		for (var key in _arr) 
		{
		  if (_arr.hasOwnProperty(key)) 
		  {
		      _arr[key] = _arr[key].join('#$');
			  bcon = true;
		  }
		}
		if(!bcon) return;
		
		$('#myGrid').prepend('<div id="dimgrid" style="position: absolute;top:0;left:0;width: 100%;height:100%;z-index:102;opacity:0.4;filter: alpha(opacity = 40);background-color:grey;"></div>');
		DisableAllControls(true);
		ajaxarr.data = _arr;
		ajaxarr.dupcount = _duplicatenumber;
		jQuery.ajax({
		     type : "post",
		     dataType : "json",
		     url : W3ExABE.ajaxurl,
		     data : ajaxarr,
		     success: function(response) {
			 		$('#dimgrid').remove();
					DisableAllControls(false);
					$('.showajax').remove();
					
			 		if(response.products === undefined || response.products === null)
						return;
		 			_grid.setSelectedRows([]);
			 		var newvars = response.products;
//					for(var ir=0; ir < newvars.length; ir++)
//					{
//						var selitem = newvars[ir];
//						selitem.post_title = 'New Product';
//					}
//					
					var selindexes = [];
					if(_data.length === 0)
					{
						for(var i=0; i<newvars.length; i++) 
						{
					        _data[i] = newvars[i];
//							selindexes.push(i);
					    }
					}else
					{
						for(var ir=0; ir < _data.length; ir++)
						{
							if(_data[ir] === undefined) continue;
							var selitem = _data[ir];
						  	if(ir == 0)
							{
								for(var i=_data.length-1; i>=ir; i--) 
								{
							        _data[i + newvars.length] = _data[i];
							    }
								
							    for(var i=0; i<newvars.length; i++) 
								{
							        _data[i+ir] = newvars[i];
									selindexes.push(i+ir);
							    }
								break;
							}
						}
					}
					_grid.setSelectedRows(selindexes);
					var all = _data.length;
					var seltext = ' '+selindexes.length+' of ' + all;
					if(_totalrecords !== -1)
					{
						_totalrecords+= newvars.length;
						$('#totalrecords').text(_totalrecords);
					}
					
					newvars.sort(function(a, b){return a-b});
					
					var addedrowslength = newvars.length;
					
					if(addedrowslength > 0)
					{
						for(var ir=_arrEdited.length -1; ir >=0; ir--)
						{
							var row = _arrEdited[ir];
							if(row === undefined) continue;
							if(ir+addedrowslength >= 0)
							{
								_arrEdited[ir+addedrowslength] = row;
								delete _arrEdited[ir];
							}
						}
						var arrchangedkeys = [];
						for (var key in _changed) 
						{
						  if (_changed.hasOwnProperty(key)) 
						  {
						     arrchangedkeys.push(parseInt(key));
						  }
						}
						arrchangedkeys.sort(function(a, b){return a-b});
						for(var ir=arrchangedkeys.length -1; ir >=0; ir--)
						{
							var row = arrchangedkeys[ir];
							if(row === undefined) continue;
							if(_changed[row] === undefined) continue;
							if(row+addedrowslength >= 0)
							{
								_changed[row+addedrowslength] = _changed[row];
								delete _changed[row];
							}
						}
					}
					
					RefreshGroupedItems();
//					_shouldhandle = false;
//					_grid.resetActiveCell();
//					_grid.invalidate();
//					_shouldhandle = true;	
					
					$('#dimgrid').remove();
					DisableAllControls(false);
					$('.showajax').remove();
					
					try{
						_grid.removeCellCssStyles("changed");
						_grid.setCellCssStyles("changed", _changed);
					} catch (err) {
						;
					}
					_shouldhandle = false;
					_grid.resetActiveCell();
					_grid.invalidate();
					_shouldhandle = true;	
		     },
			 complete:function (args)
			 {
			  	//uncomment to debug
//			    $('#debuginfo').html(args.responseText);
			 }, error:function (xhr, status, error) 
			  {
			  	//uncomment to debug
				  $('#dimgrid').remove();
				  $('.showajax').remove();
				  DisableAllControls(false);
				  $('#debuginfo').html(xhr.responseText);
			  }
		  }) ;
	}
	
	////////////////////////////////////////////////////////////////////////////////////////////////
//	$('#linkededit').attr('checked','checked');
	////////////////////////////////////////////////////////////////////////////////////////////////
	$("#addproddialog").dialog({			
    autoOpen: false,
    height: 620,
    width:850,
    modal: true,
	draggable:true,
	resizable:false,
	title:"Add Variations",
	closeOnEscape: true,
	create: function (event, ui) {
        $(this).dialog('widget')
            .css({ position: 'fixed'})
    },
	open: function( event, ui ) {
		 var d = $('.ui-dialog:visible');
		 $(d).css('z-index',300002);
		 $('.ui-dialog:visible').wrap('<div class="w3exabe w3exabedel" />');
		  $('#addproddialog').css('height','480px');
		  $('.ui-widget-overlay').each(function () {
			 $(this).next('.ui-dialog').andSelf().wrapAll('<div class="w3exabe w3exabedel" />');
		});
		$("#addproddialog").html('');
		var hasattributes = false;
		var islinkedediting = $('#linkededit').is(':checked');
		var trans_attributes = "Attributes";
		var trans_select = "Select";
		var trans_bulkadd = "Bulk Add";
		var trans_addsingle = "Add Single Variation"
		var trans_seldoesnot = "Selected product does not have any attributes";
		var trans_linkednote = "Note ! - Linked editing is turned on, all new variations will be added to all of the selected products. A large number of products * variations can cause a php timeout";
		if(W3Ex.trans_attributes !== undefined && W3Ex.trans_attributes !== "")
			trans_attributes = W3Ex.trans_attributes;
		if(W3Ex.trans_select !== undefined && W3Ex.trans_select !== "")
			trans_select = W3Ex.trans_select;
		if(W3Ex.trans_bulkadd !== undefined && W3Ex.trans_bulkadd !== "")
			trans_bulkadd = W3Ex.trans_bulkadd;
		if(W3Ex.trans_addsingle !== undefined && W3Ex.trans_addsingle !== "")
			trans_addsingle = W3Ex.trans_addsingle;
		if(W3Ex.trans_seldoesnot !== undefined && W3Ex.trans_seldoesnot !== "")
			trans_seldoesnot = W3Ex.trans_seldoesnot;
		if(W3Ex.trans_linkednote !== undefined && W3Ex.trans_linkednote !== "")
			trans_linkednote = W3Ex.trans_linkednote;
		if(islinkedediting)
		{
			$("#addproddialog").append('<div>'+trans_linkednote+'.</div>');
			$("#addproddialog").append('</br><div id="attributeplaceholder"><div class="variationholder">'+trans_select+' \
			<input id="selectattributes" class="button" style="vertical-align:middle;" type="button" value="'+trans_attributes+'" /></div></div><div style="clear:both;"></div></br>' );
			
		}else
		{
			if(_selectedParent.post_type == 'product')
			{
				for (var key in _selectedParent) 
				{
		 			 if (_selectedParent.hasOwnProperty(key)) 
					 {
		  	  		 	 if(_mapfield[key] === undefined) continue;
		  	  			 var col = _idmap[_mapfield[key]];
			  			 if(col === undefined) continue;
						 if(true === col.attribute)
						 {
						 	if(_selectedParent[col.field + '_ids'] !== undefined && _selectedParent[col.field + '_ids'] != "")
							{
								hasattributes = true;
								var attrname = col.name.replace('(attr) ','');
								$("#addproddialog").append('<div class="variationholder">' +attrname+' \
								<input data-id="'+col.field+'" style="vertical-align:middle;" class="button addbulkvars" type="button" value="'+trans_bulkadd+'" /></div>' );
							}
						 }
					 }
				}
				$("#addproddialog").append('<div style="clear:both;"></div></br>');
				if(!hasattributes)
				{
					$("#addproddialog").append(''+trans_seldoesnot+' !');
				}else
				{
					$("#addproddialog").append('<input id="addsinglevar" style="vertical-align:middle;" class="button" type="button" value="'+trans_addsingle+'" /></br>');
					$("#addproddialog").append('<div id="variationholder"></div>');
				}
				
			}
		}
	},
	close: function( event, ui ) {
		$(".w3exabedel").contents().unwrap();
	},
 	buttons: {
	"OK": function() 
	{	
		for (var key in _varstocreate) 
		{
		  if (_varstocreate.hasOwnProperty(key)) 
		  {
		      delete _varstocreate[key];
		  }
		}
		var counter = 0;
		$('.addedvariation').each(function ()
		{
			var $div = $(this);
			if($('#linkededit').is(':checked'))
			{
				//find all parents
				var parentids = [];
				var selectedRows = _grid.getSelectedRows();
				for(var irow=0; irow < selectedRows.length; irow++)
				{
					var rowid = selectedRows[irow];
					if(rowid === undefined) continue;
					if(_data[rowid] === undefined) continue;
					var selitem = _data[rowid];
					if(selitem.post_type == 'product_variation')
					{
						var hasitalready = false;
						for (var i = 0; i < parentids.length; i++) 
						{
					        if (parentids[i] == selitem.post_parent) 
							{
								hasitalready = true;
					            break;
					        }
					    }
						if(!hasitalready)
						{
							parentids.push(selitem.post_parent);
						}
						continue;
					}else
					{//maybe inserted from child ?
						var hasitalready = false;
						for (var i = 0; i < parentids.length; i++) 
						{
					        if (parentids[i] == selitem.ID) 
							{
								hasitalready = true;
					            break;
					        }
					    }
						if(!hasitalready)
						{
							parentids.push(selitem.ID);
						}
					}
				}
				
				
					for (var j = 0; j < parentids.length; j++) 
					{
						$div.find('select').each(function ()
			   			{
							var attname = $(this).attr('data-id');
							if( _idmap[_mapfield[attname]] === undefined) return true;
							if(_varstocreate[counter.toString()] === undefined)
					    	_varstocreate[counter.toString()] = [];
					    	_varstocreate[counter.toString()].push(parentids[j] + '$#' +attname + '$#' + $(this).val());
						})
						counter++;
				    }
					
					 
			   
			}else
			{
				$div.find('select').each(function ()
			    {
					  var attname = $(this).attr('data-id');
					  if( _idmap[_mapfield[attname]] === undefined) return true;
					  if(_varstocreate[counter.toString()] === undefined)
					   	  _varstocreate[counter.toString()] = [];
	//					   _arr[key].push(selitem.ID + '$#' + selitem.post_parent + '$#' + valtoinsert);
					  _varstocreate[counter.toString()].push(_selectedParent.ID + '$#' +attname + '$#' + $(this).val());
					 
			    })
			}
			counter++;
		})
		for (var key in _varstocreate) 
		{
		  if (_varstocreate.hasOwnProperty(key)) 
		  {
		      _varstocreate[key] = _varstocreate[key].join('#$');
		  }
		}
		_hascreation = true;
		SaveChanges('savechanges');
//		CreateVariations();
	  	$( this ).dialog( "close" );
	},
	Cancel: function()
	{
		  $( this ).dialog( "close" );
	}
	}
});

	$('body').on('mouseleave','#deletearea',function()
	{
		$(this).find('div.deleteextra').remove();
	});
	
	$('body').on('click','#deletebutr',function()
	{
		var selectedRows = _grid.getSelectedRows();
		if(selectedRows.length <= 0)
		{
			 $('.deleteextra').remove();
			 return;
		}
		
		if( $('input[name=deletewhat]').length > 0)
			_deletetype = $('input[name=deletewhat]:checked').val();

		$('.deleteextra').remove();
		_confirmationclick = "delete";
		$("#confirmdialog").dialog("open");	
	});
	
	$("#confirmdialog").dialog({			
    autoOpen: false,
    height: 140,
    width: 380,
    modal: true,
	draggable:true,
	resizable:false,
	title:"Confirm Action",
	closeOnEscape: true,
	create: function (event, ui) {
        $(this).dialog('widget')
            .css({ position: 'fixed'})
    },
	open: function( event, ui ) {
		 var d = $('.ui-dialog:visible');
		 $(d).css('z-index',300002);
		 $('.ui-dialog:visible').wrap('<div class="w3exabe w3exabedel" />');
		  $('.ui-widget-overlay').each(function () {
			 $(this).next('.ui-dialog').andSelf().wrapAll('<div class="w3exabe w3exabedel" />');
		});
		 $('#confirmdialog').css('height','auto');
		 _cancontinueconfirm = false;
	},
	close: function( event, ui ) {
		$(".w3exabedel").contents().unwrap();
	},
 	buttons: {
	  "OK": function() 
	  {
	  	if(_confirmationclick === "delete")
		{
			 _cancontinueconfirm = true;
		
	  	 	$( this ).dialog( "close" );
		 	 DeleteProducts(_deletetype);
		}else if(_confirmationclick === "save")
		{
		 	$( this ).dialog( "close" );
			SaveChanges();
		}else
		{
			$( this ).dialog( "close" );
		}
	  	
	  },
	  Cancel: function()
	  {
	  	  _cancontinueconfirm = false;
		  $( this ).dialog( "close" );
	  }
	 }
});

	function DeleteProducts(type)
	{
		var selectedRows = _grid.getSelectedRows();
//		alert('Disabled in demo');
//		return;
		var ajaxarr = {};
		ajaxarr.action = 'wpmelon_adv_bulk_edit';
		ajaxarr.type = 'deleteproducts';
		ajaxarr.nonce = W3ExABE.nonce;
		var selectedRows = _grid.getSelectedRows();
		var _arrData = [];
		var _arr = {};
		_arr['post_status'] = [];
		var _arrParents = [];
		for(var ir=0; ir < selectedRows.length; ir++)
		{
			var rowid = selectedRows[ir];
			if(rowid === undefined) continue;
			if(_data[rowid] === undefined) continue;
			var selitem = _data[rowid];
		  	_arr['post_status'].push(selitem.ID + '$#' + selitem.post_parent + '$#' + selitem.post_status);
		}
		var bcon = false;
		for (var key in _arr) 
		{
		  if (_arr.hasOwnProperty(key)) 
		  {
		      _arr[key] = _arr[key].join('#$');
			  bcon = true;
		  }
		}
		if(!bcon) return;
		
		$('#myGrid').prepend('<div id="dimgrid" style="position: absolute;top:0;left:0;width: 100%;height:100%;z-index:102;opacity:0.4;filter: alpha(opacity = 40);background-color:grey;"></div>');
		DisableAllControls(true);
		
		ajaxarr.data = _arr;
		ajaxarr.deletetype = type;
		jQuery.ajax({
		     type : "post",
		     dataType : "json",
		     url : W3ExABE.ajaxurl,
		     data : ajaxarr,
		     success: function(response) {
			 		selectedRows.sort(function(a, b){return a-b});
					var deleteRows = [];
					for(var i=0;i < selectedRows.length;i++ )
					{
						var rowid = selectedRows[i];
						if(rowid === undefined) continue;
						if(_data[rowid] === undefined) continue;
						if($.inArray(rowid,deleteRows) === -1)
							deleteRows.push(rowid);
						var selitem = _data[rowid];
						if(selitem.haschildren !== undefined)
						{
							var parentid = selitem.ID;
							for(var j=0;j < _data.length;j++ )
							{
								var selitemin = _data[j];
								if(selitemin === undefined) continue;
								if(selitemin.ID == parentid) continue;
								if(selitemin.post_parent == parentid)
								{
									if($.inArray(j,deleteRows) === -1)
										deleteRows.push(j);
								}
							}
						}
					}
					
					deleteRows.sort(function(a, b){return a-b});
					if(_totalrecords !== -1)
					{
						_totalrecords-= deleteRows.length;
						$('#totalrecords').text(_totalrecords);
					}
					var delrowslength = deleteRows.length;
					var objdelmap = {};
					while(deleteRows.length > 0) 
					{
						var rowid = deleteRows[deleteRows.length -1];
						if(rowid === undefined)
						{
							 deleteRows.pop();
							 continue;
						}
						if(_data[rowid] === undefined)
						{
							 deleteRows.pop();
							 continue;
						}
							_data.splice(rowid,1);
						if(_arrEdited[rowid] !== undefined)
							_arrEdited.splice(rowid,1);
						if(_changed[rowid] !== undefined)
							delete _changed[rowid];
						for(var ir=0; ir < _arrEdited.length; ir++)
						{
							var row = _arrEdited[ir];
							if(row === undefined) continue;
							if(ir > rowid)
							{
								if(objdelmap[ir] === undefined)
								{
									objdelmap[ir] = 1;
								}else
								{
									var temp = parseInt(objdelmap[ir]);
									temp++;
									objdelmap[ir] = temp;
								}
							}
						}
					    deleteRows.pop();
					}
					if(delrowslength > 0)
					{
						for(var ir=0; ir < _arrEdited.length; ir++)
						{
							var row = _arrEdited[ir];
							if(row === undefined) continue;
							if(objdelmap[ir] !== undefined)
							{
								if(ir-objdelmap[ir] >= 0)
								{
									_arrEdited[ir-objdelmap[ir]] = row;
									delete _arrEdited[ir];
								}
							}
							
						}
						var arrchangedkeys = [];
						for (var key in _changed) 
						{
						  if (_changed.hasOwnProperty(key)) 
						  {
						     arrchangedkeys.push(key);
						  }
						}
						arrchangedkeys.sort(function(a, b){return a-b});
						for(var ir=0; ir < arrchangedkeys.length; ir++)
						{
							var row = arrchangedkeys[ir];
							if(row === undefined) continue;
							if(_changed[row] === undefined) continue;
							if(objdelmap[row] !== undefined)
							{
								if(row-objdelmap[row] >= 0)
								{
									_changed[row-objdelmap[row]] = _changed[row];
									delete _changed[row];
								}
							}
						}
					}
					
						  	
					try{
						_grid.removeCellCssStyles("changed");
						_grid.setCellCssStyles("changed", _changed);
					} catch (err) {
						;
					}
					_grid.setSelectedRows([]);
					$('#dimgrid').remove();
					DisableAllControls(false);
					$('.showajax').remove();
					var all = _data.length;
					var seltext = ' 0 of ' + all;
					$('#bulkeditinfo').text(seltext);
					RefreshGroupedItems();
					_shouldhandle = false;
					_grid.resetActiveCell();
					_grid.invalidate();
					_shouldhandle = true;	
		     },
			 complete:function (args)
			 {
			  	//uncomment to debug
//			    $('#debuginfo').html(args.responseText);
			 }, error:function (xhr, status, error) 
			  {
			  	//uncomment to debug
				  $('#dimgrid').remove();
				  $('.showajax').remove();
				  DisableAllControls(false);
				  $('#debuginfo').html(xhr.responseText);
			  }
		  }) ;
	}
	
			
	$('body').on('change','.bulkselect',function()
    {
    	var what = $(this).val();
		if(what == "replace")
		{
			$(this).parent().parent().find('.divwithvalue').show();
			$(this).parent().parent().find('.labelignorecase').show();
		}else
		{
			$(this).parent().parent().find('.divwithvalue').hide();
			$(this).parent().parent().find('.labelignorecase').hide();
		}
	})
	
	$('body').on('click','.deletecustomfield',function()
	{
		var $trtohide = $(this).parents('tr.trcustom');	
		var ctext = $trtohide.find('td:first').text();
		_changedcustom.push(ctext);
		if(_mapfield[ctext] !== undefined)
		{
			if(_idmap[_mapfield[ctext]] !== undefined)
			{
				_idmap[_mapfield[ctext]].isdeleted = true;
			}
		}
		$trtohide.hide(500);//,function ()
//		{
//		  $trtohide.remove();
//		})
	})

	$('body').on('click','.editorcats .clearothersattr input',function()
	{
		var bcheck = true;
		if(!$(this).is(':checked'))
		{
			bcheck = false;
			var val = $(this).attr('value');
			$('.editorcats  .clearothersattr input').each(function ()
			 {
			 	 if( $(this).attr('value') !== val)
				 {
				 	if($(this).is(':checked'))
					{
						bcheck = true;
						return;
					}
				 }
			 })
		}
		
		
		$('.clearothersattr input').prop('checked', false);
		if(bcheck)
			$(this).prop('checked',true);
	})
	
	$('body').on('click','.editorcats .clearothers input',function()
	{
		/*var bcheck = true;
		if(!$(this).is(':checked'))
		{
			bcheck = false;
			var val = $(this).attr('value');
			$('.editorcats  .clearothers input').each(function ()
			 {
			 	 if( $(this).attr('value') !== val)
				 {
				 	if($(this).is(':checked'))
					{
						bcheck = true;
						return;
					}
				 }
			 })
		}
		*/
		
		$('.clearothers input').prop('checked', false);
//		if(bcheck)
		$(this).prop('checked','checked');
	})

	
	$("#addcustomfield").click(function ()
	 {
	 	$(this).hide();
		$('#fieldname').val('');
		$('#fieldtype').val('text');
		$('#fieldvisible').val('yes');
		$('#extracustominfo').html('');
		$('.addcontrols').fadeIn();
		$('.addokcancel').fadeIn();
//		$("#addok").button("disable");
	 })
	 
	/* $("#fieldname").keyup(function() 
	 {
		var text = $(this).val();
		text = $.trim(text);
		if(text == "")
		{
			$("#addok").button("disable");
		}else
		{
			$("#addok").button("enable");
		}
	 })*/
	 
	
	 $("#addcancel").click(function ()
	 {
	 	$('#addcustomfield').show();
		$('.addcontrols').hide();
		$('.addokcancel').hide();
	 })
			
			
    columns.push(checkboxSelector.getColumnDefinition());
	
 	function formatter(row, cell, value, columnDef, dataContext) {
        return value;
    }
	
	var SCOPE = {
		ALL:0,
		PRODALL:1,
		PRODS:2,
		VAR:3,
		PRODSVAR:4,
		PRODSWITHVARS:5,
		NONE:6
	}
	
	var _mapfield  = 
	{
		'ID':0,'post_title':1,'_thumbnail_id':2,'_product_image_gallery':3,'post_content':4,'post_excerpt':5,'post_name':6,'post_date':7,'_sku':8,'product_cat':9,'product_tag':10,'_regular_price':11,'_sale_price':12,'_sale_price_dates_from':13,'_sale_price_dates_to':14,'_featured':15,'_tax_status':16,'_tax_class':17,'_weight':18,'_height':19,'_width':20,'_length':21,'_stock':22,'_stock_status':23,'_manage_stock':24,'_backorders':25,'_sold_individually':26,'product_shipping_class':27,'grouped_items':28,'_product_adminlink':29,'_purchase_note':30,'post_status':31,'_visibility':32,'_upsell_ids':33,'_crosssell_ids':34,'_downloadable':35,'_virtual':36,'_download_expiry':37,'_download_limit':38,'_downloadable_files':39,'_download_type':40,'_product_url':41,'_button_text':42,'comment_status':43,'menu_order':44,'product_type':45,'_product_permalink':46,'_default_attributes':47
	};
	
	var _idmap = [
		{id:'ID',field:'ID',name:'ID',visible:true,type:'int'},
		{id:'post_title',field:'post_title',name:'Title',scope:SCOPE.PRODALL,visible:true,width: 270},
		{id:'_thumbnail_id',field:'_thumbnail_id',name:'Image',type:'image',image:true},
		{id:'_product_image_gallery',field:'_product_image_gallery',name:'Image Gallery',type:'image_gallery',image_gallery:true,scope:SCOPE.PRODALL},
		{id:'post_content',field:'post_content',name:'P. Description',tooltip:'Product Description',width: 170,textarea:true,scope:SCOPE.PRODALL},
		{id:'post_excerpt',field:'post_excerpt',name:'P. Excerpt',tooltip:'Product Short Description',width: 170,textarea:true,scope:SCOPE.PRODALL},
		{id:'post_name',field:'post_name',name:'Slug',tooltip:'Product Slug',scope:SCOPE.PRODALL},
		{id:'post_date',field:'post_date',name:'Publish Date',scope:SCOPE.PRODALL,width:100,date:true},
		{id:'_sku',field:'_sku',name:'SKU',scope:SCOPE.PRODSVAR},
		{id:'product_cat',field:'product_cat',name:'Categories',width:130,category:true,scope:SCOPE.PRODALL,type:'customtaxh'},
		{id:'product_tag',field:'product_tag',name:'Tags',width:110,category:true,scope:SCOPE.PRODALL,type:'customtax',isnewvals:true},
		{id:'_regular_price',field:'_regular_price',name:'Price',scope:SCOPE.PRODSVAR,width:80,type:'float2'},
		{id:'_sale_price',field:'_sale_price',name:'Sale Price',scope:SCOPE.PRODSVAR,width:80,type:'float2'},
		{id:'_sale_price_dates_from',field:'_sale_price_dates_from',name:'Sale From',scope:SCOPE.PRODSVAR,width:100,date:true},
		{id:'_sale_price_dates_to',field:'_sale_price_dates_to',name:'Sale To',scope:SCOPE.PRODSVAR,width:100,date:true},
		{id:'_featured',field:'_featured',name:'Featured',scope:SCOPE.PRODALL,type:'set',checkbox:true},
		{id:'_tax_status',field:'_tax_status',name:'Tax Status',type:'set',options: "Taxable,Shipping only,None",scope:SCOPE.PRODALL},
		{id:'_tax_class',field:'_tax_class',name:'Tax Class',type:'set',options: "Standard,Reduced Rate,Zero Rate"},
		{id:'_weight',field:'_weight',name:'Weight',scope:SCOPE.PRODSVAR,type:'float3'},
		{id:'_height',field:'_height',name:'Height',scope:SCOPE.PRODSVAR,type:'float3'},
		{id:'_width',field:'_width',name:'Width',scope:SCOPE.PRODSVAR,type:'float3'},
		{id:'_length',field:'_length',name:'Length',scope:SCOPE.PRODSVAR,type:'float3'},
		{id:'_stock',field:'_stock',name:'Stock Q.',tooltip:'Stock Quantity',width:80,type:'int'},
		{id:'_stock_status',field:'_stock_status',name:'Stock Status',width:80,checkbox:true,type:'set'},
		{id:'_manage_stock',field:'_manage_stock',name:'Manage Stock',checkbox:true,type:'set'},
		{id:'_backorders',field:'_backorders',name:'Backorders',options: "Do not allow,Allow but notify,Allow",type:'set'},
		{id:'_sold_individually',field:'_sold_individually',name:'Sold Individually',checkbox:true,type:'set',scope:SCOPE.PRODALL},
		{id:'product_shipping_class',field:'product_shipping_class',name:'Shipping class',width:130,category:true,type:'customtaxh'},
		{id:'grouped_items',field:'grouped_items',name:'Grouping',scope:SCOPE.PRODALL,width: 100,type:'customtaxh'},
		{id:'_product_adminlink',field:'_product_adminlink',name:'Edit in admin',scope:SCOPE.NONE,width: 170,url:true},
		{id:'_purchase_note',field:'_purchase_note',name:'Purchase Note',textarea:true},
		{id:'post_status',field:'post_status',name:'Publish',tooltip:'Product Status',width:70,options: "publish,draft,private",type:'set'},
		{id:'_visibility',field:'_visibility',name:'Visibility',tooltip:'Catalog Visibility',width:90,options: "Catalog/search,Catalog,Search,Hidden",scope:SCOPE.PRODALL,type:'set'},
		{id:'_upsell_ids',field:'_upsell_ids',name:'Up-Sells',scope:SCOPE.PRODALL},
		{id:'_crosssell_ids',field:'_crosssell_ids',name:'Cross-Sells',scope:SCOPE.PRODALL},
		{id:'_downloadable',field:'_downloadable',name:'Downloadable',checkbox:true,scope:SCOPE.PRODSVAR,type:'set'},
		{id:'_virtual',field:'_virtual',name:'Virtual',checkbox:true,scope:SCOPE.PRODSVAR,type:'set'},
		{id:'_download_expiry',field:'_download_expiry',name:'D. Expiry',tooltip:'Download Expiry',scope:SCOPE.PRODSVAR,type:'int'},
		{id:'_download_limit',field:'_download_limit',name:'D. Limit',tooltip:'Download Limit',scope:SCOPE.PRODSVAR,type:'int'},
		{id:'_downloadable_files',field:'_downloadable_files',name:'D. Files',tooltip:'Downloadable Files',files:true,width:90,scope:SCOPE.PRODSVAR},
		{id:'_download_type',field:'_download_type',name:'D. Type',tooltip:'Download Type',options: "Standard,Application,Music",width:70,scope:SCOPE.PRODSVAR,type:'set'},	
		{id:'_product_url',field:'_product_url',name:'Product URL(ext. prod.)',scope:SCOPE.PRODALL,width: 170},
		{id:'_button_text',field:'_button_text',name:'But. Text',tooltip:'Button Text'},
		{id:'comment_status',field:'comment_status',name:'Reviews',tooltip:'Enable Reviews',checkbox:true,scope:SCOPE.PRODALL,type:'set'},
		{id:'menu_order',field:'menu_order',name:'Menu Order',width:80,type:'int'},
		{id:'product_type',field:'product_type',name:'Prod. Type',tooltip:'Product Type',scope:SCOPE.PRODALL,width: 100,type:'customtaxh'},
		{id:'_product_permalink',field:'_product_permalink',name:'Product URL(permalink)',scope:SCOPE.NONE,width: 170,url:true},
		{id:'_default_attributes',field:'_default_attributes',name:'Default Attributes',scope:SCOPE.PRODSWITHVARS,width: 90,defattrs:true}
	];
	
	
	 
	function escapeRegExp(string) {
	    return string.replace(/([.*+?^=!:${}()|\[\]\/\\])/g, "\\$1");
	}

	function replaceAll(string, find, replace) {
	  return string.replace(new RegExp(escapeRegExp(find), 'g'), replace);
	}

	function AddBulkAndSelectFieldsAttributes(attr_slug,attr_name)
	{
		var newhtml = "";
		if(W3Ex[attr_slug + 'bulk'] !== undefined)
			newhtml = W3Ex[attr_slug + 'bulk'];
		var contains = "contains";
		var doesnot = "does not contain";
		var starts = "starts with";
		var ends = "ends with";
		var isempty = "field is empty";
		if(W3Ex.trans_contains !== undefined && W3Ex.trans_contains !== "")
			contains = W3Ex.trans_contains;
		if(W3Ex.trans_doesnot !== undefined && W3Ex.trans_doesnot !== "")
			doesnot = W3Ex.trans_doesnot;
		if(W3Ex.trans_starts !== undefined && W3Ex.trans_starts !== "")
			starts = W3Ex.trans_starts;
		if(W3Ex.trans_ends !== undefined && W3Ex.trans_ends !== "")
			ends = W3Ex.trans_ends;
		if(W3Ex.trans_isempty !== undefined && W3Ex.trans_isempty !== "")
			isempty = W3Ex.trans_isempty;
		$('#bulkdialog table').append(newhtml); 
//			if(customobj.isvisible)
//				$("#bulkdialog tr[data-id='" + attr_slug + "']").show();
		newhtml = '<tr data-id="'+attr_slug+'">\
					<td>\
						'+attr_name+'\
					</td>\
					<td>\
						 <select id="select'+attr_slug+'" class="selectselect" data-id="'+attr_slug+'">\
							<option value="con">'+contains+'</option>\
							<option value="notcon">'+doesnot+'</option>\
							<option value="start">'+starts+'</option>\
							<option value="end">'+ends+'</option>\
							<option value="empty">'+isempty+'</option>\
						</select>\
					</td>\
					<td>\
						<input id="select'+attr_slug+'value" type="text" placeholder="Skipped (empty)" data-id="'+attr_slug+'" class="selectvalue"/>\
					</td>\
					<td>\
					<label><input data-id="'+attr_slug+'" class="selectifignorecase" type="checkbox"> Ignore case</label>\
					</td>\
				</tr>';
		$('#selectdialog table').append(newhtml); 
//			if(customobj.isvisible)
//				$("#selectdialog tr[data-id='" + attr_slug + "']").show();
		
	}
	
	function AddBulkAndSelectFields(customobj)
	{
		var newhtml = "";
		var contains = "contains";
		var doesnot = "does not contain";
		var starts = "starts with";
		var ends = "ends with";
		var isempty = "field is empty";
		var setnew = "set new";
		var prepend = "prepend";
		var append = "append";
		var replacet = "replacetext";
		var incbyvalue = "increase by value";
		var decbyvalue = "decrease by value";
		var incbyper = "increase by %";
		var decbyper = "decrease by %";
		if(W3Ex.trans_contains !== undefined && W3Ex.trans_contains !== "")
			contains = W3Ex.trans_contains;
		if(W3Ex.trans_doesnot !== undefined && W3Ex.trans_doesnot !== "")
			doesnot = W3Ex.trans_doesnot;
		if(W3Ex.trans_starts !== undefined && W3Ex.trans_starts !== "")
			starts = W3Ex.trans_starts;
		if(W3Ex.trans_ends !== undefined && W3Ex.trans_ends !== "")
			ends = W3Ex.trans_ends;
		if(W3Ex.trans_isempty !== undefined && W3Ex.trans_isempty !== "")
			isempty = W3Ex.trans_isempty;
		if(W3Ex.trans_incbyvalue !== undefined && W3Ex.trans_incbyvalue !== "")
			incbyvalue = W3Ex.trans_incbyvalue;
		if(W3Ex.trans_decbyvalue !== undefined && W3Ex.trans_decbyvalue !== "")
			decbyvalue = W3Ex.trans_decbyvalue;
		if(W3Ex.trans_incbyper !== undefined && W3Ex.trans_incbyper !== "")
			incbyper = W3Ex.trans_incbyper;
		if(W3Ex.trans_decbyper !== undefined && W3Ex.trans_decbyper !== "")
			decbyper = W3Ex.trans_decbyper;
		if(W3Ex.trans_setnew !== undefined && W3Ex.trans_setnew !== "")
			setnew = W3Ex.trans_setnew;
		if(W3Ex.trans_append !== undefined && W3Ex.trans_append !== "")
			append = W3Ex.trans_append;
		if(W3Ex.trans_prepend !== undefined && W3Ex.trans_prepend !== "")
			prepend = W3Ex.trans_prepend;
		if(W3Ex.trans_replacetext !== undefined && W3Ex.trans_replacetext !== "")
			replacet = W3Ex.trans_replacetext;
//		if(W3Ex.trans_ends !== undefined && W3Ex.trans_ends !== "")
//			ends = W3Ex.trans_ends;
//		if(W3Ex.trans_isempty !== undefined && W3Ex.trans_isempty !== "")
//			isempty = W3Ex.trans_isempty;
		if(customobj.type == "text" || customobj.type == "custom" || customobj.type == "customh")
		{
			newhtml = '<tr data-id="'+customobj.name+'"> \
						<td> \
							'+customobj.name+'\
						</td>\
						<td>\
							 <select id="bulk'+customobj.name+'" class="bulkselect">\
								<option value="new">'+setnew+'</option>\
								<option value="prepend">'+prepend+'</option>\
								<option value="append">'+append+'</option>\
								<option value="replace">'+replacet+'</option>\
							</select>\
							<label class="labelignorecase" style="display:none;">\
							<input class="inputignorecase" type="checkbox">\
							Ignore case</label>\
						</td>\
						<td>\
							<input id="bulk'+customobj.name+'value" type="text" data-id="'+customobj.name+'" class="bulkvalue" placeholder="Skipped (empty)"/>\
						</td>\
						<td>\
							<div class="divwithvalue" style="display:none;">with text <input class="inputwithvalue" type="text"></div>\
						</td>\
					</tr>';
			if(W3Ex[customobj.name + 'bulk'] !== undefined)
				newhtml = W3Ex[customobj.name + 'bulk'];
			$('#bulkdialog table').append(newhtml); 
			if(customobj.isvisible)
				$("#bulkdialog tr[data-id='" + customobj.name + "']").show();
			newhtml = '<tr data-id="'+customobj.name+'">\
						<td>\
							'+customobj.name+'\
						</td>\
						<td>\
							 <select id="select'+customobj.name+'" class="selectselect" data-id="'+customobj.name+'">\
								<option value="con">'+contains+'</option>\
								<option value="notcon">'+doesnot+'</option>\
								<option value="start">'+starts+'</option>\
								<option value="end">'+ends+'</option>\
								<option value="empty">'+isempty+'</option>\
							</select>\
						</td>\
						<td>\
							<input id="select'+customobj.name+'value" type="text" placeholder="Skipped (empty)" data-id="'+customobj.name+'" class="selectvalue"/>\
						</td>\
						<td>\
						<label><input data-id="'+customobj.name+'" class="selectifignorecase" type="checkbox"> Ignore case</label>\
						</td>\
					</tr>';
//			if(W3Ex[customobj.name + 'select'] !== undefined)
//				newhtml = W3Ex[customobj.name + 'select'];
			$('#selectdialog table').append(newhtml); 
			if(customobj.isvisible)
				$("#selectdialog tr[data-id='" + customobj.name + "']").show();
		}else if(customobj.type == "multitext")
		{
			newhtml = '<tr data-id="'+customobj.name+'"> \
						<td> \
							'+customobj.name+'\
						</td>\
						<td>\
							 <select id="bulk'+customobj.name+'" class="bulkselect">\
								<option value="new">'+setnew+'</option>\
								<option value="prepend">'+prepend+'</option>\
								<option value="append">'+append+'</option>\
								<option value="replace">'+replacet+'</option>\
							</select>\
							<label class="labelignorecase" style="display:none;">\
							<input class="inputignorecase" type="checkbox">\
							Ignore case</label>\
						</td>\
						<td>\
							<textarea id="bulk'+customobj.name+'value" rows="1" cols="15" data-id="'+customobj.name+'" class="bulkvalue" placeholder="Skipped (empty)"></textarea>\
						</td>\
						<td>\
							<div class="divwithvalue" style="display:none;">with text <textarea class="inputwithvalue" rows="1" cols="15"></textarea></div>\
						</td>\
					</tr>';
			$('#bulkdialog table').append(newhtml); 
			if(customobj.isvisible)
				$("#bulkdialog tr[data-id='" + customobj.name + "']").show();
			newhtml = '<tr data-id="'+customobj.name+'">\
						<td>\
							'+customobj.name+'\
						</td>\
						<td>\
							 <select id="select'+customobj.name+'" class="selectselect" data-id="'+customobj.name+'">\
								<option value="con">'+contains+'</option>\
								<option value="notcon">'+doesnot+'</option>\
								<option value="start">'+starts+'</option>\
								<option value="end">'+ends+'</option>\
								<option value="empty">'+isempty+'</option>\
							</select>\
						</td>\
						<td>\
							<textarea cols="15" rows="1" id="select'+customobj.name+'value" placeholder="Skipped (empty)" data-id="'+customobj.name+'" class="selectvalue"></textarea >\
						</td>\
						<td>\
						<label><input data-id="'+customobj.name+'" class="selectifignorecase" type="checkbox"> Ignore case</label>\
						</td>\
					</tr>';
			$('#selectdialog table').append(newhtml); 
			if(customobj.isvisible)
				$("#selectdialog tr[data-id='" + customobj.name + "']").show();
		}else if(customobj.type == "integer")
		{
			newhtml = '<tr data-id="'+customobj.name+'">\
						<td>\
							'+customobj.name+'\
						</td>\
						<td>\
							 <select id="bulk'+customobj.name+'" data-id="'+customobj.name+'">\
								<option value="new">'+setnew+'</option>\
								<option value="incvalue">increase by value</option>\
								<option value="decvalue">decrease by value</option>\
							</select>\
						</td>\
						<td>\
							<input id="bulk'+customobj.name+'value" type="text" data-id="'+customobj.name+'" class="bulkvalue" placeholder="Skipped (empty)"/>\
						</td>\
						<td>\
							\
						</td>';
			$('#bulkdialog table').append(newhtml); 
			if(customobj.isvisible)
				$("#bulkdialog tr[data-id='" + customobj.name + "']").show();
			newhtml = '<tr data-id="'+customobj.name+'">\
						<td>\
							'+customobj.name+'\
						</td>\
						<td>\
							 <select id="select'+customobj.name+'" class="selectselect" data-id="'+customobj.name+'">\
								<option value="more">></option>\
								<option value="less"><</option>\
								<option value="equal">==</option>\
								<option value="moree">>=</option>\
								<option value="lesse"><=</option>\
								<option value="empty">'+isempty+'</option>\
							</select>\
						</td>\
						<td>\
							<input id="select'+customobj.name+'value" type="text" placeholder="Skipped (empty)" data-id="'+customobj.name+'" class="selectvalue" />\
						</td>\
						<td>\
						</td>\
					</tr>';
			$('#selectdialog table').append(newhtml); 
			if(customobj.isvisible)
				$("#selectdialog tr[data-id='" + customobj.name + "']").show();
		}else if(customobj.type == "decimal" || customobj.type == "decimal3")
		{
			newhtml = '<tr data-id="'+customobj.name+'">\
						<td>\
							'+customobj.name+'\
						</td>\
						<td>\
							 <select id="bulk'+customobj.name+'" data-id="'+customobj.name+'">\
								<option value="new">'+setnew+'</option>\
								<option value="incvalue">increase by value</option>\
								<option value="decvalue">decrease by value</option>\
								<option value="incpercent">increase by %</option>\
								<option value="decpercent">decrease by %</option>\
							</select>\
						</td>\
						<td>\
							<input id="bulk'+customobj.name+'value" type="text" data-id="'+customobj.name+'" class="bulkvalue" placeholder="Skipped (empty)" />\
						</td>\
						<td>\
							\
						</td>\
					</tr>';
			$('#bulkdialog table').append(newhtml); 
			if(customobj.isvisible)
			{
				$("#bulkdialog tr[data-id='" + customobj.name + "']").show();
			}
			newhtml = '<tr data-id="'+customobj.name+'">\
						<td>\
							'+customobj.name+'\
						</td>\
						<td>\
							 <select id="select'+customobj.name+'" class="selectselect" data-id="'+customobj.name+'">\
								<option value="more">></option>\
								<option value="less"><</option>\
								<option value="equal">==</option>\
								<option value="moree">>=</option>\
								<option value="lesse"><=</option>\
								<option value="empty">'+isempty+'</option>\
							</select>\
						</td>\
						<td>\
							<input id="select'+customobj.name+'value" type="text" placeholder="Skipped (empty)" data-id="'+customobj.name+'" class="selectvalue" />\
						</td>\
						<td>\
						</td>\
					</tr>';
			$('#selectdialog table').append(newhtml); 
			if(customobj.isvisible)
				$("#selectdialog tr[data-id='" + customobj.name + "']").show();
		}
		else if(customobj.type == "checkbox")
		{
			newhtml = '<tr data-id="'+customobj.name+'">\
					<td>\
						<input id="set'+customobj.name+'" type="checkbox" class="bulkset" data-id="'+customobj.name+'"><label for="set'+customobj.name+'">Set '+customobj.name+'</label>\
					</td>\
					<td>\
					</td>\
					<td>\
						 <select id="bulk'+customobj.name+'">\
						<option value="yes">Yes</option>\
						<option value="no">No</option>\
					</select>\
					</td>\
					<td>\
					</td>\
				</tr>';
			$('#bulkdialog table').append(newhtml); 
			if(customobj.isvisible)
			{
				$("#bulkdialog tr[data-id='" + customobj.name + "']").show();
			}
			newhtml = '<tr data-id="'+customobj.name+'">\
						<td>\
							<input id="setsel'+customobj.name+'" type="checkbox" class="selectset" data-id="'+customobj.name+'"><label for="setsel'+customobj.name+'">Where '+customobj.name+' is</label>\
						</td>\
						<td>\
						</td>\
						<td>\
							 <select id="select'+customobj.name+'">\
								<option value="yes">Yes</option>\
								<option value="no">No</option>\
							</select>\
						</td>\
						<td>\
						</td>\
					</tr>';
			$('#selectdialog table').append(newhtml); 
			if(customobj.isvisible)
				$("#selectdialog tr[data-id='" + customobj.name + "']").show();
		}
		else if(customobj.type == "select")
		{//select
			if(customobj.selvals === undefined) return;
			newhtml = '<tr data-id="'+customobj.name+'">\
					<td>\
						<input id="set'+customobj.name+'" type="checkbox" class="bulkset" data-id="'+customobj.name+'"><label for="set'+customobj.name+'">Set '+customobj.name+'</label>\
					</td>\
					<td>\
					</td>\
					<td>\
						 <select id="bulk'+customobj.name+'">';
						 var vals = customobj.selvals.split(',');
						 for(var i = 0; i < vals.length; i++)
						 {
						 	newhtml+= '<option value="' + vals[i] + '">' + vals[i] + '</option>'; 
						 }
					newhtml+='</select>\
					</td>\
					<td>\
					</td>\
				</tr>';
			$('#bulkdialog table').append(newhtml); 
			if(customobj.isvisible)
			{
				$("#bulkdialog tr[data-id='" + customobj.name + "']").show();
			}
			newhtml = '<tr data-id="'+customobj.name+'">\
						<td>\
							<input id="setsel'+customobj.name+'" type="checkbox" class="selectset" data-id="'+customobj.name+'"><label for="setsel'+customobj.name+'">Where '+customobj.name+' is</label>\
						</td>\
						<td>\
						</td>\
						<td>\
							 <select id="select'+customobj.name+'">';
							var vals = customobj.selvals.split(',');
							 for(var i = 0; i < vals.length; i++)
							 {
							 	newhtml+= '<option value="' + vals[i] + '">' + vals[i] + '</option>'; 
							 }
						newhtml+='</select>\
						</td>\
						<td>\
						</td>\
					</tr>';
			$('#selectdialog table').append(newhtml); 
			if(customobj.isvisible)
				$("#selectdialog tr[data-id='" + customobj.name + "']").show();
		}
	}
	
	function ShowCustomSearchFilters()
	{
		var cols = {};
		cols = W3Ex.customfields;
		$('.customfield').remove();
		for (var key in cols) 
		{
		  if (cols.hasOwnProperty(key))
		  {
		  	   var customobj = cols[key];
			   if(customobj === undefined) continue;
			   if(_mapfield[customobj.name] === undefined)
			   		continue;
			   
				
				var text = '<div class="customfield">';
				text+='<label><input type="checkbox" data-id="'+customobj.name+'" data-type="'+customobj.type+'">'+customobj.name+'</input></label></div>';
				$('#pluginsettingstab-2').append(text);
		  }
		}
		$('#pluginsettingstab-2').append('<div style="clear:both;"><div>');
		
		if(W3Ex.customfieldssel !== undefined && $.isArray(W3Ex.customfieldssel))
		{
			var contains = "contains";
			var doesnot = "does not contain";
			var starts = "starts with";
			var ends = "ends with";
			if(W3Ex.trans_contains !== undefined && W3Ex.trans_contains !== "")
				contains = W3Ex.trans_contains;
			if(W3Ex.trans_doesnot !== undefined && W3Ex.trans_doesnot !== "")
				doesnot = W3Ex.trans_doesnot;
			if(W3Ex.trans_starts !== undefined && W3Ex.trans_starts !== "")
				starts = W3Ex.trans_starts;
			if(W3Ex.trans_ends !== undefined && W3Ex.trans_ends !== "")
				ends = W3Ex.trans_ends;
			var selcols = W3Ex.customfieldssel;
			var tdcounter = 0;
			var appendtext = "";
			$('.customfilterstr').remove();
			for(var i = 0; i < selcols.length; i++)
			{
				var selitem = selcols[i];
				if(_mapfield[selitem] === undefined)
			   		continue;
				$('.customfield input[data-id="'+selitem+'"]').prop('checked',true);
				if(cols[selitem] !== undefined)
				{
					 var customobj = cols[selitem];
			  		 appendtext+= '<td>'+customobj['name']+'</td><td><div class="customfieldtable" data-id="'+customobj['name']+'" data-type="'+customobj['type']+'">';
					if(customobj['type'] === "text" || customobj['type'] === "multitext" ||  customobj['type'] === "select" || customobj['type'] === "checkbox")
					{
						appendtext+= '<select> \
					<option value="con">' + contains + '</option> \
					<option value="notcon">' + doesnot + '</option> \
					<option value="start">' + starts + '</option> \
					<option value="end">'+ ends +'</option> \
				</select> \
				<input type="text"/>';
					}else if(customobj['type'] === "customh" || customobj['type'] === "custom")
					{
						if(W3Ex['taxonomyterms' + customobj['name']] !== undefined)
						{
							appendtext+= '<select ' + W3Ex['taxonomyterms' + customobj['name']];
						}
					}else{
						appendtext+=  '<select> \
							<option value="more">></option> \
							<option value="less"><</option> \
							<option value="equal">==</option> \
							<option value="moree">>=</option> \
							<option value="lesse"><=</option> \
						</select> \
						<input type="text"/>';
					}
					appendtext+= '</div></td>';
					if(tdcounter % 2 == 0)
					{
						appendtext = '<tr class="customfilterstr">' + appendtext;
					}else
					{
						appendtext = appendtext + '</tr>';
						$('#tablesearchfilters tbody').append(appendtext);
						appendtext = '';
					}
					tdcounter++;
				}
			}
			if(tdcounter % 2 !== 0 && appendtext !== "")
			{
				appendtext = appendtext + '<td></td><td></td></tr>';
				$('#tablesearchfilters tbody').append(appendtext);
			}
		}
	}
	
	function SetCustomFields()
	{
		if(W3Ex.attr_cols !== undefined)
		{
			var cols = {};
			cols = W3Ex.attr_cols;
			for (var key in cols) {
			  if (cols.hasOwnProperty(key)) {
			  	   var customobj = cols[key];
				   if(customobj === undefined) continue;
				    var insertobj = {};
					var attr_slug = "attribute_pa_" + customobj.value;
					insertobj[attr_slug] = _mapfield.length;
					
					_mapfield[attr_slug] = _idmap.length;
					insertobj.field = attr_slug;
					insertobj.id = insertobj.field;
					insertobj.name = "(attr) " + customobj.attr;
					
					var newitem = {};
					newitem.id = attr_slug;
					newitem.name = attr_slug;
					newitem.field = attr_slug;
					insertobj.type = 'customtaxh';
					insertobj.attribute = true;
					AddBulkAndSelectFieldsAttributes(attr_slug,insertobj.name);
					newitem.sortable = true;
	//				_allcols.push(newitem);
					_idmap.push(insertobj);
	//				if(customobj.isvisible === "true")
	//					gridColumns.push(newitem);
					
			  }
			}
		}
		if(W3Ex.customfields === undefined)
			return;
		
		var cols = {};
		cols = W3Ex.customfields;
		for (var key in cols) {
		  if (cols.hasOwnProperty(key)) {
		  	   var customobj = cols[key];
			   if(customobj === undefined) continue;
			   if(_mapfield[customobj.name] !== undefined)
			   		continue;
			    var insertobj = {};
				insertobj[customobj.name] = _mapfield.length;
				
				_mapfield[customobj.name] = _idmap.length;
				insertobj.field = customobj.name;
				insertobj.id = insertobj.field;
				insertobj.name = insertobj.field;
				
				var newhtml = "<tr class='trcustom'><td data-field='name'><strong>";
			 	var ctext = customobj.name;
				ctext = $.trim(ctext);
				if(ctext == "") return;
				newhtml+= ctext + "</strong></td><td";
				ctext = customobj.type;
				switch(ctext){
				case "text":
				{
					newhtml+= " data-type='text' data-field='type'>type: <strong>Text (single line)</strong></td>";
				}
				break;
				case "multitext":
				{
					newhtml+= " data-type='multitext' data-field='type'>type: <strong>Text (multi line)</strong></td>";
				}
				break;
				case "integer":
				{
					newhtml+= " data-type='integer' data-field='type'>type: <strong>Number (integer)</strong></td>";
				}
				break;
				case "decimal":
				{
					newhtml+= " data-type='decimal' data-field='type'>type: <strong>Number (decimal .00)</strong></td>";
				}
				break;
				case "decimal3":
				{
					newhtml+= " data-type='decimal3' data-field='type'>type: <strong>Number (decimal .000)</strong></td>";
				}
				break;
				case "select":
				{
//					if(customobj.selvals !== undefined)
					{
						newhtml+= " data-type='select' data-field='type' data-vals='" + customobj.selvals + "'>type: <strong>select</strong><br/>(" + customobj.selvals + ")</td>";
					}
					
				}
				break;
				case "checkbox":
				{
					newhtml+= " data-type='checkbox' data-field='type'>type: <strong>Checkbox</strong></td>";
				}
				break;
				case "custom":
				{
					newhtml+= " data-type='custom' data-field='type' data-vals='" + customobj.isnewvals + "'>type: <strong>Custom Taxonomy</td>";
				}
				break;
				case "customh":
				{
					newhtml+= " data-type='customh' data-field='type'>type: <strong>Custom Taxonomy(hierar.)</td>";
				}
				break;
				
				default:
					break;
			}
				ctext = customobj.isvisible;
				if(ctext == "true")
				{
					newhtml+= '<td data-field="isvisible"><label><input type="checkbox" class="customisvisible" checked="checked">Visible</label><input class="button deletecustomfield" type="button" value="delete" /></td></tr>';
					customobj.isvisible = true;
				}else
				{
					newhtml+= '<td data-field="isvisible"><label><input type="checkbox" class="customisvisible">Visible</label><input class="button deletecustomfield" type="button" value="delete" /></td></tr>';
					customobj.isvisible = false;
				}
				$(newhtml).insertBefore('.addcontrols');
				
				
				var newitem = {};
				newitem.id = customobj.name;
				newitem.name = customobj.name;
				newitem.field = customobj.name;
				
				if(customobj.type == "text")
				{
					newitem.editor = Slick.Editors.Text;
				}else if(customobj.type == "multitext")
				{
					newitem.editor = Slick.Editors.TextArea;
					insertobj.textarea = true;
				}else if(customobj.type == "integer")
				{
					newitem.editor = Slick.Editors.Text;
					insertobj.type = 'int';
				}else if(customobj.type == "decimal")
				{
					newitem.editor = Slick.Editors.Text;
					insertobj.type = 'float2';
				}else if(customobj.type == "decimal3")
				{
					newitem.editor = Slick.Editors.Text;
					insertobj.type = 'float3';
				}else if(customobj.type == "checkbox")
				{
					newitem.cssClass = "cell-effort-driven";
					newitem.formatter = Slick.Formatters.Checkmark;
					newitem.editor = Slick.Editors.Checkbox;
					insertobj.checkbox = true;
					insertobj.type = 'set';
				}else if(customobj.type == "select")
				{
					newitem.editor = Slick.Editors.Select;
					newitem.options = customobj.selvals;
					insertobj.type = 'set';
					insertobj.options= customobj.selvals;
				}else if(customobj.type == "custom")
				{
					newitem.editor = Slick.Editors.Text;
					insertobj.scope = SCOPE.PRODALL;
					insertobj.type = 'customtax';
					insertobj.isnewvals = customobj.isnewvals;
				}else if(customobj.type == "customh")
				{
					newitem.editor = Slick.Editors.Text;
					insertobj.scope = SCOPE.PRODALL;
					insertobj.type = 'customtaxh';
				}
				AddBulkAndSelectFields(customobj);
				newitem.sortable = true;
//				_allcols.push(newitem);
				_idmap.push(insertobj);
//				if(customobj.isvisible === "true")
//					gridColumns.push(newitem);
				
		  }
		}
		ShowCustomSearchFilters();
		
	}
	
	SetCustomFields();
	
	function SetColumns()
	{
		//get translation
		
		for(var i=0; i < _idmap.length; i++)
		{
			var col = _idmap[i];
			if(col.field == 'ID')
				continue;
			if(_mapfield[col.field] === undefined)
				continue;
			if(W3Ex[col.field] !== undefined && W3Ex[col.field] !== "")
			{
				col.name = W3Ex[col.field];
				col.tooltip = W3Ex[col.field];
			}
		}
		///////
		
		var cols = {
			"_sku":60,
			"_regular_price":80,
			"_sale_price":80,
			"product_cat":130,
			"_stock":80,
			"_stock_status":80,
			"post_status":70,
			"_visibility":190
		}
		if(W3Ex.colsettings !== undefined) cols = W3Ex.colsettings;
//		cols["attribute_pa_color"] = 100;
		for (var key in cols) {
		  if (cols.hasOwnProperty(key)) {
		  	   if(_mapfield[key] === undefined) continue;
		  	   var col = _idmap[_mapfield[key]];
			   if(col === undefined) continue;
			   col.visible = true;
			   var cwidth = parseInt(cols[key]);
			   if(isNaN(cwidth)) continue;
			   if(cwidth < 50) cwidth = 50;
		       col.width = cwidth;
			   	$('.dsettings[data-id="'+key+'"]').each(function()
				{
					$(this).prop('checked', true);
					var id = $(this).attr('data-id');
					$("#bulkdialog tr[data-id='" + id + "']").show();
					$("#selectdialog tr[data-id='" + id + "']").show();
					var id = $(this).attr('id');
					$('#' + id + '_check').css('visibility','visible');
					$('#' + id + ' + label').css('font-weight','bold');
				})
				
		  }
		}
		
	}
	
	SetColumns();
	
	$('.makechosen').chosen({disable_search_threshold: 10,search_contains:true});
	
	
	var gridColumns = [
			checkboxSelector.getColumnDefinition()
		];		
		
		var _allcols = [checkboxSelector.getColumnDefinition()];//$.extend(true, [], gridColumns);
		
		for (var i = 0; i < _idmap.length; i++) 
		{
		    var item = _idmap[i];
	  		
			var newitem = {};
			newitem.id = item.id;
			newitem.name = item.name;
			newitem.field = item.field;
			if(item.width !== undefined)
				newitem.width = item.width;
				
			if(item.tooltip !== undefined)
			{
				newitem.toolTip = item.tooltip;
			}else
			{
				newitem.toolTip= item.name;
			}
//			if(item.field != "ID")
			{
				newitem.editor = Slick.Editors.Text;
				if(item.options !== undefined)
				{
					newitem.editor = Slick.Editors.Select;
					newitem.options = item.options;
				}
				if(item.files !== undefined)
				{
					newitem.editor = Slick.Editors.LongText;
				}
				if(item.checkbox !== undefined)
				{
					newitem.cssClass = "cell-effort-driven";
					newitem.formatter = Slick.Formatters.Checkmark;
					newitem.editor = Slick.Editors.Checkbox;
				}
				if(item.date !== undefined)
				{
					newitem.editor = Slick.Editors.CustomDate;
				}
				if(item.textarea !== undefined)
				{
					newitem.editor = Slick.Editors.TextArea;
				}
				if(item.image !== undefined)
				{
					newitem.editor = Slick.Editors.Image;
					newitem.formatter = Slick.Formatters.Image;
				}
				if(item.url !== undefined)
				{
//					newitem.editor = Slick.Editors.Image;
					newitem.formatter = Slick.Formatters.ProductUrl;
				}
				if(item.image_gallery !== undefined)
				{
					newitem.editor = Slick.Editors.Gallery;
					newitem.formatter = Slick.Formatters.Gallery;
				}
				if(item.defattrs !== undefined)
				{
					newitem.editor = Slick.Editors.DefAttrs;
				}
				if(item.type !== undefined)
				{
					if(item.type == 'customtaxh')
					{
						newitem.editor = Slick.Editors.Category;
						newitem.scope = SCOPE.PRODALL;
					}else if(item.type == 'customtax')
					{
						newitem.scope = SCOPE.PRODALL;
					}
				}
			}
			newitem.sortable = true;
			_allcols.push(newitem);
			if(item.visible === undefined)
			{
				 continue;
			}
			var newcol = $.extend(true, {}, newitem);
			gridColumns.push(newcol);
		}
		

	
		var gridOptions = {
			enableCellNavigation: true,
			enableColumnReorder: true,
			defaultColumnWidth: 60,
			cellFlashingCssClass: "current-server",
			editable: true
			
		};		

	$("#fieldtype").change(function() 
    {
    	var what = $(this).val();
		if(what == "select")
		{
			$('#extracustominfo').html('<input type="text" placeholder="val1,val2... (csv)" />');
		}else if(what == "custom")
		{
			$('#extracustominfo').html('<label><input type="checkbox">Allow adding of new terms</label>');
		}else{
			$('#extracustominfo').html('');
		}
	})
	
	$('body').on('change','#bulkdialog .selectvisiblefp',function(){
		var $parent = $(this).parent().parent();
		var column = $(this).attr('data-id');
		$parent.find(".selectvisiblefp").prop("disabled",false);
		$parent.find(".selectusedforvars").prop("disabled",false);
		var setvisval = $parent.find(".selectvisiblefp").val();
		if(setvisval == "skip")
		{
			$parent.find(".visiblefp").prop("disabled",true);
			$('#bulk' + column).prop('disabled', false).trigger("chosen:updated");
			$('#bulkadd' + column).prop("disabled",false);
		}else if(setvisval == "andset")
		{
			$parent.find(".visiblefp").prop("disabled",false);
			$('#bulk' + column).prop('disabled', false).trigger("chosen:updated");
			$('#bulkadd' + column).prop("disabled",false);
		}else if(setvisval == "onlyset")
		{
			$parent.find(".visiblefp").prop("disabled",false);
			$parent.find(".selectusedforvars").prop("disabled",true);
			$parent.find(".usedforvars").prop("disabled",true);
			$('#bulkadd' + column).prop("disabled",true);
			$('#bulk' + column).prop('disabled', true).trigger("chosen:updated");
		}
		if( $parent.find(".selectusedforvars").is(':enabled'))
		{
			setvisval = $parent.find(".selectusedforvars").val();
			if(setvisval == "skip")
			{
				$parent.find(".usedforvars").prop("disabled",true);
			}else if(setvisval == "andset")
			{
				$parent.find(".usedforvars").prop("disabled",false);
			}else if(setvisval == "onlyset")
			{
				$parent.find(".usedforvars").prop("disabled",false);
				$parent.find(".selectvisiblefp").prop("disabled",true);
				$parent.find(".visiblefp").prop("disabled",true);
				$('#bulkadd' + column).prop("disabled",true);
				$('#bulk' + column).prop('disabled', true).trigger("chosen:updated");
			}
		}
		
	})
	
	$('body').on('change','#bulkdialog .selectusedforvars',function(){
		var $parent = $(this).parent().parent();
		var column = $(this).attr('data-id');
		$parent.find(".selectvisiblefp").prop("disabled",false);
		$parent.find(".selectusedforvars").prop("disabled",false);
		var setvisval = $(this).val();
		if(setvisval == "skip")
		{
			$parent.find(".usedforvars").prop("disabled",true);
			$('#bulk' + column).prop('disabled', false).trigger("chosen:updated");
			$('#bulkadd' + column).prop("disabled",false);
		}else if(setvisval == "andset")
		{
			$parent.find(".usedforvars").prop("disabled",false);
			$('#bulk' + column).prop('disabled', false).trigger("chosen:updated");
			$('#bulkadd' + column).prop("disabled",false);
		}else if(setvisval == "onlyset")
		{
			$parent.find(".usedforvars").prop("disabled",false);
			$parent.find(".selectvisiblefp").prop("disabled",true);
			$parent.find(".visiblefp").prop("disabled",true);
			$('#bulkadd' + column).prop("disabled",true);
			$('#bulk' + column).prop('disabled', true).trigger("chosen:updated");
		}
		if( $parent.find(".selectvisiblefp").is(':enabled'))
		{
			setvisval = $parent.find(".selectvisiblefp").val();
			if(setvisval == "skip")
			{
				$parent.find(".visiblefp").prop("disabled",true);
			}else if(setvisval == "andset")
			{
				$parent.find(".visiblefp").prop("disabled",false);
			}else if(setvisval == "onlyset")
			{
				$parent.find(".visiblefp").prop("disabled",false);
				$parent.find(".selectusedforvars").prop("disabled",true);
				$parent.find(".usedforvars").prop("disabled",true);
				$('#bulkadd' + column).prop("disabled",true);
				$('#bulk' + column).prop('disabled', true).trigger("chosen:updated");
			}
		}
		
	})
	
	$('body').on('click','#bulkdialog .bulkset',function(){
    	var item = $(this);
		var column = item.attr('data-id');
		var coldef = _idmap[_mapfield[column]];
		if(!item.prop('checked'))
		{
			if(coldef !== undefined && coldef.type === "customtaxh")
			{
				$('#bulk' + column).prop('disabled', true).trigger("chosen:updated");
				$('#bulkadd' + column).prop("disabled",true);
				if(true === coldef.attribute)
				{
					item.parent().parent().find(".selectvisiblefp").prop("disabled",true);
					item.parent().parent().find(".selectusedforvars").prop("disabled",true);
					item.parent().parent().find(".visiblefp").prop("disabled",true);
					item.parent().parent().find(".usedforvars").prop("disabled",true);
				}
			}
			else
				$('#bulk' + column).prop("disabled",true);
		}else
		{
			if(coldef !== undefined && coldef.type === "customtaxh")
			{
				$('#bulk' + column).prop('disabled', false).trigger("chosen:updated");
				$('#bulkadd' + column).prop("disabled",false);
				if(true === coldef.attribute)
				{
					var $parent = item.parent().parent();
					$parent.find(".selectvisiblefp").prop("disabled",false);
					$parent.find(".selectusedforvars").prop("disabled",false);
					var setvisval = $parent.find(".selectvisiblefp").val();
					if(setvisval == "skip")
					{
						$parent.find(".visiblefp").prop("disabled",true);
					}else if(setvisval == "andset")
					{
						$parent.find(".visiblefp").prop("disabled",false);
					}else if(setvisval == "onlyset")
					{
						$parent.find(".visiblefp").prop("disabled",false);
						$parent.find(".selectusedforvars").prop("disabled",true);
						$parent.find(".usedforvars").prop("disabled",true);
						$('#bulkadd' + column).prop("disabled",true);
						$('#bulk' + column).prop('disabled', true).trigger("chosen:updated");
					}
					
					setvisval = $parent.find(".selectusedforvars").val();
					if(setvisval == "skip")
					{
						$parent.find(".usedforvars").prop("disabled",true);
					}else if(setvisval == "andset")
					{
						$parent.find(".usedforvars").prop("disabled",false);
					}else if(setvisval == "onlyset")
					{
						$parent.find(".usedforvars").prop("disabled",false);
						$parent.find(".selectvisiblefp").prop("disabled",true);
						$parent.find(".visiblefp").prop("disabled",true);
						$('#bulkadd' + column).prop("disabled",true);
						$('#bulk' + column).prop('disabled', true).trigger("chosen:updated");
					}
				}
			}
			else
				$('#bulk' + column).prop("disabled",false);
		}
	});

	
	$('body').on('mouseenter','.galleryholder li',function(){
			$(this).parent().find('img.delete').css('visibility','hidden');
			$(this).find('img').css('visibility','visible');
		});

	$('body').on('mouseleave','.galleryholder li',function(){
			$(this).find('img').css('visibility','hidden');
		});

	$('body').on('click','.galleryholder img.delete',function(){
			$(this).parent().remove();
		});
	
//selection manager
	$('body').on('click','#selectdialog .selectset',function(){
    	var item = $(this);
		if(!item.prop('checked'))
		{
			$('#selectdialog #select' + item.attr('data-id')).prop("disabled",true);
		}else
		{
			$('#selectdialog #select' + item.attr('data-id')).prop("disabled",false);
		}
	});

	$('#exportproducts').click(function() 
    {
		$('#exportdialog').dialog("open");
		return;
		
	})
	
	function SelectUpdateField(field,selitem,value,rowid,action,params,ignorecase,found)
	{
		var col = _idmap[_mapfield[field]];
		if(col === undefined) return;
		if(value === undefined) value = "";
		if(col.scope !== undefined)
		{
			if(col.scope == SCOPE.PRODALL)
			{
				if(selitem.post_type == 'product_variation')
				{
					found.notfoundcon = true;
					return;
				}
			}
			if(col.scope == SCOPE.PRODSVAR)
			{
				if(selitem.haschildren !== undefined)
				{
					found.notfoundcon = true;
					return;
				}
			}
		}
		if(field == "grouped_items") 
		{
			if(selitem.product_type != 'simple')
			{
				return;
			}
		}
		
		if(col.type === undefined || col.type === "customtax" || col.type === "customtaxh")
		{//text field
			var selvalue = selitem[field];
			if(action == "empty")
			{
				if(selitem[field] === "" || selitem[field] === undefined)
				{
					found.foundcon = true;
				}else
				{
					found.notfoundcon = true;
				}
			}
			if(selvalue === undefined || selvalue === null)
				selvalue = "";
			if(ignorecase[field] !== undefined && ignorecase[field])
			{
				selvalue = selvalue.toLowerCase();
				value =  String(value);
				value = value.toLowerCase();
			}
			
			switch(action)
			{
				case "con":
				{
					if(selvalue.indexOf(value) >= 0)
					{
						found.foundcon = true;
					}else
					{
						found.notfoundcon = true;
					}
				}break;
				case "notcon":
				{
					if(selvalue.indexOf(value) == -1)
					{
						found.foundcon = true;
					}else
					{
						found.notfoundcon = true;
					}
				}break;
				case "start":
				{
					if(selvalue.indexOf(value) == 0)
					{
						found.foundcon = true;
					}else
					{
						found.notfoundcon = true;
					}
				}break;
				case "end":
				{
					var n = selvalue.lastIndexOf(value);
					if(selvalue.length > 0)
					{
						if((n + value.length) == selvalue.length)
						{
							found.foundcon = true;
						}else
						{
							found.notfoundcon = true;
						}
					}
					
				}break;
				case "iscon":
				{
					if(value.indexOf('\n') != 0 || value.indexOf('\r\n') != 0)
					{
						var lines = value.split(/\r\n|\r|\n/g);
						var bfound = false;
						for(var i=0; i<lines.length; i++)
						{
							var line = lines[i];
							line = $.trim(line);
							if(line === "") continue;
							if(line.indexOf(selvalue) >= 0)
							{
								bfound = true;
								break;
							}
						}
						if(bfound)
						{
							found.foundcon = true;
						}else
						{
							found.notfoundcon = true;
						}
					}else
					{
						if(value.indexOf(selvalue) >= 0)
						{
							found.foundcon = true;
						}else
						{
							found.notfoundcon = true;
						}
					}
				}break;
				default:break;
			}
			return;
		}
		if(col.type === 'set')
		{
			if(value == selitem[field])
			{
				found.foundcon = true;
			}else
			{
				found.notfoundcon = true;
			}
			return;
		}
		if(col.type === 'float2' || col.type === 'float3' || col.type === 'int')
		{
			var usecommas = false;
			if(W3Ex.sett_usecomma !== undefined && W3Ex.sett_usecomma == 1)
			{
				usecommas = true;
			}
			if(usecommas)
			{
				value = replaceAll(value,',', '.');	
			}
			var bulkvalue = parseFloat(value);
			if(isNaN(bulkvalue))
			{
				found.notfoundcon = true;
				return;
			}
//			bulkvalue = Number(bulkvalue);
			var pricestr = selitem[field];
			if(usecommas)
			{
				pricestr = replaceAll(pricestr,',', '.');	
			}
			var price = parseFloat(pricestr);
			if(action == "empty")
			{
				if(selitem[field] == "" || selitem[field] === undefined)
				{
					found.foundcon = true;
				}else
				{
					found.notfoundcon = true;
				}
				return;
			}
			if(!isNaN(bulkvalue) && bulkvalue >= 0 && !isNaN(price))
			{
				switch(action)
				{
					case "more":
					{
						if(price > bulkvalue)
						{
							found.foundcon = true;
						}else
						{
							found.notfoundcon = true;
						}
					}break;
					case "less":
					{
						if(price < bulkvalue)
						{
							found.foundcon = true;
						}else
						{
							found.notfoundcon = true;
						}
					}break;
					case "equal":
					{
						if(price == bulkvalue)
						{
							found.foundcon = true;
						}else
						{
							found.notfoundcon = true;
						}
						
					}break;
					case "moree":
					{
						if(price >= bulkvalue)
						{
							found.foundcon = true;
						}else
						{
							found.notfoundcon = true;
						}
					}break;
					case "lesse":
					{
						if(price<= bulkvalue)
						{
							found.foundcon = true;
						}else
						{
							found.notfoundcon = true;
						}
					}break;
					default:break;
				}
				
			}
		}
	}
	

	function HandleSelectUpdate(params)
	{
		var selectedRows = [];
		var type = $('#selectproduct').val();
		var add = $('#selectany').val();
		var select = $('#selectselect').val();
		var ignorecase = {};
		$('.selectifignorecase:visible').each(function ()
		{
			var itemid = $(this).attr('data-id');
			ignorecase[itemid] = $(this).is(':checked');
		})
		var found = {
			foundcon:false,
			notfoundcon:false
		};
		for(var irow=0; irow < _data.length; irow++)
		{
			if(_data[irow] === undefined) continue;
			var selitem = _data[irow];
			if( type === "prod")
			{
				if(selitem.post_type !== undefined)
					if(selitem.post_type == 'product_variation')
					    continue;
			}
			if( type === "var")
			{
				if(selitem.post_type !== undefined)
					if(selitem.post_type == 'product')
					    continue;
			}
			
			
			for (var key in params) {
			  if (params.hasOwnProperty(key)) {
			     if(key.indexOf('value') === -1)
				 {//key e actions
//				 	BulkUpdateField(field,selitem,value,rowid,action)
				 	if(params[key + 'value'] !== undefined)
				 	    SelectUpdateField(key,selitem,params[key + 'value'],irow,params[key],params,ignorecase,found);
					else
						SelectUpdateField(key,selitem,"",irow,params[key],params,ignorecase,found);
				 }
			  }
			}
			if(add == "any")
			{
				if(found.foundcon)
				{
					selectedRows.push(irow);
				}
			}else
			{
				if(found.foundcon && !found.notfoundcon)
				{
					selectedRows.push(irow);
				}
			}
			found.foundcon = false;
			found.notfoundcon = false;
		}
		if(select == "select")
		{
			var selectedRows1 = _grid.getSelectedRows();
			selectedRows1 = selectedRows1.concat(selectedRows);
			_grid.setSelectedRows(selectedRows1);
		}else
		{
			var sel1= _grid.getSelectedRows();
			var temp = {}, i, result = [];

		    for (i = 0; i < selectedRows.length; i++) {
		        temp[selectedRows[i]] = true;
		    }

		    for (i = 0; i < sel1.length; i++) {
		        if (!(sel1[i] in temp)) {
		            result.push(sel1[i]);
		        }
		    }
			_grid.setSelectedRows(result);
		}
		 
	}
	 	

	
	$("#bulk_sale_price").change(function() 
    {
    	var what = $(this).val();
		if(what == "delete")
		{
			$('#bulksalepricevalue').prop("disabled",true);
		}else
		{
			$('#bulksalepricevalue').prop("disabled",false);
		}
		
		if(what == "decvaluereg" || what == "decpercentreg")
		{
			$('#saleskip').show();
			$('#saleskiplabel').show();
			
		}else
		{
			$('#saleskip').hide();
			$('#saleskiplabel').hide();
		}
	})
		
	function DisableAllControls(bdis)
    {
  		if(bdis)
	  	{
	  		$('#getproducts').prop("disabled",true);
			$('#savechanges').prop("disabled",true);
			$('#selectedit').prop("disabled",true);
			$('#bulkedit').prop("disabled",true);
			$('#butprevious').prop("disabled",true);
			$('#gotopage').prop("disabled",true);
			$('#butnext').prop("disabled",true);
			$('#revertcell').prop("disabled",true);
			$('#revertrow').prop("disabled",true);
			$('#revertall').prop("disabled",true);
			$('#deletebut').prop("disabled",true);
			$('#addprodbut').prop("disabled",true);
			$('#duplicateprodbut').prop("disabled",true);
		}else
		{
			$('#getproducts').prop("disabled",false);
			$('#savechanges').prop("disabled",false);
			$('#selectedit').prop("disabled",false);
			$('#bulkedit').prop("disabled",false);
			$('#deletebut').prop("disabled",false);
			$('#addprodbut').prop("disabled",false);
			$('#duplicateprodbut').prop("disabled",false);
			if(_totalrecords > _recordslimit)
			{
				$('#butprevious').prop("disabled",false);
				$('#gotopage').prop("disabled",false);
				$('#butnext').prop("disabled",false);
			}
			$('#revertcell').prop("disabled",false);
			$('#revertrow').prop("disabled",false);
			$('#revertall').prop("disabled",false);
		}
	}

	$('#selectedit').prop("disabled",true);
	$('#bulkedit').prop("disabled",true);
	$('#butprevious').prop("disabled",true);
	$('#gotopage').prop("disabled",true);
	$('#butnext').prop("disabled",true);
	$('#revertcell').prop("disabled",true);
	$('#revertrow').prop("disabled",true);
	$('#revertall').prop("disabled",true);
	$('#deletebut').prop("disabled",true);
	$('#addprodbut').prop("disabled",true);
	$('#duplicateprodbut').prop("disabled",true);
	$('#getproducts').prop("disabled",false);
	$('#savechanges').prop("disabled",false);
	$('#revertcell').prop("disabled",false);
	$('#revertrow').prop("disabled",false);
	$('#revertall').prop("disabled",false);
	$('#settings').prop("disabled",false);
	$('#customfieldsbut').prop("disabled",false);
	$('#findcustomfieldsbut').prop("disabled",false);
	$('#pluginsettingsbut').prop("disabled",false);
	$('#exportproducts').prop("disabled",false);
	
	function replaceAll(str, token, newToken, ignoreCase) {
	    var i = -1, _token;
	    if(typeof token === "string") {
	        if(ignoreCase === true) {
	            _token = token.toLowerCase();
	            while((i = str.toLowerCase().indexOf( _token, i >= 0? i + newToken.length : 0 )) !== -1 ) {
	                str = str.substring(0, i)
	                        .concat(newToken)
	                        .concat(str.substring(i + _token.length));
	            }
	        } else {
	            return str.split(token).join(newToken);
	        }
	    }
		return str;
	}

	function BulkUpdateField(field,selitem,value,rowid,action,params)
	{
		var col = _idmap[_mapfield[field]];
		if(col === undefined) return;
		if(col.scope !== undefined)
		{
			if(col.scope == SCOPE.PRODALL)
			{
				if(selitem.post_type == 'product_variation')
				{
					return;
				}
			}
			if(col.scope == SCOPE.PRODSVAR)
			{
				if(selitem.haschildren !== undefined)
				{
					return;
				}
			}
		}
		if(field == "grouped_items") 
		{
			if(selitem.product_type != 'simple')
			{
				return;
			}
			
		}
		if(col.type !== undefined && col.type == 'customtaxh')
		{
			if(selitem[field] === undefined || selitem[field] === null)
				selitem[field] = "";
			if(true === col.attribute && ( params[field + '_visiblefp'] !== undefined || params[field + '_usedforvars'] !== undefined))
			{
				if(selitem[field + '_ids'] === undefined)
					selitem[field + '_ids'] = "";
				if(selitem.post_type == 'product' && params[field + '_onlyvisiblefp'] === 1)
				{
					var oldvisibleandused = selitem[field + '_visiblefp'];
					if(params[field + '_visiblefp'] === 1 && (oldvisibleandused & 1))
					{
						return;
					}
					if(params[field + '_visiblefp'] === 0 && !(oldvisibleandused & 1))
					{
						return;
					}
					if(selitem[field + '_ids'] != "")
					{
						SetEditValue(rowid,field + '_visiblefp',selitem[field + '_visiblefp']);
						if(params[field + '_visiblefp'] == 1)
						{
							oldvisibleandused|= 1;
						}else
						{
						    oldvisibleandused&= ~1;
						}
						selitem[field + '_visiblefp'] = oldvisibleandused;
						if(_changed[rowid] === undefined)
							_changed[rowid] = {};
						_changed[rowid][field] = "changed";
					}
					return;
				}
				if(selitem.product_type === 'variable' && params[field + '_onlyusedforvars'] === 1)
				{
					var oldvisibleandused = selitem[field + '_visiblefp'];
					if(params[field + '_usedforvars'] === 1 && (oldvisibleandused & 2))
					{
						return;
					}
					if(params[field + '_usedforvars'] === 0 && !(oldvisibleandused & 2))
					{
						return;
					}
					if(selitem[field + '_ids'] != "")
					{
						SetEditValue(rowid,field + '_visiblefp',selitem[field + '_visiblefp']);
						var oldvisibleandused = selitem[field + '_visiblefp'];
						if(params[field + '_usedforvars'] == 1)
						{
							oldvisibleandused|= 2;
						}else
						{
						    oldvisibleandused&= ~2;
						}
						selitem[field + '_visiblefp'] = oldvisibleandused;
						if(_changed[rowid] === undefined)
							_changed[rowid] = {};
						_changed[rowid][field] = "changed";
					}
					return;
				}
				
				if(params[field + '_onlyusedforvars'] === 1 || params[field + '_onlyvisiblefp'] === 1)
					return;
					
				if((selitem.product_type == 'simple' || selitem.product_type == 'grouped') && params[field + '_visiblefp'] !== undefined)
				{
					if(params[field + '_visiblefp'] !== selitem[field + '_visiblefp'])
					{
						
						if((params[field + 'value_ids'] !== undefined) && (params[field + 'value_ids']) && params[field + 'action'] !== "remove")
						{
							if(params[field + 'value_ids'].length > 0 || selitem[field + '_ids'] != "")
							{
								if(!(params[field + 'value_ids'].length === 0 && params[field + 'action'] === "new"))
								{
									SetEditValue(rowid,field + '_visiblefp',selitem[field + '_visiblefp']);
									selitem[field + '_visiblefp'] = params[field + '_visiblefp'];
									if(_changed[rowid] === undefined)
										_changed[rowid] = {};
									_changed[rowid][field] = "changed";
								}
							}
						}
					}
				}
				
				if(params[field + '_visiblefp'] !== undefined && selitem.product_type == 'variable')
				{
					var oldvisibleandused = selitem[field + '_visiblefp'];
					
					if((params[field + '_visiblefp'] === 1 && !(oldvisibleandused & 1)) || (params[field + '_visiblefp'] === 0 && (oldvisibleandused & 1)))
					{
						if(params[field + 'value_ids'] !== undefined && params[field + 'value_ids'] !== "" && params[field + 'action'] !== "remove")
						{
							if(params[field + 'value_ids'].length > 0 || selitem[field + '_ids'] != "")
							{
								if(!(params[field + 'value_ids'].length === 0 && params[field + 'action'] === "new"))
								{
									SetEditValue(rowid,field + '_visiblefp',selitem[field + '_visiblefp']);
									if(params[field + '_visiblefp'] === 1)
									{
										oldvisibleandused|= 1;
									}else
									{
									    oldvisibleandused&= ~1;
									}
									selitem[field + '_visiblefp'] = oldvisibleandused;
									if(_changed[rowid] === undefined)
										_changed[rowid] = {};
									_changed[rowid][field] = "changed";
								}
							}
						}
					}
				}
				if(params[field + '_usedforvars'] !== undefined && selitem.product_type == 'variable')
				{
					var oldvisibleandused = selitem[field + '_visiblefp'];
					if((params[field + '_usedforvars'] === 1 && !(oldvisibleandused & 2)) || (params[field + '_usedforvars'] === 0 && (oldvisibleandused & 2)))
					{
						if(params[field + 'value_ids'] !== undefined && params[field + 'value_ids'] !== "" && params[field + 'action'] !== "remove")
						{
							if(params[field + 'value_ids'].length > 0 || selitem[field + '_ids'] != "")
							{
								if(!(params[field + 'value_ids'].length === 0 && params[field + 'action'] === "new"))
								{
									SetEditValue(rowid,field + '_visiblefp',selitem[field + '_visiblefp']);
									if(params[field + '_usedforvars'] === 1)
									{
										oldvisibleandused|= 2;
									}else
									{
									    oldvisibleandused&= ~2;
									}
									selitem[field + '_visiblefp'] = oldvisibleandused;
									if(_changed[rowid] === undefined)
										_changed[rowid] = {};
									_changed[rowid][field] = "changed";
								}
							}
						}
					}
				}
			}
			
			
			if(params[field + 'action'] !== undefined && params[field + 'action'] !== "new")
			{
				if(params[field + 'action'] === "add")
				{
					var catsids = selitem[field + '_ids'];
					var curcatsids = params[field + 'value_ids'].join();
					
					if(catsids === undefined)
						catsids = "";
					if(curcatsids === undefined)
						curcatsids = "";
					if(true === col.attribute && selitem['post_type'] == 'product_variation')
					{
						if(catsids !== "" || selitem[field] != "")
						{//variation has one already
							return;
						}
					}
					
					
					if(curcatsids == "")
						return; //empty string, bye, bye
						
					catsids = catsids.split(',');
					curcatsids = curcatsids.split(',');
					
					
					if (catsids instanceof Array && curcatsids instanceof Array) 
					{
						var addcats = [];
						for(var i=0; i < curcatsids.length; i++)
						{
							if(catsids.indexOf(curcatsids[i]) === -1)
							{
							   addcats.push(curcatsids[i]);
							}
						}
						if(addcats.length == 0) return; //nothing to add
						var insertval = params[field + 'value'];
						if(true === col.attribute && selitem['post_type'] == 'product_variation')
						{
							if(addcats.length > 1)
							{//variation has one already
								addcats.splice(1,addcats.length -1);
							}
							 if(params[field + 'value'].indexOf(',') !== -1)
							 {
							 	insertval = params[field + 'value'].substring(0,params[field + 'value'].indexOf(","));
							 }
						}
						catsids = catsids.concat(addcats); 
						SetEditValue(rowid,field,selitem[field]);
						if(selitem[field + '_ids'] === undefined)
							selitem[field + '_ids'] = "";
						SetEditValue(rowid,field + '_ids',selitem[field + '_ids']);
						selitem[field + '_ids'] = catsids.join();
						if(selitem[field + '_ids'] === "")
							selitem[field] = "";
						else
						{
							if(selitem[field] == "")
								selitem[field] = insertval;
							else
								selitem[field] = selitem[field] + ", " + insertval;
						}
							
						if(_changed[rowid] === undefined)
							_changed[rowid] = {};
						_changed[rowid][field] = "changed";
					}
					
					return;
				}
				if(params[field + 'action'] === "remove")
				{
					var catsids = selitem[field + '_ids'];
					var curcatsids = params[field + 'value_ids'].join();
					
					if(catsids === undefined)
						catsids = "";
					if(curcatsids === undefined)
						curcatsids = "";
					/*if(true === col.attribute && selitem['post_type'] == 'product' && catsids !== "")
					{
						if(params[field + '_visiblefp'] !== undefined)
						{
							if(params[field + '_visiblefp'] !== selitem[field + '_visiblefp'])
							{
								SetEditValue(rowid,field + '_visiblefp',selitem[field + '_visiblefp']);
								selitem[field + '_visiblefp'] = params[field + '_visiblefp'];
								SetEditValue(rowid,field,selitem[field]);
								if(selitem[field + '_ids'] === undefined)
									selitem[field + '_ids'] = "";
								SetEditValue(rowid,field + '_ids',selitem[field + '_ids']);
								if(_changed[rowid] === undefined)
									_changed[rowid] = {};
								_changed[rowid][field] = "changed";
							}
						}
					}*/
					
					if(curcatsids == "")
						return; //empty string, bye, bye
						
					catsids = catsids.split(',');
					curcatsids = curcatsids.split(',');
					/*if(params[field + '_visiblefp'] !== undefined && true === col.attribute && selitem['post_type'] == 'product')
					{
						SetEditValue(rowid,field + '_visiblefp',selitem[field + '_visiblefp']);
						selitem[field + '_visiblefp'] = params[field + '_visiblefp'];
					}*/
					if (catsids instanceof Array && curcatsids instanceof Array) 
					{
						var remcats = [];
						for(var i=0; i < curcatsids.length; i++)
						{
							if(catsids.indexOf(curcatsids[i]) !== -1)
							{
							   remcats.push(curcatsids[i]);
							}
						}
						if(remcats.length == 0) return; //nothing to remove
						for(var i=0; i < remcats.length; i++)
						{
							if(catsids.indexOf(remcats[i]) !== -1)
							{
							    catsids.splice(catsids.indexOf(remcats[i]), 1);
							}
						}
						SetEditValue(rowid,field,selitem[field]);
						if(selitem[field + '_ids'] === undefined)
							selitem[field + '_ids'] = "";
						SetEditValue(rowid,field + '_ids',selitem[field + '_ids']);
						selitem[field + '_ids'] = catsids.join();
						if(selitem[field + '_ids'] === "")
							selitem[field] = "";
						else
						{
							var oldcats = selitem[field];
							var removecats = params[field + 'value'];
							oldcats = oldcats.replace(/\s/g, ""); 
							removecats = removecats.replace(/\s/g, ""); 
							oldcats = oldcats.split(',');
							removecats = removecats.split(',');
					
							if (oldcats instanceof Array && removecats instanceof Array) 
							{
								for(var i=0; i < removecats.length; i++)
								{
									if(oldcats.indexOf(removecats[i]) !== -1)
									{
									    oldcats.splice(oldcats.indexOf(removecats[i]), 1);
									}
								}
								var newcats = "";
								for(var i=0; i < oldcats.length; i++)
								{
									if(i == 0)
										newcats = oldcats[i];
									else
										newcats+= ", " + oldcats[i];
								}
								selitem[field] = newcats;
							}
						}
							
						if(_changed[rowid] === undefined)
							_changed[rowid] = {};
						_changed[rowid][field] = "changed";
					}
					return;
				}
				
				return;
			}
			var changedvisible = false;
			/*if(true === col.attribute && selitem['post_type'] == 'product')
			{
				if(params[field + '_visiblefp'] !== undefined)
				{
					if(params[field + '_visiblefp'] !== selitem[field + '_visiblefp'])
					{
						changedvisible = true;
					}
				}
			}*/
			
			{
				var catsids = selitem[field + '_ids'];
				var curcatsids = params[field + 'value_ids'].join();
				
				if(catsids === undefined)
					catsids = "";
				if(curcatsids === undefined)
					curcatsids = "";
				
				if(catsids === "" && curcatsids === "")
					return; //ignore change visible for empty cells
					
				catsids = catsids.split(',');
				curcatsids = curcatsids.split(',');
				
				
				if (catsids instanceof Array && curcatsids instanceof Array) 
				{
					if(catsids.length == curcatsids.length)
					{
						var breturn = true;
						for(var i=0; i < catsids.length; i++)
						{
							if(curcatsids.indexOf(catsids[i]) === -1)
							{
							   breturn = false;
							   break;
							}
						}
						if(breturn && !changedvisible)
						{
							return;
						}
					}
				}
			}

			var insertval = "";
			var temparr = [];
			if(true === col.attribute && selitem['post_type'] == 'product_variation')
			{
				
				 if(params[field + 'value_ids'] instanceof Array)
				 {
				 	temparr = $.extend(true, [], params[field + 'value_ids']);
				 	if(temparr.length > 1)
					{
						temparr.splice(1,temparr.length - 1);
					}
				 }
				 insertval = params[field + 'value'];
				 if(params[field + 'value'].indexOf(',') !== -1)
				 {
				 	insertval = params[field + 'value'].substring(0,params[field + 'value'].indexOf(","));
				 }
				SetEditValue(rowid,field,selitem[field]);
				if(selitem[field + '_ids'] === undefined)
					selitem[field + '_ids'] = "";
				SetEditValue(rowid,field + '_ids',selitem[field + '_ids']);
				selitem[field + '_ids'] = temparr.join();
				if(selitem[field + '_ids'] === "")
					selitem[field] = "";
				else
					selitem[field] = insertval;
				if(_changed[rowid] === undefined)
					_changed[rowid] = {};
				_changed[rowid][field] = "changed";
			}else
			{
//				if()
				{
					SetEditValue(rowid,field,selitem[field]);
					if(selitem[field + '_ids'] === undefined)
						selitem[field + '_ids'] = "";
					SetEditValue(rowid,field + '_ids',selitem[field + '_ids']);
					/*if(changedvisible && true === col.attribute && selitem['post_type'] == 'product')
					{
						SetEditValue(rowid,field + '_visiblefp',selitem[field + '_visiblefp']);
						selitem[field + '_visiblefp'] = params[field + '_visiblefp'];
					}*/
					selitem[field + '_ids'] = params[field + 'value_ids'].join();
					if(selitem[field + '_ids'] === "")
						selitem[field] = "";
					else
						selitem[field] = params[field + 'value'];
					if(_changed[rowid] === undefined)
						_changed[rowid] = {};
					_changed[rowid][field] = "changed";
				}
			}
			return;
		}
		if(col.type === undefined || col.type === 'customtax')
		{//text field
			if(selitem[field] === undefined || selitem[field] === null)
				selitem[field] = "";
			var oldvalue = selitem[field];
			var bupdate = false;
			switch(action)
			{
				case "new":
				{
					selitem[field] = value;
				}break;
				case "prepend":
				{
					if(selitem[field] !== undefined)
					{
						selitem[field] = value + selitem[field];
					}else
					{
						selitem[field] = value;
					}
				}break;
				case "append":
				{
					if(selitem[field] !== undefined)
					{
						selitem[field] = selitem[field] + value;
					}else
					{
						selitem[field] = value;
					}
				}break;
				case "replace":
				{
					if(selitem[field] != "")
					{
//						allow replace with empty string === delete search string
//						if(reptext != "")
						{
							var ifignorecase = params[field + 'ifignore'];
							var reptext = params[field + 'replacewith'];
							var posttitle = selitem[field];
							var replaced = replaceAll(posttitle,value,reptext,ifignorecase);
							if(replaced != selitem[field])
							{
								selitem[field] = replaced;
							}
						}
					}
					
				}break;
				default:break;
			}
			if(action == "delete")
			{
				selitem[field] = "";
				if(selitem[field] !== oldvalue)
				{
					SetEditValue(rowid,field,String(oldvalue));
					if(_changed[rowid] === undefined)
						_changed[rowid] = {};
					_changed[rowid][field] = "changed";
				}
			}else{
				if(selitem[field] !== oldvalue)
				{
					SetEditValue(rowid,field,oldvalue);
					if(_changed[rowid] === undefined)
						_changed[rowid] = {};
					_changed[rowid][field] = "changed";
				}
			}
		}
		if(col.type === 'set')
		{
			if(selitem[field] === undefined || selitem[field] === null)
				selitem[field] = "";
			if(value !== selitem[field])
			{
				SetEditValue(rowid,field,selitem[field]);
				selitem[field] = value;
				if(_changed[rowid] === undefined)
					_changed[rowid] = {};
				_changed[rowid][field] = "changed";
			}
			return;
		}
		if(col.type === 'float2' || col.type === 'float3')
		{
			if(selitem[field] === undefined || selitem[field] === null)
				selitem[field] = "";
			var oldvalue = selitem[field];
			var hascommas = false;
			if(W3Ex.sett_usecomma !== undefined && W3Ex.sett_usecomma == 1)
			{
				hascommas = true;
			}
			if(hascommas)
			{
				value = replaceAll(value,',', '.');
			}
			var bulkvalue = parseFloat(value);
			
			
			if(isNaN(bulkvalue))
				return;
			var prec = 3;
			if(col.type === 'float2') prec = 2;
			bulkvalue = Number(bulkvalue.toFixed(prec));
			var pricestr = selitem[field];
			if(pricestr.indexOf(',') !== -1)
			{
				pricestr = replaceAll(pricestr,',', '.');
				hascommas = true;
			}
			var price = parseFloat(pricestr);
			
			var bsetedit = false;
			if(!isNaN(bulkvalue))
			{
				switch(action)
				{
					case "new":
					{
						{
							selitem[field] = bulkvalue;
						}
					}break;
					case "incvalue":
					{
						if(!isNaN(price))
						{
							selitem[field] =  (Number(price) + bulkvalue).toFixed(prec);
							bsetedit = true;
						}
					}break;
					case "incpercent":
					{
						if(!isNaN(price))
						{
							var percent = (bulkvalue * 0.01) * parseFloat(price);
							selitem[field] =  (Number(price) + parseFloat(percent)).toFixed(prec);
							bsetedit = true;
						}
						
					}break;
					case "decvalue":
					{
						if(!isNaN(price))
						{
							if((Number(price) - bulkvalue) > 0)
							{
								selitem[field] =  (Number(price) - bulkvalue).toFixed(prec);
								bsetedit = true;
							}
						}
					}break;
					case "decpercent":
					{
						if(!isNaN(price))
						{
							var percent = (bulkvalue * 0.01) * parseFloat(price);
							selitem[field] =  (parseFloat(price) - parseFloat(percent)).toFixed(prec);
							bsetedit = true;
						}
					}break;
					case "decvaluereg":
					{//sale price only
						{//only without sale price set
							if(params.isskipsale !== undefined)
							{
								if(params.isskipsale)
								{
									if(!isNaN(price) || price == 0)
									{
										break;
									}
								}
							}
							var regpricestr = selitem._regular_price;
							if(regpricestr.indexOf(',') !== -1)
							{
								regpricestr = replaceAll(regpricestr,',', '.');
								hascommas = true;
							}
							var regprice = parseFloat(regpricestr);
							
							if(!isNaN(regprice))
							{
								selitem._sale_price =   (Number(regprice) - bulkvalue).toFixed(prec);
								bsetedit = true;
							}
						}
					}break;
					case "decpercentreg":
					{
						{
							if(params.isskipsale !== undefined)
							{
								if(params.isskipsale)
								{
									if(!isNaN(price) || price == 0)
									{
										break;
									}
								}
							}
							var regpricestr = selitem._regular_price;
							if(regpricestr.indexOf(',') !== -1)
							{
								regpricestr = replaceAll(regpricestr,',', '.');
								hascommas = true;
							}
							var regprice = parseFloat(regpricestr);
							if(!isNaN(regprice))
							{
								var percent = (bulkvalue * 0.01) * parseFloat(regprice);
								selitem._sale_price =  (parseFloat(regprice) - parseFloat(percent)).toFixed(prec);
								bsetedit = true;
							}
						}
					}break;
					default:break;
				}
				if(selitem[field]  !== undefined)
				{
					selitem[field]  = String(selitem[field]);
					if(col.type === 'float3')
						selitem[field]  = selitem[field].replace('.000','');
					else
						selitem[field]  = selitem[field].replace('.00','');
					if(hascommas)
					{
						if(col.type === 'float3')
							selitem[field]  = selitem[field].replace(',000','');
						else
							selitem[field]  = selitem[field].replace(',00','');
					}
				}
				if(action == "delete")
				{
					selitem[field] = "";
					if(selitem[field] !== oldvalue)
					{
						if(!isNaN(oldvalue))
						{
							SetEditValue(rowid,field,String(oldvalue));
							if(_changed[rowid] === undefined)
								_changed[rowid] = {};
							_changed[rowid][field] = "changed";
						}
					}
				}else{
					if(selitem[field] !== oldvalue)
					{
						if(isNaN(value))
							SetEditValue(rowid,field,"");
						else
							SetEditValue(rowid,field,String(oldvalue));
						if(_changed[rowid] === undefined)
							_changed[rowid] = {};
						_changed[rowid][field] = "changed";
					}
				}
			}
			
		}
		if(col.type === 'int')
		{
			if(selitem[field] === undefined || selitem[field] === null)
				selitem[field] = "";
			var oldvalue = selitem[field];
			var bulkvalue = parseInt(value);
			if(isNaN(bulkvalue))
				return;
			bulkvalue = Number(bulkvalue.toFixed());
			var price = parseInt(selitem[field]);
			var bsetedit = false;
			if(!isNaN(bulkvalue))
			{
				switch(action)
				{
					case "new":
					{
//						if(bulkvalue == 0)
//						{
//							selitem[field] = "";
//						}else
						{
							selitem[field] = bulkvalue;
						}
					}break;
					case "incvalue":
					{
						if(!isNaN(price))
						{
							selitem[field] =  (Number(price) + bulkvalue).toFixed();
							bsetedit = true;
						}
					}break;
					case "incpercent":
					{
						if(!isNaN(price))
						{
							var percent = (bulkvalue * 0.01) * parseInt(price);
							selitem[field] =  (Number(price) + parseInt(percent)).toFixed();
							bsetedit = true;
						}
						
					}break;
					case "decvalue":
					{
						if(!isNaN(price))
						{
							if((Number(price) - bulkvalue) > 0)
							{
								selitem[field] =  (Number(price) - bulkvalue).toFixed();
								bsetedit = true;
							}
						}
					}break;
					case "decpercent":
					{
						if(!isNaN(price))
						{
							var percent = (bulkvalue * 0.01) * parseInt(price);
							selitem[field] =  (parseInt(price) - parseInt(percent)).toFixed();
							bsetedit = true;
						}
					}break;
					case "decvaluereg":
					{//sale price only
						{//only without sale price set
							if(params.isskipsale !== undefined)
							{
								if(params.isskipsale)
								{
									if(!isNaN(price) || price == 0)
									{
										break;
									}
								}
							}
							var regprice = parseInt(selitem._regular_price);
							if(!isNaN(regprice))
							{
								selitem._sale_price =   (Number(regprice) - bulkvalue).toFixed();
								bsetedit = true;
							}
						}
					}break;
					case "decpercentreg":
					{
						{
							if(params.isskipsale !== undefined)
							{
								if(params.isskipsale)
								{
									if(!isNaN(price) || price == 0)
									{
										break;
									}
								}
							}
							var regprice = parseInt(selitem._regular_price);
							if(!isNaN(regprice))
							{
								var percent = (bulkvalue * 0.01) * parseInt(regprice);
								selitem._sale_price =  (parseInt(regprice) - parseInt(percent)).toFixed();
								bsetedit = true;
							}
						}
					}break;
					default:break;
				}
//				if(selitem[field]  !== undefined)
//				{
//					selitem[field]  = String(selitem[field]);
//					if(col.type === 'float3')
//						selitem[field]  = selitem[field].replace('.000','');
//					else
//						selitem[field]  = selitem[field].replace('.00','');
//				}
				if(action == "delete")
				{
					selitem[field] = "";
					if(selitem[field] !== oldvalue)
					{
						if(!isNaN(oldvalue))
						{
							SetEditValue(rowid,field,String(oldvalue));
							if(_changed[rowid] === undefined)
								_changed[rowid] = {};
							_changed[rowid][field] = "changed";
						}
					}
				}else{
					if(selitem[field] !== oldvalue)
					{
						if(isNaN(value))
							SetEditValue(rowid,field,"");
						else
							SetEditValue(rowid,field,String(oldvalue));
						if(_changed[rowid] === undefined)
							_changed[rowid] = {};
						_changed[rowid][field] = "changed";
					}
				}
			}
			
		}
	}
	
	function HandleBulkUpdate(params)
	{
		var selectedRows = _grid.getSelectedRows();
		for(var irow=0; irow < selectedRows.length; irow++)
		{
			var rowid = selectedRows[irow];
			if(rowid === undefined) continue;
			if(_data[rowid] === undefined) continue;
			var selitem = _data[rowid];
			var current = {};
			var bupdate = false;
			for (var key in params) {
			  if (params.hasOwnProperty(key)) {
			     if(key.indexOf('value') === -1)
				 {//key e actions
//				 	BulkUpdateField(field,selitem,value,rowid,action)
				 	if(params[key + 'value'] !== undefined)
				 	    BulkUpdateField(key,selitem,params[key + 'value'],rowid,params[key],params);
					else
						BulkUpdateField(key,selitem,"",rowid,params[key],params);
				 }
			  }
			}
		}
		
		try{
				_grid.removeCellCssStyles("changed");
				_grid.setCellCssStyles("changed", _changed);
			} catch (err) {
				;
			}
		if(params['product_type'] !== undefined)
			RefreshGroupedItems();
		_shouldhandle = false;
		_grid.resetActiveCell();
		_grid.invalidate();
		_shouldhandle = true;		
	}

	
	function SetEditValue(row,cell,value,ifdelete)
	{
		ifdelete = typeof ifdelete !== 'undefined' ? ifdelete : false;
		var Row = [];
		if(_arrEdited[row] === undefined)
		{
			_arrEdited[row] = Row;
		}else
		{
			Row = _arrEdited[row];
		}
		if(Row[cell] === undefined)
		{
			Row[cell] = value;
		}
		if(ifdelete)
		{
			delete Row[cell];
			if(cell == '_downloadable_files')
			{
				if(Row['_downloadable_files_val'] !== undefined)
					delete Row['_downloadable_files_val'];
			}
			var coldef = _idmap[_mapfield[cell]];
			if(coldef.type === "customtaxh")
			{
				if(Row[cell + '_ids'] !== undefined)
					delete Row[cell + '_ids'];
				if(true === coldef.attribute)
				{
					if(Row[cell + '_visiblefp'] !== undefined)
						delete Row[cell + '_visiblefp'];
				}
			}
			row = row.toString();
			if(_changed[row] !== undefined)
			{
				if(_changed[row][cell] !== undefined)
				{
					var cellv = _changed[row];
					if(cellv[cell] !== undefined)
					{
						delete cellv[cell];
					}
					if(cell === '_downloadable_files')
					{
						if(cellv['_downloadable_files_val'] !== undefined)
						{
							delete cellv['_downloadable_files_val'];
						}
					}
					if(cellv[cell + '_ids'] !== undefined)
					{
						delete cellv[cell + '_ids'];
					}
					if(cellv[cell + '_visiblefp'] !== undefined)
					{
						delete cellv[cell + '_visiblefp'];
					}
				}
			}
			try{
				_grid.removeCellCssStyles("changed");
				_grid.setCellCssStyles("changed", _changed);
			} catch (err) {
				;
			}
		}
	}
	
	function GetEditValue(row,cell,current)
	{
		var Row = [];
		if(_arrEdited[row] === undefined)
		{
			return false;
		}else
		{
			Row = _arrEdited[row];
		}
		if(Row[cell] === undefined)
		{
			return false;
		}
		current.value = Row[cell];
		return true;
	}
	
  
	_grid = new Slick.Grid("#myGrid", _data, gridColumns, gridOptions);
//	var columnpicker = new Slick.Controls.ColumnPicker(columns, _grid, options);
    _grid.setSelectionModel(new Slick.RowSelectionModel({selectActiveRow: false}));
    _grid.registerPlugin(checkboxSelector);
  
 	_grid.onSort.subscribe(function (e, args) {
		for(var ir=0; ir < _arrEdited.length; ir++)
		{
			var row = _arrEdited[ir];
			if(row === undefined) continue;
			for (var key in row) 
			{
			  if (row.hasOwnProperty(key)) 
			  {
			     if(key !== undefined)
				 	return;
			  }
			}
		}
		_grid.setSelectedRows([]);
		
        var field = args.sortCol.field;
		var col = _idmap[_mapfield[args.sortCol.field]];
		if(col === undefined) return;
		var isnumber = false;
		if(col.type !== undefined)
		{
			if(col.type === 'int' || col.type === 'float2' || col.type === 'float3')
				isnumber = true;
		}
        _data.sort(function(a, b){
			var av = a[field];
			var bv = b[field];
			if(isnumber)
			{
				if(av === undefined)
					av = 0;
				if(bv === undefined)
					bv = 0;
				av = parseFloat(av);
				bv = parseFloat(bv);
				if(isNaN(av))
					av = 0;
				if(isNaN(bv))
					bv = 0;
			}else
			{
				if(av === undefined)
					av = "";
				if(bv === undefined)
					bv = "";
			}
            var result = 
                av > bv ? 1 :
                av < bv ? -1 :
                0;

            return args.sortAsc ? result : -result;
        });
	    _grid.invalidateAllRows();
	    _grid.render();
	 });
	 
	_grid.onSelectedRowsChanged.subscribe(function(e,args){
    	var selectedRows = _grid.getSelectedRows().length;
		var all = _grid.getData().length;
		var seltext = ' ' + selectedRows + ' of ' + all;
		$('#bulkeditinfo').text(seltext);
	});
	
	_grid.onBeforeEditCell.subscribe(function(e,args){
		if(_data[args.row] != undefined)
		{
			for (var key in _currentItem) {
			  if (_currentItem.hasOwnProperty(key)) {
			    delete _currentItem[key];
			  }
			}
			var selitem = _data[args.row];
			for (var key in selitem) {
			  if (selitem.hasOwnProperty(key)) {
			     _currentItem[key] = selitem[key];
			  }
			}
			var item = _idmap[_mapfield[args.column.id]];
			
			if(item.field == "grouped_items") 
			{
				if(selitem.product_type != 'simple')
				{
					e.stopPropagation();
					return false;
				}
				
			}
			
			if(item.scope !== undefined)
			{
				if(item.scope == SCOPE.PRODALL)
				{
					if(selitem.post_type == 'product_variation')
					{
						e.stopPropagation();
						return false;
					}
				}
				if(item.scope == SCOPE.PRODSVAR)
				{
					if(selitem.haschildren !== undefined)
					{
						e.stopPropagation();
						return false;
					}
				}
				if(item.scope == SCOPE.PRODSWITHVARS)
				{
					if(selitem.haschildren === undefined)
					{
						e.stopPropagation();
						return false;
					}
				}
				if(item.scope == SCOPE.NONE)
				{
					e.stopPropagation();
					return false;
				}
			}
			
		}
	});
	
	function HandleValueUpdate(what,whatproperty,acell,object)
	{//when value has changed
		object = typeof object !== 'undefined' ? object : _currentItem;
		var sellitem = _data[acell.row];
		var coldef = _idmap[_mapfield[whatproperty]];
		var current = {};
		current.value = "";
		if(coldef.type !== "customtaxh")
		{
			if(GetEditValue(acell.row,whatproperty,current))
			{
				if(current.value === what)
				{//returned to original
					SetEditValue(acell.row,whatproperty,current.value,true);
					_shouldinvalidate = true;
					return;
				}
			}
		}else
		{
			var catsids = sellitem[whatproperty + '_ids'];
			var curcatsids = "";
			var current = {};
			current.value = "";
			if(GetEditValue(acell.row,whatproperty + '_ids',current))
			{
				curcatsids = current.value;
				if(catsids === undefined)
				catsids = "";
				if(curcatsids === undefined)
					curcatsids = "";
				catsids = catsids.split(',');
				curcatsids = curcatsids.split(',');
				
				if (catsids instanceof Array && curcatsids instanceof Array) 
				{
					if(catsids.length == curcatsids.length)
					{
						var breturn = true;
						for(var i=0; i < catsids.length; i++)
						{
							if(curcatsids.indexOf(catsids[i]) === -1)
							{
							   breturn = false;
							   break;
							}
						}
						if(breturn)
						{//when reverted to original value
							SetEditValue(acell.row,coldef.field,sellitem[coldef.field],true);
							_shouldinvalidate = true;
							return;
						}
					}
				}
			}
			if(true === coldef.attribute)
			{
				current.value = "";
				if(GetEditValue(acell.row,whatproperty + '_visiblefp',current))
				{
					SetEditValue(acell.row,coldef.field,sellitem[coldef.field],true);
					_shouldinvalidate = true;
					return;
				}
			}
			
		}
		
		if(object[whatproperty] === undefined)
		{
			object[whatproperty] = "";
		}
		SetEditValue(acell.row,whatproperty,object[whatproperty]);
		if(whatproperty == "_downloadable_files")
		{
			if(object[whatproperty+"_val"] === undefined)
			{
				object[whatproperty+"_val"] = "";
			}
			SetEditValue(acell.row,"_downloadable_files_val",object[whatproperty+"_val"]);
		}
		
		if(coldef.type === "customtaxh")
		{
			if(object[whatproperty + "_ids"] === undefined)
			{
				object[whatproperty + "_ids"] = "";
			}
			SetEditValue(acell.row,whatproperty + "_ids",object[whatproperty + "_ids"]);
			if(true === coldef.attribute)
			{
				if(object[whatproperty + "_visiblefp"] !== undefined)
					SetEditValue(acell.row,whatproperty + "_visiblefp",object[whatproperty + "_visiblefp"]);
			}
		}
		
		if(_changed[acell.row] === undefined)
			_changed[acell.row] = {};
		_changed[acell.row][whatproperty] = "changed";
		try{
			_grid.removeCellCssStyles("changed");
			_grid.setCellCssStyles("changed", _changed);
		} catch (err) {
			;
		}
		return;
	}
	
	function HandleSingleCellUpdate(acell,column)
	{
		var sellitem = _data[acell.row];
		var item = _idmap[_mapfield[column.id]];
		if(item.image !== undefined || item.image_gallery !== undefined)
					return;
		var changedattr = false;
		if(true === item.attribute)
		{
			if(sellitem[item.field + '_visiblefp'] !== _currentItem[item.field + '_visiblefp'])
				changedattr = true;
		}
		if(sellitem[item.field] !== undefined)
		{
			if(_currentItem[item.field] === undefined)
			{
				_currentItem[item.field] = "";
//				if(item.checkbox !== undefined)
//				{
//					if(sellitem[item.field] === "no")
//						return;
//				}
			}
			
			if(sellitem[item.field] !== _currentItem[item.field] || changedattr)
			{
				if(item.type !== undefined)
				{
					if(item.type === 'float2' || item.type === 'float3' || item.type === 'int')
					{
						var newval = sellitem[item.field];
						if(isNaN(newval))
						{//allow only numbers
							sellitem[item.field] = _currentItem[item.field];
							return;
						}
//						if(newval < 0)
//						{
//							sellitem[item.field] = _currentItem[item.field];
//							return;
//						}
					}
				}
				
				HandleValueUpdate(sellitem[item.field],item.field,acell);
			}
		}
	}
	
	function RevertToOriginalTaxonomy(sellitem,acell,item)
	{
		var current = {};
		current.value = "";
		var catsids = sellitem[item.field + '_ids'];
		if(GetEditValue(acell.row,item.field + '_ids',current))
		{
			var curcatsids = current.value;
			if(catsids === undefined)
			catsids = "";
			if(curcatsids === undefined)
				curcatsids = "";
			catsids = catsids.split(',');
			curcatsids = curcatsids.split(',');
			
			if (catsids instanceof Array && curcatsids instanceof Array) 
			{
				if(catsids.length == curcatsids.length)
				{
					var breturn = true;
					for(var i=0; i < catsids.length; i++)
					{
						if(curcatsids.indexOf(catsids[i]) === -1)
						{
						   breturn = false;
						   break;
						}
					}
					if(breturn)
					{//when reverted to original value
						SetEditValue(acell.row,item.field,sellitem[item.field],true);
						if(_changed[acell.row.toString()] !== undefined)
						{
							if(_changed[acell.row][item.field] !== undefined)
							{
								var cellv = _changed[acell.row.toString()];
								if(cellv[item.field] !== undefined)
								{
									delete cellv[item.field];
								}
								if(cellv[item.field + '_ids'] !== undefined)
								{
									delete cellv[item.field + '_ids'];
								}
								if(cellv[item.field + '_visiblefp'] !== undefined)
								{
									delete cellv[item.field + '_visiblefp'];
								}
							}
						}
						try{
							_grid.removeCellCssStyles("changed");
							_grid.setCellCssStyles("changed", _changed);
						} catch (err) {
							;
						}
						_shouldinvalidate = true;
						return true;
					}
				}
			}
		}
		if(true === item.attribute)
		{
			current.value = "";
			if(GetEditValue(acell.row,item.field + '_visiblefp',current))
			{
				SetEditValue(acell.row,item.field,sellitem[item.field],true);
				if(_changed[acell.row.toString()] !== undefined)
				{
					if(_changed[acell.row][item.field] !== undefined)
					{
						var cellv = _changed[acell.row.toString()];
						if(cellv[item.field] !== undefined)
						{
							delete cellv[item.field];
						}
						if(cellv[item.field + '_ids'] !== undefined)
						{
							delete cellv[item.field + '_ids'];
						}
						if(cellv[item.field + '_visiblefp'] !== undefined)
						{
							delete cellv[item.field + '_visiblefp'];
						}
					}
				}
				try{
					_grid.removeCellCssStyles("changed");
					_grid.setCellCssStyles("changed", _changed);
				} catch (err) {
					;
				}
				_shouldinvalidate = true;
				return true;
			}
		}
		return false;
	}
	
	_grid.onBeforeCellEditorDestroy.subscribe(function(e,args)
	{
		if(!_shouldhandle) return;
		var acell = _grid.getActiveCell();
		if(acell === null) return;
		var column = _grid.getColumns()[acell.cell];
		if(column == undefined) return;
		var origsellitem = _data[acell.row];
		var item = _idmap[_mapfield[column.id]];
		if(item === undefined) return;
		if(item.field === 'ID')
		{
			origsellitem['ID'] = _currentItem['ID'];
			return;	
		}
		
		
//		if(sellitem[item.field] !== undefined)
//		{
//			if(_currentItem[item.field] === undefined)
			
		HandleSingleCellUpdate(acell,column);
		
		var clickedonsel = false;
		if($('#linkededit').is(':checked'))
		{
			var iRow = acell.row;
			var selectedRows = _grid.getSelectedRows();
			if(selectedRows.length > 0)
			{
				for(var irow=0; irow < selectedRows.length; irow++)
				{
					var rowid = selectedRows[irow];
					if(rowid === undefined) continue;
					if(_data[rowid] === undefined) continue;
					if(rowid === iRow)
					{//clicked on selected
						clickedonsel = true;
						break;
					}
				}
			}
		}
		
		if(!clickedonsel)
		{
			if(item.field == 'product_type')
				RefreshGroupedItems();
			return;
		}
		
		var origsellitem = _data[acell.row];
		var item = _idmap[_mapfield[column.id]];
		
		
		var changedattr = false;
		if(true === item.attribute)
		{
			if(origsellitem[item.field + '_visiblefp'] !== _currentItem[item.field + '_visiblefp'])
				changedattr = true;
		}
		
		if(origsellitem[item.field] === _currentItem[item.field] && !changedattr )
			return;
		

		var iRow = acell.row;
		var selectedRows = _grid.getSelectedRows();
		var iscustomtaxh = false;
		
		if(selectedRows.length > 0)
		{
			var sellitem = _data[acell.row];
			
			var item = _idmap[_mapfield[column.id]];
			if(item.image !== undefined || item.image_gallery !== undefined)
				return;
			if(sellitem[item.field] === undefined)
				sellitem[item.field] = "";
			{
//					if(_currentItem[item.field] === undefined)
//					{
//						if(sellitem[item.field] === "no")
//							return;
//					}
//					
				{
					var changevalue = origsellitem[item.field];
					var changedids = origsellitem[item.field + '_ids'];
					_shouldinvalidate = true;
					var clickedobject = $.extend(true, {}, sellitem);
					for(var irow=0; irow < selectedRows.length; irow++)
					{
						var rowid = selectedRows[irow];
						if(rowid === undefined) continue;
						if(_data[rowid] === undefined) continue;
						var sellitem = _data[rowid];
						var item = _idmap[_mapfield[column.id]];
						acell.row = rowid;
						if(iRow === rowid)
						{
							continue;
						}
						if(sellitem[item.field] === origsellitem[item.field] )
						{
							if(changedids !== undefined)
							{
								if(sellitem[item.field + '_ids'] === changedids && !changedattr)
									continue;
							}else
								continue;
						}
						if(item.field == "grouped_items") 
						{
							if(sellitem['product_type'] != 'simple' || sellitem['post_type'] == 'product_variation')
							{
								continue;
							}
							
						}
						if(item.scope !== undefined)
						{
							if(item.scope == SCOPE.PRODALL)
							{
								if(sellitem.post_type == 'product_variation')
								{
									continue;
								}
							}
							if(item.scope == SCOPE.PRODSVAR)
							{
								if(sellitem.haschildren !== undefined)
								{
									continue;
								}
							}
							if(item.scope == SCOPE.PRODSWITHVARS)
							{
								if(sellitem.haschildren === undefined)
								{
									continue;
								}
							}
						}
						
					if(item.type !== undefined)
					{
						if(item.type === 'float2' || item.type === 'float3' || item.type === 'int')
						{
							var newval = origsellitem[item.field];
							if(isNaN(newval))
							{
								continue;
							}
//								if(newval < 0)
//								{
//									continue;
//								}
						}else if(item.type === 'customtaxh')
						{
							var catsids = "";
							iscustomtaxh = true;
							if(sellitem[item.field] === undefined)
								sellitem[item.field] = "";
							
							{
								//check visible on product page first
								var breturn = true;
								if(true === item.attribute)
								{
									if(origsellitem[item.field + '_visiblefp'] !== sellitem[item.field + '_visiblefp'])
										breturn = false;
								}
								/////
								var catsids = sellitem[item.field + '_ids'];
								var curcatsids = changedids;
								
								if(catsids === undefined)
									catsids = "";
								if(curcatsids === undefined)
									curcatsids = "";
								catsids = catsids.split(',');
								curcatsids = curcatsids.split(',');
								
								if (catsids instanceof Array && curcatsids instanceof Array) 
								{
									if(catsids.length == curcatsids.length)
									{
										
										for(var i=0; i < catsids.length; i++)
										{
											if(curcatsids.indexOf(catsids[i]) === -1)
											{
											   breturn = false;
											   break;
											}
										}
										if(breturn)
										{
											continue;
										}
									}
								}
							}
							var insertval = "";
							var temparr = [];
							if(true === item.attribute && sellitem['post_type'] == 'product_variation')
							{
								 if(curcatsids instanceof Array)
								 {
								 	temparr = $.extend(true, [], curcatsids);
								 	if(temparr.length > 1)
									{
										temparr.splice(1,temparr.length - 1);
									}
								 }
								 if(changedids.indexOf(',') !== -1)
								 {
								 	insertval = changedids.substring(0,changedids.indexOf(","));
								 }
								if(RevertToOriginalTaxonomy(origsellitem,acell,item))
								{
									sellitem[item.field + '_ids'] = temparr.join();
									if(sellitem[item.field + '_ids'] === "")
										sellitem[item.field] = "";
									else
										sellitem[item.field] = insertval;
									insertval = changevalue;
									 if(changevalue.indexOf(',') !== -1)
									 {
									 	insertval = changevalue.substring(0,changevalue.indexOf(","));
									 }
									sellitem[item.field] = insertval;
									continue;
								}
									
								SetEditValue(rowid,item.field,sellitem[item.field]);
								if(sellitem[item.field + '_ids'] === undefined)
									sellitem[item.field + '_ids'] = "";
								SetEditValue(rowid,item.field + '_ids',sellitem[item.field + '_ids']);
								sellitem[item.field + '_ids'] = temparr.join();
								if(sellitem[item.field + '_ids'] === "")
									sellitem[item.field] = "";
								else
									sellitem[item.field] = insertval;
								if(_changed[rowid] === undefined)
									_changed[rowid] = {};
								_changed[rowid][item.field] = "changed";
								insertval = changevalue;
								 if(changevalue.indexOf(',') !== -1)
								 {
								 	insertval = changevalue.substring(0,changevalue.indexOf(","));
								 }
								sellitem[item.field] = insertval;
								continue;
							}else
							{
								if(RevertToOriginalTaxonomy(origsellitem,acell,item))
								{
									sellitem[item.field + '_ids'] = curcatsids.join();
									if(sellitem[item.field + '_ids'] === "")
										sellitem[item.field] = "";
									else
										sellitem[item.field] = changedids;
									sellitem[item.field] = changevalue;
									if(origsellitem[item.field + '_visiblefp'] !== undefined)
										sellitem[item.field + '_visiblefp'] = origsellitem[item.field + '_visiblefp'];
									continue;
								}
								SetEditValue(rowid,item.field,sellitem[item.field]);
								if(sellitem[item.field + '_ids'] === undefined)
									sellitem[item.field + '_ids'] = "";
								SetEditValue(rowid,item.field + '_ids',sellitem[item.field + '_ids']);
								sellitem[item.field + '_ids'] = curcatsids.join();
								if(sellitem[item.field + '_visiblefp'] !== undefined)
									SetEditValue(rowid,item.field + '_visiblefp',sellitem[item.field + '_visiblefp']);
								if(origsellitem[item.field + '_visiblefp'] !== undefined)
										sellitem[item.field + '_visiblefp'] = origsellitem[item.field + '_visiblefp'];
								if(sellitem[item.field + '_ids'] === "")
									sellitem[item.field] = "";
								else
									sellitem[item.field] = changedids;
								if(_changed[rowid] === undefined)
									_changed[rowid] = {};
								_changed[rowid][item.field] = "changed";
							}
							sellitem[item.field] = changevalue;
							continue;
						}
					}
					//handle value update for normal fields
				
					
					HandleValueUpdate(changevalue,item.field,acell,sellitem);
					sellitem[item.field] = changevalue;
					if(changedids !== undefined)
						sellitem[item.field + '_ids'] = changedids;
					if(item.field == "_downloadable_files" || item.textarea !== undefined)
					{
						var W3Ex = window.W3Ex || {};
  						W3Ex.invalidateselected = true;
						sellitem[item.field+"_val"] = origsellitem[item.field+"_val"];
					}
				}
			}
				
			}
			if(iscustomtaxh)
			{
				if(_shouldinvalidate)
				{
				     var W3Ex = window.W3Ex || {};
  					 W3Ex.invalidateselected = true;
				}
			}
			if(item.field == 'product_type')
				RefreshGroupedItems();
		}
	});

	_grid.onActiveCellChanged.subscribe(function(e,args){
		if(_shouldinvalidate)
		{
			_grid.invalidate();
		   _shouldinvalidate = false;
		}
	});
	
	$('#showsavetool').mouseover(function(){
		$('#savenote').show();
	})
	
	$('#showsavetool').mouseleave(function(){
		$('#savenote').hide();
	})
	
	$('#showlinked').mouseover(function(){
		$('#linkednote').css('visibility','visible');
	})
	
	$('#showlinked').mouseleave(function(){
		$('#linkednote').css('visibility','hidden');
	})


	$('#revertcell').click(function ()
	{
		var acell = _grid.getActiveCell();
		if(acell === null) return;
		if(_data[acell.row] === undefined) return;
		var selitem = _data[acell.row];
		var current = {};
		current.value = "";
		var column = _grid.getColumns()[acell.cell];
		if(column == undefined) return;
		var bupdategrouped = false;
		if(GetEditValue(acell.row,column.id,current))
		{
//			column
			selitem[column.id] = current.value;
			
			if(column.id === "product_type")
				bupdategrouped = true;
				
			if(column.id == "_downloadable_files")
			{
				var current_val = {};
				current_val.value = "";
				if(GetEditValue(acell.row,'_downloadable_files_val',current_val))
				{
					selitem._downloadable_files_val = current_val.value;
				}
			}
			var coldef = _idmap[_mapfield[column.id]];
			if(coldef !== undefined && coldef.type === "customtaxh")
			{
				var current_val = {};
				current_val.value = "";
				if(GetEditValue(acell.row,column.id + '_ids',current_val))
				{
					selitem[column.id + '_ids'] = current_val.value;
				}
				if(true === coldef.attribute)
				{
					current_val.value = "";
					if(GetEditValue(acell.row,column.id + '_visiblefp',current_val))
					{
						selitem[column.id + '_visiblefp'] = current_val.value;
					}
				}
			}
			SetEditValue(acell.row,column.id,current.value,true);
			if(bupdategrouped)
				RefreshGroupedItems();
			_shouldhandle = false;
			_grid.resetActiveCell();
			_grid.invalidate();
			_shouldhandle = true;
		}
		if(GetEditValue(acell.row,column.id + '_visiblefp',current))
		{
			var coldef = _idmap[_mapfield[column.id]];
			if(coldef.type === "customtaxh")
			{
				var current_val = {};
				current_val.value = "";
				if(true === coldef.attribute)
				{
					current_val.value = "";
					if(GetEditValue(acell.row,column.id + '_visiblefp',current_val))
					{
						selitem[column.id + '_visiblefp'] = current_val.value;
					}
				}
			}
			SetEditValue(acell.row,column.id,current.value,true);
			if(bupdategrouped)
				RefreshGroupedItems();
			_shouldhandle = false;
			_grid.resetActiveCell();
			_grid.invalidate();
			_shouldhandle = true;
		}	
	})

	$('#revertrow').click(function ()
	{
		var acell = _grid.getActiveCell();
		if(acell === null) return;
		if(_data[acell.row] === undefined) return;
		var selitem = _data[acell.row];
		var current = {};
		var columns = _grid.getColumns();//[acell.cell];
		var bupdategrouped = false;
		for(var i=0; i <= columns.length; i++)
		{
			var column = columns[i];
			if(column === undefined) continue;
			if(GetEditValue(acell.row,column.id,current))
			{
				selitem[column.id] = current.value;
				
				if(column.id === "product_type")
					bupdategrouped = true;
						
				if(column.id == "_downloadable_files")
				{
					var current_val = {};
					current_val.value = "";
					if(GetEditValue(acell.row,'_downloadable_files_val',current_val))
					{
						selitem._downloadable_files_val = current_val.value;
					}
				}
				var coldef = _idmap[_mapfield[column.id]];
				if(coldef.type === "customtaxh")
				{
					var current_val = {};
					current_val.value = "";
					if(GetEditValue(acell.row,column.id + '_ids',current_val))
					{
						selitem[column.id + '_ids'] = current_val.value;
					}
					if(true === coldef.attribute)
					{
						current_val.value = "";
						if(GetEditValue(acell.row,column.id + '_visiblefp',current_val))
						{
							selitem[column.id + '_visiblefp'] = current_val.value;
						}
					}
				}
				SetEditValue(acell.row,column.id,current.value,true);
			}
			if(GetEditValue(acell.row,column.id + '_visiblefp',current))
			{
				var coldef = _idmap[_mapfield[column.id]];
				if(coldef.type === "customtaxh")
				{
					var current_val = {};
					current_val.value = "";
					if(true === coldef.attribute)
					{
						current_val.value = "";
						if(GetEditValue(acell.row,column.id + '_visiblefp',current_val))
						{
							selitem[column.id + '_visiblefp'] = current_val.value;
						}
					}
				}
				SetEditValue(acell.row,column.id,current.value,true);
			}
		}
		
		try{
			_grid.removeCellCssStyles("changed");
			_grid.setCellCssStyles("changed", _changed);
		} catch (err) {
			;
		}
		if(bupdategrouped)
			RefreshGroupedItems();
		_shouldhandle = false;
		_grid.resetActiveCell();
		_grid.invalidate();
		_shouldhandle = true;
	})

	$('#revertall').click(function ()
	{
		var selectedRows = _grid.getSelectedRows();
		var bupdategrouped = false;
		for(var irow=0; irow < selectedRows.length; irow++)
		{
			var rowid = selectedRows[irow];
			if(rowid === undefined) continue;
			if(_data[rowid] === undefined) continue;
			var selitem = _data[rowid];
			var current = {};
			var columns = _grid.getColumns();//[acell.cell];
			for(var i=0; i <= columns.length; i++)
			{
				var column = columns[i];
				if(column === undefined) continue;
				if(GetEditValue(rowid,column.id,current))
				{
					selitem[column.id] = current.value;
					if(column.id === "product_type")
						bupdategrouped = true;
						
					if(column.id == "_downloadable_files")
					{
						var current_val = {};
						current_val.value = "";
						if(GetEditValue(rowid,'_downloadable_files_val',current_val))
						{
							selitem._downloadable_files_val = current_val.value;
						}
					}
					var coldef = _idmap[_mapfield[column.id]];
					if(coldef.type === "customtaxh")
					{
						var current_val = {};
						current_val.value = "";
						if(GetEditValue(rowid,column.id + '_ids',current_val))
						{
							selitem[column.id + '_ids'] = current_val.value;
						}
						if(true === coldef.attribute)
						{
							current_val.value = "";
							if(GetEditValue(rowid,column.id + '_visiblefp',current_val))
							{
								selitem[column.id + '_visiblefp'] = current_val.value;
							}
						}
					}
					SetEditValue(rowid,column.id,current.value,true);
				}
				if(GetEditValue(rowid,column.id + '_visiblefp',current))
				{
					var coldef = _idmap[_mapfield[column.id]];
					if(coldef.type === "customtaxh")
					{
						var current_val = {};
						current_val.value = "";
						if(true === coldef.attribute)
						{
							current_val.value = "";
							if(GetEditValue(rowid,column.id + '_visiblefp',current_val))
							{
								selitem[column.id + '_visiblefp'] = current_val.value;
							}
						}
					}
					SetEditValue(rowid,column.id,current.value,true);
				}
			}
			
			try{
				_grid.removeCellCssStyles("changed");
				_grid.setCellCssStyles("changed", _changed);
			} catch (err) {
				;
			}
		}
		if(bupdategrouped)
			RefreshGroupedItems();
		_shouldhandle = false;
		_grid.resetActiveCell();
		_grid.invalidate();
		_shouldhandle = true;
	})

	
	
	$('#savechanges').click(function ()
	{
		var _arr = {};
		for(var ir=0; ir < _arrEdited.length; ir++)
		{
			var row = _arrEdited[ir];
			if(row === undefined) continue;
			var bcon = false;
			for (var key in row) 
			{
			  if (row.hasOwnProperty(key)) 
			  {
			     bcon  = true;
				 break;
			  }
			}
			if(!bcon) continue;
			if(_data[ir] === undefined) continue;
			var selitem = _data[ir];
			for (var key in row) 
			{
			  if (row.hasOwnProperty(key)) 
			  {
				  var valtoinsert;
				  valtoinsert = selitem[key];
				  
				  if(key === "_downloadable_files")
				  {
				  	  valtoinsert = selitem._downloadable_files_val;
				  }
				  var coldef = _idmap[_mapfield[key]];
				  if(coldef !== undefined && coldef.type === "customtaxh")
				  {
				  	  valtoinsert = selitem[key + '_ids'];
					  if(valtoinsert === undefined)
					  	valtoinsert = "";
					 
				  }
				  if(key.indexOf('_ids') !== -1)
				  {
				  	  var test = key.replace('_ids','');
					  if(_mapfield[test] !== undefined)
					  	continue;
				  }
				  if(_arr[key] === undefined)
				   	  _arr[key] = [];
					  
				  _arr[key].push(selitem.ID + '$#' + selitem.post_parent + '$#' + valtoinsert);
				  break;
			  }
			}
		}

		var bcon = false;
		for (var key in _arr) 
		{
		  if (_arr.hasOwnProperty(key)) 
		  {
		      _arr[key] = _arr[key].join('#$');
			  bcon = true;
		  }
		}
		if(!bcon)
		{
			return;
		}
		if($('#confirmsave').is(':checked'))
		{
			_confirmationclick = "save";
			$("#confirmdialog").dialog("open");	
			return;
		}
		SaveChanges('savechanges');
	});
	
$("#exportdialog").dialog({			
    autoOpen: false,
    height: 340,
    width: 480,
    modal: true,
	draggable:true,
	resizable:false,
	title:"Export to CSV",
	closeOnEscape: true,
	create: function (event, ui) {
        $(this).dialog('widget')
            .css({ position: 'fixed'})
    },
	open: function( event, ui ) {
		 var d = $('.ui-dialog:visible');
		 $(d).css('z-index',300002);
		 $('.ui-dialog:visible').wrap('<div class="w3exabe w3exabedel" />');
		  $('.ui-widget-overlay').each(function () {
			 $(this).next('.ui-dialog').andSelf().wrapAll('<div class="w3exabe w3exabedel" />');
		});
		 $('#exportdialog').css('height','auto');
		 $('input:radio[name=exportwhat]').each(function () { $(this).prop('checked', false); });	
		 $('#exportall').prop('checked',true);
	},
	close: function( event, ui ) {
		$(".w3exabedel").contents().unwrap();
	},
 	buttons: {
	  "OK": function() {
	  	var selid = $('input[name=exportwhat]:checked').attr('id');
		var ajaxarr = {};
		ajaxarr.action = 'wpmelon_adv_bulk_edit';
		ajaxarr.type = 'exportproducts';
		ajaxarr.nonce = W3ExABE.nonce;
		var selectedRows = _grid.getSelectedRows();
		var _arrData = [];
		var strCSV = "";
		var _arrParents = [];
		var delimiter = $('#exportdelimiter').val();
		var buserealmeta = $('#userealmeta').is(':checked');
		if(selid == "exportall")
		{
			strCSV = 'id' + delimiter + 'post_parent' + delimiter + 'image' + delimiter + 'image_gallery';
			for(var i=0; i < _idmap.length; i++)
			{
				var col = _idmap[i];
				if(col.field == '_thumbnail_id' || col.field == 'ID' || col.field == '_product_image_gallery')
					continue;
				if(_mapfield[col.field] === undefined)
					continue;
				 strCSV+= delimiter + col.field;
			}
			for(var irow=0; irow < _data.length; irow++)
			{
				if(_data[irow] === undefined) continue;
				var selitem = _data[irow];
				strCSV+= '\n' + selitem.ID + delimiter + selitem.post_parent;
				if(selitem._thumbnail_id_original !== undefined)
					strCSV+= delimiter + W3Ex.uploaddir + "/" + selitem._thumbnail_id_original;
				else
					strCSV+= delimiter + "";
				if(selitem._product_image_gallery_original !== undefined)
					strCSV+= delimiter + selitem._product_image_gallery_original;
				else
					strCSV+= delimiter + "";
				for(var i=0; i < _idmap.length; i++)
				{
					var col = _idmap[i];
					if(col.field == '_thumbnail_id' || col.field == 'ID' || col.field == '_product_image_gallery')
						continue;
					 var val = selitem[col.field];
				  	 if(val === undefined || val === null)
					 	val = "";
					var realval = '';
					if(buserealmeta)
					{
						switch(col.field)
						{
							case "_visibility":
							{
								if(val == "Catalog/search")
									realval = "visible";
								if(val == "Catalog")
									realval = "catalog";
								if(val == "Search")
									realval = "search";
								if(val== "Hidden")
									realval = "hidden";
								val = realval;
							}break;
							case "_download_type":
							{
								if(val == "Application")
									realval = "application";
								if(val == "Music")
									realval = "music";
								val = realval;
							}break;
							case "_tax_class":
							{
								realval = "";
								if(val == "Reduced Rate")
									realval = "reduced-rate";
								if(val == "Zero Rate")
									realval = "zero-rate";
								val = realval;
							}break;
							case "_tax_status":
							{
								realval = "taxable";
								if(val == "Shipping only")
									realval = "shipping";
								if(val == "None")
									realval = "none";
								val = realval;
							}break;
							case "_sold_individually":
							{
								if(val == "no")
									realval = "";
								if(val == "yes")
									realval = "yes";
								val = realval;
							}break;
							case "_backorders":
							{
								if(val == "Do not allow")
									realval = "no";
								if(val == "Allow but notify")
									realval = "notify";
								if(val == "Allow")
									realval = "yes";
								val = realval;
							}break;
						}
					}
					
					 if(val.indexOf('"') !=- 1)
					 {
					 	val = replaceAll(val,'"', '""');
						val = '"' + val + '"';
					 }
					 if(val.indexOf(delimiter) !=- 1 && val.indexOf('"') ==- 1)
					 {
					 	val = '"' + val + '"';
					 }
					 if(val.indexOf(',') !=- 1 && val.indexOf('"') ==- 1)
					 {
					 	val = '"' + val + '"';
					 }
					 if(val.indexOf('\n') !=- 1 && val.indexOf('"') ==- 1)
					 {
					 	val = '"' + val + '"';
					 }
					 strCSV+= delimiter + val;
				}
				
			}
		}else
		{
			if(selectedRows.length > 0)
			{
				strCSV = 'id' + delimiter + 'post_parent' + delimiter + 'image' + delimiter + 'image_gallery';
				for(var i=0; i < _idmap.length; i++)
				{
					var col = _idmap[i];
					if(col.field == '_thumbnail_id' || col.field == 'ID' || col.field == '_product_image_gallery')
						continue;
					 strCSV+= delimiter + col.field;
				}
				for(var irow=0; irow < selectedRows.length; irow++)
				{
					var rowid = selectedRows[irow];
					if(rowid === undefined) continue;
					if(_data[rowid] === undefined) continue;
					var selitem = _data[rowid];
					strCSV+= '\n' + selitem.ID + delimiter + selitem.post_parent;
					if(selitem._thumbnail_id_original !== undefined && W3Ex.uploaddir !== undefined)
						strCSV+= delimiter + W3Ex.uploaddir + "/" + selitem._thumbnail_id_original;
					else
						strCSV+= delimiter + "";
					if(selitem._product_image_gallery_original !== undefined)
						strCSV+= delimiter + selitem._product_image_gallery_original;
					else
						strCSV+= delimiter + "";
					for(var i=0; i < _idmap.length; i++)
					{
						var col = _idmap[i];
						if(col.field == '_thumbnail_id' || col.field == 'ID' || col.field == '_product_image_gallery')
							continue;
						 var val = selitem[col.field];
					  	if(val === undefined || val === null)
					 	   val = "";
						var realval = '';
						if(buserealmeta)
						{
							switch(col.field)
							{
								case "_visibility":
								{
									if(val == "Catalog/search")
										realval = "visible";
									if(val == "Catalog")
										realval = "catalog";
									if(val == "Search")
										realval = "search";
									if(val== "Hidden")
										realval = "hidden";
									val = realval;
								}break;
								case "_download_type":
								{
									if(val == "Application")
										realval = "application";
									if(val == "Music")
										realval = "music";
									val = realval;
								}break;
								case "_tax_class":
								{
									realval = "";
									if(val == "Reduced Rate")
										realval = "reduced-rate";
									if(val == "Zero Rate")
										realval = "zero-rate";
									val = realval;
								}break;
								case "_tax_status":
								{
									realval = "taxable";
									if(val == "Shipping only")
										realval = "shipping";
									if(val == "None")
										realval = "none";
									val = realval;
								}break;
								case "_sold_individually":
								{
									if(val == "no")
										realval = "";
									if(val == "yes")
										realval = "yes";
									val = realval;
								}break;
								case "_backorders":
								{
									if(val == "Do not allow")
										realval = "no";
									if(val == "Allow but notify")
										realval = "notify";
									if(val == "Allow")
										realval = "yes";
									val = realval;
								}break;
							}
						}
							
						 if(val.indexOf('"') !=- 1)
						 {
						 	val = replaceAll(val,'"', '""');
							val = '"' + val + '"';
						 }
						 if(val.indexOf(delimiter) !=- 1 && val.indexOf('"') ==- 1)
						 {
						 	val = '"' + val + '"';
						 }
						 if(val.indexOf(',') !=- 1 && val.indexOf('"') ==- 1)
						 {
						 	val = '"' + val + '"';
						 }
						 if(val.indexOf('\n') !=- 1 && val.indexOf('"') ==- 1)
						 {
						 	val = '"' + val + '"';
						 }
						 strCSV+= delimiter + val;
					}
				}
			}
		}
		if(strCSV == "")
		{
			$( this ).dialog( "close" );
			return;
		}			
		var $elem = $('.ui-dialog-buttonset > .ui-button:visible').first();
        $elem.css('position','relative').append('<div class="showajax"></div>');
		$('.showajax').css({
			left:'15px'
		});
		$elem.button("disable");
		ajaxarr.data = strCSV;
		var dlg = $(this);
		jQuery.ajax({
		     type : "post",
		     dataType : "json",
		     url : W3ExABE.ajaxurl,
		     data : ajaxarr,
		     success: function(response) {
					$('.showajax').remove();
					$('#exportiframe').attr('src',response.products);
					$elem.button("enable");
					var link = '<a href ="'+response.products+'" target="_blank">Download CSV File</a>';
					$('#exportinfo').html( link + ' (if download did not start automatically)');
					dlg.dialog( "close" );
					
		     },
			  error:function (xhr, status, error) 
			  {
				  $('.showajax').remove();
				  $elem.button("enable");
				  dlg.dialog( "close" );
				  $('#debuginfo').html(xhr.responseText);
			  },
			 complete:function (args)
			 {
			  	//uncomment to debug
//			    $('#debuginfo').html(args.responseText);
			 }
		  }) ;
	  	
	  },
	  Cancel: function()
	  {
		  $( this ).dialog( "close" );
	  }
	 }
});



$("#pluginsettings").dialog({			
    autoOpen: false,
    height: 590,
    width: 780,
    modal: true,
	draggable:true,
	resizable:true,
	title:"Plugin Settings",
	closeOnEscape: true,
	create: function (event, ui) {
        $(this).dialog('widget')
            .css({ position: 'fixed'})
    },
	open: function( event, ui ) {
		
		 var d = $('.ui-dialog:visible');
		 $(d).css('z-index',300002);
		  $('.ui-dialog:visible').wrap('<div class="w3exabe w3exabedel" />');
		  $('.ui-widget-overlay').each(function () {
			 $(this).next('.ui-dialog').andSelf().wrapAll('<div class="w3exabe w3exabedel" />');
		});
		 $('#pluginsettings').css('height','520px');
	},
	close: function( event, ui ) {
		$(".w3exabedel").contents().unwrap();
	},
 	buttons: {
	  "OK": function() {
	  	var settings = {};
		if($('#gettotalnumber').is(':checked'))
			settings['settgetall'] = 1;
		else
			settings['settgetall'] = 0;
		if($('#retrievevariations').is(':checked'))
			settings['settgetvars'] = 1;
		else
			settings['settgetvars'] = 0;
		if($('#includechildren').is(':checked'))
			settings['incchildren'] = 1;
		else
			settings['incchildren'] = 0;
		if($('#disattributes').is(':checked'))
			settings['disattributes'] = 1;
		else
			settings['disattributes'] = 0;
		if($('#converttoutf8').is(':checked'))
			settings['converttoutf8'] = 1;
		else
			settings['converttoutf8'] = 0;
		if($('#dontcheckusedfor').is(':checked'))
			settings['dontcheckusedfor'] = 1;
		else
			settings['dontcheckusedfor'] = 0;
		if($('#calldoaction').is(':checked'))
			settings['calldoaction'] = 1;
		else
			settings['calldoaction'] = 0;
		if($('#confirmsave').is(':checked'))
			settings['confirmsave'] = 1;
		else
			settings['confirmsave'] = 0;
		if($('#showattributes').is(':checked'))
		{
			settings['showattributes'] = 1;
			$('.showattributes').show();
		}			
		else
		{
			settings['showattributes'] = 0;
			$('.custattributes').val('');
			$('.custattributes').trigger("chosen:updated");
			$('.showattributes').hide();
		}
		if($('#showprices').is(':checked'))
		{
			settings['showprices'] = 1;
			$('.showprices').show();
		}
		else
		{
			settings['showprices'] = 0;
			$('#pricevalue').val('');
			$('#salepricevalue').val('');
			$('.showprices').hide();
		}
		if($('#showskutags').is(':checked'))
		{
			settings['showskutags'] = 1;
			$('.showskutags').show();
		}
		else
		{
			settings['showskutags'] = 0;
			$('#skuvalue').val('');
			$('#tagsparams').val('');
			$('#tagsparams').trigger("chosen:updated");
			$('.showskutags').hide();
		}
		if($('#showdescriptions').is(':checked'))
		{
			settings['showdescriptions'] = 1;
			$('.showdescriptions').show();
		}
		else
		{
			settings['showdescriptions'] = 0;
			$('#descvalue').val('');
			$('#shortdescvalue').val('');
			$('.showdescriptions').hide();
		}
			
		var prodlimit = $('#productlimit').val();
		if(!isNaN(prodlimit))
		{
			settings['settlimit'] = prodlimit;
			_recordslimit = parseInt(prodlimit);
		}
		var selcustomfields = [];
		$('.customfield input:checked').each(function(){
			selcustomfields.push($(this).attr('data-id'));
		})
		
		settings['selcustomfields'] = selcustomfields;
		W3Ex.customfieldssel = selcustomfields;
		var ajaxarr = {};
		ajaxarr.action = 'wpmelon_adv_bulk_edit';
		ajaxarr.type = 'savesettings';
		ajaxarr.nonce = W3ExABE.nonce;
      	var $elem = $('.ui-dialog-buttonset > .ui-button:visible').first();
        $elem.css('position','relative').append('<div class="showajax"></div>');
		$('.showajax').css({
			left:'15px'
		});
		$elem.button("disable");
		ajaxarr.data = settings;
		var dlg = $(this);
		jQuery.ajax({
		     type : "post",
		     dataType : "json",
		     url : W3ExABE.ajaxurl,
		     data : ajaxarr,
		     success: function(response) {
					$('.showajax').remove();
					$elem.button("enable");
					ShowCustomSearchFilters();
					$('.makechosen').chosen({disable_search_threshold: 10});
					dlg.dialog( "close" );
					
		     },
			  error:function (xhr, status, error) 
			  {
				  $('.showajax').remove();
				  $elem.button("enable");
				  dlg.dialog( "close" );
				  $('#debuginfo').html(xhr.responseText);
			  },
			 complete:function (args)
			 {
			  	//uncomment to debug
//			    $('#debuginfo').html(args.responseText);
			 }
		  }) ;
	  	
	  },
	  Cancel: function()
	  {
		  $( this ).dialog( "close" );
	  }
	 }
});

$("#bulkdialog").dialog({			
    autoOpen: false,
    height: 620,
    width: 1150,
    modal: true,
	draggable:true,
	resizable:true,
	closeOnEscape: true,
	title:"Bulk edit selected products",
	create: function (event, ui) {
        $(this).dialog('widget')
            .css({ position: 'fixed'})
    },
	open: function( event, ui ) {
		 var d = $('.ui-dialog:visible');
		 $(d).css('z-index',300002);
		 $('.ui-dialog:visible').wrap('<div class="w3exabe w3exabedel" />');
		  $('#bulkdialog').css('height','520px');
		  $('.ui-widget-overlay').each(function () {
			 $(this).next('.ui-dialog').andSelf().wrapAll('<div class="w3exabe w3exabedel" />');
			
		});
//		$('#bulkdialog .bulkvalue').val('');
		$('#bulkdialog .bulkset').each(function(){
			var item = $(this);
			if(!item.prop('checked'))
			{
				var column = item.attr('data-id');
				var coldef = _idmap[_mapfield[column]];
				if(coldef !== undefined && coldef.type === "customtaxh")
				{
					$('#bulk' + column).prop('disabled', true).trigger("chosen:updated");
					$('#bulkadd' + column).prop("disabled",true);
				}
				else
					$('#bulk' + column).prop("disabled",true);
			}else
			{
				var column = item.attr('data-id');
				var coldef = _idmap[_mapfield[column]];
				if(coldef !== undefined && coldef.type === "customtaxh")
				{
					if(true === coldef.attribute)
					{
						var $parent = $(this).parent().parent();
						if( $parent.find(".selectvisiblefp").is(':enabled'))
						{
							var setvisval = $parent.find(".selectvisiblefp").val();
							if(setvisval == "onlyset")
							{
								$parent.find(".visiblefp").prop("disabled",false);
								$parent.find(".selectusedforvars").prop("disabled",true);
								$parent.find(".usedforvars").prop("disabled",true);
								$('#bulkadd' + column).prop("disabled",true);
								$('#bulk' + column).prop('disabled', true).trigger("chosen:updated");
								return;
							}
						}
						if( $parent.find(".selectusedforvars").is(':enabled'))
						{
							var setvisval = $parent.find(".selectusedforvars").val();
							if(setvisval == "onlyset")
							{
								$parent.find(".usedforvars").prop("disabled",false);
								$parent.find(".selectvisiblefp").prop("disabled",true);
								$parent.find(".visiblefp").prop("disabled",true);
								$('#bulkadd' + column).prop("disabled",true);
								$('#bulk' + column).prop('disabled', true).trigger("chosen:updated");
								return;
							}
						}
					}
					$('#bulk' + column).prop('disabled', false).trigger("chosen:updated");
					$('#bulkadd' + column).prop("disabled",false);
					
				}					
				else
					$('#bulk' + column).prop("disabled",false);
			}
		})
		/*$('#bulkdialog .selectvisiblefp').each(function(){
			var item = $(this);
			if(!item.prop('checked'))
			{
				item.parent().parent().find('.visiblefp').attr("disabled","disabled");
			}else
			{
				item.parent().parent().find('.visiblefp').removeAttr("disabled");
			}
		})*/
		
	},
	close: function( event, ui ) {
		$(".w3exabedel").contents().unwrap();
	},
 	buttons: {
	  "OK": function() {
	  	var params = {};
		$('#bulkdialog .bulkvalue:visible').each(function(){
			var item = $(this);
//			if(!item.is(':visible')) continue;
			var value = item.val();
			var id = item.attr('data-id');
			if(value != "")
			{
				params[id] = $('#bulk'+ id).val();
				params[id + 'value'] = value;
				if(id === "_sale_price")
				{
					if(params[id] == 'decvaluereg' || params[id] == 'decpercentreg')
					{
						params.isskipsale = $('#saleskip').prop('checked');
					}
				}
				if(params[id] === "replace")
				{
					params[id + 'ifignore'] = item.parent().parent().find('.inputignorecase').prop('checked');
					params[id + 'replacewith'] = item.parent().parent().find('.inputwithvalue').val();
				}
			}
		})
		$('#bulkdialog select option[value="delete"]:selected').each(function(){
			var item = $(this).parent();
			if(item.is(':visible'))
			{
				var id = item.attr('data-id');
				params[id] = 'delete';
				params[id + 'value'] = 0;
			}
		})
		
		$('#bulkdialog .bulkset:checked').each(function(){
			var item = $(this);
			if(item.is(':visible'))
			{
				var id = item.attr('data-id');
				params[id] = id;
				params[id+ 'value'] = $('#bulkdialog select#bulk' + id).val();
				if(item.attr('data-type') === "customtaxh")
				{
					var cats = [];
					var textvals = "";
					$("#bulk"+id+".catselset :selected").each(function(){
			    		 cats.push($(this).val());
						 if(textvals == "")
						 	textvals = $.trim($(this).text());
						 else
						 	textvals+= ', ' + $.trim($(this).text());
			   		});
					params[id+ 'value_ids'] = cats;
					params[id+ 'value'] = textvals;
					if($('#bulkdialog select#bulkadd' + id).length > 0)
						params[id+ 'action'] = $('#bulkdialog select#bulkadd' + id).val();
					var coldef = _idmap[_mapfield[id]];
					if(coldef !== undefined && true === coldef.attribute)
					{
						var $select = item.parent().parent().find('.selectvisiblefp');
						if($select.length > 0 && $select.is(':enabled'))
						{
							if($select.val() !== "skip")
							{
								params[id+ '_visiblefp'] = item.parent().parent().find('.visiblefp').is(':checked');
								if(params[id+ '_visiblefp'] == true)
									params[id+ '_visiblefp'] = 1;
								else
									params[id+ '_visiblefp'] = 0;
								var selectval = $select.val();
								if(selectval == "onlyset")
								{
									params[id+ '_onlyvisiblefp'] = 1;
								}
							}
						}
						$select = item.parent().parent().find('.selectusedforvars');
						if($select.length > 0 && $select.is(':enabled'))
						{
							if($select.val() !== "skip")
							{
								params[id+ '_usedforvars'] = item.parent().parent().find('.usedforvars').is(':checked');
								if(params[id+ '_usedforvars'] == true)
									params[id+ '_usedforvars'] = 1;
								else
									params[id+ '_usedforvars'] = 0;
								var selectval = $select.val();
								if(selectval == "onlyset")
								{
									params[id+ '_onlyusedforvars'] = 1;
								}
							}
						}	
					}
				}
			}
		})

		HandleBulkUpdate(params);
	     $( this ).dialog( "close" );
	  },
	  Cancel: function()
	  {
		  $( this ).dialog( "close" );
	  }
	 }
});


		
	$('#bulkedit').click(function ()
	{
		$('#bulkdialog').dialog("open");
	})
	
	$('#selectedit').click(function ()
	{
		$('#selectdialog').dialog("open");
	})
	
	
	$('#getproducts').click(function ()
	{
		LoadProducts('getproducts');
	});
	  
	function LoadProducts(control,pagination,isnext)
	{
		pagination = typeof pagination !== 'undefined' ? pagination : false;
		isnext = typeof isnext !== 'undefined' ? isnext : true;
		var bhasunsaved = false;
		for(var ir=0; ir < _arrEdited.length; ir++)
		{
			var row = _arrEdited[ir];
			if(row === undefined) continue;
			if(_data[ir] === undefined) continue;
			bhasunsaved = true;
			break;
		}
		if(bhasunsaved && control === "savechanges")
		{
			var ret = confirm("Changes will be lost, continue ?");
			if (!ret) 
			{
			    return;
			} 
		}
		_grid.resetActiveCell();
		_grid.invalidate();
		_grid.resetActiveCell();
		_grid.invalidate();
		DisableAllControls(true);
		if(control == 'getproducts')
		{
			$('#getproducts').parent().append('<div class="showajax"></div>');
			_currentoffset = 1;
			pagination = false;
			isnext = false;
		}else
		{
			$('#pagingholder').append('<div class="showajax"></div>');
			$('.showajax').css({
				left:'170px',
				top:'30px'
			});
		}
	  	var ajaxarr = {};
		ajaxarr.action = 'wpmelon_adv_bulk_edit';
		ajaxarr.type = 'loadproducts';
		ajaxarr.nonce = W3ExABE.nonce;
		var cats = [];
		var attrs = [];
		var priceparam = {};
		var saleparam = {};
		var titleparam = {};
		var descparam = {};
		var shortdescparam = {};
		var customparam = [];
		var tagsparam = [];
		var skuparam = {};
		var custsearchparam = [];
		
		$('.trcustom').each(function ()
		{
			var $tds = $(this).children('td');
			$tds.each(function ()
			{
				var field = $(this).attr('data-field');
				if(field == 'name')
				{
					customparam.push($(this).text());
				}
			})
		})
		
		$(".catsel :selected").each(function(){
    		 cats.push($(this).val());
   		});
		
		$(".custattributes :selected").each(function(){
			var attr = $(this).val();
			if($(this).val() != "")
			{
				if(W3Ex.attributes != undefined)
				{
					if(W3Ex.attributes[parseInt(attr)] != undefined)
					{
						var attrobj = W3Ex.attributes[parseInt(attr)];
						attrs.push(attrobj);
					}
				}
			}
   		});
		
		$("#tagsparams :selected").each(function(){
			if($(this).val() != "")
    			tagsparam.push($(this).val());
   		});
		
		var price = $('#pricevalue').val();
		price = $.trim(price);
		if(price != "")
		{
			price = parseFloat(price);
			if(price !== NaN && price >= 0)
			{
				priceparam.price = price;
				priceparam.value = $('#price').val();
			}
			
		}
		var sale = $('#salepricevalue').val();
		sale = $.trim(sale);
		if(sale != "")
		{
			sale = parseFloat(sale);
			if(sale !== NaN && sale >= 0)
			{
				saleparam.price = sale;
				saleparam.value = $('#saleprice').val();
			}
			
		}
		var title = $('#titlevalue').val();
		title = $.trim(title);
		if(title != "")
		{
			titleparam.title = title;
			titleparam.value = $('#titleparams').val();
		}
		
		title = $('#descvalue').val();
		title = $.trim(title);
		if(title != "")
		{
			descparam.title = title;
			descparam.value = $('#descparams').val();
		}
		title = $('#shortdescvalue').val();
		title = $.trim(title);
		if(title != "")
		{
			shortdescparam.title = title;
			shortdescparam.value = $('#shortdescparams').val();
		}
		
		var sku = $('#skuvalue').val();
		sku = $.trim(sku);
		if(sku != "")
		{
			skuparam.title = sku;
			skuparam.value = $('#skuparams').val();
		}
		$('.customfieldtable').each(function(){
			var custitem = {};
			var $par = $(this);
			custitem.type = $par.attr('data-type');
			custitem.id = $par.attr('data-id');
			if(custitem.type !== "custom" && custitem.type !== "customh")
			{
				var itemtitle = $par.find('input').val();
				itemtitle = $.trim(itemtitle);
				if(itemtitle !== "")
				{
					custitem.title = itemtitle;
					custitem.value = $par.find('select').val();
					custsearchparam.push(custitem);
				}
				
			}else
			{
				$par.find("select :selected").each(function(){
					if($(this).val() != "")
					{
						if(custitem.array === undefined) custitem.array = [];
						custitem.array.push($(this).val());
					}
		    			
		   		});
				if(custitem.array !== undefined)
				{
					custsearchparam.push(custitem);
				}
			}
		})
		
		$('#myGrid').prepend('<div id="dimgrid" style="position: absolute;top:0;left:0;width: 100%;height:100%;z-index:102;opacity:0.4;filter: alpha(opacity = 40);background-color:grey;"></div>');
		if(control !== 'getproducts')
		{
			cats =  _pagecats;
			attrs =  _pageattrs;
			priceparam = _pagepriceparam;
			saleparam = _pagesaleparam;
			titleparam = _pagetitleparam;
			descparam = _pagedescparam;
			shortdescparam = _pageshortdescparam;
			customparam = _pagecustomparam;
			skuparam = _pageskuparam;
			tagsparam = _pagetagsparam;
			custsearchparam = _pagecustsearchparam;
		}else
		{
			_pagecats = cats;
			_pageattrs = attrs;
			_pagepriceparam = priceparam;
			_pagesaleparam = saleparam;
			_pagetitleparam = titleparam;
			_pagedescparam = descparam;
			_pageshortdescparam = shortdescparam;
			_pagecustomparam = customparam;
			_pageskuparam = skuparam;
			_pagetagsparam = tagsparam;
			_pagecustsearchparam = custsearchparam;
		}
	
		ajaxarr.catparams = cats;
		if($('#categoryor').is(':checked'))
		{
			ajaxarr.categoryor = true;
		}
		ajaxarr.attrparams = attrs;
		ajaxarr.priceparam = priceparam;
		ajaxarr.saleparam = saleparam;
		ajaxarr.titleparam = titleparam;
		ajaxarr.descparam = descparam;
		ajaxarr.shortdescparam = shortdescparam;
		ajaxarr.customparam = customparam;
		ajaxarr.custsearchparam = custsearchparam;
		ajaxarr.skuparam = skuparam;
		ajaxarr.tagsparams = tagsparam;
		ajaxarr.ispagination = pagination;
		ajaxarr.isnext = isnext;
		ajaxarr.isvariations = $('#getvariations').is(':checked');
		jQuery.ajax({
		     type : "post",
		     dataType : "json",
		     url : W3ExABE.ajaxurl,
		     data : ajaxarr,
		     success: function(response) {
			 		_changed = {};
					while(_arrEdited.length > 0) {
					    _arrEdited.pop();
					}
					$('#dimgrid').remove();
					$('.showajax').remove();
					DisableAllControls(false);
					if(_data !== undefined || _data !== null)
			 			_grid.setSelectedRows([]);
					if(response.products === undefined || response.products === null)
					{
//						if(_data === undefined || _data === null)
		 				_data = [];
						_grid.setData(_data);
						_totalrecords = 0;
						_currentoffset = 1;
						$('#butprevious').prop("disabled",true);
						$('#gotopage').prop("disabled",true);
						$('#butnext').prop("disabled",true);
						$('#totalpages').text('');
						$('#viewingwhich').text('');
						$('#totalrecords').text(_totalrecords);
						$('#gotopagenumber').val(_currentoffset);
						$('#bulkeditinfo').text(' 0 of 0');
						$('#debuginfo').html('');
						return;
					}
			 		var newdata = response.products;
					_totalrecords = parseInt(response.total);
					var hasnext = response.hasnext;
					if(hasnext || hasnext === "true")
						_hasnext = true;
					else
						_hasnext = false;
					var isbegin = response.isbegin;
					if(isbegin || isbegin === "true")
					{
						_currentoffset = 1;
					}
					$('#gotopagenumber').val(_currentoffset);
					
					if(_totalrecords <= _recordslimit)
					{
						$('#butprevious').prop("disabled",true);
						$('#gotopage').prop("disabled",true);
						$('#butnext').prop("disabled",true);
						$('#totalpages').text('');
						$('#viewingwhich').text('');
					}else
					{
						$('#butprevious').prop("disabled",false);
						$('#gotopage').prop("disabled",false);
						if(_hasnext)
							$('#butnext').prop("disabled",false);
						else
							$('#butnext').prop("disabled",true);
						var viewtext = "";
						var tpages = 0;
						tpages = Math.ceil(_totalrecords/_recordslimit);
						$('#totalpages').text('(' + String(tpages) + ' pages)' );
//						var viewing = _currentoffset;
//						viewtext = "";
//						if(((_currentoffset*_recordslimit)) > _totalrecords)
//						{
//							viewing--;
//							viewtext = String((viewing*_recordslimit) +1)+ '-' + String(_totalrecords);
//						}else
//						{
//							viewing--;
//							viewtext = String((viewing*_recordslimit) +1)+ '-' + String(_currentoffset*_recordslimit);
//						}
//						$('#viewingwhich').text('; Viewing ' + viewtext );
					}
					if(_totalrecords == -1)
					{
						if(_currentoffset !== 1)
						{
							$('#butprevious').prop("disabled",false);
							$('#gotopage').prop("disabled",false);
						}
						if(_hasnext)
							$('#butnext').prop("disabled",false);
						else
							$('#butnext').prop("disabled",true);
					}
					if(_currentoffset == 1)
					{
						$('#butprevious').prop("disabled",true);
						$('#gotopage').prop("disabled",true);
					}
					$('#totalrecords').text(_totalrecords);

					if(newdata === null || newdata === undefined)
						newdata = [];
					_grid.setData(newdata);
					_data = newdata;
					try{
						_grid.removeCellCssStyles("changed");
						_grid.setCellCssStyles("changed", _changed);
					} catch (err) {
						;
					}
					GenerateGroupedItems();
					_shouldhandle = false;
					_grid.invalidate();
					_shouldhandle = true;	
					var all = newdata.length;
					var seltext = ' 0 of ' + all;
					$('#bulkeditinfo').text(seltext);
					$('#debuginfo').html('');
		     }, complete:function (args)
				  {
				  	//uncomment to debug
//					$('#debuginfo').html(args.responseText);
				  }
			 , error:function (xhr, status, error) 
			  {
				  $('#dimgrid').remove();
				  $('.showajax').remove();
				  DisableAllControls(false);
				  $('#debuginfo').html(xhr.responseText);
			  }
		  }) ;
	}
	
	function RefreshGroupedItems()
	{
		var arrindexes = [];
		var arrnames = [];
		for(var ir=0; ir < _data.length; ir++)
		{
			if(_data[ir] === undefined) continue;
			var selitem = _data[ir];

			if(selitem.product_type !== 'grouped') continue;
			arrindexes.push(selitem.ID);
			arrnames.push(selitem.post_title);
			var exists = false;
			for(var k=0; k<_loadedgrouped.length;k++)
			{
				if(_loadedgrouped[k] == selitem.ID)
				{
					exists = true;
					break;
				}
			}
			if(!exists)
			{
				_loadedgrouped.push(selitem.ID);
			}
		}
		var arrhtml = [];
		var arrexids = [];
		var removeids = [];
		$('.grouped_items input').each(function(){
			var id = $(this).val();
			if( id > 0)
			{
				arrexids.push(id);
			}
				
		})
		for(var i=0; i<arrindexes.length;i++)
		{
			var idn = arrindexes[i];
			var exists = false;
			for(var j=0; j<arrexids.length;j++)
			{
				var idex = arrexids[j];
				if(idex == idn)
				{
					exists = true;
					break;
				}
			}
			if(!exists)
			{
				if(arrnames[i] !== undefined)
				{
					arrexids.push(idn);
					$('.grouped_items ul').append('<li><label class="selectit"><input value="'+idn+'" type="checkbox" data-name="'+arrnames[i]+'" />'+arrnames[i]+'</label></li>');
					$('#bulkgrouped_items').append('<option value="'+idn+'">'+arrnames[i]+'</option>');
				}
			}
		}
		
		//remove changed
		
		for(var k=0; k<_loadedgrouped.length;k++)
		{
			var exists = false;
			for(var j=0; j<arrindexes.length;j++)
			{
				var idex = arrindexes[j];
				if(idex == _loadedgrouped[k])
				{
					exists = true;
					break;
				}
			}
			if(!exists)
			{
				removeids.push(_loadedgrouped[k]);
			}
		}
		
		
		for(var i=0; i<removeids.length;i++)
		{
			var idn = removeids[i];
			if($('.grouped_items input[value="'+idn+'"]').length > 0)
			{
				$('.grouped_items input[value="'+idn+'"]').parent().parent().remove();
				$('#bulkgrouped_items option[value="'+idn+'"]').remove();
			}
		}
	}
	
	function GenerateGroupedItems()
	{
		var arrindexes = [];
		var arrnames = [];
		_loadedgrouped = [];
		_loadedgrouped.length = 0;
		$('.grouped_items input').each(function(){
			var id = $(this).val();
			if( id > 0)
			{
				arrindexes.push(id);
				arrnames.push($(this).attr('data-name'));
			}
				
		})
		
		for(var ir=0; ir < _data.length; ir++)
		{
			if(_data[ir] === undefined) continue;
			var selitem = _data[ir];
			var exists = false;
			if(selitem.post_type === 'product_variation') continue;
			
			selitem._product_adminlink = "post.php?post=" + selitem.ID + "&action=edit";
			if(selitem.product_type === 'grouped')
			{
				for(var j=0;j<_loadedgrouped.length;j++)
				{
					if(_loadedgrouped[j] == selitem.ID)
					{
						exists = true;
						break;
					}
				}
				if(!exists)
				{
					_loadedgrouped.push(selitem.ID);
				}
			}
			if(selitem.product_type !== 'simple') continue;
			exists = false;
			for(var i=0; i<arrindexes.length;i++)
			{
				var id = arrindexes[i];
				if(id === selitem.post_parent)
				{
					if(arrnames[i] !== undefined)
					{
						selitem.grouped_items = arrnames[i];
						selitem.grouped_items_ids = id;
						exists = true;
						break;
					}
				}
			}
			if(!exists)
			{
				selitem.grouped_items = "Choose a grouped product...";
				selitem.grouped_items_ids = "0";
			}
			
		}
	}
	
	function SaveChanges(control,load,gotopage)
	{
		load = typeof load !== 'undefined' ? load : false;
		gotopage = typeof gotopage !== 'undefined' ? gotopage : 0;
		var ajaxarr = {};
		ajaxarr.action = 'wpmelon_adv_bulk_edit';
		ajaxarr.type = 'saveproducts';
		ajaxarr.nonce = W3ExABE.nonce;
		var selectedRows = _grid.getSelectedRows();
		var _arrData = [];
		var _arr = {};
		var _arrParents = [];
		var hassale = false;
		for(var ir=0; ir < _arrEdited.length; ir++)
		{
			var row = _arrEdited[ir];
			if(row === undefined) continue;
			var bcon = false;
			for (var key in row) 
			{
			  if (row.hasOwnProperty(key)) 
			  {
			     bcon  = true;
				 break;
			  }
			}
			if(!bcon) continue;
			if(_data[ir] === undefined) continue;
			var selitem = _data[ir];
			for (var key in row) 
			{
			  if (row.hasOwnProperty(key)) 
			  {
				  var valtoinsert;
				  valtoinsert = selitem[key];
				  
				  if(key === "_downloadable_files")
				  {
				  	  valtoinsert = selitem._downloadable_files_val;
				  }
				  var coldef = _idmap[_mapfield[key]];
				  if(coldef !== undefined && coldef.type === "customtaxh")
				  {
				  	  valtoinsert = selitem[key + '_ids'];
					  if(valtoinsert === undefined)
					  	valtoinsert = "";
					 
				  }
				  if(key.indexOf('_ids') !== -1)
				  {
				  	  var test = key.replace('_ids','');
					  if(_mapfield[test] !== undefined)
					  	continue;
				  }
				  if(_arr[key] === undefined)
				   	  _arr[key] = [];
					  
				  _arr[key].push(selitem.ID + '$#' + selitem.post_parent + '$#' + valtoinsert);
				  
				  if(key === "_regular_price")
				  {
				  	  	if(selitem.post_type == 'product_variation')
						{
							var dontadd  = false;
							for(var cc=0;cc < _arrParents.length;cc++)
							{
								if(_arrParents[cc] == selitem.post_parent)
								{
									dontadd = true;
									break;
								}
							}
							if(!dontadd)
							{
								_arrParents.push(selitem.post_parent);
							}
						}
				  }
				  if(key === "_sale_price")
				  {
				  	  if(selitem.post_type == 'product_variation')
					  {
							var dontadd  = false;
							for(var cc=0;cc < _arrParents.length;cc++)
							{
								if(_arrParents[cc] == selitem.post_parent)
								{
									dontadd = true;
									break;
								}
							}
							if(!dontadd)
							{
								_arrParents.push(selitem.post_parent);
							}
  					  }
				  }
			  }
			}
		}

		var bcon = false;
		for (var key in _arr) 
		{
		  if (_arr.hasOwnProperty(key)) 
		  {
		      _arr[key] = _arr[key].join('#$');
			  bcon = true;
		  }
		}
		if(!bcon)
		{
			if(_hascreation)
			{
				if(_addprodtype == "1")
				{
					CreateVariations();
				}else
				{
					CreateProducts();
				}
			}
			return;
		}
		
		var arrColumns = {};
		var newcols = _grid.getColumns();
		var newlen = newcols.length;
		while (newlen--) {
		    var newobj = newcols[newlen];
			arrColumns[newobj.field] = newobj.width;
		}
		
		$('#myGrid').prepend('<div id="dimgrid" style="position: absolute;top:0;left:0;width: 100%;height:100%;z-index:102;opacity:0.4;filter: alpha(opacity = 40);background-color:grey;"></div>');
		DisableAllControls(true);
		if(control === "savechanges")
		{
			$('#getproducts').parent().append('<div class="showajax"></div>');
			$('.showajax').css('left','270px');
		}else
		{
			$('#pagingholder').append('<div class="showajax"></div>');
			$('.showajax').css({
				left:'170px',
				top:'30px'
			});
		}
		var objChildren = {};
		objChildren.children = [];
		for(var cc=0;cc < _arrParents.length;cc++)
		{
			var id = _arrParents[cc];
			for(var ir=0; ir < _data.length; ir++)
			{
				if(_data[ir] === undefined) continue;
				var selitem = _data[ir];
				if(selitem.post_parent == id)
				{
					var child = "";
					
//					child.parentid = id;
//					child.ID = selitem.ID;
					var _sale_price = "";
					var _regular_price = "";
					if(selitem._regular_price !== undefined)
						_regular_price = String(selitem._regular_price);
					if(selitem._sale_price !== undefined)
						_sale_price = String(selitem._sale_price);
					child = selitem.ID + '#' + id + '#' + _regular_price + '#' + _sale_price;
					objChildren.children.push(child);
				}
			}
		}
		if (objChildren.children.length > 0) 
		{
		  	objChildren.children = objChildren.children.join('#$');
		}else
		{
			objChildren.children = "";
		}
		ajaxarr.data = _arr;
//		ajaxarr.children = objChildren;
		ajaxarr.columns = arrColumns;
		jQuery.ajax({
		     type : "post",
		     dataType : "json",
		     url : W3ExABE.ajaxurl,
		     data : ajaxarr,
		     success: function(response) {
			 		$('#dimgrid').remove();
					DisableAllControls(false);
					$('.showajax').remove();
					while(_arrEdited.length > 0)
					{
					    _arrEdited.pop();
					}
					_changed = {};
					try{
							_grid.removeCellCssStyles("changed");
							_grid.setCellCssStyles("changed", _changed);
						} catch (err)
						{
							;
						}
					//update slug
					var newdata = response.products;
					if(newdata !== undefined && !load  && newdata instanceof Array)
					{
						if(newdata.length > 0)
						{
							var idmaps = [];
							for(var i=0; i < _data.length; i++)
							{
								if(_data[i] === undefined) continue;
								var selitem = _data[i];
								idmaps[selitem.ID] = i;
							}
							for(var j=0; j < newdata.length; j++)
							{
								if(newdata[j] === undefined) continue;
								var selitem = newdata[j];
								if(selitem.post_name === undefined)
								{//update attributes
									if(idmaps[selitem.ID] !== undefined)
									{
										if(_data[idmaps[selitem.ID]] !== undefined)
										{
											var initem = _data[idmaps[selitem.ID]];
											for (var key in selitem) 
											{
											  if (selitem.hasOwnProperty(key)) 
											  {
												  if(key == 'ID' || key == 'post_parent')
												  	continue;
												  if(key.indexOf('_visiblefp') !== -1)
													{
														if(initem[key] !== undefined)
														   initem[key]|= selitem[key];
														else
														   initem[key] = selitem[key];
													}else
													  initem[key] = selitem[key];
											  }
											}
										}
									}
									continue;
								}
								if(idmaps[selitem.ID] !== undefined)
								{
									if(_data[idmaps[selitem.ID]] !== undefined)
									{
										var initem = _data[idmaps[selitem.ID]];
										initem.post_name = selitem.post_name;
										initem._product_permalink = selitem._product_permalink;
									}
								}
							}
							while(idmaps.length > 0) 
							{
							    idmaps.pop();
							}
						}
					}
					_shouldhandle = false;
					_grid.resetActiveCell();
					_grid.invalidate();
					_shouldhandle = true;
					if(_hascreation)
					{
						if(_addprodtype == "1")
						{
							CreateVariations();
						}else
						{
							CreateProducts();
						}
					}
					if(load)
					{
						LoadProducts("pagination",0,gotopage);
					}
		     },
			 complete:function (args)
			 {
			  	//uncomment to debug
//			    $('#debuginfo').html(args.responseText);
			 }, error:function (xhr, status, error) 
			  {
			  	//uncomment to debug
				  $('#dimgrid').remove();
				  $('.showajax').remove();
				  DisableAllControls(false);
				  $('#debuginfo').html(xhr.responseText);
			  }
		  }) ;
	}
	
	$('#butprevious').click(function ()
	{
		var gotopage = parseInt(_currentoffset);
		gotopage--;
		if(isNaN(gotopage) || gotopage <= 0) return;
		_currentoffset = gotopage;
		if(_currentoffset == 1)
		{
			$('#gotopagenumber').val('1');
			HandlePaginationData(false,false);
		}
		else
			HandlePaginationData(true,false);		
	});

	$('#gotopage').click(function ()
	{//go to first
//		var gotopage = $('#gotopagenumber').val();
//		gotppage = parseInt(gotopage);
//		if(isNaN(gotopage) || gotopage < 1 || _totalrecords <= _recordslimit) return;
		_currentoffset = 1;
		$('#gotopagenumber').val('1');
		HandlePaginationData(false,false);
	});

	$('#butnext').click(function ()
	{
		var gotopage = parseInt(_currentoffset);
		gotopage++;
		if(isNaN(gotopage) || gotopage <= 1 || !_hasnext) return;
		_currentoffset = gotopage;
		HandlePaginationData(true,true);
	});
	
	$("#settingsdialog").dialog({			
	    autoOpen: false,
	    height: 670,
	    width: 820,
	    modal: true,
		draggable:true,
		resizable:true,
		closeOnEscape: true,
		title:"Column Settings",
		create: function (event, ui) {
	        $(this).dialog('widget')
	            .css({ position: 'fixed'})
	    },
		open: function( event, ui ) {
			 var d = $('.ui-dialog:visible');
			 $(d).css('z-index',300002);
			 $('.ui-dialog:visible').wrap('<div class="w3exabe w3exabedel" />');
			  $('.ui-widget-overlay').each(function () {
				 $(this).next('.ui-dialog').andSelf().wrapAll('<div class="w3exabe w3exabedel" />');
				});
			  $('#settingsdialog').css('height','560px');
		},
		close: function( event, ui ) {
			$(".w3exabedel").contents().unwrap();
		},
	 	buttons: {
		  "OK": function() {
  			   try{
	 				var newcols = _grid.getColumns();
					var changed = false;
					var offset = 0;
					var _arrData = {};
					$('.dsettings').each(function()
					{
						
						var id= $(this).attr('data-id');
						$("#bulkdialog tr[data-id='" + id + "']").hide();
						$("#selectdialog tr[data-id='" + id + "']").hide();
						if(!$(this).is(':checked'))
						{
							offset++;
							var len = newcols.length;
							while (len--) {
							    var obj = newcols[len];
								if(obj.field === id)
								{
									newcols.splice(len,1);
									changed = true;
									break;
								}
							}
						}
						else
						{//add
							$("#bulkdialog tr[data-id='" + id + "']").show();
							$("#selectdialog tr[data-id='" + id + "']").show();
							var offset = _allcols.length - newcols.length;
							var hascol = false;
							var len = newcols.length;
							while (len--) {
							    var obj = newcols[len];
								if(obj.field === id)
								{
									hascol = true;
									break;
								}
							}
							if(!hascol)
							{
								len = _allcols.length;
								var shouldsearch = false;
								var found = false;
								var insertobj;
								while (len--) {
								    var obj = _allcols[len];
									if(obj.field === id)
									{
										insertobj = _allcols[len];
		//								var newobj = $.extend(true,{},obj)
										shouldsearch = true;
										continue;
									}
									if(shouldsearch)
									{
										var newlen = newcols.length;
										while (newlen--) {
										    var newobj = newcols[newlen];
											if(newobj.field === obj.field )
											{
												newcols.splice(newlen+1,0,insertobj);
												changed = true;
												found = true;
												break;
											}
										}
									}
									if(found) break;
										
								}
								if(!found)
									newcols.push(insertobj);
							}
							
						}
					});
					
					if(changed)
					{
						_grid.setColumns(newcols);
						var newlen = newcols.length;
						while (newlen--) 
						{
							var arritem = {};
						    var newobj = newcols[newlen];
							arritem.field = newobj.field;
							arritem.width = newobj.width;
							_arrData[arritem.field] = arritem.width ;
						}
					}
						
					if(!changed)
					{
						$( this ).dialog( "close" );
						return;
					}	
				}catch(err)
				{
					$( this ).dialog( "close" );
						return;
				}
				var ajaxarr = {};
				ajaxarr.action = 'wpmelon_adv_bulk_edit';
				ajaxarr.type = 'savecolumns';
				ajaxarr.nonce = W3ExABE.nonce;
						
				var $elem = $('.ui-dialog-buttonset > .ui-button:visible').first();
		        $elem.css('position','relative').append('<div class="showajax"></div>');
				$('.showajax').css({
					left:'15px'
				});
				$elem.button("disable");
				ajaxarr.data = _arrData;
				var dlg = $(this);
				jQuery.ajax({
				     type : "post",
				     dataType : "json",
				     url : W3ExABE.ajaxurl,
				     data : ajaxarr,
				     success: function(response) {
							$('.showajax').remove();
							$elem.button("enable");
							dlg.dialog( "close" );
							_grid.setSelectedRows(_grid.getSelectedRows());
				     },
					  error:function (xhr, status, error) 
					  {
//					  	 $('#debuginfo').html(error);
					  	  $('.showajax').remove();
						  $elem.button("enable");
						  dlg.dialog( "close" );
					  }
				  }) ;
//				$( this ).dialog( "close" );
		  },
		  Cancel: function()
		  {
			  $( this ).dialog( "close" );
		  }
		  }
		});

	$('#settings').click(function()
	{
		$("#settingsdialog").dialog("open");	
	});
	$('#pluginsettingsbut').click(function()
	{
		$("#pluginsettings").dialog("open");	
	});
	
	$('#customfieldsbut').click(function()
	{
		$("#customfieldsdialog").dialog("open");	
	});
	
	$('#findcustomfieldsbut').click(function()
	{
		$("#findcustomfieldsdialog").dialog("open");	
	});
	
	
	$('body').on('click','#settingsdialog .dsettings',function()
	{
		var checkdiv = '<img src ="' + W3Ex.imagepath + 'images/tick.png' + '" />';
		var id= $(this).attr('id');
		if($(this).is(':checked'))
		{
			$('#' + id + '_check').css('visibility','visible');
			$('#' + id + ' + label').css('font-weight','bold');
		}
		else
		{
			$('#' + id + '_check').css('visibility','hidden');
			$('#' + id + ' + label').css('font-weight','normal');
		}
	});

	$("#selectdialog").dialog({			
    autoOpen: false,
    height: 620,
    width: 880,
    modal: true,
	draggable:true,
	resizable:true,
	closeOnEscape: true,
	title:"Selection Manager",
	create: function (event, ui) {
        $(this).dialog('widget')
            .css({ position: 'fixed'})
    },
	open: function( event, ui ) {
		 var d = $('.ui-dialog:visible');
		 $(d).css('z-index',300002);
		 $('.ui-dialog:visible').wrap('<div class="w3exabe w3exabedel" />');
		  $('#selectdialog').css('height','500px');
		  $('.ui-widget-overlay').each(function () {
			 $(this).next('.ui-dialog').andSelf().wrapAll('<div class="w3exabe w3exabedel" />');
			
	});
		$('#selectdialog .selectset').each(function(){
			var item = $(this);
			if(!item.prop('checked'))
			{
				$('#selectdialog #select' + item.attr('data-id')).prop("disabled",true);
			}else
			{
				$('#selectdialog #select' + item.attr('data-id')).prop("disabled",false);
			}
		})
	},
	close: function( event, ui ) {
		$(".w3exabedel").contents().unwrap();
	},
 	buttons: {
	  "OK": function() {
	  	var params = {};
		$('#selectdialog .selectvalue:visible').each(function(){
			var item = $(this);
//			if(!item.is(':visible')) continue;
			var value = item.val();
			var id = item.attr('data-id');
			if(value != "")
			{
				params[id] = $('#select'+ id).val();
				params[id + 'value'] = value;
//				if(params[id] === "empty")
//					params[id + 'value'] = 0;
//				if(id === "_sale_price")
//				{
//					if(params[id] == 'decvaluereg' || params[id] == 'decpercentreg')
//					{
//						params.isskipsale = $('#saleskip').prop('checked');
//					}
//				}
			}
		})
		$('#selectdialog select option[value="empty"]:selected').each(function(){
			var item = $(this).parent();
			if(item.is(':visible'))
			{
				var id = item.attr('data-id');
				params[id] = 'empty';
				params[id + 'value'] = 0;
			}
		})
		
		$('#selectdialog .selectset:checked').each(function(){
			var item = $(this);
			if(item.is(':visible'))
			{
				var id = item.attr('data-id');
				params[id] = id;
				params[id+ 'value'] = $('#selectdialog select#select' + id).val();
			}
		})
		
		HandleSelectUpdate(params);
	     $( this ).dialog( "close" );
	  },
	  Cancel: function()
	  {
		  $( this ).dialog( "close" );
	  }
	 }
});


	function HandlePaginationData(pagination,isnext)
	{
		var hastosave = false;
		for(var ir=0; ir < _arrEdited.length; ir++)
		{
			var row = _arrEdited[ir];
			if(row === undefined) continue;
			if(_data[ir] === undefined) continue;
			hastosave = true;
			break;
		}
		if(hastosave)
		{
			SaveChanges("pagination","load",gotopage);
		}else
		{
			LoadProducts("pagination",pagination,isnext);
		}
	}


	$('#findcustomfield').click(function ()
	{
		var ctext = $('#productid').val();
		ctext = $.trim(ctext);
		if(ctext == "") return;
		var ajaxarr = {};
		ajaxarr.action = 'wpmelon_adv_bulk_edit';
		ajaxarr.type = 'findcustomfields';
		ajaxarr.nonce = W3ExABE.nonce;
				
		var $elem = $('#findcustomfield');
        $elem.css('position','relative').append('<div class="showajax"></div>');
		$('.showajax').css({
			left:'15px'
		});
//		$elem.button("disable");
		ajaxarr.data = ctext;
		var dlg = $(this);
		jQuery.ajax({
		     type : "post",
		     dataType : "json",
		     url : W3ExABE.ajaxurl,
		     data : ajaxarr,
		     success: function(response) {
					$('.showajax').remove();
					$('#findcustomfieldsdialog table tr').remove();
					var metas = response.customfields;
					if(metas === undefined || metas === null)
					{
						$('#findcustomfieldsdialog table').append('<tr><td>Nothing found</td></tr>');
						return;
					}
					if(metas === -1)
					{
						$('#findcustomfieldsdialog table').append('<tr><td>Product does not exist</td></tr>');
						return;
					}
					var texttoadd = "<tr><td></td><td>Meta Key</td><td>Meta Value</td><td></td></tr>";
					for (var i=0; i< metas.length; i++) {
					    var meta = metas[i];
						meta.meta_value = meta.meta_value.toString();
						meta.meta_value = meta.meta_value.substr(0, 100);
						texttoadd+= '<tr><td data-field=""><input class="customisvisible" type="checkbox"></td><td data-field="metakey" meta-field="'+meta.meta_key+'">'+ meta.meta_key+'</td><td data-field="">'+meta.meta_value+'</td><td data-field="type">Field type:&nbsp;<select class="fieldtypefound"><option value="text">Text (single line)</option><option value="multitext">Text (multi line)</option><option value="integer">Number (integer)</option><option value="decimal">Number (decimal .00)</option><option value="decimal3">Number (decimal .000)</option><option value="checkbox">Checkbox</option></select></td></tr>';
					}
					if(texttoadd !== "<tr><td></td><td>Meta Key</td><td>Meta Value</td><td></td></tr>")
						$('#findcustomfieldsdialog table').append(texttoadd);
					else
						$('#findcustomfieldsdialog table').append('<tr><td>Nothing found</td></tr>');
			},
			  error:function (xhr, status, error) 
			  {
			  	  $('.showajax').remove();
				  $('#findcustomfieldsdialog table tr').remove();
				  $('#findcustomfieldsdialog table').append('<tr><td>Product does not exist</td></tr>');
			  }
		  }) ;
	});

	$('#findcustomtaxonomies').click(function ()
	{
	
		var ajaxarr = {};
		ajaxarr.action = 'wpmelon_adv_bulk_edit';
		ajaxarr.type = 'findcustomtaxonomies';
		ajaxarr.nonce = W3ExABE.nonce;
				
		var $elem = $('#findcustomtaxonomies');
        $elem.css('position','relative').append('<div class="showajax"></div>');
		$('.showajax').css({
			left:'15px'
		});
		ajaxarr.data = "";
		var dlg = $(this);
		jQuery.ajax({
		     type : "post",
		     dataType : "json",
		     url : W3ExABE.ajaxurl,
		     data : ajaxarr,
		     success: function(response) {
					$('.showajax').remove();
					$('#findcustomfieldsdialog table tr').remove();
					var metas = response.customfields;
					if(metas === undefined || metas === null)
					{
						$('#findcustomfieldsdialog table').append('<tr><td>Nothing found</td></tr>');
						return;
					}
					if(metas === -1)
					{
						$('#findcustomfieldsdialog table').append('<tr><td>Nothing found</td></tr>');
						return;
					}
					var texttoadd = "<tr><td></td><td>Taxonomy name</td><td>Taxonomy Terms</td></tr>";
					for (var i=0; i< metas.length; i++) {
					    var meta = metas[i];
						meta.tax = meta.tax.toString();
						meta.terms = meta.terms.substr(0, 100);
						texttoadd+= '<tr><td data-field=""><input class="customisvisible" type="checkbox"></td><td data-field="metakey" meta-field="'+meta.tax+'">'+ meta.tax+'</td><td data-field="typecustom">'+meta.terms+'</td></tr>';
					}
					if(texttoadd !== "<tr><td></td><td>Taxonomy name</td><td>Taxonomy Terms</td></tr>")
						$('#findcustomfieldsdialog table').append(texttoadd);
					else
						$('#findcustomfieldsdialog table').append('<tr><td>Nothing found</td></tr>');
			},
			  error:function (xhr, status, error) 
			  {
			  	  $('.showajax').remove();
				  $('#findcustomfieldsdialog table tr').remove();
				  $('#findcustomfieldsdialog table').append('<tr><td>Product does not exist</td></tr>');
			  }
		  }) ;
	});
	
	 $("#addok").click(function ()
	 {
	 	var newhtml = "<tr class='trcustom'><td data-field='name'><strong>";
	 	var ctext = $('#fieldname').val();
		ctext = $.trim(ctext);
		if(ctext == "") return;
		if(_mapfield[ctext] !== undefined)
		{
			if(_idmap[_mapfield[ctext]] !== undefined)
			{
				if(_idmap[_mapfield[ctext]].isdeleted === undefined)
				{
					alert('Field with the same id already exists !');
					return;
				}
			}
		}
		var bexit = false;
		$('.trcustom:visible').each(function ()
		{
			var $tds = $(this).children('td');
			var oldid = "";
			$tds.each(function ()
			{
				var field = $(this).attr('data-field');
				var fieldinfo = "";
				if(field == 'name')
				{
					oldid  = $(this).text();
					if(oldid == ctext)
					{
						alert('Field with the same id already exists !');
						bexit = true;
					}
				}
			})
		})
		if(bexit) return;
//		alert($('#fieldtype').val());
		if($('#fieldtype').val() == "custom" || $('#fieldtype').val() == "customh")
		{//check for category existance
			var ajaxarr = {};
			ajaxarr.action = 'wpmelon_adv_bulk_edit';
			ajaxarr.type = 'checkcustom';
			ajaxarr.nonce = W3ExABE.nonce;
					
			var $elem = $('#addok');
	        $elem.css('position','relative').append('<div class="showajax"></div>');
			$('.showajax').css({
				left:'15px'
			});
			$elem.button("disable");
			ajaxarr.extrafield = ctext;
			jQuery.ajax({
			     type : "post",
			     dataType : "json",
			     url : W3ExABE.ajaxurl,
			     data : ajaxarr,
			     success: function(response) {
				 		if(response.error !== undefined)
				 		 	$('#extracustominfo').html('<div style="color:red;">Taxonomy does not exist !</div>');
						else
						{
							if(_mapfield[ctext] !== undefined)
							{
								if(_idmap[_mapfield[ctext]] !== undefined)
								{
									if(_idmap[_mapfield[ctext]].isdeleted === undefined)
									{
										alert('Field with the same id already exists !');
										return;
									}
								}
							}
							var bexit = false;
							$('.trcustom:visible').each(function ()
							{
								var $tds = $(this).children('td');
								var oldid = "";
								$tds.each(function ()
								{
									var field = $(this).attr('data-field');
									var fieldinfo = "";
									if(field == 'name')
									{
										oldid  = $(this).text();
										if(oldid == ctext)
										{
											alert('Field with the same id already exists !');
											bexit = true;
										}
									}
								})
							})
							if(bexit) return;
							newhtml+= ctext + "</strong></td><td";
							ctext = $('#fieldtype').val();
							switch(ctext){
								case "text":
								{
									newhtml+= " data-type='text' data-field='type'>type: <strong>Text (single line)</strong></td>";
								}
								break;
								case "multitext":
								{
									newhtml+= " data-type='multitext' data-field='type'>type: <strong>Text (multi line)</strong></td>";
								}
								break;
								case "integer":
								{
									newhtml+= " data-type='integer' data-field='type'>type: <strong>Number (integer)</strong></td>";
								}
								break;
								case "decimal":
								{
									newhtml+= " data-type='decimal' data-field='type'>type: <strong>Number (decimal .00)</strong></td>";
								}
								break;
								case "decimal3":
								{
									newhtml+= " data-type='decimal3' data-field='type'>type: <strong>Number (decimal .000)</strong></td>";
								}
								break;
								case "select":
								{
									var selvals = $('#extracustominfo input').val();
									if(selvals == "")
										return;
									newhtml+= " data-type='select' data-field='type' data-vals='" + selvals + "'>type: <strong>select</strong><br/>(" + selvals + ")</td>";
								}
								break;
								case "checkbox":
								{
									newhtml+= " data-type='checkbox' data-field='type'>type: <strong>Checkbox</strong></td>";
								}
								break;
								case "custom":
								{
									newhtml+= " data-type='custom' data-field='type' data-vals='" +  $('#extracustominfo input').is(':checked') + "'>type: <strong>Custom Taxonomy</td>";
								}
								break;
								case "customh":
								{
									newhtml+= " data-type='customh' data-field='type'>type: <strong>Custom Taxonomy(hierar.)</td>";
								}
								break;
								
								default:
									break;
							}
							
							ctext = $('#fieldvisible').val();
							if(ctext == "yes")
							{
								newhtml+= '<td data-field="isvisible"><label><input type="checkbox" class="customisvisible" checked="checked">Visible</label><input class="button deletecustomfield" type="button" value="delete" /></td></tr>';
							}else
							{
								newhtml+= '<td data-field="isvisible"><label><input type="checkbox" class="customisvisible">Visible</label><input class="button deletecustomfield" type="button" value="delete" /></td></tr>';
							}
							$(newhtml).insertBefore('.addcontrols');
						 	$('#addcustomfield').show();
							$('.addcontrols').hide();
							$('.addokcancel').hide();
						}
						$('.showajax').remove();
						$elem.button("enable");
			     },
				  error:function (xhr, status, error) 
				  {
				  	  $('.showajax').remove();
					  $elem.button("enable");
					 
				  }
			  }) ;
		    return;
		}
		
		
		
		newhtml+= ctext + "</strong></td><td";
		ctext = $('#fieldtype').val();
		switch(ctext){
			case "text":
			{
				newhtml+= " data-type='text' data-field='type'>type: <strong>Text (single line)</strong></td>";
			}
			break;
			case "multitext":
			{
				newhtml+= " data-type='multitext' data-field='type'>type: <strong>Text (multi line)</strong></td>";
			}
			break;
			case "integer":
			{
				newhtml+= " data-type='integer' data-field='type'>type: <strong>Number (integer)</strong></td>";
			}
			break;
			case "decimal":
			{
				newhtml+= " data-type='decimal' data-field='type'>type: <strong>Number (decimal .00)</strong></td>";
			}
			break;
			case "decimal3":
			{
				newhtml+= " data-type='decimal3' data-field='type'>type: <strong>Number (decimal .000)</strong></td>";
			}
			break;
			case "select":
			{
				var selvals = $('#extracustominfo input').val();
				if(selvals == "")
					return;
				newhtml+= " data-type='select' data-field='type' data-vals='" + selvals + "'>type: <strong>select</strong><br/>(" + selvals + ")</td>";
			}
			break;
			case "checkbox":
			{
				newhtml+= " data-type='checkbox' data-field='type'>type: <strong>Checkbox</strong></td>";
			}
			break;
			case "custom":
			{
				newhtml+= " data-type='custom' data-field='type' data-vals='" +  $('#extracustominfo input').is(':checked') + "'>type: <strong>Custom Taxonomy</td>";
			}
			break;
			case "customh":
			{
				newhtml+= " data-type='customh' data-field='type'>type: <strong>Custom Taxonomy(hierar.)</td>";
			}
			break;
			
			default:
				break;
		}
		
		ctext = $('#fieldvisible').val();
		if(ctext == "yes")
		{
			newhtml+= '<td data-field="isvisible"><label><input type="checkbox" class="customisvisible" checked="checked">Visible</label><input class="button deletecustomfield" type="button" value="delete" /></td></tr>';
		}else
		{
			newhtml+= '<td data-field="isvisible"><label><input type="checkbox" class="customisvisible">Visible</label><input class="button deletecustomfield" type="button" value="delete" /></td></tr>';
		}
		$(newhtml).insertBefore('.addcontrols');
	 	$('#addcustomfield').show();
		$('.addcontrols').hide();
		$('.addokcancel').hide();
		
		
	 })
	 
	 $("#findcustomfieldsdialog").dialog({			
	    autoOpen: false,
	    height: 640,
	    width: 820,
	    modal: true,
		draggable:true,
		resizable:false,
		closeOnEscape: true,
		title:"Find Custom Fields",
		create: function (event, ui) {
	        $(this).dialog('widget')
	            .css({ position: 'fixed'})
	    },
		open: function( event, ui ) {
			 var d = $('.ui-dialog:visible');
			 $(d).css('z-index',300002);
			/* if($('.ui-widget-overlay:visible').length > 0)
			 {
			  	  $('.ui-widget-overlay').each(function () {
				 $(this).next('.ui-dialog').andSelf().wrapAll('<div class="w3exabe w3exabedel" />');
				});
			  }else*/
			  {
				$('.ui-dialog:visible').wrap('<div class="w3exabe w3exabedel" />');
			  }
			   $('.ui-widget-overlay').each(function () {
				 $(this).next('.ui-dialog').andSelf().wrapAll('<div class="w3exabe w3exabedel" />');
				});
			  $('#findcustomfieldsdialog').css('height','502px');
			  $('#productid').val('');
 			  $('#findcustomfieldsdialog table tr').remove();
			  _changedcustom = [];
		},
		close: function( event, ui ) {
			$(".w3exabedel").contents().unwrap();
		},
	 	buttons: {
		  "Save Selected and Close": function() {
  			   try{
					
			   		var changed = false;
	 				var newcols = _grid.getColumns();
					
					var offset = 0;
					var _arrData = {};
					
					var customobj = {};
						
					$('.trcustom').each(function ()
					{
						var $tdsc = $(this).children('td');
						customobj = {};
						$tdsc.each(function ()
						{
							var field = $(this).attr('data-field');
							var fieldinfo = "";
							if(field == 'name')
							{
								customobj.name = $(this).text();
								
							}else if(field == 'type')
							{
								customobj.type = $(this).attr('data-type');
								if(customobj.type == 'custom')
								{
									if($(this).attr('data-vals') == "true")
										customobj.isnewvals = true;
									else
										customobj.isnewvals = false;
								}else if(customobj.type == 'select')
								{
									customobj.selvals = $(this).attr('data-vals');
								}
							}else if(field == 'isvisible')
							{
								customobj.isvisible = $(this).find('input').is(':checked');
							}
						})
						_arrData[customobj.name] = customobj;
					});
						
					$('#findcustomfieldsdialog table tr:visible').each(function ()
					{
						var $tds = $(this).children('td');
						if($(this).find('input:checkbox').length > 0)
						{
							if(!$(this).find('input:checkbox').is(':checked'))
							{
								return true;
							}
						}else
						{
							return true;
						}
						
						customobj = {};
						
						var existsalready = false;
						
						$tds.each(function ()
						{
							var field = $(this).attr('data-field');
							var fieldinfo = "";
							if(field == 'metakey')
							{
								customobj.name = $(this).attr('meta-field');
								if(_mapfield[customobj.name] !== undefined)
								{
//									if(_idmap[_mapfield[customobj.name]] !== undefined)
									{
//										if(_idmap[_mapfield[customobj.name]].isdeleted === undefined)
										{
											existsalready = true;
										}
									}
								}
								
							}else if(field == 'type')
							{
								customobj.type = $(this).find('.fieldtypefound').val();
							}else if(field == 'typecustom')
							{
								customobj.type = 'customh';
							}
						})
						
						if(existsalready) return true;
						
						customobj.isvisible = true;
						var newhtml = "<tr class='trcustom'><td data-field='name'><strong>";
						newhtml+= customobj.name + "</strong></td><td";
						switch(customobj.type){
							case "text":
							{
								newhtml+= " data-type='text' data-field='type'>type: <strong>Text (single line)</strong></td>";
							}
							break;
							case "multitext":
							{
								newhtml+= " data-type='multitext' data-field='type'>type: <strong>Text (multi line)</strong></td>";
							}
							break;
							case "integer":
							{
								newhtml+= " data-type='integer' data-field='type'>type: <strong>Number (integer)</strong></td>";
							}
							break;
							case "decimal":
							{
								newhtml+= " data-type='decimal' data-field='type'>type: <strong>Number (decimal .00)</strong></td>";
							}
							break;
							case "decimal3":
							{
								newhtml+= " data-type='decimal3' data-field='type'>type: <strong>Number (decimal .000)</strong></td>";
							}
							break;
							case "checkbox":
							{
								newhtml+= " data-type='checkbox' data-field='type'>type: <strong>Checkbox</strong></td>";
							}
							break;
							case "custom":
							{
								newhtml+= " data-type='custom' data-field='type' data-vals='false'>type: <strong>Custom Taxonomy</td>";
							}
							break;
							case "customh":
							{
								newhtml+= " data-type='customh' data-field='type'>type: <strong>Custom Taxonomy(hierar.)</td>";
							}
							break;
							default:
								break;
						}
						
							newhtml+= '<td data-field="isvisible"><label><input type="checkbox" class="customisvisible" checked="checked">Visible</label><input class="button deletecustomfield" type="button" value="delete" /></td></tr>';
							
						$(newhtml).insertBefore('.addcontrols');
		
						_arrData[customobj.name] = customobj;
						if(_mapfield[customobj.name] === undefined)
						{
							var insertobj = {};
							insertobj[customobj.name] = _mapfield.length;
							
							_mapfield[customobj.name] = _idmap.length;
							insertobj.field = customobj.name;
							insertobj.id = insertobj.field;
							insertobj.name = insertobj.field;
							
							var newitem = {};
							newitem.id = customobj.name;
							newitem.name = customobj.name;
							newitem.field = customobj.name;
							
							if(customobj.type == "text")
							{
								newitem.editor = Slick.Editors.Text;
							}else if(customobj.type == "multitext")
							{
								newitem.editor = Slick.Editors.TextArea;
								insertobj.textarea = true;
							}else if(customobj.type == "integer")
							{
								newitem.editor = Slick.Editors.Text;
								insertobj.type = 'int';
							}else if(customobj.type == "decimal")
							{
								newitem.editor = Slick.Editors.Text;
								insertobj.type = 'float2';
							}else if(customobj.type == "decimal3")
							{
								newitem.editor = Slick.Editors.Text;
								insertobj.type = 'float3';
							}else if(customobj.type == "checkbox")
							{
								newitem.cssClass = "cell-effort-driven";
								newitem.formatter = Slick.Formatters.Checkmark;
								newitem.editor = Slick.Editors.Checkbox;
								insertobj.checkbox = true;
								insertobj.type = 'set';
							}else if(customobj.type == "select")
							{
								newitem.editor = Slick.Editors.Select;
								newitem.options = customobj.selvals;
								insertobj.type = 'set';
								insertobj.options= customobj.selvals;
							}else if(customobj.type == "custom")
							{
								newitem.editor = Slick.Editors.Text;
								insertobj.scope = SCOPE.PRODALL;
								insertobj.type = 'customtax';
								insertobj.isnewvals = customobj.isnewvals;
							}else if(customobj.type == "customh")
							{
								newitem.editor = Slick.Editors.Category;
								insertobj.scope = SCOPE.PRODALL;
								insertobj.type = 'customtaxh';
							}
							AddBulkAndSelectFields(customobj);
							newitem.sortable = true;
							_allcols.push(newitem);
							_idmap.push(insertobj);
							changed = true;
							if(customobj.isvisible)
							{
								var offset = _allcols.length - newcols.length;
								var hascol = false;
								var len = newcols.length;
								while (len--) {
								    var obj = newcols[len];
									if(obj.field === customobj.name)
									{
										hascol = true;
										break;
									}
								}
								if(!hascol)
								{
									len = _allcols.length;
									var shouldsearch = false;
									var found = false;
									var insertobj;
									while (len--) {
									    var obj = _allcols[len];
										if(obj.field === customobj.name)
										{
											insertobj = _allcols[len];
											shouldsearch = true;
											continue;
										}
										if(shouldsearch)
										{
											var newlen = newcols.length;
											while (newlen--) {
											    var newobj = newcols[newlen];
												if(newobj.field === obj.field )
												{
													newcols.splice(newlen+1,0,insertobj);
													changed = true;
													found = true;
													break;
												}
											}
										}
										if(found) break;
											
									}
									if(!found)
										newcols.push(insertobj);
								}
							}
							

						}else
						{//field exits
							
							try{
									if(!customobj.isvisible)
									{
										offset++;
										var len = newcols.length;
										while (len--) {
										    var obj = newcols[len];
											if(obj.field === customobj.name)
											{
												newcols.splice(len,1);
												changed = true;
												break;
											}
										}
										$("#bulkdialog tr[data-id='" + customobj.name + "']").hide();
										$("#selectdialog tr[data-id='" + customobj.name + "']").hide();
									}else
									{
										$("#bulkdialog tr[data-id='" + customobj.name + "']").show();
										$("#selectdialog tr[data-id='" + customobj.name + "']").show();
										var offset = _allcols.length - newcols.length;
										var hascol = false;
										var len = newcols.length;
										while (len--) {
										    var obj = newcols[len];
											if(obj.field === customobj.name)
											{
												hascol = true;
												break;
											}
										}
										if(!hascol)
										{
											len = _allcols.length;
											var shouldsearch = false;
											var found = false;
											var insertobj;
											while (len--) {
											    var obj = _allcols[len];
												if(obj.field === customobj.name)
												{
													insertobj = _allcols[len];
													shouldsearch = true;
													continue;
												}
												if(shouldsearch)
												{
													var newlen = newcols.length;
													while (newlen--) {
													    var newobj = newcols[newlen];
														if(newobj.field === obj.field )
														{
															newcols.splice(newlen+1,0,insertobj);
															changed = true;
															found = true;
															break;
														}
													}
												}
												if(found) break;
													
											}
											if(!found)
												newcols.push(insertobj);
										}
									}
								}catch(err)
								{
									;
								}
								
							
						}
					
						
						
					})
						if(changed)
						{
							_grid.setColumns(newcols);
							var newlen = newcols.length;
							while (newlen--) 
							{
								var arritem = {};
							    var newobj = newcols[newlen];
								arritem.field = newobj.field;
								arritem.width = newobj.width;
//								_arrData[arritem.field] = arritem.width ;
							}
						}
						
					if(!changed)
					{
						$( this ).dialog( "close" );
						return;
					}	
				}catch(err)
				{
					_grid.setColumns(newcols);
					$( this ).dialog( "close" );
					return;
				}
				var arrColumns = {};
				var newcols = _grid.getColumns();
				var newlen = newcols.length;
				while (newlen--) {
				    var newobj = newcols[newlen];
					arrColumns[newobj.field] = newobj.width;
				}
				var ajaxarr = {};
				ajaxarr.action = 'wpmelon_adv_bulk_edit';
				ajaxarr.type = 'savecustom';
				ajaxarr.nonce = W3ExABE.nonce;
						
				var $elem = $('.ui-dialog-buttonset > .ui-button:visible').first();
		        $elem.css('position','relative').append('<div class="showajax"></div>');
				$('.showajax').css({
					left:'15px'
				});
				$elem.button("disable");
				ajaxarr.data = _arrData;
				ajaxarr.columns = arrColumns;
				var dlg = $(this);
				jQuery.ajax({
				     type : "post",
				     dataType : "json",
				     url : W3ExABE.ajaxurl,
				     data : ajaxarr,
				     success: function(response) {
							$('.showajax').remove();
							$elem.button("enable");
							for (var key in _arrData) 
							{
							  if (_arrData.hasOwnProperty(key)) 
							  {
								var obj = _arrData[key];
								if(obj.type !== undefined && (obj.type=== 'customh' || obj.type=== 'custom'))
								{
									if(response[obj.name] !== undefined)
									{
										if(obj.type=== 'customh')
										{
											var bulkdata = '<td><input id="set'+obj.name+'" type="checkbox" class="bulkset" data-id="'+obj.name+'" data-type="customtaxh"><label for="set'+obj.name+'">Set '+obj.name+'</label></td><td></td><td><select id="bulk' + obj.name + '"'+response[obj.name]+'</td><td></td>';
											$("#bulkdialog tr[data-id='" + obj.name + "']").html(bulkdata);
											if(response[obj.name + 'edit'] !== undefined)
											{
												$("#categoriesdialog").append(response[obj.name + 'edit']);
											}
										}
										W3Ex['taxonomyterms' + obj.name] = response[obj.name];
									}
									
								}
							   }
							}
							if(response['customfieldsdata'] !== undefined)
							{
								W3Ex.customfields = response['customfieldsdata'];
							}
							ShowCustomSearchFilters();
							$('.makechosen').chosen({disable_search_threshold: 10});
							dlg.dialog( "close" );
							
				     },
					  error:function (xhr, status, error) 
					  {
//					  	 $('#debuginfo').html(error);
					  	  $('.showajax').remove();
						  $elem.button("enable");
						  dlg.dialog( "close" );
					  }
				  }) ;
//				$( this ).dialog( "close" );
		  },
		  Cancel: function()
		  {
		  	 
			  $( this ).dialog( "close" );
		  }
		  }
		});
		
	 
	$("#customfieldsdialog").dialog({			
	    autoOpen: false,
	    height:640,
	    width: 820,
	    modal: true,
		draggable:true,
		resizable:false,
		closeOnEscape: true,
		title:"Custom Fields",
		create: function (event, ui) {
	        $(this).dialog('widget')
	            .css({ position: 'fixed'})
	    },
		open: function( event, ui ) {
			 var d = $('.ui-dialog:visible');
			 $(d).css('z-index',300002);
			/* if($('.ui-widget-overlay:visible').length > 0)
			 {
			  	  $('.ui-widget-overlay').each(function () {
				 $(this).next('.ui-dialog').andSelf().wrapAll('<div class="w3exabe w3exabedel" />');
				});
			  }else*/
			  {
				$('.ui-dialog:visible').wrap('<div class="w3exabe w3exabedel" />');
			  }
			   $('.ui-widget-overlay').each(function () {
				 $(this).next('.ui-dialog').andSelf().wrapAll('<div class="w3exabe w3exabedel" />');
				});
			  $('#customfieldsdialog').css('height','502px');
			  _changedcustom = [];
		},
		close: function( event, ui ) {
			$('.trcustom').each(function ()
			{
				var $td = $(this).children('td:first');
				var field = $td.text();
				if(_mapfield[field] === undefined)
				$(this).remove();
			})
			
			 $('.trcustom:hidden').each(function ()
				{
					$(this).show();
					var $td = $(this).children('td:first');
					var field = $td.text();
					if(_mapfield[field] !== undefined)
					{
						if(_idmap[_mapfield[field]] !== undefined)
						{
							if(_idmap[_mapfield[field]].isdeleted !== undefined)
								delete _idmap[_mapfield[field]].isdeleted;
						}
					}
//					var $tds = $(this).children('td');
//					$tds.each(function ()
//					{
//						var field = $(this).attr('data-field');
//						if(field == 'name')
//						{
//							if($(this).text() === delfield)
//							   $tr.show();
//						}
//					})
				})
			$('#addcustomfield').show();
			$('.addcontrols').hide();
			$('.addokcancel').hide();
			$(".w3exabedel").contents().unwrap();
		},
	 	buttons: {
		  "OK": function() {
  			   try{
			   		var changed = false;
	 				var newcols = _grid.getColumns();
					
					for(var i=0 ; i < _changedcustom.length; i++)
					{
						var delfield = _changedcustom[i];
						if(_mapfield[delfield] !== undefined)
						{
							delete _mapfield[delfield];
							var newlen = _allcols.length;
							while (newlen--) {
							    var newobj = _allcols[newlen];
								if(newobj.field === delfield )
								{
									_allcols.splice(newlen,1);
									changed = true;
								}
							}
						}
						var len = newcols.length;
						while (len--) {
						    var obj = newcols[len];
							if(obj.field === delfield)
							{
								newcols.splice(len,1);
								$("#bulkdialog tr[data-id='" + delfield + "']").remove();
								$("#selectdialog tr[data-id='" + delfield + "']").remove();
								$("#categoriesdialog ." + delfield).remove();
								break;
							}
						}
						for(var ir=0; ir < _arrEdited.length; ir++)
						{
							var row = _arrEdited[ir];
							if(row === undefined) continue;
							if(row[delfield] === undefined) continue;
							delete row[delfield];
						}
						for(var id=0; id < _data.length; id++)
						{
							
							if(_data[id] === undefined) continue;
							var selitem = _data[id];
							if(selitem[delfield] === undefined) continue;
							delete selitem[delfield];
							if(_changed[id.toString()] !== undefined)
								if(_changed[id.toString()][delfield] !== undefined)
									delete _changed[id.toString()][delfield];
						}
					
					}
					_changedcustom = [];
					try{
							_grid.removeCellCssStyles("changed");
							_grid.setCellCssStyles("changed", _changed);
							_grid.setColumns(newcols);
						} catch (err) {
							;
						}
					
					var offset = 0;
					var _arrData = {};
					$('.trcustom:visible').each(function ()
					{
						var $tds = $(this).children('td');
						var customobj = {};
						$tds.each(function ()
						{
							var field = $(this).attr('data-field');
							var fieldinfo = "";
							if(field == 'name')
							{
								customobj.name = $(this).text();
								
							}else if(field == 'type')
							{
								customobj.type = $(this).attr('data-type');
								if(customobj.type == 'custom')
								{
									if($(this).attr('data-vals') == "true")
										customobj.isnewvals = true;
									else
										customobj.isnewvals = false;
								}else if(customobj.type == 'select')
								{
									customobj.selvals = $(this).attr('data-vals');
								}
							}else if(field == 'isvisible')
							{
								customobj.isvisible = $(this).find('input').is(':checked');
							}
						})
						_arrData[customobj.name] = customobj;
						if(_mapfield[customobj.name] === undefined)
						{
							var insertobj = {};
							insertobj[customobj.name] = _mapfield.length;
							
							_mapfield[customobj.name] = _idmap.length;
							insertobj.field = customobj.name;
							insertobj.id = insertobj.field;
							insertobj.name = insertobj.field;
							
							var newitem = {};
							newitem.id = customobj.name;
							newitem.name = customobj.name;
							newitem.field = customobj.name;
							
							if(customobj.type == "text")
							{
								newitem.editor = Slick.Editors.Text;
							}else if(customobj.type == "multitext")
							{
								newitem.editor = Slick.Editors.TextArea;
								insertobj.textarea = true;
							}else if(customobj.type == "integer")
							{
								newitem.editor = Slick.Editors.Text;
								insertobj.type = 'int';
							}else if(customobj.type == "decimal")
							{
								newitem.editor = Slick.Editors.Text;
								insertobj.type = 'float2';
							}else if(customobj.type == "decimal3")
							{
								newitem.editor = Slick.Editors.Text;
								insertobj.type = 'float3';
							}else if(customobj.type == "checkbox")
							{
								newitem.cssClass = "cell-effort-driven";
								newitem.formatter = Slick.Formatters.Checkmark;
								newitem.editor = Slick.Editors.Checkbox;
								insertobj.checkbox = true;
								insertobj.type = 'set';
							}else if(customobj.type == "select")
							{
								newitem.editor = Slick.Editors.Select;
								newitem.options = customobj.selvals;
								insertobj.type = 'set';
								insertobj.options= customobj.selvals;
							}else if(customobj.type == "custom")
							{
								newitem.editor = Slick.Editors.Text;
								insertobj.scope = SCOPE.PRODALL;
								insertobj.type = 'customtax';
								insertobj.isnewvals = customobj.isnewvals;
							}else if(customobj.type == "customh")
							{
								newitem.editor = Slick.Editors.Category;
								insertobj.scope = SCOPE.PRODALL;
								insertobj.type = 'customtaxh';
							}
							AddBulkAndSelectFields(customobj);
							newitem.sortable = true;
							_allcols.push(newitem);
							_idmap.push(insertobj);
							changed = true;
							if(customobj.isvisible)
							{
								var offset = _allcols.length - newcols.length;
								var hascol = false;
								var len = newcols.length;
								while (len--) {
								    var obj = newcols[len];
									if(obj.field === customobj.name)
									{
										hascol = true;
										break;
									}
								}
								if(!hascol)
								{
									len = _allcols.length;
									var shouldsearch = false;
									var found = false;
									var insertobj;
									while (len--) {
									    var obj = _allcols[len];
										if(obj.field === customobj.name)
										{
											insertobj = _allcols[len];
											shouldsearch = true;
											continue;
										}
										if(shouldsearch)
										{
											var newlen = newcols.length;
											while (newlen--) {
											    var newobj = newcols[newlen];
												if(newobj.field === obj.field )
												{
													newcols.splice(newlen+1,0,insertobj);
													changed = true;
													found = true;
													break;
												}
											}
										}
										if(found) break;
											
									}
									if(!found)
										newcols.push(insertobj);
								}
							}
							

						}else
						{//field exits
							
							try{
									if(!customobj.isvisible)
									{
										offset++;
										var len = newcols.length;
										while (len--) {
										    var obj = newcols[len];
											if(obj.field === customobj.name)
											{
												newcols.splice(len,1);
												changed = true;
												break;
											}
										}
										$("#bulkdialog tr[data-id='" + customobj.name + "']").hide();
										$("#selectdialog tr[data-id='" + customobj.name + "']").hide();
									}else
									{
										$("#bulkdialog tr[data-id='" + customobj.name + "']").show();
										$("#selectdialog tr[data-id='" + customobj.name + "']").show();
										var offset = _allcols.length - newcols.length;
										var hascol = false;
										var len = newcols.length;
										while (len--) {
										    var obj = newcols[len];
											if(obj.field === customobj.name)
											{
												hascol = true;
												break;
											}
										}
										if(!hascol)
										{
											len = _allcols.length;
											var shouldsearch = false;
											var found = false;
											var insertobj;
											while (len--) {
											    var obj = _allcols[len];
												if(obj.field === customobj.name)
												{
													insertobj = _allcols[len];
													shouldsearch = true;
													continue;
												}
												if(shouldsearch)
												{
													var newlen = newcols.length;
													while (newlen--) {
													    var newobj = newcols[newlen];
														if(newobj.field === obj.field )
														{
															newcols.splice(newlen+1,0,insertobj);
															changed = true;
															found = true;
															break;
														}
													}
												}
												if(found) break;
													
											}
											if(!found)
												newcols.push(insertobj);
										}
									}
								}catch(err)
								{
									;
								}
								
							
						}
					
						
						
					})
						if(changed)
						{
							_grid.setColumns(newcols);
							var newlen = newcols.length;
							while (newlen--) 
							{
								var arritem = {};
							    var newobj = newcols[newlen];
								arritem.field = newobj.field;
								arritem.width = newobj.width;
//								_arrData[arritem.field] = arritem.width ;
							}
						}
						
					if(!changed)
					{
						$( this ).dialog( "close" );
						return;
					}	
				}catch(err)
				{
					_grid.setColumns(newcols);
					$( this ).dialog( "close" );
					return;
				}
				var arrColumns = {};
				var newcols = _grid.getColumns();
				var newlen = newcols.length;
				while (newlen--) {
				    var newobj = newcols[newlen];
					arrColumns[newobj.field] = newobj.width;
				}
				var ajaxarr = {};
				ajaxarr.action = 'wpmelon_adv_bulk_edit';
				ajaxarr.type = 'savecustom';
				ajaxarr.nonce = W3ExABE.nonce;
						
				var $elem = $('.ui-dialog-buttonset > .ui-button:visible').first();
		        $elem.css('position','relative').append('<div class="showajax"></div>');
				$('.showajax').css({
					left:'15px'
				});
				$elem.button("disable");
				ajaxarr.data = _arrData;
				ajaxarr.columns = arrColumns;
				var dlg = $(this);
				jQuery.ajax({
				     type : "post",
				     dataType : "json",
				     url : W3ExABE.ajaxurl,
				     data : ajaxarr,
				     success: function(response) {
							$('.showajax').remove();
							$elem.button("enable");
							for (var key in _arrData) 
							{
							  if (_arrData.hasOwnProperty(key)) 
							  {
								var obj = _arrData[key];
								if(obj.type !== undefined && (obj.type=== 'customh' || obj.type=== 'custom'))
								{
									if(response[obj.name] !== undefined)
									{
										if(obj.type=== 'customh')
										{
											var bulkdata = '<td><input id="set'+obj.name+'" type="checkbox" class="bulkset" data-id="'+obj.name+'" data-type="customtaxh"><label for="set'+obj.name+'">Set '+obj.name+'</label></td><td></td><td><select id="bulk' + obj.name + '"'+response[obj.name]+'</td><td></td>';
											$("#bulkdialog tr[data-id='" + obj.name + "']").html(bulkdata);
											if(response[obj.name + 'edit'] !== undefined)
											{
												$("#categoriesdialog").append(response[obj.name + 'edit']);
											}
										}
										W3Ex['taxonomyterms' + obj.name] = response[obj.name];
									}
									
								}
							   }
							}
							if(response['customfieldsdata'] !== undefined)
							{
								W3Ex.customfields = response['customfieldsdata'];
							}
							ShowCustomSearchFilters();
							$('.makechosen').chosen({disable_search_threshold: 10});
							dlg.dialog( "close" );
							
				     },
					  error:function (xhr, status, error) 
					  {
//					  	 $('#debuginfo').html(error);
					  	  $('.showajax').remove();
						  $elem.button("enable");
						  dlg.dialog( "close" );
					  }
				  }) ;
//				$( this ).dialog( "close" );
		  },
		  Cancel: function()
		  {
		  	 
			  $( this ).dialog( "close" );
		  }
		  }
		});

	$('#showselectedbut').click(function(){
//		alert('asd');
		var selectedRows = _grid.getSelectedRows();
		_seldata.length = 0;
		_seldata.length = _data.length;
		for(var i = 0; i < selectedRows.length; i++)
		{
			if(_data[selectedRows[i]] !== undefined)
			{
				_seldata[selectedRows[i]] = _data[selectedRows[i]];
			}
		}
		_grid.setData(_seldata);
		_grid.resetActiveCell();
		_grid.invalidate();
			
	})
	
	 return {
		incConItems:function(){
			_conitems++;
		}
	};
	
	
})(jQuery);

});

