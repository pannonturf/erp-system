<?php
require_once('../config/config.php');
$db = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.'', DB_USER, DB_PASS);


$id = $_POST['id'];
$field = $_POST['field'];
$value = $_POST['value']

//Update operations
$sql = "UPDATE `order` SET `".$field."` = :field WHERE `id` = :id";
$query = $db->prepare($sql);

$query->bindParam(":field", $value, PDO::PARAM_STR);
$query->bindParam(":id", $id, PDO::PARAM_STR);

$query->execute();

?>
