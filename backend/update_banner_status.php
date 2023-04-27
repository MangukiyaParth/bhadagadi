<?php

function update_banner_status()
{
	global $outputjson, $gh, $db, $const,$tz_name, $tz_offset, $md5_user_id;
	$outputjson['status'] = 0;

	$id = $gh->read("id",0);
    $dateNow = date('Y-m-d H:i:s');

	$banners_query = "SELECT * FROM tbl_banners WHERE id = $id";
    $banners_rows = $db->execute($banners_query);
    
    if ($banners_rows != null && is_array($banners_rows) && count($banners_rows) > 0) {
		$curr_status = $banners_rows[0]['is_active'];
		$new_status = 1;
		if($curr_status == 1)
		{
			$new_status = 0;
		}
		$db->update("tbl_banners", array("is_active"=>$new_status),array("id"=>$id));
		$outputjson['status'] = 1;
		$outputjson['message'] = 'Status updated successfully.';
	}
	else {
		$outputjson['message'] = "No Banner Found!";
	}

}

?>