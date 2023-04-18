<?php
function apply_for_rates()
{
	global $outputjson, $gh, $db, $const,$tz_name, $tz_offset, $loggedin_user;
	$outputjson['success'] = 0;

	$id = $gh->read("id");
	$trip_rate = $gh->read("trip_rate");
	$comment = $gh->read("comment");
	$loggedin_user_id = $loggedin_user['id'];	
    $dateNow = date('Y-m-d H:i:s');

	if(empty($trip_rate)){
		$outputjson['message'] = "Please fill rates";
		return;
	}

	$requirement_query = "SELECT vr.*,(SELECT id FROM tbl_vehicle_requirement_rates rates WHERE rates.requirement_id = $id AND rates.created_by = $loggedin_user_id LIMIT 1) AS is_applied FROM tbl_vehicle_requirement vr WHERE vr.id = $id";
    $requirement_rows = $db->execute($requirement_query);

	if ($requirement_rows != null && is_array($requirement_rows) && count($requirement_rows) > 0) {

		if($requirement_rows[0]['created_by'] == $loggedin_user['id'])
		{
			$outputjson['message'] = "You can't apply for your own requirement";
			return;
		}
		if($requirement_rows[0]['is_applied'] > 0)
		{
			$outputjson['message'] = "You have already applied your rates for this requirement";
			return;
		}

		$tableData = array(
			"requirement_id"=>$id,
			"trip_rate"=>$trip_rate,
			"comment"=>$comment,
			"created_by"=>$loggedin_user['id']
		);
		$result = $db->insert("tbl_vehicle_requirement_rates", $tableData);
		if ($result) {
			$outputjson['message'] = "Rate applied successfully";
			$outputjson['status'] = 1;
			$outputjson['result'] = $result;
		}
		else {
			$outputjson['message'] = "Somthing is wrong. Please try again";
		}
	}
	else {
		$outputjson['message'] = "Somthing is wrong. Please try again.";
	}

}

?>