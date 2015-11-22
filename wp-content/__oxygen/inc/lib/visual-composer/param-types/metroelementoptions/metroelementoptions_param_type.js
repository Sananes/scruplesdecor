/*
	Metro Element Param Type
*/

;(function($, window, undefined)
{
	$(document).ready(function()
	{
		var wpb_element_edit_modal = $(".wpb-element-edit-modal"),
			me_size = wpb_element_edit_modal.find('select[name="metroelement_size"]'),
			type_options = wpb_element_edit_modal.find('#metroelement_box_type_options'),
			selected_widgets_field = wpb_element_edit_modal.find('input[name="selected_widgets"]');
		
		// IF Metro Element Modal Is Opened
		if(me_size.length)
		{
			var loading_box = type_options.find('.loading_options'),
				all_box_options = wpb_element_edit_modal.find(".me_box_widgets");
			
			checkOptions();
			parseStrValuesToFields();
			
			fillField();
			
			me_size.change(checkOptions);
			
			type_options.find('.me_field').change(fillField);
		}
		
		function checkOptions()
		{	
			var size = me_size.val(),
				box_options_to_show = all_box_options.filter('#me_box_widgets_' + size);
			
			loading_box.hide();
			
			all_box_options.hide();
			box_options_to_show.show();
		}
		
		function fillField()
		{
			var parsed_str = '';
			
			type_options.find('.me_field:visible').each(function(i, el)
			{
				var $this 	= $(el),
					name	= $this.attr('name'),
					value 	= $this.val();
					
				if($this.is('[type="checkbox"]'))
				{
					value = $this.is(':checked') ? 1 : 0;
				}
				
				parsed_str += name + '=' + value + '&';
			});
			
			if(parsed_str.length > 0)
			{
				parsed_str = parsed_str.substr(0, parsed_str.length - 1);
			}
			
			selected_widgets_field.val(parsed_str);
			
			console.log(parsed_str);
			
			return parsed_str;
		}
		
		function parseStrValuesToFields()
		{
			var values = {},
				str = selected_widgets_field.val().split('&');
			
			if(str && str.length > 0)
			{
				for(var i in str)
				{
					var str_x = str[i].split('='),
						name = str_x[0],
						value = str_x[1];
					
					if(name != undefined)
					{
						values[name] = value;	
					}
				}
			}
			
			for(var field_id in values)
			{
				var $field = type_options.find('.me_field[name="' + field_id + '"]:visible'),
					value = values[field_id];
				
				// Set Selected for Field Types
				if($field.is('select'))
				{
					$field.find('option').attr('selected', false).filter('[value="' + value + '"]').attr('selected', true);
				}
				else
				if($field.is('input[type="checkbox"]'))
				{
					$field.attr('checked', value == 1 ? true : false);
				}
			}
			
			//type_options.find('.me_field:visible');
		}
	});
	
})(jQuery, window);