<?php

function get_state()
{
	global $outputjson, $gh, $db, $const,$tz_name, $tz_offset, $phone_format;
	$outputjson['success'] = 0;
    $dateNow = date('Y-m-d H:i:s');

    $states_user = "SELECT * FROM tbl_states";
    $rows = $db->execute($states_user);
    
    if ($rows != null && is_array($rows) && count($rows) > 0) {
		$outputjson['success'] = 1;
		$outputjson['message'] = 'success.';
		$outputjson["data"] = $rows;
	}
	else {
		$outputjson['message'] = "No State Found!";
	}

}

?>