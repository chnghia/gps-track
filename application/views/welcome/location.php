    <!-- Columns -->
		<script type="text/javascript">
			var map, map1, map2, map3;
			//var geocoder;
			var coordInfoWindow = new google.maps.InfoWindow({content: "Tan Binh, HCMC"});
			//var pointMarker;
			var image = '/assets/images/icons/auto_icon.gif';
			
			var mapOptions = {
				zoom: <? echo GPS_Controller::ZOOM_DEFAULT; ?>,
				streetViewControl: false,
				panControl: false,
				zoomControlOptions: { position: google.maps.ControlPosition.TOP_LEFT, style: google.maps.ZoomControlStyle.SMALL },
				mapTypeId: google.maps.MapTypeId.ROADMAP
			};

			var map_pointer = {
			<? $i=0;foreach ($device_list as $imei=>$val) : ?>
				 <? echo ($i > 0)? "," : ""; ?> 
				 "imei_<?echo $imei;?>" : {"marker" : new google.maps.Marker({icon: image}), "infoWindow" : new google.maps.InfoWindow({content: ""}), "default" : <? echo $val['default']; ?> }
			<? $i++;endforeach; ?>
			};
			
			var defaultPoint = new google.maps.LatLng(<? echo GPS_Controller::LAT_DEFAULT; ?>, <? echo GPS_Controller::LNG_DEFAULT; ?>);
			
			var treeData = [
					{title: "item1 with key and tooltip", tooltip: "Look, a tool tip!" },
					{title: "item2: selected on init", select: true },
					{title: "Folder", isFolder: true, key: "id3",
						children: [
							{title: "Sub-item 3.1",
								children: [
									{title: "Sub-item 3.1.1", key: "id3.1.1" },
									{title: "Sub-item 3.1.2", key: "id3.1.2" }
								]
							},
							{title: "Sub-item 3.2",
								children: [
									{title: "Sub-item 3.2.1", key: "id3.2.1" },
									{title: "Sub-item 3.2.2", key: "id3.2.2" }
								]
							},
							{title: "Sub-item 3.3",
								children: [
									{title: "Sub-item 3.3.1", key: "id3.3.1" },
									{title: "Sub-item 3.3.2", key: "id3.3.2" }
								]
							},
							{title: "Sub-item 3.4",
								children: [
									{title: "Sub-item 3.4.1", key: "id3.4.1" },
									{title: "Sub-item 3.4.2", key: "id3.4.2" }
								]
							},
							{title: "Sub-item 3.5",
								children: [
									{title: "Sub-item 3.5.1", key: "id3.5.1" },
									{title: "Sub-item 3.5.2", key: "id3.5.2" }
								]
							},
							{title: "Sub-item 3.6",
								children: [
									{title: "Sub-item 3.4.1", key: "id3.4.1" },
									{title: "Sub-item 3.4.2", key: "id3.4.2" }
								]
							},
							{title: "Sub-item 3.7",
								children: [
									{title: "Sub-item 3.5.1", key: "id3.5.1" },
									{title: "Sub-item 3.5.2", key: "id3.5.2" }
								]
							}
							,
							{title: "Sub-item 3.8",
								children: [
									{title: "Sub-item 3.5.1", key: "id3.5.1" },
									{title: "Sub-item 3.5.2", key: "id3.5.2" }
								]
							}
						]
					},
					{title: "Document with some children (expanded on init)", key: "id4", expand: true,
						children: [
							{title: "Sub-item 4.1 (active on init)", activate: true,
								children: [
									{title: "Sub-item 4.1.1", key: "id4.1.1" },
									{title: "Sub-item 4.1.2", key: "id4.1.2" }
								]
							},
							{title: "Sub-item 4.2 (selected on init)", select: true,
								children: [
									{title: "Sub-item 4.2.1", key: "id4.2.1" },
									{title: "Sub-item 4.2.2", key: "id4.2.2" }
								]
							},
							{title: "Sub-item 4.3 (hideCheckbox)", hideCheckbox: true },
							{title: "Sub-item 4.4 (unselectable)", unselectable: true }
						]
					}
				];
		</script> 
		<script type="text/javascript">
			var outerLayout, middleLayout, innerLayout_Center, innerLayout_South;
			
			$(document).ready(function () {
				middleLayout = $('div.ui-layout-center').layout({
					name: "middle", 
					center__paneSelector: ".middle-center",
					south__paneSelector: ".middle-south",
					south__size: "50%",
					south__initClosed: true,
					spacing_open: default_spacing_open,	// ALL panes
					spacing_closed: default_spacing_closed // ALL panes
				});
				
				innerLayout_Center = $('div.middle-center').layout({
					name: "innerCenter",
					center__paneSelector: ".north-center",
					east__paneSelector: ".north-east",
					east__size: "50%",
					east__initClosed: true,
					spacing_open: default_spacing_open,	// ALL panes
					spacing_closed: default_spacing_closed,	// ALL panes
					east__spacing_closed:	default_spacing_closed
				});

				innerLayout_South = $('div.middle-south').layout({
					name: "innerSouth",
					center__paneSelector: ".south-center",
					east__paneSelector: ".south-east",
					east__size: "50%",
					east__initClosed: true,
					spacing_open: default_spacing_open,   // ALL panes
					spacing_closed:	default_spacing_closed, // ALL panes
					east__spacing_closed:	default_spacing_closed
				});
				
				map = new google.maps.Map(document.getElementById("map_canvas"), mapOptions); 
				map.setCenter(defaultPoint);
				
				map1 = new google.maps.Map(document.getElementById("map_canvas1"), mapOptions); 
				map1.setCenter(defaultPoint);
				
				map2 = new google.maps.Map(document.getElementById("map_canvas2"), mapOptions); 
				map2.setCenter(defaultPoint);
				
				map3 = new google.maps.Map(document.getElementById("map_canvas3"), mapOptions); 
				map3.setCenter(defaultPoint);
				
				$("#aside-01-tree3").dynatree({
					checkbox: true,
					selectMode: 3,
					children: treeData,
					onSelect: function(select, node) {
						// Get a list of all selected nodes, and convert to a key array:
						var selKeys = $.map(node.tree.getSelectedNodes(), function(node){
							return node.data.key;
						});
						$("#echoSelection3").text(selKeys.join(", "));

						// Get a list of all selected TOP nodes
						var selRootNodes = node.tree.getSelectedNodes(true);
						// ... and convert to a key array:
						var selRootKeys = $.map(selRootNodes, function(node){
							return node.data.key;
						});
						$("#echoSelectionRootKeys3").text(selRootKeys.join(", "));
						$("#echoSelectionRoots3").text(selRootNodes.join(", "));
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
					// The following options are only required, if we have more than one tree on one page:
					//initId: "treeData",
					cookieId: "dynatree-Cb3",
					idPrefix: "dynatree-Cb3-"
				});
				
				$( "#tabs" ).tabs({ disabled: [1] });
				
				tabs_scrollable();
			});
		</script>
    <div id="cols" class="box">
        <!-- Content -->
        <div id="content">
				<div id="container">
					<div id="container-content" class="pane ui-layout-center">
						<div id="" class="middle-center"><!-- Inner-North Layout Container -->
							<div class="north-center border"> <?/*North Center <br /><br />(Inner-Center Layout - Center Pane) */?>
								<div id="map_canvas"></div>
							</div>
							<div class="north-east border"> <?/*North East     <br /><br />(Inner-Center Layout - East Pane) */?>
								<div id="map_canvas1"></div>
							</div>
						</div>

						<div class="middle-south"><!-- Inner-South Layout Container -->
							<div class="south-center border"><?/*South Center <br /><br />(Inner-South Layout - Center Pane)*/?>
								<div id="map_canvas2"></div>
							</div>
							<div class="south-east border"><?/*South East     <br /><br />(Inner-South Layout - East Pane)*/?>
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