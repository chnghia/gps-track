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
					{title: "1. Định vị thiết bị GPS", key: "id1", expand: true,
						children: [
							{title: "1.1 Định vị thiết bị", activate: true },
							{title: "1.2 Định vị nhóm thiết bị", activate: true },
							{title: "1.3 Chia màn hình", activate: true },
							{title: "1.4 Hiển thị thông tin", activate: true },
						]
					},
					{title: "2. Truy vấn lịch sử thiết bị GPS"},
					{title: "3. Thống kê", key: "id3", expand: true,
						children: [
							{title: "3.1 Thống kê 1", activate: true },
							{title: "3.2 Thống kê 2", activate: true },
						]
					},
					{title: "4. Cấu hình thiết bị"}
				];
		</script> 
		<script type="text/javascript">
			var outerLayout, middleLayout, innerLayout_Center, innerLayout_South;
			
			$(document).ready(function () {
			
				middleLayout = $('div.ui-layout-center').layout({
					name: "middle", 
					center__paneSelector: ".middle-center",
					spacing_open: default_spacing_open,	// ALL panes
					spacing_closed: default_spacing_closed // ALL panes
				});
				
				//map = new google.maps.Map(document.getElementById("map_canvas"), mapOptions); 
				//map.setCenter(defaultPoint);
				
				$("#aside-01-tree3").dynatree({
					checkbox: false,
					// Override class name for checkbox icon:
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
						<div id="" class="middle-center"><!-- Inner-Center Layout Container -->
							<div class="ui-widget-header summary-header" style="border: 0;">Giúp đỡ</div>
							<div id="help-content" class="ui-layout-content">
								1. Định vị thiết bị GPS<br>
								1.1 Định vị thiết bị<br>
								1.2 Định vị nhóm thiết bị<br>
								1.3 Chia màn hình<br>
								1.4 Hiển thị thông tin<br>
							</div>
							<div class="ui-widget-header summary-footer" style="border: 0;">&nbsp;</div>
						</div>
						<?/*
						<div class="middle-south"><!-- Inner-South Layout Container -->
							Inner-South
						</div>
						*/?>
					</div> <!-- /container-content -->
					<div class="pane ui-layout-west">
						<div id="tabs">
							<ul class="tabs-ul">
								<li><a href="#tabs-1">Mục lục</a></li>
							</ul>
							<div id="tabs-1">
								<div id="aside-01-tree3" ></div>
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