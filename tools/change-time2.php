<?php
require_once('../config/config.php');
$db = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.'', DB_USER, DB_PASS);

$id = $_POST['id'];
$time = $_POST['time'];

//Update operations
$sql = "UPDATE `trucks` SET `time` = :time WHERE `id` = :id";
$query = $db->prepare($sql);

$query->bindParam(":time", $time, PDO::PARAM_STR);
$query->bindParam(":id", $id, PDO::PARAM_STR);

$query->execute();
?>
