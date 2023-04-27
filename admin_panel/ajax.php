<?php
if (!isset($include_stylesheet_in_header)) $include_stylesheet_in_header = "";
if (!isset($include_stylesheet_in_footer)) $include_stylesheet_in_footer = "";
$include_javscript_at_bottom = "";
if (session_status() == PHP_SESSION_NONE || !isset($_SESSION)) {
	session_start();
}
global $DEBUG, $db, $gh, $const_session_key_value;

require_once('_DEFINE.php');

if(WEBSITE_UNDER_MAINTENANCE == true) {
	header("Location: maintenance.php");
	exit(0);
}

define("HANDLEBARS_PAGES", array("/", "index.php"));
require_once('_SUPPORT.php');

$gh = new SUPPORT();

if(!empty($gh->read("current_user_timezone")) && count($gh->read("current_user_timezone")) > 0) {
	$timezone = $gh->read("current_user_timezone");
	$gh->set_session("timezone", $timezone);
	setcookie(
		"timezone",
		$timezone,
		time() + (10 * 365 * 24 * 60 * 60), '/', str_replace("www", "", strtolower($_SERVER['SERVER_NAME']))
	);
	exit();
	return;
}

$current_page = basename($_SERVER['PHP_SELF']); /* Returns The Current PHP File Name */
$should_redirect_to = $_SERVER['REQUEST_URI'];
$current_module_name='';
if($gh->read("login") == "1")
{
	if($gh->read("current_user", null, true) != "")
	{
		$current_user_json = $gh->read("current_user", null, true);
		$current_user = json_decode($current_user_json, true);

		// if(json_last_error() != JSON_ERROR_NONE) {
		//  error_log('Last JSON error: '. json_last_error(). json_last_error_msg() . PHP_EOL. PHP_EOL,0);
		// }

		$gh->set_user_data_in_session($current_user);
		$gh->set_cookie("session_key__1", $const_session_key_value);
		$gh->Log($_COOKIE);
		$temp = $gh->get_cookie("session_key__1");
		$gh->Log("session_key__1 from cookie = ".$temp);

		$gh->Log("set_user_data_in_session -- DONE ". $const_session_key_value);

		echo json_encode(array("DONE ". $const_session_key_value));
		return ;
	}
	else
	{
		$gh->Log("NOT A JSON. HOW COME??". $current_page);
		$gh->Log(json_encode($gh->read("current_user")));
		return json_encode(array("ERROR"));
	}

}

$login_not_needed_pages = array("ajax.php", "login.php", "logout.php");
$header_not_needed_pages = array();
$gh->Log("Page View: ".$current_page." ".$_SERVER['REQUEST_URI']." ".print_r($gh->get_current_user("id"), true));

if ($gh->get_current_user() === false && !in_array($current_page, $login_not_needed_pages)) {
	$gh->Log("Auto Login Needed");

	if($gh->auto_login_from_cookie() === false) {
		$gh->Log("Auto Login Failed ".$should_redirect_to);
		header("Location: login.php");
		exit(0);
	}
	else
	{
		$gh->Log("Auto Login Success. Should rediect to ".$should_redirect_to);
		header("Location: ".$should_redirect_to);
		exit(0);
	}
}

if ($gh->get_current_user() !== false && ($current_page == "login.php")) {
	header("Location: index.php");
	exit(0);
}

$useragent = (isset($_SERVER['HTTP_USER_AGENT'])) ? $_SERVER['HTTP_USER_AGENT'] : '';
$logged_in_username = str_replace('"','\"', str_replace("'","\'",$gh->get_current_user("name")));

$include_javscript_library_before_custom_script_at_bottom = "<script>
	var IS_PRODUCTION = '" . IS_PRODUCTION ."';
	var IS_BETA = '" . IS_BETA ."';
	var IS_DEVELOPMENT = '" . IS_DEVELOPMENT ."';
	var WEB_API_FOLDER = '" . API_SERVICE_URL ."';
	var API_SERVICE_URL = '" . API_SERVICE_URL . "manage.php';
	var ADMIN_PANEL_URL = '" . ADMIN_PANEL_URL . "';
	var USER_AVATAR = '" . ADMIN_PANEL_URL . "images/user_avtar.png';
	var GLOBAL_STORAGE_KEY = 'bhadagadi_';
	var CURRENT_USER_ID = '". $gh->get_current_user("id")."';
	var CURRENT_USER_TOKEN = '". $gh->get_current_user("token")."';
	var LOGGED_IN_USERNAME = '". $logged_in_username ."';
	var CURRENT_COMPANY_ID = '". $gh->get_current_user("company_id")."';
	var HANDLEBARS_PAGES = ".json_encode(HANDLEBARS_PAGES).";
	</script>";

?>
