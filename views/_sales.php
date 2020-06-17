<?php 
///////////////////////////
// Overview about orders //
///////////////////////////

// stop autorefresh of page, when one entry is edited
if (isset($_GET['edit']) OR isset($_GET['note'])) {
	$refresh = 0;
}
else {
	$refresh = 1;
}

$sales = 1;
$edit_link = "sales.php?";

include('views/_header'.$header.'.php');

require_once('config/config.php');
$db = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.'', DB_USER, DB_PASS);
$db -> exec("set names utf8");
date_default_timezone_set('Europe/Budapest');

include('tools/functions.php');
include('views/_edit_database.php'); // Update database when order is edited


//Show edit view, if required        
if (isset($_GET['edit'])) {
	$edit = $_GET['edit'];
	$edit_link2 = "sales.php?";
	include('views/_edit.php');	// edit view
}
//Show deliverynote view, if required        
elseif (isset($_GET['note'])) {
	$order_id = $_GET['note'];
	$edit_link2 = "sales.php?";
	include('views/_deliverynote.php');	// edit view
}
else {		// show order lists
?>

	<div class="inputform">

		<?php
		//////////////////
		// OPEN ORDERS
		//////////////////

		$start = '2019-01-01';
		$lastday = $today;
		$i = 1;

		$query = $db->prepare("SELECT * FROM `order` WHERE `date` < :datum AND `date` > :start AND ((`status` = 4 AND `paid` = 0 AND (`payment` = 1 OR (`payment` = 2 AND `type3` = 2))) OR `status` < 4)  ORDER BY `time` ASC");
		//$query = $db->prepare("SELECT * FROM `order` WHERE `date` < :datum AND `date` > :start AND ((`status` = 4 AND `paid` = 0 AND `payment` = 1) OR `status` < 4)  ORDER BY `time` ASC");
		$query->bindParam(":datum", $today, PDO::PARAM_STR);
		$query->bindParam(":start", $start, PDO::PARAM_STR);
		$query->execute(); 

		$count = 0;
		foreach ($query as $row) {
			$name1 = $row['name'];
			$status = $row['status'];
			$query = $db->prepare("SELECT * FROM customers WHERE `id` = :id");
	        $query->bindParam(":id", $name1, PDO::PARAM_STR);
	        $query->execute();
	        $result = $query->fetch(PDO::FETCH_OBJ);
			$bulk = $result->bulk;

			if ($bulk == 0 OR ($bulk == 1 AND $status == 0)) {
				$count ++;
			}

		}

		if ($count > 0) {
			//$count = $query->rowCount();

		 	if (isset($_GET['open'])) {
		 		?>
		 		<div class="col-md-3">
		 			<button class="btn btn-danger active" type="button" onclick="document.location = 'sales.php'"><span class="glyphicon glyphicon-exclamation-sign"></span>&nbsp;&nbsp;Nyított megrendelések &nbsp; <span class="badge" style="background-color: red;color: white;"><?echo $count;?></span></button>
		 			</div>
		 		</div>

		 		<div class="col-md-2" id="showSum"></div>

		 		<div class="col-md-2">
		 		<?php
		 		if ($login == 1) {
		 			$query = $db->prepare("SELECT * FROM `customers` WHERE `checkdata` = 0 AND `status` = 1");
					$query->execute(); 
					if ($query->rowCount() > 0) {
						$count = $query->rowCount();

						echo '<button class="btn btn-danger"><span class="glyphicon glyphicon-exclamation-sign"></span>&nbsp;&nbsp;Check data &nbsp; <span class="badge" style="background-color: red;color: white;">'.$count.'</span></button>';
					}
		 		}
				?>
		 		</div>

		 		<div class="col-md-3"></div>
				<div class="col-md-2">	
					<?php
					if ($login == 1 OR $login == 2) {
						if ($cutting_modus == 2) {
			 				echo '<div class="checkbox"><label><input type="checkbox" onClick="switchMode(1)" checked> Vágás modus 2</label></div>';
			 			}
			 			elseif ($cutting_modus == 1) {
			 				echo '<div class="checkbox"><label><input type="checkbox" onClick="switchMode(2)"> Vágás modus 2</label></div>';
			 			}
			 		}
		 			?>
		 		</div>
		 		<?php
				
				echo '<div>';
					echo '<div class="row">';
					    echo '<div class="col-md-12">';
							echo '<div class="panel panel-danger">';
								echo '<div class="panel-heading"><h4><span class="glyphicon glyphicon-exclamation-sign"></span>&nbsp;&nbsp;Nyított megrendelések</h4></div>';
								echo "<table class='table'>";
								echo "<tr class='title'><td>Sz.</td><td>Időpont</td><td>Vevő</td><td>m&sup2;</td><td colspan='2'>Tipus</td><td>Terület</td><td>Szállitás</td><td>Fizetés</td><td><span class='glyphicon glyphicon-comment'></span></td><td class='more'>Státusz</td><td></td></tr>";

								for ($j=1; $j < 100; $j++) { 
									$lastday = date('Y-m-d', strtotime($lastday.' -1 day'));
									$day = date('w', strtotime($lastday));

									$dayHeading = $days[$day];
									$dayHeading2 = $days[$day];
									$datum = $lastday;
									$check = 4;

									include('views/_listpoints.php');	// include rows
								}
								
								echo "</table>";
							?>
							</div>
				    	</div>
				  	</div>
			  	</div>
			  	<?php
			}
			else {
				?>
		 		<div class="col-md-3">
		 			<button class="btn btn-danger" type="button" onclick="document.location = 'sales.php?open=1'"><span class="glyphicon glyphicon-exclamation-sign"></span>&nbsp;&nbsp;Nyított megrendelések &nbsp; <span class="badge" style="background-color: red;color: white;"><?echo $count;?></span></button>
		 		</div>

		 		<div class="col-md-2" id="showSum"></div>
		 		
		 		<div class="col-md-2">
		 		<?php
		 		if ($login == 1) {
		 			$query = $db->prepare("SELECT * FROM `customers` WHERE `checkdata` = 0 AND `status` = 1");
					$query->execute(); 
					if ($query->rowCount() > 0) {
						$count = $query->rowCount();

						echo '<button class="btn btn-danger"><span class="glyphicon glyphicon-exclamation-sign"></span>&nbsp;&nbsp;Check data &nbsp; <span class="badge" style="background-color: red;color: white;">'.$count.'</span></button>';
					}
		 		}
				?>
		 		</div>

				<div class="col-md-3"></div>
				<div class="col-md-2">	
					<?php
					if ($login == 1 OR $login == 2) {
						if ($cutting_modus == 2) {
			 				echo '<div class="checkbox"><label><input type="checkbox" onClick="switchMode(1)" checked> Vágás modus 2</label></div>';
			 			}
			 			elseif ($cutting_modus == 1) {
			 				echo '<div class="checkbox"><label><input type="checkbox" onClick="switchMode(2)"> Vágás modus 2</label></div>';
			 			}
			 		}
		 			?>
		 		</div>
		 		</div>
		 		
		 		<?php
			}
	  	}
	  	else {
	  		echo "</div>";
	  	}
	  	?>

	    <div class="row">
		    <div class="col-md-12" style="padding-bottom: 10px;">
		      	
		    	<?php
		    	//////////////////
				// TODAY
				//////////////////

				$day = date('w', strtotime($today));
				$datum = $today;
				$dayHeading = "Ma";
				$dayHeading2 = $days[$day];
				$check = 1;
				$projectday = 0;

				// Check if project today
				$query = $db->prepare("SELECT * FROM `trucks` WHERE `datum` = :today ORDER BY `sort` ASC");
				$query->bindParam(":today", $today, PDO::PARAM_STR);
				$query->execute();

				echo '<div class="panel panel-primary">';
				echo '<div class="panel-heading"><h4><span class="glyphicon glyphicon-th-list"></span>&nbsp;&nbsp;Mai megrendelések</h4></div>';
				echo "<table class='table'>";

				include('tools/get-amounts.php'); // get amounts of small and big rolls of the day

				if ($cutting_modus == 1) {
					echo '<tr><td colspan="9">'.$dayHeading2.",&nbsp;".$datum.'</td><td colspan="4" style="text-align:right;"><i>'.$total_disp.'</i></td></tr>';
				}
				elseif ($cutting_modus == 2)  {
					echo '<tr><td colspan="9">'.$dayHeading2.",&nbsp;".$datum.'</td><td colspan="4" style="text-align:right;"><i>'.$total_disp.'</i></td></tr>';
					//<td colspan="2"><i>Raktáron: '.$dayHeading2.' m&sup2;</i></td>
				}

				// Projekt view today (truck today)
				$query = $db->prepare("SELECT * FROM `trucks` WHERE `datum` = :datum ORDER BY `sort` ASC");
				$query->bindParam(":datum", $datum, PDO::PARAM_STR);
				$query->execute();

				$border = "";
				if ($query->rowCount() > 0) {
					include('views/_listpoints_projekt.php');	// special rows for projects
					$border = "truck_lower";
				}

				include('views/_listpoints.php');	// include rows
				echo "</table>";
				?>
				</div>
	    	</div>
	  	</div>

  	
		<?php
		if ($login < 4) {	// add buttons to cutting planning for management
			?>
			<div class="row" style="margin-bottom:20px;">
				<div class="col-md-2">
					<button type="button" class="btn btn-info" onclick="document.location = 'today.php'">Mai vágás</button>
				</div>
			</div>
		<?php
		}
		?>

	  	<div class="row">
		    <div class="col-md-12" style="padding-bottom: 10px;">
		      
		    	<?php
		    	//////////////////
				// NEXT DAY
				//////////////////

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

				include('tools/get-amounts.php');

				echo '<tr><td colspan="9">'.$dayHeading2.",&nbsp;".$datum.'</td><td colspan="4" style="text-align:right;"><i>'.$total_disp.'</i></td></tr>';

				
				// Projekt view today (truck today)
				$query = $db->prepare("SELECT * FROM `trucks` WHERE `datum` = :datum ORDER BY `sort` ASC");
				$query->bindParam(":datum", $datum, PDO::PARAM_STR);
				$query->execute();

				$border = "";
				if ($query->rowCount() > 0) {
					include('views/_listpoints_projekt.php');
					$border = "truck_lower";
				}
				

				include('views/_listpoints.php');

				echo "</table>";
				?>
				</div>
	    	</div>
	  	</div>

	  	<?php
		if ($login < 4) {
			?>
			<div class="row" style="margin-bottom:20px;">
				<div class="col-md-2">
					<button type="button" class="btn btn-info" onclick="document.location = 'plan.php'"><?echo $dayHeading3;?> vágás</button>
				</div>
			</div>
		<?php
		}


		//////////////////
		// FURTHER ORDERS
		//////////////////
		?>


	  	<div class="row">
		    <div class="col-md-12">
		    	<div class="panel panel-default">
		        <!-- Default panel contents -->
		        <div class="panel-heading"><h4><span class="glyphicon glyphicon-th-list"></span>&nbsp;&nbsp;Tovabbi megrendelések</h4> </div>
				
				<table class='table'>
				      
		    	<?php
				echo "<tr class='title'><td>Sz.</td><td>Időpont</td><td>Vevő</td><td>m&sup2;</td><td colspan='2'>Tipus</td><td>Terület</td><td>Szállitás</td><td>Fizetés</td><td><span class='glyphicon glyphicon-comment'></span></td><td class='more'>Státusz</td><td></td></tr>";

				$t = 1;
				$c = 0;

				for ($j=1; $j < 20; $j++) { 
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


					include('views/_listpoints.php');
					$t++;

				}

				if ($c == 0) {
					echo "<tr><td colspan='13'>Nincs</td></tr>";
				}
				
				echo "</table>";	

				?>

		      	</div>
		      	</div>
	    	</div>
	  	</div>



	</div>

	<?php
}
?>


