<?php

class Maps extends GPS_Controller {

	function __construct()
	{
		parent::__construct();
		
		$this->add_javascript('http://maps.google.com/maps/api/js?sensor=false');
		$this->add_stylesheet('http://code.google.com/apis/maps/documentation/javascript/examples/standard.css');
	}
	
	function index()
	{
		$this->point();
	}
	
	function data($imei)
	{
		$this->renderMode = "block";
		$this->load->model('gps_trunk');
		
		$imei = $this->gps_trunk->get_lastest_point_of_imei($imei);
		
		$imei['raw_data'] = rawdata_refine($imei['raw_data']);
		
		$thirtymin_before = strtotime("-".self::TIME_DEFFAULT." minutes");
		$created = strtotime($imei['created']);
		
		$imei['connected'] = ($created-$thirtymin_before>0)? true: false;
		
		$json['imei'] = $imei;
		//$json['raw_data'] = rawdata_refine($imei['raw_data']);
		echo json_encode($json);
	}
	
	function data_path($imei)
	{
		$this->renderMode = "block";
		$this->load->model('gps_trunk');
		
		$from_date = $this->input->get('from_date');
		$from_hour = $this->input->get('from_hour');
		$to_date = $this->input->get('to_date');
		$to_hour = $this->input->get('to_hour');
		
		$from_date = $this->_convert_date($from_date, $from_hour);
		$to_date = $this->_convert_date($to_date, $to_hour);
		
		$path = $this->gps_trunk->get_path_of_imei($imei, $from_date, $to_date);
		$distance = 0;
		$tmp = null;
		//foreach ($path as &$row) {
		//	if ($tmp) {
		//		$distance += distance($tmp['lat'], $tmp['lng'], $row['lat'], $row['lng']);
		//	}
		//	$row['distance'] = round($distance, 4);
		//	$row['raw_data'] = rawdata_refine_array($row['raw_data']);
		//	$tmp = $row;
		//}
		
		$json['path'] = $path;
		echo json_encode($json);
		//$this->_render(print_r($path,true));
	}
	
	function data_summary() {
		$this->load->model('gps_trunk');
		
		$from_date = $this->input->get('from_date');
		$from_hour = $this->input->get('from_hour');
		$to_date = $this->input->get('to_date');
		$to_hour = $this->input->get('to_hour');
		$imei_list = $this->input->get('imei_list');
		
		$from_date = $this->_convert_date($from_date, $from_hour);
		$to_date = $this->_convert_date($to_date, $to_hour);
		$imei_arr = explode("|", $imei_list);
		
		$summary = $this->gps_trunk->get_summary_of_imei_list($imei_arr, $from_date, $to_date);
		$distance = 0;
		$tmp = null;
		foreach ($summary as &$row) {
			if ($tmp) {
				$distance += distance($tmp['lat'], $tmp['lng'], $row['lat'], $row['lng']);
			}
			$row['distance'] = round($distance, 4);
			$row['raw_data'] = rawdata_refine_array($row['raw_data']);
			$tmp = $row;
		}
		
		$json['summary'] = $summary;
		echo json_encode($json);
	}
	
	function data_summary_1()
	{
		$this->load->model('gps_trunk');
		
		$from_date = $this->input->get('from_date');
		$from_hour = $this->input->get('from_hour');
		$to_date = $this->input->get('to_date');
		$to_hour = $this->input->get('to_hour');
		$imei_list = $this->input->get('imei_list');
		
		$from_date = $this->_convert_date($from_date, $from_hour);
		$to_date = $this->_convert_date($to_date, $to_hour);
		$imei_arr = explode("|", $imei_list);
		
		$summary = $this->gps_trunk->get_summary_of_imei_list_all($imei_arr, $from_date, $to_date);
		/*
		$distance = 0;
		$tmp = null;
		foreach ($summary as &$row) {
			if ($tmp) {
				$distance += distance($tmp['lat'], $tmp['lng'], $row['lat'], $row['lng']);
			}
			$row['distance'] = round($distance, 4);
			$row['raw_data'] = rawdata_refine_array($row['raw_data']);
			$tmp = $row;
		}
		*/
		
		$json['summary'] = $summary;
		echo json_encode($json);
	}
	
	function data_summary_distance()
	{
		$this->load->model('gps_trunk');
		
		$from_date = $this->input->get('from_date');
		$from_hour = $this->input->get('from_hour');
		$to_date = $this->input->get('to_date');
		$to_hour = $this->input->get('to_hour');
		$imei_list = $this->input->get('imei_list');
		
		$from_date = $this->_convert_date($from_date, $from_hour);
		$to_date = $this->_convert_date($to_date, $to_hour);
		$imei_arr = explode("|", $imei_list);
		
		$summary = $this->gps_trunk->get_summary_of_imei_list_distance($imei_arr, $from_date, $to_date);
		/*
		$distance = 0;
		$tmp = null;
		foreach ($summary as &$row) {
			if ($tmp) {
				$distance += distance($tmp['lat'], $tmp['lng'], $row['lat'], $row['lng']);
			}
			$row['distance'] = round($distance, 4);
			$row['raw_data'] = rawdata_refine_array($row['raw_data']);
			$tmp = $row;
		}
		*/
		
		$json['summary'] = $summary;
		echo json_encode($json);
	}
	
	function data_summary_usage()
	{
		$this->load->model('gps_trunk');
		
		$from_date = $this->input->get('from_date');
		$from_hour = $this->input->get('from_hour');
		$to_date = $this->input->get('to_date');
		$to_hour = $this->input->get('to_hour');
		$imei_list = $this->input->get('imei_list');
		
		$from_date = $this->_convert_date($from_date, $from_hour);
		$to_date = $this->_convert_date($to_date, $to_hour);
		$imei_arr = explode("|", $imei_list);
		
		$summary = $this->gps_trunk->get_summary_of_imei_list_usage($imei_arr, $from_date, $to_date);
		/*
		$distance = 0;
		$tmp = null;
		foreach ($summary as &$row) {
			if ($tmp) {
				$distance += distance($tmp['lat'], $tmp['lng'], $row['lat'], $row['lng']);
			}
			$row['distance'] = round($distance, 4);
			$row['raw_data'] = rawdata_refine_array($row['raw_data']);
			$tmp = $row;
		}
		*/
		
		$json['summary'] = $summary;
		echo json_encode($json);
	}
	
	function data_path_export()
	{
		$this->renderMode = "block";
		$this->load->model('gps_trunk');
		
		$imei = $this->input->post('imei');
		$from_date = $this->input->post('from_date');
		$from_hour = $this->input->post('from_hour');
		$to_date = $this->input->post('to_date');
		$to_hour = $this->input->post('to_hour');
		
		$from_date = $this->_convert_date($from_date, $from_hour);
		$to_date = $this->_convert_date($to_date, $to_hour);
		
		$path = $this->gps_trunk->get_path_of_imei($imei, $from_date, $to_date);
		$distance = 0;
		$tmp = null;
		$i = 1;
		foreach ($path as &$row) {
			if ($tmp) {
				$distance += round(distance($tmp['lat'], $tmp['lng'], $row['lat'], $row['lng']), 2);
			}
			$row['id'] = $i++;
			$raw_data = rawdata_refine_array($row['raw_data']);
			foreach ($raw_data as $key=>$val) {
				$row[$key] = $val;
			}
			$row['distance'] = $distance;
			unset($row['raw_data']);
			unset($row['imei']);
			$tmp = $row;
		}
		$header = array(array("id" => "Stt", "lat" => "LAT", "lng" => "LNG", "created" => "Ngày", "distance" => "Quãng đường", "speed" => "Tốc độ"));
		$path = array_merge($header, $path);
		array_to_csv($path, 'path_export_'.$imei.'.csv');
		//$json['path'] = $path;
		//echo json_encode($json);
	}
	
	function point()
	{
		$this->add_javascript('maps-point.js');
		$this->load->model('gps_userdevices');
		$this->load->model('gps_trunk');
		
		$user_id = $this->user_id;
		if (!$this->logged) {
			$user_id = GPS_Controller::DEMO_USER_ID;
		}
		$devies = $this->gps_userdevices->get_user_devices($user_id);
		
		$this->v['device_list'] = array();
		foreach ($devies as $item) {
			$item['lat_lng'] = $this->gps_trunk->get_lastest_point_of_imei($item['imei']);
			$this->v['device_list'][$item['imei']] = $item;
		}
		
		$content = $this->load->view('maps/point', $this->v, true);
		$this->_render($content);
	}
	
	function path()
	{
		//$this->add_javascript('http://www.google.com/jsapi');
		$this->add_javascript('maps-path.js');
		$this->add_javascript('jquery-ui-1.8.9.custom.min.js');
		$this->add_stylesheet('redmond/jquery-ui-1.8.9.custom.css');
		
		$this->load->model('gps_userdevices');
		$this->load->model('gps_trunk');
		
		$user_id = $this->user_id;
		if (!$this->logged) {
			$user_id = GPS_Controller::DEMO_USER_ID;
		}
		$devies = $this->gps_userdevices->get_user_devices($user_id);
		
		$this->v['device_list'] = array();
		foreach ($devies as $item) {
			$this->v['device_list'][$item['imei']] = $item;
		}
		
		$this->v['from_date'] = $this->_convert_date(null);
		$this->v['to_date'] = $this->_convert_date(null);

		$content = $this->load->view('maps/path', $this->v, true);
		$this->_render($content);
	}
	
	function _convert_date($date_str, $hour_str=null) {
		if (!$date_str)
			return strtotime("now");

		if ($hour_str) {
			$hourArr = explode(":", $hour_str);
		}
		$dateArr = explode("/",$date_str);
		
		if (is_array($hourArr)) {
			$timeStamp = mktime($hourArr[0],$hourArr[1],0,$dateArr[1],$dateArr[0],$dateArr[2]);
		} else {
			$timeStamp = mktime(0,0,0,$dateArr[1],$dateArr[0],$dateArr[2]);
		}
		
		return $timeStamp;
	}
}

/* End of file Maps.php */
/* Location: ./system/application/controllers/Maps.php */