<?php

function update_notification_settings()
{
	global $outputjson, $gh, $db, $const,$tz_name, $tz_offset, $loggedin_user;
	$outputjson['status'] = 0;

	$user_id = $loggedin_user['id'];
	$informations = $gh->read("informations", 1);
	$one_way_trip = $gh->read("one_way_trip",1);
	$round_trip = $gh->read("round_trip",1);
	$sedan = $gh->read("sedan",1);
	$suv = $gh->read("suv",1);
	$hatchback = $gh->read("hatchback",1);
	$traveler_tempo = $gh->read("traveler_tempo",1);
	$bus = $gh->read("bus",1);
	$mini_bus = $gh->read("mini_bus",1);
	
	if($informations == 0)
	{
		$one_way_trip = 0;
		$round_trip = 0;
		$sedan = 0;
		$suv = 0;
		$hatchback = 0;
		$traveler_tempo = 0;
		$bus = 0;
		$mini_bus = 0;
	}

	$setting_query = "SELECT * FROM tbl_notification_settings WHERE user_id = $user_id";
    $setting_rows = $db->execute($setting_query);


	if ($setting_rows != null && is_array($setting_rows) && count($setting_rows) > 0) {
		$tableData = array(
			"informations" => $informations,
			"one_way_trip" => $one_way_trip,
			"round_trip" => $round_trip,
			"sedan" => $sedan,
			"suv" => $suv,
			"hatchback" => $hatchback,
			"traveler_tempo" => $traveler_tempo,
			"bus" => $bus,
			"mini_bus" => $mini_bus,
		);
		$result = $db->update("tbl_notification_settings", $tableData, array("user_id"=>$user_id));
	}
	else {
		$tableData = array(
			"informations" => $informations,
			"one_way_trip" => $one_way_trip,
			"round_trip" => $round_trip,
			"sedan" => $sedan,
			"suv" => $suv,
			"hatchback" => $hatchback,
			"traveler_tempo" => $traveler_tempo,
			"bus" => $bus,
			"mini_bus" => $mini_bus,
			"user_id" => $user_id,
		);
		$result = $db->insert("tbl_notification_settings", $tableData);
	}
		
	$setting_rows = $db->execute($setting_query);
	$outputjson['message'] = "data updated successfully";
	$outputjson['status'] = 1;
	$outputjson['data'] = $setting_rows[0];

}

?>