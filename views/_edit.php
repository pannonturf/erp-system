<?php
///////////////////////////////////////////////////////////
// Include edit, delete and finish modals for sales.php  //
///////////////////////////////////////////////////////////   
?>

<div class="inputform">

<div class="row">
	<div class="col-md-10">
		<h3 style="margin-top:10px;">Módosítás</h3>
	</div>
	<div class="col-md-2">
		<?php
		// check if open orders were displayed to show again after closing the edit
		if (isset($_GET['open'])) {
			echo '<button type="button" style="font-size: 30px;margin-top: 20px;" data-href="'.$edit_link2.'open=1" class="close"><span aria-hidden="true">&times;</span></button>';
		}
		else {
			echo '<button type="button" style="font-size: 30px;margin-top: 20px;" data-href="'.$edit_link2.'" class="close"><span aria-hidden="true">&times;</span></button>';
		}
		?>
	</div>
</div>

<?php
// get data for order to be edited
$query = $db->prepare("SELECT * FROM `order` WHERE `id` = :id");
$query->bindParam(":id", $edit, PDO::PARAM_STR);
$query->execute(); 

foreach ($query as $row) {
	$id2 = $row['id2'];
	$id3 = $row['id3'];
	$prefix = $row['prefix'];
	$datum = $row['date'];
	$time = $row['time'];
	$sort = $row['sort'];
	$time_original = $time;
	$timedisplay = substr($time, 0, 5);
	$planneddate = substr($row['planneddate'], 0, 16);
	$customer_id = $row['name'];
	$type1_original = $row['type1'];
	$type2 = $row['type2'];
	$type3_original = $row['type3'];
	$amount = getAmount(amount_decrypt($row['amount'], $key2), $type3_original, $modus);
	$amount_original = $amount;
	$field = $row['field'];
	$pallet = $row['pallet'];
	$delivery = $row['delivery'];
	$forwarder = $row['forwarder'];
	$payment = $row['payment'];
	$paid = $row['paid'];
	$status = $row['status'];

	$deliveryname = $row['deliveryname'];
	$deliveryaddress = $row['deliveryaddress'];
	$country = $row['country'];
	$city = $row['city'];
	$telephone = $row['telephone'];
	$email = $row['email'];
	$invoicename = $row['invoicename'];
	$invoiceaddress = $row['invoiceaddress'];
	$invoicenumber = $row['invoicenumber'];
	$note = $row['note'];
	$note2 = $row['note2'];
	$licence = $row['licence'];

	$query = $db->prepare("SELECT * FROM cities WHERE `id` = :id");
    $query->bindParam(":id", $city, PDO::PARAM_STR);
    $query->execute();
    $result = $query->fetch(PDO::FETCH_OBJ);
	$city_disp = $result->name;
	$plz = $result->plz;

	if (isset($_GET['open'])) {
		$target = $edit_link2.'open=1';
	}
	else {
		$target = $edit_link2;
	}

	$id3_display = substr($id3, -2);

	if ($id3_display == "00") {
		$id3_display = 100;
	}

				
	// Customer data
	$query = $db->prepare("SELECT * FROM customers WHERE `id` = :id");
	$query->bindParam(":id", $customer_id, PDO::PARAM_STR);
	$query->execute();
	$result = $query->fetch(PDO::FETCH_OBJ);
	$customer_name = $result->name;
	$contactperson = $result->contactperson;
	$customer_street = $result->street;
	$customer_plz = $result->plz;
	$customer_city = $result->city;
	$customer_country = $result->country;
	$customer_phone = $result->phone;
	$customer_email = $result->email;
	?>

	<form method="post" name="myForm" action="<?php echo $target?>" accept-charset="utf-8" onsubmit="return validateForm4()">

	<div class="row modal_row">
		<div class="col-md-4">
			<div class="form-group">
				<div class="col-sm-11" style="padding-bottom: 10px;">
				  <input type="text" class="form-control" name="customer_name" value="<?echo $customer_name;?>">
				</div>
			</div>
	        <br>
	        <div class="form-group">
				<div class="col-sm-4" style="padding-bottom: 10px;">
				  <input type="number" class="form-control" name="customer_plz" value="<?echo $customer_plz;?>">
				</div>
				<div class="col-sm-7" style="padding-bottom: 10px;">
				  <input type="text" class="form-control" name="customer_city" value="<?echo $customer_city;?>">
				</div>
			</div>
			<br>
	        <div class="form-group">
				<div class="col-sm-11" style="padding-bottom: 10px;">
				  <input type="text" class="form-control" name="customer_street" value="<?echo $customer_street;?>">
				</div>
			</div>
	    </div>

	    <div class="col-md-4">
	    	<div class="form-horizontal">
		        <div class="form-group" style="margin-bottom: 10px;" id="customer-group-3">
					<label for="contactperson" class="col-sm-4 control-label" style="padding-bottom: 0px;">Kapcsolattartó</label>
					<div class="col-sm-8" style="padding-bottom: 0px;">
					  <input type="text" class="form-control" name="contactperson" value="<?echo $contactperson;?>">
					</div>
				</div>
		        <div class="form-group" style="margin-bottom: 10px;" id="customer-group-4">
					<label for="telephone" class="col-sm-4 control-label" style="padding-bottom: 0px;">Telefon</label>
						<div class="col-sm-8" style="padding-bottom: 0px">
						  <input type="tel" class="form-control" name="customer_phone" value="<?echo $customer_phone;?>">
						</div>
				</div>
				<div class="form-group" id="customer-group-5">
					<label for="email" class="col-sm-4 control-label">Email</label>
					<div class="col-sm-8">
					  <input type="email" class="form-control" name="customer_email" value="<?echo $customer_email;?>">
					</div>
				</div>
			</div>
	    </div>

		<div class="col-md-2"></div>
		<div class="col-md-2" style="padding-bottom: 0px;">
	    	<div class="form-group">
	      		<label for="status">Státusz</label>
		        			
		        <?php
	        	echo '<select class="form-control" name="status">';
		        if ($status == 1) {
		        	 echo '<option value="1" selected>Megbízás</option>';
		        	 if ($cutting_modus == 1) {
		        	 	echo '<option value="3">Vágás befejezett</option>';
		        	 }	 
		        	 echo '<option value="4">Elvitt</option>';
		        }
		        elseif ($status == 2) {
		        	 echo '<option value="1">Megbízás</option>';
		        	 echo '<option value="2" selected>Most munkában</option>';
		        	 echo '<option value="3">Vágás befejezett</option>';
		        }
		        elseif ($status == 3) {
		        	 echo '<option value="1">Megbízás</option>';
		        	 echo '<option value="3" selected>Vágás befejezett</option>';
		        	 echo '<option value="4">Elvitt</option>';
		        }
		        elseif ($status == 4) {
		        	if ($cutting_modus == 1) {
		        	 	echo '<option value="3">Vágás befejezett</option>';
		        	 }
		        	 else {
		        	 	echo '<option value="1" selected>Megbízás</option>';
		        	 }
		        	 echo '<option value="4" selected>Elvitt</option>';
		        }
		        elseif ($status == 5) {
		        	 echo '<option value="1">Megbízás</option>';
		        	 echo '<option value="5" selected>Törölt</option>';
		        }
	            echo '</select>';
	            echo '<input type="hidden" name="oldstatus" value="'.$status.'">';
				

	            /*
				if ($status < 4) {
					if ($paid == 0) {
						echo '<div style="margin-top: 15px;" id="advance_payment"><button class="btn btn-success btn-xs" onclick="statusFunction('.$edit.', 4, 4)">Előre Fizetés</button></div>';
					}
					else {
						?>
			            <div class="checkbox">
						    <label>
						        <input type="checkbox" name="paid" checked> Fizetett
						    </label>
						</div> 
						<?php
					}
				}
				*/
				if ($paid == 0) {
				?>
	            <div class="checkbox">
				    <label>
				        <input type="checkbox" name="paid"> Fizetett
				    </label>
				</div> 
				<?php
				}
				elseif ($paid == 1) {
				?>
	            <div class="checkbox">
				    <label>
				        <input type="checkbox" name="paid" checked> Fizetett
				    </label>
				</div> 
				<?php
				}
				?>
			</div>
	    </div>
	</div>

	<div id="editDiv" class="row modal_row">
	<?php
	// static view, if order is already finished
	if ($status == 4) {
		?>
		<div class="col-md-5"><br>
			<div class="form-group">
				<label for="date" class="col-sm-3 control-label" style="padding-bottom: 0px;">Dátum</label>
				<div class="col-sm-9" style="padding-bottom: 0px;">
					<?php
					echo '<p class="form-control-static">'.$datum.'</p>';
					echo '<input type="hidden" name="date" value="'.$datum.'">';
					?>
				</div>
			</div>
		</div>

		<div class="col-md-5"><br>
			<div class="form-group">
				<label for="deliverytime" class="col-sm-3 control-label" style="padding-bottom: 0px;">Időpont</label>
				<div class="col-sm-5" style="padding-bottom: 0px;">
					<?php
					echo '<p class="form-control-static">'.$timedisplay.'</p>';
					echo '<input type="hidden" name="time" value="'.$timedisplay.'">';
					?>
				</div>
			</div>
		</div>
	</div>

	<?php
	}

	// simple date and time view, when order is not finished, but in the past
	elseif ($datum < $today) {
		?>
		<div class="col-md-5"><br>
			<div class="form-group">
				<label for="date" class="col-sm-3 control-label" style="padding-bottom: 0px;">Dátum</label>
				<div class="col-sm-4" style="padding-bottom: 0px;">
				<input type='date' class="form-control date-field" style="padding: 0px 10px;" name="date" value="<?php echo $datum; ?>">
				</div>
			</div>
		</div>

		<div class="col-md-5"><br>
			<div class="form-group">
				<label for="deliverytime" class="col-sm-3 control-label" style="padding-bottom: 0px;">Időpont</label>
				<div class="col-sm-3" style="padding-bottom: 0px;">
				<input type='time' class="form-control date-field" style="padding: 0px 10px;" name="time" value="<?php echo $timedisplay; ?>">
				</div>
			</div>
		</div>
	</div>
	<?php
	}

	// dynamic view with capacity check
	else {
	?>
		<div class="col-md-8">
			<label for="date">Nap<sup>*</sup></label>
			<table class="table table-bordered table-condensed" id="dateTable">
				<?php
				$days = array("Vasárnap", "Hétfő", "Kedd", "Szerda", "Csütörtök", "Péntek", "Szombat");
				$currentWeekNumber = date('W');
				$currentYear = date('Y');
				$today = date("Y-m-d");
				$currentWeekDay = date('w');
				$other_datum = 1;

				// check capacity - get standard values
				$query = $db->prepare("SELECT * FROM `amounts` WHERE `type` = 0 AND `timespan` = 1 ORDER BY `edited` DESC LIMIT 1");
				$query->execute(); 
				$result = $query->fetch(PDO::FETCH_OBJ);
				$standard_total = $result->amount;

				$query = $db->prepare("SELECT * FROM `amounts` WHERE `type` = 0 AND `timespan` = 2 ORDER BY `edited` DESC LIMIT 1");
				$query->execute(); 
				$result = $query->fetch(PDO::FETCH_OBJ);
				$standard_1 = $result->amount;


				//Current week
				$time = new DateTime();
				$time->setISODate($currentYear, $currentWeekNumber);

				echo "<tr><td>".$currentWeekNumber."</td>";

				$k = 1;
				for ($i=1; $i < 6; $i++) { 
				    $date = $time->format('m-d');
				    $fulldate = $time->format('Y-m-d');
				    $day = $time->format('w');
				    $total_big = 0;
				    $total_small = 0;

				    if ($datum == $fulldate) {
				    	$checked = "checked";
				    	$other_datum = 0;
				    }
				    else {
				    	$checked = "";
				    }

				    //get amounts on date    
					$query = $db->prepare("SELECT * FROM `order` WHERE `date` = :datum AND `status` < 5 ORDER BY `time` ASC");
					$query->bindParam(":datum", $fulldate, PDO::PARAM_STR);
					$query->execute(); 
					foreach ($query as $row) {
						$type1 = $row['type1'];
						$type3 = $row['type3'];
						$amount = getAmount(amount_decrypt($row['amount'], $key2), $type3, $modus);
						$project_id = $row['project_id'];
						$name = $row['name'];

						if ($type1 < 4 AND $project_id == 0) {
					    	$total_small += $amount;
					    }
					}

					$total_small_disp = number_format($total_small, 0, ',', ' ');

					// check capacity - see if it differs from standard
					$query = $db->prepare("SELECT * FROM `amounts` WHERE `datum` = :datum AND `type` = 1 AND `timespan` = 1 ORDER BY `edited` DESC LIMIT 1");
					$query->bindParam(":datum", $fulldate, PDO::PARAM_STR);
					$query->execute(); 
					$list_length = $query->rowCount();
					$result = $query->fetch(PDO::FETCH_OBJ);

					if ($list_length > 0) {
						$standard_total_single = $result->amount;
					}
					else {
						$standard_total_single = $standard_total;
					}

					$available_total = $standard_total_single - $total_small;
					$available_total_disp = number_format($available_total, 0, ',', ' ');
  					$past = 0;	


					if ($i < $currentWeekDay) {
						echo '<td class="past" style="padding-left: 25px;">'.$days[$day].'<br>'.$date;
						$past = 1;
					}
					elseif ($i == $currentWeekDay) {
						echo '<td class="active" id="datebutton'.$k.'"><label class="radio-inline"><input type="radio" id="datum'.$k.'" name="date" onclick="dateFunction('.$k.', 1, '.$available_total.')" value="'.$fulldate.'" '.$checked.'>Ma<br><b>'.$date.'</b>';
						$k++;
					}
					elseif ($i == $currentWeekDay + 1) {
						echo '<td class="normal" id="datebutton'.$k.'"><label class="radio-inline"><input type="radio" id="datum'.$k.'" name="date" onclick="dateFunction('.$k.', 1, '.$available_total.')" value="'.$fulldate.'" '.$checked.'>Holnap<br><b>'.$date.'</b>';
						$k++;
					}
					else {
						echo '<td class="normal" id="datebutton'.$k.'"><label class="radio-inline"><input type="radio" id="datum'.$k.'" name="date" onclick="dateFunction('.$k.', 1, '.$available_total.')" value="'.$fulldate.'" '.$checked.'> '.$days[$day].'<br><b>'.$date.'</b>';
						$k++;
					}
					
					if ($past == 0) {

						// also check capacity for mornings
	  					$total1 = 0;
						$time1 = "09:00:01";

						$query = $db->prepare("SELECT * FROM `order` WHERE `date` = :datum AND `status` < 5 ORDER BY `time` ASC");
						$query->bindParam(":datum", $fulldate, PDO::PARAM_STR);
						$query->execute(); 
						foreach ($query as $row) {
							$type1 = $row['type1'];
							$type3 = $row['type3'];
							$amount = getAmount(amount_decrypt($row['amount'], $key2), $type3, $modus);
							$project_id = $row['project_id'];
							$time_order = $row['time'];
							$name = $row['name'];

							if (($type1 < 4 AND $project_id == 0) AND ($time_order < $time1)) {
								$total1 += $amount;
						    }	
						}

						$query = $db->prepare("SELECT * FROM `amounts` WHERE `datum` = :datum AND `type` = 1 AND `timespan` = 2 ORDER BY `edited` DESC LIMIT 1");
						$query->bindParam(":datum", $fulldate, PDO::PARAM_STR);
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
					
						echo "<br>";

						if ($available_total > 500) {
							echo '<span class="badge" style="background-color: #5cb85c;font-size: 14px;">'.$available_total_disp.'</span>&nbsp;&nbsp;';

							if ($available_1 > 500) {
								echo '<span class="badge" style="background-color: #5cb85c;font-size: 14px;">R</span>';
							}
							elseif ($available_1 > 0) {
								echo '<span class="badge" style="background-color: #ff9c2e;font-size: 14px;"">R</span>';
							}
							else {
								echo '<span class="badge" style="background-color: red;font-size: 14px;">R</span>';

							}
						}
						elseif ($available_total > 0) {
							echo '<span class="badge" style="background-color: #ff9c2e;font-size: 14px;"">'.$available_total_disp.'</span>&nbsp;&nbsp;';

							if ($available_1 > 500) {
								echo '<span class="badge" style="background-color: #5cb85c;font-size: 14px;">R</span>';
							}
							elseif ($available_1 > 0) {
								echo '<span class="badge" style="background-color: #ff9c2e;font-size: 14px;"">R</span>';
							}
							else {
								echo '<span class="badge" style="background-color: red;font-size: 14px;">R</span>';
							}
						}
						else {
							echo '<span class="badge" style="background-color: red;font-size: 14px;">Megtelt</span>';
						}

						echo "<br><i>".$total_small_disp." m&sup2</i>";
						echo "</label></td>";
					}

					$time->add(new DateInterval('P1D'));
				}
				echo "</tr>";

				for ($j=2; $j < 4; $j++) { 
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

					    if ($datum == $fulldate) {
					    	$checked = "checked";
					    	$other_datum = 0;
					    }
					    else {
					    	$checked = "";
					    }

					    //get total amount of orders for each day     
						$query = $db->prepare("SELECT * FROM `order` WHERE `date` = :datum AND `status` < 5 ORDER BY `time` ASC");
						$query->bindParam(":datum", $fulldate, PDO::PARAM_STR);
						$query->execute(); 
						foreach ($query as $row) {
							$type1 = $row['type1'];
							$type3 = $row['type3'];
							$amount = getAmount(amount_decrypt($row['amount'], $key2), $type3, $modus);
							$project_id = $row['project_id'];
							$name = $row['name'];

							if ($type1 < 4 AND $project_id == 0) {
						    	$total_small += $amount;
						    }
						}

						$total_small_disp = number_format($total_small, 0, ',', ' ');
						

						// check capacity - see if it differs from standard
						$query = $db->prepare("SELECT * FROM `amounts` WHERE `datum` = :datum AND `type` = 1 AND `timespan` = 1 ORDER BY `edited` DESC LIMIT 1");
						$query->bindParam(":datum", $fulldate, PDO::PARAM_STR);
						$query->execute(); 
						$list_length = $query->rowCount();
						$result = $query->fetch(PDO::FETCH_OBJ);

						if ($list_length > 0) {
							$standard_total_single = $result->amount;
						}
						else {
							$standard_total_single = $standard_total;
						}

						$available_total = $standard_total_single - $total_small;
						$available_total_disp = number_format($available_total, 0, ',', ' ');

						// also check capacity for mornings
	  					$total1 = 0;
						$time1 = "09:00:01";

						$query = $db->prepare("SELECT * FROM `order` WHERE `date` = :datum AND `status` < 5 ORDER BY `time` ASC");
						$query->bindParam(":datum", $fulldate, PDO::PARAM_STR);
						$query->execute(); 
						foreach ($query as $row) {
							$type1 = $row['type1'];
							$type3 = $row['type3'];
							$amount = getAmount(amount_decrypt($row['amount'], $key2), $type3, $modus);
							$project_id = $row['project_id'];
							$time_order = $row['time'];
							$name = $row['name'];

							if (($type1 < 4 AND $project_id == 0) AND ($time_order < $time1)) {
								$total1 += $amount;
						    }	
						}

						$query = $db->prepare("SELECT * FROM `amounts` WHERE `datum` = :datum AND `type` = 1 AND `timespan` = 2 ORDER BY `edited` DESC LIMIT 1");
						$query->bindParam(":datum", $fulldate, PDO::PARAM_STR);
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


						echo '<td class="normal" id="datebutton'.$k.'"><label class="radio-inline"><input type="radio" id="datum'.$k.'" name="date" onclick="dateFunction('.$k.', 1, '.$available_total.')" value="'.$fulldate.'" '.$checked.'> '.$days[$day].'<br><b>'.$date.'</b>';
					
						echo "<br>";

						if ($available_total > 500) {
							echo '<span class="badge" style="background-color: #5cb85c;font-size: 14px;">'.$available_total_disp.'</span>&nbsp;&nbsp;';

							if ($available_1 > 500) {
								echo '<span class="badge" style="background-color: #5cb85c;font-size: 14px;">R</span>';
							}
							elseif ($available_1 > 0) {
								echo '<span class="badge" style="background-color: #ff9c2e;font-size: 14px;">R</span>';
							}
							else {
								echo '<span class="badge" style="background-color: red;font-size: 14px;">R</span>';
							}
						}
						elseif ($available_total > 0) {
							echo '<span class="badge" style="background-color: #ff9c2e;font-size: 14px;">'.$available_total_disp.'</span>&nbsp;&nbsp;';

							if ($available_1 > 500) {
								echo '<span class="badge" style="background-color: #5cb85c;font-size: 14px;">R</span>';
							}
							elseif ($available_1 > 0) {
								echo '<span class="badge" style="background-color: #ff9c2e;font-size: 14px;">R</span>';
							}
							else {
								echo '<span class="badge" style="background-color: red;font-size: 14px;">R</span>';
							}
						}
						else {
							echo '<span class="badge" style="background-color: red;font-size: 14px;">Megtelt</span>';
						}
						
						echo "<br><i>".$total_small_disp." m&sup2</i>";
						echo "</label></td>";


						$time->add(new DateInterval('P1D'));
						$k++;
					}
					echo "</tr>";
				}

			echo "</table></div>";
			

				if ($other_datum == 1) {
					$other_check = "checked";
					$show = "show";
				}
				else {
					$other_check = "";
					$show = "hide";
				}

				?>
			<div class="col-md-1"></div>
			<div class="col-md-2">
				<input type="radio" name="date" id="more" onclick="moreFunction()" value="100" <?php echo $other_check; ?>> Egyedi dátum<br><br>
				<div id="orderdate" class="<?php echo $show; ?>">
					<input type='date' class="form-control date-field" style="padding: 0px 10px;" id="moreDatum" onchange="moreRefresh(1)" name="moreDatum" min="<?php echo $today; ?>" value="<?php echo $datum; ?>"><br>
				</div>

		</div>

	</div>

	<div class="row modal_row">
		<div class="col-md-12">
			<label for="date">Teljesítési időpont<sup>*</sup></label>
			<div id="time-output">
				<?php
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

					if ($time_original == $ordertime) {
						$checked_time1 = "checked";
						$class1 = 'selected';
						$selected_time = $n;
					}
					else {
						$checked_time1 = "";
						$class1 = "normal";
					}

					if ($time_original == $ordertime2) {
						$checked_time2 = "checked";
						$class2 = 'selected';
						$selected_time = $n + 1;
					}
					else {
						$checked_time2 = "";
						$class2 = "normal";
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
						echo '<td class="'.$class1.'" id="timebutton'.$n.'">';
					}
					else {
						echo '<td class="'.$class1.'" style="display: none;" id="timebutton'.$n.'">';
					}
						echo '<label class="radio-inline"><input type="radio" name="time" onclick="timeFunction('.$n.')" value="'.$ordertime.'" '.$checked_time1.'> '.$i.':00<br>';
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
							echo '<td class="'.$class2.'" id="timebutton'.$n.'">';
						}
						else {
							echo '<td class="'.$class2.'" style="display: none;" id="timebutton'.$n.'">';
						}
						echo '<label class="radio-inline"><input type="radio" name="time" onclick="timeFunction('.$n.')" value="'.$ordertime2.'" '.$checked_time2.'> '.$i.':30<br>';
						echo "<i>".$total." m&sup2;</i></label></td>";
						$n++;
					}
				}	


				for ($i=12; $i < 18; $i++) { 
					$ordertime = $i.":00:00";

					if ($time_original == $ordertime) {
						$checked_time = "checked";
						$class = 'selected';
						$selected_time = $n;
					}
					else {
						$checked_time = "";
						$class = "normal";
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
					
					echo '<td class="'.$class.'" id="timebutton'.$n.'"><label class="radio-inline"><input type="radio" name="time" onclick="timeFunction('.$n.')" value="'.$ordertime.'" '.$checked_time.'> '.$i.':00<br>';
					echo "<i>".$total." m&sup2;</i></label></td>";
					$n++;
				}

				echo '</tr>';

				$total_small = 0;					
				//get total amount of orders for each day     
				$query = $db->prepare("SELECT * FROM `order` WHERE `date` = :datum AND `status` < 5 ORDER BY `time` ASC");
				$query->bindParam(":datum", $datum, PDO::PARAM_STR);
				$query->execute(); 
				foreach ($query as $row) {
					$type1 = $row['type1'];
					$type3 = $row['type3'];
					$amount = getAmount(amount_decrypt($row['amount'], $key2), $type3, $modus);	
					$project_id = $row['project_id'];
					$name = $row['name'];

					if ($type1 < 4 AND $project_id == 0) {
				    	$total_small += $amount;
				    }
				}

				$total_small_disp = number_format($total_small, 0, ',', ' ');
				

				// check capacity - see if it differs from standard
				$query = $db->prepare("SELECT * FROM `amounts` WHERE `datum` = :datum AND `type` = 1 AND `timespan` = 1 ORDER BY `edited` DESC LIMIT 1");
				$query->bindParam(":datum", $datum, PDO::PARAM_STR);
				$query->execute(); 
				$list_length = $query->rowCount();
				$result = $query->fetch(PDO::FETCH_OBJ);

				if ($list_length > 0) {
					$standard_total_single = $result->amount;
				}
				else {
					$standard_total_single = $standard_total;
				}

				$available_total = $standard_total_single - $total_small + $amount_original;
				$available_total_disp = number_format($available_total, 0, ',', ' ');



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
					echo '<tr><td class="normal2" colspan="4"><b><i>'.$total1."  m&sup2;</i></b>";
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
				echo "<input type='hidden' value='".($available_1 + $amount_original)."' id='availability_1'>";

				
				?>

			</div>
		</div>
	</div>

	<?php
	// needed for JS
	echo "<input type='hidden' value='".$selected_time."' id='selectedtime'>";
	
	}
	?>


	<div class="row modal_row">
		<div class="col-md-2">
			<div class="form-group">
	      		<label for="amount">Mennyiség (m&sup2;)</label>
	      		<?php
	      			$amount_disp = intval($amount_original);
		        	if ($status < 4 OR $status == 5) {
						echo '<input class="form-control" type="number" step="1" min="0" value="'.$amount_disp.'" name="amount">';
					}
					else {
						echo '<p class="form-control-static" style="padding-left: 15px;">'.$amount_disp.'</p>';
						echo '<input type="hidden" name="amount" value="'.$amount_disp.'">';
					}
					?>	          		
	        </div>
	    </div>
	    <div class="col-md-1"></div>
	    <div class="col-md-7">
	    	<label class="radio-inline">
			<?php
			//if ($status < 4 OR $status == 5) {
			  	if ($type1_original == 1) {
			  		echo '<input type="radio" name="type1" value="1" checked> Kistekercs';
			  	} else {
			  		echo '<input type="radio" name="type1" value="1"> Kistekercs';
			  	}
			  	?>
			</label>
			<label class="radio-inline">
			  	<?php
			  	if ($type1_original == 2) {
			  		echo '<input type="radio" name="type1" value="2" checked> Kistekercs stadion';
			  	} else {
			  		echo '<input type="radio" name="type1" value="2"> Kistekercs stadion';
			  	}
			  	?>
			</label>
			<label class="radio-inline">
			  	<?php
			  	if ($type1_original == 3) {
			  		echo '<input type="radio" name="type1" value="3" checked> Kistekercs 2,5 cm';
			  	} else {
			  		echo '<input type="radio" name="type1" value="3"> Kistekercs 2,5 cm';
			  	}
			  	?>
			</label>
			<br>
			<label class="radio-inline">
			  	<?php
			  	if ($type1_original == 4) {
			  		echo '<input type="radio" name="type1" value="4" checked> Nagytekercs';
			  	} else {
			  		echo '<input type="radio" name="type1" value="4"> Nagytekercs';
			  	}
			  	?>
			</label>
			<label class="radio-inline">
			  	<?php
			  	if ($type1_original == 5) {
			  		echo '<input type="radio" name="type1" value="5" checked> Nagytekercs 2,5 cm';
			  	} else {
			  		echo '<input type="radio" name="type1" value="5"> Nagytekercs 2,5 cm';
			  	}
			  	?>

			</label>
			<label class="radio-inline">
			  	<?php
			  	if ($type1_original == 6) {
			  		echo '<input type="radio" name="type1" value="6" checked> Nagytekercs 3 cm';
			  	} else {
			  		echo '<input type="radio" name="type1" value="6"> Nagytekercs 3 cm';
			  	}
			  	?>
			</label>
			<br><br>

			<label class="radio-inline">
			  	<?php
			  	if ($type2 == 1) {
			  		echo '<input type="radio" name="type2" value="1" checked> Nórmal (Poa)';
			  	} else {
			  		echo '<input type="radio" name="type2" value="1"> Nórmal (Poa)';
			  	}
			  	?>
			</label>
			<label class="radio-inline">	
			  	<?php
			  	if ($type2 == 2) {
			  		echo '<input type="radio" name="type2" value="2" checked> Mediterrán';
			  	} else {
			  		echo '<input type="radio" name="type2" value="2"> Mediterrán';
			  	}
			  	?>
			</label>
			<br><br>

			<?php
			if ($cutting_modus == 2) {
				?>

				<label class="radio-inline">
				  	<?php
				  	if ($pallet == 1) {
				  		echo '<input type="radio" name="pallet" value="1" checked> 50';
				  	} else {
				  		echo '<input type="radio" name="pallet" value="1"> 50';
				  	}
				  	?>
				</label>
				<label class="radio-inline">
				  	<?php
				  	if ($pallet == 2) {
				  		echo '<input type="radio" name="pallet" value="2" checked> 30';
				  	} else {
				  		echo '<input type="radio" name="pallet" value="2"> 30';
				  	}
				  	?>
				</label>
				<label class="radio-inline">
				  	<?php
				  	if ($pallet == 3) {
				  		echo '<input type="radio" name="pallet" value="3" checked> 56';
				  	} else {
				  		echo '<input type="radio" name="pallet" value="3"> 56';
				  	}
				  	?>
				</label>

				<br><br>

				<?php
					}
				?>

			<?php
			if ($modus == 1) {
			?>
				<label class="radio-inline">
				  	<?php
				  	if ($type3_original == 1) {
				  		echo '<input type="radio" name="type3" value="1" checked> I';
				  	} else {
				  		echo '<input type="radio" name="type3" value="1"> I';
				  	}
				  	?>
				</label>
				<label class="radio-inline">
				  	<?php
				  	if ($type3_original == 2) {
				  		echo '<input type="radio" name="type3" value="2" checked> II';
				  	} else {
				  		echo '<input type="radio" name="type3" value="2"> II';
				  	}
				  	?>
				</label>
				<label class="radio-inline">
				  	<?php
				  	if ($type3_original == 3) {
				  		echo '<input type="radio" name="type3" value="3" checked> Garancia';
				  	} else {
				  		echo '<input type="radio" name="type3" value="3"> Garancia';
				  	}
				  	?>
				</label>
			<?php
			}
			else {		// mode 2
				echo '<input name="type3" value="'.$type3_original.'" type="hidden"/>';
			}
			?>

	    </div>
	    <div class="col-md-2">
	    	<div class="form-group">
	      		<label for="field">Terület</label>
		        <?php

				$query = $db->prepare("SELECT * FROM fields WHERE `id` = :id");
				$query->bindParam(":id", $field, PDO::PARAM_STR);
	            $query->execute();
	            $result = $query->fetch(PDO::FETCH_OBJ);

	            if ($field == 111111) {
					$lastfieldname = "?";
				}
				elseif ($field == 222222) {
					$lastfieldname = "ZEHETBAUER";
				}
				else {
					$lastfieldname = $result->name;
				}

	            echo '<select class="form-control" name="field">';
	            echo '<option value="'.$field.'" selected>';
	            echo $lastfieldname;
	            echo "</option>";
	            echo '<option value="111111">?</option>';
	            //echo '<option value="222222">ZEHETBAUER</option>';
	 
	            $query = $db->prepare("SELECT * FROM fields WHERE `complete` < 1 AND `cutting` = 1");
	            $query->execute();
	            while($row = $query->fetch()) {
	                if ($field != $row['id']) {
	                	echo "<option value='".$row['id']."'>".$row['name']."</option>";
	                }
	            }

	            echo '</select>';
		        ?>
			</div>
	    </div>
	</div>

	<div class="row modal_row">
		
		<div class="col-md-2">
			<label for="delivery">Szállítás</label><br>
			<?php
		  	if ($delivery == 1) {
		  		?>
		  		<label class="radio-inline">
				  	<input type="radio" name="delivery" onclick="deliveryFunction2()" value="1" checked> ABH
				</label>
				<label class="radio-inline">
				  	<input type="radio" name="delivery" onclick="deliveryFunction()" value="2"> Szállítva
				</label>
		    </div>
		    	<?php
		  	} else {
		  		?>
		  		<label class="radio-inline">
				  	<input type="radio" name="delivery" onclick="deliveryFunction2()" value="1"> ABH
				</label>
				<label class="radio-inline">
				  	<input type="radio" name="delivery" onclick="deliveryFunction()" value="2" checked> Szállítva
				</label>
		    </div>
		    <?php
		  	}
		  	?>

		    <div class="col-md-6" style="padding-bottom: 0;">
				<div class="form-horizontal">
					<br>
					<div class="form-group">
						<label for="deliveryname" class="col-sm-4 control-label" style="padding-bottom: 10px;">Szállítási név</label>
						<div class="col-sm-6" style="padding-bottom: 10px;">
						  <input type="text" class="form-control" name="deliveryname" id="deliveryname" value="<?echo $deliveryname;?>">
						</div>
					</div>

					<div class="form-group">
		          		<label for="country1" class="col-sm-4 control-label">Ország</label>
		          		<div class="col-sm-4" style="padding-bottom: 10px;">
					        <?php

		    				$query = $db->prepare("SELECT * FROM countries WHERE `id` = :id");
		    				$query->bindParam(":id", $country, PDO::PARAM_STR);
				            $query->execute();
				            $result = $query->fetch(PDO::FETCH_OBJ);
		    				$countryname = $result->name2;

		    				if ($country > 0 AND $city == 0) {
				            	echo '<select class="form-control" id="country1" name="country1" onchange="countryFunction2()">';
				            }
				            else {
				            	echo '<select class="form-control" id="country1" name="country1" onchange="countryFunction2()">';
				            }
				            echo '<option value="'.$country.'" selected>';
				            echo $countryname;
				            echo "</option>";
				 
				            $query = $db->prepare("SELECT * FROM countries WHERE (`type` = 1 OR `type` = 3) ORDER BY `name2` ASC");
				            $query->execute();
				            while($row = $query->fetch()) {
				                if ($country != $row['id']) {
				                	echo "<option value='".$row['id']."'>".$row['name2']."</option>";
				                }
				            }

				            echo '</select>';
					        ?>

					    </div>
	        		</div>

					<div class="form-group">
						<label for="deliveryaddress" class="col-sm-4 control-label" style="padding-bottom: 10px;">Szállítási cím</label>
						<div class="col-sm-6" style="padding-bottom: 10px;">
						  <input type="text" class="form-control" name="deliveryaddress" id="deliveryaddress" value="<?echo $deliveryaddress;?>">
						</div>
					</div>

					<?php
					if ($country == 0 AND $city > 0) {
						echo '<div id="plz-group" class="form-group show">';
					}
					else {
						echo '<div id="plz-group" class="form-group hide">';
					}
					?>
			          		<label for="customer" class="col-sm-4 control-label" style="padding-bottom: 10px;">&nbsp;</label>
			          		<div class="col-sm-6" style="padding-bottom: 10px;">
			          			<input class="form-control" id="plz_input" type="text" name="plz" value="<?echo $plz." ".$city_disp;?>"/>
			          		</div>

	            			<input name="city_id" id="city_id" value="<?php echo $city; ?>" type="hidden"/>
			        	</div>

			        <?php
					if ($project == 0) { 	// enter licence number not for projects
					?>
				        <div class="form-group">
							<label for="time" class="col-sm-4 control-label" style="padding-bottom: 10px;">Rendszám</label>
				          	<div class="col-sm-4" style="padding-bottom: 10px;">
				          		<input type="text" class="form-control" name="licence" id="licence" value="<?echo $licence?>"/>
				        	</div>
						</div>
					<?php
					}
					?>
					
				</div>
		    </div>

		    <div class="col-md-1"></div>

		    <?php
		  	if ($delivery == 1) {
		  		echo ' <div id="deliveryAgent" class="col-md-3 hide">';
		  	}
		  	elseif ($delivery == 2) {
		  		echo ' <div id="deliveryAgent" class="col-md-3 show">';
		  	}
		  	?>

				<div class="col-md-6">
		    	<br><label for="forwarder">Fuvaros</label><br>

		    	<?php
		    	$k = 1;
		    	$query = $db->prepare("SELECT * FROM forwarder WHERE `status` = 1");
	            $query->execute();
	            while($row = $query->fetch()) {
	                echo '<div class="radio"><label>';
	                if ($row['id'] == $forwarder) {
	                	echo '<input type="radio" name="forwarder" onclick="forwarderFunction('.$row['id'].')" value="'.$row['id'].'" checked>'.$row['name']."</label></div>";
	                } else {
	                	echo '<input type="radio" name="forwarder" onclick="forwarderFunction('.$row['id'].')" value="'.$row['id'].'">'.$row['name']."</label></div>";
	                }

	                if ($k == 6) {
	                	echo '</div><br><br><div class="col-md-6">';
	                }
	                $k++;
	                
	            }
		    	?>
		    </div>
		   
	    </div>
	</div>


	<div class="row modal_row">
		
		<div class="col-md-3">
			<label for="payment">Fizetési mód</label><br>
			<label class="radio-inline">
			  	<?php
			  	if ($payment == 1) {
			  		echo '<input type="radio" name="payment" value="1" checked> Kézpénz';
			  	} else {
			  		echo '<input type="radio" name="payment" value="1"> Kézpénz';
			  	}
			  	?>

			</label>
			<label class="radio-inline">
			  	<?php
			  	if ($payment == 2) {
			  		echo '<input type="radio" name="payment" value="2" checked> Átutalás';
			  	} else {
			  		echo '<input type="radio" name="payment" value="2"> Átutalás';
			  	}
			  	?>
			</label>

			<br><br><br>
			<div class="form-horizontal">
				<div class="form-group">
					<label for="invoicenumber" class="col-sm-3 control-label" style="padding-bottom: 10px;">Sz.sz.: </label>
					<div class="col-sm-6" style="padding-bottom: 10px;">
					  <input type="number" class="form-control" name="invoicenumber" step="1" min="0" value="<?echo $invoicenumber;?>">
					</div>
				</div>
			</div>
	    </div>

	</div>

	<div class="row modal_row_last">
        <div class="col-md-3 formrow" style="padding-bottom: 10px;">
	        <div class="form-group">
	            <label for="exampleTextarea">Jegyzet (kint)</label>
	            <textarea class="form-control" name="note" id="note" rows="3"><?echo $note;?></textarea>
	        </div>
	        <button type="button" class="btn btn-default" onclick="noteRefresh2(1)">még visz</button>
	        <button type="button" class="btn btn-default" onclick="noteRefresh2(2)">folytatás</button>
        </div>

	    <div class="col-md-3 formrow" style="padding-bottom: 10px;">
	        <div class="form-group">
	            <label for="exampleTextarea">Jegyzet (iroda)</label>
	            <textarea class="form-control" name="note2" id="note2" rows="3"><?echo $note2;?></textarea>
	        </div>
	        <button type="button" class="btn btn-default" onclick="noteRefresh()">Ugyanaz, mint kint</button>

	    </div>
	    <div class="col-md-1"></div>
	    
	</div>

	<input type="hidden" name="id" value="<?echo $edit;?>">
	<input type="hidden" name="sort" value="<?echo $sort;?>">
	<input type="hidden" name="olddatum" value="<?echo $datum;?>">
	<input type="hidden" name="oldtime" value="<?echo $timedisplay;?>">
	<input type="hidden" name="oldplanneddate" value="<?echo $planneddate;?>">
	<input type="hidden" name="oldnote" value="<?echo $note;?>">	
	<input type="hidden" name="oldnote2" value="<?echo $note2;?>">
	<input type="hidden" name="customer_id" value="<?echo $customer_id;?>">
	<input type="hidden" name="edit_link2" value="<?echo $edit_link2;?>">

	<br><br>
	<button type="submit" class="btn btn-primary edit-btn" name="editOrderForm" value="Submit">Küldés</button>
	
	<?php  
	   	
	
	if ($status < 2 OR $login == 1) {
		echo '<button type="submit" class="btn btn-danger btn-sm delete-btn" name="deleteOrderForm" onclick="return ConfirmDelete();" value="Submit">Törlés (egész megrendelés)</button>';
	}

	if ($id2 > 0 AND $status < 4) {
		echo '<button type="submit" class="btn btn-danger btn-sm delete-btn" onclick="deleteId2('.$edit.');" value="Submit">Sorszám törlés ('.$prefix.'-'.$id2.')</button>';
	}
	if ($id3 > 0 AND $status < 4) {
		echo '<button type="submit" class="btn btn-danger btn-sm delete-btn" onclick="deleteId3('.$edit.');" value="Submit">Sorszám törlés ('.$id3_display.')</button>';
	}
	if ($status == 0) {		// show right control to put on/off hold
		echo '<button class="btn btn-complete btn-sm delete-btn paused_btn" style="margin-right: 5px;" onclick="statusFunction('.$edit.', 1, 1)"><span class="glyphicon glyphicon-time"></span></button>';
	}
	elseif ($status == 1) {
		echo '<button class="btn btn-complete btn-sm delete-btn" style="margin-right: 5px;" onclick="statusFunction('.$edit.', 0, 1)"><span class="glyphicon glyphicon-time"></span></button>';
	}

		?>
	</div>
	</form>

	<br><br><br><br><br><br><br><br><br><br><br><br><br><br>
<?php
}
?>


 