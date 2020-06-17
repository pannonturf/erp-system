<?php 
/////////////////
// Logout user //
/////////////////

// start session
session_start();

//Update operations
require_once('config/config.php');
$db = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.'', DB_USER, DB_PASS);
$db -> exec("set names utf8");

$end =date("Y-m-d H:i:s");
$accessid = $_SESSION['accessid'];

$sql = "UPDATE `accesscontrol` SET `end` = :end WHERE `id` = :id";
$query = $db->prepare($sql);

$query->bindParam(":end", $end, PDO::PARAM_STR);
$query->bindParam(":id", $accessid, PDO::PARAM_STR);

$query->execute();

// Destroy user session
setcookie("login","3",time() - 3600);
setcookie("userid",$_SESSION['accessid'],time() - 3600);
unset($_SESSION['login']);
unset($_SESSION['accessid']);

// Redirect to index.php page
header("Location: index.php");


?>