<?php

function get_dashboard_data()
{
	global $outputjson, $gh, $db;
	$outputjson['status'] = 0;
	$today = date('Y-m-d H:i:s');

	$earning_chart_qry = "SELECT SUM(plan.price) AS amount, DATE_FORMAT(uplan.created_at,'%Y-%m-%d') AS purchase_date FROM tbl_users_plan uplan INNER JOIN tbl_plans plan ON plan.id = uplan.plan_id  GROUP BY DATE_FORMAT(created_at,'%Y-%m-%d')";
	$earning_chart_rows = $db->execute($earning_chart_qry);
	$earning_chart_dates = array_column($earning_chart_rows, 'purchase_date');
	$earning_chart_amount = array_column($earning_chart_rows, 'amount');

	$earning_qry = "SELECT IFNULL((SELECT SUM(plan.price) FROM tbl_users_plan uplan INNER JOIN tbl_plans plan ON plan.id = uplan.plan_id WHERE DATE_FORMAT(created_at,'%Y-%m-%d') = '$today' ),0) AS today_earning,
	IFNULL((SELECT SUM(plan.price) FROM tbl_users_plan uplan INNER JOIN tbl_plans plan ON plan.id = uplan.plan_id WHERE DATE_FORMAT(created_at,'%Y-%m-%d') BETWEEN DATE_SUB(CURDATE(), INTERVAL WEEKDAY(CURDATE()) DAY) AND STR_TO_DATE('$today','%Y-%m-%d') ),0) AS week_earning,
	IFNULL((SELECT SUM(plan.price) FROM tbl_users_plan uplan INNER JOIN tbl_plans plan ON plan.id = uplan.plan_id WHERE DATE_FORMAT(created_at,'%Y-%m-%d') BETWEEN DATE_FORMAT(NOW() ,'%Y-%m-01') AND STR_TO_DATE('$today','%Y-%m-%d') ),0) AS month_earning";
	$earning_rows = $db->execute($earning_qry);



	$users_chart_qry = "SELECT COUNT(id) AS user_cnt, DATE_FORMAT(insert_at,'%Y-%m-%d') AS insert_at FROM tbl_users WHERE id <> 1 GROUP BY DATE_FORMAT(insert_at,'%Y-%m-%d')";
	$users_chart_rows = $db->execute($users_chart_qry);
	$users_chart_dates = array_column($users_chart_rows, 'insert_at');
	$users_chart_cnt = array_column($users_chart_rows, 'user_cnt');

	$user_cnt_qry = "SELECT IFNULL((SELECT COUNT(id) FROM tbl_users WHERE id <> 1 AND DATE_FORMAT(insert_at,'%Y-%m-%d') = '$today' ),0) AS today_user_cnt,
	IFNULL((SELECT COUNT(id) FROM tbl_users WHERE id <> 1 AND DATE_FORMAT(insert_at,'%Y-%m-%d') BETWEEN DATE_SUB(CURDATE(), INTERVAL WEEKDAY(CURDATE()) DAY) AND STR_TO_DATE('$today','%Y-%m-%d') ),0) AS week_user_cnt,
	IFNULL((SELECT COUNT(id) FROM tbl_users WHERE id <> 1 AND DATE_FORMAT(insert_at,'%Y-%m-%d') BETWEEN DATE_FORMAT(NOW() ,'%Y-%m-01') AND STR_TO_DATE('$today','%Y-%m-%d') ),0) AS month_user_cnt";
	$user_cnt_rows = $db->execute($user_cnt_qry);

	if (!in_array($today, $earning_chart_dates, true)) {
		array_push($earning_chart_dates, $today);
		array_push($earning_chart_amount, "");
	}
	if (!in_array($today, $users_chart_dates, true)) {
		array_push($users_chart_dates, $today);
		array_push($users_chart_cnt, "");
	}
	$outputjson['status'] = 1;
	$outputjson['message'] = 'success.';
	$outputjson["earning_chart_dates"] = $earning_chart_dates;
	$outputjson["earning_chart_amount"] = $earning_chart_amount;
	$outputjson["earning_rows"] = $earning_rows;
	$outputjson["users_chart_dates"] = $users_chart_dates;
	$outputjson["users_chart_cnt"] = $users_chart_cnt;
	$outputjson["user_cnt_rows"] = $user_cnt_rows;
}
