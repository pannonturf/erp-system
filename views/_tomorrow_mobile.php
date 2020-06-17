<?php 
//////////////////////////////////////////////////
// Cutting list of tomorrow mobile (management) //
//////////////////////////////////////////////////

include('views/_header'.$header.'.php');
include('tools/functions.php');

require_once('config/config.php');
$db = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.'', DB_USER, DB_PASS);
$db -> exec("set names utf8");
date_default_timezone_set('Europe/Budapest');
?>


<div class="row">
    <div class="col-md-12">
      
    	<?php
		$today = date("Y-m-d");
		$days = array("Vasárnap", "Hétfő", "Kedd", "Szerda", "Csütörtök", "Péntek", "Szombat");

		$nextday = date('Y-m-d', strtotime('tomorrow'));
		$day = date('w', strtotime($nextday));

		if ($day == 6) {
			$nextday = date('Y-m-d', strtotime($nextday.' +2 days'));
			$nextdayname = date('w', strtotime($nextday));
			$dayHeading = $days[$nextdayname];
			$dayHeading2 = "Hétfői";
		}
		elseif ($day == 0) {
			$nextday = date('Y-m-d', strtotime($nextday.' +1 days'));
			$nextdayname = date('w', strtotime($nextday));
			$dayHeading = $days[$nextdayname];
			$dayHeading2 = "Hétfői";
		}
		else {
			$dayHeading = $days[$day];
			$dayHeading2 = "Holnapi";
		}

		$datum = $nextday;

		echo '<div class="panel panel-warning">';
		echo '<div class="panel-heading"><h4><span class="glyphicon glyphicon-th-list"></span>&nbsp;&nbsp;'.$dayHeading2.' megrendelések</h4></div>';
		echo "<table class='table'>";

		//get total amount of the day     
		$total = 0;
		$query = $db->prepare("SELECT * FROM `order` WHERE `date` = :datum AND `status` < 5 ORDER BY `time` ASC");
		$query->bindParam(":datum", $datum, PDO::PARAM_STR);
		$query->execute(); 

		foreach ($query as $row) {
			$amount = amount_decrypt($row['amount'], $key2);
			$total += $amount;
		}
		$total = number_format($total, 0, ',', ' ');

		echo '<tr><td colspan="3"><b>'.$dayHeading.",&nbsp;".$datum.'</b></td><td style="text-align:right;"><i><b>'.$total.' m&sup2;</b></i></td></tr>';

		$query->bindParam(":datum", $datum, PDO::PARAM_STR);
		$query->execute(); 

		if ($query->rowCount() > 0) {
			foreach ($query as $row) {
				$id = $row['id'];
				$id2 = $row['id2'];
				$date = $row['date'];
				$time = $row['time'];
				$timedisplay = substr($time, 0, 5);
				$name = $row['name'];
				$amount = amount_decrypt($row['amount'], $key2);
				$type1 = $row['type1'];
				$type2 = $row['type2'];
				$type3 = $row['type3'];
				$field = $row['field'];
				$delivery = $row['delivery'];
				$forwarder = $row['forwarder'];
				$status = $row['status'];
				$note = $row['note'];

				if ($note == "") {
					$note_display = $note;
				}
				else {
					$note_display = '<mark style="background: #89ec7f;">'.$note.'</mark>';
				}
				
				$team = $row['team'];

				echo '<tr>';
				if ($id2 > 0) {
					echo '<td><b><u>'.$id2."</u></b><br>";
				}
				else {
					echo '<td>';
				}
				echo "<i>".$timedisplay."</i>";

				echo '</td>';				

				$query = $db->prepare("SELECT * FROM customers WHERE `id` = :id");
				$query->bindParam(":id", $name, PDO::PARAM_STR);
				$query->execute();
				$result = $query->fetch(PDO::FETCH_OBJ);
				echo '<td>'.$result->name."<br>";
				
				if ($type2 == 1) {
					$type2_display = "";
				}
				elseif ($type2 == 0) {
					$type2_display = "err2";
				}
				elseif ($type2 == 2) {
				 	$type2_display = "<mark style='background: #f62323; color: white;'>MED</mark>";
				} 

				echo '<td><b>'.$amount." m&sup2;</b> ".$type2_display."<br>";

				$query = $db->prepare("SELECT * FROM fields WHERE `id` = :id");
				$query->bindParam(":id", $field, PDO::PARAM_STR);
				$query->execute();
				$result = $query->fetch(PDO::FETCH_OBJ);
				echo $result->name."</td>";
				
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

				echo '<td style="padding-top:15px;">'.$status_display;
				echo "</td></tr>";
				
			}
		}
		else {
			echo "<tr><td colspan='5'>Nincs</td></tr>";
		}
	
		?>
		</table>

		</div>
	</div>
</div>
