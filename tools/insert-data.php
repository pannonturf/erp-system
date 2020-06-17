<?php

/*
Developer: Ehtesham Mehmood
Site:      PHPCodify.com
Script:    Insert Data in PHP using jQuery AJAX without Page Refresh
File:      Insert-Data.php
*/
require_once('../config/config.php');
$db = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.'', DB_USER, DB_PASS);
$db -> exec("set names utf8");

$customer_name=$_POST['customer_name'];
$contactperson=$_POST['contactperson'];
$plz=$_POST['plz'];
$city=$_POST['city'];
$street=$_POST['street'];
$phone=$_POST['phone'];
$email=$_POST['email'];

$delivery=$_POST['delivery'];
$payment=$_POST['payment'];
$country=$_POST['country'];
$area=$_POST['area'];
$status = 1;
$type = 1;
$added = date("Y-m-d");
$equal = 0;

if (!empty($customer_name)) {
	$query = $db->prepare("SELECT * FROM `customers`");
    $query->execute(); 

    foreach ($query as $row) {
        $name = $row['name'];
        
        if ($name == $customer_name) {
        	$equal = 1;
        	break;
        }
    }

    if ($equal == 0) {
		$stmt = $db->prepare("INSERT INTO customers(id, name, contactperson, street, plz, city, phone, email, status, type, delivery, payment, country, area, added) VALUES(NULL, :name, :contactperson, :street, :plz, :city, :phone, :email, :status, :type, :delivery, :payment, :country, :area, :added)");
		 
		$stmt->bindparam(':name', $customer_name);
		$stmt->bindParam(":contactperson", $contactperson);
		$stmt->bindParam(":street", $street);
		$stmt->bindParam(":plz", $plz);
		$stmt->bindParam(":city", $city);
		$stmt->bindParam(":phone", $phone);
		$stmt->bindParam(":email", $email);
		$stmt->bindparam(':status', $status);
		$stmt->bindparam(':type', $type);
		$stmt->bindparam(':delivery', $delivery);
		$stmt->bindparam(':payment', $payment);
		$stmt->bindparam(':country', $country);
		$stmt->bindparam(':area', $area);
		$stmt->bindparam(':added', $added);
		if($stmt->execute())
		{
			$query = $db->prepare("SELECT * FROM `customers` ORDER BY id DESC LIMIT 1");
			$query->execute(); 
			$result = $query->fetch(PDO::FETCH_OBJ);
			$customer_id = $result->id;

		  	$res="Sikerült!";
		  	echo json_encode(array($res, $customer_name, $customer_id, $contactperson, $street, $plz, $city, $phone, $email, $delivery, $payment));
		  	//echo json_encode($res);
		}
		else {
		  $error="Nem sikerült. Kérdezz meg Bernhard-töl!";
		  echo json_encode(array($error, "", "0"));
		}
	}
	else {
		$error="Nem sikerült. A név már létezik.";
		echo json_encode(array($error, "", "0"));
	}
}
else {
	$error="Nem sikerült. Írja be egy nevet.";
	echo json_encode(array($error, "", "0"));
}
 
 
 
 ?>