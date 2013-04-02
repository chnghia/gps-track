<?php

class GPS extends GPS_Controller {

	function __construct()
	{
		parent::__construct();
	}

	/**
	 * Overridden Remapping Function Calls
	 */
	public function _remap($method)
	{
		$this->v['activetab'] = ($method != 'index')? $method : 'home';
		if (!in_array($method, array('options')))
		{
			$this->index($method);
		} else {
			$this->$method();
    	}
	}

	function index($method='index')
	{
		if ($method == 'index') $method = 'location';
		if (in_array($method, array('options', 'path'))) {
			$this->add_javascript('jquery.event.drag-2.0.min.js');
			$this->add_javascript('slick.editors.js');
			$this->add_javascript('slick.grid.js');
			$this->add_stylesheet('slick.grid.css');
			$this->add_stylesheet('slick.theme.css');
		}
		
		if (in_array($method, array('summary'))) {
			$this->add_stylesheet('jqGrid/ui.jqgrid.css"');
			$this->add_javascript('jqGrid/i18n/grid.locale-en.js');
			$this->add_javascript('jqGrid/jquery.jqGrid.min.js');
		} 
		$this->add_javascript('http://maps.google.com/maps/api/js?sensor=false&language=vi&libraries=geometry');
		$this->add_stylesheet('http://code.google.com/apis/maps/documentation/javascript/examples/standard.css');
		$this->add_stylesheet('dynatree/ui.dynatree.css');
		$this->add_javascript('jquery.dynatree.js');
		
		// Get data for treeview
		$this->load->model('gps_userdevices');
		$this->load->model('gps_trunk');
		
		if (in_array($method, array('summary', 'path'))) {
			$this->v['from_date'] = $this->v['to_date'] = strtotime("now");
		}
		
		$user_id = $this->user_id;
		if (!$this->logged) {
			$user_id = GPS_Controller::DEMO_USER_ID;
		}
		$device_groups = $this->gps_userdevices->get_user_devices_by_group($user_id);
		
		$this->v['device_groups'] = $device_groups;
		
		$content = $this->load->view('gps/'.$method, $this->v, true);
		$this->_render($content);
	}

	function options()
	{
		// Get data for treeview
		$this->load->model('gps_userdevices');
		
		$action = $this->input->get('act');
		$device_id = $this->input->get('device_id');
		
		if ($action == 'edit') {
			$device = $this->gps_userdevices->get_device_detail($device_id);
			$this->v['device_edit'] = $device;
		} elseif ($action == 'edit-save') {
			$this->renderMode = 'block';
			
			$dev = array(
				'number_plate' => $this->input->get('number_plate'),
				'group_name' => $this->input->get('gps_group'),
				'avatar' => $this->input->get('avatar'));
			$ret = $this->gps_userdevices->update_userdevices($device_id, $dev);
			$json['success'] = false;
			if ($ret) {
				$json['success'] = true;
			} 
			echo json_encode($json);
			return;
		} elseif ($action == 'delete') {
			$this->gps_userdevices->delete_device($device_id);
		}
		
		//if (!$this->logged) {
		//	redirect('');
		//	return;
		//}
		//$this->add_javascript('http://maps.google.com/maps/api/js?sensor=false');
		$this->add_stylesheet('http://code.google.com/apis/maps/documentation/javascript/examples/standard.css');
		$this->add_stylesheet('dynatree/ui.dynatree.css');
		$this->add_stylesheet('tipsy.css');
		$this->add_javascript('jquery.dynatree.js');
		$this->add_javascript('jquery.tipsy.js');
		$this->add_javascript('gps-options.js');
		
		$user_id = $this->user_id;
		if (!$this->logged) {
			$user_id = GPS_Controller::DEMO_USER_ID;
		}
		$device_groups = $this->gps_userdevices->get_user_devices_by_group($user_id);
		
		$this->v['device_groups'] = $device_groups;

		$content = $this->load->view('gps/options', $this->v, true);
		$this->_render($content);
	}
}

/* End of file welcome.php */
/* Location: ./system/application/controllers/welcome.php */