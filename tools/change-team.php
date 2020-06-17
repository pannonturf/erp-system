<?php
require_once('../config/config.php');
$db = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.'', DB_USER, DB_PASS);

$id = $_POST['id'];
$team = $_POST['team'];

if ($team == 1) {
	$team_alt = 2;
}
else {
	$team_alt = 1;
}

/*
$nextday = date('Y-m-d', strtotime('tomorrow'));

if ($day == 6) {
	$nextday = date('Y-m-d', strtotime($nextday.' +2 days'));
}
*/

$query = $db->prepare("SELECT * FROM `order` WHERE `id` = :id");
$query->bindParam(":id", $id, PDO::PARAM_STR);
$query->execute();
$result = $query->fetch(PDO::FETCH_OBJ);

$planneddate = $result->planneddate;

$date = substr($planneddate, 0, 10);
$plannedtime = substr($planneddate, 11, 8);
$old_sort = $result->sort;

$start = $date." 00:00:00";
$end = date('Y-m-d', strtotime($date.' +1 day'))." 00:00:00";

//Find the right sort number and update all orders behind it (+1)
$query = $db->prepare("SELECT * FROM `order` WHERE planneddate >= :start AND planneddate < :end AND `team` = :team AND `type1` < 4 AND `project_id` = 0 AND `status` < 3 ORDER BY `sort` DESC");
$query->bindParam(":start", $start, PDO::PARAM_STR);
$query->bindParam(":end", $end, PDO::PARAM_STR);
$query->bindParam(":team", $team, PDO::PARAM_STR);
$query->execute(); 


if ($query->rowCount() == 0) {
	$sort = 1;
}
else {
	$sort = $query->rowCount() + 1;
	foreach ($query as $row) {
		$next_id = $row['id'];
		$next_planneddate = $row['planneddate'];
		$next_plannedtime = substr($next_planneddate, 11, 8);

		if ($plannedtime < $next_plannedtime) {
			$sql2 = "UPDATE `order` SET `sort` = :sort WHERE `id` = :id";
			$query2 = $db->prepare($sql2);
		  	$query2->bindParam(":sort", $sort, PDO::PARAM_STR);  
		  	$query2->bindParam(":id", $next_id, PDO::PARAM_STR);  
			$query2->execute();

			$sort = $sort - 1;
		}
	}
}

//Update orders
$sql = "UPDATE `order` SET `sort` = :sort, `team` = :team WHERE `id` = :id";
$query = $db->prepare($sql);

$query->bindParam(":sort", $sort, PDO::PARAM_STR); 
$query->bindParam(":team", $team, PDO::PARAM_STR);
$query->bindParam(":id", $id, PDO::PARAM_STR);

$query->execute();



//Update list from team 1
$query = $db->prepare("SELECT * FROM `order` WHERE planneddate >= :start AND planneddate < :end AND `team` = :team AND `type1` < 4 AND `project_id` = 0 AND `status` < 3 ORDER BY `sort` DESC");
$query->bindParam(":start", $start, PDO::PARAM_STR);
$query->bindParam(":end", $end, PDO::PARAM_STR);
$query->bindParam(":team", $team_alt, PDO::PARAM_STR);
$query->execute(); 


if ($query->rowCount() > 0) {
	$sort = $query->rowCount();
	foreach ($query as $row) {
		$next_id = $row['id'];
		$next_sort = $row['sort'];
		if ($old_sort < $next_sort) {
			$sql2 = "UPDATE `order` SET `sort` = :sort WHERE `id` = :id";
			$query2 = $db->prepare($sql2);
		  	$query2->bindParam(":sort", $sort, PDO::PARAM_STR);  
		  	$query2->bindParam(":id", $next_id, PDO::PARAM_STR);  
			$query2->execute();

			$sort = $sort - 1;
		}
	}
}  
