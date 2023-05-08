<?php

function get_available_vehicle()
{
	global $outputjson, $gh, $db, $const,$tz_name, $tz_offset, $loggedin_user;
	$outputjson['status'] = 0;
	$loggedin_user_id = $loggedin_user['id'];
	$city_preferance = $loggedin_user['city_preferance'];
	$get_my_details = $gh->read("get_my_details", 0);
	$page = $gh->read("page", 1);
	$from = $gh->read("from");
	$to = $gh->read("to");
	$car_type = $gh->read("car_type");
	$length = 10;
	$start = ($page - 1) * $length;

	$where = " WHERE va.status <> 1 ";
	if($get_my_details == 1)
	{
		$where =" WHERE va.created_by =  $loggedin_user_id";
	}
	if(!empty($from) && $from != "")
	{
		$where.=" AND va.from = $from";
	}
	if(!empty($to) && $to != "")
	{
		$where.=" AND va.to = $to";
	}
	if(!empty($car_type) && $car_type != "")
	{
		$where.=" AND va.car_type = $car_type";
	}

	if ($city_preferance) {
		$city_preferance = implode(',', array_map('intval', $city_preferance));
		$where .= " AND (va.from IN (" . $city_preferance . ") OR va.to IN (" . $city_preferance . "))";
	} else {
		$where .= " AND 1=2 ";
	}

    $available_vehicle_query = "SELECT va.*, user.business_name, user.phone FROM tbl_available_vehicle va INNER JOIN tbl_users user ON user.id = va.created_by $where LIMIT $start, $length";
    $available_vehicle_rows = $db->execute($available_vehicle_query);
    
    if ($available_vehicle_rows != null && is_array($available_vehicle_rows) && count($available_vehicle_rows) > 0) {
		$outputjson['status'] = 1;
		$outputjson['message'] = 'success.';
		$outputjson["data"] = $available_vehicle_rows;
	}
	else {
		$outputjson['message'] = "No Requirements Found!";
	}

}
