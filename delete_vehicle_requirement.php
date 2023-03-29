<?php

function delete_vehicle_requirement()
{
	global $outputjson, $gh, $db, $const,$tz_name, $tz_offset, $loggedin_user;
	$outputjson['success'] = 0;
    $dateNow = date('Y-m-d H:i:s');
	$id = $gh->read("id", 0);

    $requirement_query = "SELECT vr.* FROM tbl_vehicle_requirement vr WHERE id = $id";
    $requirement_rows = $db->execute($requirement_query);
    
    if ($requirement_rows != null && is_array($requirement_rows) && count($requirement_rows) > 0) {
		if($requirement_rows[0]['created_by'] != $loggedin_user['id'])
		{
			$outputjson['message'] = "Unauthorised request!";
			return;
		}
		$db->delete("tbl_vehicle_requirement", array("id"=>$id));
		$outputjson['success'] = 1;
		$outputjson['message'] = 'Requirement deleted successfully.';
	}
	else {
		$outputjson['message'] = "No Requirements Found!";
	}

}

?>