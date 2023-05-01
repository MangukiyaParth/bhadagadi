<?php

function get_plans()
{
	global $outputjson, $gh, $db, $const,$tz_name, $tz_offset, $phone_format;
	$outputjson['status'] = 0;
    $dateNow = date('Y-m-d H:i:s');

    $city_qry = "SELECT * FROM tbl_plans WHERE id <> 1";
    $rows = $db->execute($city_qry);
    
    if ($rows != null && is_array($rows) && count($rows) > 0) {
		$outputjson['status'] = 1;
		$outputjson['message'] = 'success.';
		$outputjson["data"] = $rows;
	}
	else {
		$outputjson['message'] = "No plans found";
	}

}

?>