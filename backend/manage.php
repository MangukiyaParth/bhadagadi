<?php

	ini_set('max_execution_time', 300); //300 seconds = 5 minutes
	ini_set('display_errors',1);
	ini_set('mysql.connect_timeout', 300);
	$start_service=microtime(true);
	header("Content-type: application/json; charset=utf-8");

	include("_DEFINE.php");
	include("_SUPPORT.php");
	require_once('_DATABASE.php');
	
	array_filter($_POST, 'trim_value');
	global $outputjson, $db, $debug_mode, $const, $gh, $md5_user_id, $loggedin_user;
	$db = new MysqliDB(db_host, db_user, db_pass, db_name);
	$gh = new SUPPORT();

	$login_not_require_operation = array("login_user","add_user","get_state","get_city","get_all_basics","get_car_type","get_plans");
	$plan_not_require_operation = array("login_user","add_user","get_state","get_city","get_all_basics","get_car_type","get_plans","add_user_plan");

	$operation = $gh->read("operation","");
	$token = $gh->read("token","");
	$user_id = $gh->read("user_id",0);

	$loggedin_user = [];
	$md5_user_id = 0;
	if($token !== ""){
		$explode_token = explode('_', $token);
		$md5_user_id = $explode_token[1];
		
		$loggedin_userData = getUsersDetails($md5_user_id, true);
		if($loggedin_userData){
			if(isset($operation) && !in_array($operation, $plan_not_require_operation) && $loggedin_userData['has_active_plan'] == 0){
				$error = "Plan is not active for use Id: ".$loggedin_userData['id'];
				$gh->Log($error);
		
				$outputjson['message'] = "Sorry! You don't have any active plan, Please purchase any plan to continue this service.";
				$outputjson['status'] = -1;
				$outputjson['data'] = [];
				$response_string = json_encode(($outputjson), JSON_PRETTY_PRINT);
				echo $response_string;
				return;
			}
			$city_preferance = [];
			$city_preferance_name = [];
			if(!empty($loggedin_userData['city_preferance']))
			{
				$city_preferance = json_decode($loggedin_userData['city_preferance']);
				$city_preferance_name = json_decode($loggedin_userData['city_preferance_name']);
			}
			$loggedin_userData["city_preferance"] = $city_preferance;
			$loggedin_userData["city_preferance_name"] = $city_preferance_name;

			$loggedin_user = $loggedin_userData;
		}
		else {
			$outputjson['message'] = "Invalid Token!";
			$outputjson['status'] = -2;
			$outputjson['data'] = [];
			$response_string = json_encode(($outputjson), JSON_PRETTY_PRINT);
			echo $response_string;
			return;
		}
	}
	elseif (!in_array($operation, $login_not_require_operation)){
		$outputjson['message'] = "Token not Found.";
		$outputjson['status'] = -2;
		$outputjson['data'] = [];
		$response_string = json_encode(($outputjson), JSON_PRETTY_PRINT);
		echo $response_string;
		return;
	}

	if(isset($_POST) && count($_POST) > 0) {
		foreach ($_POST as $post_key => &$post_value) {
			if(is_string($post_value)){
				$post_value = strip_tags($post_value);
			}
		}
	}

	$log_mode = $gh->read("log", LOG_MODE);

	$handler = function(\Throwable $ex) {
		global $gh;
		$msg = "[ {$ex->getCode()} ] {$ex->getTraceAsString()}";
		$error = "Service Error: ".$ex->getMessage().PHP_EOL.$msg;
		$gh->Log($error);

		$outputjson['message'] = "Something went wrong. This issue has been reported. Please try again.";
		$outputjson['status'] = 0;
		$outputjson['data'] = [];

		$response_string = json_encode(($outputjson), JSON_PRETTY_PRINT);
		echo $response_string;
		return;
	};
	set_exception_handler($handler);


	function ServiceErrorHandler(int $errNo, string $errMsg, string $file, int $line) {

		global $gh;
		if($errMsg == "mkdir(): File exists") {
			// nothing to do. just ingore it.
		}
		else {
			$gh->Log(__FUNCTION__." Error: #[$errNo] occurred in [$file] at line [$line]: [$errMsg]");
		}
	}
	set_error_handler('ServiceErrorHandler');

	$request_string = "";
	if($log_mode >= 1)
	{
		$request = array();
		$request = $request + array("QUERY_STRING" => $_SERVER['QUERY_STRING']) + array("IP_ADDRESS" => $gh->get_client_ip());
		if(!empty($_POST) && count($_POST) > 0){
			$request = $request + $_POST;
		}
		$gh -> Log($request);

	}

	if(!is_numeric($user_id)){
		$outputjson['message'] = "Invalid User ID. Must be an integer.";
		$outputjson['status'] = 0;
		$response_string = json_encode($outputjson, JSON_PRETTY_PRINT);
		echo $response_string;
		return;
	}
	else {
		if ($user_id > 0 && $user_id != '') {
			$user = $db->execute("SELECT usr.* FROM `tbl_users` as usr WHERE usr.id = " . $user_id . " LIMIT 1");
			if (count($user) > 0) {
				$userObj = $user[0];
				$gh->current_user = $userObj;
				if ($userObj['account_status'] == 4) {
					$outputjson['message'] = "Your account is susspended, please contact admin";
					$outputjson['status'] = 0;
					$response_string = json_encode($outputjson, JSON_PRETTY_PRINT);
					echo $response_string;
					return;
				}
			}
		}

		try {
			if (!isset($operation) || empty($operation)) {
				$outputjson['error'] = "Operation missing in request.";
			}
			else if (file_exists($operation . ".php")) {
				include($operation . ".php");
				if (is_callable($operation)) {
					$module_key = (isset($_REQUEST['module_key']))?$_REQUEST['module_key']:'';
					$primary_key = (isset($_REQUEST['primary_key']))?$_REQUEST['primary_key']:'';
					$op = (isset($_REQUEST['op']))?$_REQUEST['op']:'';
					$params = $_REQUEST;
					$operation($params);
					/***AUDIT LOG START****/
					$str_arr = explode ("_", $operation);
					include("log_manage.php");
					log_manage($params,$outputjson,$operation) ;
					/***AUDIT LOG OVER****/

				} else {
					$outputjson['error'] = "Operation does not exists";
				}
			} else {
				$outputjson['error'] = "file does not exist";
			}
		} catch (Exception $e) {
			$gh->Log($e->getMessage());
		}
	}

	if($log_mode == 2 || $debug_mode >= 1) {
		// append at top of the array..  alternate to array_unshift()
		$outputjson = array("__REQUEST__" => $request) + $outputjson;
	}
	$temp_outputjson = ($outputjson);
	if(empty($json_feed)) {
		$outputjson["date_now"] = date('Y-m-d H:i:s');
		$temp_outputjson = stripslashes_recursively($temp_outputjson);

		$stop_service = microtime(true);
	}

	if(empty($json_feed)) {
		$response_string = json_encode(($temp_outputjson), JSON_INVALID_UTF8_IGNORE | JSON_PRETTY_PRINT);
	}
	else{
		$response_string = json_encode(($temp_outputjson['data']));
	}
	$response_string = str_replace('&apos;',"'", $response_string);
	if($log_mode == 2)
	{
		$gh -> Log($response_string);
	}

	$response_string = str_replace('\r\n', "", $response_string);
	$response_string = str_replace('\/', "/", $response_string);
	echo $response_string;

	function getUsersDetails($id, $is_md5) {
		global $db;
		$current_date = date('Y-m-d H:i:s');
		if($is_md5)
		{
			$query_user = "SELECT usr.*, 
				(SELECT COUNT(id) FROM `tbl_users_plan` WHERE user_id = usr.id AND STR_TO_DATE('$current_date','%Y-%m-%d %H:%i:%s') BETWEEN STR_TO_DATE(start_date,'%Y-%m-%d %H:%i:%s') AND STR_TO_DATE(end_date,'%Y-%m-%d %H:%i:%s') LIMIT 1) AS has_active_plan
				FROM tbl_users as usr WHERE md5(id) = '$id'";
			$rows = $db->execute($query_user);
		}
		else
		{
			$query_user = "SELECT usr.*,
				(SELECT COUNT(id) FROM `tbl_users_plan` WHERE user_id = usr.id AND STR_TO_DATE('$current_date','%Y-%m-%d %H:%i:%s') BETWEEN STR_TO_DATE(start_date,'%Y-%m-%d %H:%i:%s') AND STR_TO_DATE(end_date,'%Y-%m-%d %H:%i:%s') LIMIT 1) AS has_active_plan 
				FROM tbl_users as usr WHERE id = $id";
			$rows = $db->execute($query_user);
		}
		if ($rows != null && is_array($rows) && count($rows) > 0) {
			return $rows[0];
		}
		else{
			return null;
		}
	}
	
	function stripslashes_recursively($value) {
		// echo $value."+++";
		if($value)
		{
			$value = is_array($value) ?	array_map('stripslashes_recursively', $value) : (($value instanceof stdClass) ? $value : (isJson($value) ? $value : stripslashes($value)));
		}
		return $value;
	}

	function trim_value($value)
	{
		if(is_string($value)){
			$value = trim($value);    // this removes whitespace and related characters from the beginning and end of the string
		}
	}

	function isJson($string) {
		if($string && is_string($string) && strpos($string, "[") === 0){
			json_decode($string);
			return (json_last_error() == JSON_ERROR_NONE);
		}
		else{
			return false;
		}
	}

	interface MyPackageThrowable extends Throwable {}

	class MyPackageException extends Exception implements MyPackageThrowable {}



?>

