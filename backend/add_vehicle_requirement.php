<?php

function add_vehicle_requirement()
{
	global $outputjson, $gh, $db, $const,$tz_name, $tz_offset, $loggedin_user;
	$outputjson['status'] = 0;

	$from = $gh->read("from");
	$to = $gh->read("to");
	$pickup_date = $gh->read("pickup_date");
	$pickup_time = $gh->read("pickup_time");

	$car_type = $gh->read("car_type");
	$trip_type = $gh->read("trip_type");
	$trip_type_text = "Round Trip";
	if($trip_type == 2)
	{
		$trip_type_text = "One way trip";
	}

	$only_verified_users = $gh->read("only_verified_users");
	$contact_type = $gh->read("contact_type");
	$contact_type_text = "Regular Trip";
	if($trip_type == 2)
	{
		$contact_type_text = "Rate submission";
	}
	$comment = $gh->read("comment");
	$budget = $gh->read("budget");
	
    $dateNow = date('Y-m-d H:i:s');

	if(empty($from) || empty($to) || empty($pickup_date) || empty($pickup_time) || empty($car_type) || empty($trip_type) || empty($contact_type)){
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
		"pickup_date"=>$pickup_date,
		"pickup_time"=>$pickup_time,
		"car_type"=>$car_type,
		"car_type_text"=>$car_type_text,
		"trip_type"=>$trip_type,
		"trip_type_text"=>$trip_type_text,
		"only_verified_users"=>$only_verified_users,
		"contact_type"=>$contact_type,
		"contact_type_text"=>$contact_type_text,
		"comment"=>$comment,
		"budget"=>$budget,
		"created_by"=>$loggedin_user['id']
	);
	$result = $db->insert("tbl_vehicle_requirement", $tableData);
    if ($result) {

		/* Send Notification */
		// $query_get_users = `SELECT GROUP_CONCAT(DISTINCT fcm_token) AS tokens FROM tbl_users 
		// 	WHERE 
		// 	account_status = 1 
		// 	AND
		// 	(
		// 		WHERE FIND_IN_SET(".$from.", (REPLACE(REPLACE(REPLACE(city_preferance,'","',','),'["',''),'"]','')) ) 
		// 		OR 
		// 		FIND_IN_SET(".$to.", (REPLACE(REPLACE(REPLACE(city_preferance,'","',','),'["',''),'"]','')) )
		// 	)
		// 	AND
		// 	id IN (SELECT user_id FROM tbl_users_plan WHERE NOW() BETWEEN start_date AND end_date)`;
		// $get_users_rows = $db->execute($query_get_users);
		// $device_token_array = explode(',',$get_users_rows[0]['tokens']);
		// SendAndroidPushNotification($device_token_array, $msg_title, $msg_body, $extra_args = array());
		/*********************/
		$outputjson['message'] = "Requirement added successfully";
		$outputjson['status'] = 1;
		$outputjson['result'] = $result;
	}
	else {
		$outputjson['message'] = "Somthing is wrong. Please try again";
	}

}

?>