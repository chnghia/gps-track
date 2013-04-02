<script type="text/javascript">
	var map;
	//var geocoder;
  var coordInfoWindow = new google.maps.InfoWindow({content: "Tan Binh, HCMC"});
	//var pointMarker;
	var image = '/assets/images/icons/auto_icon.gif';
	
	var mapOptions = {
		zoom: <? echo GPS_Controller::ZOOM_DEFAULT; ?>,
		streetViewControl: false,
		mapTypeId: google.maps.MapTypeId.ROADMAP
	};

	var map_pointer = {
	<? $i=0;foreach ($device_list as $imei=>$val) : ?>
		 <? echo ($i > 0)? "," : ""; ?> 
		 "imei_<?echo $imei;?>" : {"marker" : new google.maps.Marker({icon: image}), "infoWindow" : new google.maps.InfoWindow({content: ""}), "default" : <? echo $val['default']; ?> }
	<? $i++;endforeach; ?>
	};
	
	var defaultPoint = new google.maps.LatLng(<? echo GPS_Controller::LAT_DEFAULT; ?>, <? echo GPS_Controller::LNG_DEFAULT; ?>);
</script> 
<div id="wrap">
	<div class="wrap-main">
			<div class="wrap-content">
				<div class="wrap-innercontent-maps">
					<!-- Column 1 start -->
					<div id="map_canvas"></div> 
					<!-- Column 1 end -->
				</div>
			</div>
	</div>
	<? echo $this->load->view('maps/left-side'); ?>
</div>