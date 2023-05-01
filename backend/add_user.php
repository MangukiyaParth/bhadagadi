<?php
include("add_user_plan.php");
function add_user()
{
	global $outputjson, $gh, $db;
	$outputjson['status'] = 0;

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
		$outputjson['status'] = 2;
		$outputjson['message'] = "Phone number is already registerd";
		$outputjson['data'] = $chk_rows[0];
		return;
	}

	$query_get_names = "SELECT (SELECT city FROM tbl_cities WHERE id = $city_id) AS city_text, 
		(SELECT name FROM tbl_states WHERE id = $state_id) AS state_text";
	$get_names_rows = $db->execute($query_get_names);
	$get_names = $get_names_rows[0];
	$city_text = $get_names['city_text'];
	$state_text = $get_names['state_text'];

	$city_preferance = [$city_id];
	$city_preferance_name = [];
	$new_city = array(
		"id"=>$city_id,
		"name"=>$city_text
	);
	array_push($city_preferance_name, $new_city);;

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
		"state"=>$state_text,
		"city_id"=>$city_id,
		"city"=>$city_text,
		"city_preferance"=> json_encode($city_preferance),
		"city_preferance_name"=> json_encode($city_preferance_name),
		"pin_code"=>$pin_code,
	);
	$result = $db->insert("tbl_users", $tableData);
	$token = md5(date('YmdHis')).'_'.md5($result);
    if ($result) {
		$db->update("tbl_users", array("token"=>$token), array("id"=>$result));
		add_user_plan(array("user_id"=>$result, "plan_id"=>1));
		$userData = getUsersDetails($result, false);
		$outputjson['message'] = "User signedup successfully";
		$outputjson['status'] = 1;
		$outputjson['data'] = $userData;
	}
	else {
		$outputjson['message'] = "Somthing is wrong. Please try again";
	}

}

?>