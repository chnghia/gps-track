<?
/*
*/
define('KNOTS', 1.85200);
define('INSERT_QUERY', "INSERT INTO gps_trunk(`imei`, `lat`, `long`, `raw_data`) VALUES ('%s', '%s', '%s', '%s')");

include("classes/KLogger.php");
include("util.php");

class gpsServer extends socketServer {
}

class gpsServerClient extends socketServerClient {
	private $max_total_time = 45;
	private $max_idle_time  = 15;
	private $keep_alive = false;
	private $accepted;
	private $last_action;
	private $log;
	private $db_conn;
	private $db_name = 'GPStrack_local';
	private $sql_host = "localhost";
	private $sql_username = "root";
	private $sql_password = "";
	
	public function __construct()
	{
		$this->log = new KLogger ( "log/" , KLogger::DEBUG );
		$this->db_conn = mysql_connect( $this->sql_host, $this->sql_username, $this->sql_password ) OR die(mysql_error());
		mysql_select_db( $this->db_name, $this->db_conn ) OR die(mysql_error());
	}
	
	//Decimal Degrees = Degrees + minutes/60 + seconds/3600
	function decimal_convert($degree, $min, $second)
	{
		$a = (float) $degree;
		$b = (float) ($min.'.'.$second) / 60 ;
		//$c = ( float ) ($second/3600);
		$dbl = $a + $b;// + $c;
		//$log->logInfo("Convert $degree: $min to $a: $b to $dbl");
		return $dbl;
	}

	function parser_gprmc($item, &$gps_data)
	{
			// B Column: hhmmss.dd , S                   , xxmm.dddd , <N|S>        , yyymm.dddd  , <E|W>     , s.s          ,   h.h       , ddmmyy
			//           UTC Time  , A: Valid/V: Invalid , Latitude  , North/South  , Longtitude  , East/West , Speed(knots) ,Heading(deg) , Date
			$gps_arr = explode(',', $item);
			
			//$log->logInfo('Parser ID: ' . print_r($gps_arr, true));
			if (count($gps_arr) > 6 ) {
				$gps_data['utc_time'] = $gps_arr[0];
				$gps_data['valid'] = ($gps_arr[1] == 'A')? true : false;
				//$lat = sprintf("%s.%s%s", substr($gps_arr[2], 0, 2), substr($gps_arr[2], 2, 2), substr($gps_arr[2], 5, 4));
				//$lat = (( float ) $gps_arr[2]) / 100;
				$lat = decimal_convert(substr($gps_arr[2], 0, 2), substr($gps_arr[2], 2, 2), substr($gps_arr[2], 5, 4));
				//$log->logInfo('Parser Lat before: ' . $gps_arr[2]);
				//$log->logInfo('Parser Lat: ' . $lat);
				$gps_data['lat'] = ($gps_arr[3] == 'N')? $lat : 0-$lat;
				
				//$long = sprintf("%s.%s%s", substr($gps_arr[4], 0, 3), substr($gps_arr[4], 3, 2), substr($gps_arr[4], 6, 4));
				//$long = (( float ) $gps_arr[4]) / 100;
				$long = decimal_convert(substr($gps_arr[4], 0, 3), substr($gps_arr[4], 3, 2), substr($gps_arr[4], 6, 4));
				//$log->logInfo('Parser Long before: ' . $gps_arr[4]);
				//$log->logInfo('Parser Long: ' . $long);
				$gps_data['long'] = ($gps_arr[5] == 'E')? $long : 0-$long;
				
				$gps_data['speed'] = (($gps_arr[6])? (float) $gps_arr[6] : 0) * KNOTS;
			}
	}

	function gps_parser_vt310($data)
	{
			$gps_data = array();
			$arr = explode('&', $data);
			
			foreach ($arr as $item) {
				if (startsWith('$$', $item)) {
					// ID
					$gps_data['imei'] = str_replace('?', '', substr($item, 2));
					$this->log->logInfo('Parser ID: ' . $gps_data['imei']);
				} elseif (startsWith('A', $item)) {
					// A Column
				} elseif (startsWith('B', $item)) {
					$item = substr($item, 1);
					$this->parser_gprmc($item, $gps_data);
					
				} elseif (startsWith('C', $item)) {
					// C Column
				} elseif (startsWith('D', $item)) {
					// D Column
				} elseif (startsWith('E', $item)) {
					// E Column
				} elseif (startsWith('Y', $item)) {
					// Y Column
				}
			}
			return $gps_data;
	}

	function gps_parser_tlt2h($data)
	{
			$gps_data = array();
			
			$data = substr($data, 1);
			$arr = explode('#', $data);
			
			//for ($i=0; $i<count($arr); $i++)
			//{
			//	echo "ID : " . $arr[$i] . "\n";
			//}
			
			// 0 : ID
			//echo "ID : " . $arr[0] . "\n";
			$gps_data['imei'] = $arr[0];
			// 1 : skip
			// 2 : skip
			// 3 : skip
			// 4 : Number of GPRMC
			if (isset($arr[4]) && $arr[4] > 0) {
				for ($i=0; $i<$arr[4]; $i++) {
					// remove GPRMC ID
					$pos = strpos($arr[5+$i], '$GPRMC,');
					$gprmc = substr($arr[5+$i], $pos + 7);
					$this->log->logInfo( 'position: ' . $pos);
					$this->log->logInfo( 'GPRMC ' . $i.': ' . $gprmc);
					$this->parser_gprmc($gprmc, $gps_data);
					break;
				}
			}
			
			return $gps_data;
	}

	// 
	// GPS Data Parser Function
	//
	// DEVICE TLT-2H:
	// #IMEI # user name #service password #condition# data quantity 
	// #the base station’s information$GPRMCE.. 
	// # the base station’s information$GPRMCE. ## 
	// #357671030278938#V500#0000#AUTOLOW#7
	// #5187a37d$GPRMC,093533.000,A,1046.8759,N,10640.0930,E,0.00,153.38,120111,,,A*66
	// #5187a37d$GPRMC,093744.000,A,1046.8864,N,10640.0860,E,0.00,146.70,120111,,,A*69
	// #5187a37d$GPRMC,093953.000,A,1046.8864,N,10640.0860,E,0.00,146.70,120111,,,A*61
	// #5187a37d$GPRMC,094153.000,A,1046.8864,N,10640.0860,E,0.00,146.70,120111,,,A*6E
	// #5187a37d$GPRMC,094439.000,A,1046.8770,N,10640.0818,E,0.00,133.66,120111,,,A*67
	// #5187a37d$GPRMC,094639.000,A,1046.8770,N,10640.0818,E,0.00,133.66,120111,,,A*65
	// #5187a37d$GPRMC,094840.000,A,1046.8772,N,10640.0859,E,0.00,154.88,120111,,,A*63
	// ##
	// DEVICE VT310
	// $$3037913502????&A9955&B085343.498,V,1046.8735,N,10640.0846,E,,0.00,311210,,,N*67||&C0000000000&D000023:2&E00000000&Y00000000##
	function gps_parser($data, $insertdb=true)
	{		
		//$data = '$$3037913502????&A9955&B085343.498,V,1046.8735,N,10640.0846,E,3,0.00,311210,,,N*67||&C0000000000&D000023:2&E00000000&Y00000000##';
		$this->log->logInfo('Parser data: '. $data);
		
		$gps_data = array();
		
		// switch case for GPS DEVICES
		if (startsWith('$$', $data) && endsWith('##', $data)) {
			// switch case for VT310
			$this->log->logInfo('Parser device: VT310');
			$gps_data = $this->gps_parser_vt310($data);
		} elseif (startsWith('#', $data) && endsWith('##', $data)) {
			// switch case for TLT-2H
			$this->log->logInfo('Parser device: TLT-2H');
			$gps_data = $this->gps_parser_tlt2h($data);
		} else {
			$this->log->logInfo('Cannot parser device');
		}

		if (is_array($gps_data) && $insertdb) {
			if (isset($gps_data['imei']) && isset($gps_data['lat']) && isset($gps_data['long'])) {
				$query = sprintf(INSERT_QUERY, $gps_data['imei'], $gps_data['lat'], $gps_data['long'], mysql_real_escape_string("speed={$gps_data['speed']}"));
				//$log->logInfo('MySQL Query: ' .  $query);
				if (!mysql_query($query, $this->db_conn)) {
					$this->log->logInfo('MySQL Error: ' .  mysql_error());
				}
			} else {
				$this->log->logInfo('Skip gps_data: ' .  print_r($gps_data, true));
			}
		}
		
		return $gps_data;
	}
	
	/**
	 *
	 *
	 */
	public function on_read()
	{
		$this->last_action = time();
		$this->read_buffer = trim($this->read_buffer);
		if (!empty($this->read_buffer)) {
			$this->gps_parser($this->read_buffer);
				
			$this->log->logInfo("[gpsServerClient] {$this->remote_address} disconnected!" );
			$this->read_buffer  = '';
		}
	}

	public function on_connect()
	{
		//echo "[gpsServerClient] accepted connection from {$this->remote_address}\n";
		$this->log->logInfo("[gpsServerClient] accepted connection from {$this->remote_address}");
		$this->accepted    = time();
		$this->last_action = $this->accepted;
	}
	
	public function on_disconnect()
	{
		//echo "[gpsServerClient] {$this->remote_address} disconnected\n";
		$this->log->logInfo("[gpsServerClient] {$this->remote_address} disconnected!");
	}
	
	public function on_write()
	{
		if (strlen($this->write_buffer) == 0 && !$this->keep_alive) {
			$this->disconnected = true;
			$this->on_disconnect();
			$this->close();
		}
	}

	public function on_timer()
	{
		$idle_time  = time() - $this->last_action;
		$total_time = time() - $this->accepted;
		if ($total_time > $this->max_total_time || $idle_time > $this->max_idle_time) {
			$this->log->logInfo("[gpsServerClient] Client keep-alive time exceeded ({$this->remote_address})");
			$this->close();
		}
	}
}