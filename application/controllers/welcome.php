<?php

class Welcome extends GPS_Controller {

	function __construct()
	{
		parent::__construct();
	}
	
	/**
	 * Overridden Remapping Function Calls
	 */
	public function _remap($method)
	{
		if ($method)
		{
			$this->index($method);
		} else {
			$this->default_method();
    	}
	}
	
	function index($method='index')
	{
		if (in_array($method, array('summary', 'options', 'path'))) {
			$this->add_javascript('jquery.event.drag-2.0.min.js');
			$this->add_javascript('slick.editors.js');
			$this->add_javascript('slick.grid.js');
			$this->add_stylesheet('slick.grid.css');
			$this->add_stylesheet('slick.theme.css');
		}
		$this->add_javascript('http://maps.google.com/maps/api/js?sensor=false');
		$this->add_stylesheet('http://code.google.com/apis/maps/documentation/javascript/examples/standard.css');
		$this->add_stylesheet('dynatree/ui.dynatree.css');
		$this->add_javascript('jquery.dynatree.js');
		$content = $this->load->view('welcome/'.$method, $this->v, true);
		$this->_render($content);
	}
}

/* End of file welcome.php */
/* Location: ./system/application/controllers/welcome.php */