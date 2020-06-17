<?php
require_once('../config/config.php');
$db = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.'', DB_USER, DB_PASS);

$id = $_POST['id'];

if (isset($_SESSION['userid'])) {
	$user = $_SESSION['userid'];
}
else {
	$user = $_COOKIE["userid"];
}

$status = 5;
$deletedate = date("Y-m-d H:i:s");

// Get project_id
$query = $db->prepare("SELECT * FROM `order` WHERE `id` = :id");
$query->bindParam(":id", $id, PDO::PARAM_STR);
$query->execute();
$result = $query->fetch(PDO::FETCH_OBJ);
$project_id = $result->project_id;

// delete trucks
$query = $db->prepare("DELETE FROM `trucks` WHERE `project` = :project");
$query->bindParam(":project", $project_id, PDO::PARAM_STR);
$query->execute();
$result = $query->fetch(PDO::FETCH_OBJ);


//Update operations
$sql = "UPDATE `order` SET `status` = :status, `completer` = :deleter, `completedate` = :deletedate WHERE `id` = :id";
$query = $db->prepare($sql);

$query->bindParam(":status", $status, PDO::PARAM_STR);
$query->bindParam(":deleter", $user, PDO::PARAM_STR);
$query->bindParam(":deletedate", $deletedate, PDO::PARAM_STR);
$query->bindParam(":id", $id, PDO::PARAM_STR);

$query->execute();
?>