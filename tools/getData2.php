<?php
include('functions.php');

require_once('../config/config.php');
$db = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.'', DB_USER, DB_PASS);
$db -> exec("set names utf8");

$currentyear = $_POST['year'];
$lastyear = $currentyear - 1;


$table = array();
$table['cols'] = array(
array('label' => 'm2', 'type' => 'string'),
array('label' => $currentyear, 'type' => 'number'),
array('label' => $lastyear, 'type' => 'number'),

);

$rows = array();

$cumulated_month_this = 0;
$cumulated_month_last = 0;
////////////////
/// Total per month
for ($j=1; $j < 13; $j++) { 

	$month = $j;
	$temp = array();
	$temp[] = array('v' => $month);

	//get total amounts of the month     
	$startdate = $currentyear."-".$month."-01 00:00:00";
	$enddate = $currentyear."-".($month + 1)."-01 00:00:00";

	$query = $db->prepare("SELECT * FROM `order` WHERE `date` >= :startdate AND `date` < :enddate AND `status` = 4 ORDER BY `time` ASC");
	$query->bindParam(":startdate", $startdate, PDO::PARAM_STR);
	$query->bindParam(":enddate", $enddate, PDO::PARAM_STR);
	$query->execute(); 
	foreach ($query as $row) {
		$type1 = $row['type1'];
		$amount = amount_decrypt($row['amount'], $key2);
		$cumulated_month_this += $amount;
	}

	$cumulated_month_this_display = $cumulated_month_this / 10000;

	$temp[] = array('v' => $cumulated_month_this_display);

	//get total amounts of the month last year    
	$startdate = $lastyear."-".$month."-01 00:00:00";
	$enddate = $lastyear."-".($month + 1)."-01 00:00:00";

	$query = $db->prepare("SELECT * FROM `order` WHERE `date` >= :startdate AND `date` < :enddate AND `status` = 4 ORDER BY `time` ASC");
	$query->bindParam(":startdate", $startdate, PDO::PARAM_STR);
	$query->bindParam(":enddate", $enddate, PDO::PARAM_STR);
	$query->execute(); 

	foreach ($query as $row) {
		$type1 = $row['type1'];
		$amount = amount_decrypt($row['amount'], $key2);
		$cumulated_month_last += $amount;
	}

	$cumulated_month_last_display = $cumulated_month_last / 10000;

	$temp[] = array('v' => $cumulated_month_last_display);

	$rows[] = array('c' => $temp);

}

$table['rows'] = $rows;
$jsonTable = json_encode($table);
echo $jsonTable;
    
?>