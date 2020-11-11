<?php

/*::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::*/
/*::                                                                         :*/
/*::  This routine calculates the distance between two points (given the     :*/
/*::  latitude/longitude of those points). It is being used to calculate     :*/
/*::  the distance between two locations using GeoDataSource(TM) Products    :*/
/*::                                                                         :*/
/*::  Definitions:                                                           :*/
/*::    South latitudes are negative, east longitudes are positive           :*/
/*::                                                                         :*/
/*::  Passed to function:                                                    :*/
/*::    lat1, lon1 = Latitude and Longitude of point 1 (in decimal degrees)  :*/
/*::    lat2, lon2 = Latitude and Longitude of point 2 (in decimal degrees)  :*/
/*::    unit = the unit you desire for results                               :*/
/*::           where: 'M' is statute miles (default)                         :*/
/*::                  'K' is kilometers                                      :*/
/*::                  'N' is nautical miles                                  :*/
/*::  Worldwide cities and other features databases with latitude longitude  :*/
/*::  are available at https://www.geodatasource.com                          :*/
/*::                                                                         :*/
/*::  For enquiries, please contact sales@geodatasource.com                  :*/
/*::                                                                         :*/
/*::  Official Web site: https://www.geodatasource.com                        :*/
/*::                                                                         :*/
/*::         GeoDataSource.com (C) All Rights Reserved 2018                  :*/
/*::                                                                         :*/
/*::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::*/
function distance($lat1, $lon1, $lat2, $lon2, $unit)
{
  if (($lat1 == $lat2) && ($lon1 == $lon2)) {
    return 0;
  }
  // else {
  //   $theta = $lon1 - $lon2;
  //   $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
  //   $dist = acos($dist);
  //   $dist = rad2deg($dist);
  //   $miles = $dist * 60 * 1.1515;
  //   $unit = strtoupper($unit);

  //   if ($unit == "K") {
  //     return ($miles * 1.609344);
  //   } else if ($unit == "N") {
  //     return ($miles * 0.8684);
  //   } else {
  //     return $miles;
  //   }
  // }

  else {
    // $theta = $lon1 - $lon2;
    // $dist = 
    $miles = rad2deg(acos(sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($lon1 - $lon2)))) * 60 * 1.1515;
    $unit = strtoupper($unit);

    if ($unit == "K") {
      return ($miles * 1.609344);
    } else if ($unit == "N") {
      return ($miles * 0.8684);
    } else {
      return $miles;
    }
  }
}

echo distance(32.9697, -96.80322, 29.46786, -98.53506, "M") . " Miles<br>";
echo distance(32.9697, -96.80322, 29.46786, -98.53506, "K") . " Kilometers<br>";
echo distance(32.9697, -96.80322, 29.46786, -98.53506, "N") . " Nautical Miles<br>";

?>

<?php

$sqlDistance = "
SELECT id,user_id, (6371 * acos( cos( radians(latitude) ) * cos( radians( 29.807903289794923 ) ) * cos( radians( 52.48660659790040 ) - radians(longitude) ) + sin( radians(latitude) ) * sin( radians( 29.807903289794923 ) ) ) ) as distance from pish_phocamaps_marker_store WHERE user_id is not null && user_id>0 order by distance
";
