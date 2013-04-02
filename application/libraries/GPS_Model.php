<?php
/**
 * Master Model for GPSTracking
 */
class GPS_Model extends Model
{
	var $table = null;
	
	/*
    var $p = array();	    // properties of the table
    var $primaryKeys = array();
    var $insertID = null;
	*/

    // public methods
    function GPS_Model()
    {
		parent::Model();
    }

	function get_detail($id)
	{
		$this->_checkTable();
		$this->db->where($id);
		
		return _get_result();
	}
	
	function _get_result($query = null)
	{
		if (!$query)
			$query = $this->db->get();
		
		if ($query->num_rows() == 1) {
			return $query->row_array();
		}
		
		return false;
	}
	
	function _get_results($query = null)
	{
		if (!$query)
			$query = $this->db->get();
		
		log_message('debug', 'QUERY: ' . $this->db->last_query());
			
		if ($query->num_rows() > 0) {
			return $query->result_array();
		}
		
		return false;
	}
	
	
    /**
     * Returns an array of stdObjects for the configured table
     *
     * @access public
     * /
    function fetch_all()
    {
				$this->_checkTable();
				$query = $this->db->get($this->table);
				return $query->result();
	
    }

    /**
     * Retrieves a record from the database for the object and sets it's properties.
     * $id can be a scalar or an array for a mutli-field primary key
     *
     * @access public
     * /
    function fetch($id)
    {
				$this->_checkTable();
				$query = $this->db->getwhere($this->table,$this->_createPrimaryKeyList($id));

				if (count($query->result_array()) == 1)
				{
						$this->_processFields($query);
						return true;
				}

				return false;
    }

    /**
     * Generic field value retrival method
     *
     * @access public
     * /
    function get($key)
    {
				if (isset($this->p[$key]))
				{
						return $this->p[$key];
				}
				else
				{
						return null;
				}
    }

    /**
     * Generic field setter method
     *
     * @access public
     * /
    function set($key,$value)
    {
				$this->p[$key] = $value;
    }

    /**
     * Generic check to see if a field is set or not
     *
     * @access public
     * /
    function isKeySet($key)
    {
				return isset($this->p[$key]);
    }

    /**
     * Generic method to unset a field in the properties array
     *
     * @access public
     * /
    function unsetKey($key)
    {
				unset($this->p[$key]);
    }

    /**
     * Performs a save operation on the object. If there are IDs and the record exists in the DB, then an update is run,
     * otherwise an insert is run.
     *
     * @access public
     * /
    function save()
    {
				$this->_checkTable();

				// we check for an ID and that there is a database record before we know we need to update
				if ($this->_isIDSet() && $this->_recordExists($this->_idList()))
				{
						// run update 
						$whereList = $this->_createPrimaryKeyList($this->_idList());
						$results = $this->db->update($this->table,$this->p,$whereList);

						if ($results)
						{
								return true;
						}
						else
						{
								return false;
						}
				}
				else
				{
						// run insert 
						$this->db->insert($this->table,$this->p);
						$this->insertID = $this->db->insert_id();
						if ($this->db->insert_id() != 0)
						{
								return true;
						}
						else
						{
								return false;
						}
				}
    }

    /**
     * Deletes the record. It's still valid until the end of the request.
     *
     * @access public
     * /
    function delete()
    {
				$this->_checkTable();

				$conditions = $this->_createPrimaryKeyList($this->_idList());
				$results = $this->db->delete($this->table,$conditions);
				if ($results)
				{
						return true;
				}
				else
				{
						return false;
				}
    }

    function extendObject($stdObject)
    {
				foreach ($stdObject as $key=>$value)
				{
						$this->p[$key] = $value;
				}
    }

    // private methods
    function _processFields($query)
    {
				$list = $query->result_array();
				$this->p = $list[0];
		}

		function _checkTable()
		{
				if ($this->table === null)
				{
						exit(get_class($this) . " needs to specify a table");
				}		
    }

    /**
     * Generates an associative array of key names and values that can be used in queries to the database.
     * The parameter $ids can be a single value or an array. If it's a single value, it's turned into an array of one value.
     * New values from $ids are used until the values run out, then the last is used repeadedly. Normally, the values received should
     * match the number in the primaryKey list.
     *
     * @access private
     * /
    function _createPrimaryKeyList($ids)
    {
				$data = array();
				$keyLength = count($this->primaryKeys);

				if (!is_array($ids)) $ids = array($ids);
				$idsLength = count($ids);

				for ($x = 0; $x < $keyLength; $x++)
				{
						$keyName = $this->primaryKeys[$x];
						if ($x < $idsLength)
						{
								$idValue = $ids[$x];
						}
						else
						{
								$idValue = $ids[$idsLength - 1];
						}

						$data[$keyName] = $idValue;
				}

				return $data;
    }

    function _idList()
    {
				$idList = array();
				foreach ($this->p as $key=>$value)
				{
						if (in_array($key,$this->primaryKeys))
						{
								$idList[] = $value;
						}
				}

				return $idList;
    }

    function _isIDSet()
    {
				$ids = $this->_idList();	
				if (count($ids) > 0)
				{
						return true;
				}
				else
				{
						return false;
				}
    }
    
    function _recordExists($id)
    {
				$this->_checkTable();
				$query = $this->db->getwhere($this->table,$this->_createPrimaryKeyList($id));

				if ($query->num_rows() > 0)
				{
						return true;
				}
				else
				{
						return false;
				}
    }
		*/
}

?>
