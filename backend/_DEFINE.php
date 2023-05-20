<?php
if (session_status() == PHP_SESSION_NONE || !isset($_SESSION)) {
    @session_start();
}

error_reporting(0);
error_reporting(E_ALL);
date_default_timezone_set('Asia/Calcutta');
// server credentials file
require_once(__DIR__."/../config/MANAGE_CONFIG.php");

// for omr connection
include_once("_idiorm.php");

ORM::configure(array(
    'connection_string' => 'mysql:host='.db_host.';dbname='.db_name.';charset=utf8;',
    'username' => db_user,
    'password' => db_pass
));

ORM::configure('logging', true);
ORM::configure('caching', false);
ORM::configure('return_result_sets', true); // returns result sets

// // override the primary column if its not "id"
ORM::configure('id_column_overrides', array(
    'tbl_translation_hash' => 'hash_id',
    'tbl_translation_locale' => 'id',
));

define("UPLOAD", "upload/");
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Content-Range, Content-Disposition");
header('Access-Control-Allow-Methods: GET, HEAD, OPTIONS, POST, PUT');

/*
DEBUG_MODE = 0  // Do not debug
DEBUG_MODE = 1  // Append query only
DEBUG_MODE = 2  // Append query and output into JSON
 */
define("DEBUG_MODE", "0");

/*
LOG_MODE = 0  // Do not log
LOG_MODE = 1  // Log only request
LOG_MODE = 2  // Log request and response
LOG_MODE = 3  // Log only query
LOG_MODE = 4  // Log query & result to log file
 */
define("LOG_MODE", "1");

// Default: false,  Set to true when releasing website changes so no one uses it.
define("WEBSITE_UNDER_MAINTENANCE", false);

define("FCM_KEY", "AAAAzD3KQXA:APA91bEZ5U7r_7VvAsQFWoNP1ecburT_C0VUx9FVR1iWkeR53_AyMBdyOR5LZePcmEFaljQ--g0evovk59U3UTRttTIOhEdUxxYuzBPZPy9p_92Ce4rG-5A2-tQUR3m3YR5pvw6i9xHr");
