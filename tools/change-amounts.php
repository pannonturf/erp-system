<?php
/////////////////////////////////////
// change maximum possible amounts //
/////////////////////////////////////

// function amountsFunction(n, timespan)
// pages: amounts.php

require_once('../config/config.php');
$db = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.'', DB_USER, DB_PASS);
date_default_timezone_set('Europe/Budapest');

$n = $_POST['n'];
$amount = $_POST['amount'];
$timespan = $_POST['timespan'];
$currentdate = date("Y-m-d H:i:s");

if (isset($_COOKIE["userid"])) {
	$user = $_COOKIE["userid"];
}
elseif (isset($_SESSION["userid"])) {
	$user = $_SESSION['userid'];
}
else {
	$user = 0;
}

if (empty($user)) {
	$user = 0;
}

if ($n < 3) {
	$datum = "0000-00-00";
	$type = 0;
}
else {
	$datum = $_POST['datum'];
	$type = 1;
}

//Update operations
$sql = "INSERT INTO `amounts` (`id`, `datum`, `timespan`, `amount`, `user`, `edited`, `type`) VALUES (NULL, :datum, :timespan, :amount, :user, :edited, :type);";
$query = $db->prepare($sql);

$query->bindParam(":datum", $datum, PDO::PARAM_STR);
$query->bindParam(":timespan", $timespan, PDO::PARAM_STR);
$query->bindParam(":amount", $amount, PDO::PARAM_STR);
$query->bindParam(":user", $user, PDO::PARAM_STR);
$query->bindParam(":edited", $currentdate, PDO::PARAM_STR);
$query->bindParam(":type", $type, PDO::PARAM_STR);

$query->execute();
?>
