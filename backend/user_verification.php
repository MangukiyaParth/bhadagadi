<?php
function user_verification()
{
	global $outputjson, $gh, $db;
	$outputjson['status'] = 0;

	$phone = $gh->read("phone");
	$fcm_token = $gh->read("fcm_token");
    $dateNow = date('Y-m-d H:i:s');

	if(empty($phone)){
		$outputjson['message'] = "Please fill Phone no";
		return;
	}

	$query_chk_user = "SELECT usr.* FROM tbl_users as usr WHERE phone = '$phone'";
	$chk_rows = $db->execute($query_chk_user);
	$otp = random_int(100000, 999999);

	// $username="maulik139";
	// $password="maulik139$";
	// $message= $otp." is your otp to login to bhadagadi";
	// $mobile_number= $phone;
	// $url ="https://login.bulksmsgateway.in/textmobilesmsapi.php?user=".urlencode($username)."&password=".urlencode($password)."&mobile=".urlencode($mobile_number)."&message=".urlencode($message)."&type=".urlencode('3');
	// $ch = curl_init($url);
	// curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	// $curl_scraped_page = curl_exec($ch);
	// curl_close($ch);

	if(count($chk_rows) > 0)
	{
		$db->update("tbl_users", array("fcm_token"=>$fcm_token), array("id"=>$chk_rows[0]['id']));
		$outputjson['status'] = 2;
		$outputjson['message'] = "Phone number is already registerd";
		$outputjson['data'] = $chk_rows[0];
		$outputjson['otp'] = $otp;
	}
	else {
		$outputjson['status'] = 1;
		$outputjson['message'] = "OTP send successfully";
		$outputjson['otp'] = $otp;
	}

}

?>