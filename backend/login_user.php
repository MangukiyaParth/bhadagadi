<?php

function login_user()
{
	global $outputjson, $gh, $db, $const,$tz_name, $tz_offset, $phone_format;
	$outputjson['success'] = 0;

	$username = $gh->read("username");
	$username = addslashes(str_replace('&apos;', "'", $username));
	$password = $gh->read("password");
    $dateNow = date('Y-m-d H:i:s');

	if(empty($username)){
		$outputjson['message'] = "Username is required.";
		return;
	}
	if(empty($password)){
		$outputjson['message'] = "Password is required.";
		return;
	}

	$user_id = 0;
	$where = "( usr.username ='" . $username . "' ) ";
    $query_user = "SELECT usr.* FROM tbl_users as usr WHERE " . $where."";
    $rows = $db->execute($query_user);
    
    if ($rows != null && is_array($rows) && count($rows) > 0) {
		$user = $rows[0];
        $userPassword = $user['password'];
        
        // remove password from user object
        unset($user["password"]);
        
		if($userPassword == $password || $user_id > 0) {
            if($user['is_deleted'] == 1){
                $outputjson['message'] = "Your account is susspended, please contact admin";
                return;
            }

			$role_id = ($user['role_id'] == '') ? -1 : $user['role_id'];
			
			// disable the last login update when login in from Admin. so we can have the real last login dates.
            $update = array();
            $update['last_logged_in'] = $dateNow;
            if(count($update) > 0){
                $db->update("tbl_users", $update, array("user_id" => $user["user_id"]));
            }

            $outputjson['success'] = 1;
            $outputjson['global_search_flag'] = 1;
			$outputjson['message'] = 'User logged in successfully.';

			$city_preferance = [];
			$city_preferance_name = [];
			if(!empty($user['city_preferance']))
			{
				$city_preferance = json_decode($user['city_preferance']);
				$city_preferance_name = json_decode($user['city_preferance_name']);
			}
			$user["city_preferance"] = $city_preferance;
			$user["city_preferance_name"] = $city_preferance_name;
			
            $outputjson["data"] = $user;
		}
		else{
			$outputjson['message'] = "Invalid password. Try again or use Forgot Password. If you are an employee and do not have an email associated with your account, contact your Account Administrator.";
		}
	}
	else {
		$outputjson['message'] = "Your account is Inactive or this Username does not exist. Please try again";
	}

}

?>