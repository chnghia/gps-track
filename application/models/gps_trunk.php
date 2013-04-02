<?php
/**
 * Class GPS_trunk
 */

class GPS_trunk extends GPS_Model 
{

	/**
	 * initialises the class inheriting the methods of the class Model 
	 *
	 * @return GPS_trunk
	 */
	function GPS_trunk()
	{
		parent::GPS_Model();
		
		$this->table = 'gps_trunk';
	}
	
	function get_all()
	{
		$this->db->select('*');
		$this->db->from($this->table);

		return $this->_get_results();		
	}
	
	/**
	 * From the parameter $id from the table news 
	 * retrieves the new and returns it
	 * 
	 * @param integer $id
	 * @return (New)
	 */
	function get_imei_list()
	{
		$this->db->distinct();
		$this->db->select('imei');
		$this->db->from($this->table);

		return $this->_get_results();
	}
	
	function get_lastest_point_of_imei($imei)
	{
		$this->db->select($this->table.'.*');
		$this->db->select('gps_userdevices.number_plate');
		$this->db->from($this->table);
		$this->db->join('gps_userdevices', 'gps_userdevices.imei = '.$this->table.'.imei', 'left');
		$this->db->order_by('created', 'DESC');
		$this->db->where($this->table.'.imei', $imei);
		$this->db->limit(1);
		
		return $this->_get_result();
	}
	
	function get_path_of_imei($imei, $from, $to)
	{
		$this->db->select('min(id) as id, imei, lat, lng, min(raw_data) as raw_data, min(created) as created');
		$this->db->from($this->table);
		$this->db->order_by('created', 'ASC');
		$this->db->group_by('lat, lng');
		$this->db->where('imei', $imei);
		$this->db->where('created >=', date('Y/m/d H:i:s', $from));
		$this->db->where('created <=', date('Y/m/d H:i:s', $to));
		return $this->_get_results();
	}
	
	function get_summary_of_imei_list($imei_list, $from, $to)
	{
		$this->db->select($this->table.'.id');
		$this->db->select('gps_userdevices.number_plate');
		$this->db->select($this->table.'.imei');
		$this->db->select($this->table.'.lat');
		$this->db->select($this->table.'.lng');
		$this->db->select($this->table.'.raw_data');
		$this->db->select($this->table.'.created');
		$this->db->from($this->table);
		$this->db->join('gps_userdevices', 'gps_userdevices.imei = '.$this->table.'.imei', 'LEFT');
		$this->db->order_by($this->table.'.created', 'ASC');
		$this->db->where_in($this->table.'.imei', $imei_list);
		$this->db->where($this->table.'.created >=', date('Y/m/d H:i:s', $from));
		$this->db->where($this->table.'.created <=', date('Y/m/d H:i:s', $to));
		return $this->_get_results();
	}
	
	/**
	 * Summary function: Get summary report
	 *  
	 * @param unknown_type $imei_list
	 * @param unknown_type $from
	 * @param unknown_type $to
	 */
	function get_summary_of_imei_list_11($imei_list, $from, $to)
	{
		$this->db->select('gps_userdevices.number_plate');
		$this->db->select($this->table.'.*');
		$this->db->select($this->table.'.speed', false);
		$this->db->from($this->table);
		$this->db->join('gps_userdevices', 'gps_userdevices.imei = '.$this->table.'.imei', 'LEFT');
		$this->db->order_by($this->table.'.created', 'ASC');
		$this->db->where_in($this->table.'.imei', $imei_list);
		$this->db->where('a2.speed = 0');
		$this->db->where($this->table.'.created >=', date('Y/m/d H:i:s', $from));
		$this->db->where($this->table.'.created <=', date('Y/m/d H:i:s', $to));
		return $this->_get_results();
	}
	
	function get_summary_of_imei_list_12($imei_list, $from, $to)
	{
		$this->db->select('gps_userdevices.number_plate');
		$this->db->select($this->table.'.*');
		$this->db->select($this->table.'.speed', false);
		$this->db->from($this->table);
		$this->db->join('gps_userdevices', 'gps_userdevices.imei = '.$this->table.'.imei', 'LEFT');
		$this->db->order_by($this->table.'.created', 'ASC');
		$this->db->where_in($this->table.'.imei', $imei_list);
		$this->db->where('a2.speed = 0');
		$this->db->where($this->table.'.created >=', date('Y/m/d H:i:s', $from));
		$this->db->where($this->table.'.created <=', date('Y/m/d H:i:s', $to));
		return $this->_get_results();
	}
	
	function get_summary_of_imei_list_all($imei_list, $from, $to)
	{
		$this->db->select('a2.imei', false);
		$this->db->select('dev.number_plate');
		$this->db->select('dev.over_speed');
		$this->db->select('DATE_FORMAT(a2.created, \'%Y-%m-%d\') as date', false);
		$this->db->select('sum(a2.distance) as distance');
		$this->db->select('(select count(*)*dev.timing from gps_trunk a1 where a1.imei = a2.imei and speed=0 and DATE_FORMAT(a1.created, \'%Y-%m-%d\') = DATE_FORMAT(a2.created, \'%Y-%m-%d\')) as stop_time', false);
		$this->db->select('count(*)*dev.timing as run_time', false);
		$this->db->select('AVG(a2.speed) as avg_speed', false);
		$this->db->select('MAX(a2.speed) as max_speed', false);
		$this->db->from('gps_trunk as a2');
		$this->db->join('gps_userdevices as dev', 'dev.imei = a2.imei', 'LEFT');
		$this->db->where_in('a2.imei', $imei_list);
		$this->db->where('a2.speed > 0');
		$this->db->where('a2.created >=', date('Y/m/d H:i:s', $from));
		$this->db->where('a2.created <=', date('Y/m/d H:i:s', $to));
		$this->db->group_by('a2.imei, DATE_FORMAT(a2.created, \'%Y-%m-%d\')');
		$this->db->order_by('dev.number_plate');
		$this->db->order_by('a2.created', 'ASC');
		return $this->_get_results();
	}
	
	function get_summary_of_imei_list_distance($imei_list, $from, $to)
	{
		$this->db->select('a2.imei', false);
		$this->db->select('dev.number_plate');
		$this->db->select('dev.over_speed');
		$this->db->select('DATE_FORMAT(a2.created, \'%Y-%m-%d\') as date', false);
		$this->db->select('sum(a2.distance) as distance');
		$this->db->select('(select count(*)*dev.timing from gps_trunk a1 where a1.imei = a2.imei and a2.speed=0 and DATE_FORMAT(a1.created, \'%Y-%m-%d\') = DATE_FORMAT(a2.created, \'%Y-%m-%d\')) as stop_time', false);
		$this->db->select('count(*)*dev.timing as run_time', false);
		$this->db->select('(select count(*)*dev.timing*dev.gas_of_stop from gps_trunk a1 where a1.imei = a2.imei and a2.speed=0 and DATE_FORMAT(a1.created, \'%Y-%m-%d\') = DATE_FORMAT(a2.created, \'%Y-%m-%d\')) as gas_stop', false);
		$this->db->select('count(*)*dev.timing*gas_of_run as run_gas', false);
		$this->db->select('((select count(*)*dev.timing*dev.gas_of_stop from gps_trunk a1 where a1.imei = a2.imei and a2.speed=0 and DATE_FORMAT(a1.created, \'%Y-%m-%d\') = DATE_FORMAT(a2.created, \'%Y-%m-%d\'))+count(*)*dev.timing*gas_of_run) as total_gas', false);
		$this->db->select('AVG(a2.speed) as avg_speed', false);
		$this->db->select('MAX(a2.speed) as max_speed', false);
		$this->db->from('gps_trunk as a2');
		$this->db->join('gps_userdevices as dev', 'dev.imei = a2.imei', 'LEFT');
		$this->db->where_in('a2.imei', $imei_list);
		$this->db->where('a2.speed > 0');
		$this->db->where('a2.created >=', date('Y/m/d H:i:s', $from));
		$this->db->where('a2.created <=', date('Y/m/d H:i:s', $to));
		$this->db->group_by('a2.imei, DATE_FORMAT(a2.created, \'%Y-%m-%d\')');
		$this->db->order_by('dev.number_plate');
		$this->db->order_by('a2.created', 'ASC');
		return $this->_get_results();
	}
	
	/**
	 * Enter description here ...
	 * 
	 * SELECT a2.imei, dev.number_plate, dev.over_speed, DATE_FORMAT(a2.created, '%Y-%m-%d %H:00:00') as date, count(*)*30 as timing, min(lat) as start_lat, min(lng) as start_lng, max(lat) as end_lat, max(lng) as end_lng
	 * FROM (gps_trunk as a2)
	 * LEFT JOIN gps_userdevices as dev ON dev.imei = a2.imei
	 * WHERE a2.imei IN ('00001')
	 * GROUP BY a2.imei, DATE_FORMAT(a2.created, '%Y-%m-%d %H:00:00')
	 * ORDER BY dev.number_plate, DATE_FORMAT(a2.created, '%Y-%m-%d %H:00:00') ASC
	 * 
	 * @param unknown_type $imei_list
	 * @param unknown_type $from
	 * @param unknown_type $to
	 */
	function get_summary_of_imei_list_usage($imei_list, $from, $to)
	{
		$this->db->select('a2.imei', false);
		$this->db->select('dev.number_plate');
		$this->db->select('dev.over_speed');
		$this->db->select('sum(a2.distance) as distance');
		$this->db->select('DATE_FORMAT(a2.created, \'%Y-%m-%d %H:00:00\') as date', false);
		$this->db->select('count(*)*30 as timing');
		$this->db->select('min(lat) as start_lat');
		$this->db->select('min(lng) as start_lng');
		$this->db->select('max(lat) as end_lat');
		$this->db->select('max(lng) as end_lng');
		$this->db->from('gps_trunk as a2');
		$this->db->join('gps_userdevices as dev', 'dev.imei = a2.imei', 'LEFT');
		$this->db->where_in('a2.imei', $imei_list);
		//$this->db->where('a2.raw_data not like \'speed=0\'');
		$this->db->where('a2.created >=', date('Y/m/d H:i:s', $from));
		$this->db->where('a2.created <=', date('Y/m/d H:i:s', $to));
		$this->db->group_by('a2.imei, DATE_FORMAT(a2.created, \'%Y-%m-%d %H:00:00\')');
		$this->db->order_by('dev.number_plate');
		$this->db->order_by('DATE_FORMAT(a2.created, \'%Y-%m-%d %H:00:00\')', 'ASC');
		
		return $this->_get_results();
	}
}
?>