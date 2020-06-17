<?php
require_once('../config/config.php');
$db = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.'', DB_USER, DB_PASS);
$db -> exec("set names utf8");
date_default_timezone_set('Europe/Budapest');

$field = $_POST['field'];
$type = $_POST['type'];
$amount = $_POST['amount'];
$team = $_POST['team'];
$day = $_POST['day'];

$now = date("Y-m-d H:i:s");
$today = date("Y-m-d");
$tomorrow = date("Y-m-d",strtotime("tomorrow"));

if ($day == 1) {
	$datum = $today;
}
else {
	$datum = $tomorrow;
}

//get last id2 of the day
$query = $db->prepare("SELECT * FROM `pallets` WHERE `datum` = :datum ORDER BY `id2` DESC LIMIT 1");
$query->bindParam(":datum", $datum, PDO::PARAM_STR);
$query->execute(); 

if ($query->rowCount() > 0) {
	$result = $query->fetch(PDO::FETCH_OBJ);
	$current_id2 = $result->id2;
	$id2 = $current_id2 + 1;
}
else {
	$id2 = 1;
}

$status = 1;


$query = $db->prepare("INSERT INTO `pallets` (`id`, `id2`, `datum`, `type`, `amount`, `field`, `team`, `status`, `cutdate`) VALUES (NULL, :id2, :datum, :type, :amount, :field, :team, :status, :cutdate)");

$query->bindParam(":id2", $id2, PDO::PARAM_STR);
$query->bindParam(":datum", $datum, PDO::PARAM_STR);
$query->bindParam(":type", $type, PDO::PARAM_STR);
$query->bindParam(":amount", $amount, PDO::PARAM_STR);
$query->bindParam(":field", $field, PDO::PARAM_STR);
$query->bindParam(":team", $team, PDO::PARAM_STR);
$query->bindParam(":status", $status, PDO::PARAM_STR);
$query->bindParam(":cutdate", $now, PDO::PARAM_STR);
$query->execute(); 


?>


 
