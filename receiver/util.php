<?php
function startsWith($needle, $haystack) 
{
	 return preg_match('/^'.preg_quote($needle)."/", $haystack);
}

function endsWith($needle, $haystack) 
{
	 return preg_match("/".preg_quote($needle) .'$/', $haystack);
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
?>