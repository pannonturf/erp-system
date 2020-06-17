<?php 
//////////////////////////////////////
// Cutting list of next working day //
//////////////////////////////////////

if ($cutting_modus == 1) {
	$wide = 1;		// other CSS for better overview
}

include('views/_header'.$header.'.php');
include('tools/functions.php');

require_once('config/config.php');
$db = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.'', DB_USER, DB_PASS);
$db -> exec("set names utf8");
date_default_timezone_set('Europe/Budapest');

$nextday = date('Y-m-d', strtotime('tomorrow'));
$day_tomorrow = date('w', strtotime($nextday));

if ($day_tomorrow == 6) {		// Saturday
	$nextday = date('Y-m-d', strtotime($nextday.' +2 days'));	// Monday
	$day = date('w', strtotime($nextday));
	$dayHeading = "Hétfői";
	$dayHeading2 = $days[$day];
}
elseif ($day_tomorrow == 0) {	// Sunday
	$nextday = date('Y-m-d', strtotime($nextday.' +1 days'));	// Monday
	$day = date('w', strtotime($nextday));
	$dayHeading = "Hétfői";
	$dayHeading2 = $days[$day];
}
else {							// Any other day
	$dayHeading = "Holnapi";
	$dayHeading2 = $days[$day];
}

$datum = $nextday;
$check = 2;

$nextday_midnight = $nextday." 00:00:00";
$after_midnight = date('Y-m-d', strtotime($nextday.' +1 day'))." 00:00:00";


/////////////
// if field is changed
if (isset($_POST['changeField'])) {
	$idArray = $_POST['id'];
	$newField = $_POST['newField'];

	foreach($idArray AS $id => $control) {

		if (isset($_POST['editField'.$id.''])) {
			//Update order
			$sql = "UPDATE `order` SET `field` = :field WHERE `id` = :id";
			$query = $db->prepare($sql);

			$query->bindParam(":field", $newField, PDO::PARAM_STR);
			$query->bindParam(":id", $id, PDO::PARAM_STR);

			$query->execute();
		}
	}

	echo '<div class="alert alert-success center-block" role="alert">Terület modosítás sikerült!</div>';
}

///////////////////////
///////////////////////

//get total amounts of the day     
$total = 0;
$total_open = 0;
$total_finish = 0;
$total_finish1 = 0;
$total_finish2 = 0;
$total_finish3 = 0;
$query = $db->prepare("SELECT * FROM `order` WHERE `date` = :datum AND `type1` < 4 AND `project_id` = 0 AND `status` < 5 ORDER BY `time` ASC");
$query->bindParam(":datum", $datum, PDO::PARAM_STR);
$query->execute(); 
foreach ($query as $row) {
	$amount = amount_decrypt($row['amount'], $key2);
	$type2 = $row['type2'];
	$field_id = $row['field'];
	$pallet = $row['pallet'];
	$pickup = $row['pickup'];
	$status = $row['status'];
	$total += $amount;

	$plan_total[$field_id][$pallet] += $amount; 	// amount to be cut with [1] 50 pallets, [2] 30 pallets, [3] 56 pallets on the whole day

	if ($pickup == 1) {
		$pickup_array[$field_id][$pallet] += $amount; 
	}

	if ($type2 == 2 AND $status > 2) {
		$med_finished += $amount;
	}
}

// get amounts already cut
$query = $db->prepare("SELECT * FROM `pallets` WHERE `datum` = :datum ORDER BY `id` ASC");
$query->bindParam(":datum", $datum, PDO::PARAM_STR);
$query->execute(); 

foreach ($query as $row) {
	$field_id = $row['field'];
	$type = $row['type'];
	$amount = $row['amount'];
	$team = $row['team'];

	$amount_cut[$field_id][$type] += $amount;

	$total_cut += $amount;
	$total_pallets_cut ++;

	if ($team == 1) {
		$total_cut_1 += $amount;
		$total_pallets_cut_1 ++;
	}
	elseif ($team == 2) {
		$total_cut_2 += $amount;
		$total_pallets_cut_2 ++;
	}
	elseif ($team == 3) {
		$total_cut_3 += $amount;
		$total_pallets_cut_3 ++;
	}
	else {
		$total_cut_4 += $amount;
		$total_pallets_cut_4 ++;
	}

	$pallets_cut[$field_id][$type] ++;
}

$total_ordered = $total;
$sum = $total_cut + $med_finished;
$total_missing = $total_ordered - $sum;

?>

<div class="inputform">

  	<div class="row">
	    <div class="col-md-5">
	      <h3 style="margin-top:10px;"><?echo $dayHeading;?> kistekercs vágás</h3>  
	    </div>
	</div>


	<div class="row">
	    <div class="col-md-1"></div>

	    <div class="col-md-7">
    		<div class="panel panel-default">
			<div class="panel-heading"><h4><span class="glyphicon glyphicon-list-alt"></span>&nbsp;&nbsp;Aktuális készlet</h4> </div>

    		<table class="table">
    			<tr class='title' style="text-align: center;"><td></td><td>Megrendelt</td><td>Raktár</td><td>Igény</td><td>Hiányzó</td></tr>
	    		<?php
	    		$total_inventory = 0;
	    		$total_handout = 0;

	    		foreach ($plan_total as $field_id => $amounts) {
	    			for ($j=1; $j < 4; $j++) { 
	    				$ordered = $amounts[$j];

	    				if ($ordered > 0) {
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

			    			$query = $db->prepare("SELECT * FROM fields WHERE `id` = :id");
							$query->bindParam(":id", $field_id, PDO::PARAM_STR);
							$query->execute();
							$result = $query->fetch(PDO::FETCH_OBJ);
							
							echo "<tr style='font-size: 20px;'><td>".$result->name." <i>[".$type_amount."]</i>";

							if ($result->type == 3) {
								echo " &nbsp; <mark style='background: #f62323; color: white;'>MED</mark>";
							}
							echo "</td>";

							// total ordered amount
							echo "<td style='text-align: center;'>".number_format($ordered, 0, ',', ' ')." m&sup2;</td>";

							// amount on inventory
							$inventory = $amount_cut[$field_id][$j] - $pickup_array[$field_id][$j];
							echo "<td style='text-align: center;'><b>".number_format($inventory, 0, ',', ' ')." m&sup2;</b></td>";
							$total_inventory += $inventory;

							// amount still needed to be handed out
							$handout = $ordered - $pickup_array[$field_id][$j];
							if ($handout < $inventory) {
								$handout_style = " background-color: red; color: white;";
							}
							elseif ($handout == $inventory) {
								$handout_style = " background-color: #12dd72; color: white;";
							}
							else {
								$handout_style = "";
							}

							echo "<td style='text-align: center;".$handout_style."'>".number_format($handout, 0, ',', ' ')." m&sup2;</td>";
							$total_handout += $handout;

							// amount still needed to be cut
							$cut = $ordered - $amount_cut[$field_id][$j];
							if ($cut < 0) {
								$cut_display = $cut." m&sup2;";
								$cut_style = " color: red;";
							}
							elseif ($cut == 0) {
								$cut_display = "KÉSZ";
								$cut_style = " color: #12dd72;";
							}
							else {
								$cut_pallets = ceil($cut / $type_amount);
								$cut_display = $cut_pallets." raklap";
								$cut_style = "";
							}
							
							echo "<td style='text-align: center;".$cut_style."'><i>".$cut_display."</i></td>";

							echo "<tr>";
						}

					}
	    		}

	    		echo "<tr style='font-size: 20px; background-color: #f8f8f8; border-top: black 2px solid;'><td><b>TOTAL</b></td>";
				echo "<td style='text-align: center;'><b>".number_format($total_ordered, 0, ',', ' ')." m&sup2;</b></td>";
				echo "<td style='text-align: center;'><b>".number_format($total_inventory, 0, ',', ' ')." m&sup2;</b></td>";

				if ($total_handout < $total_inventory) {
					$handout_style = " background-color: red; color: white;";
				}
				elseif ($total_handout == $total_inventory) {
					$handout_style = " background-color: #12dd72; color: white;";
				}
				else {
					$handout_style = "";
				}
				echo "<td style='text-align: center;".$handout_style."'><b>".number_format($total_handout, 0, ',', ' ')." m&sup2;</b></td>";

				if ($total_missing < 0) {
					$cut_display = number_format($total_missing, 0, ',', ' ')." m&sup2;";
					$cut_style = " color: red;";
				}
				elseif ($total_missing == 0) {
					$cut_display = "KÉSZ";
					$cut_style = " color: #12dd72;";
				}
				else {
					$cut_display = number_format($total_missing, 0, ',', ' ')." m&sup2;";
					$cut_style = "";
				}
				echo "<td style='text-align: center;".$cut_style."'><b><i>".$cut_display."</i></b></td>";
				echo "</tr>";

    		?>
	    	</table>
	    	</div>
	    </div>


	    <div class="col-md-3">
		    <div class="panel panel-default">
				<div class="panel-body">
					<?php
				
					if ($total_pallets_cut > 0) {
						$total_pallets_cut_display = "<i>(".$total_pallets_cut." raklap)</i>";
					}
					else {
						$total_pallets_cut_display = "";
					}
					echo 'Kivágott: &nbsp; <b>'.number_format($sum, 0, ',', ' ').' m&sup2;</b> &nbsp; '.$total_pallets_cut_display;
					
					if ($total_cut_1 > 0) {
						echo '<br><br> &nbsp; &nbsp; &nbsp; Gép 1: &nbsp; '.number_format($total_cut_1, 0, ',', ' ').' m&sup2; &nbsp; <i>('.$total_pallets_cut_1.' raklap)</i>';
					}

					if ($total_cut_2 > 0) {
						echo '<br> &nbsp; &nbsp; &nbsp; Gép 2: &nbsp; '.number_format($total_cut_2, 0, ',', ' ').' m&sup2; &nbsp; <i>('.$total_pallets_cut_2.' raklap)</i>';
					}
					if ($total_cut_3 > 0) {
						echo '<br> &nbsp; &nbsp; &nbsp; Gép 3: &nbsp; '.number_format($total_cut_3, 0, ',', ' ').' m&sup2; &nbsp; <i>('.$total_pallets_cut_3.' raklap)</i>';
					}
					if ($total_cut_4 > 0) {
						echo '<br> &nbsp; &nbsp; &nbsp; Iroda: &nbsp;&nbsp; '.number_format($total_cut_4, 0, ',', ' ').' m&sup2; &nbsp; <i>('.$total_pallets_cut_4.' raklap)</i>';
					}
					if ($med_finished > 0) {
						echo '<br> &nbsp; &nbsp; &nbsp; MED: &nbsp; &nbsp; '.number_format($med_finished, 0, ',', ' ').' m&sup2;';
					}
					?>

				</div>
			</div>
		</div>
	</div>
	<br><br>


	<?php
	echo '<table class="table table-condensed centertext"><tr class="greyBG"><td></td>';
	$text = "<tr><td class='border'><b>Összes</b></td>";
	$text1 = "<tr><td class='border'>Poa</td>";
	$text2 = "<tr><td class='border'>MED</td>";

	for ($i=7; $i < 12; $i++) { 

		if ($i < 10) {
			$planneddate = $nextday." 0".$i.":00:00";
			$planneddate2 = $nextday." 0".$i.":30:00";
		}
		else {
			$planneddate = $nextday." ".$i.":00:00";
			$planneddate2 = $nextday." ".$i.":30:00";
		}

		//get total amount of orders for each time of a given day     
		$total = 0;
		$total1 = 0;
		$total2 = 0;
		$query = $db->prepare("SELECT * FROM `order` WHERE `planneddate` = :planneddate AND `type1` < 4 AND `project_id` = 0 AND `status` < 5 AND `status` > 0 ORDER BY `time` ASC");
		$query->bindParam(":planneddate", $planneddate, PDO::PARAM_STR);
		$query->execute(); 
		foreach ($query as $row) {
			$type2 = $row['type2'];
			$amount = amount_decrypt($row['amount'], $key2);
			$total += $amount;
			$cumulated += $amount;

			if ($type2 == 1) {
				$total1 += $amount;
			}
			elseif ($type2 == 2) {
				$total2 += $amount;
			}
		}

		echo '<td>'.$i.':00</td>';
		if ($total > 0) {
			if ($cumulated <= $sum) {
				$text .= "<td style='background-color: #fffaac;'><b>".$total." m&sup2;</b></td>";
			}
			else {
				$text .= "<td><b>".$total." m&sup2;</b></td>";
			}
		}
		else {
			$text .= "<td>-</td>";
		}
		if ($total1 > 0) {
			$text1 .= "<td><i>".$total1." m&sup2;</i></td>";

			if ($total2 > 0) {
			$text2 .= "<td><i>".$total2." m&sup2;</i></td>";
			}
			else {
				$text2 .= "<td>-</td>";
			}
		}
		else {
			if ($total2 > 0) {
				$text2 .= "<td><i>".$total2." m&sup2;</i></td>";

				if ($total1 > 0) {
					$text1 .= "<td><i>".$total1." m&sup2;</i></td>";
				}
				else {
					$text1 .= "<td>-</td>";
				}
			}
			else {
				$text1 .= "<td></td>";
				$text2 .= "<td></td>";
			}
		}

		//get total amount of orders for each time of a given day (half hours)     
		$total = 0;
		$total1 = 0;
		$total2 = 0;
		$query = $db->prepare("SELECT * FROM `order` WHERE `planneddate` = :planneddate AND `type1` < 4 AND `project_id` = 0 AND `status` < 5 AND `status` > 0 ORDER BY `time` ASC");
		$query->bindParam(":planneddate", $planneddate2, PDO::PARAM_STR);
		$query->execute(); 
		foreach ($query as $row) {
			$type2 = $row['type2'];
			$amount = amount_decrypt($row['amount'], $key2);
			$total += $amount;
			$cumulated += $amount;

			if ($type2 == 1) {
				$total1 += $amount;
			}
			elseif ($type2 == 2) {
				$total2 += $amount;
			}
		}

		echo '<td>'.$i.':30</td>';
		if ($total > 0) {
			if ($cumulated <= $sum) {
				$text .= "<td style='background-color: #fffaac;'><b>".$total." m&sup2;</b></td>";
			}
			else {
				$text .= "<td><b>".$total." m&sup2;</b></td>";
			}
		}
		else {
			$text .= "<td>-</td>";
		}
		if ($total1 > 0) {
			$text1 .= "<td><i>".$total1." m&sup2;</i></td>";

			if ($total2 > 0) {
			$text2 .= "<td><i>".$total2." m&sup2;</i></td>";
			}
			else {
				$text2 .= "<td>-</td>";
			}
		}
		else {
			if ($total2 > 0) {
				$text2 .= "<td><i>".$total2." m&sup2;</i></td>";

				if ($total1 > 0) {
					$text1 .= "<td><i>".$total1." m&sup2;</i></td>";
				}
				else {
					$text1 .= "<td>-</td>";
				}
			}
			else {
				$text1 .= "<td></td>";
				$text2 .= "<td></td>";
			}
		}
		
	}	


	for ($i=12; $i < 19; $i++) { 
		$planneddate = $nextday." ".$i.":00:00";

		//get total amount of orders for each time of a given day     
		$total = 0;
		$total1 = 0;
		$total2 = 0;
		$query = $db->prepare("SELECT * FROM `order` WHERE `planneddate` = :planneddate AND `type1` < 4 AND `project_id` = 0 AND `status` < 5 AND `status` > 0 ORDER BY `time` ASC");
		$query->bindParam(":planneddate", $planneddate, PDO::PARAM_STR);
		$query->execute(); 
		foreach ($query as $row) {
			$type2 = $row['type2'];
			$amount = amount_decrypt($row['amount'], $key2);
			$total += $amount;
			$cumulated += $amount;

			if ($type2 == 1) {
				$total1 += $amount;
			}
			elseif ($type2 == 2) {
				$total2 += $amount;
			}
		}

		echo '<td>'.$i.':00</td>';
		if ($total > 0) {
			if ($cumulated <= $sum) {
				$text .= "<td style='background-color: #fffaac;'><b>".$total." m&sup2;</b></td>";
			}
			else {
				$text .= "<td><b>".$total." m&sup2;</b></td>";
			}
		}
		else {
			$text .= "<td>-</td>";
		}
		if ($total1 > 0) {
			$text1 .= "<td><i>".$total1." m&sup2;</i></td>";

			if ($total2 > 0) {
			$text2 .= "<td><i>".$total2." m&sup2;</i></td>";
			}
			else {
				$text2 .= "<td>-</td>";
			}
		}
		else {
			if ($total2 > 0) {
				$text2 .= "<td><i>".$total2." m&sup2;</i></td>";

				if ($total1 > 0) {
					$text1 .= "<td><i>".$total1." m&sup2;</i></td>";
				}
				else {
					$text1 .= "<td>-</td>";
				}
			}
			else {
				$text1 .= "<td></td>";
				$text2 .= "<td></td>";
			}
		}
		
	}

	echo "</tr>";
	echo $text;
	echo $text1;
	echo $text2;

	echo '</table>';

	echo "<br><br>";

	//// Overview of orders - change fields
?>
	
	<br><br><br><br>
	<div class="row">
	    <div class="col-md-7">
	    	<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" accept-charset="utf-8">

	    	<?php

			//get open amount
			$total = 0;  
			$total_paused = 0;   
			$query = $db->prepare("SELECT * FROM `order` WHERE planneddate >= :today AND planneddate < :tomorrow AND `type1` < 4 AND `type2` = 1 AND `project_id` = 0 AND (`status` = 1 OR `status` = 4) AND `pickup` = 0");
			$query->bindParam(":today", $nextday_midnight, PDO::PARAM_STR);
			$query->bindParam(":tomorrow", $after_midnight, PDO::PARAM_STR);
			$query->execute(); 
				foreach ($query as $row) {
				$status = $row['status'];
				$amount = amount_decrypt($row['amount'], $key2);
				$total += $amount;

				if ($status == 0) {
		    		$total_paused += $amount;
		    	}
			}
			$total_small_disp = number_format(($total - $total_paused), 0, ',', ' ');
			$total_paused_disp = number_format($total_paused, 0, ',', ' ');

			if ($total_paused == 0) {
				$total_disp = $total_small_disp.' m&sup2';
			}
			else {
				$total_disp = $total_small_disp.' m&sup2 &nbsp; ( <span class="glyphicon glyphicon-time"></span> '.$total_paused_disp.' m&sup2;)';
			}
			?>


			<div class="panel panel-success">
				<div class="panel-heading"><h4><span class="glyphicon glyphicon-edit"></span>&nbsp;&nbsp;Rakodás lista (Igény)</h4> </div>

				<?php
				echo "<table class='table'>";
				echo '<tr><td colspan="7">Ma,&nbsp;'.$datum.'</td><td colspan="2"><i>'.$total_disp.'</i></td></tr>';
				echo "<tr class='title'><td>Sz.</td><td>Idő</td><td></td><td>Vevő</td><td>m&sup2;</td><td>Tipus</td><td>Terület</td><td style='width: 110px;'><span class='glyphicon glyphicon-comment'></span></td><td></td></tr>";

				//get open orders of the day  
				$query = $db->prepare("SELECT * FROM `order` WHERE planneddate >= :today AND planneddate < :tomorrow AND `status` < 2 AND `type1` < 4 AND `type2` = 1 AND `project_id` = 0 AND (`status` = 1 OR `status` = 4) AND `pickup` = 0 ORDER BY `time` ASC, `id` ASC");
				$query->bindParam(":today", $nextday_midnight, PDO::PARAM_STR);
				$query->bindParam(":tomorrow", $after_midnight, PDO::PARAM_STR);	
				$query->execute(); 

				$previoustime = "00:00";
				$i = 1;

				if ($query->rowCount() > 0) {

					$nextday = $today;
					$now = 1;
					include('views/_plan_foreach2.php');		// insert single rows

					?>
					
					<tr class="changeRow2">
					<td colspan="6"></td>
					<td colspan="2">
					<input type="checkbox" id="checkAll" onClick="toggle(<?echo $i;?>)" /> Összes bejelölése<br><br>
					<select class="form-control" name="newField" style="width: 150px;">
					<?php
					// change the field for selected orders
					$query = $db->prepare("SELECT * FROM fields WHERE `complete` < 1 AND `cutting` = 1");
		            $query->execute();
		            while($row = $query->fetch()) {
		                if ($lastfield != $row['id']) {
		                	echo "<option value='".$row['id']."'>".$row['name']."</option>";
		                }
		            }
			        ?>
			    	</select>
					<br><button type="submit" class="btn btn-primary btn-sm" name="changeField">Terület módosítása</button></td>
					<td></td></tr>
				<?php
				}
				else {
					echo "<tr><td colspan='9'>Nincs</td></tr>";
				}
				?>
				</table>
			</div>
	      	
	      	</form>
    	</div>

<div class="col-md-5">
    		<div class="panel panel-warning">
				<div class="panel-heading"><h4><span class="glyphicon glyphicon-time"></span>&nbsp;&nbsp;Protokoll</h4> </div>

				<?php
				echo "<table class='table'>";
				echo "<tr class='title'><td>Sz.</td><td>Idő</td><td>Terület</td><td style='text-align:center;'>m&sup2;</td><td style='text-align:center;'>Vevő</td><td>Raktár</td></tr>";

				// get all entries for protocol
				$entries = array();
				$sort = array();
				$inventory = array();

				// set up array for inventory
				$query = $db->prepare("SELECT * FROM fields WHERE `cutting` = 1 AND `complete` < 1");
				$query->execute();
				foreach ($query as $row) {
					$field_id = $row['id'];

					for ($k=1; $k < 4; $k++) { 
						$inventory[$field_id][$k] = 0; 			
					}
				}

				// (1) get cut pallets
				$query = $db->prepare("SELECT * FROM `pallets` WHERE datum = :datum ORDER BY `cutdate` ASC");
				$query->bindParam(":datum", $datum, PDO::PARAM_STR);
				$query->execute(); 

				foreach ($query as $row) {
					$data['i'] = 1;				// cutting pallets
					$data['id'] = $row['id2'];
					$data['pallet'] = $row['type'];
					$data['amount'] = $row['amount'];
					$data['field'] = $row['field'];
					$data['subject'] = $row['team'];
					$data['datetime'] = $row['cutdate'];

					array_push($entries, $data);
				}

				// (2) get orders picked up
				$query = $db->prepare("SELECT * FROM `order` WHERE `date` = :datum AND `type1` < 4 AND `type2` = 1 AND `pickup` = 1 ORDER BY `time` ASC");
				$query->bindParam(":datum", $datum, PDO::PARAM_STR);
				$query->execute(); 

				foreach ($query as $row) {
					$data['i'] = 2;				// picked up orders
					$data['id'] = $row['id3'];
					$data['pallet'] = $row['pallet'];
					$data['amount'] = getAmount(amount_decrypt($row['amount'], $key2), $type3, $modus);
					$data['field'] = $row['field'];
					$data['subject'] = $row['name'];
					$data['datetime'] = $row['pickupdate'];

					array_push($entries, $data);
				}

				// sort based on time
				foreach ($entries as $key => $data) {
				    $sort[$key] = strtotime($data['datetime']);
				}

				array_multisort($sort, SORT_ASC, $entries);

				// print sorted data
				foreach ($entries as $key => $data) {
					$time_display = substr($data['datetime'], 11, 5);

					// Team (1) or Customer name (2)
					if ($data['i'] == 1) {
						$amount_display = "+ ".$data['amount'];
						$inventory[$data['field']][$data['pallet']] += $data['amount'];

						$subject_display = "Gép ".$data['subject'];
						echo "<tr class ='protocol1'>";
						echo "<td>".$data['id']."</td>";
					}
					elseif ($data['i'] == 2) {
						$amount_display = "- ".$data['amount'];
						$inventory[$data['field']][$data['pallet']] -= $data['amount'];

						$query = $db->prepare("SELECT * FROM customers WHERE `id` = :id");
						$query->bindParam(":id", $data['subject'], PDO::PARAM_STR);
						$query->execute();
						$result = $query->fetch(PDO::FETCH_OBJ);

						$subject_display = $result->name;
						echo "<tr class ='protocol2'>";

						$id3_display = substr($data['id'], -2);

						if ($id3_display == "00") {
							$id3_display = 100;
						}

						echo "<td>".$id3_display."</td>";
					}

					
					echo "<td>".$time_display."</td>";

					if ($data['pallet'] == 1) {
						$pallet_display = 50;
					}
					elseif ($data['pallet'] == 2) {
						$pallet_display = 30;
					}
					elseif ($data['pallet'] == 3) {
						$pallet_display = 56;
					}

					$query = $db->prepare("SELECT * FROM fields WHERE `id` = :id");
					$query->bindParam(":id", $data['field'], PDO::PARAM_STR);
					$query->execute();
					$result = $query->fetch(PDO::FETCH_OBJ);
					echo "<td>".$result->name." [".$pallet_display."]</td>";

					echo "<td style='text-align:center;'>".$amount_display."</td>";

					echo "<td style='text-align:center;'>".$subject_display."</td>";

					echo "<td><b>".$inventory[$data['field']][$data['pallet']]." m&sup2;</b></td>";
					echo "</tr>";
				}

				echo "</table>";

				?>
    	</div>
    </div>

</div>

  


