<?php

function update_user_profile()
{
	global $outputjson, $gh, $db, $const,$tz_name, $tz_offset, $md5_user_id;
	$outputjson['status'] = 0;

	$token = $gh->read("token");
	$name = $gh->read("name");
	$business_name = $gh->read("business_name");
	$business_description = $gh->read("business_description");
	// $email = $gh->read("email");
	// $state_id = $gh->read("state_id");
	// $city_id = $gh->read("city_id");
	// $pin_code = $gh->read("pin_code");
    $dateNow = date('Y-m-d H:i:s');

	if(empty($business_name) || empty($name)){
		$outputjson['message'] = "Please fill all require fields";
		return;
	}

	$tableData = array(
		"name"=>$name,
		"business_name"=>$business_name,
		"business_description"=>$business_description,
		// "email"=>$email,
		// "state_id"=>$state_id,
		// "city_id"=>$city_id,
		// "pin_code"=>$pin_code,
	);
	$result = $db->update("tbl_users", $tableData, array("MD5(id)"=>$md5_user_id));
	
	$outputjson['message'] = "data updated successfully";
	$outputjson['status'] = 1;
	$outputjson['data'] = getUsersDetails($md5_user_id, true);;

}

?>