<?php
include('functions.php');

require_once('../config/config.php');
$db = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.'', DB_USER, DB_PASS);
$db -> exec("set names utf8");

$currentyear = $_POST['year'];
$lastyear = $currentyear - 1;

$label1 = 'KR '.$currentyear;
$label2 = 'GR '.$currentyear;
$label3 = 'KR '.$lastyear;
$label4 = 'GR '.$lastyear;

$table = array();
$table['cols'] = array(
array('label' => 'm2', 'type' => 'string'),
array('label' => $label1, 'type' => 'number'),
array('label' => $label2, 'type' => 'number'),
array('label' => $label3, 'type' => 'number'),
array('label' => $label4, 'type' => 'number')
);

$rows = array();
////////////////
/// Total per month
for ($j=1; $j < 13; $j++) { 

	$month = $j;
	$temp = array();
	$temp[] = array('v' => $month);

	//get total amounts of the month     
	$total_month_this = 0;
	$total_month_this_small = 0;
	$total_month_this_big = 0;
	$startdate = $currentyear."-".$month."-01 00:00:00";
	$enddate = $currentyear."-".($month + 1)."-01 00:00:00";

	$query = $db->prepare("SELECT * FROM `order` WHERE `date` >= :startdate AND `date` < :enddate AND `status` = 4 ORDER BY `time` ASC");
	$query->bindParam(":startdate", $startdate, PDO::PARAM_STR);
	$query->bindParam(":enddate", $enddate, PDO::PARAM_STR);
	$query->execute(); 
	foreach ($query as $row) {
		$type1 = $row['type1'];
		$amount = amount_decrypt($row['amount'], $key2);
		$total_month_this += $amount;

		if ($type1 < 4) {
    		$total_month_this_small += $amount;
        }
        elseif ($type1 > 3) {
        	$total_month_this_big += $amount;
        } 
	}
	$small1 = $total_month_this_small / 10000;
	$big1 = $total_month_this_big / 10000;

	$temp[] = array('v' => $small1);
	$temp[] = array('v' => $big1);


	//get total amounts of the month last year    
	$total_month_last = 0;
	$total_month_last_small = 0;
	$total_month_last_big = 0;
	$startdate = $lastyear."-".$month."-01 00:00:00";
	$enddate = $lastyear."-".($month + 1)."-01 00:00:00";

	$query = $db->prepare("SELECT * FROM `order` WHERE `date` >= :startdate AND `date` < :enddate AND `status` = 4 ORDER BY `time` ASC");
	$query->bindParam(":startdate", $startdate, PDO::PARAM_STR);
	$query->bindParam(":enddate", $enddate, PDO::PARAM_STR);
	$query->execute(); 

	foreach ($query as $row) {
		$type1 = $row['type1'];
		$amount = amount_decrypt($row['amount'], $key2);
		$total_month_last += $amount;

		if ($type1 < 4) {
    		$total_month_last_small += $amount;
        }
        elseif ($type1 > 3) {
        	$total_month_last_big += $amount;
        }
	}

	$small2 = $total_month_last_small / 10000;
	$big2 = $total_month_last_big / 10000;

	$temp[] = array('v' => $small2);
	$temp[] = array('v' => $big2);

	$rows[] = array('c' => $temp);

}

$table['rows'] = $rows;
$jsonTable = json_encode($table);
echo $jsonTable;
    
?>