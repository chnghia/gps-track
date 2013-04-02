<?php

class products extends GPS_Controller {

	function __construct()
	{
		parent::__construct();	
	}
	
	function index()
	{
		$this->Display_product();
		
	}
	function Display_product()
	{
		//show products
		$this->load->model('products_model');
		$results=$this->products_model->search();
		$data['products']=$results['rows'];
		$data['num_results']=$results['num_rows'];
		//delete
		//delete devices
		if($_GET['action']=='delete')
		{
			$this->load->model('products_model');
			$data =array($id = $_GET['param'],$id=trim($id),unlink($id));
			$this->products_model->delete($id);
			header('Location:Display_product');
			exit();
			
		}
		//insert and update
		if(isset($_POST['insert']))
		{
			if($_GET['action']=='update'&&$_GET['param']!=NULL)
			{
				$data = array
						(	
							$name=$this->input->post('name'),
							$main_feature=$this->input->post('main_feature')
						);
						$id =$_GET['param'];
						$id=trim($id);
						unlink($id);
						$devices_list=$this->products_model->update($id);
						header('Location:Display_product');
						exit();
			}
			else
			{
				$this->load->library('DX_Auth');
				$this->load->helper(array('form', 'url'));
				$this->load->library('form_validation');
				//$this->form_validation->set_rules($config); 
				$this->form_validation->set_rules('name', 'Product Name', 'trim|required');
				$this->form_validation->set_rules('main_feature', 'main feature', 'trim|required');
				if ($this->form_validation->run()==FALSE)
				{
						
				}
				else
				{
					//$this->load->view('devices/mana');
			
					$this->load->model('products_model');
					$data=array(
						
								$name=$this->input->post('name'),
								$main_feature=$this->input->post('main_feature')
							);
						
					$results=$this->products_model->insert($name,$main_feature);
					header('Location:Display_product');
					exit();
				}
			}
		}
			//load du lieu khi click update
		if($_GET['action']=='update')
		{
			$id =$_GET['param'];
			$id=trim($id);
			unlink($id);
			//command id
			$this->load->model('products_model');
			$results=$this->products_model->Search_id($id);
			$data['products_id']=$results['rows'];
		}

		$content = $this->load->view('products/product', $data, true);
		$this->_render($content);
	}
}