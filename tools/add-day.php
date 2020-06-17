<?php
include('functions.php');

require_once('../config/config.php');
$db = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.'', DB_USER, DB_PASS);

$datum = $_POST['datum'];
$projectid = $_POST['project'];

$status = 1;

$query = $db->prepare("SELECT * FROM `trucks` WHERE `project` = :projectid ORDER BY `sort` DESC LIMIT 1");
$query->bindParam(":projectid", $projectid, PDO::PARAM_STR);
$query->execute();
$result = $query->fetch(PDO::FETCH_OBJ);
$oldsort = $result->sort;
$sort = $oldsort + 1;


if (isset($_SESSION['userid'])) {
	$creator = $_SESSION['userid'];
}
elseif (isset($_COOKIE['userid'])) {
	$creator = $_COOKIE["userid"];
}
else {
	$creator = 0;
}

$query = $db->prepare("INSERT INTO `trucks` (`id`, `project`, `sort`, `datum`, `status`, `creator`) VALUES (NULL, :projectid, :sort, :datum, :status, :creator)");

$query->bindParam(":projectid", $projectid, PDO::PARAM_STR);  
$query->bindParam(":sort", $sort, PDO::PARAM_STR);
$query->bindParam(":datum", $datum, PDO::PARAM_STR);
$query->bindParam(":status", $status, PDO::PARAM_STR);
$query->bindParam(":creator", $creator, PDO::PARAM_STR);
$query->execute(); 


?>


 
