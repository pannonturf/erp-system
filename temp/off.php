<?php 
include('../tools/functions.php');

// Database connection
require_once('../config/config.php');
$db = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.'', DB_USER, DB_PASS);

//Find the right sort number and update all orders behind it (+1)
$startdate = "2019-01-01 00:00:00";
$enddate = "2019-12-31 23:59:59";

$sql = "UPDATE `order` SET `temp` = :temp WHERE `id` = :id";

$query = $db->prepare("SELECT * FROM `order` WHERE planneddate >= :start AND planneddate < :end");

$query->bindParam(":start", $startdate, PDO::PARAM_STR);
$query->bindParam(":end", $enddate, PDO::PARAM_STR);
$query->execute(); 

foreach ($query as $row) {
	$id = $row['id'];
	$temp = 0;

	$query2 = $db->prepare($sql);
	$query2->bindParam(":id", $id, PDO::PARAM_STR);
	$query2->bindParam(":temp", $temp, PDO::PARAM_STR);
	$query2->execute();
}

echo "OK";

?>