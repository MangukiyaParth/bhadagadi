<?php

function get_car_type()
{
	global $outputjson, $gh, $db, $const,$tz_name, $tz_offset, $phone_format;
	$outputjson['success'] = 0;
    $dateNow = date('Y-m-d H:i:s');

    $car_type_qry = "SELECT * FROM tbl_car_type";
    $rows = $db->execute($car_type_qry);
    
    if ($rows != null && is_array($rows) && count($rows) > 0) {
		$outputjson['success'] = 1;
		$outputjson['message'] = 'success.';
		$outputjson["data"] = $rows;
	}
	else {
		$outputjson['message'] = "No car types found";
	}

}

?>