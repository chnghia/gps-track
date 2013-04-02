<?php
/**
 * Class devices
 */

class users_model extends GPS_Model 
{

	/**
	 * initialises the class inheriting the methods of the class Model 
	 *
	 * @return GPS_trunk
	 */
	//function GPS_trunk()
	//{
		//parent::GPS_Model();
		//$this->table ='gps_products';
	//}
	function Search()
	{
		$q = $this->db->select('*')
		 	->from('users');
			//->limit($limit,$offset);
		$ret['rows']=$q->get()->result();
		
		//gggg
		//$q = $this->db->select('COUNT(*) as count',FALSE)
		 	//->from('gps_userdevices');
			
		//$tmp= $q->get()->result();
		//$ret['num_rows']=$tmp[0]->count;
		
		return $ret;
	}
}
