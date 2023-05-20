<?php

function get_version_data()
{
	global $outputjson;

	$data = array(
		"app_version" => "1.2",
		"update_mendatory" => true,
		"video_url" => "https://www.youtube.com/watch?v=uYPbbksJxIg",
		"upi_id" => "63280109966.payswiff@indus",
		"contact_no" => "9856789548",
		"add_vehicle_note" => "Enter this trip only if you have a requirement for this type of vehicle, otherwise, your account will be suspended and a penalty of Rs 1,000 will be charged for opening the account.",
	);
	$outputjson['status'] = 1;
	$outputjson['data'] = $data;
	$outputjson['message'] = 'success.';
}
