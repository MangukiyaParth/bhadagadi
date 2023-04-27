<?php

function add_available_vehicle()
{
	global $outputjson, $gh, $db, $const,$tz_name, $tz_offset, $loggedin_user;
	$outputjson['status'] = 0;

	$from = $gh->read("from");
	$to = $gh->read("to");
	$available_date = $gh->read("available_date");
	$available_time = $gh->read("available_time");
	$car_type = $gh->read("car_type");
	$comment = $gh->read("comment");
	
    $dateNow = date('Y-m-d H:i:s');

	if(empty($from) || empty($to) || empty($available_date) || empty($available_time) || empty($car_type)){
		$outputjson['message'] = "Please fill all require fields";
		return;
	}

	$query_get_names = "SELECT (SELECT CONCAT(city,' , ',state) FROM tbl_cities WHERE id = $from) AS from_text, 
		(SELECT CONCAT(city,' , ',state) FROM tbl_cities WHERE id = $to) AS to_text, 
		(SELECT type_name FROM `tbl_car_type` WHERE id = $car_type) AS car_type_text";
	$get_names_rows = $db->execute($query_get_names);
	$get_names = $get_names_rows[0];
	$from_text = $get_names['from_text'];
	$to_text = $get_names['to_text'];
	$car_type_text = $get_names['car_type_text'];

	$tableData = array(
		"from"=>$from,
		"from_text"=>$from_text,
		"to"=>$to,
		"to_text"=>$to_text,
		"available_date"=>$available_date,
		"available_time"=>$available_time,
		"car_type"=>$car_type,
		"car_type_text"=>$car_type_text,
		"comment"=>$comment,
		"created_by"=>$loggedin_user['id']
	);
	$result = $db->insert("tbl_available_vehicle", $tableData);
    if ($result) {
		$outputjson['message'] = "add vehicle for available successfully";
		$outputjson['status'] = 1;
		$outputjson['result'] = $result;
	}
	else {
		$outputjson['message'] = "Somthing is wrong. Please try again";
	}

}

?>