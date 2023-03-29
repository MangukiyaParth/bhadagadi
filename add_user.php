<?php

function add_user()
{
	global $outputjson, $gh, $db, $const,$tz_name, $tz_offset, $phone_format;
	$outputjson['success'] = 0;

	$role_id = $gh->read("role_id");
	$name = $gh->read("name");
	$phone = $gh->read("phone");
	$password = $gh->read("password");
	$business_name = $gh->read("business_name");
	$business_description = $gh->read("business_description");
	$email = $gh->read("email");
	$state_id = $gh->read("state_id");
	$city_id = $gh->read("city_id");
	$pin_code = $gh->read("pin_code");
    $dateNow = date('Y-m-d H:i:s');

	if(empty($name) || empty($phone) || empty($password) || empty($business_name) || empty($email) || empty($state_id) || empty($city_id) || empty($pin_code)){
		$outputjson['message'] = "Please fill all require fields";
		return;
	}

	$query_chk_user = "SELECT usr.* FROM tbl_users as usr WHERE phone = '$phone'";
	$chk_rows = $db->execute($query_chk_user);
	if(count($chk_rows) > 0)
	{
		$outputjson['message'] = "Phone number is already registerd";
		return;
	}

	$tableData = array(
		"role_id"=>$role_id,
		"name"=>$name,
		"username"=>$phone,
		"phone"=>$phone,
		"password"=>$password,
		"business_name"=>$business_name,
		"business_description"=>$business_description,
		"email"=>$email,
		"state_id"=>$state_id,
		"city_id"=>$city_id,
		"pin_code"=>$pin_code,
	);
	$result = $db->insert("tbl_users", $tableData);
	$token = md5(date('YmdHis')).'_'.md5($result);
    if ($result) {
		$db->update("tbl_users", array("token"=>$token), array("id"=>$result));
		$outputjson['message'] = "User signedup successfully";
		$outputjson['status'] = 1;
		$outputjson['result'] = $result;
	}
	else {
		$outputjson['message'] = "Somthing is wrong. Please try again";
	}

}

?>