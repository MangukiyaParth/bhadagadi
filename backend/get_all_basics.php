<?php

function get_all_basics()
{
	global $outputjson, $gh, $db, $const,$tz_name, $tz_offset, $phone_format;
	$outputjson['status'] = 0;
    $dateNow = date('Y-m-d H:i:s');

	$states_user = "SELECT * FROM tbl_states";
    $state_rows = $db->execute($states_user);

    $city_qry = "SELECT * FROM tbl_cities ORDER BY CASE WHEN state_id = 12 THEN 0 ELSE 1 END";
    $city_rows = $db->execute($city_qry);
    
	$car_type_qry = "SELECT * FROM tbl_car_type";
    $car_type_rows = $db->execute($car_type_qry);

	$rows = array(
		"state"=>$state_rows,
		"city"=>$city_rows,
		"car_type"=>$car_type_rows,
	);
	$outputjson['status'] = 1;
	$outputjson['message'] = 'success.';
	$outputjson["data"] = $rows;
}
