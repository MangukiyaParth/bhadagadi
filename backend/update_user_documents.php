<?php

function update_user_documents()
{
	global $outputjson, $gh, $db, $const,$tz_name, $tz_offset, $md5_user_id;
	$outputjson['status'] = 0;

	$token = $gh->read("token");
    $dateNow = date('Y-m-d H:i:s');
	$dl_front = $_FILES['dl_front'];
	$dl_back = $_FILES['dl_back'];
	$adhar_front = $_FILES['adhar_front'];
	$adhar_back = $_FILES['adhar_back'];

	$chk_row = getUsersDetails($md5_user_id, true);;
	$dl_front_path = $chk_row['dl_front'];
	if($dl_front != "")
	{
		if($chk_row['dl_front'])
		{
			unlink($chk_row['dl_front']);
			unlink(str_replace('/tmp','/tmp_thumb',$chk_row['dl_front']));
		}
		$dl_front_path = $gh -> UploadImage("dl_front", true, "");
	}

	$dl_back_path = $chk_row['dl_back'];
	if($dl_back != "")
	{
		if($chk_row['dl_back'])
		{
			unlink($chk_row['dl_back']);
			unlink(str_replace('/tmp','/tmp_thumb',$chk_row['dl_back']));
		}
		$dl_back_path = $gh -> UploadImage("dl_back", true, "");
	}

	$adhar_front_path = $chk_row['adhar_front'];
	if($adhar_front != "")
	{
		if($chk_row['adhar_front'])
		{
			unlink($chk_row['adhar_front']);
			unlink(str_replace('/tmp','/tmp_thumb',$chk_row['adhar_front']));
		}
		$adhar_front_path = $gh -> UploadImage("adhar_front", true, "");
	}

	$adhar_back_path = $chk_row['adhar_back'];
	if($adhar_back != "")
	{
		if($chk_row['adhar_back'])
		{
			unlink($chk_row['adhar_back']);
			unlink(str_replace('/tmp','/tmp_thumb',$chk_row['adhar_back']));
		}
		$adhar_back_path = $gh -> UploadImage("adhar_back", true, "");
	}

	$tableData = array(
		"dl_front"=>$dl_front_path,
		"dl_back"=>$dl_back_path,
		"adhar_front"=>$adhar_front_path,
		"adhar_back"=>$adhar_back_path,
		"account_status"=>2, // Pending
	);
	$result = $db->update("tbl_users", $tableData, array("MD5(id)"=>$md5_user_id));

	$outputjson['message'] = "data updated successfully";
	$outputjson['status'] = 1;
	$outputjson['data'] = getUsersDetails($md5_user_id, true);

}

?>