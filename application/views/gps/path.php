    <!-- Columns -->
		<script type="text/javascript">
			var map, map1, map2, map3;
			var geocoder = new google.maps.Geocoder();
			var coordInfoWindow = new google.maps.InfoWindow({content: "Tan Binh, HCMC"});
			var image = '/assets/images/icons/auto_icon.gif';

			// For path screen
			var DEFAULT_WAYPOINTS = 7;
			var directionsService = new google.maps.DirectionsService();
			var directionsDisplay = [];
			var marker1, marker2;
			var info1, info2;
			var path_line;
			
			var mapOptions = {
				zoom: <? echo GPS_Controller::ZOOM_DEFAULT; ?>,
				streetViewControl: false,
				panControl: false,
				zoomControlOptions: { position: google.maps.ControlPosition.TOP_LEFT, style: google.maps.ZoomControlStyle.SMALL },
				mapTypeId: google.maps.MapTypeId.ROADMAP
			};

			var map_pointer = {
			<? foreach ($device_groups as $key=>$group) : ?>
				<? foreach ($group as $device) : ?>
				<? $icon = ($device['avatar'])? '/assets/images/icons/'.$device['avatar'] : '/assets/images/icons/icon_car.png' ; ?>
				"imei_<?= $device['imei']; ?>" : {"marker" : new google.maps.Marker({icon: "<?= $icon; ?>"}), "infoWindow" : new google.maps.InfoWindow({content: ""}), "default" : <? echo ($device['default'])? $device['default'] : 0; ?> },
				<? endforeach; ?>
			<? endforeach; ?>
			};
			
			var defaultPoint = new google.maps.LatLng(<? echo GPS_Controller::LAT_DEFAULT; ?>, <? echo GPS_Controller::LNG_DEFAULT; ?>);
			
			var treeData = [
							<? $i=0;foreach ($device_groups as $key=>$group) : ?>
								{title: "<?= $key . " (".count($group).")"; ?>", isFolder: true, hideCheckbox: true, key: "group_<?=$i;?>", <?= ($i==0)? 'expand: true,' : ''; ?>
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
			
			var grid;

			var grid_data = [];

			var columns = [
						{id:"id", name:"STT", field:"id", width:40, cssClass:"cell-title"},
						/*{id:"number_plate", name:"Số xe", width:80, field:"number_plate"},*/
						{id:"street", name:"Địa chỉ", width:180, field:"street"},
						{id:"latlng", name:"Tọa độ", width:140, field:"latlng"},
						{id:"date", name:"Thời gian", width:130, field:"date"},
						{id:"speed", name:"Tốc độ(km/h)", width:90, field:"speed"},
						{id:"distance", name:"Quãng đường", width:90, field:"distance"}
						/*{id:"lat", name:"KK", field:"percentComplete", width:80, resizable:false, formatter:GraphicalPercentCompleteCellFormatter},*/
						/*{id:"start", name:"Start", field:"start", minWidth:60},*/
						/*{id:"finish", name:"Finish", field:"finish", minWidth:60},*/
						/*{id:"effort-driven", name:"Effort Driven", sortable:false, width:80, minWidth:20, maxWidth:80, cssClass:"cell-effort-driven", field:"effortDriven", formatter:BoolCellFormatter}*/
					];


			var options = {
				editable: false,
				enableAddRow: false,
				enableCellNavigation: true,
				rowCssClasses: function(item) {
					// if a task is 100% done then its row gets an additional CSS class
					return (item.percentComplete == 100) ? 'complete' : '';
				}
			};
			
			$(document).ready(function () {
			
				middleLayout = $('div.ui-layout-center').layout({
					name: "middle", 
					north__paneSelector: ".middle-north",
					center__paneSelector: ".middle-center",
					south__paneSelector: ".middle-south",
					north__size: 35,
					north__maxSize: 35,
					south__size: "50%",
					/*south__initClosed: true,*/
					spacing_open: default_spacing_open,	// ALL panes
					spacing_closed: default_spacing_closed // ALL panes
				});
				
				map = new google.maps.Map(document.getElementById("map_canvas"), mapOptions); 
				map.setCenter(defaultPoint);
				
				$("#aside-01-tree3").dynatree({
					checkbox: true,
					// Override class name for checkbox icon:
					classNames: {checkbox: "dynatree-radio"},
					selectMode: 1,
					children: treeData,
					onSelect: function(select, node) {
						// Get a list of all selected nodes, and convert to a key array:
						selKeys = $.map(node.tree.getSelectedNodes(), function(node){
							return node.data.key;
						});

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
				$( "#path-radio-div" ).buttonset();
				tabs_scrollable();

				/*
				for (var i=0; i<500; i++) {
					var d = (data[i] = {});

					d["title"] = "Task " + i;
					d["duration"] = "5 days";
					d["percentComplete"] = Math.min(100, Math.round(Math.random() * 110));
					d["start"] = "01/01/2009";
					d["finish"] = "01/05/2009";
					d["effortDriven"] = (i % 5 == 0);
				}
				*/

				grid = new Slick.Grid($("#summary-table"), grid_data, columns, options);
				grid.onClick = function (e, row, cell) {
					if (columns[cell].id == "street")
	                {
						var latlng = grid_data[row].latlng;
						eval("var coord = new google.maps.LatLng" + latlng + ";");
						 
						geocoder.geocode({'latLng': eval("coord")}, function(results, status) {
							if (status == google.maps.GeocoderStatus.OK) {
								if (results[0]) {
									content = results[0].formatted_address;
									grid_data[row].street = content;
									grid.updateRow(row);
									//return true;
								}
							} else {
								//alert("Geocoder failed due to: " + status);
							}
						});
	                }
				}
				
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

				$( "#search_submit" )
				.button()
				.click(function(evt) {
					evt.preventDefault();

					var selNodes = $("#aside-01-tree3").dynatree("getTree").getSelectedNodes(false);
					var selKeys = $.map(selNodes, function(node){
						return node.data.key;
					});
					if (selKeys.length == 0) {
						open_dialog("<p style='text-align: center; color: red;'><span class='ui-icon ui-icon-alert' style='float:left; margin:2px 5px 0 0;'></span>Hãy chọn 1 thiết bị.</p>");
						return; 
					}
					selKeys = selKeys[0];
					
					$('#map_status').show();
					var imei = selKeys.split("_");
					if (imei[0] == "group")
						return true;
					imei = imei[1];

					if ($('input:radio[name="path-radio"]:checked').val() == 'street') {
						display_path_of_imei(imei, map);
					} else {
						display_line_of_imei(imei, map);
					}
					//$( "#form_search" ).submit();
				});

				//$( "#search_right" ).buttonset();

				$( "#rewind" ).button({
					text: false,
					disabled: true,
					icons: {
						primary: "ui-icon-seek-prev"
					}
				});
				$( "#play" ).button({
					text: false,
					disabled: true,
					icons: {
						primary: "ui-icon-play"
					}
				})
				.click(function() {
					var options;
					if ( $( this ).text() === "play" ) {
						options = {
							label: "pause",
							icons: {
								primary: "ui-icon-pause"
							}
						};
					} else {
						options = {
							label: "play",
							icons: {
								primary: "ui-icon-play"
							}
						};
					}
					$( this ).button( "option", options );
				});
				$( "#stop" ).button({
					text: false,
					disabled: true,
					icons: {
						primary: "ui-icon-stop"
					}
				})
				.click(function() {
					$( "#play" ).button( "option", {
						label: "play",
						icons: {
							primary: "ui-icon-play"
						}
					});
				});
				$( "#forward" ).button({
					text: false,
					disabled: true,
					icons: {
						primary: "ui-icon-seek-next"
					}
				});
			});
		</script>
    <div id="cols" class="box">
        <!-- Content -->
        <div id="content">
				<div id="container">
					<div id="container-content" class="pane ui-layout-center">
						<div id="" class="middle-north"><!-- Inner-North Layout Container -->
							<div class="ui-widget-content ui-corner-all search_form">
								<form id="form_search" name="form_search" method="post" action="">
								<input type="hidden" name="imei" id="imei"></input>
								<div class="search_from">
									<label for="from">Từ</label>
									<input type="text" id="from_date" name="from_date" class="text ui-widget-content" value="<? echo date('d/m/Y', $from_date); ?>"/>
									<? echo form_hours_dropdown('from_hour', null, 'class="text ui-widget-content"'); ?>
								</div>
								<div class="search_to">
									<label for="to">Đến</label>
									<input type="text" id="to_date" name="to_date" class="text ui-widget-content" value="<? echo date('d/m/Y', $to_date); ?>"/>
									<? echo form_hours_dropdown('to_hour', null, 'class="text ui-widget-content"'); ?>
								</div>
								<div class="search_btn">
									<!-- <input type="submit" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" name="SEARCH" value="SEARCH"/>  -->
									<button id="search_submit">TÌM</button>
								</div>
								</form>
								<div class="ui-widget-content search_right_div search_sep">&nbsp</div>
								<? /*
								<div id="search_right" class="search_right_div">
									<label for="rewind">Mô phỏng: </label>
									<button id="rewind">Quay lại</button>
									<button id="play">Chạy</button>
									<button id="stop">Dừng</button>
									<button id="forward">Nhanh</button>
								</div>
								*/?>
							</div>
						</div>
						<div id="" class="middle-center"><!-- Inner-Center Layout Container -->
							<div id="map_status" class="map-status-loading" ><img width="64" height="15" src="/assets/images/icons/ajax-loader.gif"></img></div>
							<div id="map_canvas"></div>
						</div>
						<div class="middle-south"><!-- Inner-South Layout Container -->
							<div class="ui-widget-header summary-header" style="border: 0; height: 20px;">
								<div id="summary-header-title">
									Đường đi
								</div>
							</div>
							<div id="summary-table" class="ui-layout-content"></div>
							<div class="ui-widget-header summary-footer" style="border: 0; height: 22px;">
								<div id="search_right" class="search_right_div">
									<label for="rewind">Mô phỏng: </label>
									<button id="rewind">Quay lại</button>
									<button id="play">Chạy</button>
									<button id="stop">Dừng</button>
									<button id="forward">Nhanh</button>
								</div>
								<div id="tsv-export" style="">
								<a href="#" onclick="javascript:export_path_tsv();">TSV</a>
								</div>
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
							<div id="path-radio-div" class="tree-footer-content">
								<input type="radio" id="path-radio1" value="street" name="path-radio"  checked="checked" /><label for="path-radio1">Đường đi</label>
								<input type="radio" id="path-radio2" value="point" name="path-radio"/><label for="path-radio2">Gần đúng</label>
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