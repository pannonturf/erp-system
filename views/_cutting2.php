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

if (isset($_SESSION['userid'])) {
	$user = $_SESSION['userid'];
}
else {
	$user = $_COOKIE["userid"];
}

// display right list for right user
if ($user == 16) {
	$team = 1;
}
elseif ($user == 17) {
	$team = 2;
}
elseif ($user == 37) {
	$team = 3;
}
else {
	$team = 0;
}


// Get the field on which the team is working   
if (isset($_GET['field'])) {
	$current_field = $_GET['field'];
	$current_type = $_GET['type'];
}
else {
	$current_field = 0;
	$current_type = 0;
}


// calculations
$plan = array();
$cut = array();
$left = array();

// set up array for fields
$query = $db->prepare("SELECT * FROM fields WHERE `cutting` = 1 AND `complete` < 1");
$query->execute();
foreach ($query as $row) {
	$field_id = $row['id'];

	// check, if order is assigned to field today
	$query2 = $db->prepare("SELECT * FROM `order` WHERE `date` = :datum AND `field` = :field AND (`status` = 1 OR `status` = 4) AND `type1` = 1 AND `type2` = 1");
	$query2->bindParam(":datum", $today, PDO::PARAM_STR);
	$query2->bindParam(":field", $field_id, PDO::PARAM_STR);
	$query2->execute();
	$count = $query2->rowCount();

	if ($count > 0) {
		for ($k=1; $k < 4; $k++) { 
			foreach ($query2 as $row2) {
				$amount = getAmount(amount_decrypt($row2['amount'], $key2), $type3, $modus);
				$pallet = $row2['pallet'];

				$plan_total[$field_id][$pallet] += $amount; 	// amount to be cut with [1] 50 pallets, [2] 30 pallets, [3] 56 pallets on the whole day
			}
					
			$plan[$field_id][$k] = 0; 				// amount to be cut with [1] 50 pallets, [2] 30 pallets, [3] 56 pallets at a given time
			$cut[$field_id][$k] = 0; 				// amount finished
			$left[$field_id][$k] = 0; 				// pallets left for next time slot
			$unfinished[$field_id][$k] = 0; 		// check if field is unfinished
			$cumulated[$field_id][$k] = 0;			// cumulated amounts to be cut
			$planned_pallets[$field_id][$k] = 0; 	// planned pallets in total
		}
	}
}

// Add check for unassigned fields
$unassigned = 0;
$team_count = 0;	

// get amounts already cut
$query = $db->prepare("SELECT * FROM `pallets` WHERE `datum` = :datum ORDER BY `id` ASC");
$query->bindParam(":datum", $today, PDO::PARAM_STR);
$query->execute(); 

foreach ($query as $row) {
	$field_id = $row['field'];
	$type = $row['type'];
	$amount = $row['amount'];
	$pallet_team = $row['team'];

	if ($pallet_team == $team) {
		$team_count ++;
	}

	$cut[$field_id][$type] += $amount;
	$left[$field_id][$type] += $amount;		// fill up
}

/////////////////////
// check if today is finished and show pallets from tomorrow until 9:00
$today_unfinished = 0;
foreach($plan_total as $field_id => $amounts_total) {
	for ($j=1; $j < 4; $j++) {
		$today_left = $amounts_total[$j] - $cut[$field_id][$j];

		if ($today_left > 0) {
			$today_unfinished = 1;
		}
	}	
}

if ($today_unfinished == 0) {
	// set up new array
	$query = $db->prepare("SELECT * FROM fields WHERE `cutting` = 1 AND `complete` < 1");
	$query->execute();
	foreach ($query as $row) {
		$field_id = $row['id'];

		// check, if order is assigned to field today
		$query2 = $db->prepare("SELECT * FROM `order` WHERE `date` = :datum AND `field` = :field AND (`status` = 1 OR `status` = 4) AND `type1` = 1 AND `type2` = 1");
		$query2->bindParam(":datum", $tomorrow, PDO::PARAM_STR);
		$query2->bindParam(":field", $field_id, PDO::PARAM_STR);
		$query2->execute();
		$count = $query2->rowCount();

		if ($count > 0) {
			for ($k=1; $k < 4; $k++) { 
				foreach ($query2 as $row2) {
					$amount = getAmount(amount_decrypt($row2['amount'], $key2), $type3, $modus);
					$pallet = $row2['pallet'];

					$plan_total[$field_id][$pallet] += $amount; 	// amount to be cut with [1] 50 pallets, [2] 30 pallets, [3] 56 pallets on the whole day
				}	

				$plan[$field_id][$k] = 0; 				// amount to be cut with [1] 50 pallets, [2] 30 pallets, [3] 56 pallets at a given time
				$cut[$field_id][$k] = 0; 				// amount finished
				$left[$field_id][$k] = 0; 				// pallets left for next time slot
				$unfinished[$field_id][$k] = 0; 		// check if field is unfinished
				$cumulated[$field_id][$k] = 0;			// cumulated amounts to be cut
				$planned_pallets[$field_id][$k] = 0; 	// planned pallets in total					
			}
		}
	}

	// get amounts already cut
	$query = $db->prepare("SELECT * FROM `pallets` WHERE `datum` = :datum ORDER BY `id` ASC");
	$query->bindParam(":datum", $tomorrow, PDO::PARAM_STR);
	$query->execute(); 

	foreach ($query as $row) {
		$field_id = $row['field'];
		$type = $row['type'];
		$amount = $row['amount'];

		$cut[$field_id][$type] += $amount;
		$left[$field_id][$type] += $amount;		// fill up
	}
}


/////////////7
// control for 10 pallets
$points = substr($team_count, -1);

if ($team_count == 0) {
	$points = 0;
}
elseif ($points == "0") {
	$points = 10;
}

$next_point = 1;

echo '<div class="row">';
	echo '<div class="col-xs-12" style="padding-top: 15px; padding-bottom: 10px;">';

		for ($j=1; $j < 11; $j++) {
			if ($j <= $points) {
				echo '<span id="pallet_'.($j+1).'"><img src="../img/point.png" style="height: 20px;"></span>';
				$next_point ++;
			}
			else {
				echo '<span id="pallet_'.($j+1).'"><img src="../img/point_grey.png" style="height: 20px;"></span>';
			}

			if ($j == 5) {
				echo " &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ";
			}
			elseif ($j < 10) {
				echo " &nbsp;&nbsp; ";
			}

			
		} 

	echo "</div>";
echo "</div>";


//////////////////////////////////////////
if ($today_unfinished == 0) {
	echo "<h3 style='color: red;'>HOLNAP</h3>";
}

echo '<table class="table">';

// get number of pallets that need to be cut on each field at each time
if ($today_unfinished == 1) {
	$time_end = 18;
}
else {
	$time_end = 10;
}

for ($i=7; $i < $time_end; $i++) { 
	// set plan array back to 0
	foreach($plan as $field_id => $amounts) {
		$plan[$field_id][1] = 0; 	
		$plan[$field_id][2] = 0;	
		$plan[$field_id][3] = 0; 	
	}

	if ($i < 10) {
		$start = "0".$i.":00:00";
		$end = "0".$i.":30:00";
	}
	else {
		$start = $i.":00:00";
		$end = $i.":30:00";
	}
	$time_display = $i.":00";

	if ($today_unfinished == 1) {
		$datum = $today;
	}
	else {
		$datum = $tomorrow;
	}

	$query = $db->prepare("SELECT * FROM `order` WHERE `date` = :datum AND `type1` = 1 AND `type2` = 1 AND (`status` = 1 OR `status` = 4) AND `time` >= :start AND `time` <= :end ORDER BY `time` ASC, `id` ASC");
	

	$query->bindParam(":datum", $datum, PDO::PARAM_STR);
	$query->bindParam(":start", $start, PDO::PARAM_STR);
	$query->bindParam(":end", $end, PDO::PARAM_STR);
	$query->execute(); 

	foreach ($query as $row) {
		$time = $row['time'];
		$field_id = $row['field'];
		$pallet = $row['pallet'];
		$type3 = $row['type3'];
		$amount = getAmount(amount_decrypt($row['amount'], $key2), $type3, $modus);

		$plan[$field_id][$pallet] += $amount;	// amount to be cut at a given time
		$cumulated2[$field_id][$pallet] += $amount;	// amount to be cut at a given time
	}


	foreach($plan as $field_id => $amounts) {

		// check if there is enough inventory left to satisfy the amount for this time slot
		for ($j=1; $j < 4; $j++) { 
			// calculations
			if ($j == 1) {
				$type_amount = 50;
			}
			elseif ($j == 2) {
				$type_amount = 30;
			}
			elseif ($j == 3) {
				$type_amount = 65;
			}

			$left[$field_id][$j] -= $amounts[$j];
			//$pallets_needed = ceil($amounts[$j] / $type_amount);

			if ($left[$field_id][$j] < 0 AND $amounts[$j] > 0) {
				$cumulated[$field_id][$j] += $amounts[$j];		// cumulating amounts to be cut

				$amount_missing = $left[$field_id][$j] * (-1);				// amount still to be cut
				$amount_missing_total = $plan_total[$field_id][$j] - $cut[$field_id][$j];	// amount that is still left on the field for today
				$amount_planned = $planned_pallets[$field_id][$j] * $type_amount;		// amount already planned in previous time slots (not finished yet); with standard amounts
				$pallets_missing = ceil(($amount_missing - $amount_planned) / $type_amount);		// number of pallets still to be cut at a given time
				$total_pallets = ceil(($cumulated[$field_id][$j] - $amount_planned) / $type_amount); 	// total pallets to be cut at a given time

				$finished_pallets = $total_pallets - $pallets_missing;

				if ($amount_missing_total < $type_amount) {		// check if it ios the last pallet on the field today (exact number needed)
					$pallet_amount = $amount_missing_total;
					$btn_type = "btn-warning";
				}
				else {
					$pallet_amount = $type_amount;
					$btn_type = "btn-success";
				}


				// get field name
				$query = $db->prepare("SELECT * FROM fields WHERE `id` = :id");
				$query->bindParam(":id", $field_id, PDO::PARAM_STR);
				$query->execute();
				$result = $query->fetch(PDO::FETCH_OBJ);
				$field_display = $result->name;
				$field_type = $result->type;

				////////////////
				// show name, amounts and button for current field
				if ($current_field == $field_id AND $current_type == $j AND $unfinished[$field_id][$j] == 0) {

					echo '<div class="row">';
						echo '<div class="col-xs-3"></div>';
						echo '<div class="col-xs-6">';
							echo "<div style='font-size: 40px; text-align:center; margin-top: 20px;'><i>".$time_display."</i></div>";
						echo '</div>';
						echo '<div class="col-xs-3">';
						?>
							<button class="btn btn-default btn-lg" style="border:none; border-radius:10px; margin-right:40px; margin-top:25px;" onclick="document.location = 'index.php'"><span class="glyphicon glyphicon-remove"></span></button>
						<?php
						echo '</div>';
					echo '</div>';

					echo '<div class="row">';
						echo '<div class="col-xs-12">';
							echo "<div class='next_id' style='font-size: 30px; margin-top:0px;'>".$field_display."</div>";
						echo '</div>';
					echo '</div>';

					if ($field_type == 3) {
						echo '<div class="row">';
							echo '<div class="col-xs-12">';
								echo "<div class='next_details' style='font-size: 30px; margin-top:0px;'><mark style='background: #f62323; color: white;'><b>MED</b></mark></div>";
							echo '</div>';
						echo '</div>';
					}

					echo '<div class="row" style="border-bottom: solid 1px black;">';
						echo '<div class="col-xs-1"></div>';

						echo '<div class="col-xs-4">';
							echo '<div class="complete_button2"><button class="btn '.$btn_type.' btn-lg" style="font-size: 50px;" onclick="palletFunction('.$current_field.', '.$team.', '.$current_type.', '.$pallet_amount.', '.($pallets_missing - 1).', '.$today_unfinished.', '.$next_point.')">';
							echo $pallet_amount;
							echo '</button></div>';
						echo '</div>';
						echo '<div class="col-xs-7" style="font-size: 50px; margin-top: 10px;">';
							/*
							echo "<span id='pallets_finish'>".$finished_pallets."</span>";
							echo ' / ';
							echo $total_pallets;
							*/
							echo 'még ';
							echo "<span id='pallets_finish'>".$pallets_missing."</span>";
	
						echo '</div>';
					echo '</div>';
				}
				else {

					if ($field_id == 111111) {			// no field assigned yet
						$field_display = "?";
						echo '<tr>';
					}
					else {
						echo '<tr class="clickable" id="'.$field_id.'" id2="'.$j.'">';
					}

					// insert row in list
					echo '<td>'.$time_display.'</td>';

					echo '<td>';

					if ($field_type == 3) {
						echo "<mark style='background: #f62323; color: white;'>";
					}

					echo $field_display;

					if ($field_type == 3) {
						echo "</mark>";
					}


					echo '</td><td><b>';

					if ($type_amount == 50) {
						echo '['.$type_amount.']';
					}
					else {
						echo '<span style="color: red;">['.$type_amount.']</span>';
					}
					

					echo '</b></td>';

					echo '<td>';
					
					if ($unfinished[$field_id][$j] == 1) {
						echo "0";
					}
					else {
						echo $finished_pallets;
					}

					echo " / ".$total_pallets;
					echo '</td>';
					echo '</tr>';

				}	// else

				// check to assign 0 pallets cut for next time slots
				$unfinished[$field_id][$j] = 1;

				// safe number of pallets already planned
				$planned_pallets[$field_id][$j] += $total_pallets;
			}	// if

		}	// for
	}	// foreach
}	// for

echo "</table>";
?>
