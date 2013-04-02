jQuery(document).ready(function() {
	initialize();
	
	jQuery( 'input[name="imei"]' ).click(function(evt) {
			displayDevices();
	});

	setTimeout(function (){ 
		displayDevices();
	}, 1000);
});

function initialize() {

	map = new google.maps.Map(document.getElementById("map_canvas"), mapOptions); 
	
	map.setCenter(defaultPoint);
	
	window.setInterval('updateMap();', 15000);
}

/*
function setInfoWindow(mappoint, image)
{
	//var mappoint = new google.maps.LatLng(<? echo $imei['lat'];?>, <? echo $imei['long']; ?>);

	var pointMarker = new google.maps.Marker({
			position: mappoint,
			map: map,
			icon: image
	});

	var latlngStr = "IMEI:" + "<? echo $imei['imei']; ?>" +"<br />" + 
									"LatLng: " + mappoint.lat() + " , " + mappoint.lng() + "<br />" +
									"<? echo $imei['raw_data']; ?>";
									
	//coordInfoWindow = new google.maps.InfoWindow({content: "Tan Binh, HCMC"});
	coordInfoWindow.setContent(latlngStr);
	coordInfoWindow.setPosition(mappoint);
	
	var geocoder = new google.maps.Geocoder();
	geocoder.geocode({'latLng': mappoint}, function(results, status) {
		if (status == google.maps.GeocoderStatus.OK) {
			if (results[0]) {
				latlngStr += "<br/>Address: " + results[0].formatted_address;
				coordInfoWindow.setContent(latlngStr);
				coordInfoWindow.setPosition(mappoint);
			}
		} else {
			//alert("Geocoder failed due to: " + status);
		}
	});
	
	google.maps.event.addListener(pointMarker, 'click', function() {
		coordInfoWindow.open(map);
	});
}
*/

/*
function change_imei(frm, btn)
{
		if (btn && document.forms[frm].which_button) document.forms[frm].which_button.value = btn;
		document.forms[frm].submit();
}
*/

function updateMap()
{
	//busMarker.setIcon("/images/track.png");
	//busMarker.setTitle(tstamp.getHours() + ":" + tstamp.getMinutes() + ":" + tstamp.getSeconds());
	/*
	tstamp = new Date();
		// get the data for the map
			jQuery.getJSON("<? echo site_url('/maps/data/'.$curr_imei); ?>", {}, function(data){
			if (data != undefined && data.length!=0) {
			
				var newCoord = new google.maps.LatLng(parseFloat(data.imei.lat), parseFloat(data.imei.long));
				pointMarker.setPosition(newCoord);
				
				var latlngStr = "IMEI:" + data.imei.imei +"<br />" + 
									"LatLng: " + newCoord.lat() + " , " + newCoord.lng() + "<br />" +
									data.imei.raw_data;
				coordInfoWindow.setContent(latlngStr);
				coordInfoWindow.setPosition(newCoord);
				geocoder.geocode({'latLng': newCoord}, function(results, status) {
					if (status == google.maps.GeocoderStatus.OK) {
						if (results[0]) {
							//infowindow.setContent(results[0].formatted_address);
							//infowindow.open(map, marker);
							latlngStr += "<br/>Address: " + results[0].formatted_address;
							coordInfoWindow.setContent(latlngStr);
							coordInfoWindow.setPosition(newCoord);
						}
					} else {
						//alert("Geocoder failed due to: " + status);
					}
				});
				
				map.setCenter(newCoord);
			}
		});
	*/
	displayDevices();
}

function displayDevices()
{
	jQuery( 'input[name="imei"]' ).each(function(evt) {
		if (!jQuery(this).is(':checked')) {
			var imei = jQuery(this).val();
			
			eval("map_pointer.imei_"+imei+".marker.setVisible(false)");
			return true;
		}

		var imei = jQuery(this).val();
		jQuery.getJSON(base_url + "/maps/data/" + imei, {}, function(data){
		
			var newCoord = new google.maps.LatLng(parseFloat(data.imei.lat), parseFloat(data.imei.lng));
			
			eval("map_pointer.imei_"+imei+".marker.setVisible(true);");
			eval("map_pointer.imei_"+imei+".marker.setMap(map);");
			eval("map_pointer.imei_"+imei+".marker.setPosition(newCoord);");
			
			if (eval("map_pointer.imei_"+imei+".default")) {
				map.panTo(newCoord);
			}


			var latlngStr = "Last update: " + data.imei.created +"<br />" + 
									"Device: " + data.imei.number_plate +"<br />" + 
									"LatLng: " + (newCoord.lat()).toFixed(5)+ " , " + (newCoord.lng()).toFixed(5) + "<br />" +
									data.imei.raw_data;

			eval("map_pointer.imei_"+imei+".infoWindow.setContent(latlngStr);");
			eval("map_pointer.imei_"+imei+".infoWindow.setPosition(newCoord);");
			
			/*
			geocoder.geocode({'latLng': newCoord}, function(results, status) {
					if (status == google.maps.GeocoderStatus.OK) {
						if (results[0]) {
							latlngStr += "<br/>Address: " + results[0].formatted_address;
							coordInfoWindow.setContent(latlngStr);
							coordInfoWindow.setPosition(newCoord);
						}
					} else {
						alert("Geocoder failed due to: " + status);
					}
				});
			*/
			
			google.maps.event.clearListeners(eval("map_pointer.imei_"+imei+".marker"), 'click')
			google.maps.event.addListener(eval("map_pointer.imei_"+imei+".marker"), 'click', function() {
				jQuery( 'input[name="imei"]' ).each(function(evt) {
					var imei = jQuery(this).val();
					eval("map_pointer.imei_"+imei+".infoWindow.close();");
				});
				eval("map_pointer.imei_"+imei+".infoWindow.open(map);");
			});
			
		// END getJSON
		});
	});
}