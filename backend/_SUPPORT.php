<?php
global $debug_mode, $db, $log_mode;
require_once '_ThumbImage.php';
class SUPPORT
{
	public $current_user;
	public $InstanceCache;


	public function get($input, $trim_special_characters = false)
	{
		$output = $input;
		$output = html_entity_decode($input, ENT_QUOTES | ENT_HTML5);
		if ($trim_special_characters) {
			$output = preg_replace("/[:,?;]/", '', $output);
		}
		return $output;
	}

	public function format_error($errno, $errstr, $errfile, $errline)
	{

		$trace = print_r(debug_backtrace(false), true);
		$content = '<table width="100%" border="1" cellspacing="1" cellpadding="5" style="border:1px solid red"><tr><th>Item</th><th>Description</th></tr><tr><th>Error</th><td><pre>' . $errstr . '</pre></td></tr><tr><th>#</th><td><pre>' . $errno . '</pre></td></tr><tr><th>File</th><td><pre>' . $errfile . '</pre></td></tr><tr><th>Line</th><td><pre>' . $errline . '</pre></td></tr><tr><th>Trace</th><td><pre>' . $trace . '</pre></td></tr></table>';

		return $content;
	}

	public function is_empty($value)
	{
		return (!isset($value)
			|| is_null($value)
			|| (!$value && $value != 0)
			|| ($value == "")
		);
	}

	public function read($param_name, $default = null, $input_is_json = false)
	{
		// POST
		if (isset($_POST[$param_name]) && is_array($_POST[$param_name])) {
			return $_POST[$param_name];
		} else if (isset($_POST[$param_name]) && is_string($_POST[$param_name])) {
			$ret = trim($_POST[$param_name]);
			if ($input_is_json == false) {
				$ret = addslashes(rawurldecode($ret));
			}

			return $ret;
		} // REQUEST
		else if (isset($_REQUEST[$param_name]) && is_array($_REQUEST[$param_name])) {
			return $_REQUEST[$param_name];
		} else if (isset($_REQUEST[$param_name]) && is_string($_REQUEST[$param_name])) {
			$ret = trim($_REQUEST[$param_name]);
			$ret = addslashes(urldecode($ret));
			return $ret;
		} else {
			return $default;
		}
		return $ret;
	}

	public function debug($obj, $force_display = false)
	{
		global $debug_mode, $outputjson;
		if ($debug_mode >= 1 || $force_display == true) {
			$outputjson["debug"][] = $obj;
		}
	}

	public function Log($__msg, $sub_path = "", $try_send_email = true)
	{
		//Comment this function because if I some text with word 'error', it goes to infinite loop
		if ($try_send_email) {
			// $this->send_email_if_needed($__msg);
		}

		$msg = "";
		$path = __DIR__ . "/";
		try {
			if (is_array($__msg)) {
				$msg = print_r($__msg, true);
			}

			if (is_object($__msg) && ($__msg instanceof Exception)) {
				$msg = $__msg->getMessage();
				$msg .= $__msg->getTraceAsString();
			}
			if (is_string($__msg)) {
				$msg = $__msg;
			}

			$requested_by_user_id = $this->read("user_id", "00000");
			$requested_by_user_id = ($requested_by_user_id == "0" || $requested_by_user_id == "") ? "00000" : $requested_by_user_id;
			$requested_by_user_id .= "/";

			$sub_path .= ($sub_path != "") ? "/" : "";

			$month_year = date('Y-m') . "/";
			if ($month_year == "2021-05/") {
				$month_year = "";
			}

			$path1 = $path . "upload/_log/" . $month_year;
			$this->TryCreateDirIfNeeded($path1);

			$path1 = $path . "upload/_log/" . $month_year . $sub_path;
			$this->TryCreateDirIfNeeded($path1);

			$path1 = $path . "upload/_log/" . $month_year . $sub_path . $requested_by_user_id;
			$this->TryCreateDirIfNeeded($path1);

			$msg = str_replace('\r\n', PHP_EOL, $msg);
			$msg = PHP_EOL . date('Y-m-d H:i:s') . ": " . $msg;
			file_put_contents($path1 . date('Y_m_d') . ".txt", $msg, FILE_APPEND | LOCK_EX);
		} catch (Throwable $t) {
			file_put_contents("upload/_log/catch_error.txt", PHP_EOL . date('Y-m-d H:i:s') . "  ==>  " . $t->getMessage(), FILE_APPEND | LOCK_EX);
		} catch (Exception $e) {
			file_put_contents("upload/_log/catch_error.txt", PHP_EOL . date('Y-m-d H:i:s') . "  ==>  " . $e->getMessage(), FILE_APPEND | LOCK_EX);
		}
	}

	public function TryCreateDirIfNeeded($dirpath, $mode = 0777)
	{

		// is_dir() - Tells whether the filename is a directory
		// file_exists — Checks whether a file or directory exists
		$success = false;

		try {

			if (is_dir($dirpath) && file_exists($dirpath)) {
				$success = true;
			} else if (!file_exists($dirpath)) {
				// When we have multiple calls first time (when dir not exist), the process is facing the race condition. so try to sleep the process with randon microseconds. 100000 = 100ms
				usleep(intval(rand(1, 100000)));

				$oldmask = umask(0);
				$success = @mkdir($dirpath, $mode, true);
				@chmod($dirpath, $mode);
				umask($oldmask);
			}
		} catch (Throwable $th_ex) {
			// ignore
		} catch (Exception $ex) {
			// ignore
		} finally {
			// not needed for now
		}
		return  $success;
	}

	public function LogQueryStart($query)
	{
		global $debug_mode, $outputjson, $log_mode;
		if ($debug_mode >= 1 || $log_mode >= 3) {
			$query_info = array();

			$query = str_replace('\r\n', " ", $query);
			$query = str_replace("\t", " ", $query);
			$query = str_replace("\r", " ", $query);
			$query = str_replace("\n", " ", $query);
			$query_info["query"] = $query;
			$query_start = microtime(true);
			$outputjson["query_info"][] = $query_info;

			return $query_start;
		}
		return null;
	}

	public function LogQueryResult($query_result)
	{
		global $debug_mode, $outputjson, $log_mode;

		if ($debug_mode <= 1 && $log_mode < 4) {
			return;
		}

		if (is_null($query_result)) {
			return;
		}

		if ($debug_mode >= 1 || $log_mode == 4) {
			$query_info = array_pop($outputjson["query_info"]);
			$query_info["output"] = $query_result;
			$outputjson["query_info"][] = $query_info;
		}

		if ($log_mode == 4 && isset($query_info)) {
			$this->Log($query_info, "QUERY");
			unset($outputjson["query_info"]);
		}
	}

	public function LogQueryEnd($query_start)
	{
		global $debug_mode, $outputjson, $log_mode;

		if (is_null($query_start)) {
			return;
		}

		if ($debug_mode >= 1 || $log_mode >= 3) {
			$query_stop = microtime(true);
			$query_time_diff = ($query_stop - $query_start) . ' seconds';
			$query_info = array_pop($outputjson["query_info"]);
			$query_info["time"] = $query_time_diff;
			$outputjson["query_info"][] = $query_info;
		}

		if ($log_mode == 3 && isset($query_info)) {
			$this->Log($query_info, "QUERY");
			unset($outputjson["query_info"]);
		}
	}

	public function encrypt($string_to_encrypt)
	{
		$password = "TheBhadaApp";
		$encrypted_string = openssl_encrypt($string_to_encrypt, "AES-128-ECB", $password);
		return $encrypted_string;
	}

	public function decrypt($encrypted_string)
	{
		$encrypted_string = preg_replace('/\s+/', '+', $encrypted_string);
		$password = "TheBhadaApp";
		$decrypted_string = openssl_decrypt($encrypted_string, "AES-128-ECB", $password);
		return $decrypted_string;
	}

	public function parse_json_to_array($json)
	{
		$json = htmlspecialchars_decode($json);
		return (array)json_decode($json);
	}

	public function call_service($parameter = array())
	{
		$result = null;
		try {
			$curl_handle = curl_init();
			curl_setopt($curl_handle, CURLOPT_URL, API_SERVICE_URL . "manage.php");
			curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($curl_handle, CURLOPT_POST, 1);
			curl_setopt($curl_handle, CURLOPT_POSTFIELDS, $parameter);
			$buffer = curl_exec($curl_handle);
			curl_close($curl_handle);
			$data = $this->removeBOM($buffer);
			$result = json_decode($data, true);
		} catch (Throwable $t) {
			// Executed only in PHP 7, will not match in PHP 5.x
			$this->Log($t);
		} catch (Exception $e) {
			$this->Log($e);
		}
		return $result;
	}

	function removeBOM($data)
	{
		if (0 === strpos(bin2hex($data), 'efbbbf')) {
			return substr($data, 3);
		}
		return $data;
	}

	public function get_client_ip()
	{
		$ipaddress = '';
		if (isset($_SERVER['HTTP_CLIENT_IP'])) {
			$ipaddress = $_SERVER['HTTP_CLIENT_IP'];
		} else if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else if (isset($_SERVER['HTTP_X_FORWARDED'])) {
			$ipaddress = $_SERVER['HTTP_X_FORWARDED'];
		} else if (isset($_SERVER['HTTP_FORWARDED_FOR'])) {
			$ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
		} else if (isset($_SERVER['HTTP_FORWARDED'])) {
			$ipaddress = $_SERVER['HTTP_FORWARDED'];
		} else if (isset($_SERVER['REMOTE_ADDR'])) {
			$ipaddress = $_SERVER['REMOTE_ADDR'];
		} else {
			$ipaddress = 'UNKNOWN';
		}

		return $ipaddress;
	}

	public function format_currency($amount)
	{
		$amount = str_replace("_", "", $amount);
		$amount = $amount == "" ? 0.00 : $amount;
		$currency = "USD ";
		$dec_point = ".";
		$thousands_sep = ",";

		$output = $currency . number_format($amount / 100, 2, $dec_point, $thousands_sep);
		return $output;
	}

	public function GetString($input)
	{
		if (isset($input) && $input != null) {
			$input = html_entity_decode($input, ENT_QUOTES | ENT_HTML5);
			return preg_replace('/(?!<[a-zA-Z=\"\':; ]*[^ ]>|<\\/[a-zA-Z="\':; ]*>)(<)/', "&lt;", $input);
		}
		return "";
	}

	function encrypt_decrypt($action, $string)
	{
		$output = false;
		$encrypt_method = "AES-256-CBC";
		$secret_key = 'This is my secret key';
		$secret_iv = 'This is my secret iv';
		// hash
		$key = hash('sha256', $secret_key);

		// iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
		$iv = substr(hash('sha256', $secret_iv), 0, 16);
		if ($action == 'encrypt') {
			$output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
			$output = base64_encode($output);
		} else if ($action == 'decrypt') {
			$output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
		}
		return $output;
	}

	function getRealIpAddr()
	{
		if (!empty($_SERVER['HTTP_CLIENT_IP'])) {   //check ip from share internet
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {   //to check ip is pass from proxy
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else {
			$ip = $_SERVER['REMOTE_ADDR'];
		}
		return $ip;
	}

	// In use for creating filename on Amazon S3 bucket.
	public function remove_special_char($filename)
	{

		// replace special characters except underscore, dash, dot
		$filename = preg_replace("/[^a-z0-9\s\_\-\.]/i", '', $filename);

		// replace multiple space with single
		$filename = preg_replace('!\s+!', ' ', $filename);

		// replace space with underscore
		$filename = str_replace(' ', '_', $filename);

		return $filename;
	}

	/* This Function is used for upload image. When thumb_needed is true is also upload thumbnail image in thumb folder */
	public function UploadImage($file, $thumb_needed)
	{

		// global $gh;

		$img_path = "";
		$thumb_needed = isset($thumb_needed) ? $thumb_needed : true;

		if (!isset($_FILES[$file]['size']) || $_FILES[$file]['size'] == '' || $_FILES[$file]['size'] <= 0) {
			return $img_path;
		}

		$prepend = time() . "_";
		if (isset($_FILES[$file]['name']) && $_FILES[$file]['name'] != '') {
			$success = move_uploaded_file($_FILES[$file]['tmp_name'], "upload/tmp/" . $prepend . $_FILES[$file]['name']);
			if ($success) {
				$img_path = UPLOAD . 'tmp/' . $prepend . $_FILES[$file]['name'];
				$this->Log('image uploaded: ' . $img_path);
			}

			if ($success && $thumb_needed == true) {
				$this->GetThumbnail("upload/tmp/" . $prepend . $_FILES[$file]['name'], "upload/tmp_thumb/" . $prepend . $_FILES[$file]['name'], 308);
				$thumb_img_path = UPLOAD . 'tmp_thumb/' . $prepend . $_FILES[$file]['name'];
				$this->Log('thumb uploaded: ' . $thumb_img_path);
			}
		}
		return $img_path;
	}

	/* This function is used for get thumbnail size image */
	public function GetThumbnail($big_img_path, $image_path, $wid)
	{
		$this->debug($image_path . " - " . $wid);
		$image = new ThumbImage();
		$image->load($big_img_path);
		if ($wid > 0 && $wid != $image->getWidth()) {
			$this->debug("Processing $image_path -> $wid");
			$image->resizeToWidth($wid);
			$result = $image->save($image_path);
			$this->debug($result);
			$this->debug("Processing done");
		} else {
			$this->debug("No processing needed for $image_path");
		}
	}

	function cleanup_enconding($str)
	{

		// for some reason, mb_convert is not working
		// echo mb_convert_encoding($msg_title, 'HTML-ENTITIES', 'UTF-8');

		$str = str_replace("â€™", "'", $str); // left single smart-quote
		$str = str_replace("â€˜", "'", $str); // right single smart-quote
		$str = str_replace("â€œ", "\"", $str); // left double smart-quotation mark
		$str = str_replace("â€", "\"", $str); // right double smart-quotation mark
		return $str;
	}

	public function SendAndroidPushNotification($device_token_array, $msg_title, $msg_body, $extra_args = array())
	{
		global $db;
		$this->Log(" ========= CALL SendAndroidPushNotification =============== ");
		$msg_body = strip_tags(str_replace("<br>", "\n", $msg_body));
		$fields = array(
			'registration_ids' => $device_token_array,
			'data' => array(
				'message' => $msg_body,
				'title' => $msg_title,
				'extra' => $extra_args,
			),
		);

		$headers = array(
			'Authorization: key=' . FCM_KEY,
			'Content-Type: application/json',
		);

		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($fields));

		$response = curl_exec($curl);
		$err = curl_error($curl);

		curl_close($curl);

		if ($err) {
			$this->Log("***** CURL ERROR ***** ");
			$this->Log($err);
		} else {
			$this->Log("Push Android Rsponse: " . print_r($response, true));
			if ($response && $response != null && strlen($response) > 0) {
				$json = (array)json_decode($response);
				if ($json != null) {
					if ($json["success"] == "1") {
					} else {
						$token_to_delete = array();
						if (isset($json['results']) && $json['results'] != null && count($json['results']) > 0) {
							foreach ($json['results'] as $index => $result) {
								if (isset($result) && isset($result->error)) {
									$token_to_delete[] = $device_token_array[$index];
								}
							}
						}

						// if(count($token_to_delete) > 0) {
						// 	$token_to_delete_string = "'".implode("','", $token_to_delete)."'";
						// 	$query = "delete from tbl_user_device_tokens where device_token in ($token_to_delete_string)";
						// 	$this->Log($query);
						// 	$res = $db->execute_query($query);
						// }
					}
				}
			} else {
				$this->Log("Push sent to android: " . $msg_title . " " . $msg_body);
			}
		}
	}
}
