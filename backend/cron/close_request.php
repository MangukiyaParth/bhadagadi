<?php
header("Content-type: application/json; charset=utf-8");

include("../_DEFINE.php");
include("../_SUPPORT.php");
require_once('../_DATABASE.php');

global $db, $const, $gh;
$db = new MysqliDB(db_host, db_user, db_pass, db_name);
$gh = new SUPPORT();

$db->update("tbl_vehicle_requirement", array("status"=>1), " STR_TO_DATE(CONCAT(pickup_date,' ',pickup_time),'%d/%m/%Y %h:%i %p') < NOW() ");
?>