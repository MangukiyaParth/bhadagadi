<?php

function update_user_city_prefrence()
{
	global $outputjson, $gh, $db, $const,$tz_name, $tz_offset, $md5_user_id;
	$outputjson['status'] = 0;

	$token = $gh->read("token");
	$city = $gh->read("city");
	$is_delete = $gh->read("is_delete",0);
    $dateNow = date('Y-m-d H:i:s');

	if(empty($city)){
		$outputjson['message'] = "Please select city";
		return;
	}
	
	$userData = getUsersDetails($md5_user_id, true);
	$city_preferance = [];
	$city_preferance_name = [];
	if(!empty($userData['city_preferance']))
	{
		$city_preferance = json_decode($userData['city_preferance']);
		$city_preferance_name = json_decode($userData['city_preferance_name']);
	}

	$query_city = "SELECT * FROM tbl_cities WHERE id = $city";
	$city_rows = $db->execute($query_city);
	$cityData = $city_rows[0];

	if(!in_array($city, $city_preferance, true)){
		array_push($city_preferance, $city);
		array_push($city_preferance_name, $cityData['city']);
    }
	else if($is_delete)
	{
		$city_preferance = array_values(array_filter($city_preferance, fn ($m) => $m != $city));
		$city_preferance_name = array_values(array_filter($city_preferance_name, fn ($m) => $m != $cityData['city']));
	}
	$tableData = array(
		"city_preferance"=> json_encode($city_preferance),
		"city_preferance_name"=> json_encode($city_preferance_name)
	);
	$result = $db->update("tbl_users", $tableData, array("MD5(id)"=>$md5_user_id));
	$userData["city_preferance"] = $city_preferance;
	$userData["city_preferance_name"] = $city_preferance_name;
	
	$outputjson['message'] = "data updated successfully";
	$outputjson['status'] = 1;
	$outputjson['data'] = $userData;

}

?>