<?php

function add_user_plan($params)
{
	global $outputjson, $gh, $db;
	$outputjson['status'] = 0;

	$user_id = $gh->read("user_id", $params['user_id']);
	$plan_id = $gh->read("plan_id", $params['plan_id']);
    $dateNow = date('Y-m-d H:i:s');

	$plan_query = "SELECT *,(SELECT end_date FROM `tbl_users_plan` WHERE user_id = $user_id ORDER BY start_date DESC LIMIT 1) AS last_plan_end_date FROM tbl_plans WHERE id = $plan_id";
    $plan_rows = $db->execute($plan_query);
    
    if ($plan_rows != null && is_array($plan_rows) && count($plan_rows) > 0) {
		$plan_days = $plan_rows[0]['duration'];
		$last_plan_end_date = $plan_rows[0]['last_plan_end_date'];
		$start_date = $dateNow;

		if($last_plan_end_date && (strtotime($last_plan_end_date) > strtotime($dateNow)) ){
			$start_date = date('Y-m-d H:i:s', strtotime($last_plan_end_date. ' + 1 seconds'));
		}

		$end_date = date('Y-m-d H:i:s', strtotime($start_date. ' + '. $plan_days .' days'));

		$insert_data = array(
			"user_id" => $user_id,
			"plan_id" => $plan_id,
			"start_date" => $start_date,
			"end_date" => $end_date,
		);
		$db->insert("tbl_users_plan", $insert_data);
		$outputjson['status'] = 1;
		$outputjson['message'] = 'Plan added successfully.';
	}
	else {
		$outputjson['message'] = "No Plans Found!";
	}
}

?>