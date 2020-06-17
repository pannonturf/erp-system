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

$customers1 = 0;
$customers2 = 0;
$customers3 = 0;
$customers4 = 0;

$temp = array();
$temp[] = array('v' => '2000+');

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

    if ($total > 0) {
        if ($total < 500) {
            $customers1 += $total;
        }
        elseif ($total < 1000) {
            $customers2 += $total;
        }
        elseif ($total < 2000) {
            $customers3 += $total;
        }
        elseif ($total > 2000) {
            $customers4 += $total;
        }
    }
}

$temp[] = array('v' => $customers4);

$rows[] = array('c' => $temp);


$temp = array();
$temp[] = array('v' => '1000 - 2000');

$temp[] = array('v' => $customers3);

$rows[] = array('c' => $temp);


$temp = array();
$temp[] = array('v' => '500 - 1000');

$temp[] = array('v' => $customers2);

$rows[] = array('c' => $temp);


$temp = array();
$temp[] = array('v' => '0 - 500');

$temp[] = array('v' => $customers1);

$rows[] = array('c' => $temp);

$table['rows'] = $rows;
$jsonTable = json_encode($table);
echo $jsonTable;
    
?>