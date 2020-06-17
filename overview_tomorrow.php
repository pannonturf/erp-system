<?php
/////////////////////////////////
// List of orders for next day //
/////////////////////////////////
?>

<!DOCTYPE html>
<html lang="hu">
<head>
<meta charset="UTF-8"> 
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex">

<link href="style/bootstrap3.css" type="text/css" rel="stylesheet">
<link href="style/style.css" type="text/css" rel="stylesheet">

<script type="text/JavaScript">

  function printPage() {
      window.print();
  }
</script>

<title>Holnapi lista</title>

</head>

<body>

<div class="container" style="font-size: 15px;">

<?php

//$id = $_POST['id'];
$id = 7;

if ($id > 0) {

    include('tools/functions.php');

    require_once('config/config.php');
    $db = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.'', DB_USER, DB_PASS);
    $db -> exec("set names utf8");
    date_default_timezone_set('Europe/Budapest');

    // Get modus
    $query = $db->prepare("SELECT * FROM `system` WHERE `id` = 2");
    $query->execute(); 
    $result = $query->fetch(PDO::FETCH_OBJ);
    $modus = $result->active;

    // Get cutting modus
    $query = $db->prepare("SELECT * FROM `system` WHERE `id` = 3");
    $query->execute(); 
    $result = $query->fetch(PDO::FETCH_OBJ);
    $cutting_modus = $result->active;

    $days_short = array("V", "H", "K", "S", "C", "P", "S");

    $nextday = date('Y-m-d', strtotime('tomorrow'));
    $day = date('w', strtotime($nextday));

    if ($day == 6) {
        $nextday = date('Y-m-d', strtotime($nextday.' +2 days'));
        $nextdayname = date('w', strtotime($nextday));
        $dayHeading = $days[$nextdayname];
    }
    elseif ($day == 0) {
        $nextday = date('Y-m-d', strtotime($nextday.' +1 days'));
        
    }

    $datum = $nextday;

    $nextdayname = date('w', strtotime($nextday));
    $dayHeading = $days[$nextdayname];

    echo '<div class="row hidden-print"><div class="col-md-12">';
    echo '<button type="button" class="btn btn-secondary" style="float: right; margin-right: 15px;" onclick="printPage()">Nyomtatás</button>';
    echo '</div></div>';


    echo '<div class="row">';
    echo '<div class="col-md-12">';
    echo '<div class="panel panel-warning">';
    echo '<div class="panel-heading"><h4><span class="glyphicon glyphicon-th-list"></span>&nbsp;&nbsp;'.$dayHeading.', '.$datum.' –  Megrendelések</h4></div>';
    echo "<table class='table'>";

    echo "<tr class='title'><td>Sz.</td><td>Időpont</td><td>m&sup2;</td><td>Tipus</td><td>Szállitás</td><td>Rendszám</td><td>Szállitási cím</td><td>Fizetés</td></tr>";

    $query = $db->prepare("SELECT * FROM `order` WHERE `date` = :datum AND `status` < 5 AND `name` = :customer ORDER BY `time` ASC, `id` ASC");
    $query->bindParam(":datum", $datum, PDO::PARAM_STR);
    $query->bindParam(":customer", $id, PDO::PARAM_STR);
    $query->execute(); 

    foreach ($query as $row) {
        $id2 = $row['id2'];
        $id3 = $row['id3'];
        $prefix = $row['prefix'];
        $time = $row['time'];
        $type1 = $row['type1'];
        $type2 = $row['type2'];
        $type3 = $row['type3'];
        $amount = getAmount(amount_decrypt($row['amount'], $key2), $type3, $modus);
        $amount = getAmount2($amount, $type3, $modus);
        $delivery = $row['delivery'];
        $forwarder = $row['forwarder'];
        $payment = $row['payment'];
        $deliveryaddress = $row['deliveryaddress'];
        $city = $row['city'];
        $licence = $row['licence'];

        echo '<tr>';

        // ID
        $id3_display = substr($id3, -2);
        if ($id3_display == "00") {
            $id3_display = 100;
        }

        if ($id2 > 0 AND $cutting_modus == 1) {     // day prefix + number - cutting mode 1
            echo '<td><b>'.$prefix."-".$id2."</b>";
            
        }
        elseif ($id3 > 0 AND $cutting_modus == 2) {     // number running from 1 - 100 - cutting mode 2
            echo '<td style="padding-left: 10px;"><b>'.$id3_display."</b>";
        }
        else {
            echo "<td></td>";
        }

        // Time
        $timedisplay = substr($time, 0, 5);
        echo '<td>'.$timedisplay.'</td>';

        // Amount
        if ($type3 == 2) {
            echo '<td>'.$amount.' m&sup2;</td>';
        }
        else {
            echo "<td><mark style='background: green; color: white;'>".$amount.' m&sup2;</mark></td>';
        }
        
        // Type 2
        if ($type2 == 1) {
            $type2_display = "";        // Poa
        }
        elseif ($type2 == 0) {
            $type2_display = "err2";    // error
        }
        elseif ($type2 == 2) {          // Mediterran
            $type2_display = "<mark style='background: #f62323; color: white;'>MED</mark>";
        } 
        echo '<td>'.$type2_display."</td>";


        // Delivery
        if ($delivery == 1) {           // self collection
            $delivery_display = "István";
        }
        elseif ($delivery == 2) {       // delivery
            $query = $db->prepare("SELECT * FROM forwarder WHERE `id` = :id");
            $query->bindParam(":id", $forwarder, PDO::PARAM_STR);
            $query->execute();
            $result = $query->fetch(PDO::FETCH_OBJ);
            $delivery_display = $result->name;
        } 
        echo '<td>'.$delivery_display."</td>";

        // Licence plate
        echo '<td>'.$licence."</td>";

        // Delivery address
        $query = $db->prepare("SELECT * FROM cities WHERE `id` = :id");
        $query->bindParam(":id", $city, PDO::PARAM_STR);
        $query->execute();
        $result = $query->fetch(PDO::FETCH_OBJ);
        $city_disp = $result->name;
        $plz = $result->plz;

        $deliveryaddress_disp = $plz." ".$city_disp.", ".$deliveryaddress;
        echo '<td>'.$deliveryaddress_disp."</td>";


        // Payment method
        if ($payment == 1) {
            $payment_display = "kp";
        }
        elseif ($payment == 2) {
            $payment_display = "Átutalás";
        } 
        echo '<td>'.$payment_display."</td>";

        echo "</tr>";
    }


    echo "</div></div></div>";

}

echo "</div></body>";
