<?php
include('../tools/functions.php');

require_once('../config/config.php');
$db = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.'', DB_USER, DB_PASS);
$db -> exec("set names utf8");
date_default_timezone_set('Europe/Budapest');

$today = date("Y-m-d");
$tomorrow = date('Y-m-d', strtotime('tomorrow'));
$yesterday = date('Y-m-d', strtotime('yesterday'));
$today_midnight = date("Y-m-d")." 00:00:00";
$tomorrow_midnight = $tomorrow." 00:00:00";
$yesterday_midnight = $yesterday." 00:00:00";
$after_midnight = date('Y-m-d', strtotime($tomorrow.' +1 day'))." 00:00:00";
$after = $tomorrow." 09:00:01";

$id = $_POST['id'];
$type = $_POST['type'];

// get planneddate, teamand old sort of selected order
$query3 = $db->prepare("SELECT * FROM `order` WHERE `id` = :id");
$query3->bindParam(":id", $id, PDO::PARAM_STR);
$query3->execute(); 
$result3 = $query3->fetch(PDO::FETCH_OBJ);
$planneddate = $result3->planneddate;
$team = $result3->team;
$old_sort = $result3->sort;

if ($type == 1) {
	$new_sort = $old_sort - 1;
}
elseif ($type == 2) {
	$new_sort = $old_sort + 1;
}

// get id of other order that is exchanged
$today = date("Y-m-d");
$tomorrow = date('Y-m-d', strtotime('tomorrow'));
$today_midnight = date("Y-m-d")." 00:00:00";
$tomorrow_midnight = $tomorrow." 00:00:00";

$query = $db->prepare("SELECT * FROM `order` WHERE planneddate >= :start AND planneddate < :end AND `sort` = :sort AND `team` = :team ORDER BY `sort` ASC LIMIT 1");
$query->bindParam(":start", $today_midnight, PDO::PARAM_STR);
$query->bindParam(":end", $tomorrow_midnight, PDO::PARAM_STR);
$query->bindParam(":team", $team, PDO::PARAM_STR);
$query->bindParam(":sort", $new_sort, PDO::PARAM_STR);
$query->execute(); 
$result = $query->fetch(PDO::FETCH_OBJ);
$other_id = $result->id;

// Update orders
$query = $db->prepare("UPDATE `order` SET `sort` = :sort WHERE `id` = :id");
$query->bindParam(":sort", $new_sort, PDO::PARAM_STR); 
$query->bindParam(":id", $id, PDO::PARAM_STR);
$query->execute();

$query->bindParam(":sort", $old_sort, PDO::PARAM_STR); 
$query->bindParam(":id", $other_id, PDO::PARAM_STR);
$query->execute();


?>