<?php 
///////////////////////////
// Cutting list of today //
///////////////////////////

$wide = 1;		// other CSS for better overview

include('views/_header'.$header.'.php');
include('tools/functions.php');

require_once('config/config.php');
$db = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.'', DB_USER, DB_PASS);
$db -> exec("set names utf8");
date_default_timezone_set('Europe/Budapest');

$today = date("Y-m-d");
$today_midnight = date("Y-m-d")." 00:00:00";
$tomorrow_midnight = date('Y-m-d', strtotime('tomorrow'))." 00:00:00";
$yesterday_midnight = date('Y-m-d', strtotime('yesterday'))." 00:00:00";
$before_midnight = date('Y-m-d', strtotime($today.' -3 days'))." 00:00:00";

$check = 1;
$team = 1;


/////////////
//check if order from last days has not been finished
$query = $db->prepare("SELECT * FROM `order` WHERE planneddate >= :start AND planneddate < :end AND `status` < 2 AND `type1` < 4 AND `project_id` = 0 ORDER BY `sort` DESC");
$query->bindParam(":start", $before_midnight, PDO::PARAM_STR);
$query->bindParam(":end", $today_midnight, PDO::PARAM_STR);
$query->execute(); 

if ($query->rowCount() > 0) {
	// Update planneddate to today 7:00
	$query2 = $db->prepare("SELECT * FROM `order` WHERE planneddate >= :start AND planneddate < :end AND `status`< 2 AND `type1` < 4 AND `project_id` = 0 AND `team` = :team ORDER BY `sort` ASC LIMIT 1");
	$query2->bindParam(":start", $today_midnight, PDO::PARAM_STR);
	$query2->bindParam(":end", $tomorrow_midnight, PDO::PARAM_STR);
	$query2->bindParam(":team", $team, PDO::PARAM_STR);
	$query2->execute(); 
	$result = $query2->fetch(PDO::FETCH_OBJ);
	$current_sort = $result->sort;
	$sort = $current_sort - 1;

	foreach ($query as $row) {
		$id_old = $row['id'];
		$new_planneddate = $today." 07:00:00";

		$query4 = $db->prepare("UPDATE `order` SET `sort` = :sort, `planneddate` = :planneddate, `team` = 1 WHERE `id` = :id");
		$query4->bindParam(":sort", $sort, PDO::PARAM_STR); 
		$query4->bindParam(":planneddate", $new_planneddate, PDO::PARAM_STR); 
		$query4->bindParam(":id", $id_old, PDO::PARAM_STR);
		$query4->execute();
		
		$sort++;
	}
	echo "<script type='text/javascript'> document.location = 'today.php'; </script>";
}


/////////////
// if field is changed
if (isset($_POST['changeField'])) {
	$idArray = $_POST['id'];
	$newField = $_POST['newField'];

	foreach($idArray AS $id => $control) {

		if (isset($_POST['editField'.$id.''])) {
			//Update operations
			$sql = "UPDATE `order` SET `field` = :field WHERE `id` = :id";
			$query = $db->prepare($sql);

			$query->bindParam(":field", $newField, PDO::PARAM_STR);
			$query->bindParam(":id", $id, PDO::PARAM_STR);

			$query->execute();
		}
	}

	echo "<script type='text/javascript'> document.location = 'today.php'; </script>";
}

/////////////////////////////////
/////////////////////////////////

$days = array("Vasárnap", "Hétfő", "Kedd", "Szerda", "Csütörtök", "Péntek", "Szombat");

$today = date('Y-m-d');
$day = date('w', strtotime($today));

$tomorrow = date('Y-m-d', strtotime('tomorrow'));
$yesterday = date('Y-m-d', strtotime('yesterday'));

$dayHeading = "Ma";
$dayHeading2 = $days[$day];

$datum = $today;

//get total amounts of the day     
$total = 0;
$total_open = 0;
$total_finish = 0;
$query = $db->prepare("SELECT * FROM `order` WHERE planneddate >= :today AND planneddate < :tomorrow AND `type1` < 4 AND `project_id` = 0 AND `status` < 5 ORDER BY `time` ASC");
$query->bindParam(":today", $today_midnight, PDO::PARAM_STR);
$query->bindParam(":tomorrow", $tomorrow_midnight, PDO::PARAM_STR);
$query->execute(); 
foreach ($query as $row) {
	$amount = amount_decrypt($row['amount'], $key2);
	$total += $amount;
	$status = $row['status'];

	if ($status < 3) {
		$total_open += $amount;
	}
	else {
		$total_finish += $amount;
	}
}
$total = number_format($total, 0, ',', ' ');
$total_open = number_format($total_open, 0, ',', ' ');
$total_finish = number_format($total_finish, 0, ',', ' ');

?>

<div class="inputform">

  	<div class="row">
	    <div class="col-md-6">
	      <h3 style="margin-top:10px;">Mai kistekercs vágás</h3>  
	    </div>
	     <div class="col-md-2">
		    <div class="panel panel-default">
				<div class="panel-body">
					Összes: <?php echo $total; ?> m&sup2;
				</div>
			</div>
		</div>
	    <div class="col-md-2">
		    <div class="panel panel-default">
				<div class="panel-body">
					Befejezett: <?php echo $total_finish; ?> m&sup2;
				</div>
			</div>
		</div>
	    <div class="col-md-2">
	    	<div class="panel panel-default">
				<div class="panel-body">
					Nyított: <?php echo $total_open; ?> m&sup2;
				</div>
			</div>
		</div>
	</div>

	<?php
	echo '<table class="table table-condensed centertext"><tr class="greyBG"><td></td>';
	$text = "<tr><td class='border'><b>Összes</b></td>";
	$text1 = "<tr><td class='border'>Gép 1</td>";
	$text2 = "<tr><td class='border'>Gép 2</td>";

	$total = 0;
	$total1 = 0;
	$total2 = 0;

	for ($i=7; $i < 12; $i++) { 

		if ($i < 10) {
			$planneddate = $today." 0".$i.":00:00";
			$planneddate2 = $today." 0".$i.":30:00";
		}
		else {
			$planneddate = $today." ".$i.":00:00";
			$planneddate2 = $today." ".$i.":30:00";
		}

		//get total amount of orders for each time of a given day     
		$total = 0;
		$total1 = 0;
		$total2 = 0;
		$query = $db->prepare("SELECT * FROM `order` WHERE `planneddate` = :planneddate AND `type1` < 4 AND `project_id` = 0 AND `status` < 5 ORDER BY `time` ASC");
		$query->bindParam(":planneddate", $planneddate, PDO::PARAM_STR);
		$query->execute(); 
		foreach ($query as $row) {
			$team = $row['team'];
			$amount = amount_decrypt($row['amount'], $key2);
			$total += $amount;
			
			if ($team == 1) {
				$total1 += $amount;
			}
			elseif ($team == 2) {
				$total2 += $amount;
			}
		}

		echo '<td>'.$i.':00</td>';
		if ($total > 0) {
			$text .= "<td><b>".$total." m&sup2;</b></td>";
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
		$query = $db->prepare("SELECT * FROM `order` WHERE `planneddate` = :planneddate AND `type1` < 4 AND `project_id` = 0 AND `status` < 5 ORDER BY `time` ASC");
		$query->bindParam(":planneddate", $planneddate2, PDO::PARAM_STR);
		$query->execute(); 
		foreach ($query as $row) {
			$team = $row['team'];
			$amount = amount_decrypt($row['amount'], $key2);
			$total += $amount;
			
			if ($team == 1) {
				$total1 += $amount;
			}
			elseif ($team == 2) {
				$total2 += $amount;
			}
		}

		echo '<td>'.$i.':30</td>';

		if ($total > 0) {
			$text .= "<td><b>".$total." m&sup2;</b></td>";
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
		$planneddate = $today." ".$i.":00:00";

		//get total amount of orders for each time of a given day     
		$total = 0;
		$total1 = 0;
		$total2 = 0;
		$query = $db->prepare("SELECT * FROM `order` WHERE `planneddate` = :planneddate AND `type1` < 4 AND `project_id` = 0 AND `status` < 5 ORDER BY `time` ASC");
		$query->bindParam(":planneddate", $planneddate, PDO::PARAM_STR);
		$query->execute(); 
		foreach ($query as $row) {
			$team = $row['team'];
			$amount = amount_decrypt($row['amount'], $key2);
			$total += $amount;
			
			if ($team == 1) {
				$total1 += $amount;
			}
			elseif ($team == 2) {
				$total2 += $amount;
			}
		}
		
		echo '<td>'.$i.':00</td>';
		if ($total > 0) {
			$text .= "<td><b>".$total." m&sup2;</b></td>";
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
	?>
	<br><br>

	

  	<div class="row">
	    <div class="col-md-6">
	    	<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" accept-charset="utf-8">

	    	<?php

			//get open amount of team 1
			$total = 0;   
			$query = $db->prepare("SELECT * FROM `order` WHERE planneddate >= :today AND planneddate < :tomorrow AND `type1` < 4 AND `project_id` = 0 AND `status` < 3 AND `team` = 1");
			$query->bindParam(":today", $yesterday_midnight, PDO::PARAM_STR);
			$query->bindParam(":tomorrow", $tomorrow_midnight, PDO::PARAM_STR);
			$query->execute(); 
			foreach ($query as $row) {
				$amount = amount_decrypt($row['amount'], $key2);
				$total += $amount;
			}
			$total = number_format($total, 0, ',', ' ');
			?>


			<div class="panel panel-success">
				<div class="panel-heading"><h4><span class="glyphicon glyphicon-edit"></span>&nbsp;&nbsp;1. Gép</h4> </div>

				<?php
				echo "<table class='table' id='table-1'>";
				echo '<tr class="nodrop nodrag"><td colspan="8">'.$dayHeading2.",&nbsp;".$datum.'</td><td><i>'.$total.' m&sup2;</i></td></tr>';
				echo "<tr class='title nodrop nodrag'><td>Sz.</td><td>Teljesítési<br>időpont</td><td></td><td>Vevő</td><td>m&sup2;</td><td>Tipus</td><td>Terület</td><td style='width: 110px;'><span class='glyphicon glyphicon-comment'></span></td><td></td></tr>";
			
				//get order in progress (status = 2)    
				$query = $db->prepare("SELECT * FROM `order` WHERE planneddate >= :today AND planneddate < :tomorrow AND `status` = 2 AND `team` = 1 AND `type1` < 4 AND `project_id` = 0 ORDER BY `sort` ASC");
				$query->bindParam(":today", $today_midnight, PDO::PARAM_STR);
				$query->bindParam(":tomorrow", $tomorrow_midnight, PDO::PARAM_STR);	
				$query->execute(); 

				if ($query->rowCount() > 0) {
					$changeTeam = "changeTeam2";
					$changeLabel = "2. Gép";
					$team = 2;
					$nextday = $today;
					$now = 1;
					include('views/_plan_foreach.php');		// insert single rows
				}

				//get open orders of the day of team 1   
				$query = $db->prepare("SELECT * FROM `order` WHERE planneddate >= :today AND planneddate < :tomorrow AND `status` < 2 AND `team` = 1 AND `type1` < 4 AND `project_id` = 0 ORDER BY `sort` ASC");
				$query->bindParam(":today", $today_midnight, PDO::PARAM_STR);
				$query->bindParam(":tomorrow", $tomorrow_midnight, PDO::PARAM_STR);	
				$query->execute(); 

				$previoustime = "00:00";
				$i = 1;

				if ($query->rowCount() > 0) {

					$changeTeam = "changeTeam2";
					$changeLabel = "2. Gép";
					$team = 2;
					$nextday = $today;
					$now = 1;
					include('views/_plan_foreach.php');		// insert single rows

					?>
					
					<tr class="changeRow2 nodrop nodrag">
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
					echo "<tr class='nodrop nodrag'><td colspan='9'>Nincs</td></tr>";
				}
				?>
				</table>
			</div>
	      	
	      	</form>
    	</div>



	    <div class="col-md-6">
	      	<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" accept-charset="utf-8">
	    	<?php
			//get open amount of team 2 
			$total = 0;   
			$query = $db->prepare("SELECT * FROM `order` WHERE planneddate >= :today AND planneddate < :tomorrow AND `status` < 3 AND `type1` < 4 AND `project_id` = 0 AND `team` = 2");
			$query->bindParam(":today", $yesterday_midnight, PDO::PARAM_STR);
			$query->bindParam(":tomorrow", $tomorrow_midnight, PDO::PARAM_STR);
			$query->execute(); 
			foreach ($query as $row) {
				$amount = amount_decrypt($row['amount'], $key2);
				$total += $amount;
			}
			$total = number_format($total, 0, ',', ' ');
			?>

			<div class="panel panel-success">
				<div class="panel-heading"><h4><span class="glyphicon glyphicon-edit"></span>&nbsp;&nbsp;2. Gép</h4> </div>

				<?php

				echo "<table class='table' id='table-2'>";
				echo '<tr class="nodrop nodrag"><td colspan="8">'.$dayHeading2.",&nbsp;".$datum.'</td><td><i>'.$total.' m&sup2;</i></td></tr>';

				echo "<tr class='title nodrop nodrag'><td>Sz.</td><td>Teljesítési<br>időpont</td><td></td><td>Vevő</td><td>m&sup2;</td><td>Tipus</td><td>Terület</td><td style='width: 110px;'><span class='glyphicon glyphicon-comment'></span></td><td></td></tr>";


				//get order in progress    
				$query = $db->prepare("SELECT * FROM `order` WHERE planneddate >= :today AND planneddate < :tomorrow AND `status` = 2 AND `team` = 2 AND `type1` < 4 AND `project_id` = 0 ORDER BY `sort` ASC");
				$query->bindParam(":today", $today_midnight, PDO::PARAM_STR);
				$query->bindParam(":tomorrow", $tomorrow_midnight, PDO::PARAM_STR);	
				$query->execute(); 

				if ($query->rowCount() > 0) {
					$changeTeam = "changeTeam1";
					$changeLabel = "1. Gép";
					$team = 1;
					$nextday = $today;
					$now = 1;
					include('views/_plan_foreach.php');
				}


				//get orders of the day   
				$query = $db->prepare("SELECT * FROM `order` WHERE planneddate >= :today AND planneddate < :tomorrow AND `status` < 2 AND `team` = 2 AND `type1` < 4 AND `project_id` = 0 ORDER BY `sort` ASC");
				$query->bindParam(":today", $today_midnight, PDO::PARAM_STR);
				$query->bindParam(":tomorrow", $tomorrow_midnight, PDO::PARAM_STR);
				$query->execute(); 

				$start_i = $i;
				$previoustime = "00:00";

				if ($query->rowCount() > 0) {
					$changeTeam = "changeTeam";
					$changeLabel = "1. Gép";
					$team = 1;
					$nextday = $today;
					$now = 1;
					include('views/_plan_foreach.php');

					?>
					
					<tr class="changeRow2 nodrop nodrag">
					<td colspan="6"></td>
					<td colspan="2">
						<input type="checkbox" id="checkAll2" onClick="toggle2(<?echo $i;?>, <?echo $start_i;?>)" /> Összes bejelölése<br><br>
						<select class="form-control" name="newField" style="width: 150px;">
						<?php
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
					echo "<tr class='nodrop nodrag'><td colspan='9'>Nincs</td></tr>";
				}
				?>
				</table>
			</div>

	      	</form>
    	</div>
  	</div>
</div>

  


