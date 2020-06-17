<?php

require_once('../config/config.php');
$db = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.'', DB_USER, DB_PASS);
$db -> exec("set names utf8");

$id = $_POST['id'];


$query = $db->prepare("SELECT * FROM `forwarder` WHERE `id` = :id");
$query->bindparam(':id', $id);
$query->execute(); 

$result = $query->fetch(PDO::FETCH_OBJ);
echo $result->licence;
?>