<?php
//////////////////////////////////
// change planned date of order //
//////////////////////////////////

// function changeDay(id, type)
// pages: today.php, plan.php

require_once('../config/config.php');
$db = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.'', DB_USER, DB_PASS);
$db -> exec("set names utf8");
date_default_timezone_set('Europe/Budapest');

$today = date("Y-m-d");
$tomorrow = date('Y-m-d', strtotime('tomorrow'));

$id = $_POST['id'];
$type = $_POST['type'];


////////////
// get newDate and sort
if ($type == 1) {		// move from tomorrow to today (plan.php)

	// automatically set it to today 18:00 
	$newDate = $today." 18:00:00";

	//Find the right sort number (at the end of the list)
	$today_midnight = $today." 00:00:00";
	$tomorrow_midnight = $tomorrow." 00:00:00";

	$query = $db->prepare("SELECT * FROM `order` WHERE planneddate >= :today AND planneddate < :tomorrow AND `team` = 1 AND `type1` < 4 AND `project_id` = 0 AND `status` = 1 ORDER BY `sort` ASC");
	$query->bindParam(":today", $today_midnight, PDO::PARAM_STR);
	$query->bindParam(":tomorrow", $tomorrow_midnight, PDO::PARAM_STR);
	$query->execute(); 
	
	if ($query->rowCount() == 0) {
		$sort = 1;
	}
	else {
		$sort = $query->rowCount() + 1;		// number of items plus one -> end of list
	}
}

elseif ($type == 2) {	// move back from today to tomorrow (today.php)
	
	// get original time
	$query = $db->prepare("SELECT * FROM `order` WHERE `id` = :id");
	$query->bindParam(":id", $id, PDO::PARAM_STR);
	$query->execute();
	$result = $query->fetch(PDO::FETCH_OBJ);
	$time = $result->time;
	$newDate = $tomorrow." ".$time;

	//Find the right sort number (at the beginning of the list)
	$nextday_midnight = $tomorrow." 00:00:00";
	$after_midnight = date('Y-m-d', strtotime($tomorrow.' +1 day'))." 00:00:00";

	$query = $db->prepare("SELECT * FROM `order` WHERE planneddate >= :tomorrow AND planneddate < :aftertomorrow AND `team` = 1 AND `type1` < 4 AND `project_id` = 0 AND `status` = 1 ORDER BY `sort` ASC LIMIT 1");
	$query->bindParam(":tomorrow", $nextday_midnight, PDO::PARAM_STR);
	$query->bindParam(":aftertomorrow", $after_midnight, PDO::PARAM_STR);
	$query->execute(); 
	
	if ($query->rowCount() == 0) {
		$sort = 1;
	}
	else {
		$result = $query->fetch(PDO::FETCH_OBJ);
		$current_sort = $result->sort;
		$sort = $current_sort - 1;		// current sort minus 1 -> beginning of list
	}
}


///////////
// update database
$sql = "UPDATE `order` SET `sort` = :sort, `planneddate` = :datum WHERE `id` = :id";
$query = $db->prepare($sql);

$query->bindParam(":sort", $sort, PDO::PARAM_STR);
$query->bindParam(":datum", $newDate, PDO::PARAM_STR);
$query->bindParam(":id", $id, PDO::PARAM_STR);

$query->execute();

?>