/*
	Contact Page
*/


function initialize()
{
	var rotate_angle = 0;

	// Init Vars
	map_types = [google.maps.MapTypeId.ROADMAP, google.maps.MapTypeId.SATELLITE, google.maps.MapTypeId.HYBRID];
	position = new google.maps.LatLng(contact_vars.lat, contact_vars.lng);

	var map_options = {
			//center: position,
			zoom: contact_vars.zoom,
			disableDefaultUI: true,
			mapTypeId: map_types[contact_vars.map_type_id],
			scrollwheel: false
		},
		markers = [],
		bounds = new google.maps.LatLngBounds();;

	map = new google.maps.Map(document.getElementById("contact-map"), map_options);
	
	google.maps.event.addListener(map, 'click', function (event) {
		map.setOptions( { 'scrollwheel': true });
	});
	
	jQuery( '#contact-map' ).on( 'mouseout', function() {
		map.setOptions( { 'scrollwheel': false });
	} );

	var markerUrl = contact_vars.shopPin;

	jQuery.each(contact_vars.locations, function(i, loc)
	{
		var location = new google.maps.LatLng(loc.lat, loc.lng);
		
		if(typeof contact_vars.pinSize == 'object')
		{
			contact_vars.shopPin = new google.maps.MarkerImage(markerUrl, null, null, null, new google.maps.Size(contact_vars.pinSize[0]/2, contact_vars.pinSize[1]/2));
		}

		markers.push(new google.maps.Marker({
			position: location,
			map: map,
			icon: contact_vars.shopPin
		}));

		bounds.extend(location);
	});

	map.panBy(pan_x, pan_y);

	map.setCenter( bounds.getCenter() );

	if(contact_vars.fourtyFive)
	{
		map.setTilt(45);
		map.setHeading(rotate_angle);
	}


	// Panorama
	panorama = map.getStreetView();

	panorama.setPosition(position);
	panorama.setPov({
		heading: contact_vars.streetViewHeading,
		pitch: contact_vars.streetViewPitch
	});

	panorama.setOptions({
		panControl: false,
		zoomControl: false,
		mapTypeControl: false,
		scaleControl: false,
		streetViewControl: false,
		overviewMapControl: false
	});

	if(contact_vars.map_type_id == 3)
		panorama.setVisible(true);

	var $switch_type_links = jQuery(".map-type-switcher a").not('.rotate-view'),
		$rotate_map = jQuery(".map-type-switcher .rotate-view");

	$switch_type_links.on('click', function(ev)
	{
		ev.preventDefault();

		var $this = jQuery(this);

		$switch_type_links.removeClass('current');
		$this.addClass('current');

		if($this.data('type') == 'street')
			panorama.setVisible(true);
		else
			panorama.setVisible(false);

		switch($this.data('type'))
		{
			case 'roadmap':
				map.setMapTypeId(map_types[0]);
				break;

			case 'hybrid':
				map.setMapTypeId(map_types[2]);
				break;

			case 'street':
				panorama.setZoom(1);
				break;

			default:
				map.setMapTypeId(map_types[1]);
		}


		switch($this.data('type'))
		{
			case 'hybrid':
			case 'satellite':
				$switch_type_links.parent().addClass('satellite-view');
				break;

			default:
				$switch_type_links.parent().removeClass('satellite-view');
		}

		map.setCenter(position);
		map.panBy(pan_x, pan_y);

		if(contact_vars.fourtyFive)
		{
			map.setTilt(45);
			map.setHeading(rotate_angle);
		}
	});



	$rotate_map.click('click', function(ev)
	{
		ev.preventDefault();

		if(contact_vars.fourtyFive)
		{
			rotate_angle += 90;
			map.setTilt(45);
			map.setHeading(rotate_angle);
		}
	});

	// Resize
	jQuery(window).on('lab.resize', function(ev)
	{
		resizeMapContainer();
	});

	resizeMapContainer();

	// Toggle Blocks
	var $cbe = jQuery(".contact-blocks-env"),
		$tib = jQuery(".toggle-info-blocks"),
		hidden_cbe_class = 'hidden-blocks';

	$tib.click(function(ev)
	{
		ev.preventDefault();

		var is_visible = $cbe.hasClass(hidden_cbe_class),
			text = $tib.data('visible');

		if( ! is_visible)
		{
			text = $tib.data('hidden');
			$cbe.addClass(hidden_cbe_class);
		}
		else
		{
			$cbe.removeClass(hidden_cbe_class);
		}

		$tib.html( text );
	});
}

function resizeMapContainer()
{
	var $map = jQuery("#contact-map.contact-map-canvas");


	if(public_vars.$body.hasClass('ht-1'))
	{
		$map.css({
			minHeight: jQuery(window).height()
		});
	}

	google.maps.event.trigger(map, 'resize');
}

google.maps.event.addDomListener(window, 'load', initialize);


jQuery(document).ready(function($)
{
	var $roc		  = $(".route-options-container"),
		$ro           = $(".route-options"),
		$show_route   = $('.show-me-the-route > a'),
		$af           = $ro.find(".address-field"),
		$cr			  = $roc.find("#calc-route"),
		$address	  = $af.find('input[type="text"]'),
		$re			  = $roc.find('.route-error'),
		$rd			  = $roc.find('.route-details'),
		$rc 		  = $roc.find('.route-clear'),
		added_markers = [];

	$show_route.on('click', function(ev)
	{
		ev.preventDefault();

		$roc.slideToggle('fast');
	});

	$rc.on('click', function(ev)
	{
		ev.preventDefault();

		$rc.removeClass('visible');

		$.each(added_markers, function(i, val)
		{
			val.setMap(null);
		});

		map.setCenter(position);
		map.panBy(pan_x, pan_y);
		$rd.hide();
		$address.val('');
	});

	$address.keyup(function(ev)
	{
		if(ev.keyCode == 13)
		{
			$cr.trigger('click');
		}
	});

	if( ! navigator.geolocation && $ro.length > 1)
	{
		$ro.first().remove();
		$af.show();
	}
	else
	{
		$ro.on('change', 'input[type="radio"]', function(ev)
		{
			var $this = $(this);

			if($this.val() == 'address')
			{
				$af.slideDown('fast');
				$address.focus();
			}
			else
			{
				$af.slideUp('fast');
			}
		});

	}

	$ro.find('input[type="radio"]').first().attr('checked', true);

	$cr.click(function(ev)
	{
		var type = $ro.find('input[type="radio"]:checked').val(),
			cca = parseInt($("#current-contact-address").val(), 10),
			position = contact_vars.locations[cca];

		$rd.hide();


		if(type == 'address')
		{
			if($.trim($address.val()).length > 0)
			{
				$cr.addClass('loading');

				var geocoder = new google.maps.Geocoder();

				geocoder.geocode( { 'address': $address.val()}, function(results, status)
				{
					if(status == google.maps.GeocoderStatus.OK)
					{
						var first_loc = results[0].geometry.location;

						$address.next().removeClass('visible');

						var origin = position.lat + ',' + position.lng,
							destination = first_loc.lat() + ',' + first_loc.lng();

						getDirections(origin, destination);

					}
					else
					{
						$address.next().addClass('visible');
					}
				});
			}
			else
			{
				$address.focus();
			}
		}
		else
		if(type == 'gps')
		{
			$cr.addClass('loading');

			navigator.geolocation.getCurrentPosition(function(pos)
			{
				var destination = pos.coords.latitude + ',' + pos.coords.longitude,
					origin = position.lat + ',' + position.lng;

				getDirections(origin, destination);
			});
		}
	});


	function getDirections(origin, destination)
	{
		var route_path = 'http://maps.googleapis.com/maps/api/directions/json?origin=' + origin + '&destination=' + destination + '&sensor=false'

		$.getJSON(ajaxurl,
		{
			action: 'laborator_calc_route',
			route_path: route_path
		},
		function(resp)
		{
			$cr.removeClass('loading');

			if(resp.status != 'OK')
			{
				$re.slideDown('fast');
				$rd.hide();
				$rc.removeClass('visible');
				return;
			}

			$re.slideUp('fast');

			// Remove Existing Markers
			$.each(added_markers, function(i, val)
			{
				val.setMap(null);
			});


			// Create Route Path
			var points = resp.routes[0].overview_polyline.points,
				polyline = google.maps.geometry.encoding.decodePath(points),
				dest_waypoint = new google.maps.Polyline({
					path: polyline,
					geodesic: true,
					strokeColor: $cr.css('backgroundColor'),
					strokeOpacity: 0.9,
					strokeWeight: 4
				}),

				distance = resp.routes[0].legs[0].distance,
				duration = resp.routes[0].legs[0].duration,

				bounds = new google.maps.LatLngBounds();

				dest_waypoint.getPath().forEach(function(e){
					bounds.extend(e);
				});

				map.fitBounds(bounds);

				dest_waypoint.setMap(map);

				added_markers.push(dest_waypoint);

				var last_point = polyline[polyline.length - 1],
					marker = new google.maps.Marker({
						position: last_point,
						map: map,
						icon: contact_vars.carPin
					});


			added_markers.push(marker);

			var dist = parseFloat(distance.value/1000).toFixed(2);

			if(dist > 1000)
				dist = parseInt(dist);

			$rd.find('.route-detail.time span').html( parseInt(Math.round(duration.value/60)) );
			$rd.find('.route-detail.distance span').html( dist );

			// Pan Map
			map.panBy(pan_x, pan_y);
			$rd.show();
			$rc.addClass('visible');
		});
	}
});