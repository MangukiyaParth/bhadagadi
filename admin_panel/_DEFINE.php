<?php
    ob_start();
    // if (session_status() == PHP_SESSION_NONE || !isset($_SESSION)) {
    //     session_start();
    // }

    error_reporting(0);
    error_reporting(E_ALL);

    date_default_timezone_set('Asia/Kolkata');
    ini_set("gd.jpeg_ignore_warning", 1);


    define("IS_DEVELOPMENT", true);
    define("IS_SANDBOX", false);
    define("IS_BETA", false);
    define("IS_PRODUCTION", (!IS_SANDBOX && !IS_BETA && !IS_DEVELOPMENT));
    define("EXTEND_BUCKET", (IS_SANDBOX || IS_BETA) ? '0000_sandbox/' : ( (IS_DEVELOPMENT) ? '0000_local/' : '') );
    define('ThrottleExceededErrorCode', '3001');
    define('sleepSec', 30);

    if(IS_PRODUCTION || IS_BETA || IS_SANDBOX) {
        $protocol = (@$_SERVER["HTTPS"] == "on") ? "https://" : "http://";
    }

    define('ONLY_DB_CONFIG', true);
    require_once(__DIR__."/../config/MANAGE_CONFIG.php");
    require_once(__DIR__."/_idiorm.php");

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

    if(IS_PRODUCTION)
    {
        define("API_SERVICE_URL", "https://".$_SERVER['HTTP_HOST']."/backend/");
        define("ADMIN_PANEL_URL", "https://".$_SERVER['HTTP_HOST']."/admin_panel/");
        define("ALLOW_EXTERNAL_SCRIPT","1");
        define("ALLOW_MIXPANEL_SCRIPT","1");
    }
    else
    {
        define("API_SERVICE_URL", "http://".$_SERVER['HTTP_HOST']."/product/bhadagadi/backend/");
        define("ADMIN_PANEL_URL", "http://".$_SERVER['HTTP_HOST']."/product/bhadagadi/admin_panel/");
        define("ALLOW_EXTERNAL_SCRIPT","0");
        define("ALLOW_MIXPANEL_SCRIPT","0");
    }
   
    // Default: false,  Set to true when releasing website changes so no one uses it.
    define("WEBSITE_UNDER_MAINTENANCE", false);
    define("PHPFASTCACHE_EXPIRE_SEC", 30*24*60*60); // 30 days

    header('Access-Control-Allow-Origin: *');
