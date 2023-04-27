<?php

function get_banners()
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
	
	$filtered_count = $total_count = $db->get_row_count('tbl_banners', "1=1");

	$orderby = "";
	if($ordercolumn != "")
	{
		$orderby=" ORDER BY ".$ordercolumn." ".$orderdir;
	}
    $qry = "SELECT * FROM tbl_banners";
	if($get_active){
		$qry.=" WHERE is_active=1 ";
	}
	else{

		$qry.= $orderby." LIMIT ".$start.",".$length;
	}
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
		$outputjson['message'] = "No State Found!";
	}

}

?>