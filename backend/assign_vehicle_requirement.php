<?php

function assign_vehicle_requirement()
{
	global $outputjson, $gh, $db, $const,$tz_name, $tz_offset, $loggedin_user;
	$outputjson['status'] = 0;
    $dateNow = date('Y-m-d H:i:s');
	$id = $gh->read("id", 0);
	$phone = $gh->read("phone", 0);

    $user_query = "SELECT * FROM tbl_users WHERE phone = '$phone'";
    $user_rows = $db->execute($user_query);

	$requirement_query = "SELECT vr.* FROM tbl_vehicle_requirement vr WHERE id = $id";
    $requirement_rows = $db->execute($requirement_query);

	if($requirement_rows[0]['created_by'] != $loggedin_user['id'])
	{
		$outputjson['message'] = "Unauthorised request!";
		return;
	}
    
    if ($user_rows != null && is_array($user_rows) && count($user_rows) > 0) {
		$db->update("tbl_vehicle_requirement", array("assigned_id"=>$user_rows[0]['id']),array("id"=>$id));
		$outputjson['status'] = 1;
		$outputjson['message'] = 'Requirement assigned successfully.';
	}
	else {
		$outputjson['message'] = "No User Found with this contact detail!";
	}

}

?>