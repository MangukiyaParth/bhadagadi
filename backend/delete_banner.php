<?php

function delete_banner()
{
	global $outputjson, $gh, $db, $const,$tz_name, $tz_offset, $loggedin_user;
	$outputjson['status'] = 0;
    $dateNow = date('Y-m-d H:i:s');
	$id = $gh->read("id", 0);

    $banner_query = "SELECT * FROM tbl_banners WHERE id = $id";
    $banner_rows = $db->execute($banner_query);
    
    if ($banner_rows != null && is_array($banner_rows) && count($banner_rows) > 0) {
		unlink($banner_rows[0]['file']);
		$db->delete("tbl_banners", array("id"=>$id));
		$outputjson['status'] = 1;
		$outputjson['message'] = 'Banner deleted successfully.';
	}
	else {
		$outputjson['message'] = "No Banner Found!";
	}

}

?>