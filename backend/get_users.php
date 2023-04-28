<?php

function get_users()
{
	global $outputjson, $gh, $db, $const,$tz_name, $tz_offset, $phone_format;
	$outputjson['status'] = 0;
    $dateNow = date('Y-m-d H:i:s');
	$get_active = $gh->read("get_active",0);
	$start = $gh->read("start");
	$length = $gh->read("length");
	$searcharr = $gh->read("search");
	$search = "";
	if($searcharr && count($searcharr) > 0)
	{
		$search = $searcharr['value'];
	}
	$orderarr = $gh->read("order");
	if($orderarr && count($orderarr) > 0)
	{
		$orderindex = $orderarr[0]['column'];
		$orderdir = $orderarr[0]['dir'];
	}
	$columnsarr = $gh->read("columns");
	if($columnsarr && count($columnsarr) > 0)
	{
		$ordercolumn = $columnsarr[$orderindex]['name'];
	}



	$where = " id <> 1 ";
	if($search != "")
	{
		$where.=" AND ( name LIKE '%$search%' OR phone LIKE '%$search%' OR email LIKE '%$search%' OR business_name LIKE '%$search%' OR state LIKE '%$search%' OR city LIKE '%$search%')";
	}
	$total_count = $db->get_row_count('tbl_users', "id <> 1");
	$filtered_count = $db->get_row_count('tbl_users', $where);
	$orderby = "";
	if($ordercolumn != "")
	{
		$orderby=" ORDER BY ".$ordercolumn." ".$orderdir;
	}



    $qry = "SELECT id, name, phone, email, business_name, business_description, state_id, state, city_id, city, pin_code, dl_front, dl_back, adhar_front, adhar_back, city_preferance_name, account_status FROM tbl_users WHERE ".$where.$orderby." LIMIT ".$start.",".$length;
    $rows = $db->execute($qry);
    
    if ($rows != null && is_array($rows) && count($rows) > 0) {
		$outputjson['status'] = 1;
		$outputjson['message'] = 'success.';
		$outputjson['recordsTotal'] = $total_count;
        $outputjson['recordsFiltered'] = $filtered_count;
		$outputjson["data"] = $rows;
	}
	else {
		$outputjson["data"] = [];
		$outputjson['recordsTotal'] = $total_count;
        $outputjson['recordsFiltered'] = 0;
		$outputjson['message'] = "No User Found!";
	}

}

?>