    <!-- Columns -->
		<script type="text/javascript">
			var map, map1, map2, map3;
			var cur_map;
			var geocoder = new google.maps.Geocoder();
			var coordInfoWindow = new google.maps.InfoWindow({content: "Tan Binh, HCMC"});
			//var pointMarker;
			var image = '/assets/images/icons/icon_car.png';
			
			var mapOptions = {
				zoom: <? echo GPS_Controller::ZOOM_DEFAULT; ?>,
				streetViewControl: false,
				panControl: false,
				zoomControlOptions: { position: google.maps.ControlPosition.TOP_LEFT, style: google.maps.ZoomControlStyle.SMALL },
				mapTypeId: google.maps.MapTypeId.ROADMAP
			};
			
			var defaultPoint = new google.maps.LatLng(<? echo GPS_Controller::LAT_DEFAULT; ?>, <? echo GPS_Controller::LNG_DEFAULT; ?>);
			
			var map_pointer = {
			<? foreach ($device_groups as $key=>$group) : ?>
				<? foreach ($group as $device) : ?>
				<? $icon = ($device['avatar'])? '/assets/images/icons/'.$device['avatar'] : '/assets/images/icons/icon_car.png' ; ?>
				"imei_<?= $device['imei']; ?>" : {"marker" : new google.maps.Marker({icon: "<?= $icon; ?>"}), "infoWindow" : new google.maps.InfoWindow({content: ""}), "default" : <? echo ($device['default'])? $device['default'] : 0; ?> },
				<? endforeach; ?>
			<? endforeach; ?>
			};

			var selNodes = null;

			var treeData = [
							<? $i=0;foreach ($device_groups as $key=>$group) : ?>
								{title: "<?= $key . " (".count($group).")"; ?>", isFolder: true, key: "group_<?=$i;?>", <?= ($i==0)? 'expand: true,' : ''; ?>
									children: [
										<? foreach ($group as $device) : ?>
										{title: "<?= $device['number_plate']; ?>", icon: "web-car-node.png", key: "imei_<?= $device['imei']; ?>" },
										<? endforeach; ?>
									]
								},
							<? $i++;endforeach; ?>
							];
		</script> 
		<script type="text/javascript">
			var outerLayout, middleLayout, innerLayout_Center, innerLayout_South;
			var follow_id = 0;
			
			$(document).ready(function () {
				middleLayout = $('div.ui-layout-center').layout({
					name: "middle", 
					center__paneSelector: ".middle-center",
					south__paneSelector: ".middle-south",
					south__size: "50%",
					south__initClosed: true,
					spacing_open: default_spacing_open,	// ALL panes
					spacing_closed: default_spacing_closed, // ALL panes
					south__onopen_end:	function () {
						if ($('#map_canvas2').length > 0 && map2 == undefined) {
							map2 = new google.maps.Map(document.getElementById("map_canvas2"), mapOptions); 
							map2.setCenter(defaultPoint);
						}
						google.maps.event.trigger(document.getElementById("map_canvas1"), "resize");
						google.maps.event.trigger(document.getElementById("map_canvas"), "resize");
					},
					south__onclose_end:	function () {
						if ($('#map_canvas2').length > 0 && map2 != undefined) {
							//map2 = null;
						}
						google.maps.event.trigger(document.getElementById("map_canvas1"), "resize");
						google.maps.event.trigger(document.getElementById("map_canvas"), "resize");
					}
				});
				
				innerLayout_Center = $('div.middle-center').layout({
					name: "innerCenter",
					center__paneSelector: ".north-center",
					east__paneSelector: ".north-east",
					east__size: "50%",
					east__initClosed: true,
					spacing_open: default_spacing_open,	// ALL panes
					spacing_closed: default_spacing_closed,	// ALL panes
					east__spacing_closed:	default_spacing_closed,
					east__onopen_end:	function () {
						if ($('#map_canvas1').length > 0 && map1 == undefined) {
							map1 = new google.maps.Map(document.getElementById("map_canvas1"), mapOptions); 
							map1.setCenter(defaultPoint);
						}
						google.maps.event.trigger(document.getElementById("map_canvas"), "resize");
					},
					east__onclose_end:	function () {
						if ($('#map_canvas1').length > 0 && map1 != undefined) {
							//map1 = null;
						}
						google.maps.event.trigger(document.getElementById("map_canvas"), "resize");
					}
				});

				innerLayout_South = $('div.middle-south').layout({
					name: "innerSouth",
					center__paneSelector: ".south-center",
					east__paneSelector: ".south-east",
					east__size: "50%",
					east__initClosed: true,
					spacing_open: default_spacing_open,   // ALL panes
					spacing_closed:	default_spacing_closed, // ALL panes
					east__spacing_closed:	default_spacing_closed,
					east__onopen_end:	function () {
						if ($('#map_canvas3').length > 0 && map3 == undefined) {
							map3 = new google.maps.Map(document.getElementById("map_canvas3"), mapOptions); 
							map3.setCenter(defaultPoint);
						}
						google.maps.event.trigger(document.getElementById("map_canvas2"), "resize");
						google.maps.event.trigger(document.getElementById("map_canvas1"), "resize");
						google.maps.event.trigger(document.getElementById("map_canvas"), "resize");
					},
					east__onclose_end:	function () {
						if ($('#map_canvas3').length > 0 && map3 != undefined) {
							//map3 = null;
						}
						google.maps.event.trigger(document.getElementById("map_canvas2"), "resize");
						google.maps.event.trigger(document.getElementById("map_canvas1"), "resize");
						google.maps.event.trigger(document.getElementById("map_canvas"), "resize");
					}
				});
				
				map = new google.maps.Map(document.getElementById("map_canvas"), mapOptions); 
				map.setCenter(defaultPoint);
				
				$("#aside-01-tree3").dynatree({
					checkbox: true,
					selectMode: 3,
					children: treeData,
					onSelect: function(select, node) {
						$('#map_status').show();
						// Get a list of all selected nodes, and convert to a key array:
						selNodes = $.map(node.tree.getSelectedNodes(), function(node){
							return node.data.key;
						});
						
						var imei = node.data.key.split("_");
						if (imei[0] == "group") {
							$('#map_status').hide();
							return true;
						}

						imei = imei[1];
						if (!node.bSelected) {
							eval("map_pointer.imei_"+imei+".marker.setVisible(false)");
							$('#map_status').hide();
							return true;
						}

						//var current_map = $('input:radio[name=screen-radio]:checked').val();
						if (cur_map != null) {
							display_location_of_imei(imei, cur_map);
							cur_map = null;
						} else {
							display_location_of_imei(imei, map);
						}
						/*
						$("#echoSelection3").text(selKeys.join(", "));

						// Get a list of all selected TOP nodes
						var selRootNodes = node.tree.getSelectedNodes(true);
						// ... and convert to a key array:
						var selRootKeys = $.map(selRootNodes, function(node){
							return node.data.key;
						});
						$("#echoSelectionRootKeys3").text(selRootKeys.join(", "));
						$("#echoSelectionRoots3").text(selRootNodes.join(", "));
						*/
					},
					onClick: function(node, event) {
						//node.toggleSelect();
					},
					onDblClick: function(node, event) {
						node.toggleSelect();
					},
					onKeydown: function(node, event) {
						if( event.which == 32 ) {
							node.toggleSelect();
							return false;
						}
					},
					dnd: {
						onDragStart: function(node) {
							/** This function MUST be defined to enable dragging for the tree.
							 *  Return false to cancel dragging of node.
							 */
							//logMsg("tree.onDragStart(%o)", node);
							if(node.data.isFolder || node.bSelected)
								return false;
							return true;
						},
						onDragStop: function(node) {
							//logMsg("tree.onDragStop(%o)", node);
						}
					},
					// The following options are only required, if we have more than one tree on one page:
					//initId: "treeData",
					cookieId: "dynatree-Cb3",
					idPrefix: "dynatree-Cb3-"
				});
				
				$( "#tabs" ).tabs({ disabled: [1] });
				$( "#location-radio-div" ).buttonset();
				$( "#screen-radio-div" ).buttonset();
				tabs_scrollable();

				$( "#location-radio1" ).click(function(evt) {
					evt.preventDefault();
					if (follow_id > 0) {
						clearInterval( follow_id );
						follow_id = 0; 
					}
				});

				$( "#location-radio2" ).click(function(evt) {
					evt.preventDefault();
					if (follow_id > 0) {
						return;
					}

					follow_id = setInterval("follow_devices()", 10*1000);
				});

				$( "#map_canvas" ).droppable({
					drop: function( event, ui ) {
						var source = ui.helper.data("dtSourceNode") || ui.draggable;
						cur_map = map;
						source.toggleSelect();
					}
				});
				
				$( "#map_canvas1" ).droppable({
					drop: function( event, ui ) {
						var source = ui.helper.data("dtSourceNode") || ui.draggable;
						cur_map = map1;
						source.toggleSelect();
					}
				});

				$( "#map_canvas2" ).droppable({
					drop: function( event, ui ) {
						var source = ui.helper.data("dtSourceNode") || ui.draggable;
						cur_map = map2;
						source.toggleSelect();
					}
				});

				$( "#map_canvas3" ).droppable({
					drop: function( event, ui ) {
						var source = ui.helper.data("dtSourceNode") || ui.draggable;
						cur_map = map3;
						source.toggleSelect();
					}
				});
			});

			function follow_devices() {
				var selNodes = $("#aside-01-tree3").dynatree("getTree").getSelectedNodes(false);
				var selKeys = $.map(selNodes, function(node){
					return node.data.key;
				});
				if (selKeys.length == 0) {
					return; 
				}

				var imei_list = [];
				
				for (var i=0; i<selKeys.length; i++) {
					var imei = selKeys[i].split("_");
					if (imei[0] == "group")
						continue;
					display_location_of_imei(imei[1], "NONE");
				}
			}
		</script>
    <div id="cols" class="box">
        <!-- Content -->
        <div id="content">
				<div id="container">
					<div id="container-content" class="pane ui-layout-center">
						<div id="" class="middle-center"><!-- Inner-North Layout Container -->
							<div class="north-center border"> <?/*North Center <br /><br />(Inner-Center Layout - Center Pane) */?>
								<div id="map_status" class="map-status-loading"><img width="64" height="15" src="/assets/images/icons/ajax-loader.gif"></img></div>
								<div id="map_canvas" class=""></div>
							</div>
							<div class="north-east border"> <?/*North East     <br /><br />(Inner-Center Layout - East Pane) */?>
								<div id="map_status1" class="map-status-loading"><img width="64" height="15" src="/assets/images/icons/ajax-loader.gif"></img></div>
								<div id="map_canvas1"></div>
							</div>
						</div>

						<div class="middle-south"><!-- Inner-South Layout Container -->
							<div class="south-center border"><?/*South Center <br /><br />(Inner-South Layout - Center Pane)*/?>
								<div id="map_status2" class="map-status-loading"><img width="64" height="15" src="/assets/images/icons/ajax-loader.gif"></img></div>
								<div id="map_canvas2"></div>
							</div>
							<div class="south-east border"><?/*South East     <br /><br />(Inner-South Layout - East Pane)*/?>
								<div id="map_status3" class="map-status-loading"><img width="64" height="15" src="/assets/images/icons/ajax-loader.gif"></img></div>
								<div id="map_canvas3"></div>
							</div>
						</div>
					</div> <!-- /container-content -->
					<div class="pane ui-layout-west">
						<div id="tabs">
							<ul class="tabs-ul">
								<li><a href="#tabs-1">Danh sách xe</a></li>
								<li><a href="#tabs-2">Thiết lập</a></li>
							</ul>
							<div id="tabs-1">
								<div id="aside-01-tree3" ></div>
							</div>
							<div id="tabs-2">
								<div id="tabs-2-content"> 
								<p>Morbi tincidunt, Proin elit arcu, rutrum commodo, vehicula tempus, commodo a, risus. Curabitur nec arcu. Donec sollicitudin mi sit amet mauris. Nam elementum quam ullamcorper ante. Etiam aliquet massa et lorem. Mauris dapibus lacus auctor risus. Aenean tempor ullamcorper leo. Vivamus sed magna quis ligula eleifend adipiscing. Duis orci. Aliquam sodales tortor vitae ipsum. Aliquam nulla. Duis aliquam molestie erat. Ut et mauris vel pede varius sollicitudin. Sed ut dolor nec orci tincidunt interdum. Phasellus ipsum. Nunc tristique tempus lectus. Proin elit arcu, rutrum commodo, vehicula tempus, commodo a, risus. Curabitur nec arcu. Donec sollicitudin mi sit amet mauris. Nam elementum quam ullamcorper ante. Etiam aliquet massa et lorem. Mauris dapibus lacus auctor risus. Aenean tempor ullamcorper leo. Vivamus sed magna quis ligula eleifend adipiscing. Duis orci. Aliquam sodales tortor vitae ipsum. Aliquam nulla. Duis aliquam molestie erat. Ut et mauris vel pede varius sollicitudin. Sed ut dolor nec orci tincidunt interdum. Phasellus ipsum. Nunc tristique tempus lectus.</p>
								</div>
							</div>
							<div id="tree-radio-div" class="ui-widget-content tree-footer">
							<div id="location-radio-div" class="tree-footer-content">
								<input type="radio" id="location-radio1" value="location" name="location-radio"  checked="checked" /><label for="location-radio1">Vị trí</label>
								<input type="radio" id="location-radio2" value="follow" name="location-radio"/><label for="location-radio2">Theo dõi</label>
							</div>
							<div id="screen-radio-div">
								<input type="radio" id="screen-radio1" value="map" name="screen-radio"  checked="checked" title="Bản đồ 1" onclick="javascript:open_map(0);" /><label for="screen-radio1">1</label>
								<input type="radio" id="screen-radio2" value="map1" name="screen-radio" title="Bản đồ 2" onclick="javascript:open_map(1);" /><label for="screen-radio2">2</label>
								<input type="radio" id="screen-radio3" value="map2" name="screen-radio" title="Bản đồ 3" onclick="javascript:open_map(2);" /><label for="screen-radio3">3</label>
								<input type="radio" id="screen-radio4" value="map3" name="screen-radio" title="Bản đồ 4" onclick="javascript:open_map(3);" /><label for="screen-radio4">4</label>
							</div>
							</div>
						</div>
					</div> <!-- /ui-layout-west -->
				</div>
        <hr class="noscreen" />
        </div> <!-- /content -->
    </div> <!-- /cols -->
<?/*<div id="wrap">
	<div id="wrapmain">
		Welcome GPS Tracking System

		<br/><br/><br/>
		<p>
		Changelog:<br>
		2011/1/7:<br>
			- Server demo release<br>
		2010:<br>
			- Demo version<br>
		</p>
	</div>
</div>
*/?>