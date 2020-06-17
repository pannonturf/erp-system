<?php
/////////////////////////////////////////////////////
// Single rows of the order tables for mobile view //
///////////////////////////////////////////////////// 

// fewer information than on desktop version - only overview

//get total amount of the day     
$mobile = 1;
$total_small = 0;   
$total_big = 0;
$today = date("Y-m-d");
$stripe = 1;
$query = $db->prepare("SELECT * FROM `order` WHERE `date` = :datum AND `status` < 5 ORDER BY `time` ASC");
$query->bindParam(":datum", $datum, PDO::PARAM_STR);
$query->execute(); 

foreach ($query as $row) {
	$type1 = $row['type1'];
	$type3 = $row['type3'];
	$project_id = $row['project_id'];
	$amount = getAmount(amount_decrypt($row['amount'], $key2), $type3, $modus);

	if ($type1 < 4) {
    	$total_small += $amount;
    }
    elseif ($project_id == 0) {
    	$total_big += $amount;
    }
}
$total_small_disp = number_format($total_small, 0, ',', ' ');
$total_big_disp = number_format($total_big, 0, ',', ' ');

if ($total_big == 0) {
	$total_disp = $total_small_disp.' m&sup2';
}
else {
	$total_disp = $total_small_disp.' m&sup2<br>(+ '.$total_big_disp.' m&sup2 GR)';
}

// plus amount of projekt
$query2 = $db->prepare("SELECT * FROM `trucks` WHERE `datum` = :datum");
$query2->bindParam(":datum", $datum, PDO::PARAM_STR);
$query2->execute();
$truck_amount = $query2->rowCount();
foreach ($query2 as $row2) {
	$total_big += $row2['amount'];
}


$total_small_disp = number_format($total_small, 0, ',', ' ');
$total_big_disp = number_format($total_big, 0, ',', ' ');

if ($total_big == 0) {
	$total_disp = $total_small_disp.' m&sup2';
}
else {
	$total_disp = $total_small_disp.' m&sup2<br>(+ '.$total_big_disp.' m&sup2 GR)';
}

$query->bindParam(":datum", $datum, PDO::PARAM_STR);
$query->execute(); 
$count = $query->rowCount();
if ($check < 3) {
	$count = 1;
}

if ($count > 0) {

	if ($check > 2) {
		echo '<tr><td colspan="2" class="day_row_mobile">'.$dayHeading2.",&nbsp;".$datum.'</td>';
		if ($check == 3) {
			echo '<td style="text-align:right;" class="day_row_mobile" colspan="3"><i><b>'.$total_disp.'</b></i></td></tr>';
		}
		else {
			echo '<td colspan="4"></td></tr>';
		}
	}

	if ($query->rowCount() > 0) {
		foreach ($query as $row) {
			$id = $row['id'];
			$id2 = $row['id2'];
			$projectid = $row['project_id'];
			$prefix = $row['prefix'];

			$date = $row['date'];
			$time = $row['time'];
			$timedisplay = substr($time, 0, 5);
			$timedisplay2 = substr($time, 0, 5);
			$name = $row['name'];
			$type1 = $row['type1'];
			$type2 = $row['type2'];
			$type3 = $row['type3'];
			$amount = getAmount(amount_decrypt($row['amount'], $key2), $type3, $modus);
			$field = $row['field'];
			$delivery = $row['delivery'];
			$forwarder = $row['forwarder'];
			$payment = $row['payment'];
			$paid = $row['paid'];
			$status = $row['status'];
			$projectname = $row['projectname'];

			$deliveryname = $row['deliveryname'];
			$deliveryaddress = $row['deliveryaddress'];
			$country = $row['country'];
			$deliverytime = substr($row['deliverytime'], 0, 5);
			$telephone = $row['telephone'];
			$email = $row['email'];
			$invoicename = $row['invoicename'];
			$invoiceaddress = $row['invoiceaddress'];
			$invoicenumber = $row['invoicenumber'];
			$created = $row['created'];
			$creator = $row['creator'];
			$note = $row['note'];
			$note2 = $row['note2'];

			if ($note2 == "") {
				$note2_display = $note2;
			}
			else {
				$note2_display = '<mark style="background: #89ec7f;">'.$note2.'</mark>';
			}
			
			$planneddate = substr($row['planneddate'], 0, 16);
			$cutdate = substr($row['cutdate'], 0, 16);
			$completedate = substr($row['completedate'], 0, 16);
			$receivedate = substr($row['completedate'], 0, 16);
			$completer = $row['completer'];
			$receiver = $row['receiver'];
			$team = $row['team'];


			$check_project = 0;

			if ($projectid > 0 AND $check < 3) {
				// check if project is running today
				$query6 = $db->prepare("SELECT * FROM `trucks` WHERE `datum` = :datum");
				$query6->bindParam(":datum", $datum, PDO::PARAM_STR);
				$query6->execute();

				foreach ($query6 as $row2) {
					if ($projectid == $row2['project']) {
						$check_project = 1;
					}
				}
			}

			if ($check_project == 0) {
			
				$query = $db->prepare("SELECT * FROM customers WHERE `id` = :id");
				$query->bindParam(":id", $name, PDO::PARAM_STR);
				$query->execute();
				$result = $query->fetch(PDO::FETCH_OBJ);
				$customer_name = $result->name;

				//////////////
				// Projekt view future and normal order view
				if ($projectid > 0) {
					
					if ($stripe == 1) {
						echo '<tr class="stripe" data-href="project.php?id='.$projectid.'">';
					}
					else {
						echo '<tr data-href="project.php?id='.$projectid.'">';
					}
					
					echo '<td colspan="2"><mark style="background: #429741; color: white;">Projekt</mark><br>'.$customer_name.'</td>';
				}
				else {

					if ($status == 0) {
						echo '<tr class="paused2">';
					}
					elseif ($stripe == 1) {
						echo '<tr class="stripe">';
					}
					else {
						echo '<tr>';
					}

					if ($id2 > 0) {
						echo '<td><b><u>'.$prefix."-".$id2."</u></b><br>";
					}
					elseif ($check == 1 AND $type1 < 4 AND $login < 3) {
						echo '<td><button class="btn btn-default btn-xs" style="border-radius:10px;" onclick="statusFunction('.$id.', 1, 2)"><span class="glyphicon glyphicon-asterisk"></span></button>';
					}
					else {
						echo '<td>';
					}
					echo "<i>".$timedisplay."</i>";

					echo '</td>';	

					echo "<td>".$customer_name."</td>";	
				}				

				if ($type1 == 1) {
					$type1_display = "";
				}
				elseif ($type1 == 0) {
				 	$type1_display = "err1";
				}
				elseif ($type1 == 2) {
				 	$type1_display = "";
				}
				elseif ($type1 == 3) {
				 	$type1_display = "<mark style='background: #468dc9; color: white;'>2,5 cm</mark>";
				} 
				elseif ($type1 == 4) {
				 	$type1_display = "<mark style='background: #468dc9; color: white;'>GR</mark>";
				} 
				elseif ($type1 == 5) {
				 	$type1_display = "<mark style='background: #468dc9; color: white;'>GR 2,5 cm</mark>";
				} 
				elseif ($type1 == 6) {
				 	$type1_display = "<mark style='background: #468dc9; color: white;'>GR 3 cm</mark>";
				} 
				
				if ($type2 == 1) {
					$type2_display = "";
				}
				elseif ($type2 == 0) {
					$type2_display = "err2";
				}
				elseif ($type2 == 2) {
				 	$type2_display = "<mark style='background: #f62323; color: white;'>MED</mark>";
				} 

				echo '<td><b>'.$amount." m&sup2;</b> ".$type1_display." ".$type2_display."<br>";

				$query = $db->prepare("SELECT * FROM fields WHERE `id` = :id");
				$query->bindParam(":id", $field, PDO::PARAM_STR);
				$query->execute();
				$result = $query->fetch(PDO::FETCH_OBJ);

				if ($field == 111111) {
					$field_display = "?";
				}
				elseif ($field == 222222) {
					$field_display = "ZEHETBAUER";
				}
				else {
					$field_display = $result->name;
				}

				echo $field_display."</td>";
				
				if ($delivery == 1) {
					$delivery_display = "<u>ABH</u>";
				}
				elseif ($delivery == 2) {
					$query = $db->prepare("SELECT * FROM forwarder WHERE `id` = :id");
			        $query->bindParam(":id", $forwarder, PDO::PARAM_STR);
			        $query->execute();
			        $result = $query->fetch(PDO::FETCH_OBJ);
					$delivery_display = "<u>".$result->name."</u>";

					if ($forwarder == 1) {
						$delivery_display = "<mark style='background: #f62323; color: white;'>".$result->name."</mark>";
					}
				} 
				echo '<td>'.$delivery_display."<br>";

				echo '<i>'.$note_display."</i></td>";

				if ($status == 0) {
					$status_display = '<button class="btn btn-complete btn-sm paused_btn" onclick="statusFunction('.$id.', 1, 1)"><span class="glyphicon glyphicon-time"></span></button>';
				}
				elseif ($status == 1) {
				 	$status_display = '<img src="../img/yellow.png" class="picture_status">'; 
				} 
				elseif ($status == 2) {
				 	$status_display = '<img src="../img/orange.png" class="picture_status">'; 
				} 
				elseif ($status == 3) {
				 	$status_display = '<img src="../img/green.png" class="picture_status">'; 
				} 
				elseif ($status == 4) {
					 $status_display = '<span class="glyphicon glyphicon-ok"></span>'; 
				} 

				if ($login == 1) {
					echo '<td style="padding-top:5px;">'.$status_display;
					echo '<br><button type="button" data-href="sales.php?edit='.$id.'" class="btn btn-default btn-xs" style="float: right;"><span class="glyphicon glyphicon-pencil"></span></button>';
				}
				else {
					echo '<td style="padding-top:15px;">'.$status_display;
				}

				echo "</td></tr>";

				$i++;

				if ($stripe == 1) {
					$stripe = 0;
				}
				elseif ($stripe == 0) {
					$stripe = 1;
				}
				
			}
		}
	}
	else {
		echo "<tr><td colspan='5'>Nincs</td></tr>";
	}
}
?>	