<?php
require_once('../config/config.php');
$db = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.'', DB_USER, DB_PASS);
date_default_timezone_set('Europe/Budapest');

$id = $_POST['id'];
$status = $_POST['status'];

if ($status == 2) {
	$status2 = 3;
}
elseif ($status == 3) {
	$status2 = 4;
}
else {
	$status2 = 1;
}

$sql = "UPDATE `order` SET `project_status` = :status, `status` = :status2 WHERE `id` = :id";
$query = $db->prepare($sql);
$query->bindParam(":id", $id, PDO::PARAM_STR);
$query->bindParam(":status", $status, PDO::PARAM_STR);
$query->bindParam(":status2", $status2, PDO::PARAM_STR);
$query->execute();

?>