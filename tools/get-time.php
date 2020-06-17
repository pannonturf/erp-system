<?php
include('functions.php');

require_once('../config/config.php');
$db = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.'', DB_USER, DB_PASS);

$query = $db->prepare("SELECT * FROM `system` WHERE `id` = 2");
$query->execute(); 
$result = $query->fetch(PDO::FETCH_OBJ);
$modus = $result->active;

$datum = $_POST['datum'];
$checkdatum = $datum;
$available_total = $_POST['available'];

echo " <i> &nbsp;".$datum." </i>";

echo '<table class="table table-bordered table-condensed" id="timeTable"><tr>';

//get standard amounts from database for 7:00 - 9:00     
$query = $db->prepare("SELECT * FROM `amounts` WHERE `type` = 0 AND `timespan` = 2 ORDER BY `edited` DESC LIMIT 1");
$query->execute(); 
$result = $query->fetch(PDO::FETCH_OBJ);
$standard_1 = $result->amount;

$n = 1;
for ($i=7; $i < 12; $i++) { 

	if ($i < 10) {
		$ordertime = "0".$i.":00:00";
		$ordertime2 = "0".$i.":30:00";
	}
	else {
		$ordertime = $i.":00:00";
		$ordertime2 = $i.":30:00";
	}

	//get total amount of orders for each time of a given day     
	$total = 0;
	$query = $db->prepare("SELECT * FROM `order` WHERE `date` = :datum AND `time` = :ordertime AND `status` < 5 ORDER BY `time` ASC");
	$query->bindParam(":datum", $datum, PDO::PARAM_STR);
	$query->bindParam(":ordertime", $ordertime, PDO::PARAM_STR);
	$query->execute(); 
	foreach ($query as $row) {
		$type1 = $row['type1'];
		$type3 = $row['type3'];
		$amount = getAmount(amount_decrypt($row['amount'], $key2), $type3, $modus);
		$project_id = $row['project_id'];
		$name = $row['name'];

		if ($type1 < 4 AND $project_id == 0) {
	    	$total += $amount;
	    }
	}

	// don't show 7:00 on Monday
	$day = date('w', strtotime($datum));
	if (!($day == 1 AND $i == 7)) {
		echo '<td class="normal" id="timebutton'.$n.'">';
	}
	else {
		echo '<td class="normal" style="display: none;" id="timebutton'.$n.'">';
	}
		echo '<label class="radio-inline"><input type="radio" name="time" onclick="timeFunction('.$n.')" value="'.$ordertime.'"> '.$i.':00<br>';
		echo "<i>".$total." m&sup2;</i></label></td>";
	

	$n++;

	if ($i != 9) {
		//get total amount of orders for each time of a given day (half hours)     
		$total = 0;
		$query = $db->prepare("SELECT * FROM `order` WHERE `date` = :datum AND `time` = :ordertime AND `status` < 5 ORDER BY `time` ASC");
		$query->bindParam(":datum", $datum, PDO::PARAM_STR);
		$query->bindParam(":ordertime", $ordertime2, PDO::PARAM_STR);
		$query->execute(); 
		foreach ($query as $row) {
			$type1 = $row['type1'];
			$type3 = $row['type3'];
			$amount = getAmount(amount_decrypt($row['amount'], $key2), $type3, $modus);
			$project_id = $row['project_id'];
			$name = $row['name'];

			if ($type1 < 4 AND $project_id == 0) {
		    	$total += $amount;
		    }
		}

		// don't show 7:00 on Monday
		$day = date('w', strtotime($datum));
		if (!($day == 1 AND $i == 7)) {
			echo '<td class="normal" id="timebutton'.$n.'">';
		}
		else {
			echo '<td class="normal" style="display: none;" id="timebutton'.$n.'">';
		}
		echo '<label class="radio-inline"><input type="radio" name="time" onclick="timeFunction('.$n.')" value="'.$ordertime2.'"> '.$i.':30<br>';
		echo "<i>".$total." m&sup2;</i></label></td>";
		$n++;
	}
}	


for ($i=12; $i < 18; $i++) { 
	$ordertime = $i.":00:00";

	//get total amount of orders for each time of a given day     
	$total = 0;
	$query = $db->prepare("SELECT * FROM `order` WHERE `date` = :datum AND `time` = :ordertime AND `status` < 5 ORDER BY `time` ASC");
	$query->bindParam(":datum", $datum, PDO::PARAM_STR);
	$query->bindParam(":ordertime", $ordertime, PDO::PARAM_STR);
	$query->execute(); 
	foreach ($query as $row) {
		$type1 = $row['type1'];
		$type3 = $row['type3'];
		$amount = getAmount(amount_decrypt($row['amount'], $key2), $type3, $modus);
		$project_id = $row['project_id'];
		$name = $row['name'];

		if ($type1 < 4 AND $project_id == 0) {
	    	$total += $amount;
	    }
	}
	
	echo '<td class="normal" id="timebutton'.$n.'"><label class="radio-inline"><input type="radio" name="time" onclick="timeFunction('.$n.')" value="'.$ordertime.'"> '.$i.':00<br>';
	echo "<i>".$total." m&sup2;</i></label></td>";
	$n++;
}

echo '</tr>';

// show total amounts of different time spans
$total1 = 0;
$total2 = 0;
$total3 = 0;
$time1 = "09:00:01";
$time2 = "12:00:01";

$query = $db->prepare("SELECT * FROM `order` WHERE `date` = :datum AND `status` < 5 ORDER BY `time` ASC");
$query->bindParam(":datum", $datum, PDO::PARAM_STR);
$query->execute(); 
foreach ($query as $row) {
	$type1 = $row['type1'];
	$type3 = $row['type3'];
	$amount = getAmount(amount_decrypt($row['amount'], $key2), $type3, $modus);
	$project_id = $row['project_id'];
	$time = $row['time'];
	$name = $row['name'];

	if ($type1 < 4 AND $project_id == 0) {
    	if ($time < $time1) {
			$total1 += $amount;
		}
		elseif ($time < $time2) {
			$total2 += $amount;
		}
		else {
			$total3 += $amount;
		}	
    }	
}

// check capacity - see if it differs from standard
$query = $db->prepare("SELECT * FROM `amounts` WHERE `datum` = :datum AND `type` = 1 AND `timespan` = 2 ORDER BY `edited` DESC LIMIT 1");
$query->bindParam(":datum", $datum, PDO::PARAM_STR);
$query->execute(); 
$list_length = $query->rowCount();
$result = $query->fetch(PDO::FETCH_OBJ);

if ($list_length > 0) {
	$standard_1_single = $result->amount;
}
else {
	$standard_1_single = $standard_1;
}

$available_1 = $standard_1_single - $total1;
$available_1_disp = number_format($available_1, 0, ',', ' ');


if ($day != 1) {
	echo '<tr><td class="normal2" colspan="5"><b><i>'.$total1."  m&sup2;</i></b>";


}
else {
	echo '<tr><td class="normal2" colspan="3"><b><i>'.$total1."  m&sup2;</i></b>";
}

echo "&nbsp; &nbsp; &nbsp; &nbsp;";

if ($available_1 > 500) {
	echo '<span class="badge" style="background-color: #5cb85c;font-size: 14px;">'.$available_1_disp.'</span>';
}
elseif ($available_1 > 10) {
	echo '<span class="badge" style="background-color: #ff9c2e;font-size: 14px;"">'.$available_1_disp.'</span>';
}
else {
	echo '<span class="badge" style="background-color: red;font-size: 14px;">Megtelt</span>';
}
	
echo "</td>";

echo '<td class="normal2" colspan="5"><b><i>'.$total2."  m&sup2;</i></b></td>";
echo '<td class="normal2" colspan="5"><b><i>'.$total3."  m&sup2;</i></b></td></tr>";

echo '</table>';

echo "<input type='hidden' value='".$available_total."' id='availability_total'>";
echo "<input type='hidden' value='".$available_1."' id='availability_1'>";

 
?>