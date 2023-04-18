<?php

function get_vehicle_requirement()
{
	global $outputjson, $gh, $db, $const,$tz_name, $tz_offset, $loggedin_user;
	$outputjson['success'] = 0;
    $dateNow = date('Y-m-d H:i:s');
	$loggedin_user_id = $loggedin_user['id'];
	$get_my_requirements = $gh->read("get_my_requirements", 0);
	$page = $gh->read("page", 1);
	$length = 10;
	$start = ($page - 1) * $length;

	$where = " WHERE status <> 1 ";
	if($get_my_requirements == 1)
	{
		$where.=" AND vr.created_by =  $loggedin_user_id";
	}
    $requirement_query = "SELECT vr.*, user.business_name, user.phone FROM tbl_vehicle_requirement vr INNER JOIN tbl_users user ON user.id = vr.created_by $where LIMIT $start, $length";
    $requirement_rows = $db->execute($requirement_query);
    
    if ($requirement_rows != null && is_array($requirement_rows) && count($requirement_rows) > 0) {
		$outputjson['success'] = 1;
		$outputjson['message'] = 'success.';
		$outputjson["data"] = $requirement_rows;
	}
	else {
		$outputjson['message'] = "No Requirements Found!";
	}

}

?>