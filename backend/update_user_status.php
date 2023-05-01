<?php

function update_user_status()
{
	global $outputjson, $gh, $db, $const,$tz_name, $tz_offset, $md5_user_id;
	$outputjson['status'] = 0;

	$id = $gh->read("id");
	$user_status = $gh->read("user_status");
    $dateNow = date('Y-m-d H:i:s');


	$tableData = array(
		"account_status"=>$user_status,
	);
	$result = $db->update("tbl_users", $tableData, array("id"=>$id));
	
	$outputjson['message'] = "data updated successfully";
	$outputjson['status'] = 1;
}

?>