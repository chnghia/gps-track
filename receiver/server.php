#!/usr/local/bin/php -q
<?php
/**
 * GPS receiver server
 * 
 * TLT-2H GPS upload data format
 * Upload format:
 * 
 * #IMEI # user name #service password #condition
 *
 * # data quantity #the base station's information $GPRMC .... 
 * # the base station�fs information $GPRMC .... ##
 *
 */
require ('classes/KLogger.php');
require ('util.php');

define('KNOTS', 1.85200);
define('INSERT_QUERY', "INSERT INTO gps_trunk(`imei`, `lat`, `lng`, `raw_data`, `speed`, `distance`) VALUES ('%s', '%s', '%s', '%s', '%s', '%s')");
define('INSERT_QUERY_WITH_TIME', "INSERT INTO gps_trunk(`imei`, `lat`, `lng`, `raw_data`, `created`, `speed`, `distance`) VALUES ('%s', '%s', '%s', '%s', '%s', '%s', '%s')");
define('SELECT_RECENT_LOCATION', "select * from gps_trunk where `imei`='%s' AND `created` >= '%s' order by id desc limit 1;");

error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
ini_set('mbstring.func_overload', '0');
ini_set('output_handler', '');
@ob_end_flush();
set_time_limit(0);

global $log;
$log = new KLogger ( "/srv/www/GPStrack.com/htdocs/receiver/log/" , KLogger::DEBUG );

error_reporting(E_ALL);

// Set time limit to indefinite execution 
set_time_limit (0); 

// Set the ip and port we will listen on 
$address = "192.168.1.20"; 
$port = 8020; 
$max_clients = 10; 

$sql_host = "localhost";
$sql_username = "root";
$sql_password = "Camsong@2010";
$db_name = "GPStrack_dev";
global $db_conn;

// Array that will hold client information 
$clients = Array(); 
// Create a TCP Stream socket 
$socket = @socket_create(AF_INET, SOCK_STREAM, SOL_TCP);

@socket_set_option($socket, SOL_SOCKET, SO_REUSEADDR, 1);

$log->logInfo("Listening on $address:$port ..."); 

// Bind the socket to an address/port 
@socket_bind($socket, $address, $port) or $log->logError('Could not bind to address'); 
// Start listening for connections 
@socket_listen($socket, $max_clients); 

$clients = array('0' => array('socket' => $socket));

$db_conn = mysql_connect( $sql_host, $sql_username, $sql_password ) OR die(mysql_error());
mysql_select_db( $db_name, $db_conn ) OR die(mysql_error());

//$query  = "SELECT id, member_number, firstname, lastname, crm_guid FROM members_voting";
//$result = mysql_query($query);
//while ($row = mysql_fetch_assoc($result)) {
//		$member_rowset[$row['member_number']] = $row;
//}

//Decimal Degrees = Degrees + minutes/60 + seconds/3600
function decimal_convert($degree, $min, $second)
{
	global $log;
	$a = (float) $degree;
	$b = (float)( $min.'.'.$second) / 60 ;
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
	global $log;
	$gps_data = array();
	$arr = explode('&', $data);
	
	foreach ($arr as $item) {
		if (startsWith('$$', $item)) {
			// ID
			$gps_data['imei'] = str_replace('?', '', substr($item, 2));
			//$log->logInfo('Parser ID: ' . $gps_data['imei']);
		} elseif (startsWith('A', $item)) {
			// A Column
		} elseif (startsWith('B', $item)) {
			$item = substr($item, 1);
			parser_gprmc($item, $gps_data);
			
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
	global $log;
	$gps_data_list = array();
	//$gps_data = array();
	
	$data = substr($data, 1);
	$arr = explode('#', $data);
	
	//for ($i=0; $i<count($arr); $i++)
	//{
	//	echo "ID : " . $arr[$i] . "\n";
	//}
	
	// 0 : ID
	//echo "ID : " . $arr[0] . "\n";
	//$gps_data['imei'] = $arr[0];
	// 1 : skip
	// 2 : skip
	// 3 : skip
	// 4 : Number of GPRMC
	if (isset($arr[4]) && $arr[4] > 0) {
		
		for ($i=0; $i<$arr[4]; $i++) {
			$gps_data = array();
		    // Set 0:ID
			$gps_data['imei'] = $arr[0];
			
			// remove GPRMC ID
			$pos = strpos($arr[5+$i], '$GPRMC,');
			$gprmc = substr($arr[5+$i], $pos + 7);
			//$log->logInfo( 'position: ' . $pos);
			//$log->logInfo( 'GPRMC ' . $i.': ' . $gprmc);
			parser_gprmc($gprmc, $gps_data);
			
			$gps_data_list[] = $gps_data;
			//break;
		}
	}
	
	if (count($gps_data_list) == 1)
		return $gps_data;
	
	return $gps_data_list;
}

function gps_parser_vt300($data)
{
	global $log;
	$gps_data = array();
	
	$imei = substr($data, 4, 7);
	
	$gps_data['imei'] = str_replace('f', '', strhex($imei));
	//$log->logInfo('gps_parser_vt300: '. $gps_data['imei']);
	
	$data = substr($data, 13);
	$arr = explode('|', $data);
	
	parser_gprmc($arr[0], $gps_data);
	
	//$log->logInfo('gps_parser_vt300: '. $arr[0]);
	
	return $gps_data;
}

// 
// GPS Data Parser Function
//
// DEVICE TLT-2H:
// #IMEI # user name #service password #condition# data quantity 
// #the base station�s information$GPRMC��.. 
// # the base station�s information$GPRMC��. ## 
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
// DEVICE VT300
// $$<L><ID><0x9955><data><checksum>\r\n
function gps_parser($data, $insertdb=true)
{
	global $log;
	
	//$data = '$$3037913502????&A9955&B085343.498,V,1046.8735,N,10640.0846,E,3,0.00,311210,,,N*67||&C0000000000&D000023:2&E00000000&Y00000000##';
	$log->logInfo('Parser data: '. $data);
	
	$gps_data = array();
	
	// switch case for GPS DEVICES
	if (startsWith('$$', $data) && endsWith('##', $data)) {
		// switch case for VT310
		$log->logInfo('Parser device: VT310');
		$gps_data = gps_parser_vt310($data);
	} elseif (startsWith('#', $data) && endsWith('##', $data)) {
		// switch case for TLT-2H
		$log->logInfo('Parser device: TLT-2H');
		$gps_data = gps_parser_tlt2h($data);
	} elseif (startsWith('$$', $data) /*&& endsWith(hexstr("20"), $data)*/) {
		$log->logInfo('Parser device: VT300');
		$gps_data = gps_parser_vt300($data);
	} else {
		$log->logInfo('Cannot parser device');
	}

	if (is_array($gps_data) && $insertdb) {
		if (isset($gps_data['imei']) && isset($gps_data['lat']) && isset($gps_data['long'])) {
			// Get distance from previous location
			$query = sprintf(SELECT_RECENT_LOCATION, $gps_data['imei'], date('Y/m/d H:i:s', strtotime("-10 minutes")));
			if (!$result=mysql_query($query)) {
				$log->logInfo('MySQL Error: ' .  mysql_error());
			}
			if (mysql_num_rows($result) > 0) {
				$row = mysql_fetch_assoc($result);
				$distance = distance($row['lat'], $row['lng'], $gps_data['lat'], $gps_data['long']);
				$gps_data['distance'] = round($distance, 5);
			} else {
				$gps_data['distance'] = 0;
			}
			
			$query = sprintf(INSERT_QUERY, 
			                 $gps_data['imei'], 
			                 $gps_data['lat'], 
			                 $gps_data['long'], 
			                 mysql_real_escape_string("speed={$gps_data['speed']}"),
			                 $gps_data['speed'],
			                 $gps_data['distance']
			                 );
			//$log->logInfo('MySQL Query: ' .  $query);
			if (!mysql_query($query)) {
				$log->logInfo('MySQL Error: ' .  mysql_error());
			}
		} elseif (count($gps_data) > 0) {
			for ($i=0; $i<count($gps_data); $i++) {
				if (isset($gps_data[$i]['imei']) && isset($gps_data[$i]['lat']) && isset($gps_data[$i]['long'])) {
					//Get distance from previous location
					$query = sprintf(SELECT_RECENT_LOCATION, $gps_data[$i]['imei'], date('Y/m/d H:i:s', strtotime("-10 minutes")));
					if (!$result=mysql_query($query)) {
						$log->logInfo('MySQL Error: ' .  mysql_error());
					}
					if (mysql_num_rows($result) > 0) {
						$row = mysql_fetch_assoc($result);
						$distance = distance($row['lat'], $row['lng'], $gps_data[$i]['lat'], $gps_data[$i]['long']);
						$gps_data[$i]['distance'] = round($distance, 5);
					} else {
						$gps_data[$i]['distance'] = 0;
					}
					
					$seconds = (count($gps_data)-$i)*30;
					$query = sprintf(INSERT_QUERY_WITH_TIME, 
					                 $gps_data[$i]['imei'], 
									 $gps_data[$i]['lat'], 
									 $gps_data[$i]['long'], 
									 mysql_real_escape_string("speed={$gps_data[$i]['speed']}"),
									 date('Y/m/d H:i:s', strtotime("- $seconds seconds")),
									 $gps_data[$i]['speed'],
									 $gps_data[$i]['distance']);
					//$log->logInfo('MySQL Query: ' .  $query);
					if (!mysql_query($query)) {
						$log->logInfo('MySQL Error: ' .  mysql_error());
					}
				}
			}
			//$log->logInfo('Skip gps_data: ' .  print_r($gps_data, true));
		} else {
		}
	}
	
	return $gps_data;
}

function readSocketForDataLength ($socket, $len)
{
    $offset = 0;
    $socketData = '';
    
    while ($offset < $len) {
        if (($data = @socket_read ($socket, $len-$offset)) === false) {
            return false;
        }
        
        $dataLen = strlen ($data);
        $offset += $dataLen;
        $socketData .= $data;
        
        if ($dataLen == 0) { break; }
    }

    return $socketData;
}

Function hexstr($hexstr) {
  $hexstr = str_replace(' ', '', $hexstr);
  $hexstr = str_replace('\x', '', $hexstr);
  $retstr = pack('H*', $hexstr);
  return $retstr;
}

Function strhex($string) {
  $hexstr = unpack('H*', $string);
  return array_shift($hexstr);
}

/*
$data = '#357671030278938#V500#0000#AUTOLOW#7
#5187a37d$GPRMC,093533.000,A,1046.8759,N,10640.0930,E,0.00,153.38,120111,,,A*66
#5187a37d$GPRMC,093744.000,A,1046.8864,N,10640.0860,E,0.00,146.70,120111,,,A*69
#5187a37d$GPRMC,093953.000,A,1046.8864,N,10640.0860,E,0.00,146.70,120111,,,A*61
#5187a37d$GPRMC,094153.000,A,1046.8864,N,10640.0860,E,0.00,146.70,120111,,,A*6E
#5187a37d$GPRMC,094439.000,A,1046.8770,N,10640.0818,E,0.00,133.66,120111,,,A*67
#5187a37d$GPRMC,094639.000,A,1046.8770,N,10640.0818,E,0.00,133.66,120111,,,A*65
#5187a37d$GPRMC,094840.000,A,1046.8772,N,10640.0859,E,0.00,154.88,120111,,,A*63
##';

$data = hexstr('24 24 00 7E 00 00 1F FF FF FF FF 99 55 30 37 31 35 30 30 2E 30 30 30 2C 41 2C 31 30 34 36 2E 31 34 39 39 2C 4E 2C 31 30 36 34 31 2E 38 31 39 31 2C 45 2C 30 2E 30 30 2C 31 35 39 2C 32 30 30 34 31 31 2C 2C 2A 30 41 7C 31 2E 31 7C 33 32 7C 32 30 30 30 7C 30 31 30 38 2C 30 30 46 39 7C 30 31 43 34 30 30 30 32 30 30 45 41 30 43 35 42 7C 31 36 7C 30 30 31 37 39 39 30 32 43 56 20 0D 0A');

echo "PARSER :" . print_r(gps_parser($data, false) ,true);
*/

// Loop continuously 
while(TRUE) {
	$read[0] = $socket;
	for ($i=1;$i<count($clients)+1;$i++) {
		if (isset($clients[$i]) && $clients[$i] != NULL) {
			$read[$i+1] = $clients[$i]['socket'];
		}
	}

	$ready = @socket_select($read, $write=NULL, $except=NULL, 0);

	if (in_array($socket,$read)) {
		for ($i=1;$i<$max_clients+1;$i++) {
			if (!isset($clients[$i])) {
				$clients[$i]['socket'] = @socket_accept($socket);
				@socket_getpeername($clients[$i]['socket'],$ip);
				$clients[$i]['ipaddy'] = $ip;

				//socket_write($clients[$i]['socket'],"Welcome to my Custom Socket Server \r\n");
				//socket_write($clients[$i]['socket'],"There are " . (count($clients) - 1) . " client(s) connected to this server. \r\n"); 

				$log->logInfo("New client connected: " . $clients[$i]['ipaddy']);
				break;
			}
			elseif ($i == $max_clients - 1) {
				$log->logInfo("To many Clients connected!");
				die;
				//break;
			}
			if ($ready < 1) {
				usleep(100000); // Sleep for 100 miliseconds;
				continue;
			}
		}
	} else {
		usleep(100000); // Sleep for 100 miliseconds;
	}
	
	for ($i=1; $i<$max_clients+1; $i++) {
		if (isset($clients[$i]['socket']) && in_array($clients[$i]['socket'], $read)) {

			//$data = readSocketForDataLength($clients[$i]['socket'], 4096);
			//$data = '';
			//while(($buf = socket_read($clients[$i]['socket'], 2048))!== false)
			//{
			//	$data .= $buf;
			//	if(preg_match("'\r\n\r\n$'s", $data)) // only need to get headers
			//	break;
			//}
			$data = @socket_read($clients[$i]['socket'], 4096, PHP_BINARY_READ);

			//$log->logInfo( $clients[$i]['ipaddy'] . " says before: $data \r\n");
			//echo $data;
			if ($data === FALSE ) {
				unset($clients[$i]);
				$log->logInfo( "Client disconnected!" );
				usleep(100000); // Sleep for 100 miliseconds;
				continue;
			}
			
			$data = trim($data);

			if (!empty($data)) {
				// TODO: Get data and parser here
				//$log->logInfo( $clients[$i]['ipaddy'] . " says: $data");
				$gps_data = gps_parser($data);
				
				//$log->logInfo( "close connection ". $clients[$i]['ipaddy'] ."!" );
				//socket_close($clients[$i]['socket']);
				//unset($clients[$i]);
				//continue;
			}	
			unset($clients[$i]);
			continue;
		}
	}
}