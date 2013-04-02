<?php

class Pages extends GPS_Controller {

	function __construct()
	{
		parent::__construct();	
	}
	
	function index($item)
	{
		$content = $this->load->view('pages/'.$item, $this->v, true);
		$this->_render($content);
	}
}

/* End of file pages.php */
/* Location: ./system/application/controllers/pages.php */