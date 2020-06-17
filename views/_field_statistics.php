<?php 
/////////////////////////////////////////////
// Statistics about current and old fields //
/////////////////////////////////////////////

require_once('config/config.php');
include('views/_header'.$header.'.php');
include('tools/functions.php');

$db = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.'', DB_USER, DB_PASS);
$db -> exec("set names utf8");

echo '<div class="inputform">';
echo '<h3 style="margin-top:10px;">Terület statisztika</h3><br>';

$startdate = $currentyear."-01-01 00:00:00";
$enddate = $currentyear."-12-31 23:59:59";
$currentmonth = date("n");


//////////////////////////////////////
// show inventory on fields

$old_sport = "";
$old_garden = "";
$old_med = "";
$new_sport = "";
$new_garden = "";
$new_med = "";

$sums = array();

for ($i=1; $i < 10; $i++) { 
	$sums[$i] = 0;
}

// get all fields that are not finished yet
$query = $db->prepare("SELECT * FROM `fields` WHERE `complete` < 1 ORDER BY `start` DESC");
$query->execute(); 


foreach ($query as $row) {
	$id = $row['id'];
	$fieldname = $row['name'];
	$start = $row['start'];
	$startyear = substr($start, 0, 4);
	$end = $row['end'];
	$size = $row['size'];
	$size_disp = number_format($size, 1, ",", ".");
	$field_seed = $row['seed'];
	$complete = $row['complete'];
	$type = $row['type'];

	$total = 0;
	$total2 = 0;
	$new_tr = "";

	$query = $db->prepare("SELECT * FROM `order` WHERE `field` = :field AND (`status` = 4 OR `status` = 3) ORDER BY `date` DESC");
	$query->bindParam(":field", $id, PDO::PARAM_STR);
	$query->execute(); 

	foreach ($query as $row) {
        $amount = amount_decrypt($row['amount'], $key2);
        $total += $amount;
        $datum = $row['date'];
    }

    // get all harvesting since last inventory ($datum)
    $query2 = $db->prepare("SELECT * FROM `progress` WHERE `field` = :field ORDER BY `datum` DESC LIMIT 1");
	$query2->bindParam(":field", $id, PDO::PARAM_STR);
	$query2->execute(); 

	$count = $query2->rowCount();
	if ($count > 0) {

		foreach ($query2 as $row) {
		    $progress_datum = $row['datum'];
		    $complete_last = $row['complete'];
		}
	    $size_last = $size * (1 - $complete_last);
    }
    else {
    	$size_last = $size;
    }

	$currentdate = date("Y-m-d H:i:s");

	$query = $db->prepare("SELECT * FROM `order` WHERE `field` = :field AND `date` < :end AND `date` > :start AND (`status` = 4 OR `status` = 3) ORDER BY `date` DESC");
	$query->bindParam(":field", $id, PDO::PARAM_STR);
	$query->bindParam(":end", $currentdate, PDO::PARAM_STR);
	$query->bindParam(":start", $progress_datum, PDO::PARAM_STR);
	$query->execute(); 

    foreach ($query as $row) {
          $amount = amount_decrypt($row['amount'], $key2);
          $total2 += $amount;
      }

    $current_size = ($size_last - ($total2 / 10000))*0.92; // 8% trash in average
    $seeded_size = ($size_last - ($total2 / 10000)); 
    $percentage = ($current_size / $size) * 100;

    $soll_output = $total / 10000;
    $ist_output = $size - ($size_last - ($total2 / 10000));
    $schwund = ($ist_output - $soll_output) / $ist_output * 100; // IST/SOLL Vergleich

    $query2 = $db->prepare("SELECT * FROM seed WHERE `id` = :id");
    $query2->bindParam(":id", $field_seed, PDO::PARAM_STR);
    $query2->execute();
    $result2 = $query2->fetch(PDO::FETCH_OBJ);
    $seedname = $result2->name;

    $new_tr .= "<tr>";
    $new_tr .= "<td style='text-align:left'><b><a href='https://turfgrass.site/fields.php?field=".$id."'>".$fieldname."</a></b></td>";
    $new_tr .= '<td style="background-color: #ececec;">'.number_format($current_size, 2, ",", ".")." ha</b></td>";
    $new_tr .= '<td><i>'.number_format($percentage, 0, ",", ".")." %</i></td>"; 
	$new_tr .= "<td>".$size_disp." ha</b></td>";

	if ($total > 0) {
		$new_tr .= '<td>'.number_format($total/10000, 2, ",", ".")." ha</td>";	
		$new_tr .= "<td class='border'><i>".number_format($schwund, 0, ",", ".")." %</i></td>";
	}
	else {
		$new_tr .= '<td>-</td><td class="border">-</td>';
	}

	$new_tr .= "<td>".$start."</td>";
	$new_tr .= "<td class='border'>".$datum."</td>";
	$new_tr .= "<td>".$seedname."</td>";
	$new_tr .= "</tr>";

	if (($currentmonth > 7 AND $startyear < $currentyear) OR ($currentmonth < 8 AND $startyear < ($currentyear - 1))) {		// make separation between old seeding date and new one
		$sums[1] += $current_size;	
		if ($type == 1) {
			$old_sport .= $new_tr;
			$sums[2] += $current_size;
		}
		elseif ($type == 2) {
			$old_garden .= $new_tr;
			$sums[3] += $current_size;
		}
		elseif ($type == 3) {
			$old_med .= $new_tr;
			$sums[4] += $current_size;
		}
	}
	else {
		$sums[5] += $current_size;
		$sums[9] += $seeded_size;
		if ($type == 1) {
			$new_sport .= $new_tr;
			$sums[6] += $current_size;
		}
		elseif ($type == 2) {
			$new_garden .= $new_tr;
			$sums[7] += $current_size;
		}
		elseif ($type == 3) {
			$new_med .= $new_tr;
			$sums[8] += $current_size;
		}
	}
}

$sum_total = $sums[1] + $sums[5];

echo '<div class="row"><div class="col-md-12">';
echo "<table class='table centertext'>";
echo "<tr class='title'><td style='text-align:left'>Terület</td><td>Eladó</td>";
echo "<td>%</td>";
echo "<td>Vetve</td>";
echo "<td>Eladva</td>";
echo "<td class='border'>Schwund</td>";
echo "<td>Vetés</td>";
echo "<td class='border'>Vágás kezdése</td>";
echo "<td>Mag</td>";
echo "</tr><tbody>";

echo '<tr><td style="text-align: left; padding-top: 20px;"><i>Sport</i></td><td style="padding-top: 20px; font-weight: bold;">'.number_format($sums[2], 2, ",", ".").' ha</td><td colspan="6"></td></tr>';
echo $old_sport;
echo '<tr><td style="text-align: left; padding-top: 20px;"><i>Kert</i></td><td style="padding-top: 20px; font-weight: bold;">'.number_format($sums[3], 2, ",", ".").' ha</td><td colspan="7"></td></tr>';
echo $old_garden;
echo '<tr><td style="text-align: left; padding-top: 20px;"><i>Mediterrán</i></td><td style="padding-top: 20px; font-weight: bold;">'.number_format($sums[4], 2, ",", ".").' ha</td><td colspan="7"></td></tr>';
echo $old_med;
echo '<tr class="sum_row"><td class="bg_main" style="text-align: left; padding-top: 20px; font-size: 18px;">ŐREG FŰ</td><td class="bg_main" style="padding-top: 20px; font-size: 18px;">'.number_format($sums[1], 1, ",", ".").' ha</td><td colspan="7"></td></tr>';

echo '<tr class="sum_row"><td style="text-align: left; padding-top: 40px;"><i>Sport</i></td><td style="padding-top: 40px; font-weight: bold;">'.number_format($sums[6], 2, ",", ".").' ha</td><td colspan="6"></td></tr>';
echo $new_sport;
echo '<tr><td style="text-align: left; padding-top: 20px;"><i>Kert</i></td><td style="padding-top: 20px; font-weight: bold;">'.number_format($sums[7], 2, ",", ".").' ha</td><td colspan="7"></td></tr>';
echo $new_garden;
echo '<tr><td style="text-align: left; padding-top: 20px;"><i>Mediterrán</i></td><td style="padding-top: 20px; font-weight: bold;">'.number_format($sums[8], 2, ",", ".").' ha</td><td colspan="7"></td></tr>';
echo $new_med;
echo '<tr><td class="bg_main" style="text-align: left; padding-top: 20px; font-size: 18px;">FIATAL FŰ</td><td class="bg_main" style="padding-top: 20px; font-size: 18px;">'.number_format($sums[5], 1, ",", ".").' ha</td><td></td><td style="padding-top: 22px;"><i>Vetés: '.number_format($sums[9], 1, ",", ".").' ha</i></td><td colspan="5"></td></tr>';
echo '<tr class="sum_row"><td style="padding-top: 20px;"></td><td colspan="7"></td></tr>';

echo "<tr class='total_row2'><td class='total_row' style='text-align: left;'>TOTAL</td><td class='total_row'>".number_format($sum_total, 1, ",", ".")." ha</td><td colspan='6'></td></tr>";


echo '</tbody></table>'; 
echo "</div></div><br><br><br>";




//////////////////////////////////////
// historical data
echo '<div class="row"><div class="col-md-12">';
echo '<h4><span class="glyphicon glyphicon-time"></span>&nbsp;&nbsp; Történet</h4>';
echo "<table class='table table-striped centertext'>";
echo "<tr class='title'><td style='text-align:left'>Terület</td><td>Vetve</td>";
echo "<td>Eladva</td>";
echo "<td>Még marad</td>";
echo "<td class='border'>%</td>";
echo "<td>Vetés</td>";
echo "<td>Vágás kezdése</td>";
echo "<td class='border'>Befejezett</td>";
echo "<td>Mag</td>";
echo "</tr><tbody>";

///// ZEHETBAUER
echo '<tr><td style="text-align: left; padding-top: 20px;">2019</td><td colspan="8"></td></tr>';

$open_zb = 0;
$sold_zb = 0;
$query = $db->prepare("SELECT * FROM `order` WHERE `field` = 222222 ORDER BY `date` DESC");
$query->execute(); 

foreach ($query as $row) {
    $amount = amount_decrypt($row['amount'], $key2);
    $status = $row['status'];

    if ($status == 1) {
    	$open_zb += $amount;
    }
    elseif ($status < 5) {
    	$sold_zb += $amount;
    }
}

echo "<tr>";
echo "<td style='text-align:left'><b>ZEHETBAUER</b></td>";
echo '<td></td>';
echo "<td><b>".number_format($sold_zb, 0, ",", ".")." m&sup2;</b></td>";
echo "<td>".number_format($open_zb, 0, ",", ".")." m&sup2;</td>";
echo '<td class="border"></td>';	
echo "<td></td>";
echo "<td></td>";
echo "<td class='border'></td>";
echo "<td></td>";
echo "</tr>";



$currentyear = date("Y");
for ($j=0; $j < 10; $j++) { 
	$year = $currentyear - $j;
	$startdate = $year."-01-01 00:00:00";
	$enddate = $year."-12-31 23:59:59";

	//get data of fields from database
	$query = $db->prepare("SELECT * FROM fields WHERE `start` >= :startdate AND `start` < :enddate AND `sold` > 0 ORDER BY `start` DESC");
	$query->bindParam(":startdate", $startdate, PDO::PARAM_STR);
	$query->bindParam(":enddate", $enddate, PDO::PARAM_STR);
	$query->execute();

	$count = $query->rowCount();
	if ($count > 0) {
		echo '<tr><td style="text-align: left; padding-top: 20px;">'.$year.'</td><td colspan="8"></td></tr>';

		foreach ($query as $row) {
			$id = $row['id'];
			$fieldname = $row['name'];
			$start = $row['start'];
			$startyear = substr($start, 0, 4);
			$end = $row['end'];
			$size = $row['size'];
			$size_disp = number_format($size, 1, ",", ".");
			$field_seed = $row['seed'];
			$complete = $row['complete'];
			$sold = $row['sold'];

			$query2 = $db->prepare("SELECT * FROM `order` WHERE `field` = :field AND (`status` = 4 OR `status` = 3) ORDER BY `date` ASC LIMIT 1");
			$query2->bindParam(":field", $id, PDO::PARAM_STR);
			$query2->execute(); 
			$result2 = $query2->fetch(PDO::FETCH_OBJ);
			$cutting_start = $result2->date;


		    $difference = $size - $sold;
		    $difference_disp = number_format($difference, 2, ",", ".");
		    $percentage = ($difference / $size) * 100;
		    $percentage_disp = number_format($percentage, 1, ",", ".");

		    $query2 = $db->prepare("SELECT * FROM seed WHERE `id` = :id");
		    $query2->bindParam(":id", $field_seed, PDO::PARAM_STR);
		    $query2->execute();
		    $result2 = $query2->fetch(PDO::FETCH_OBJ);
		    $seedname = $result2->name;

		    echo "<tr>";
		    echo "<td style='text-align:left'><b><a href='https://turfgrass.site/fields.php?field=".$id."'>".$fieldname."</a></b></td>";
		    echo '<td>'.$size_disp." ha</td>";
			echo "<td><b>".number_format($sold, 2, ",", ".")." ha</b></td>";
			echo '<td>'.$difference_disp." ha</td>";
			echo '<td class="border">'.$percentage_disp." %</td>";	
			echo "<td>".$start."</td>";
			echo "<td>".$cutting_start."</td>";
			echo "<td class='border'>".$end."</td>";
			echo "<td>".$seedname."</td>";
			echo "</tr>";
		}
	}
}
echo '</tbody></table>'; 
echo "</div></div>";

?>

</div>