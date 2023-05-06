<?php

function get_vehicle_requirement()
{
	global $outputjson, $gh, $db, $const,$tz_name, $tz_offset, $loggedin_user;
	$outputjson['status'] = 0;
	$loggedin_user_id = $loggedin_user['id'];
	$city_preferance = $loggedin_user['city_preferance'];
	$status = $gh->read("status", 0); //1=active, 2=history, 3=assigned
	$page = $gh->read("page", 1);
	$car_type = $gh->read("car_type", 0);
	$length = 10;
	$start = ($page - 1) * $length;

	$where = " WHERE status <> 1 ";
	if($status == 1){
		$where = " WHERE status = 0 AND vr.created_by =  $loggedin_user_id";
	}
	else if($status == 2){
		$where = " WHERE status = 1 AND vr.created_by =  $loggedin_user_id";
	}
	else if($status == 3){
		$where = " WHERE assigned_id > 0 AND vr.created_by =  $loggedin_user_id";
	}
	if($city_preferance && $status==0)
	{
		$city_preferance = implode(',', array_map('intval', $city_preferance));
		$where.=" AND (vr.from IN (". $city_preferance .") OR vr.to IN (". $city_preferance ."))";
	}
	if($car_type > 0)
	{
		$where.= " AND vr.car_type =  $car_type";
	}
    $requirement_query = "SELECT vr.*, user.business_name, user.phone , au.business_name AS assigned_business_name, au.phone AS assigned_phone 
		FROM tbl_vehicle_requirement vr 
		INNER JOIN tbl_users user ON user.id = vr.created_by 
		LEFT JOIN tbl_users au ON au.id = vr.assigned_id 
		$where 
		ORDER BY vr.id DESC
		LIMIT $start, $length";
    $requirement_rows = $db->execute($requirement_query);
    
    if ($requirement_rows != null && is_array($requirement_rows) && count($requirement_rows) > 0) {
		$outputjson['status'] = 1;
		$outputjson['message'] = 'success.';
		$outputjson["data"] = $requirement_rows;
	}
	else {
		$outputjson['message'] = "No Requirements Found!";
	}

}
