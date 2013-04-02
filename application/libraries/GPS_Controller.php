<?php

/**
 * Master Controller
 */
class GPS_Controller extends Controller
{
	// Holds the view data
	var $v = array();
	
	var $stylesheets = array();
	var $jscripts = array();
	var $metatags = array();

	// The default render mode displays the full page.  Can also be 'block'.
	var $renderMode = 'page';
	
	var $pageTitle = 'GPS Tracking';
	
	var $user_id = 0;
	var $user_name = '';
	var $logged = false;
	
	const DEMO_USER_ID = 2;
	const LAT_DEFAULT = 10.79883;
	const LNG_DEFAULT = 106.65999;
	const ZOOM_DEFAULT = 14;
	const TIME_DEFFAULT = 30;

	/**
	 * The constructor is used to set global view vars and determine if the request was
	 * an AJAX call (XMLHttpRequest method), and if so, set the rendering mode to 'block'.
	 */
	function __construct()
	{
		parent::Controller();
		
		log_message('debug', 'GPSController Initialized');

		if ($this->isAjax()) {
			$this->renderMode = 'block';
			log_message('debug', 'AJAX = true');
		}
		
		$this->load->library('DX_Auth');
		$this->v['logged'] = $this->logged = $this->dx_auth->is_logged_in();
		$this->v['user_id'] = $this->user_id = $this->dx_auth->get_user_id();
		$this->v['user_name'] = $this->user_name = $this->dx_auth->get_username();
		$this->v['activetab'] = 'home';
	}
	
	function isAjax()
	{
		// Determine if this was an AJAX-driven or normal HTTP request
		foreach ($_SERVER as $name => $value)
		{
			if (!is_string($name) || !is_string($value))
				continue;
			$name = strtolower($name);
			$value = strtolower($value);

			if (substr($name, 0, 5) == 'http_')
			{
				$name = substr($name, 5);

				// Prototype sets this header for all AJAX requests
				if ($name == 'x_requested_with' and $value == 'xmlhttprequest')
				{
					return true;
				}
			}
		}
		return false;
	}

	/**
	 * Renders either the block or the full page.
	 *
	 * If the render mode is 'block', just display the content, otherwise render the full
	 * page layout.  The default layout is 'content_page' but that can be overridden by
	 * the calling controller.
	 *
	 * @access private
	 */
	function _render($content, $header=null, $footer=null, $view = 'wrapper/main')
	{
		if ($this->renderMode == 'block')
		{
			echo $content;
		}
		else
		{
			$this->v['pageTitle'] = $this->pageTitle;
			$this->v['pageCSS'] = $this->_renderCSS();
			$this->v['pageScript'] = $this->_renderScripts();
			
			$this->v['metaKeywords'] = $this->_renderMetaKeywords();
			$this->v['metaDescription'] = $this->_renderMetaDescription();
			$this->v['metaCustom'] = $this->_renderMetaCustom();
			
			$this->v['pageContent'] = $content;
			if (!is_null($header)) {
				$this->v['pageHeader'] = $header;
			} else {
				$this->v['pageHeader'] = $this->_renderHeader();
			}
			
			if (!is_null($footer)) {
				$this->v['pageFooter'] = $footer;
			} else {
				$this->v['pageFooter'] = $this->_renderFooter();
			}

			if ($this->config->item('enable_profiler')) $this->output->enable_profiler(true);
			$this->load->view($view, $this->v);
		}
	}
	
	function _renderMetaKeywords()
	{
		if (!isset($this->metatags['keywords']))
			return '';
		$str = '<meta name="keywords" content="' . $this->metatags['keywords'] . '" />';
		
		return $str;
	}
	
	function _renderMetaDescription()
	{
		if (!isset($this->metatags['description']))
			return '';
		$str = '<meta name="description" content="' . $this->metatags['description'] . '" />';
		return $str;
	}
	
	function _renderMetaCustom()
	{
		$str = '';
		foreach ($this->metatags as $key=>$val) {
			if (in_array($key, array('keywords', 'description'))) continue;
			
			$str .= '<meta name="' . $key . '" content="' . $val . '" />';
		}
		return $str;
	}
	
	function _renderPopup($content)
	{
		$this->_render($content, '', '');
	}
	
	function _renderCSS() {
		$css_str = '';
		foreach($this->stylesheets as $css) {
			if (preg_match('/^(http|https):\/\/([a-z0-9-]\.+)*/i', $css)) {
				$css_str .= '<link rel="stylesheet" href="'. $css .'" type="text/css"/>';
			} else {
        $css_str .= '<link rel="stylesheet" href="/assets/css/'. $css .'" type="text/css"/>';
			}
		}
		return $css_str;
	}
	
	function _renderScripts() {
		$js_str = '';
		foreach($this->jscripts as $js) {
			if (preg_match('/^(http|https):\/\/([a-z0-9-]\.+)*/i', $js)) {
				$js_str .= '<script src="'. $js .'" type="text/javascript"></script>';
			} else {
        $js_str .= '<script src="/assets/js/'. $js .'" type="text/javascript"></script>';
			}
		}
		return $js_str;
	}
	
	function _renderHeader() {
		return $this->load->view('wrapper/header', $this->v, true);
	}
	
	function _renderFooter() {
		return $this->load->view('wrapper/footer', $this->v, true);
	}
	
	function add_stylesheet($name) {
		$this->stylesheets[] = $name;
	}
	
	function add_javascript($name) {
		$this->jscripts[] = $name;
	}
	
	/**
	 * sets a url to be redirected to after successful login
	 * @param string url
	 */
	function set_post_login_redirect($url=false)
	{
			if (empty($url)) {
					$url = $_SERVER['REQUEST_URI'];
			}

			$this->session->set_userdata('post_login_redirect', $url);
	}

	/**
	 * retrieves the url that was set for redirection
	 */
	function get_post_login_redirect()
	{
			return $this->session->userdata('post_login_redirect');
	}
}
?>
