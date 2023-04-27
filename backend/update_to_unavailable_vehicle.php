<?php

function update_to_unavailable_vehicle()
{
	global $outputjson, $gh, $db, $const,$tz_name, $tz_offset, $loggedin_user;
	$outputjson['status'] = 0;
    $dateNow = date('Y-m-d H:i:s');
	$id = $gh->read("id", 0);

    $available_vehicle_query = "SELECT * FROM tbl_available_vehicle WHERE id = $id";
    $available_vehicle_rows = $db->execute($available_vehicle_query);
    
    if ($available_vehicle_rows != null && is_array($available_vehicle_rows) && count($available_vehicle_rows) > 0) {
		if($available_vehicle_rows[0]['created_by'] != $loggedin_user['id'])
		{
			$outputjson['message'] = "Unauthorised request!";
			return;
		}
		$db->update("tbl_available_vehicle", array("status"=>1),array("id"=>$id));
		$outputjson['status'] = 1;
		$outputjson['message'] = 'Vehicle set to unavailable successfully.';
	}
	else {
		$outputjson['message'] = "No Vehicle Found!";
	}

}

?>