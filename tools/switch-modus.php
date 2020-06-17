<?php
require_once('../config/config.php');
$db = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.'', DB_USER, DB_PASS);

$mode = $_POST['mode'];

//Update settings
$sql = "UPDATE `system` SET `active` = :active WHERE `id` = 3";
$query = $db->prepare($sql);

$query->bindParam(":active", $mode, PDO::PARAM_STR);

$query->execute();
?>
