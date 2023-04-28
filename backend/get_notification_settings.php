<?php

function get_notification_settings()
{
	global $outputjson, $gh, $db, $const,$tz_name, $tz_offset, $loggedin_user;
	$outputjson['status'] = 0;

	$user_id = $loggedin_user['id'];
	$setting_query = "SELECT * FROM tbl_notification_settings WHERE user_id = $user_id";
    $setting_rows = $db->execute($setting_query);

	$data = array(
		"informations" => 1,
		"one_way_trip" => 1,
		"round_trip" => 1,
		"sedan" => 1,
		"suv" => 1,
		"hatchback" => 1,
		"traveler_tempo" => 1,
		"bus" => 1,
		"mini_bus" => 1,
		"user_id" => 1,
	);
	if ($setting_rows != null && is_array($setting_rows) && count($setting_rows) > 0) {
		$data = $setting_rows[0];
	}
		
	$setting_rows = $db->execute($setting_query);
	$outputjson['message'] = "successfully";
	$outputjson['status'] = 1;
	$outputjson['data'] = $data;

}

?>