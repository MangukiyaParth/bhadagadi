<?php
//error_reporting(0);
//error_reporting(E_ERROR | E_WARNING | E_PARSE);

global $DEBUG, $db, $mail, $const_session_key_value;

// Include composer autoloader
require __DIR__ . '/vendor/autoload.php';
use Phpfastcache\CacheManager;
use Phpfastcache\Config\ConfigurationOption;

class CacheHelper {

    // https://www.php.net/manual/en/language.oop5.static.php#101350

    protected static $InstanceCache = null;
    protected static $valid = false;

    /**
     * Prevent direct object creation
     */
    final private function  __construct() { }

    /**
     * Prevent object cloning
     */
    // final private function  __clone() { }

    /**
     * Returns new or existing Singleton instance
     * @return Singleton
     */
    final public static function GetInstance(){

        if(null !== static::$InstanceCache && static::$valid === 2){
            return static::$InstanceCache;
        }
        static::$InstanceCache = new static();

        $path = __DIR__ . "/upload/";
        if (IS_PRODUCTION == true) {
            $parent_dir1 = dirname(__FILE__); // /var/www/html/production/admin_panel
            $parent_dir2 = dirname(dirname(dirname(__FILE__))); // /var/www/html
            $parent_dir = str_replace($parent_dir2, "", $parent_dir1); //  /production/admin_panel
            //$this->Log("parent_dir = ".$parent_dir);

            $environment = IS_PRODUCTION == true ? "production" : "beta";
            
            // https://github.com/PHPSocialNetwork/phpfastcache/issues/614#issuecomment-600010179
            // change path to dedicated cache directory..  /tmp should be for temporary usage only.
            $path = "/www/".$environment."/logs/tmp/";

            //if (!is_dir($path)) mkdir($path, 0777, true);
            if (!is_dir($path) && !file_exists($path)) {
                $oldmask = umask(0);
                mkdir($path, 0777, true);
                umask($oldmask);
            }
        }
        CacheManager::setDefaultConfig(new ConfigurationOption([
            'path' => $path, // or in windows "C:/tmp/"
        ]));

        // In your class, function, you can call the Cache
        static::$InstanceCache = CacheManager::getInstance('files');

        static::$valid = 2;

        return static::$InstanceCache;
    }

}
class SUPPORT
{
	public $InstanceCache;
	
	function debug($obj, $force_display = false)
	{
		global $DEBUG;
		$force_display = isset($_REQUEST['debug']); // && $_REQUEST['debug'] == '1';
		if ($DEBUG == 1 || $force_display) {
			echo '<pre>';
			print_r($obj);
			echo '</pre>';
		}
	}

	function print_str($str)
	{
		// Order of replacement
		$order = array("\r\n", "\n", "\r");
		$replace = '<br />';

		// Processes \r\n's first so they aren't converted twice.
		$newstr = str_replace($order, $replace, $str);

		echo $newstr . '<br><br>';
	}

	function removeNull($input)
	{
		$ret = array();
		foreach ($input as $key => $val) {
			if (is_array($val))
				$ret[$key] = removeNull($val);
			else {
				if (!(is_null($val) || empty($val))) {
					$ret[$key] = $val;
				}
			}
		}
		return $ret;
	}
/*
if(get_magic_quotes_gpc()){
  $param = stripslashes($_POST['current_user']);
}
*/
	function read($param_name, $default = null, $input_is_json = false)
	{
		// POST
		if (isset($_POST[$param_name]) && is_array($_POST[$param_name])) {
			return $_POST[$param_name];
		} else if (isset($_POST[$param_name]) && is_string($_POST[$param_name])) {
			$ret = trim($_POST[$param_name]);
			if($input_is_json == false) $ret = addslashes(urldecode($ret));
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

	function serialNo($mail, $user)
	{

		$res = '';

		$tmp = '';
		for ($i = 0; $i < 4; $i++) {
			$tmp .= sprintf('%04X', mt_rand(0, 0xffff));
			//$res .= '-';
		}
		$res .= hash(mt_rand(0, 1) ? 'crc32' : 'crc32b', $tmp) . '-';
		$res .= hash('crc32b', mt_rand(0, 1) ? strtolower($tmp) : $user) . '-';
		$res .= hash(mt_rand(0, 1) ? 'adler32' : 'crc32', $mail) . '-';
		$res .= substr(hash('crc32', strrev($tmp)), 0, 5);

		return $res;
	}

	function multiRequest($data, $options = array())
	{
		// array of curl handles
		$curly = array();
		// data to be returned
		$result = array();

		// multi handle
		$mh = curl_multi_init();

		// loop into $data and create curl handles
		// then add them to the multi-handle
		if (count($data) > 0) {
			foreach ($data as $id => $d) {

				$curly[$id] = curl_init();

				$url = (is_array($d) && !empty($d['url'])) ? $d['url'] : $d;
				curl_setopt($curly[$id], CURLOPT_URL, $url);
				curl_setopt($curly[$id], CURLOPT_HEADER, 0);
				curl_setopt($curly[$id], CURLOPT_RETURNTRANSFER, 1);

				// post?
				if (is_array($d)) {
					if (!empty($d['post'])) {
						curl_setopt($curly[$id], CURLOPT_POST, 1);
						curl_setopt($curly[$id], CURLOPT_POSTFIELDS, $d['post']);
					}
				}

				// extra options?
				if (!empty($options)) {
					curl_setopt_array($curly[$id], $options);
				}

				curl_multi_add_handle($mh, $curly[$id]);
			}
		}

		// execute the handles
		$running = null;
		do {
			curl_multi_exec($mh, $running);
		} while ($running > 0);


		// get content and remove handles
		foreach ($curly as $id => $c) {
			$result[$id] = curl_multi_getcontent($c);
			curl_multi_remove_handle($mh, $c);
		}

		// all done
		curl_multi_close($mh);

		return $result;
	}

	function do_filter($ds, $col_name, $col_val, $get_single_record = false)
	{

		$result = array();

		foreach ($ds as $key => &$dr) {
			if ($dr[$col_name] == $col_val) {
				//unset($dr[$col_name]);
				$result[] = $dr;
			}
		}

		if (count($result) > 0 && $get_single_record == true) $result = $result[0];
		return $result;
	}

	function LogHeaders()
	{
		$headers = '';
		foreach ($_SERVER as $name => $value) {
			if (substr($name, 0, 5) == 'HTTP_') {
				$headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
			}
		}
		//get_headers($url, 1);
		//$this->Log($headers);
		return $headers;
	}

	function get_web_page($url)
	{
		$options = array(
			CURLOPT_RETURNTRANSFER => true,     // return web page
			CURLOPT_HEADER => false,    // don't return headers
			CURLOPT_FOLLOWLOCATION => false,     // follow redirects
			CURLOPT_ENCODING => "",       // handle all encodings
			CURLOPT_USERAGENT => "Planage", // who am i
			CURLOPT_AUTOREFERER => true,     // set referer on redirect
			CURLOPT_CONNECTTIMEOUT => 120,      // timeout on connect
			CURLOPT_TIMEOUT => 120,      // timeout on response
			CURLOPT_MAXREDIRS => 10,       // stop after 10 redirects
		);

		$ch = curl_init($url);
		curl_setopt_array($ch, $options);
		$content = curl_exec($ch);
		$err = curl_errno($ch);
		$errmsg = curl_error($ch);
		$header = curl_getinfo($ch);
		curl_close($ch);

		$header['errno'] = $err;
		$header['errmsg'] = $errmsg;
		$header['content'] = $content;
		return $header;
	}

	function is_exist($ds, $col_name, $col_val)
	{
		if (count($ds) <= 0) return false;

		foreach ($ds as $key => $dr) {
			if (count($dr) > 0) {
				if (isset($dr[$col_name]) && $dr[$col_name] == $col_val) {
					return true;
				}
			}
		}
		return false;
	}

	function getLocalDate($mysql_date, $tz = "UTC", $format = "Y-m-d H:i:s")
	{
		$UTC = new DateTimeZone("UTC");
		$date = new DateTime($mysql_date, $UTC);
		$newTZ = new DateTimeZone($tz);
		$date->setTimezone($newTZ);
		return $date->format($format);
	}

	function encrypt($string_to_encrypt)
	{
		$password = "TheSailonApp";
		$encrypted_string = openssl_encrypt($string_to_encrypt, "AES-128-ECB", $password);
		return $encrypted_string;
	}

	function decrypt($encrypted_string)
	{
		$encrypted_string = preg_replace('/\s+/', '+', $encrypted_string);
		$password = "TheSailonApp";
		$decrypted_string = openssl_decrypt($encrypted_string, "AES-128-ECB", $password);
		return $decrypted_string;
	}

	function auto_login_from_cookie()
	{
		if ($this->get_current_user() !== false) {
			$this->Log("Auto Login Not Needed");
			return false;
		}

		$encrypted_id = null;

		if (isset($_COOKIE)) {
			$encrypted_id = $this->get_cookie("user_1");
			$data = array();
			$data["operation"] = "login_user";
			$data["cookie_id"] = urlencode($encrypted_id);
			$result = $this->call_service($data);
			// NEED TO CHECK WHETHER RESPONSE IS VALID OR NOT
			if (is_array($result) && count($result) > 0 && isset($result['data'])) {
				//print_r($result);	
		        $this->Log("Auto logged in from cookie: ".$encrypted_id);

				$current_user = $result["data"];
				//echo "<pre>"; print_r($current_user);
				$this->set_user_data_in_session($current_user);
				return true;
			}
		}
		return false;
	}

	function set_user_data_in_session($current_user)
	{
		global $const_session_key_value;

		if(count($current_user) == 0) return false;

		$user_id = $current_user["id"];
		$session_key_value = $user_id.'_'.uniqid();
		$const_session_key_value = $session_key_value;
		$this->set_cookie("user_1", $this->encrypt($user_id));
		$this->set_cookie("session_key__1", $const_session_key_value);
		$this->Log(array(
			  "UserID" => $user_id
			, "UserInfo" => $this->get_if_exist(
					$current_user
					, array("first_name","last_name","company_id","company_name","email","username")
				)
			, "SessionKey" => $const_session_key_value
			, "cookie_user" => $this->encrypt($user_id)
			)
		);

		$this->set_session("all_data", $current_user);

		$this->Log("session_key_value 2 = ".$const_session_key_value);
		$this->set_session("user", $current_user);
		return false;
	}

	function get_if_exist($input_array, $keys){
		$ret = array();
		if(count($input_array) > 0){
			foreach ($keys as $key_name) {
				if(isset($input_array[$key_name])){
					$ret[] = $input_array[$key_name];
				}
			}
		}
		return $ret;
	}

	function get_current_user($col_name = "")
	{
		$user = $this->get_session("user");
		if ($user !== false) {
			if ($col_name == "") {
				return $user;
			} else {
				if (isset($user[$col_name])) {
					return $user[$col_name];
				} else {
					return "";
				}

			}
		}
		return false;
	}

    function is_primary_user(){
        $user = $this->get_session("user");
        if($user['is_primary_user'] == 1){
            return true;
        }
        else{
            return false;
        }
    }

    function has_module_access(){
        $module_pages = $this->get_session("module_pages");
        if(!$module_pages){
            $module_pages = [];
        }
        $current_page = basename($_SERVER['PHP_SELF']);
        if($current_page == 'index.php' || in_array($current_page,$module_pages)){
            return true;
        }
        else{
            return false;
        }
    }

    function has_module_access_to_update($module){
        
        $mdl = $this->get_session("modules");
        if (!is_array($mdl)) {
            $mdl = array();
        }
        $modules = array_merge($mdl, $this->get_session("rest_modules"));
        if(isset($modules[$module])){
            if($modules[$module]['can_write'] == 1){
                return true;
            }
        }
        return false;
    }

    function module_accessibility_message($module){
        $const = new ProjectConst();
        $msg = '';
        $modules = $this->get_session("modules");
        if(isset($modules[$module])){
            if($modules[$module]['can_write'] == 0) {
                $msg .= '<div class="col-md-12 center mb-lg preview-restrict-msg"><q>';
                if ($modules[$module]['has_company_access'] == 1) {
                    $msg .= $const->adminRestrictionMsg;
                } else {
                    $msg .= $const->accessLimitReadOnly;
                }
                $msg .= '</q></div>';
            }
        }
        return $msg;
    }

    function get_cookie($cookie_name){

		$cookie_name = (IS_BETA ? "beta_" : "").$cookie_name;

    	if(isset($_COOKIE[$cookie_name])){
    		return $_COOKIE[$cookie_name];
    	}
    	else
    	{
    		return null;
    	}
    }

    function set_cookie($cookie_name, $cookie_value){

    	$cookie_name = (IS_BETA ? "beta_" : "").$cookie_name;

    	setcookie(
    		$cookie_name 		// cookie name
    		, $cookie_value 	// cookie value
    		, time() + (365 * 24 * 60 * 60) 	// expires on
    		, '/' 	// path
    		, $_SERVER['SERVER_NAME']
    	);
    }

    function remove_cookie($cookie_name){

		$cookie_name = (IS_BETA ? "beta_" : "").$cookie_name;

    	setcookie(
    		$cookie_name 		// cookie name
    		, null 				// cookie value
    		, time() - 3600 	// expires on
    		, '/' 				// path
    		, $_SERVER['SERVER_NAME']
    	);
    }

	function get_session($object_name, $key = "")
	{
		global $const_session_key_value;
		
		if($const_session_key_value == ""){
			$const_session_key_value = $this->get_cookie("session_key__1");
		}

		$session_key =  $const_session_key_value;
		if($session_key == "") return false;
		$result = array();
		$is_exist = $this->read_from_cache_if_exist($session_key."_".$object_name, $result);

		if($is_exist){
			if (isset($key) && !empty($key)) {
				if (isset($result[$key])) {
					if (array_key_exists($key, $result)) {
						if (empty($result[$key])) {
							return "";
						} else
							return $result[$key];
					} else {
						return false;
					}
				} else {
					return false;
				}
			} else {
				return $result;
			}
		}
		else{
			return false;
		}  
	}


	function Log($msg, $sub_path = "")
	{
        $month_year = date('Y-m');

		if (is_array($msg)) $msg = print_r($msg, true);
		$path = "upload/_log/".$month_year."/".$sub_path."/";
		
		//if (!is_dir($path)) mkdir($path, 0777, true);
		if (!is_dir($path)) {
			$oldmask = umask(0);
			mkdir($path, 0777, true); 
			umask($oldmask);
		} 

		$msg = str_replace('\r\n', PHP_EOL, $msg);
		$msg = PHP_EOL . date('Y-m-d H:i:s') . ": " . $msg;
		file_put_contents($path . date('Y_m_d_H') . ".txt", $msg, FILE_APPEND | LOCK_EX);
	}

	function call_service($parameter = array())
	{
		$result = null;
		try 
		{
			$curl_handle = curl_init();
			curl_setopt($curl_handle, CURLOPT_URL, API_SERVICE_URL . "manage.php");
			curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($curl_handle, CURLOPT_POST, 1);
			curl_setopt($curl_handle, CURLOPT_POSTFIELDS, $parameter);
			$buffer = curl_exec($curl_handle);
			curl_close($curl_handle);
            
            // HR: 2018-04-17 MAKE SURE THIS IS NOT UNCOMMMENTED ON SERVER.. It creates 50MB log files every hour
            // $this->log('call service result is ');
            // $this->log($buffer);
            
            $data = $this->removeBOM($buffer);
			$result = json_decode($data, true);
			if(json_last_error() == JSON_ERROR_NONE){
				// Its a valid json.. so proceed..
			}
			else
			{
				$str = "";
				if(is_string($buffer)) {
					$str = $buffer;
				}
				if(is_array($buffer)) {
					$str = print_r($buffer, true);
				}
				$this->Log("ERROR: ".$str);	
			}
			// $result = json_decode(json_encode($result), true);
			
		} catch (Exception $e) {
			$this->Log($e);
		}
		return $result;
	}

	function call_service_284634($parameter = array())
	{
		$result = null;
		try 
		{
			$curl_handle = curl_init();
			curl_setopt($curl_handle, CURLOPT_URL, API_SERVICE_URL . "manage.php");
			curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($curl_handle, CURLOPT_POST, 1);
			curl_setopt($curl_handle, CURLOPT_POSTFIELDS, $parameter);
			$buffer = curl_exec($curl_handle);
			curl_close($curl_handle);
            
            // HR: 2018-04-17 MAKE SURE THIS IS NOT UNCOMMMENTED ON SERVER.. It creates 50MB log files every hour
            // $this->log('call service result is ');
            // $this->log($buffer);
			
			echo "1"."\n";
			print_r($buffer);

			$data = $this->removeBOM($buffer);
			echo "1.1"."\n";
			print_r($result);

			echo "1.2"."\n";

			$result = json_decode($data, true); //, 512, JSON_INVALID_UTF8_IGNORE);

			echo "2"."\n";
			print_r($result);

			echo "3"."\n";
			print_r(json_last_error());

			print_r(json_last_error_msg());


			/*
				0 = JSON_ERROR_NONE
				1 = JSON_ERROR_DEPTH
				2 = JSON_ERROR_STATE_MISMATCH
				3 = JSON_ERROR_CTRL_CHAR
				4 = JSON_ERROR_SYNTAX
				5 = JSON_ERROR_UTF8

			 */

			if(json_last_error() == JSON_ERROR_NONE){
				// Its a valid json.. so proceed..
			}
			else
			{
				$str = "";
				if(is_string($buffer)) {
					$str = $buffer;
				}
				if(is_array($buffer)) {
					$str = print_r($buffer, true);
				}
				$this->Log("ERROR: ".$str);	
				echo "5"."\n";
			}
			echo "6"."\n";

			print_r(json_encode($result));

			echo "8"."\n";
			print_r(json_encode($result));

			$result = json_decode(json_encode($result), true);
			echo "9"."\n";
			
		} catch (Exception $e) {
			echo "10"."\n";
			$this->Log($e);
		}
		return $result;
	}

	function removeBOM($data) {
	    if (0 === strpos(bin2hex($data), 'efbbbf')) {
	       return substr($data, 3);
	    }
	    return $data;
	}


	function set_session($object_name, $object_value)
	{
		global $const_session_key_value;
		$this->set_data_in_cache($const_session_key_value."_".$object_name, $object_value);
	}

	/** http://stackoverflow.com/a/118886/1005741
	 *  Given a file, i.e. /css/base.css, replaces it with a string containing the
	 *  file's mtime, i.e. /css/base.1221534296.css.
	 *  
	 *  @param $file  The file to be loaded.  Must be an absolute path (i.e.
	 *                starting with slash).
	 */
	function auto_version($file)
	{
	  //if(strpos($file, '/') !== 0 || !file_exists($_SERVER['DOCUMENT_ROOT'] . "/" . $file))
		if(!file_exists($_SERVER['DOCUMENT_ROOT'] . "/" . $file))
	    return $file;

		$mtime = filemtime($_SERVER['DOCUMENT_ROOT']."/" . $file);
		$filename = preg_replace('{\\.([^./]+)$}', ".$mtime.\$1", $file);
		//echo $filename;
		return $filename;
	}

	function __construct() {	}

	function read_from_cache_if_exist($key, &$result){
		global $outputjson, $db, $const;
		$is_exist = false;
		$result = array();
		if(!isset($key) || $key == null || $key == "") return null;
		try{
			$CachedString = CacheHelper::GetInstance()->getItem($key);
			if ($CachedString != null && $CachedString->isHit()) {
				$result = $CachedString->get();
				$is_exist = true;
			}
			return $is_exist;
		}catch (Exception $e) {
			return $is_exist;	
		}
	}

	function set_data_in_cache($key, $data){
		global $outputjson, $db, $const;
		if(!isset($key) || $key == null || $key == "") return null;
		$CachedString = CacheHelper::GetInstance()->getItem($key);
        if (!$CachedString->isHit()) {
            $CachedString->set($data)->expiresAfter(PHPFASTCACHE_EXPIRE_SEC);//in seconds, also accepts Datetime
            CacheHelper::GetInstance()->save($CachedString); // Save the cache item just like you do with doctrine and entities
        }
		return true;
	}

	function remove_data_from_cache($keys = array()){
		global $outputjson, $db, $const;

		if(!isset($keys) || $keys == null || count($keys) == 0) return null;

		foreach ($keys as $key) {
            $this->set_data_in_cache($key, null);
        }
		//$this->Psr16Adapter->deleteMultiple($keys);
		return true;
	}

	function set_browser_session($object_name, $object_value)
	{
		$this->Log("set_browser_session SESSION[".$object_name."]"); //" = ".print_r($object_value, true));
		if (session_status() == PHP_SESSION_NONE || !isset($_SESSION)) {
			session_start();
		}
		$_SESSION[$object_name] = $object_value;
		$this->Log(" == set_browser_session == ");
	}

	function get_browser_session($object_name, $key = ""){
		if (session_status() == PHP_SESSION_NONE || !isset($_SESSION)) {
			session_start();
		}
		if (isset($_SESSION[$object_name])) {
			if (isset($key) && !empty($key)) {
				if (isset($_SESSION[$object_name][$key])) {
					if (array_key_exists($key, $_SESSION[$object_name])) {
						if (empty($_SESSION[$object_name][$key])) {
							return "";
						} else
							return $_SESSION[$object_name][$key];
					} else {
						return false;
					}
				} else {
					return false;
				}
			} else {
				return $_SESSION[$object_name];
			}
		} else {
			return false;
		}
	}
        
        
//    START ADDED BY AKASH
    function encrypt_decrypt($action, $string) {
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

//    END ADDED BY AKASH

	// whether to give users an option to switch between old settings and new settings
	function allow_to_switch_settings() {
		return 0;
	}

	// whether new settings is enabled or not
	function is_new_settings_enabled() {
		$enable_old_settings = ((isset($this->get_session("settings")['enable_old_settings'])) ? $this->get_session("settings")['enable_old_settings'] : 0);
		$enable_old_settings = ((!empty($enable_old_settings)) ? $enable_old_settings : 0);
		// $this->Log("helper => enable_old_settings => " . $enable_old_settings);
		// if enable_old_settings = 1 then return 0, if enable_old_settings = 0 then return 1
		return !$enable_old_settings;
	}
}
?>