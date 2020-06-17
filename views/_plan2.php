<?php 
/////////////////////////////////////////////
// Cutting list of 2 working days from now //
/////////////////////////////////////////////

$wide = 1;		// other CSS for better overview

include('views/_header'.$header.'.php');
include('tools/functions.php');

require_once('config/config.php');
$db = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.'', DB_USER, DB_PASS);
$db -> exec("set names utf8");
date_default_timezone_set('Europe/Budapest');

$nextday = date('Y-m-d', strtotime('tomorrow'));
$day_tomorrow = date('w', strtotime($nextday));

if ($day_tomorrow == 6) {		// Saturday
	$nextday = date('Y-m-d', strtotime($nextday.' +3 days'));		// Tuesday
}
elseif ($day_tomorrow == 0) {	// Sunday
	$nextday = date('Y-m-d', strtotime($nextday.' +2 days'));		// Tuesday
}
else {							// Any other day
	$nextday = date('Y-m-d', strtotime($nextday.' +1 days'));		// two days from now
}

$day = date('w', strtotime($nextday));
$dayHeading = $days[$day];
$dayHeading2 = $days[$day];
$datum = $nextday;
$check = 3;

$nextday_midnight = $nextday." 00:00:00";
$after_midnight = date('Y-m-d', strtotime($nextday.' +1 day'))." 00:00:00";


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

	echo '<div class="alert alert-success center-block" role="alert">Terület modosítás sikerült!</div>';
}

?>

<div class="inputform">

  	<div class="row">
	    <div class="col-md-8">
	      <h3 style="margin-top:10px;">Kistekercs vágás tervezés (+2) - <?echo $dayHeading;?></h3>  
	    </div>
	</div>

	<?php
	echo '<table class="table table-condensed centertext"><tr class="greyBG"><td></td>';
	$text = "<tr><td class='border'><b>Összes</b></td>";
	$text1 = "<tr><td class='border'>Gép 1</td>";
	$text2 = "<tr><td class='border'>Gép 2</td>";


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
		$query = $db->prepare("SELECT * FROM `order` WHERE `planneddate` = :planneddate AND `type1` < 4 AND `project_id` = 0 AND `status` < 3 ORDER BY `time` ASC");
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
		$query = $db->prepare("SELECT * FROM `order` WHERE `planneddate` = :planneddate AND `type1` < 4 AND `project_id` = 0 AND `status` < 3 ORDER BY `time` ASC");
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
		$planneddate = $nextday." ".$i.":00:00";

		//get total amount of orders for each time of a given day     
		$total = 0;
		$total1 = 0;
		$total2 = 0;
		$query = $db->prepare("SELECT * FROM `order` WHERE `planneddate` = :planneddate AND `type1` < 4 AND `project_id` = 0 AND `status` < 3 ORDER BY `time` ASC");
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
			$query = $db->prepare("SELECT * FROM `order` WHERE planneddate >= :tomorrow AND planneddate < :aftertomorrow AND `type1` < 4 AND `project_id` = 0 AND `status` < 2 AND `team` = 1");
			$query->bindParam(":tomorrow", $nextday_midnight, PDO::PARAM_STR);
			$query->bindParam(":aftertomorrow", $after_midnight, PDO::PARAM_STR);
			$query->execute(); 
			foreach ($query as $row) {
				$amount = amount_decrypt($row['amount'], $key2);
				$total += $amount;
			}
			$total = number_format($total, 0, ',', ' ');
			?>

			<div class="panel panel-primary">
				<div class="panel-heading"><h4><span class="glyphicon glyphicon-edit"></span>&nbsp;&nbsp;1. Gép</h4> </div>

				<?php

				echo "<table class='table' id='table-1'>";
				echo '<tr class="nodrop nodrag"><td colspan="8">'.$dayHeading2.",&nbsp;".$datum.'</td><td><i>'.$total.' m&sup2;</i></td></tr>';
				echo "<tr class='title nodrop nodrag'><td>Sz.</td><td>Teljesítési<br>időpont</td><td></td><td>Vevő</td><td>m&sup2;</td><td>Tipus</td><td>Terület</td><td><span class='glyphicon glyphicon-comment'></span></td><td></td></tr>";

				//get open orders of the day of team 1     
				$query = $db->prepare("SELECT * FROM `order` WHERE planneddate >= :tomorrow AND planneddate < :aftertomorrow AND `status` < 2 AND `team` = 1 AND `type1` < 4 AND `project_id` = 0 ORDER BY `sort` ASC");
				$query->bindParam(":tomorrow", $nextday_midnight, PDO::PARAM_STR);
				$query->bindParam(":aftertomorrow", $after_midnight, PDO::PARAM_STR);
				$query->execute(); 

				$i = 1;

				if ($query->rowCount() > 0) {

					$changeTeam = "changeTeam2";
					$team = 2;
					$changeLabel = "2. Gép";
					$now = 0;
					include('views/_plan_foreach.php');		// insert single rows
					?>
					
					<tr class="changeRow nodrop nodrag">
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
	    	$total = 0;
			//get open amount of team 2    
			$query = $db->prepare("SELECT * FROM `order` WHERE planneddate >= :tomorrow AND planneddate < :aftertomorrow AND `type1` < 4 AND `project_id` = 0 AND `status` < 2 AND `team` = 2");
			$query->bindParam(":tomorrow", $nextday_midnight, PDO::PARAM_STR);
			$query->bindParam(":aftertomorrow", $after_midnight, PDO::PARAM_STR);
			$query->execute(); 
			foreach ($query as $row) {
				$amount = amount_decrypt($row['amount'], $key2);
				$total += $amount;
			}
			$total = number_format($total, 0, ',', ' ');
			?>

			<div class="panel panel-primary">
				<div class="panel-heading"><h4><span class="glyphicon glyphicon-edit"></span>&nbsp;&nbsp;2. Gép</h4> </div>

				<?php

				echo "<table class='table' id='table-2'>";

				//get open orders of the day of team 2   
				$query = $db->prepare("SELECT * FROM `order` WHERE planneddate >= :tomorrow AND planneddate < :aftertomorrow AND `status` < 2 AND `team` = 2 AND `type1` < 4 AND `project_id` = 0 ORDER BY `sort` ASC");
				$query->bindParam(":tomorrow", $nextday_midnight, PDO::PARAM_STR);
				$query->bindParam(":aftertomorrow", $after_midnight, PDO::PARAM_STR);
				$query->execute(); 

				$start_i = $i;
				$previoustime = "00:00";

				if ($query->rowCount() > 0) {
					echo '<tr class="nodrop nodrag"><td colspan="8">'.$dayHeading2.",&nbsp;".$datum.'</td><td><i>'.$total.' m&sup2;</i></td></tr>';
					echo "<tr class='title nodrop nodrag'><td>Sz.</td><td>Teljesítési<br>időpont</td><td></td><td>Vevő</td><td>m&sup2;</td><td>Tipus</td><td>Terület</td><td><span class='glyphicon glyphicon-comment'></span></td><td></td></tr>";
					// <td>Tervezett<br>időpont</td>

					$changeTeam = "changeTeam";
					$changeLabel = "1. Gép";
					$team = 1;
					$now = 0;
					include('views/_plan_foreach.php');		// insert single rows
					?>
					
					<tr class="changeRow nodrop nodrag">
					<td colspan="6"></td>
					<td colspan="2">
						<input type="checkbox" id="checkAll2" onClick="toggle2(<?echo $i;?>, <?echo $start_i;?>)" /> Összes bejelölése<br><br>
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

  	</div>

</div>

  


