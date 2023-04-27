<?php

function get_user()
{
	global $outputjson, $gh, $db, $const,$tz_name, $tz_offset, $phone_format;
	$outputjson['status'] = 0;

	$user_id = $gh->read("user_id");
    $dateNow = date('Y-m-d H:i:s');

	$userData = getUsersDetails($user_id, false);
	if(count($userData) > 0)
	{
		$outputjson['status'] = 1;
		$outputjson['message'] = "Success";
		$outputjson['data'] = $userData;
	}
	else {
		$outputjson['message'] = "Somthing is wrong. Please try again";
	}

}

?>