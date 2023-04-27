<?php

include('header.php');
$user = $gh->get_session('user');
$include_javscript_library_before_custom_script_at_bottom .= '<script type="text/javascript">
var LOGGED_IN_USER_ID = "' . (($user) ? $user['user_id'] : 0) . '";
var LOGGED_IN_COMPANY_ID = "' . (($user) ? $user['company_id'] : 0) . '";

</script>';

$include_javscript_at_bottom .= '<script src="'.$gh->auto_version('js/logout.js').'"></script>';


global $outputjson, $gh, $db, $DEBUG, $const_session_key_value;

$gh = new SUPPORT();
$result = array();

//$session_key = $gh->decrypt($gh->get_cookie("session_key_1"));
$session_key = $gh->get_cookie("session_key__1");

if($session_key == "") $session_key = $const_session_key_value;

$gh->remove_data_from_cache(array($session_key."_"."user")
);

$const_session_key_value = "";

session_regenerate_id(true);

if (session_status() == PHP_SESSION_ACTIVE|| isset($_SESSION)) {
	session_destroy();
}

$cookie_list = array('timezone','user','PHPSESSID','session_key','_ga','_gid','_gat','session_key_1','session_key__1','user','user_1');
foreach ($cookie_list as $key => $cookie_name) {
	// empty value and expiration one hour before
	$gh->remove_cookie($cookie_name);
	$res = setcookie($cookie_name, '', time() - 3600);
}

if(isset($_COOKIE)){
	foreach ($_COOKIE as $key => $value) {
		$gh->remove_cookie($key);
	}
}

?>
<?php
include('footer.php');
?>