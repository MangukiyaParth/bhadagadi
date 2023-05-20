<?php

function update_user_status()
{
	global $outputjson, $gh, $db;
	$outputjson['status'] = 0;
	$id = $gh->read("id");
	$user_status = $gh->read("user_status");
	$tableData = array(
		"account_status" => $user_status,
	);
	$result = $db->update("tbl_users", $tableData, array("id" => $id));

	/* Send Notification */
	$user_data = getUsersDetails($id, false);
	$accout_status_msg = "";
	switch ($user_data['account_status']) {
		case 1:
			$accout_status_msg = "Your account is Verified";
			break;
		case 2:
			$accout_status_msg = "Your account status is pending, We will review your details and then approve it.";
			break;
		case 3:
			$accout_status_msg = "Your account request is rejected";
			break;
		case 4:
			$accout_status_msg = "Your account Suspended";
			break;
	}
	if ($accout_status_msg != "") {
		$device_token_array = explode(',', $user_data['fcm_token']);
		$gh->SendAndroidPushNotification($device_token_array, "Account Status Update", $accout_status_msg, $extra_args = array());
	}
	/*********************/

	$outputjson['message'] = "data updated successfully";
	$outputjson['status'] = 1;
}
