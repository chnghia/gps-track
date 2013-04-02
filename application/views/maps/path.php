<script type="text/javascript">
	var DEFAULT_WAYPOINTS = 7;
	var map;
	var mapOptions = {
		zoom: <? echo GPS_Controller::ZOOM_DEFAULT; ?>,
		streetViewControl: false,
		mapTypeId: google.maps.MapTypeId.ROADMAP
	};
	var defaultPoint = new google.maps.LatLng(<? echo GPS_Controller::LAT_DEFAULT; ?>, <? echo GPS_Controller::LNG_DEFAULT; ?>);
	var directionsService = new google.maps.DirectionsService();
	var directionsDisplay = [];
	var marker1, marker2;
	
<?/*
<? if ($path) : ?>
  var elevator;
  var map;
  var chart;
  var infowindow = new google.maps.InfoWindow();
  var polyline;

  // The following path marks a general path from Mt.
  // Whitney, the highest point in the continental United
  // States to Badwater, Death Vallet, the lowest point.
	<? $i=1;foreach ($path as $point) : ?>
	<? echo "var p$i = new google.maps.LatLng({$point['lat']}, {$point['long']});\n";?>
	<? $i++;endforeach; ?>
	/*
  var p1 = new google.maps.LatLng(10.802443, 106.641163);
  var p2 = new google.maps.LatLng(10.801649, 106.647967);
  var p3 = new google.maps.LatLng(10.800753, 106.657237);
  var p4 = new google.maps.LatLng(10.799426, 106.657977);
  var p5 = new google.maps.LatLng(10.797845, 106.658385);
  var p6 = new google.maps.LatLng(10.799036, 106.659576);
	* /
  
  // Load the Visualization API and the columnchart package.
  google.load("visualization", "1", {packages: ["columnchart"]});

  function initialize() {
    var myOptions = {
      zoom: 15,
      /*center: p2,* /
			center: <? echo 'p' . (int)($i/2); ?>,
      mapTypeId: 'hybrid'
    }
    map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);

    // Create an ElevationService.
    elevator = new google.maps.ElevationService();

    // Draw the path, using the Visualization API and the Elevation service.
    drawPath();
  }

  function drawPath() {

    // Create a new chart in the elevation_chart DIV.
    chart = new google.visualization.ColumnChart(document.getElementById('elevation_chart'));

		<?
			$str='';
			for ($ii=1; $ii<$i; $ii++) {
					$str .= 'p'.$ii . ', ';
			}
			
			$str = rtrim($str, ', ');
			echo "var path = [ $str ];";
		?>
		/*
    var path = [ p1, p2, p3, p4, p5, p6];
		* /

    // Create a PathElevationRequest object using this array.
    // Ask for 256 samples along that path.
    var pathRequest = {
      'path': path,
      'samples': 256
    }

    // Initiate the path request.
    elevator.getElevationAlongPath(pathRequest, plotElevation);
  }

  // Takes an array of ElevationResult objects, draws the path on the map
  // and plots the elevation profile on a Visualization API ColumnChart.
  function plotElevation(results, status) {
    if (status == google.maps.ElevationStatus.OK) {
      elevations = results;

      // Extract the elevation samples from the returned results
      // and store them in an array of LatLngs.
      var elevationPath = [];
      for (var i = 0; i < results.length; i++) {
        elevationPath.push(elevations[i].location);
      }

      // Display a polyline of the elevation path.
      var pathOptions = {
        path: elevationPath,
        strokeColor: '#0000CC',
        opacity: 0.4,
        map: map
      }
      polyline = new google.maps.Polyline(pathOptions);

      // Extract the data from which to populate the chart.
      // Because the samples are equidistant, the 'Sample'
      // column here does double duty as distance along the
      // X axis.
      var data = new google.visualization.DataTable();
      data.addColumn('string', 'Sample');
      data.addColumn('number', 'Elevation');
      for (var i = 0; i < results.length; i++) {
        data.addRow(['', elevations[i].elevation]);
      }
    }
  }
	
	window.onload=initialize;
<? endif; ?>
*/?>
</script>
<div id="wrap">
	<div class="wrap-main">
			<div class="wrap-content">
				<div class="wrap-innercontent-maps">
					<!-- Column 1 start -->
					<div id="ajax_loading">
						<img src="/assets/images/icons/ajax-loader.gif"></img>
					</div>
					<div id="search_link">
						<a href="#" id="hide_search">hide search</a>
					</div>
					<div id="directions_panel">
						<div id="directions_panel_header">
							RESULTS <a href="#" id="hide_search_result">[hide]</a>
						</div>
						<div id="directions_panel_inner">
							<?/*text text text text text text text text text text <br>
							text text text text text text text text text text <br>
							text text text text text text text text text text <br>
							text text text text text text text text text text <br>
							text text text text text text text text text text <br>
							text text text text text text text text text text <br>
							text text text text text text text text text text <br>
							text text text text text text text text text text <br>
							text text text text text text text text text text <br>
							text text text text text text text text text text <br>
							text text text text text text text text text text <br>
							text text text text text text text text text text <br>
							text text text text text text text text text text <br>
							text text text text text text text text text text <br>
							text text text text text text text text text text <br>*/?>
						</div>
					</div>
					<div id="search_form">
						<form id="path_search_form" action="<? echo site_url('/maps/path'); ?>" method="GET">
						<fieldset class="frmfields" id="search_from_fields">
						<dl>
							<dt>&nbsp;</dt>
							<dd id="from_fieldset">
							<label for="from">From</label>
							<input type="text" id="from_date" name="from_date" value="<? echo date('d/m/Y', $from_date); ?>"/>
							<? echo form_hours_dropdown('from_hour'); ?>
							</dd>
							<dd id="to_fieldset">
							<label for="to">to</label>
							<input type="text" id="to_date" name="to_date" value="<? echo date('d/m/Y', $to_date); ?>"/>
							<? echo form_hours_dropdown('to_hour'); ?>
							</dd>
						</dl>
						<dl>
							<dt>&nbsp;</dt>
							<dd id="search_fieldset">
								<input type="submit" class="search_btn" name="SEARCH" value="SEARCH"/>
							</dd>
						</dl>
						</fieldset>
						</form>
					</div>
					<div id="map_canvas"></div>
				</div>
				<!-- Column 1 end -->
			</div>
	</div>
	<? echo $this->load->view('maps/left-side'); ?>
</div>