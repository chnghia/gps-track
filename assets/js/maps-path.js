jQuery(document).ready(function() {
	initialize();
	
	jQuery( 'a#hide_search' ).click(function(evt) {
		if ($(this).text() == 'hide search') {
			$('#search_form').slideUp(500);
			$(this).text('show search');
			$('#search_link').css('top', '105px');
			$(this).css('color', 'blue');
		} else {
			$('#search_form').slideDown(500);
			$(this).text('hide search');
			$('#search_link').css('top', '145px');
			$(this).css('color', 'white');
		}
		evt.preventDefault();
	});
	
	jQuery( 'a#hide_search_result' ).click(function(evt) {
		if ($(this).text() == '[hide]') {
			$(this).text('[show]');
			$('#directions_panel_inner').hide();
			$('#directions_panel').animate({height:'31px', top: '520px'}, 500);
		} else {
			$(this).text('[hide]');
			$('#directions_panel_inner').show();
			$('#directions_panel').animate({height:'136px', top: '415px'}, 500);
		}
		evt.preventDefault();
	});
	
	var dates = jQuery( "#from_date, #to_date" ).datepicker({
		defaultDate: "+1w",
		changeMonth: true,
		numberOfMonths: 1,
		dateFormat: "dd/mm/yy",
		onSelect: function( selectedDate ) {
			var option = this.id == "from_date" ? "minDate" : "maxDate",
				instance = $( this ).data( "datepicker" );
				date = $.datepicker.parseDate(
					instance.settings.dateFormat ||
					$.datepicker._defaults.dateFormat,
					selectedDate, instance.settings );
			dates.not( this ).datepicker( "option", option, date );
		}
	});
	
	jQuery('#path_search_form').submit(function(evt) {
		evt.preventDefault();
		
		displayDevices();
		
	});
});

function initialize() {

	map = new google.maps.Map(document.getElementById("map_canvas"), mapOptions); 
	
	map.setCenter(defaultPoint);
}

function displayDevices()
{
	jQuery('#ajax_loading').show();
	
	jQuery( 'input[name="imei"]' ).each(function(evt) {
		if (!jQuery(this).is(':checked')) {
			var imei = jQuery(this).val();
			
			return true;
		}
		
		if (directionsDisplay.length > 0) {
		for (var i=0; i<directionsDisplay.length;i++) {
			directionsDisplay[i].setMap(null);
		}
		directionsDisplay = null;
		directionsDisplay = [];
		marker1.setVisible(false);
		marker1 = null;
		marker2.setVisible(false);
		marker1 = null;
		}

		var imei = jQuery(this).val();
		var fdate = jQuery('#from_date').val();
		var fhour = jQuery('#from_hour').val();
		var tdate = jQuery('#to_date').val();
		var thour = jQuery('#to_hour').val();
		
		jQuery.getJSON(base_url + "/maps/data_path/" + imei, { from_date: fdate, from_hour: fhour, to_date: tdate, to_hour: thour }, function(data){
		
			var summaryPanel = document.getElementById("directions_panel_inner");
			summaryPanel.innerHTML = "";
			var routeSegment = 1;
			var distance = 0;
			
			if (!data.path || data.path.length < 2) {
				summaryPanel.innerHTML = "No data return";
				jQuery('#ajax_loading').hide();
				return;
			}
				
			for (var phase=0; phase<= (data.path.length/DEFAULT_WAYPOINTS); phase++) {
				if (data.path.length - phase*DEFAULT_WAYPOINTS < 2)
					continue;
					
				var start = null, end = null, waypts = [];
				
				start = new google.maps.LatLng(parseFloat(data.path[phase*DEFAULT_WAYPOINTS].lat),parseFloat(data.path[phase*DEFAULT_WAYPOINTS].lng));
				//var count = 0;
				for (var i = 1; i < DEFAULT_WAYPOINTS; i++) {
					if (phase*DEFAULT_WAYPOINTS + i >= data.path.length-1)
						break;
					waypts.push({location:new google.maps.LatLng(parseFloat(data.path[phase*DEFAULT_WAYPOINTS + i].lat),parseFloat(data.path[phase*DEFAULT_WAYPOINTS + i].lng)), stopover:true});
				}
				if (phase*DEFAULT_WAYPOINTS + i < data.path.length) {
					end = new google.maps.LatLng(parseFloat(data.path[phase*DEFAULT_WAYPOINTS+i].lat),parseFloat(data.path[phase*DEFAULT_WAYPOINTS+i].lng));
				}
				
				var request = {
						origin: start, 
						destination: end,
						waypoints: waypts,
						optimizeWaypoints: true,
						provideRouteAlternatives: false,
						travelMode: google.maps.DirectionsTravelMode.DRIVING,
						unitSystem: google.maps.DirectionsUnitSystem.METRIC
				};
				
				directionsService.route(request, function(response, status) {
					if (status == google.maps.DirectionsStatus.OK) {
						var directionDisplay = new google.maps.DirectionsRenderer({preserveViewport: true, suppressInfoWindows:true, suppressMarkers: true});
						directionDisplay.setMap(map);
						directionDisplay.setDirections(response);
						directionsDisplay.push(directionDisplay);
						var route = response.routes[0];

						// For each route, display summary information.
						for (var i = 0; i < route.legs.length; i++) {
							//var routeSegment = i + 1;
							/*
							summaryPanel.innerHTML += "<b>Route Segment: " + routeSegment++ + "</b><br />";
							summaryPanel.innerHTML += route.legs[i].start_address + " to ";
							summaryPanel.innerHTML += route.legs[i].end_address + "<br />";
							summaryPanel.innerHTML += route.legs[i].distance.text + "<br /><br />";
							*/
							distance += route.legs[i].distance.value;
							summaryPanel.innerHTML = "<b>Distance: " + Math.round(distance/10)/100 + " km</b><br/>";
						}
						jQuery('#ajax_loading').delay(500).hide();
					}
				});
				//break;
			}
			
			if (data.path.length > 0) {
				marker1 = new google.maps.Marker({  
					 map: map,  
					 position: new google.maps.LatLng(parseFloat(data.path[0].lat),parseFloat(data.path[0].lng)),  
					 title: "Start"
				});  
				
				marker2 = new google.maps.Marker({  
					 map: map,  
					 position: new google.maps.LatLng(parseFloat(data.path[data.path.length-1].lat),parseFloat(data.path[data.path.length-1].lng)),
					 title: "End"  
				}); 
				map.panTo(new google.maps.LatLng(parseFloat(data.path[0].lat),parseFloat(data.path[0].lng)));
			}
			
		// END getJSON
		});
	});
}