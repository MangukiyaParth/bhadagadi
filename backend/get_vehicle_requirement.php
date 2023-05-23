<?php

function get_vehicle_requirement()
{
	global $outputjson, $gh, $db, $const, $tz_name, $tz_offset, $loggedin_user;
	$outputjson['status'] = 0;
	$dateNow = date('Y-m-d H:i:s');
	$loggedin_user_id = $loggedin_user['id'];
	$city_preferance = $loggedin_user['city_preferance'];
	$status = $gh->read("status", 0); //1=active, 2=history, 3=assigned, 4=Admin Request
	$page = $gh->read("page", 1);
	$car_type = $gh->read("car_type", 0);
	$length = 10;
	$start = ($page - 1) * $length;

	$qry_limit = "";
	$where = " WHERE status <> 1 ";
	$orderby = "ORDER BY vr.id DESC";
	if ($status == 1) {
		$where = " WHERE status = 0 AND vr.created_by =  $loggedin_user_id";
	} else if ($status == 2) {
		$where = " WHERE status = 1 AND vr.created_by =  $loggedin_user_id";
	} else if ($status == 3) {
		$where = " WHERE assigned_id > 0 AND vr.assigned_id =  $loggedin_user_id";
	} else if ($status == 4) {
		$start = $gh->read("start");
		$length = $gh->read("length");
		$searcharr = $gh->read("search");
		$search = "";
		if ($searcharr && count($searcharr) > 0) {
			$search = $searcharr['value'];
		}
		$orderarr = $gh->read("order");
		if ($orderarr && count($orderarr) > 0) {
			$orderindex = $orderarr[0]['column'];
			$orderdir = $orderarr[0]['dir'];
		}
		$columnsarr = $gh->read("columns");
		if ($columnsarr && count($columnsarr) > 0) {
			$ordercolumn = $columnsarr[$orderindex]['name'];
		}
		$where = " WHERE 1=1 ";
		if ($search != "") {
			$where .= " AND ( vr.from_text LIKE '%$search%' OR 
							vr.to_text LIKE '%$search%' OR 
							vr.CONCAT(pickup_date,' ', pickup_time) LIKE '%$search%' OR 
							vr.car_type_text LIKE '%$search%' OR 
							vr.trip_type_text LIKE '%$search%' OR 
							user.business_name LIKE '%$search%' OR 
							user.phone LIKE '%$search%' OR 
							au.business_name LIKE '%$search%' OR 
							au.phone LIKE '%$search%')";
		}
		if ($ordercolumn != "") {
			$orderby = " ORDER BY user." . $ordercolumn . " " . $orderdir;
		}
		$qry_limit = " LIMIT $start, $length ";
	}
	if ($status == 0) {
		$where .= " AND STR_TO_DATE(CONCAT(pickup_date,' ',pickup_time),'%d/%m/%Y %h:%i %p') > STR_TO_DATE('$dateNow','%Y-%m-%d %H:%i:%s') ";
		if ($city_preferance) {
			$city_preferance = implode(',', array_map('intval', $city_preferance));
			$where .= " AND (vr.from IN (" . $city_preferance . ") OR vr.to IN (" . $city_preferance . ") OR vr.created_by =  $loggedin_user_id)";
		} else {
			$where .= " AND 1=2 ";
		}
	}
	if ($car_type > 0) {
		$where .= " AND vr.car_type =  $car_type";
	}

	$total_count = $db->get_row_count('tbl_vehicle_requirement', "1=1");
	$filtered_count = $db->get_row_count('tbl_vehicle_requirement vr INNER JOIN tbl_users user ON user.id = vr.created_by LEFT JOIN tbl_users au ON au.id = vr.assigned_id', $where);

	$requirement_query = "SELECT vr.*, user.business_name, user.phone , au.business_name AS assigned_business_name, au.phone AS assigned_phone 
		FROM tbl_vehicle_requirement vr 
		INNER JOIN tbl_users user ON user.id = vr.created_by 
		LEFT JOIN tbl_users au ON au.id = vr.assigned_id 
		$where 
		$orderby
		$qry_limit";
	$requirement_rows = $db->execute($requirement_query);
	$outputjson['qry'] = $requirement_query;

	if ($requirement_rows != null && is_array($requirement_rows) && count($requirement_rows) > 0) {
		$outputjson['status'] = 1;
		$outputjson['message'] = 'success.';
		$outputjson['recordsTotal'] = $total_count;
		$outputjson['recordsFiltered'] = $filtered_count;
		$outputjson["data"] = $requirement_rows;
	} else {
		$outputjson['recordsTotal'] = $total_count;
		$outputjson['recordsFiltered'] = 0;
		$outputjson['message'] = "No Requirements Found!";
	}
}
