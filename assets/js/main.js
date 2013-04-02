$(document).ready(function() {

	$('#main').layout({
		name: "mainlayout",
		north__paneSelector: "#header",
		center__paneSelector: "#cols",
		south__paneSelector: "#footer",
		north__resizable: false,
		south__resizable: false,
		north__slidable: false,
		south__slidable: false,
		north__togglerLength_open: 0,
		south__togglerLength_open: 0,
		center__minSize: 450,
		center__size: 450
	});
	
	$('#container').layout({
		name: "outer",
		west__size: 200,
		west__maxSize: 200,
		center__paneSelector: ".ui-layout-center",
		west__paneSelector: ".ui-layout-west",
		west__onresize_end: function () { },
		west__onopen_end:	function () { },
		center__onresize_end: function() {
			tabs_scrollable();
			if ($('#tabs-options-div').length > 0) {
				tabs_options_scrollable();
			}
			/*
			if ($('#summary-table').length > 0 && grid!=undefined) {
				grid.resizeCanvas();
			}
			*/
		}
	});
	
	$( "#dialog-login-form" ).dialog({
			autoOpen: false,
			height: 240,
			width: 300,
			modal: true,
			show: "fade",
			hide: "fade",
			resizable: false,
			buttons: {
				"Đăng nhập": function() {
					$( ".validate" ).text('').removeClass( "ui-state-highlight" );
					
					var uname = $( "#uname" ), password = $( "#password" );
					// TODO
					$.ajax({
							type: 'post',
							url: base_url + '/auth/ajax_login',
							data: "username="+uname.val()+"&password="+password.val(),
							dataType: "json",
							success: function(data) {
								if (data != undefined && data.success) {
									location.reload();
								} else if (data != undefined && !data.success && data.msg != undefined) {
									$( ".validate" ).html(data.msg).addClass( "ui-state-highlight" );
								} else {
									$( ".validate" ).html("AJAX error!").addClass( "ui-state-highlight" );
								}
							}
						});
				},
				"Đóng": function() {
					$( this ).dialog( "close" );
				}
			},
			close: function() {
				// TODO
			}
		});
	
	$( "#login-lnk" ).click(function(evt) {
			evt.preventDefault();
			$( "#dialog-login-form" ).dialog( "open" );
	});
	
	$( "#logout-lnk" ).click(function(evt) {
			evt.preventDefault();
			$.ajax({
					type: 'post',
					url: base_url + '/auth/ajax_logout',
					dataType: "json",
							success: function(data) {
								if (data != undefined && data.success) {
									location.reload();
								} else if (data != undefined && !data.success && data.msg != undefined) {
									$( ".validate" ).html(data.msg).addClass( "ui-state-highlight" );
								} else {
									$( ".validate" ).html("AJAX error!").addClass( "ui-state-highlight" );
								}
							}
			});
	});
	
	$( "#signup-lnk" ).click(function(evt) {
		evt.preventDefault();
	});
});

function tabs_scrollable() {
	$('#tabs').height($('.ui-layout-pane-west').innerHeight() - 4);
	$('#tabs-1, #tabs-2').height($('#tabs').innerHeight() - ($('#tabs .tabs-ul').innerHeight() + ( ($('#tree-radio-div .tree-footer-content').length > 0)? ( $('#tree-radio-div .tree-footer-content').innerHeight() + 10 ): 0 ) + 10 ));
}

function tabs_options_scrollable() {
	$('#tabs-options-div').height($('.ui-layout-center').innerHeight() - ($('#tabs-options-footer').innerHeight() + 2));
	$('#tabs-options-1, #tabs-options-2').height($('#tabs-options-div').innerHeight() - ($('.tabs-options-ul').innerHeight() + $('#tabs-options-footer').innerHeight() + 1 ));
}

function tabs_summary_scrollable() {
	$('#summary-tabs').height($('.ui-layout-center').innerHeight() - 4);
	$('.summary-tabs-content').height($('#summary-tabs').innerHeight() - ($('#summary-tabs .tabs-ul').innerHeight() + 10 ));	
}

function display_location_of_imei(imei, cur_map) {
	jQuery.getJSON(base_url + "/maps/data/" + imei, {}, function(data){
		
		if (data.imei.lat != undefined && data.imei.lng != undefined ) {
			var newCoord = new google.maps.LatLng(parseFloat(data.imei.lat), parseFloat(data.imei.lng));
			
			if (cur_map != "NONE") {
			eval("map_pointer.imei_"+imei+".marker.setAnimation(google.maps.Animation.DROP);");
			} else {
				if (!data.imei.connected) {
					eval("map_pointer.imei_"+imei+".marker.setAnimation(google.maps.Animation.BOUNCE);");
				}
			}
			
			eval("map_pointer.imei_"+imei+".marker.setVisible(true);");
			
			if (cur_map != "NONE") {
				eval("map_pointer.imei_"+imei+".marker.setMap(cur_map);");
			}
			
			eval("map_pointer.imei_"+imei+".marker.setPosition(newCoord);");
			
			if (cur_map != "NONE") {
				cur_map.panTo(newCoord);
			} else {
				if (data.imei.connected) {
					eval("map_pointer.imei_"+imei+".marker.getMap().panTo(newCoord);");
				}
			}
			
			var latlngStr = "";
			
			if (!data.imei.connected) {
				latlngStr = '<font style="color:red"><strong>Thiết bị không kết nối với server</strong></font><br/>';
			}  

			latlngStr += "<strong>Cập nhật:</strong> " + data.imei.created +"<br />" + 
							"<strong>Vị trí:</strong> (" + (newCoord.lat()).toFixed(5)+ ", " + (newCoord.lng()).toFixed(5) + ")<br />" +
							"<strong>Biển số:</strong> " + data.imei.number_plate +"<br />" + 
							((data.imei.raw_data)? data.imei.raw_data + "<br />" : "");
	
			eval("map_pointer.imei_"+imei+".infoWindow.setContent(latlngStr);");
			eval("map_pointer.imei_"+imei+".infoWindow.setPosition(newCoord);");
			
			get_geocode_of_coord(newCoord, eval("map_pointer.imei_"+imei+".infoWindow"));
			/*
			geocoder.geocode({'latLng': newCoord}, function(results, status) {
				if (status == google.maps.GeocoderStatus.OK) {
					if (results[0]) {
						latlngStr += "Địa chỉ: " + results[0].formatted_address;
						
						eval("map_pointer.imei_"+imei+".infoWindow.setContent(latlngStr);");
						//eval("map_pointer.imei_"+imei+".infoWindow.setPosition(newCoord);");
					}
				} else {
					//alert("Geocoder failed due to: " + status);
				}
			});
			*/
			
			google.maps.event.clearListeners(eval("map_pointer.imei_"+imei+".marker"), 'click')
			google.maps.event.addListener(eval("map_pointer.imei_"+imei+".marker"), 'click', function() {
				for (i=0; i<selNodes.length; i++) {
					var imei_tmp = selNodes[i].split("_");
					if (imei_tmp[0] == "group")
						continue;
					imei_tmp = imei_tmp[1];
					eval("map_pointer.imei_"+imei_tmp+".infoWindow.close();");
				}
				if (cur_map != "NONE") {
					eval("map_pointer.imei_"+imei+".infoWindow.open(cur_map);");
				} else {
					eval("map_pointer.imei_"+imei+".infoWindow.open(map_pointer.imei_"+imei+".marker.getMap());");
				}
			});
		}
		$('#map_status').hide();
	// END getJSON
	});
}

function display_path_of_imei(imei, cur_map) {
	clear_path();

	var fdate = jQuery('#from_date').val();
	var fhour = jQuery('#from_hour').val();
	var tdate = jQuery('#to_date').val();
	var thour = jQuery('#to_hour').val();
		
	jQuery.getJSON(base_url + "/maps/data_path/" + imei, { from_date: fdate, from_hour: fhour, to_date: tdate, to_hour: thour }, function(data){
		
		//var summaryPanel = document.getElementById("directions_panel_inner");
		//summaryPanel.innerHTML = "";
		//var routeSegment = 1;
		var distance = 0;

		if (!data.path || data.path.length < 2) {
			//summaryPanel.innerHTML = "No data return";
			open_dialog("<p style='text-align: center; color: red;'>Không có dữ liệu.</p>");
			jQuery('#map_status').hide();
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
					directionDisplay.setMap(cur_map);
					directionDisplay.setDirections(response);
					directionsDisplay.push(directionDisplay);
					var route = response.routes[0];

					// For each route, display summary information.
					//for (var i = 0; i < route.legs.length; i++) {
						//var routeSegment = i + 1;
						/*
						summaryPanel.innerHTML += "<b>Route Segment: " + routeSegment++ + "</b><br />";
						summaryPanel.innerHTML += route.legs[i].start_address + " to ";
						summaryPanel.innerHTML += route.legs[i].end_address + "<br />";
						summaryPanel.innerHTML += route.legs[i].distance.text + "<br /><br />";
						*/
						//distance += route.legs[i].distance.value;
						//$('#summary-header-title').html('Đường đi - <b>' + data.path[i].distance/*Math.round(distance/10)/100*/ + " km</b>");
						//summaryPanel.innerHTML = "<b>Distance: " + Math.round(distance/10)/100 + " km</b><br/>";
					//}
					jQuery('#map_status').delay(1000).hide();
				}
			});
			//break;
		}
			
		if (data.path.length > 0) {
			var start_pos = new google.maps.LatLng(parseFloat(data.path[0].lat), parseFloat(data.path[0].lng));
			var end_pos = new google.maps.LatLng(parseFloat(data.path[data.path.length-1].lat), parseFloat(data.path[data.path.length-1].lng));
			set_marker_with_info(map, start_pos, "marker1", "info1", "Điểm đầu<br/>");
			set_marker_with_info(map, end_pos, "marker2", "info2", "Điểm cuối<br/>");
			/*
			marker1 = new google.maps.Marker({  
				 map: cur_map,  
				 position: start_pos,  
				 title: "Start"
			});
			info1 = new google.maps.InfoWindow({content: "Đầu:<br/>"});
			get_geocode_of_coord(start_pos, info1);
			
			//google.maps.event.clearListeners(marker1, 'click')
			google.maps.event.addListener(marker1, 'click', function() {
				info1.open(cur_map);
			});
			*/
			
			/*
			marker2 = new google.maps.Marker({  
				 map: cur_map,  
				 position: end_pos,
				 title: "End"  
			});
			info2 = new google.maps.InfoWindow({content: "Cuối:<br/>"});
			get_geocode_of_coord(end_pos, info2);
			
			//google.maps.event.clearListeners(marker2, 'click')
			google.maps.event.addListener(marker2, 'click', function() {
				info2.open(cur_map);
			});
			*/
			
			map.panTo(start_pos);
			
			middleLayout.open("south");
			grid_data = [];
			for (var i=0; i<data.path.length; i++) {
				var d = (grid_data[i] = {});

				d["id"] = i;
				/*d["number_plate"] = data.path[i].number_plate;*/
				d["street"] = "(Bấm vào để hiển thị)";
				var latlng = "(" + parseFloat(data.path[i].lat).toFixed(5) + "," + parseFloat(data.path[i].lng).toFixed(5) + ")";
				d["latlng"] = latlng;
				d["date"] = data.path[i].created;
				d["speed"] = (parseFloat(data.path[i].raw_data.speed)).toFixed(2);
				d["distance"] = (data.path[i].distance).toFixed(2);
			}
			$('#summary-header-title').html('Đường đi - <b>' + Math.round(data.path[data.path.length-1].distance*100)/100/*Math.round(distance/10)/100*/ + " km</b>");
			grid.setData(grid_data, true);
			grid.render();
		}
			
	// END getJSON
	});
}

function open_map(map_id) {
	if (map_id == 0) {
		if (innerLayout_Center != undefined) {
			innerLayout_Center.close("east");
		}
		if (middleLayout != undefined) {
			middleLayout.close("south");
		}
	} else if (map_id == 1) {
		if (innerLayout_Center != undefined) {
			innerLayout_Center.open("east");
		}
		if (middleLayout != undefined) {
			middleLayout.close("south");
		}
	} else if (map_id == 2) {
		if (innerLayout_Center != undefined) {
			innerLayout_Center.open("east");
		}
		if (middleLayout != undefined) {
			middleLayout.open("south");
		}
		if (innerLayout_South != undefined) {
			innerLayout_South.close("east");
		}
	} else if (map_id == 3) {
		if (innerLayout_Center != undefined) {
			innerLayout_Center.open("east");
		}
		if (middleLayout != undefined) {
			middleLayout.open("south");
		}
		if (innerLayout_South != undefined) {
			innerLayout_South.open("east");
		}
	}
}

function open_dialog(msg) {
	$("#dialog-mesage").html(msg);
	$("#dialog-mesage").dialog({
			autoOpen: true,
			height: 160,
			width: 300,
			modal: true,
			show: "fade",
			hide: "fade",
			resizable: false,
			title: "Lỗi",
			buttons: {
				"Đóng": function() {
					$( this ).dialog( "close" );
				}
			}
	});
}

function get_geocode_of_coord(coord, infoWindow) {
	geocoder.geocode({'latLng': coord}, function(results, status) {
	if (status == google.maps.GeocoderStatus.OK) {
		if (results[0]) {
			var content = infoWindow.getContent();
			content += "Địa chỉ: " + results[0].formatted_address;
			infoWindow.setPosition(coord);
			infoWindow.setContent(content);
		}
	} else {
		//alert("Geocoder failed due to: " + status);
	}
	});
}

function set_marker_with_info(map, coord, marker, info, content) {
	window[marker] = new google.maps.Marker({  
		 map: map,  
		 position: coord,
		 title: stripHTML(content)
	});
	window[info] = new google.maps.InfoWindow({content: content});
	get_geocode_of_coord(coord, window[info]);
	
	//google.maps.event.clearListeners(marker, 'click')
	google.maps.event.addListener(window[marker], 'click', function() {
		window[info].open(map);
	});
}

function stripHTML(oldString) {
  return oldString.replace(/<\/?[^>]+>/gi, '');
}

function display_summary_of_imei_list(imei_list, grid) {
	var fdate = jQuery('#from_date').val();
	var fhour = jQuery('#from_hour').val();
	var tdate = jQuery('#to_date').val();
	var thour = jQuery('#to_hour').val();
		
	jQuery.getJSON(base_url + "/maps/data_summary", { imei_list : imei_list, from_date: fdate, from_hour: fhour, to_date: tdate, to_hour: thour }, function(data){
		if (!data.summary) {
			//summaryPanel.innerHTML = "No data return";
			open_dialog("<p style='text-align: center; color: red;'>Không có dữ liệu.</p>");
			jQuery('#map_status').hide();
			return;
		}

		grid_data = [];
		for (var i=0; i<data.summary.length; i++) {
			var d = (grid_data[i] = {});

			d["id"] = i;
			d["number_plate"] = data.summary[i].number_plate;
			d["street"] = "(Bấm vào để hiển thị)";
			var latlng = "(" + parseFloat(data.summary[i].lat).toFixed(5) + "," + parseFloat(data.summary[i].lng).toFixed(5) + ")";
			d["latlng"] = latlng;
			d["date"] = data.summary[i].created;
			d["speed"] = (parseFloat(data.summary[i].raw_data.speed)).toFixed(2);
			d["distance"] = data.summary[i].distance;
		}
		grid.setData(grid_data, true);
		grid.render();
		
	// END getJSON
	}); 
}

function display_summary(selKeys, grid) {
	var imei_list = [];
	
	for (var i=0; i<selKeys.length; i++) {
		var imei = selKeys[i].split("_");
		if (imei[0] == "group")
			continue;
		imei_list.push(imei[1]);
	}
	imei_list = imei_list.join("|");
	display_summary_of_imei_list(imei_list, grid);
}

function display_line_of_imei(imei, cur_map) {
	clear_path();

	var fdate = jQuery('#from_date').val();
	var fhour = jQuery('#from_hour').val();
	var tdate = jQuery('#to_date').val();
	var thour = jQuery('#to_hour').val();
		
	jQuery.getJSON(base_url + "/maps/data_path/" + imei, { from_date: fdate, from_hour: fhour, to_date: tdate, to_hour: thour }, function(data){
		var distance = 0;

		if (!data.path || data.path.length < 2) {
			//summaryPanel.innerHTML = "No data return";
			open_dialog("<p style='text-align: center; color: red;'>Không có dữ liệu.</p>");
			jQuery('#map_status').hide();
			return;
		}

		var start = null, end = null, waypts = [];

		start = new google.maps.LatLng(parseFloat(data.path[0].lat),parseFloat(data.path[0].lng));
		for (var i=0; i<data.path.length; i++) {
			waypts.push(new google.maps.LatLng(parseFloat(data.path[i].lat),parseFloat(data.path[i].lng)));
		}
		end = new google.maps.LatLng(parseFloat(data.path[i-1].lat),parseFloat(data.path[i-1].lng));
		
		path_line = new google.maps.Polyline({
					strokeColor: '#ff0000',
					strokeOpacity: 1.0,
					strokeWeight: 4,
					path: waypts
					});

		path_line.setMap(cur_map);
		if (data.path.length > 0) {
			var start_pos = new google.maps.LatLng(parseFloat(data.path[0].lat), parseFloat(data.path[0].lng));
			var end_pos = new google.maps.LatLng(parseFloat(data.path[data.path.length-1].lat), parseFloat(data.path[data.path.length-1].lng));
			set_marker_with_info(cur_map, start_pos, "marker1", "info1", "Điểm đầu<br/>");
			set_marker_with_info(cur_map, end_pos, "marker2", "info2", "Điểm cuối<br/>");
			
			cur_map.panTo(start_pos);
		}
		var distance = google.maps.geometry.spherical.computeLength(waypts);
		
		middleLayout.open("south");
		grid_data = [];
		for (var i=0; i<data.path.length; i++) {
			var d = (grid_data[i] = {});

			d["id"] = i;
			/*d["number_plate"] = data.path[i].number_plate;*/
			d["street"] = "(Bấm vào để hiển thị)";
			var latlng = "(" + parseFloat(data.path[i].lat).toFixed(5) + "," + parseFloat(data.path[i].lng).toFixed(5) + ")";
			d["latlng"] = latlng;
			d["date"] = data.path[i].created;
			d["speed"] = (parseFloat(data.path[i].raw_data.speed)).toFixed(2);
			d["distance"] = (data.path[i].distance).toFixed(2);
		}
		grid.setData(grid_data, true);
		grid.render();
		
		$('#summary-header-title').html('Đường đi - <b>' + Math.round(data.path[data.path.length-1].distance*100)/100/*Math.round(distance/1000)*/ + " km</b>");
		jQuery('#map_status').delay(1000).hide();
		// END getJSON
	});
}

function clear_path() {
	if (directionsDisplay.length > 0) {
		for (var i=0; i<directionsDisplay.length;i++) {
			directionsDisplay[i].setMap(null);
		}
		directionsDisplay = null;
		directionsDisplay = [];
	}
	
	if (marker1 != undefined) {
		marker1.setVisible(false);
		marker1 = null;
		info1 = null;
	}
	
	if (marker2 != undefined) {
		marker2.setVisible(false);
		marker1 = null;
		info2 = null;
	}
	
	if (path_line != undefined) {
		path_line.setMap(null);
		path_line = null;
	}
}

function export_path_tsv() {
	
	var selNodes = $("#aside-01-tree3").dynatree("getTree").getSelectedNodes(false);
	var selKeys = $.map(selNodes, function(node){
		return node.data.key;
	});
	if (selKeys.length == 0) {
		open_dialog("<p style='text-align: center; color: red;'><span class='ui-icon ui-icon-alert' style='float:left; margin:2px 5px 0 0;'></span>Hãy chọn 1 thiết bị.</p>");
		return; 
	}
	selKeys = selKeys[0];
	
	var imei = selKeys.split("_");
	if (imei[0] == "group")
		return true;
	imei = imei[1];
	
	$( "#form_search input[name=imei]" ).val(imei);
	$( '#form_search' ).attr("action", base_url + "/maps/data_path_export");
	$( '#form_search' ).submit();
}

function edit_device(device_id) {
	var url = base_url + "/gps/options?act=edit&device_id=" + device_id;
	window.location = url;
}

function delete_device(device_id) {
	var url = base_url + "/gps/options?act=delete&device_id=" + device_id;
	window.location = url;
}

function display_summary1(selKeys, grid) {
	var imei_list = [];
	
	for (var i=0; i<selKeys.length; i++) {
		var imei = selKeys[i].split("_");
		if (imei[0] == "group")
			continue;
		imei_list.push(imei[1]);
	}
	imei_list = imei_list.join("|");
	display_summary_of_imei_list_1(imei_list, grid);
}

function display_summary_of_imei_list_1(imei_list, grid) {
	var fdate = jQuery('#from_date1').val();
	var fhour = jQuery('#from_hour1').val();
	var tdate = jQuery('#to_date1').val();
	var thour = jQuery('#to_hour1').val();
		
	jQuery.getJSON(base_url + "/maps/data_summary_1", { imei_list : imei_list, from_date: fdate, from_hour: fhour, to_date: tdate, to_hour: thour }, function(data){
		if (!data.summary) {
			//summaryPanel.innerHTML = "No data return";
			open_dialog("<p style='text-align: center; color: red;'>Không có dữ liệu.</p>");
			jQuery('#map_status').hide();
			return;
		}
		
		$('#summary1-table > tbody').html('');

		for (var i=0; i<data.summary.length; i++) {
			var row = '<td>' + data.summary[i].number_plate + '</td>';
			row += '<td>' + data.summary[i].date + '</td>';
			row += '<td>' + (parseFloat(data.summary[i].distance)).toFixed(3) + '</td>';
			row += '<td>' + (data.summary[i].stop_time/60) + '</td>';
			row += '<td>' + (data.summary[i].run_time/60) + '</td>';
			row += '<td>' + ( ((parseFloat(data.summary[i].max_speed)).toFixed(2) - parseFloat(data.summary[i].over_speed).toFixed(2) > 0)? '<img src=\'/assets/images/slick/tick.png\'/>' : '&nbsp;') + '</td>';
			row += '<td>' + (parseFloat(data.summary[i].avg_speed)).toFixed(2) + '</td>';
			row += '<td>' + (parseFloat(data.summary[i].max_speed)).toFixed(2) + '</td>';
			$('#summary1-table > tbody:last').append('<tr>'+row+'</tr>');
		}
		/*
		grid_data = [];
		for (var i=0; i<data.summary.length; i++) {
			var d = (grid_data[i] = {});

			d["id"] = i;
			d["number_plate"] = data.summary[i].number_plate;
			d["street"] = "(Bấm vào để hiển thị)";
			var latlng = "(" + parseFloat(data.summary[i].lat).toFixed(5) + "," + parseFloat(data.summary[i].lng).toFixed(5) + ")";
			d["latlng"] = latlng;
			d["date"] = data.summary[i].created;
			d["speed"] = (parseFloat(data.summary[i].raw_data.speed)).toFixed(2);
			d["distance"] = data.summary[i].distance;
		}
		grid.setData(grid_data, true);
		grid.render();
		*/
		
	// END getJSON
	}); 
}

function display_summary_distance(selKeys, grid) {
	var imei_list = [];
	
	for (var i=0; i<selKeys.length; i++) {
		var imei = selKeys[i].split("_");
		if (imei[0] == "group")
			continue;
		imei_list.push(imei[1]);
	}
	imei_list = imei_list.join("|");
	display_summary_of_imei_list_distance(imei_list, grid);
}

function display_summary_of_imei_list_distance(imei_list, grid) {
	var fdate = jQuery('#from_date2').val();
	var fhour = jQuery('#from_hour2').val();
	var tdate = jQuery('#to_date2').val();
	var thour = jQuery('#to_hour2').val();
		
	jQuery.getJSON(base_url + "/maps/data_summary_distance", { imei_list : imei_list, from_date: fdate, from_hour: fhour, to_date: tdate, to_hour: thour }, function(data){
		if (!data.summary) {
			//summaryPanel.innerHTML = "No data return";
			open_dialog("<p style='text-align: center; color: red;'>Không có dữ liệu.</p>");
			jQuery('#map_status').hide();
			return;
		}
		
		$('#summary2-table > tbody').html('');

		for (var i=0; i<data.summary.length; i++) {
			var row = '<td>' + data.summary[i].number_plate + '</td>';
			row += '<td>' + data.summary[i].date + '</td>';
			row += '<td>' + (parseFloat(data.summary[i].distance)).toFixed(3) + '</td>';
			row += '<td>' + (parseFloat(data.summary[i].avg_speed)).toFixed(2) + '</td>';
			row += '<td>' + (data.summary[i].run_time/60) + '</td>';
			row += '<td>' + '0' + '</td>';
			row += '<td>' + '0' + '</td>';
			row += '<td>' + '0' + '</td>';
			/*
			row += '<td>' + (data.summary[i].stop_time/60) + ' p </td>';
			row += '<td>' + (data.summary[i].run_time/60) + ' p </td>';
			row += '<td>' + ( ((parseFloat(data.summary[i].max_speed)).toFixed(2) - parseFloat(data.summary[i].over_speed).toFixed(2) > 0)? '<img src=\'/assets/images/slick/tick.png\'/>' : '&nbsp;') + '</td>';
			row += '<td>' + (parseFloat(data.summary[i].avg_speed)).toFixed(2) + '</td>';
			row += '<td>' + (parseFloat(data.summary[i].max_speed)).toFixed(2) + '</td>';
			*/
			$('#summary2-table > tbody:last').append('<tr>'+row+'</tr>');
		}
		/*
		grid_data = [];
		for (var i=0; i<data.summary.length; i++) {
			var d = (grid_data[i] = {});

			d["id"] = i;
			d["number_plate"] = data.summary[i].number_plate;
			d["street"] = "(Bấm vào để hiển thị)";
			var latlng = "(" + parseFloat(data.summary[i].lat).toFixed(5) + "," + parseFloat(data.summary[i].lng).toFixed(5) + ")";
			d["latlng"] = latlng;
			d["date"] = data.summary[i].created;
			d["speed"] = (parseFloat(data.summary[i].raw_data.speed)).toFixed(2);
			d["distance"] = data.summary[i].distance;
		}
		grid.setData(grid_data, true);
		grid.render();
		*/
		
	// END getJSON
	}); 
}

function display_summary_usage(selKeys, grid) {
	var imei_list = [];
	
	for (var i=0; i<selKeys.length; i++) {
		var imei = selKeys[i].split("_");
		if (imei[0] == "group")
			continue;
		imei_list.push(imei[1]);
	}
	imei_list = imei_list.join("|");
	display_summary_of_imei_list_usage(imei_list, grid);
}

function display_summary_of_imei_list_usage(imei_list, grid) {
	var fdate = jQuery('#from_date3').val();
	var fhour = jQuery('#from_hour3').val();
	var tdate = jQuery('#to_date3').val();
	var thour = jQuery('#to_hour3').val();
		
	jQuery.getJSON(base_url + "/maps/data_summary_usage", { imei_list : imei_list, from_date: fdate, from_hour: fhour, to_date: tdate, to_hour: thour }, function(data){
		if (!data.summary) {
			//summaryPanel.innerHTML = "No data return";
			open_dialog("<p style='text-align: center; color: red;'>Không có dữ liệu.</p>");
			jQuery('#map_status').hide();
			return;
		}
		
		$('#summary3-table > tbody').html('');

		for (var i=0; i<data.summary.length; i++) {
			var row = '<td>' + data.summary[i].number_plate + '</td>';
			row += '<td>' + data.summary[i].date + '</td>';
			row += '<td>' + (parseFloat(data.summary[i].distance)).toFixed(3) + '</td>';
			var start_latlng = "(" + parseFloat(data.summary[i].start_lat).toFixed(5) + "," + parseFloat(data.summary[i].start_lng).toFixed(5) + ")";
			row += '<td>' + start_latlng + '</td>';
			row += '<td>' + '&nbsp;' + '</td>';
			var end_latlng = "(" + parseFloat(data.summary[i].end_lat).toFixed(5) + "," + parseFloat(data.summary[i].end_lng).toFixed(5) + ")";
			row += '<td>' + end_latlng + '</td>';
			row += '<td>' + '&nbsp;' + '</td>';
			
			/*
			row += '<td>' + (parseFloat(data.summary[i].avg_speed)).toFixed(2) + '</td>';
			row += '<td>' + (data.summary[i].run_time/60) + ' p </td>';
			row += '<td>' + '0' + '</td>';
			row += '<td>' + '0' + '</td>';
			row += '<td>' + '0' + '</td>';
			row += '<td>' + (data.summary[i].stop_time/60) + ' p </td>';
			row += '<td>' + (data.summary[i].run_time/60) + ' p </td>';
			row += '<td>' + ( ((parseFloat(data.summary[i].max_speed)).toFixed(2) - parseFloat(data.summary[i].over_speed).toFixed(2) > 0)? '<img src=\'/assets/images/slick/tick.png\'/>' : '&nbsp;') + '</td>';
			row += '<td>' + (parseFloat(data.summary[i].avg_speed)).toFixed(2) + '</td>';
			row += '<td>' + (parseFloat(data.summary[i].max_speed)).toFixed(2) + '</td>';
			*/
			$('#summary3-table > tbody:last').append('<tr>'+row+'</tr>');
		}
		/*
		grid_data = [];
		for (var i=0; i<data.summary.length; i++) {
			var d = (grid_data[i] = {});

			d["id"] = i;
			d["number_plate"] = data.summary[i].number_plate;
			d["street"] = "(Bấm vào để hiển thị)";
			var latlng = "(" + parseFloat(data.summary[i].lat).toFixed(5) + "," + parseFloat(data.summary[i].lng).toFixed(5) + ")";
			d["latlng"] = latlng;
			d["date"] = data.summary[i].created;
			d["speed"] = (parseFloat(data.summary[i].raw_data.speed)).toFixed(2);
			d["distance"] = data.summary[i].distance;
		}
		grid.setData(grid_data, true);
		grid.render();
		*/
		
	// END getJSON
	}); 
}