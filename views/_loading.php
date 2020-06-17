<?php 
//////////////////////////////////////////////
// Cutting view for outside teams (modus 2) //
//////////////////////////////////////////////

include('views/_header'.$header.'.php');
include('tools/functions.php');

require_once('config/config.php');
$db = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.'', DB_USER, DB_PASS);
$db -> exec("set names utf8");
date_default_timezone_set('Europe/Budapest');

$day = date('w', strtotime($today));

$current_order = 0;

////////////////
// show number, field, amount and button for current order
if (isset($_GET['order'])) {
	$current_order = $_GET['order'];

	// get order data
	$query = $db->prepare("SELECT * FROM `order` WHERE id = :id");
	$query->bindParam(":id", $current_order, PDO::PARAM_STR);
	$query->execute();
	$result = $query->fetch(PDO::FETCH_OBJ);

	$next_id = $result->id;
	$next_id3 = $result->id3;
	if (substr($next_id3, -2) == "00") {
		$next_id3_display = 100;
	}
	else {
		$next_id3_display = substr($next_id3, -2);
	}

	$next_pickup = $result->pickup;
	$next_field = $result->field;
	$next_pallet = $result->pallet;
	$next_licence = $result->licence;
	$next_type3 = $result->type3;
	//$next_time = substr($result->time, 0, 5);
	$next_amount = getAmount(amount_decrypt($result->amount, $key2), $next_type3, $modus);
	$next_note = $result->note;
	$next_forwarder = $result->forwarder;

	if ($next_pickup == 0 AND $next_id3 > 0) {
		
		echo '<div class="row">';
			echo '<div class="col-xs-12">';
				echo "<div class='next_id' style='font-size:40px;'>".$next_id3_display."</div>";
			echo '</div>';
		echo "</div>";

		$query = $db->prepare("SELECT * FROM fields WHERE `id` = :id");
		$query->bindParam(":id", $next_field, PDO::PARAM_STR);
		$query->execute();
		$result = $query->fetch(PDO::FETCH_OBJ);
		$field_type = $result->type;

		if ($field == 111111) {
			$field_display = "?";
		}
		else {
			$field_display = $result->name;
		}

		echo '<div class="row">';
			echo '<div class="col-xs-12">';
				echo "<div style='font-size: 25px; margin-top:0px;text-align: center; font-weight: bold;'>".$field_display."</div>";
			echo '</div>';
		echo '</div>';

		if ($field_type == 3) {
			echo '<div class="row">';
				echo '<div class="col-xs-12">';
					echo "<div class='next_details' style='font-size: 30px; margin-top:0px;'><mark style='background: #f62323; color: white;'><b>MED</b></mark></div>";
				echo '</div>';
			echo '</div>';
		}

		echo '<div class="row" style="margin-bottom:0px;">';
			echo '<div class="col-xs-1"></div>';

			if ($next_pallet == 1) {
				$type_amount = 50;
			}
			elseif ($next_pallet == 2) {
				$type_amount = 30;
			}
			elseif ($next_pallet == 3) {
				$type_amount = 65;
			}

			echo '<div class="col-xs-4" style="font-size: 50px;">';
				if ($type_amount == 50) {
					echo '['.$type_amount.']';
				}
				else {
					echo '<span style="color: red;">['.$type_amount.']</span>';
				}
			echo '</div>';
			echo '<div class="col-xs-7" style="font-size: 50px; margin-bottom:0px;"><b>';
				echo $next_amount." m&sup2;</b>";
			echo '</div>';
		echo '</div>';

		// calculations for number of pallets
		$number_pallets = floor($next_amount / $type_amount);
		$rest = $next_amount - $number_pallets * $type_amount;

		echo "<div class='next_details' style='margin-top:0px;'>";
			echo "<div style='font-size: 30px; margin-top:0px;'><i>".$number_pallets." raklap + ".$rest." m&sup2;</i><br>";
			echo $next_licence."</div>";
		echo '</div>';

		if ($next_note != "") {
				echo "<div class='next_details'><mark style='background: #ff0;'><i>".$next_note."</i>";

				if ($next_forwarder == 1) {
					echo ' | <b>Androvic</b>';
				}
				echo "</mark></div>";
			}
			elseif ($next_forwarder == 1) {
				echo "<div class='next_details'><mark style='background: #ff0;'><b>Androvic</b></mark></div>";
			}
		echo '<div class="row" style="border-bottom: solid 1px black;">';
			echo '<div class="col-xs-12">';
				echo '<div class="complete_button"><button class="btn btn-success btn-lg" style="font-size: 30px;" onclick="pickupFunction('.$next_id.')">Befejezés</button>';
				
				if ($change == 0) {
					?>
					<button class="btn btn-default btn-lg" style="border:none; border-radius:10px; margin-left:20px;" onclick="document.location = 'index.php'"><span class="glyphicon glyphicon-remove"></span></button>
					<?php
				}
				echo '</div>';
			echo '</div>';
		echo '</div>';
	}
}

// show list of orders
echo '<table class="table" style="border-bottom: solid 1px black;">';

// get list of open order to select from (with order number)
$query = $db->prepare("SELECT * FROM `order` WHERE `date` = :datum AND (`status` = 1 OR `status` = 4) AND `pickup` = 0 AND `id3` > 0 AND `type1` < 4 AND `type2` = 1 AND `project_id` = 0 ORDER BY `id3` ASC");
$query->bindParam(":datum", $today, PDO::PARAM_STR);
$query->execute();

foreach ($query as $row) {
	$order_id = $row['id'];
	$id3 = $row['id3'];
	$field = $row['field'];
	$amount = getAmount(amount_decrypt($row['amount'], $key2), $type3, $modus);
	$pallet = $row['pallet'];

	$id3_display = substr($id3, -2);

	if ($id3_display == "00") {
		$id3_display = 100;
	}

	if ($order_id != $current_order) {

		echo '<tr class="clickable2" id="'.$order_id.'">';

		// insert row in list
		echo '<td><b>'.$id3_display.'</i></td>';

		// Field
		$query2 = $db->prepare("SELECT * FROM fields WHERE `id` = :id");
		$query2->bindParam(":id", $field, PDO::PARAM_STR);
		$query2->execute();
		$result2 = $query2->fetch(PDO::FETCH_OBJ);
		$field_type = $result2->type;

		if ($field == 111111) {			// no field assigned yet
			$field_display = "?";
		}
		else {
			$field_display = $result2->name;
		}

		echo '<td>';

		if ($field_type == 3) {
			echo "<mark style='background: #f62323; color: white;'>";
		}

		echo $field_display;

		if ($field_type == 3) {
			echo "</mark>";
		}

		echo '</td>';

		if ($pallet == 1) {
			$type_amount = "50";
		}
		elseif ($pallet == 2) {
			$type_amount = "30";
		}
		elseif ($pallet == 3) {
			$type_amount = "65";
		}

		echo '<td style="width: 30px;"><b>';

		if ($type_amount == 50) {
			echo '['.$type_amount.']';
		}
		else {
			echo '<span style="color: red;">['.$type_amount.']</span>';
		}
		

		echo '</b></td>';

		echo '<td style="width: 90px;">'.$amount." m&sup2;</td>";

		echo '</tr>';
	}
}
echo "</table>";

echo "<br><br><br>";


// Inventory

// get amounts already cut
$query = $db->prepare("SELECT * FROM `pallets` WHERE `datum` = :datum ORDER BY `id` ASC");
$query->bindParam(":datum", $today, PDO::PARAM_STR);
$query->execute(); 

foreach ($query as $row) {
	$field_id = $row['field'];
	$type = $row['type'];
	$amount1 = $row['amount'];

	$amount_cut[$field_id][$type] += $amount1;
}

// get orders already picked up
$query2 = $db->prepare("SELECT * FROM `order` WHERE `date` = :datum AND `pickup` = 1");
$query2->bindParam(":datum", $today, PDO::PARAM_STR);
$query2->execute();

// get amount already picked up
foreach ($query2 as $row) {
	$amount2 = getAmount(amount_decrypt($row['amount'], $key2), $type3, $modus);
	$type1 = $row['type1'];
	$pallet = $row['pallet'];
	$field_id = $row['field'];

	$pickedup[$field_id][$pallet] += $amount2;
}

//print_r($amount_cut);
//print_r($pickedup);

echo '<div class="row">';
	echo '<div class="col-xs-12" style="font-size: 20px;">';
		echo '<h3>Raktár</h3>';

		foreach($amount_cut as $field_id => $amounts_total) {
			for ($j=1; $j < 4; $j++) {
				$inventory = $amounts_total[$j] - $pickedup[$field_id][$j];

				if ($inventory > 0) {
					
					$query = $db->prepare("SELECT * FROM fields WHERE `id` = :id");
					$query->bindParam(":id", $field_id, PDO::PARAM_STR);
					$query->execute();
					$result = $query->fetch(PDO::FETCH_OBJ);

					echo $result->name;

					if ($j == 1) {
						$type_amount = 50;
					}
					elseif ($j == 2) {
						$type_amount = 30;
					}
					elseif ($j == 3) {
						$type_amount = 65;
					}

					echo " &nbsp; [".$type_amount."] : &nbsp; ";

					echo number_format($inventory, 0, ',', ' ')." m&sup2;<br>";
				}


			}
		}

	echo '</div>';
echo '</div>';
echo "<br>";


// Inventory for tomorrow

// get amounts already cut
$query = $db->prepare("SELECT * FROM `pallets` WHERE `datum` = :datum ORDER BY `id` ASC");
$query->bindParam(":datum", $tomorrow, PDO::PARAM_STR);
$query->execute(); 

foreach ($query as $row) {
	$field_id = $row['field'];
	$type = $row['type'];
	$amount1 = $row['amount'];

	$amount_cut2[$field_id][$type] += $amount1;
}


echo '<div class="row">';
	echo '<div class="col-xs-12" style="font-size: 20px;">';
		echo '<h4>HOLNAP</h4>';

		foreach($amount_cut2 as $field_id => $amounts_total) {
			for ($j=1; $j < 4; $j++) {
				$inventory = $amounts_total[$j];

				if ($inventory > 0) {
					
					$query = $db->prepare("SELECT * FROM fields WHERE `id` = :id");
					$query->bindParam(":id", $field_id, PDO::PARAM_STR);
					$query->execute();
					$result = $query->fetch(PDO::FETCH_OBJ);

					echo "<i>".$result->name;

					if ($j == 1) {
						$type_amount = 50;
					}
					elseif ($j == 2) {
						$type_amount = 30;
					}
					elseif ($j == 3) {
						$type_amount = 65;
					}

					echo " &nbsp; [".$type_amount."] : &nbsp; ";

					echo number_format($inventory, 0, ',', ' ')." m&sup2;</i><br>";
				}


			}
		}

	echo '</div>';
echo '</div>';



/*
// get list of open order to select from (no order number yet)
echo "<h3><i>Tovabbi:</i></h3>";
echo '<table class="table">';

$query = $db->prepare("SELECT * FROM `order` WHERE `date` = :datum AND (`status` = 1 OR `status` = 4) AND `pickup` = 0 AND `id3` = 0 AND `type1` < 4 AND `project_id` = 0 ORDER BY `time` ASC");
$query->bindParam(":datum", $today, PDO::PARAM_STR);
$query->execute();

foreach ($query as $row) {
	$order_id = $row['id'];
	$time_display = substr($row['time'], 0, 5);
	$field = $row['field'];
	$amount = getAmount(amount_decrypt($row['amount'], $key2), $type3, $modus);
	$pallet = $row['pallet'];


	if ($order_id != $current_order) {

		echo '<tr class="nonclickable">';

		// insert row in list
		echo '<td><i>'.$time_display.'</i></td>';

		// Field
		$query2 = $db->prepare("SELECT * FROM fields WHERE `id` = :id");
		$query2->bindParam(":id", $field, PDO::PARAM_STR);
		$query2->execute();
		$result2 = $query2->fetch(PDO::FETCH_OBJ);

		if ($field == 111111) {			// no field assigned yet
			$field_display = "?";
		}
		else {
			$field_display = $result2->name;
		}

		echo '<td>'.$field_display."</td>";

		if ($pallet == 1) {
			$type_amount = "50";
		}
		elseif ($pallet == 2) {
			$type_amount = "30";
		}
		elseif ($pallet == 3) {
			$type_amount = "65";
		}

		echo '<td style="width: 30px;"><b>';

		if ($type_amount == 50) {
			echo '['.$type_amount.']';
		}
		else {
			echo '<span style="color: red;">['.$type_amount.']</span>';
		}
		

		echo '</b></td>';

		echo '<td style="width: 90px;">'.$amount." m&sup2;</td>";

		echo '</tr>';
	}
}

echo "</table>";
*/


?>
