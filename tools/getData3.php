<?php
include('functions.php');

require_once('../config/config.php');
$db = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.'', DB_USER, DB_PASS);
$db -> exec("set names utf8");

//$currentyear = date("Y");
$currentyear = $_POST['year'];

$table = array();
$table['cols'] = array(
array('label' => 'Type1', 'type' => 'string'),
array('label' => 'm2', 'type' => 'number'),
);

$rows = array();

$total_small = 0;
$total_big = 0;

$temp = array();
$temp[] = array('v' => 'KR');

//get total amounts of the month     
$startdate = $currentyear."-01-01 00:00:00";
$enddate = $currentyear."-12-31 23:59:59";

$query = $db->prepare("SELECT * FROM `order` WHERE `date` >= :startdate AND `date` < :enddate AND `status` = 4");
$query->bindParam(":startdate", $startdate, PDO::PARAM_STR);
$query->bindParam(":enddate", $enddate, PDO::PARAM_STR);
$query->execute(); 
foreach ($query as $row) {
	$type1 = $row['type1'];
	$amount = amount_decrypt($row['amount'], $key2);
	
	if ($type1 < 4) {
		$total_small += $amount;
    }
    elseif ($type1 > 3) {
    	$total_big += $amount;
    } 
}

$small_display = $total_small / 10000;

$temp[] = array('v' => $small_display);

$rows[] = array('c' => $temp);

$temp = array();
$temp[] = array('v' => 'GR');

$big_display = $total_big / 10000;

$temp[] = array('v' => $big_display);

$rows[] = array('c' => $temp);


$table['rows'] = $rows;
$jsonTable = json_encode($table);
echo $jsonTable;
    
?>