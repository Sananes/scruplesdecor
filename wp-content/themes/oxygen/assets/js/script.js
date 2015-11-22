
var ww = document.body.clientWidth;

$(document).ready(function() {
	$(".nav li a").each(function() {
		if ($(this).next().length > 0) {
			$(this).addClass("parent");
		};
	})
	
	$(".toggleMenu").click(function(e) {
		e.preventDefault();
		$(this).toggleClass("active");
		$(".nav").toggle();
	});
	adjustMenu();
	/* add by jas */
		/*
		$(".nav li").each(function(){
		if($(this).has("ul")){
		//$('.nav').hide();
		//$('ul.nav', this).show();	
			//if($(".nav li ul").hasClass("back-button-menu")){
			//$(".back-button-menu").remove();
			//
			
			
			}
				
    //$(this).children("ul").show();
		$(this).children("ul").prepend('<li class="back-button-menu"><a href="#">Back</a></li>');	
          }
       });
	   $(".nav li").click(function(){
	     if($(this).has("ul")){
			$('.nav').hide();
			$('.nav li ul', this).show();
		 }
	   });
			*/						
	/* end add by jas */
})

$(window).bind('resize orientationchange', function() {
	ww = document.body.clientWidth;
	adjustMenu();
});

var adjustMenu = function() {
	if (ww < 768) {
		$(".toggleMenu").css("display", "inline-block");
		if (!$(".toggleMenu").hasClass("active")) {
			$(".nav").hide();
		} else {
			$(".nav").show();
		}
		$(".nav li").unbind('mouseenter mouseleave');
		$(".nav li a.parent").unbind('click').bind('click', function(e) {
			// must be attached to anchor element to prevent bubbling
			e.preventDefault();
			$(this).parent("li").toggleClass("hover");
		});
	} 
	else if (ww >= 768) {
		$(".toggleMenu").css("display", "none");
		$(".nav").show();
		$(".nav li").removeClass("hover");
		$(".nav li a").unbind('click');
		$(".nav li").unbind('mouseenter mouseleave').bind('mouseenter mouseleave', function() {
		 	// must be attached to li so that mouseleave is not triggered when hover over submenu
		 	$(this).toggleClass('hover');
		});
	}
}

