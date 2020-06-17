<?php
require_once('../config/config.php');
$db = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.'', DB_USER, DB_PASS);

$id = $_POST['id'];
$time = $_POST['time'];

$query = $db->prepare("SELECT * FROM `order` WHERE `id` = :id ");
$query->bindParam(":id", $id, PDO::PARAM_STR);
$query->execute();
$result = $query->fetch(PDO::FETCH_OBJ);
$datum = substr($result->planneddate, 0, 10);

$planneddate = $datum." ".$time;

//Update operations
$sql = "UPDATE `order` SET `planneddate` = :planneddate WHERE `id` = :id";
$query = $db->prepare($sql);

$query->bindParam(":planneddate", $planneddate, PDO::PARAM_STR);
$query->bindParam(":id", $id, PDO::PARAM_STR);

$query->execute();
?>
