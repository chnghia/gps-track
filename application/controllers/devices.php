<?php

class Devices extends GPS_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->helper('url');
		//$this->load->helper('form');
	}
	
	function index()
	{
		$this->Display_devices();
		//$content = $this->load->view('devices/mana',$this->v, true);
		//$this->_render($content);
		//$this->load->view('devices/mana', array('error' => ' ' ));
	}
	
	
	function command()
	{
		//display Command
		$this->load->model('command_model');
		$results=$this->command_model->search();
		$data['command']=$results['rows'];
		$data['num_results']=$results['num_rows'];
		//delete
		//delete command
		if($_GET['action']=='delete')
		{
			$this->load->model('command_model');
			$data =array($id = $_GET['param'],$id=trim($id),unlink($id));
			$this->command_model->delete($id);
			header('Location:command');
			exit();
			
		}
		//insert and update
		if(isset($_POST['insert']))
		{
			if($_GET['action']=='update'&&$_GET['param']!=NULL)
			{
				$data = array
						(	
							$cmname=$this->input->post('cmname'),
							$cmsms=$this->input->post('cmsms')
						);
						$id =$_GET['param'];
						$id=trim($id);
						unlink($id);
						$devices_list=$this->command_model->update($id);
						header('Location:command');
						exit();
			}
			else
			{
				$this->load->library('DX_Auth');
				$this->load->helper(array('form', 'url'));
				$this->load->library('form_validation');
				//$this->form_validation->set_rules($config); 
				$this->form_validation->set_rules('cmname', 'Command Name', 'trim|required');
				$this->form_validation->set_rules('cmsms', 'Command SMS', 'trim|required');
				if ($this->form_validation->run()==FALSE)
				{
						
				}
				else
				{
					//$this->load->view('devices/mana');
			
					$this->load->model('command_model');
					$data=array(
						
								$cmname=$this->input->post('cmname'),
								$cmsms=$this->input->post('cmsms')
							);
						
					$results=$this->command_model->insert($cmname,$cmsms);
					header('Location:command');
					exit();
				}
			}
		}
			//update
		if($_GET['action']=='update')
		{
			$id =$_GET['param'];
			$id=trim($id);
			unlink($id);
			//command id
			$this->load->model('command_model');
			$results=$this->command_model->Search_id($id);
			$data['command_id']=$results['rows'];
		}
		
		$content = $this->load->view('commands/command',$data, true);
		$this->_render($content);
	}
	
	function Display_devices()
	{
			//.............
			
			$limit= ($this->input->get('limit'))? $this->input->get('limit') : 5;
			$per_page = ($this->input->get('per_page'))? $this->input->get('per_page'): 0;
			//device
			$this->load->model('devices_model');
			$results=$this->devices_model->search($limit, $per_page);
			$data['devices']=$results['rows'];
			$data['num_results']=$results['num_rows'];
			//porduct
			$this->load->model('products_model');
			$results=$this->products_model->search();
			$data['products']=$results['rows'];
			//users
			$this->load->model('users_model');
			$results=$this->users_model->search();
			$data['users']=$results['rows'];
			//ime
			$this->load->model('gps_trunk');
			$results=$this->gps_trunk->search();
			$data['gps_trunk']=$results['rows'];
			
			//pareation phan trang
			$this->load->library('pagination');
			$config['base_url'] = site_url() . '/devices/Display_devices?limit='.$limit;
			$config['total_rows']=$data['num_results'];
			$config['per_page']=$limit;
			$config['uri_segment']=3;
			$this->pagination->initialize($config);
			$data['paginator']=$this->pagination->create_links();
			
			
		//inssert
		$this->load->model('devices_model');
		if(isset($_POST['btlinsert']))
		{

			//bat loi
			$this->load->helper(array('form', 'url'));
			$this->load->library('form_validation');
			
			$config = array(
				   array(
						 'field'   => 'imei', 
						 'name'   => 'txtimei', 
						 'rules'   => 'required'
					  ),
				   array(
						 'field'   => 'number_plate', 
						 'name'   => 'txtnumber', 
						 'rules'   => 'required'
					  )
            	);

			//$this->form_validation->set_rules($config); 
			$this->form_validation->set_rules('txtimei', 'Imei', 'required');
			$this->form_validation->set_rules('txtnumber', 'txtnumber', 'required');
			$this->form_validation->set_rules('txtcreated', 'txtcreated', 'required');
			if ($this->form_validation->run() == FALSE)
			{
				//$this->load->view('devices/mana');
			}
			else
			{
				//upload hinh anh
				
				$config['upload_path'] = './assets/images/avatar/';
				$config['allowed_types'] = 'gif|GIF|jpg|JPG|png|PNG|jpeg|GPEG';
				$config['max_size']	= '1000';
				$config['max_width']  = '1024';
				$config['max_height']  = '1024';
		
				$this->load->library('upload', $config);
		
				if ( ! $this->upload->do_upload())
				{
					$error = array('error' => $this->upload->display_errors());
					//$this->load->view('upload_form', $error);
				}
				else
				{
					
					$data['userfile'] = array('name' => 'userfile'); 
					$data = array('upload_data' => $this->upload->data());
					//$this->load->view('upload_success', $data);
				}
				//$this->load->view('formsuccess');
				// success
				
				$product_id=$this->input->post('txtproduct');
				$user_id=$this->input->post('txtuser');
				$imei=$this->input->post('txtimei');
				$number_plate=$this->input->post('txtnumber');
				$avatar=$_FILES['userfile']['name'];
				//$avatar=$_FILES['userfile'];
				$created=$this->input->post('txtcreated');
				$data = array($product_id,$user_id,$imei,$number_plate,$avatar,$created);
				$devices_list=$this->devices_model->insert_device($product_id,$user_id,$imei,$number_plate,$avatar,$created);
				
				//images thumnail
					$config['image_library'] = 'gd';
					$config['source_image'] = './assets/images/avatar/'.$_FILES['userfile']['name'].'';
					$config['new_image'] = './assets/images/avatar/'.$_FILES['userfile']['name'].'';
					$config['create_thumb'] = TRUE;
					$config['maintain_ratio'] = TRUE;
					$config['width'] = 20;
					$config['height'] = 20;
					$this->load->library('image_lib', $config);
					$this->image_lib->resize();
				header('Location:Display_devices');
				//echo "Insert Thanh Cong ";
				exit();		
			}
		}	

		//delete devices
		if($_GET['action']=='delete')
		{
			$data =array($id = $_GET['param'],$id=trim($id),unlink($id));
			$devices_list=$this->devices_model->delete_device($id);
			header('Location:Display_devices');
			exit();
			
		}
		
		//update devices
		if($_GET['action']=='update')
		{
			$id =$_GET['param'];
			$id=trim($id);
			unlink($id);
					
			//device_id
			$this->load->model('devices_model');
			$results=$this->devices_model->Search_where($id);
			$data['devices_id']=$results['rows'];
					
					
			if($_POST['bltlupdate']=='Update')
				{
					$data = array
							(	
								$product_id=$this->input->post['txtproduct'],
								$user_id=$this->input->post['txtuser'],
								$imei=$this->input->post['txtimei'],
								$number_plate=$this->input->post['txtnumber'],
								$avatar=$_FILES['userfile']['name'],
								$created=$this->input->post['txtcreated']   
								
							);
					$devices_list=$this->devices_model->update_device($id);
					//update hinh
						$config['upload_path'] = './assets/images/avatar/';
						$config['allowed_types'] = 'gif|GIF|jpg|JPG|png|PNG|jpeg|GPEG';
						$config['max_size']	= '1000';
						$config['max_width']  = '1024';
						$config['max_height']  = '1024';
				
						$this->load->library('upload', $config);
				
						if ( ! $this->upload->do_upload())
						{
							$error = array('error' => $this->upload->display_errors());
							//$this->load->view('upload_form', $error);
						}
						else
						{
							
							$data['userfile'] = array('name' => 'userfile'); 
							$data = array('upload_data' => $this->upload->data());
							//$this->load->view('upload_success', $data);
						}
					header('Location:Display_devices');
					exit();
				}
		}
		//$content =  $this->load->view('devices/mana',$data);
		$content = $this->load->view('devices/mana',$data, true);
		$this->_render($content);
	}
	
}
		

