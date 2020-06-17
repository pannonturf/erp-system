<?php
require_once('../config/config.php');
$db = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.'', DB_USER, DB_PASS);
date_default_timezone_set('Europe/Budapest');

$truck_id = $_POST['id'];
$status = 3;
$currentdate = date("Y-m-d H:i:s");

if (isset($_SESSION['userid'])) {
	$user = $_SESSION['userid'];
}
else {
	$user = $_COOKIE["userid"];
}

$sql = "UPDATE `trucks` SET `go` = :datum, `finisher` = :user, `status` = :status WHERE `id` = :id";
$query = $db->prepare($sql);
$query->bindParam(":id", $truck_id, PDO::PARAM_STR);
$query->bindParam(":datum", $currentdate, PDO::PARAM_STR);
$query->bindParam(":user", $user, PDO::PARAM_STR);
$query->bindParam(":status", $status, PDO::PARAM_STR);
$query->execute();

?>