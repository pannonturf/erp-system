<?php
include('functions.php');

require_once('../config/config.php');
$db = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.'', DB_USER, DB_PASS);
$db -> exec("set names utf8");

$currentyear = $_POST['year'];

$table = array();
$table['cols'] = array(
array('label' => 'Type', 'type' => 'string'),
array('label' => 'Amount', 'type' => 'number'),
);

$rows = array();

$hungary = 0;
$austria = 0;
$others = 0;

//get total amounts of the month     
$startdate = $currentyear."-01-01 00:00:00";
$enddate = $currentyear."-12-31 23:59:59";

// Customer statistics this year
$query = $db->prepare("SELECT * FROM `customers` WHERE `status` = 1");
$query->execute(); 

foreach ($query as $row) {
    $id = $row['id'];
    $country = $row['country'];
    $total = 0;

    $query = $db->prepare("SELECT * FROM `order` WHERE `date` >= :startdate AND `date` <= :enddate AND `name` = :name AND `status` = 4");
    $query->bindParam(":startdate", $startdate, PDO::PARAM_STR);
    $query->bindParam(":enddate", $enddate, PDO::PARAM_STR);
    $query->bindParam(":name", $id, PDO::PARAM_STR);
    $query->execute(); 

    foreach ($query as $row) {
        $type1 = $row['type1'];
        $type2 = $row['type2'];
        $type3 = $row['type3'];
        $payment = $row['payment'];
        $delivery = $row['delivery'];
        $amount = amount_decrypt($row['amount'], $key2);
        
        $total += $amount;
    }   

    if ($country == 0) {
        $hungary += $total;
    }
    elseif ($country == 2) {
        $austria += $total;
    }
    else {
        $others += $total;
    }
}

$temp = array();
$temp[] = array('v' => 'Magyar');

$temp[] = array('v' => $hungary);

$rows[] = array('c' => $temp);


$temp = array();
$temp[] = array('v' => 'Osztrák');

$temp[] = array('v' => $austria);

$rows[] = array('c' => $temp);


$temp = array();
$temp[] = array('v' => 'Egyéb');

$temp[] = array('v' => $others);

$rows[] = array('c' => $temp);


$table['rows'] = $rows;
$jsonTable = json_encode($table);
echo $jsonTable;
    
?>