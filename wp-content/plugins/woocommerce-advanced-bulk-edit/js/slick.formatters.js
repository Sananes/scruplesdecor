/***
 * Contains basic SlickGrid formatters.
 * 
 * NOTE:  These are merely examples.  You will most likely need to implement something more
 *        robust/extensible/localizable/etc. for your use!
 * 
 * @module Formatters
 * @namespace Slick
 */

(function ($) {
  // register namespace
  $.extend(true, window, {
    "Slick": {
      "Formatters": {
        "PercentComplete": PercentCompleteFormatter,
        "PercentCompleteBar": PercentCompleteBarFormatter,
        "YesNo": YesNoFormatter,
        "Checkmark": CheckmarkFormatter,
		"Image": ImageFormatter,
		"Gallery": ImageGalleryFormatter,
		"ProductUrl":ProductUrlFormatter
      }
    }
  });

  function PercentCompleteFormatter(row, cell, value, columnDef, dataContext) {
    if (value == null || value === "") {
      return "-";
    } else if (value < 50) {
      return "<span style='color:red;font-weight:bold;'>" + value + "%</span>";
    } else {
      return "<span style='color:green'>" + value + "%</span>";
    }
  }

  function PercentCompleteBarFormatter(row, cell, value, columnDef, dataContext) {
    if (value == null || value === "") {
      return "";
    }

    var color;

    if (value < 30) {
      color = "red";
    } else if (value < 70) {
      color = "silver";
    } else {
      color = "green";
    }

    return "<span class='percent-complete-bar' style='background:" + color + ";width:" + value + "%'></span>";
  }

  function YesNoFormatter(row, cell, value, columnDef, dataContext) {
    return value ? "Yes" : "No";
  }

  function CheckmarkFormatter(row, cell, value, columnDef, dataContext) {
  	if(value === "yes" || value === "no")
   		return value === "yes" ? "<img src='" + W3Ex.imagepath + "images/tick.png'>" : "";
    else
		return value === "instock" ? "<img src='" + W3Ex.imagepath + "images/tick.png'>" : "";
  }
  
   function ImageFormatter(row, cell, value, columnDef, dataContext)
   {
   		if(dataContext['_thumbnail_id_val'] !== undefined && dataContext['_thumbnail_id_val'] !== "")
       		return "<img style='width:32px;height:32px;position:relative;top:-6px;' src='" + dataContext['_thumbnail_id_val'] + "'>";
		return "";
   }
   
   function ProductUrlFormatter(row, cell, value, columnDef, dataContext)
   {
   		if(columnDef.id !== undefined)
		{
			if(columnDef.id === '_product_permalink')
			{
				if(dataContext['_product_permalink'] !== undefined && dataContext['_product_permalink'] !== "")
       			return "<a href='" + dataContext['_product_permalink'] + "' target='_blank'>" + dataContext['_product_permalink'] + "</a>";
			}else
			{
				if(dataContext['_product_adminlink'] !== undefined && dataContext['_product_adminlink'] !== "")
       			return "<a href='" + dataContext['_product_adminlink'] + "' target='_blank'>" + dataContext['_product_adminlink'] + "</a>";
				return "";
			}
		}
   		if(dataContext['_product_permalink'] !== undefined && dataContext['_product_permalink'] !== "")
       		return "<a href='" + dataContext['_product_permalink'] + "' target='_blank'>" + dataContext['_product_permalink'] + "</a>";
		return "";
   }
   
    function ImageGalleryFormatter(row, cell, value, columnDef, dataContext)
   {
   		if(dataContext['_product_image_gallery_val'] !== undefined && dataContext['_product_image_gallery_val'] !== "")
		{
		var imgstr = "";
		var images = dataContext['_product_image_gallery_val'];
			if(images.indexOf('|') !== -1)
			{
				var res = images.split('|');
				if(res instanceof Array)
				{
					
					for(var i=0;i< res.length; i++)
					{
						imgstr+=  "<img style='width:32px;height:32px;position:relative;top:-6px;' src='" + res[i] + "'>";
					}
		       	    return imgstr;
				}
			}
			return "<img style='width:32px;height:32px;position:relative;top:-6px;' src='" + images + "'>";;
		}
		return "";
   }
})(jQuery);
