<?php

require_once('../config/config.php');
$db = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.'', DB_USER, DB_PASS);
$db -> exec("set names utf8");

$customer_id=$_POST['customer_id'];


$query = $db->prepare("SELECT * FROM `customers` WHERE `id` = :id");
$query->bindparam(':id', $customer_id);
$query->execute(); 

$result = $query->fetch(PDO::FETCH_OBJ);
echo $result->name;
?>