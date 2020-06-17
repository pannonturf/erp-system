<?php
require_once('../config/config.php');
$db = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.'', DB_USER, DB_PASS);

$id = $_POST['id'];

//Update operations
$sql = "UPDATE `order` SET `id3` = 0 WHERE `id` = :id";
$query = $db->prepare($sql);

$query->bindParam(":id", $id, PDO::PARAM_STR);

$query->execute();
?>