<?php
require_once('../config/config.php');
$db = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.'', DB_USER, DB_PASS);

$field = $_POST['id'];
$remain = $_POST['remain'];

$query = $db->prepare("SELECT * FROM fields WHERE `id` = :id");
$query->bindParam(":id", $field, PDO::PARAM_STR);
$query->execute();
$result = $query->fetch(PDO::FETCH_OBJ);
$size = $result->size;

$complete = round(1 - $remain / $size, 4);
  
if (isset($_SESSION['userid'])) {
    $user = $_SESSION['userid'];
  }
  elseif(isset($_COOKIE['userid'])){
    $user = $_COOKIE["userid"];
  }
  else {
    $user = 0;
  }

$currentdate = date("Y-m-d H:i:s");

$sql = "INSERT INTO `progress` (`id`, `datum`, `field`, `complete`, `user`) VALUES (NULL, :datum, :field, :complete, :user);";
$query = $db->prepare($sql);

$query->bindParam(":user", $user, PDO::PARAM_STR);  
$query->bindParam(":datum", $currentdate, PDO::PARAM_STR);
$query->bindParam(":field", $field, PDO::PARAM_STR);
$query->bindParam(":complete", $complete, PDO::PARAM_STR);;

$query->execute();


$query2 = $db->prepare("UPDATE `fields` SET `complete` = :complete WHERE `id` = :id");

$query2->bindParam(":id", $field, PDO::PARAM_STR);
$query2->bindParam(":complete", $complete, PDO::PARAM_STR);
$query2->execute(); 


?>