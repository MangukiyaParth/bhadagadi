<?php

function get_city()
{
	global $outputjson, $gh, $db, $const,$tz_name, $tz_offset, $phone_format;
	$outputjson['status'] = 0;
    $dateNow = date('Y-m-d H:i:s');
	$state = $gh->read("state");

    $city_qry = "SELECT * FROM tbl_cities";
	if($state>0)
	{
		$city_qry.= " WHERE state_id = $state";
	}
    $rows = $db->execute($city_qry);
    
    if ($rows != null && is_array($rows) && count($rows) > 0) {
		$outputjson['status'] = 1;
		$outputjson['message'] = 'success.';
		$outputjson["data"] = $rows;
	}
	else {
		$outputjson['message'] = "No cities found";
	}

}

?>