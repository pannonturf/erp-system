<?php
require_once('../config/config.php');
$db = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.'', DB_USER, DB_PASS);


$id = $_POST['id'];
$from = $_POST['from'];
$to = $_POST['to']

//Update operations
$sql = "UPDATE `order` SET `paid` = 1 WHERE `date` >= :startdate AND `date` <= :enddate AND `name` = :name AND `status` < 5";
$query = $db->prepare($sql);

$query->bindParam(":name", $id, PDO::PARAM_STR);
$query->bindParam(":startdate", $from, PDO::PARAM_STR);
$query->bindParam(":enddate", $to, PDO::PARAM_STR);

$query->execute();

?>
