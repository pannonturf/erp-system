<?php
require_once('../config/config.php');
$db = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.'', DB_USER, DB_PASS);
date_default_timezone_set('Europe/Budapest');


$id = $_POST['id'];
$currentdate = date("Y-m-d H:i:s");

if (isset($_SESSION['userid'])) {
  $user = $_SESSION['userid'];
}
else {
  $user = $_COOKIE["userid"];
}

//Update order
$sql = "UPDATE `order` SET `pickup` = 1, `pickupdate` = :currentdate, `loader` = :user WHERE `id` = :id";
$query = $db->prepare($sql);
$query->bindParam(":id", $id, PDO::PARAM_STR);
$query->bindParam(":currentdate", $currentdate, PDO::PARAM_STR);
$query->bindParam(":user", $user, PDO::PARAM_STR);
$query->execute();


?>