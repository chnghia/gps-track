<?php
/**
 * Class GPS_userdevices
 */

class GPS_userdevices extends GPS_Model 
{

	/**
	 * initialises the class inheriting the methods of the class Model 
	 *
	 * @return GPS_userdevices
	 */
	function GPS_userdevices()
	{
		parent::GPS_Model();
		
		$this->table = 'gps_userdevices';
	}
	
	function get_device_detail($device_id)
	{
		$this->db->select($this->table.'.*');
		$this->db->select('gps_products.name as product_name');
		$this->db->from($this->table);
		$this->db->join('gps_products', 'gps_products.id = '.$this->table.'.product_id', 'LEFT');
		$this->db->where($this->table.'.id', $device_id);
		
		return $this->_get_result();
	}

	/**
	 * From the parameter $id from the table news 
	 * retrieves the new and returns it
	 * 
	 * @param integer $id
	 * @return (New)
	 */
	function get_user_devices($user_id)
	{
		$this->db->select($this->table.'.*');
		$this->db->select('gps_products.name as product_name');
		$this->db->from($this->table);
		$this->db->join('gps_products', 'gps_products.id = '.$this->table.'.product_id', 'LEFT');
		$this->db->where('user_id', $user_id);
		$this->db->order_by($this->table.'.group_name');
		$this->db->order_by($this->table.'.number_plate');
		
		return $this->_get_results();
	}
	
	function get_user_devices_by_group($user_id)
	{
		$results = $this->get_user_devices($user_id);
		
		$ret = array();
		foreach ($results as $row) {
			if (!$row['group_name']) $row['group_name'] = "Nhóm";
			$ret[$row['group_name']][] = $row;
		}
		
		return $ret;
	}
	
	function delete_device($device_id) 
	{
		$this->db->where('id', $device_id);
		$this->db->delete($this->table);
	}
	
	function update_userdevices($device_id, $device)
	{
		$this->db->where('id', $device_id);
		$this->db->update($this->table, $device);
		return true; 
	}
}
?>