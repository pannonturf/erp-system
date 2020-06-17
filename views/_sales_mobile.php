<?php 
//////////////////////////////////
// Overview about orders mobile //
//////////////////////////////////

if (isset($_GET['edit'])) {
	$refresh = 0;
}
else {
	$refresh = 1;
}

$sales = 1;

include('views/_header'.$header.'.php');
include('tools/functions.php');
include('views/_edit_database.php'); // Update database when order is edited

require_once('config/config.php');
$db = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.'', DB_USER, DB_PASS);
$db -> exec("set names utf8");
date_default_timezone_set('Europe/Budapest');


$i = 1;

//Show buttons right for types         
if (isset($_GET['edit'])) {
	$edit = $_GET['edit'];
}
else {
	$edit = 0;
}

//Show buttons right for types         
if (isset($_GET['project'])) {
	$project = $_GET['project'];
}
else {
	$project = 0;
}

if ($project == 1) {
	$btn_type1 = "btn-success";
	$btn_type2 = "btn-default";
	$function = 2;
}
elseif ($project == 0) {
	$btn_type1 = "btn-default";
	$btn_type2 = "btn-success";
	$function = 1;
}


if ($project == 1) {
	include('views/_projekt_today.php');
}
elseif ($edit > 0) {
	include('views/_edit.php');
}
else {
?>


	<div class="inputform">

		<div class="row">
		    <div class="col-md-12">
		      
		    	<?php
				$today = date("Y-m-d");
				$datum = $today;
				//$days = array("v.", "h.", "k.", "sze.", "cs.", "p.", "szo.");
				$days = array("Vasárnap", "Hétfő", "Kedd", "Szerda", "Csütörtök", "Péntek", "Szombat");
				$day = date('w', strtotime($today));
				$dayHeading = $days[$day];
				$dayHeading2 = $days[$day];

				$check = 1;

				echo '<div class="panel panel-primary">';
				echo '<div class="panel-heading"><h4><span class="glyphicon glyphicon-th-list"></span>&nbsp;&nbsp;Mai megrendelések</h4></div>';
				echo "<table class='table'>";

				//get total amount of the day     
				$total_small = 0;   
				$total_big = 0;

				$query = $db->prepare("SELECT * FROM `order` WHERE `date` = :datum AND `status` < 5 ORDER BY `time` ASC, `id` ASC");
				$query->bindParam(":datum", $datum, PDO::PARAM_STR);
				$query->execute(); 

				foreach ($query as $row) {
					$type1 = $row['type1'];
					$project_id = $row['project_id'];
					$amount = amount_decrypt($row['amount'], $key2);
					if ($modus == 2) {	
						$type3 = $row['type3'];
						if ($type3 == 2) {
							$amount = $amount / 2;
						}	
					}
					
					if ($project_id == 0) {
						if ($type1 < 4) {
					    	$total_small += $amount;
					    }
					    else {
					    	$total_big += $amount;
					    }
					}
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

				echo '<tr><td colspan="2"><b>'.$dayHeading2.",&nbsp;".$datum.'</b></td><td style="text-align:right;" colspan="3"><i><b>'.$total_disp.'</b></i></td></tr>';

				// Projekt view today (truck today)
				$query = $db->prepare("SELECT * FROM `trucks` WHERE `datum` = :datum ORDER BY `sort` ASC");
				$query->bindParam(":datum", $datum, PDO::PARAM_STR);
				$query->execute();

				$border = "";
				if ($query->rowCount() > 0) {
					include('views/_listpoints_mobile_projekt.php');
					$border = "truck_lower";
				}

				include('views/_listpoints_mobile.php');
				?>
				</table>

				</div>
			</div>
		</div>

		<div class="row" style="margin-bottom:20px;">
			<div class="col-md-2">
				<button type="button" class="btn btn-info" onclick="document.location = 'today.php'">Mai vágás</button>
			</div>
		</div>

		<div class="row">
		    <div class="col-md-12">
		      
		    	<?php
				$nextday = date('Y-m-d', strtotime('tomorrow'));
				$day = date('w', strtotime($nextday));

				if ($day == 6) {
					$nextday = date('Y-m-d', strtotime($nextday.' +2 days'));
					$nextdayname = date('w', strtotime($nextday));
					$dayHeading = $days[$nextdayname];
					$dayHeading2 = $days[$nextdayname];
					$dayHeading3 = "Hétfői";
				}
				elseif ($day == 0) {
					$nextday = date('Y-m-d', strtotime($nextday.' +1 days'));
					$nextdayname = date('w', strtotime($nextday));
					$dayHeading = $days[$nextdayname];
					$dayHeading2 = $days[$nextdayname];
					$dayHeading3 = "Hétfői";
				}
				else {
					$dayHeading = "Holnap";
					$dayHeading2 = $days[$day];
					$dayHeading3 = "Holnapi";
				}

				$datum = $nextday;
				$check = 2;

				echo '<div class="panel panel-warning">';
				echo '<div class="panel-heading"><h4><span class="glyphicon glyphicon-th-list"></span>&nbsp;&nbsp;'.$dayHeading3.' megrendelések</h4></div>';
				echo "<table class='table'>";

				//get total amount of the day     
				$total_small = 0;   
				$total_big = 0;

				$query = $db->prepare("SELECT * FROM `order` WHERE `date` = :datum AND `status` < 5 ORDER BY `time` ASC, `id` ASC");
				$query->bindParam(":datum", $datum, PDO::PARAM_STR);
				$query->execute(); 

				foreach ($query as $row) {
					$type1 = $row['type1'];
					$project_id = $row['project_id'];
					$amount = amount_decrypt($row['amount'], $key2);
					if ($modus == 2) {	
						$type3 = $row['type3'];
						if ($type3 == 2) {
							$amount = $amount / 2;
						}	
					}
					
					if ($project_id == 0) {
						if ($type1 < 4) {
					    	$total_small += $amount;
					    }
					    else {
					    	$total_big += $amount;
					    }
					}
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

				echo '<tr><td colspan="2"><b>'.$dayHeading2.",&nbsp;".$datum.'</b></td><td style="text-align:right;" colspan="3"><i><b>'.$total_disp.'</b></i></td></tr>';

				// Projekt view today (truck today)
				$query = $db->prepare("SELECT * FROM `trucks` WHERE `datum` = :datum ORDER BY `sort` ASC");
				$query->bindParam(":datum", $datum, PDO::PARAM_STR);
				$query->execute();

				$border = "";
				if ($query->rowCount() > 0) {
					include('views/_listpoints_mobile_projekt.php');
					$border = "truck_lower";
				}

				include('views/_listpoints_mobile.php');

				echo "</table>";
				?>
				</div>
	    	</div>
	  	</div>


	  	<div class="row">
		    <div class="col-md-12">
		    	<div class="panel panel-default">
		        <!-- Default panel contents -->
		        <div class="panel-heading"><h4><span class="glyphicon glyphicon-th-list"></span>&nbsp;&nbsp;Tovabbi megrendelések</h4> </div>
				
				<table class='table'>
				      
		    	<?php

				for ($j=1; $j < 100; $j++) { 
					$nextday = date('Y-m-d', strtotime($nextday.' +1 day'));
					$day = date('w', strtotime($nextday));

					if ($day == 6) {
						$nextday = date('Y-m-d', strtotime($nextday.' +2 days'));
						$nextdayname = date('w', strtotime($nextday));
						$dayHeading = $days[$nextdayname];
						$dayHeading2 = $days[$nextdayname];
					}
					else {
						$dayHeading = $days[$day];
						$dayHeading2 = $days[$day];
					}

					$datum = $nextday;
					$check = 3;

					include('views/_listpoints_mobile.php');

				}
				
				echo "</table><br><br>";	
				?>

		      	</div>
		      	</div>
	    	</div>
	  	</div>

	</div>

	 <?php
}
?>