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
					{title: "item2: selected on init"},
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
							{title: "Sub-item 4.2 (selected on init)", 
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
			
			var grid;

			var data = [];

			var columns = [
				{id:"title", name:"Title", field:"title", width:120, cssClass:"cell-title"},
				{id:"duration", name:"Duration", field:"duration"},
				{id:"%", name:"% Complete", field:"percentComplete", width:80, resizable:false, formatter:GraphicalPercentCompleteCellFormatter},
				{id:"start", name:"Start", field:"start", minWidth:60},
				{id:"finish", name:"Finish", field:"finish", minWidth:60},
				{id:"effort-driven", name:"Effort Driven", sortable:false, width:80, minWidth:20, maxWidth:80, cssClass:"cell-effort-driven", field:"effortDriven", formatter:BoolCellFormatter}
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
					south__initClosed: true,
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
				
				for (var i=0; i<500; i++) {
					var d = (data[i] = {});

					d["title"] = "Task " + i;
					d["duration"] = "5 days";
					d["percentComplete"] = Math.min(100, Math.round(Math.random() * 110));
					d["start"] = "01/01/2009";
					d["finish"] = "01/05/2009";
					d["effortDriven"] = (i % 5 == 0);
				}

				grid = new Slick.Grid($("#summary-table"), data, columns, options);
				
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
			});
		</script>
    <div id="cols" class="box">
        <!-- Content -->
        <div id="content">
				<div id="container">
					<div id="container-content" class="pane ui-layout-center">
						<div id="" class="middle-north"><!-- Inner-North Layout Container -->
							<form name="form_search" method="post" action="">
							<div class="search_form">
								<div class="search_from">
									<label for="from">From</label>
									<input type="text" id="from_date" name="from_date" class="text ui-widget-content" value="<? echo date('d/m/Y', $from_date); ?>"/>
									<? echo form_hours_dropdown('from_hour', null, 'class="text ui-widget-content"'); ?>
								</div>
								<div class="search_to">
									<label for="to">to</label>
									<input type="text" id="to_date" name="to_date" class="text ui-widget-content" value="<? echo date('d/m/Y', $to_date); ?>"/>
									<? echo form_hours_dropdown('to_hour', null, 'class="text ui-widget-content"'); ?>
								</div>
								<div class="search_btn">
									<input type="submit" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" name="SEARCH" value="SEARCH"/>
								</div>
							</div>
							</form>
						</div>
						<div id="" class="middle-center"><!-- Inner-Center Layout Container -->
							<div id="map_canvas"></div>
						</div>
						<div class="middle-south"><!-- Inner-South Layout Container -->
							<div class="ui-widget-header summary-header" style="border: 0;">Header</div>
							<div id="summary-table" class="ui-layout-content"></div>
							<div class="ui-widget-header summary-footer" style="border: 0;">Footer</div>
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