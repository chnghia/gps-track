<?php

if ( ! function_exists('form_hours_dropdown'))
{
	function form_hours_dropdown($id, $selected=null, $extra = '')
	{
		/*** range of hours ***/
		$h = range(0, 23);
		$m = array(0, 15, 30, 45);

		/*** current hour ***/
		$selected = is_null($selected) ? date('H').':00' : $selected;

		$select = "<select name=\"$id\" id=\"$id\">\n";
		foreach ($h as $hour)
		{
			foreach ($m as $minute) {
				$item = sprintf('%02d:%02d',$hour,$minute);
				$select .= "<option value=\"$item\"";
				$select .= ($item==$selected) ? ' selected="selected"' : '';
				$select .= ">&nbsp;&nbsp;$item</option>\n";
			}
		}
		$select .= '</select>';
		return $select;
	}
}

if ( ! function_exists('rawdata_refine'))
{
	function rawdata_refine($raw_data) {
		if (!$raw_data) return null;
		$ci = &get_instance();
		$raws = explode("|", $raw_data);
		if (!is_array($raws)) $raws = array($raw_data);
		
		$ret = array();
		foreach ($raws as $row) {
			$parts = explode("=", $row);
			$title = $ci->lang->line('rawdata_'.$parts[0]);
			if ($title) {
				$ret[] = $title . ": " . $parts[1]; 
			} else {
				$ret[] = $parts[0] . ": " . $parts[1];
			}
		}
		
		return implode("<br/>", $ret);
	}
}

if ( ! function_exists('rawdata_refine_array'))
{
	function rawdata_refine_array($raw_data) {
		if (!$raw_data) return null;
		$ci = &get_instance();
		$raws = explode("|", $raw_data);
		if (!is_array($raws)) $raws = array($raw_data);
		
		$ret = array();
		foreach ($raws as $row) {
			$parts = explode("=", $row);
			$ret[$parts[0]] = $parts[1];
		}
		
		return $ret;
	}
}

/*::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::*/
/*::                                                                         :*/
/*::  this routine calculates the distance between two points (given the     :*/
/*::  latitude/longitude of those points). it is being used to calculate     :*/
/*::  the distance between two zip codes or postal codes using our           :*/
/*::  zipcodeworld(tm) and postalcodeworld(tm) products.                     :*/
/*::                                                                         :*/
/*::  definitions:                                                           :*/
/*::    south latitudes are negative, east longitudes are positive           :*/
/*::                                                                         :*/
/*::  passed to function:                                                    :*/
/*::    lat1, lon1 = latitude and longitude of point 1 (in decimal degrees)  :*/
/*::    lat2, lon2 = latitude and longitude of point 2 (in decimal degrees)  :*/
/*::    unit = the unit you desire for results                               :*/
/*::           where: 'm' is statute miles                                   :*/
/*::                  'k' is kilometers (default)                            :*/
/*::                  'n' is nautical miles                                  :*/
/*::  united states zip code/ canadian postal code databases with latitude & :*/
/*::  longitude are available at http://www.zipcodeworld.com                 :*/
/*::                                                                         :*/
/*::  For enquiries, please contact sales@zipcodeworld.com                   :*/
/*::                                                                         :*/
/*::  official web site: http://www.zipcodeworld.com                         :*/
/*::                                                                         :*/
/*::  hexa software development center © all rights reserved 2004            :*/
/*::                                                                         :*/
/*::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::*/
function distance($lat1, $lon1, $lat2, $lon2, $unit="K") { 

	$dist = sin( $lat1 * pi()/180 ) * sin( $lat2 * pi()/180  ) + 
			cos( $lat1 *pi()/180 ) * cos( $lat2 * pi()/180 ) * cos(  abs( ($lon2 * pi()/180) - ($lon1 *pi()/180) ) );
	$dist = acos( x );  
	
	// IN Kilometers
	$dist = ( 1.852 * 60.0 * ((x/pi)*180) );  

	if ($unit == "K") {
		return $dist;
	} else {
		return $dist / 1.609344;
	}
}

/**
 * Array to CSV
 *
 * download == "" -> return CSV string
 * download == "toto.csv" -> download file toto.csv
 */
if ( ! function_exists('array_to_csv'))
{
function array_to_csv($array, $download = "")
{
	if ($download != "")
	{
		header('Content-Encoding: UTF-8');
		header('Content-Type: application/csv;charset=UTF-8');
		header('Content-Disposition: attachement; filename="' . $download . '"');
	}

	ob_start();
	$f = fopen('php://output', 'w') or show_error("Can't open php://output");
	$n = 0;        
	foreach ($array as $line)
	{
		$n++;
		if ( ! fputcsv($f, $line))
		{
		    show_error("Can't write line $n: $line");
		}
	}
	fclose($f) or show_error("Can't close php://output");
	$str = ob_get_contents();
	ob_end_clean();

	if ($download == "")
	{
		return $str;    
	}
	else
	{
		echo "\xEF\xBB\xBF"; // UTF-8 BOM
		echo $str;
	}        
}
}