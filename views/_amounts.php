<?php 
///////////////////////
// Capacity planning //
///////////////////////

require_once('config/config.php');
include('views/_header'.$header.'.php');
include('tools/functions.php');

$db = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.'', DB_USER, DB_PASS);
$db -> exec("set names utf8");
date_default_timezone_set('Europe/Budapest');
?>

<div class="inputform">
	<div class="row">
	    <div class="col-md-10">
	      <h3 style="margin-top:10px;">Kapacitás tervezés</h3>  
	    </div>
	    <div class="col-md-2" style="padding-bottom: 0px;">
	    	<label>Sztandard</label>
	    	<?php
	    	//get standard amounts from database      
			$query = $db->prepare("SELECT * FROM `amounts` WHERE `type` = 0 AND `timespan` = 1 ORDER BY `edited` DESC LIMIT 1");
			$query->execute(); 
			$result = $query->fetch(PDO::FETCH_OBJ);
			$standard_total = $result->amount;

			$query = $db->prepare("SELECT * FROM `amounts` WHERE `type` = 0 AND `timespan` = 2 ORDER BY `edited` DESC LIMIT 1");
			$query->execute(); 
			$result = $query->fetch(PDO::FETCH_OBJ);
			$standard_1 = $result->amount;

	    	echo "<table class='table amounts'><tr>";
			echo "<tr><td>Nap</td>";
			echo '<td><input type="number" class="form-control" style="width: 90px;" value="'.$standard_total.'" onfocusout="amountsFunction(1, 1)" id="amounts_1"></td>';
			echo "<input type='hidden' value='0000-00-00' id='amountsdate_1'>";
			echo "</tr><tr>";
			echo "<td>- 9<sup>00</sup></td>";
			echo '<td><input type="number" class="form-control" style="width: 90px;" value="'.$standard_1.'" onfocusout="amountsFunction(2, 2)" id="amounts_2"></td>';
			echo "<input type='hidden' value='0000-00-00' id='amountsdate_2'>";
			echo "</tr></table>";
			?>

	    </div>
	</div>
	<br><br>

	<div class="row">
		<div class="col-md-12">
			<table class="table table-bordered table-condensed" id="dateTable">
				<?php
				$days = array("Vasárnap", "Hétfő", "Kedd", "Szerda", "Csütörtök", "Péntek", "Szombat");
				$currentWeekNumber = date('W');
				$currentYear = date('Y');
				$today = date("Y-m-d");
				$currentWeekDay = date('w');

				//Current week
				$time = new DateTime();
				$time->setISODate($currentYear, $currentWeekNumber);
				echo "<tr><td>".$currentWeekNumber."</td>";

				$k = 1;
				$n = 3;
				for ($i=1; $i < 6; $i++) { 
				    $date = $time->format('m-d');
				    $fulldate = $time->format('Y-m-d');
				    $day = $time->format('w');
				    $total_big = 0;
				    $total_small = 0;
				    $trucks_count = 0;

				    //get operations of the field     
					$query = $db->prepare("SELECT * FROM `order` WHERE `date` = :datum AND `status` < 5 ORDER BY `time` ASC");
					$query->bindParam(":datum", $fulldate, PDO::PARAM_STR);
					$query->execute(); 
					foreach ($query as $row) {
						$amount = amount_decrypt($row['amount'], $key2);
						$type1 = $row['type1'];
						$project_id = $row['project_id'];
						$name = $row['name'];

						/////////////////////// TEMPORARY SOLUTION: exclude Garden Group (big MED project)
						if ($name != 219) {
							if ($type1 < 4 AND $project_id == 0) {
					    		$total_small += $amount;
						    }
						    else {
						    	$total_big += $amount;
						    }
						}
					}

					// plus amount of projekt
					$query2 = $db->prepare("SELECT * FROM `trucks` WHERE `datum` = :datum");
					$query2->bindParam(":datum", $fulldate, PDO::PARAM_STR);
					$query2->execute();
					$truck_amount = $query2->rowCount();
					foreach ($query2 as $row2) {
						$trucks_count ++;
						$total_big += $row2['amount'];
					}

					// show total amounts of different time spans
					$total1 = 0;
					$time1 = "09:00:01";

					$query = $db->prepare("SELECT * FROM `order` WHERE `date` = :datum AND `status` < 5 ORDER BY `time` ASC");
					$query->bindParam(":datum", $fulldate, PDO::PARAM_STR);
					$query->execute(); 
					foreach ($query as $row) {
						
						$amount = amount_decrypt($row['amount'], $key2);
						$type1 = $row['type1'];
						$project_id = $row['project_id'];
						$time_order = $row['time'];
						$name = $row['name'];

						if ($type1 < 4 AND $project_id == 0) {
					    	if ($time_order < $time1) {
								$total1 += $amount;
							}
					    }		
					}

					$total_small_disp = number_format($total_small, 0, ',', ' ');
					$total1_disp = number_format($total1, 0, ',', ' ');
					$total_big_disp = number_format($total_big, 0, ',', ' ');


					//get standard amounts from database      
					$query = $db->prepare("SELECT * FROM `amounts` WHERE `datum` = :datum AND `type` = 1 AND `timespan` = 1 ORDER BY `edited` DESC LIMIT 1");
					$query->bindParam(":datum", $fulldate, PDO::PARAM_STR);
					$query->execute(); 
					$list_length = $query->rowCount();
					$result = $query->fetch(PDO::FETCH_OBJ);

					if ($list_length > 0) {
						$standard_total_single= $result->amount;
						$datum_edited = substr($result->edited, 0, 16);
						
						$user_edited = $result->user;
						$query = $db->prepare("SELECT * FROM users WHERE `id` = :id");
						$query->bindParam(":id", $user_edited, PDO::PARAM_STR);
						$query->execute();
						$result2 = $query->fetch(PDO::FETCH_OBJ);
						$username = $result2->username;
						

						if ($standard_total_single == $standard_total) {
							$changed1 = 0;
						}
						else {
							$changed1 = 1;
						}
					}
					else {
						$standard_total_single= $standard_total;
						$changed1 = 0;
						$username = "";
						$datum_edited = "";
					}
					

					$query = $db->prepare("SELECT * FROM `amounts` WHERE `datum` = :datum AND `type` = 1 AND `timespan` = 2 ORDER BY `edited` DESC LIMIT 1");
					$query->bindParam(":datum", $fulldate, PDO::PARAM_STR);
					$query->execute(); 
					$list_length = $query->rowCount();
					$result = $query->fetch(PDO::FETCH_OBJ);
					
					if ($list_length > 0) {
						$standard_1_single= $result->amount;
						$datum_edited2 = substr($result->edited, 0, 16);
						
						$user_edited2 = $result->user;
						$query = $db->prepare("SELECT * FROM users WHERE `id` = :id");
						$query->bindParam(":id", $user_edited2, PDO::PARAM_STR);
						$query->execute();
						$result2 = $query->fetch(PDO::FETCH_OBJ);
						$username2 = $result2->username;
						
						if ($standard_1_single == $standard_1) {
							$changed2 = 0;
						}
						else {
							$changed2 = 1;
						}
					}
					else {
						$standard_1_single= $standard_1;
						$changed2 = 0;
						$username2 = "";
						$datum_edited2 = "";
					}


					if ($i < $currentWeekDay) {
						echo '<td class="past" style="padding: 10px;">';
						$date_label = $days[$day];
						
					}
					elseif ($i == $currentWeekDay) {
						echo '<td class="active bordered" style="padding: 10px;">';
						$date_label = "Ma";
						$k++;
					}
					elseif ($i == $currentWeekDay + 1) {
						echo '<td class="normal bordered bg-orange" style="padding: 10px;">';
						$date_label = "Holnap";
						$k++;
					}
					else {
						echo '<td class="normal bordered bg-orange" style="padding: 10px;">';
						$date_label = $days[$day];
						$k++;
					}

					if ($total_big > 0) {
						$big_text = "<br><i>GR: ".$total_big_disp." m&sup2 (".$trucks_count." kamion)</i>";
					}
					else {
						$big_text = "<br><i>GR: -</i>";
					}
					
					echo '<b>'.$date_label.', '.$fulldate.'</b>';
					echo $big_text;

					if ($i < $currentWeekDay) {
						echo "<br><i>KR: ".$total_small_disp." m&sup2";
						$n++;
					}
					else {
						echo "<br><table class='table amounts' style='margin: 20px 0px;'><tr>";
						echo "<td></td><td colspan='2' style='text-align:right;'><i>Megrendelt</i></td></tr>";
						echo "<b><td>Nap</b></td>";

						// highlight if already changed
						if ($changed1 == 1) {
							echo '<td><input type="number" class="form-control" style="width: 80px; background-color: #fcffcf;" value="'.$standard_total_single.'" onfocusout="amountsFunction('.$n.', 1)" id="amounts_'.$n.'"></td>';
						}
						else {
							echo '<td><input type="number" class="form-control" style="width: 80px;" value="'.$standard_total_single.'" onfocusout="amountsFunction('.$n.', 1)" id="amounts_'.$n.'"></td>';
						}
						
						echo "<td><b><i>".$total_small_disp." m&sup2</i></b></td>";
						echo "<input type='hidden' value='".$fulldate."' id='amountsdate_".$n."'>";
						$n++;
						echo "</tr><tr>";

						// show editor if changed
						if ($changed1 == 1) {
							echo "<td colspan='3' style='color: #919191;'><i>".$datum_edited." (".$username.")</i></td>";
							echo "</tr><tr>";
						}

						echo "<td>- 9<sup>00</sup></td>";

						// highlight if already changed
						if ($changed2 == 1) {
							echo '<td><input type="number" class="form-control" style="width: 80px; background-color: #fcffcf;" value="'.$standard_1_single.'" onfocusout="amountsFunction('.$n.', 2)" id="amounts_'.$n.'"></td>';
						}
						else {
							echo '<td><input type="number" class="form-control" style="width: 80px;" value="'.$standard_1_single.'" onfocusout="amountsFunction('.$n.', 2)" id="amounts_'.$n.'"></td>';
						}
						echo "<td><i>".$total1_disp." m&sup2</i></td>";
						echo "<input type='hidden' value='".$fulldate."' id='amountsdate_".$n."'>";
						
						echo "</tr>";

						// show editor if changed
						if ($changed2 == 1) {
							echo "<td colspan='3' style='color: #919191;'><i>".$datum_edited2." (".$username2.")</i></td>";
							echo "</tr><tr>";
						}

						echo "</table>";
					}

					echo "</td>";

					$time->add(new DateInterval('P1D'));
					$n++;
				}
				echo "</tr>";
				echo "<tr class='break'><td colspan ='6'></td</tr>";

				for ($j=2; $j < 5; $j++) { 
					//next week
					$currentWeekNumber = $currentWeekNumber + 1;
					$time->add(new DateInterval('P2D'));

					echo "<tr><td>".$currentWeekNumber."</td>";

					for ($i=1; $i < 6; $i++) { 
					    $date = $time->format('m-d');
					    $fulldate = $time->format('Y-m-d');
					    $day = $time->format('w');

					    $total_big = 0;
				    	$total_small = 0;
				    	$trucks_count = 0;

					    //get total amount of orders for each day     
						$query = $db->prepare("SELECT * FROM `order` WHERE `date` = :datum AND `status` < 5 ORDER BY `time` ASC");
						$query->bindParam(":datum", $fulldate, PDO::PARAM_STR);
						$query->execute(); 
						foreach ($query as $row) {
							$amount = amount_decrypt($row['amount'], $key2);
							$type1 = $row['type1'];
							$project_id = $row['project_id'];
							$name = $row['name'];

							if ($type1 < 4 AND $project_id == 0) {
					    		$total_small += $amount;
						    }
						    else {
						    	$total_big += $amount;
						    }
						}

						// plus amount of projekt
						$query2 = $db->prepare("SELECT * FROM `trucks` WHERE `datum` = :datum");
						$query2->bindParam(":datum", $fulldate, PDO::PARAM_STR);
						$query2->execute();
						$truck_amount = $query2->rowCount();
						foreach ($query2 as $row2) {
							$trucks_count ++;
							$total_big += $row2['amount'];
						}

						// show total amounts of different time spans
						$total1 = 0;
						$time1 = "09:00:01";

						$query = $db->prepare("SELECT * FROM `order` WHERE `date` = :datum AND `status` < 5 ORDER BY `time` ASC");
						$query->bindParam(":datum", $fulldate, PDO::PARAM_STR);
						$query->execute(); 
						foreach ($query as $row) {
							
							$amount = amount_decrypt($row['amount'], $key2);
							$type1 = $row['type1'];
							$project_id = $row['project_id'];
							$time_order = $row['time'];
							$name = $row['name'];

							if ($type1 < 4 AND $project_id == 0) {
						    	if ($time_order < $time1) {
									$total1 += $amount;
								}
						    }		
						}

						$total_small_disp = number_format($total_small, 0, ',', ' ');
						$total1_disp = number_format($total1, 0, ',', ' ');
						$total_big_disp = number_format($total_big, 0, ',', ' ');


						//get standard amounts from database      
						$query = $db->prepare("SELECT * FROM `amounts` WHERE `datum` = :datum AND `type` = 1 AND `timespan` = 1 ORDER BY `edited` DESC LIMIT 1");
						$query->bindParam(":datum", $fulldate, PDO::PARAM_STR);
						$query->execute(); 
						$list_length = $query->rowCount();
						$result = $query->fetch(PDO::FETCH_OBJ);

						if ($list_length > 0) {
							$standard_total_single= $result->amount;
							$datum_edited = substr($result->edited, 0, 16);
							
							$user_edited = $result->user;
							$query = $db->prepare("SELECT * FROM users WHERE `id` = :id");
							$query->bindParam(":id", $user_edited, PDO::PARAM_STR);
							$query->execute();
							$result2 = $query->fetch(PDO::FETCH_OBJ);
							$username = $result2->username;


							if ($standard_total_single == $standard_total) {
								$changed1 = 0;
							}
							else {
								$changed1 = 1;
							}
						}
						else {
							$standard_total_single= $standard_total;
							$changed1 = 0;
							$username = "";
							$datum_edited = "";
						}
						

						$query = $db->prepare("SELECT * FROM `amounts` WHERE `datum` = :datum AND `type` = 1 AND `timespan` = 2 ORDER BY `edited` DESC LIMIT 1");
						$query->bindParam(":datum", $fulldate, PDO::PARAM_STR);
						$query->execute(); 
						$list_length = $query->rowCount();
						$result = $query->fetch(PDO::FETCH_OBJ);
						
						if ($list_length > 0) {
							$standard_1_single= $result->amount;
							$datum_edited2 = substr($result->edited, 0, 16);
							
							$user_edited2 = $result->user;
							$query = $db->prepare("SELECT * FROM users WHERE `id` = :id");
							$query->bindParam(":id", $user_edited, PDO::PARAM_STR);
							$query->execute();
							$result2 = $query->fetch(PDO::FETCH_OBJ);
							$username2 = $result2->username;
							
							if ($standard_1_single == $standard_1) {
								$changed2 = 0;
							}
							else {
								$changed2 = 1;
							}
						}
						else {
							$standard_1_single= $standard_1;
							$changed2 = 0;
							$username2 = "";
							$datum_edited2 = "";
						}


						echo '<td class="normal bordered bg-orange" style="padding: 10px;">';
						$date_label = $days[$day];

						if ($total_big > 0) {
						$big_text = "<br><i>GR: ".$total_big_disp." m&sup2 (".$trucks_count." kamion)</i>";
						}
						else {
							$big_text = "<br><i>GR: -</i>";
						}
					
						echo '<b>'.$date_label.', '.$fulldate.'</b>';
						echo $big_text;

						echo "<br><table class='table amounts' style='margin: 20px 0px;'><tr>";
						echo "<td></td><td colspan='2' style='text-align:right;'><i>Megrendelt</i></td></tr>";
						echo "<tr><td><b>Nap</b></td>";

						// highlight if already changed
						if ($changed1 == 1) {
							echo '<td><input type="number" class="form-control" style="width: 80px; background-color: #fcffcf;" value="'.$standard_total_single.'" onfocusout="amountsFunction('.$n.', 1)" id="amounts_'.$n.'"></td>';
						}
						else {
							echo '<td><input type="number" class="form-control" style="width: 80px;" value="'.$standard_total_single.'" onfocusout="amountsFunction('.$n.', 1)" id="amounts_'.$n.'"></td>';
						}						

						echo "<td><b><i>".$total_small_disp." m&sup2</i></b></td>";
						echo "<input type='hidden' value='".$fulldate."' id='amountsdate_".$n."'>";
						$n++;
						echo "</tr><tr>";

						// show editor if changed
						if ($changed1 == 1) {
							echo "<td colspan='3' style='color: #919191;'><i>".$datum_edited." (".$username.")</i></td>";
							echo "</tr><tr>";
						}

						echo "<td>- 9<sup>00</sup></td>";

						// highlight if already changed
						if ($changed2 == 1) {
							echo '<td><input type="number" class="form-control" style="width: 80px; background-color: #fcffcf;" value="'.$standard_1_single.'" onfocusout="amountsFunction('.$n.', 2)" id="amounts_'.$n.'"></td>';
						}
						else {
							echo '<td><input type="number" class="form-control" style="width: 80px;" value="'.$standard_1_single.'" onfocusout="amountsFunction('.$n.', 2)" id="amounts_'.$n.'"></td>';
						}	

						echo "<td><i>".$total1_disp." m&sup2</i></td>";
						echo "<input type='hidden' value='".$fulldate."' id='amountsdate_".$n."'>";
						echo "</tr>";

						// show editor if changed
						if ($changed2 == 1) {
							echo "<td colspan='3' style='color: #919191;'><i>".$datum_edited2." (".$username2.")</i></td>";
							echo "</tr><tr>";
						}

						echo "</table>";


						echo "</td>";

						$time->add(new DateInterval('P1D'));
						$k++;
						$n++;
					}
					echo "</tr>";
					echo "<tr class='break'><td colspan ='6'></td</tr>";
				}

			echo "</table>"
			?>
		</div>
	</div>
</div>