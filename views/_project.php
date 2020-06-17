<?php 
//////////////////////////////////////////
// Overview and single view of projects //
//////////////////////////////////////////

include('views/_header'.$header.'.php');
include('tools/functions.php');

require_once('config/config.php');
$db = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.'', DB_USER, DB_PASS);
$db -> exec("set names utf8");
date_default_timezone_set('Europe/Budapest');

$currentyear = date("Y");
$today = date("Y-m-d");

//////////
// edit project in database 
if (isset($_POST['editOrderForm'])) {
	//Get variables from form
	$id = $_POST['id'];
	$projectid = $_POST['projectid'];
	$type1 = $_POST['type1'];
	$type2 = $_POST['type2'];
	$field = $_POST['field'];
	$status = $_POST['status'];
	$project_status = $_POST['project_status'];
	$projectname = $_POST['projectname'];
	$length = $_POST['length'];
	$oldlength = $_POST['oldlength'];
	$oldcooling = $_POST['oldcooling'];

	if ($_POST['laying'] == 1) {
		$laying = 1;
	} else {
		$laying = 0;
	}

	if ($_POST['cooling'] == 1) {
		$cooling = 1;
	} else {
		$cooling = 0;
	}

	$amount0 = $_POST['amount'];
	if ($modus == 2 AND $type3 == 2) {	
		$amount0 = $amount0 * 2;	
	}
	$amount = $amount0;
	$amount_e = amount_encrypt($amount, $key2);

	if (!empty($_POST['deliveryaddress'])) {
		$deliveryaddress = $_POST['deliveryaddress'];
	}
	else {
		$deliveryaddress = "";
  	}

  	if (!empty($_POST['datum_type'])) {
		$datum_type = $_POST['datum_type'];
	}
	else {
		$datum_type = 0;
  	}

  	$country = $_POST['country1'];
	$payment = $_POST['payment'];
	
	if (!empty($_POST['telephone'])) {
		$telephone = $_POST['telephone'];
	}
	else {
		$telephone = "";
  	}
	
	$invoicenumber = $_POST['invoicenumber'];

  	if (!empty($_POST['note2'])) {
		$note2 = $_POST['note2'];
	}
	else {
		$note2 = "";
  	}

  	// Update m2 of planned trucks if length is changed
  	if ($oldlength != $length OR $oldcooling != $cooling) {

  		$query = $db->prepare("UPDATE `trucks` SET `pipes` = :pipes, `pallets` = :pallets, `amount` = :amount WHERE `id` = :id");

		if ($cooling == 0) {
			$pipes = 48;
			$pallets = 0;
		}
		elseif ($cooling == 1) {
			$pipes = 38;
			$pallets = 26;
		}

		$amount = ceil($pipes * $length * 1.2); 

		$query2 = $db->prepare("SELECT * FROM `trucks` WHERE project = :project AND status < 3");
		$query2->bindParam(":project", $projectid, PDO::PARAM_STR);
		$query2->execute(); 

		foreach ($query2 as $row) {
			$next_id = $row['id'];

			$query->bindParam(":pipes", $pipes, PDO::PARAM_STR);
			$query->bindParam(":pallets", $pallets, PDO::PARAM_STR);
			$query->bindParam(":amount", $amount, PDO::PARAM_STR);
			$query->bindParam(":id", $next_id, PDO::PARAM_STR);

			$query->execute();
		}
  	}

	//Update operations
	$sql = "UPDATE `order` SET `amount` = :amount, `type1` = :type1, `type2` = :type2, `field` = :field, `deliveryaddress` = :deliveryaddress, `payment` = :payment, `invoicenumber` = :invoicenumber, `telephone` = :telephone, `note2` = :note2, `status` = :status, `country` = :country, `project_status` = :project_status, `projectname` = :projectname, `cooling` = :cooling, `laying` = :laying, `length` = :length, `datum_type` = :datum_type WHERE `id` = :id";
	$query = $db->prepare($sql);

	$query->bindParam(":amount", $amount_e, PDO::PARAM_STR);
	$query->bindParam(":type1", $type1, PDO::PARAM_STR);
	$query->bindParam(":type2", $type2, PDO::PARAM_STR);
	$query->bindParam(":field", $field, PDO::PARAM_STR);
	$query->bindParam(":deliveryaddress", $deliveryaddress, PDO::PARAM_STR);
	$query->bindParam(":payment", $payment, PDO::PARAM_STR);
	$query->bindParam(":telephone", $telephone, PDO::PARAM_STR);
	$query->bindParam(":invoicenumber", $invoicenumber, PDO::PARAM_STR);
	$query->bindParam(":note2", $note2, PDO::PARAM_STR);
	$query->bindParam(":status", $status, PDO::PARAM_STR);
	$query->bindParam(":country", $country, PDO::PARAM_STR); 
	$query->bindParam(":project_status", $project_status, PDO::PARAM_STR);
	$query->bindParam(":projectname", $projectname, PDO::PARAM_STR);
	$query->bindParam(":cooling", $cooling, PDO::PARAM_STR);
	$query->bindParam(":laying", $laying, PDO::PARAM_STR);
	$query->bindParam(":length", $length, PDO::PARAM_STR);
	$query->bindParam(":datum_type", $datum_type, PDO::PARAM_STR);
  	$query->bindParam(":id", $id, PDO::PARAM_STR);

	$query->execute();

 	echo "<script type='text/javascript'> document.location = 'project.php?id=".$projectid."'; </script>";
}


/////////
//if customer was edited
if (isset($_POST['editCustomerForm'])) {
  //Get variables from form
  $projectid = $_POST['projectid'];
  $customer_id = $_POST['customer_id'];
  $name = $_POST['customer_name'];
  $plz = $_POST['plz'];
  $city = $_POST['city'];
  $street = $_POST['street'];
  $country = $_POST['country'];

  //Update stock of agents
  $query = $db->prepare("UPDATE `customers` SET `name` = :name, `country` = :country, `street` = :street, `city` = :city, `plz` = :plz WHERE `id` = :id");
  $query->bindParam(":id", $customer_id, PDO::PARAM_STR);
  $query->bindParam(":name", $name, PDO::PARAM_STR);
  $query->bindParam(":country", $country, PDO::PARAM_STR);
  $query->bindParam(":street", $street, PDO::PARAM_STR);
  $query->bindParam(":city", $city, PDO::PARAM_STR);
  $query->bindParam(":plz", $plz, PDO::PARAM_STR);
  $query->execute();   

 	echo "<script type='text/javascript'> document.location = 'project.php?id=".$projectid."'; </script>";

}

//////////
// insert data if truck arrives
if (isset($_POST['truckArrive'])) {
	$projectid = $_POST['projectid'];
	$truck_id = $_POST['truck_id'];
	$licence1 = $_POST['licence1'];
	$licence2 = $_POST['licence2'];

	$datum = date("Y-m-d H:i:s");
	$user = $_COOKIE["userid"];
	$status = 2;

	//Update cash
	$sql = "UPDATE `trucks` SET `licence1` = :licence1, `licence2` = :licence2, `come` = :datum, `receiver` = :user, `status` = :status WHERE `id` = :id";
	$query = $db->prepare($sql);

	$query->bindParam(":licence1", $licence1, PDO::PARAM_STR);
	$query->bindParam(":licence2", $licence2, PDO::PARAM_STR);
	$query->bindParam(":datum", $datum, PDO::PARAM_STR);
	$query->bindParam(":user", $user, PDO::PARAM_STR);
	$query->bindParam(":status", $status, PDO::PARAM_STR);
	$query->bindParam(":id", $truck_id, PDO::PARAM_STR);

	$query->execute();

	echo "<script type='text/javascript'> document.location = 'project.php?id=".$projectid."'; </script>";

}

//////////
// edit single truck 
if (isset($_POST['truckEdit'])) {
	$projectid = $_POST['projectid'];
	$truck_id = $_POST['truck_id'];
	$licence1 = $_POST['licence1'];
	$licence2 = $_POST['licence2'];
	$amount = $_POST['amount'];
	$pipes = $_POST['pipes'];
	$pallets = $_POST['pallets'];
	$status = $_POST['truck_status'];

	if (!empty($_POST['note'])) {
		$note = $_POST['note'];
	}
	else {
		$note = "";
  	}

	//Update truck
	$sql = "UPDATE `trucks` SET `licence1` = :licence1, `licence2` = :licence2, `amount` = :amount, `pipes` = :pipes, `pallets` = :pallets, `note` = :note, `status` = :status WHERE `id` = :id";
	$query = $db->prepare($sql);

	$query->bindParam(":licence1", $licence1, PDO::PARAM_STR);
	$query->bindParam(":licence2", $licence2, PDO::PARAM_STR);
	$query->bindParam(":amount", $amount, PDO::PARAM_STR);
	$query->bindParam(":pipes", $pipes, PDO::PARAM_STR);
	$query->bindParam(":pallets", $pallets, PDO::PARAM_STR);
	$query->bindParam(":note", $note, PDO::PARAM_STR);
	$query->bindParam(":status", $status, PDO::PARAM_STR);
	$query->bindParam(":id", $truck_id, PDO::PARAM_STR);

	$query->execute();

 	echo "<script type='text/javascript'> document.location = 'project.php?id=".$projectid."'; </script>";

}

//////////
// update project status when project is finished
if (isset($_POST['finishProject'])) {
	$projectid = $_POST['projectid'];
	$finalamount = $_POST['finalamount'];
	$amount_e = amount_encrypt($finalamount, $key2);

	$status = 2;
	$status2 = 4;

	$sql = "UPDATE `order` SET `project_status` = 3, `status` = 4, `amount` = :amount  WHERE `project_id` = :id";
	$query = $db->prepare($sql);
	$query->bindParam(":id", $projectid, PDO::PARAM_STR);
	$query->bindParam(":amount", $amount_e, PDO::PARAM_STR);
	$query->execute();

	echo "<script type='text/javascript'> document.location = 'project.php?id=".$projectid."'; </script>";

}

//////////////////////////////////////////////
//////////////////////////////////////////////
//////////////////////////////////////////////

//////////
// view of single project
if (isset($_GET['id'])) {
	$i = 1;
	$projectid = $_GET['id'];

	// get project data
	$query = $db->prepare("SELECT * FROM `order` WHERE `project_id` = :projectid");
	$query->bindParam(":projectid", $projectid, PDO::PARAM_STR);
	$query->execute(); 
	$result = $query->fetch(PDO::FETCH_OBJ);
	
	$id = $result->id;
	$projectname = $result->projectname;
	$project_status = $result->project_status;
	$datum = $result->date;
	$customer = $result->name;
	$amount_total = amount_decrypt($result->amount, $key2);
	$type1 = $result->type1;
	$type2 = $result->type2;
	$field = $result->field;
	$delivery = $result->delivery;
	$payment = $result->payment;
	$paid = $result->paid;
	$status = $result->status;
	$length = $result->length;
	$cooling = $result->cooling;
	$laying = $result->laying;

	$deliveryaddress = $result->deliveryaddress;
	$country = $result->country;
	$telephone = $result->telephone;
	$invoicenumber = $result->invoicenumber;
	$created = $result->created;
	$creator = $result->creator;
	$note2 = $result->note2;
	$datum_type = $result->datum_type;

	if ($note2 == "") {
		$note2_display = $note2;
	}
	else {
		$note2_display = '<mark style="background: #89ec7f;">'.$note2.'</mark>';
	}
	
	// get customer data
	$query2 = $db->prepare("SELECT * FROM customers WHERE `id` = :id");
	$query2->bindParam(":id", $customer, PDO::PARAM_STR);
	$query2->execute();
	$result2 = $query2->fetch(PDO::FETCH_OBJ);
	$customer_name = $result2->name;
	$name = $customer;
	$customer_plz = $result2->plz;
	$customer_city = $result2->city;
	$customer_street = $result2->street;
	$customer_country = $result2->country;

	// get country data
	$query = $db->prepare("SELECT * FROM countries WHERE `id` = :id");
    $query->bindParam(":id", $customer_country, PDO::PARAM_STR);
    $query->execute();
    $result = $query->fetch(PDO::FETCH_OBJ);
	$country_disp = $result->name2;
	$country_disp_en = $result->international;

	if ($project_status == 0) {			// offer only
		$status_display = "<mark style='background: #ff9c2e;'><i>Ajánlat</i></mark></h3>";
		$status_display2 = '<button type="button" class="btn btn-default" onclick="projectStatus('.$id.', 1)" style="float: left; margin-top: 7px;">Megrendel</button>';
	}
	elseif ($project_status == 1) {		// ordered
	 	$status_display = '<mark style="background: #89ec7f;">Megrendelt</mark></h3>';
	 	$status_display2 = '<button type="button" class="btn btn-default" onclick="projectStatus('.$id.', 2)" style="float: left; margin-top: 7px;"><span class="glyphicon glyphicon-ok"></span></button>';
	}
	elseif ($project_status == 2) {		// Cutting is finished
	 	$status_display = '<mark style="background: #89ec7f;">Vágás befejezett</mark></h3>';
	 	$status_display2 = '<button type="button" class="btn btn-success" onclick="stopRefresh()" role="group" data-toggle="modal" data-target="#finishProjectModal" style="float: left; margin-top: 7px;"><span class="glyphicon glyphicon-ok"></span></button>';
	}
	elseif ($project_status == 3) {		// project is finished
	 	$status_display = '<mark style="background: #89ec7f;"><span class="glyphicon glyphicon-ok"></span></mark></h3>';
	} 

	echo '<div class="row"><div class="col-md-5">';
	echo '<h3 style="margin-top:10px;">'.$projectname.' &nbsp;&nbsp ';

	// edit project data (_project_modals.php)
	echo '<button type="button" class="btn btn-default btn-xs" onclick="stopRefresh()" role="group" data-toggle="modal" data-target="#editProjectModal"><span class="glyphicon glyphicon-pencil"></span></button></h3>';
	echo '</div>';

	if ($datum_type == 1) {		// show planned month
		$month = date('n', strtotime($datum));
		echo '<div class="col-md-2"><h4>'.$months_long[($month-1)]."</h4></div>";
	}
	elseif ($datum_type == 0) {		
		echo '<div class="col-md-2"></div>';
	}

	// project status
	echo '<div class="col-md-3">';
	echo '<h3 style="margin-top:10px; font-weight:normal; float:right;">'.$status_display;
	echo '</div>';
	echo '<div class="col-md-2">';
	echo $status_display2;

	echo '</div></div>';

	echo '<div class="row">';
	echo '<div class="col-md-3">';
	echo '<b>'.$customer_name."</b>";

	// edit customer data (_project_modals.php)
	echo '<button type="button" class="btn btn-default btn-xs" style="margin-left: 10px;" onclick="stopRefresh()" role="group" data-toggle="modal" data-target="#editCustomerModal'.$i.'"><span class="glyphicon glyphicon-pencil"></span></button><br>';

	echo $customer_street."<br>";
	echo $customer_plz." ".$customer_city."<br>";
	echo $country_disp."<br><br>";
	echo $telephone;
	echo "</div>";

	if ($type1 == 1) {
		$type1_display = "Kistekercs";
		$width = 40;
	}
	elseif ($type1 == 2) {
	 	$type1_display = "Kistekercs sport";
	 	$width = 40;
	}
	elseif ($type1 == 3) {
	 	$type1_display = "Kistekercs 2,5 cm";
	 	$width = 40;
	} 
	elseif ($type1 == 4) {
	 	$type1_display = "Nagytekercs normal";
	 	$width = 120;
	} 
	elseif ($type1 == 5) {
	 	$type1_display = "Nagytekercs 2,5 cm";
	 	$width = 120;
	} 
	elseif ($type1 == 6) {
	 	$type1_display = "Nagytekercs 3 cm";
	 	$width = 120;
	} 

	if ($type2 == 1) {
		$type2_display = "";
	}
	elseif ($type2 == 2) {
	 	$type2_display = " &nbsp; <mark style='background: #f62323; color: white;'>MED</mark>";
	} 

	// Order details
	echo '<div class="col-md-3">';
	echo '<u>Megrendelt:</u><br>';
	echo '<b>'.number_format($amount_total, 0, ',', ' ')." m&sup2; ".$type1_display." ".$type2_display."</b><br>";

	// calculations for trucks needed
	if ($type1 > 3) {
		$rollsize = $length * 1.2;
		$rollsize_disp = number_format($rollsize, 2, ',', ' ');
		$length_disp = number_format($length, 2, ',', ' ');
		echo $length_disp." m x 1,20 m = ".$rollsize_disp." m&sup2; / tekercs<br>";

		$rollamount = ceil($amount_total/$rollsize);

		if ($cooling == 0) {
			$truckamount = ceil($rollamount/48);
		}
		elseif ($cooling == 1) {
			$truckamount = ceil($rollamount/38);
		}
		
		echo "<i>kb. ".$rollamount." tekercs / ".$truckamount." kamion</i><br>";

	}
	echo "<br>";

	if ($cooling == 1) {
		echo "Hűtőkamion<br>";
	}
	if ($laying == 1) {
		echo "Telepítéssel";
	}

	if ($invoicenumber != 0) {
		echo '<br><i><mark style="background: #89ec7f;">'.$invoicenumber.'</mark>';

		if ($note2 != "") {
			echo " | ";
		}
		echo $note2_display."</i>";
	}
	else {
		echo '<br><i>'.$note2_display."</i>";
	}

	echo "</div>";

	echo '<div class="col-md-4">';

	$query = $db->prepare("SELECT * FROM fields WHERE `id` = :id");
	$query->bindParam(":id", $field, PDO::PARAM_STR);
	$query->execute();
	$result = $query->fetch(PDO::FETCH_OBJ);
	echo "<span class='glyphicon glyphicon-th-large'></span>&nbsp;&nbsp; ".$result->name."<br><br>";

	echo "<b>Szállítási cím: </b><br>";
	echo $deliveryaddress."<br>";

	$query = $db->prepare("SELECT * FROM countries WHERE `id` = :id");
    $query->bindParam(":id", $country, PDO::PARAM_STR);
    $query->execute();
    $result = $query->fetch(PDO::FETCH_OBJ);
	$country_disp2 = $result->name2;
	echo $country_disp2;

	echo "</div>";

	// Get progress data
	$finish_amount = 0;
	$finish_trucks = 0;
	$finish_rolls = 0;
	$finish_pallets = 0;
	$total_trucks = 0;
	$total_amount = 0;
	$total_rolls = 0;
	$total_pallets = 0;
	$query2 = $db->prepare("SELECT * FROM `trucks` WHERE `project` = :projectid");
	$query2->bindParam(":projectid", $projectid, PDO::PARAM_STR);
	$query2->execute();
	$truck_amount = $query2->rowCount();
	foreach ($query2 as $row2) {
		$total_trucks++;
		$total_amount += $row2['amount'];
		$truck_status = $row2['status'];
		$total_rolls += $row2['pipes'];
		$total_pallets += $row2['pallets'];
		if ($truck_status == 3) {
			$finish_trucks++;
			$finish_amount += $row2['amount'];
			$finish_rolls += $row2['pipes'];
			$finish_pallets += $row2['pallets'];
		}
	}
	$progress = ceil($finish_amount/$amount*100);

	echo '<div class="col-md-2">';
	echo '<progress max="100" value="'.$progress.'"></progress><br>';
	echo '<u>Befejezett / Tervezett:</u><br>';
	echo "<b style='font-size: 12pt'>".number_format($finish_amount, 0, ',', ' ')." / ".number_format($total_amount, 0, ',', ' ')." m&sup2;</b><br><br>";
	echo $finish_trucks." / ".$total_trucks." kamion<br>";
	echo $finish_rolls." / ".$total_rolls." cső<br>";

	if ($cooling == 1) {
		echo $finish_pallets." / ".$total_pallets." raklap";
	}
	
	echo "</div>";

	echo "</div><br><hr><br>";

	$project_check = 1;

	$days = array("Vasárnap", "Hétfő", "Kedd", "Szerda", "Csütörtök", "Péntek", "Szombat");

	//////////////////////////////
	// Get start date of project
	$query = $db->prepare("SELECT * FROM `order` WHERE  `project_id` = :projectid ORDER BY `sort` ASC LIMIT 1");
	$query->bindParam(":projectid", $projectid, PDO::PARAM_STR);
	$query->execute();
	$result = $query->fetch(PDO::FETCH_OBJ);
	$datum = $result->date;

	$nextdayname = date('w', strtotime($datum));
	$dayHeading = $days[$nextdayname];

	echo "<input type='hidden' id='projectStartDatum' value='".$datum."'>";

	$t = 1;

	// check the next days for trucks in database and display them
	for ($j=1; $j < 80; $j++) { 

		// Get trucks of the day
		$query = $db->prepare("SELECT * FROM `trucks` WHERE  `project` = :projectid AND `datum` = :datum ORDER BY `sort` ASC ");
		$query->bindParam(":projectid", $projectid, PDO::PARAM_STR);
		$query->bindParam(":datum", $datum, PDO::PARAM_STR);
		$query->execute();

		if ($query->rowCount() > 0) {
			//Tools for editing number of trucks
			$query2 = $db->prepare("SELECT * FROM `trucks` WHERE  `project` = :projectid AND `datum` = :datum ORDER BY `sort` ASC ");
			$query2->bindParam(":projectid", $projectid, PDO::PARAM_STR);
			$query2->bindParam(":datum", $datum, PDO::PARAM_STR);
			$query2->execute();

			$trucks_fix = 0;
			$trucks_amount = $query2->rowCount();
			$truck_disp = "";
			
			foreach ($query2 as $row) {
				$truck_status = $row['status'];
				
				if ($truck_status == 3) {
				 	$truck_disp .= '<img src="../img/truck_green.png" class="truck">';
				 	$trucks_fix++;
				}
				elseif ($truck_status == 2) {
				 	$truck_disp .= '<img src="../img/truck_orange.png" class="truck">';
				 	$trucks_fix++;
				}
				else {
					$truck_disp .= '<img src="../img/truck.png" class="truck">';
				} 	
			}

			echo '<div class="row" style="margin-bottom: 10px;">';
			echo '<div class="col-md-3" style="padding-bottom: 10px;"><b>'.$dayHeading.',&nbsp;'.$datum.'</b></div>';
			echo '<div class="col-md-7" style="padding-bottom: 10px;"><div id="truck-output">';
			?>

			<table class="table" style="width: 800px">
				<tr>
					<td style="border-top: 0; width: 20%;"><input type='date' id="truckDatum_<?php echo $t; ?>" onfocusout="truckDatumFunction(<?php echo $projectid; ?>, <?php echo $t; ?>)" value="<?php echo $datum; ?>"></td>
					<td style="border-top: 0; width: 15%;"><button type="button" class="btn btn-complete btn-sm" style="float:left; margin-top: 5px;" onclick="changeTruck2(<?php echo $projectid; ?>, <?php echo $t; ?>, 1)"><span class="glyphicon glyphicon-minus"></span></button>
					<button type="button" class="btn btn-complete btn-sm" style="float:left; margin-left: 5px; margin-top: 5px;" onclick="changeTruck2(<?php echo $projectid; ?>, <?php echo $t; ?>, 2)"><span class="glyphicon glyphicon-plus"></span></button></td>
					<td style="border-top: 0; width: 65%;" id="truckpics_1">
						<?php echo $truck_disp; ?>
					</td>
					
				</tr>
			</table>
			<input type='hidden' id="totalTrucks_<?php echo $t; ?>" value="<?php echo $trucks_amount; ?>">
			<input type='hidden' id="fixTrucks_<?php echo $t; ?>" value="<?php echo $trucks_fix; ?>">
			<input type='hidden' id="oldDatum_<?php echo $t; ?>" value="<?php echo $datum; ?>">

			<?php
			echo '</div></div>';

			echo '</div>';

			echo '<div class="row" style="margin-bottom: 50px;">';
			echo '<div class="panel panel-primary">';
			echo "<table class='table'>";
			echo "<tr class='title'><td style='width: 20px;'></td><td style='width: 150px;'>Időpont</td><td style='width: 100px;'>Kamion</td><td style='width: 130px;'>m&sup2;</td><td style='width: 100px;'>Csövek</td>";

			if ($cooling == 1) {		// show pallets only when cooling truck
				echo "<td style='width: 100px;'>Raklap</td>";
			}

			echo "<td style='width: 200px;'>Rendszám</td><td style='width: 200px;'><span class='glyphicon glyphicon-comment'></span></td><td colspan='4'></td></tr>";

			include('views/_truck_foreach.php');	// single rows with truck data

			echo "</table>";
			echo "</div></div>";

			$after_datum = date('Y-m-d', strtotime($datum.' +1 day'));
			$t++;	
		}

		$nextday = date('Y-m-d', strtotime($datum.' +1 day'));
		$day = date('w', strtotime($nextday));
		$dayHeading = $days[$day];
		$datum = $nextday;
	}

	?>
	<div class="row" style="margin-bottom: 10px;">
		<div class="col-md-3" style="padding-bottom: 10px;"></div>
		<div class="col-md-7" style="padding-bottom: 10px;"><div id="truck-output">
			<table class="table" style="width: 800px">
				<tr>
					<td style="border-top: 0; width: 1%;"><input type='date' id="addDay" value="<?php echo $after_datum; ?>"></td>
					<td style="border-top: 0; width: 15%;">
						<button type="button" class="btn btn-complete btn-sm" style="float:left; margin-left: 5px; margin-top: 5px;" onclick="addDayFunction(<?php echo $projectid; ?>)"><span class="glyphicon glyphicon-plus"></span></button>
					</td>
				</tr>
			</table>
		</div></div><br><br><br><br><br><br><br><br>
		<div id="test-output"></div>
<?php

include('views/_project_modals.php'); 	// Insert modals for editing and deleting
}


//////////////////////////////////////////////
//////////////////////////////////////////////
//////////////////////////////////////////////

// show list of projects of the year
else {
	//Show buttons right for years    
	if (isset($_GET['year'])) {
		$year_main = $_GET['year'];
	}
	else {
		$year_main = $currentyear;
	}

	$year_main_last = $year_main - 1;

	if ($year_main == $currentyear) {
		$btn_this = "btn-success";
		$btn_last = "btn-default";
	}
	elseif ($year_main == $lastyear) {
		$btn_this = "btn-default";
		$btn_last = "btn-success";
	}
	else {
		$btn_this = "btn-default";
		$btn_last = "btn-default";
	}

	echo '<div class="inputform">';
	echo '<div class="row"><div class="col-md-8">';
	echo '<h3 style="margin-top:10px;">Projektek '.$year_main.'</h3></div>';
	?>
	<div class="col-md-4">
		<button type="button" class="btn <?php echo $btn_this; ?>" style="float:right; margin-left:5px;" onclick="document.location = 'project.php?year=<?php echo $currentyear; ?>'"><?php echo $currentyear; ?></button>
		<button type="button" class="btn <?php echo $btn_last; ?>" style="float:right;" onclick="document.location = 'project.php?year=<?php echo $lastyear; ?>'"><?php echo $lastyear; ?></button>
	</div>
	</div>
	<?php
	echo '<div class="row"><div class="col-md-10">';

	$startdate = $year_main."-01-01 00:00:00";
	$enddate = $year_main."-12-31 23:59:59";

	echo '<table class="table"><thead>';
	echo "<tr><th>Projekt</th>";
	echo "<th>Vevő</th>";
	echo "<th>Kezdés</th>";
	echo "<th>Mennyiség</th>";
	echo "<th>Típus</th>";
	echo "<th>Terület</th>";
	echo "<th>Telepítés</th>";
	echo "<th>Hűtő</th>";
	echo "<th>Status</th>";
	echo "</tr></thead><tbody>";

	// needed for calculations
	$normal_total_order = 0;
	$normal_amount_order = 0;
	$dick25_total_order = 0;
	$dick25_amount_order = 0;
	$dick30_total_order = 0;
	$dick30_amount_order = 0;
	$total_order = 0;
	$amount_order = 0;
	$laying_total_order = 0;
	$laying_amount_order = 0;
	$cooling_total_order = 0;
	$cooling_amount_order = 0;

	$normal_total_finish = 0;
	$normal_amount_finish = 0;
	$dick25_total_finish = 0;
	$dick25_amount_finish = 0;
	$dick30_total_finish = 0;
	$dick30_amount_finish = 0;
	$total_finish = 0;
	$amount_finish = 0;
	$laying_total_finish = 0;
	$laying_amount_finish = 0;
	$cooling_total_finish = 0;
	$cooling_amount_finish = 0;
	$pipes_amount = 0;
	$pallets_amount = 0;
	$trucks_amount = 0;
	$fields = array();
	$unknown_total = 0;

	$query = $db->prepare("SELECT * FROM fields WHERE `complete` < 1");
	$query->bindParam(":id", $field, PDO::PARAM_STR);
	$query->execute();
	$number_fields = $query->rowCount();
	foreach ($query as $row) {
		$field_id = $row['id'];
		$fields[$field_id][1] = 0;
		$fields[$field_id][2] = 0;
	}

	$query = $db->prepare("SELECT * FROM `order` WHERE planneddate >= :start AND planneddate < :end AND `project_id` > 38 AND status < 5 ORDER BY `date` ASC");
	$query->bindParam(":start", $startdate, PDO::PARAM_STR);
	$query->bindParam(":end", $enddate, PDO::PARAM_STR);
	$query->execute(); 

	$a = 0;
	$open = 0;
	foreach ($query as $row) {
	    $projectid = $row['project_id'];
	    $projectname = $row['projectname'];
	    $customer = $row['name'];
	    $datum = $row['date'];
	    $amount = amount_decrypt($row['amount'], $key2);
	    $type1 = $row['type1'];
	    $status = $row['project_status'];
	    $datum_type = $row['datum_type'];
	    $field = $row['field'];
	    $laying = $row['laying'];
	    $cooling = $row['cooling'];


	    $query = $db->prepare("SELECT * FROM customers WHERE `id` = :id");
		$query->bindParam(":id", $customer, PDO::PARAM_STR);
		$query->execute();
		$result = $query->fetch(PDO::FETCH_OBJ);
		$name_display = $result->name;

		if ($field == 111111) {
			$field_display = "?";
			$unknown_total += $amount;
		}
		else {
			$query = $db->prepare("SELECT * FROM fields WHERE `id` = :id");
			$query->bindParam(":id", $field, PDO::PARAM_STR);
			$query->execute();
			$result = $query->fetch(PDO::FETCH_OBJ);
			$field_display = $result->name;
			$field_id = $result->id;

			if ($status == 1) {
				$fields[$field_id][1] += $amount;
			}
			elseif ($status > 1) {
				$fields[$field_id][2] += $amount;
			}
		}
		

		if ($type1 == 1) {
			$type1_display = "Kistekercs";
		}
		elseif ($type1 == 2) {
		 	$type1_display = "Kistekercs sport";
		}
		elseif ($type1 == 3) {
		 	$type1_display = "Kistekercs 2,5 cm";
		} 
		elseif ($type1 == 4) {
		 	$type1_display = "normal";
		} 
		elseif ($type1 == 5) {
		 	$type1_display = "2,5 cm";
		} 
		elseif ($type1 == 6) {
		 	$type1_display = "3 cm";
		} 

		if ($status == 0) {
			$status_display = "<i>Ajánlat</i>";
		}
		elseif ($status == 1) {
		 	$status_display = "<font color='green'>Megrendelt</font>";
		}
		elseif ($status == 2) {
		 	$status_display = '<img src="../img/green.png" class="picture_status">';
		} 
		elseif ($status == 3) {
		 	$status_display = '<span class="glyphicon glyphicon-ok"></span>';
		} 

		if ($laying == 1) {
		 	$laying_display = '<span class="glyphicon glyphicon-remove"></span>';
		}
		else {
		 	$laying_display = '';
		} 

		if ($cooling == 1) {
		 	$cooling_display = '<span class="glyphicon glyphicon-remove"></span>';
		}
		else {
		 	$cooling_display = '';
		} 

		if ($status > 1) {
			echo '<tr data-href="project.php?id='.$projectid.'" style="background-color: #f2f2f2;">';
		}
		else {
			$open++;
			if ($open == 1) {
				echo '<tr data-href="project.php?id='.$projectid.'" style="border-top: black 2px solid;">';
			}
			else {
				echo '<tr data-href="project.php?id='.$projectid.'">';
			}
			
		}
	    
	    echo '<td><a><b>'.$projectname."</b></a></td>";
	    echo '<td>'.$name_display."</td>";

	    if ($datum_type == 1) {
	    	$months = array("Január", "Február", "Március", "Április", "Május", "Június", "Július", "Augusztus", "Szeptember", "Október", "November", "December");
			$month = date('n', strtotime($datum));
			echo '<td>'.$months[($month-1)]."</td>";
		}
		elseif ($datum_type == 0) {
			echo '<td>'.$datum."</td>";
		}

		echo "<td>".number_format($amount, 0, ',', ' ')." m&sup2;</td>";
		echo "<td>".$type1_display."</td>";
		echo "<td>".$field_display."</td>";
		echo "<td style='text-align: center;'>".$laying_display."</td>";
		echo "<td style='text-align: center;'>".$cooling_display."</td>";
		echo "<td style='text-align: center;'>".$status_display."</td>";
		echo "</tr>";

		// Statistics
		if ($status == 1) {
			$amount_order ++;
			$total_order += $amount;

			if ($type1 == 4) {
			 	$normal_amount_order ++;
				$normal_total_order += $amount;
			} 
			elseif ($type1 == 5) {
			 	$dick25_amount_order ++;
				$dick25_total_order += $amount;
			} 
			elseif ($type1 == 6) {
			 	$dick30_amount_order ++;
				$dick30_total_order += $amount;
			} 

			if ($cooling == 1) {
				$cooling_amount_order ++;
				$cooling_total_order += $amount;
			}

			if ($laying == 1) {
				$laying_amount_order ++;
				$laying_total_order += $amount;
			}
		}
		elseif ($status > 1) {
			$amount_finish ++;
			$total_finish += $amount;

			if ($type1 == 4) {
			 	$normal_amount_finish ++;
				$normal_total_finish += $amount;
			} 
			elseif ($type1 == 5) {
			 	$dick25_amount_finish ++;
				$dick25_total_finish += $amount;
			} 
			elseif ($type1 == 6) {
			 	$dick30_amount_finish ++;
				$dick30_total_finish += $amount;
			}

			if ($cooling == 1) {
				$cooling_amount_finish ++;
				$cooling_total_finish += $amount;
			}

			if ($laying == 1) {
				$laying_amount_finish ++;
				$laying_total_finish += $amount;
			}

			// Get trucks, pipes and pallets
			$query2 = $db->prepare("SELECT * FROM `trucks` WHERE `project` = :projectid");
			$query2->bindParam(":projectid", $projectid, PDO::PARAM_STR);
			$query2->execute();
			$trucks_amount += $query2->rowCount();
			foreach ($query2 as $row2) {
				$pipes_amount += $row2['pipes'];
				$pallets_amount += $row2['pallets'];
			}
		}

	}

	$normal_total_order_disp = number_format($normal_total_order, 0, ',', ' ');
	$dick25_total_order_disp = number_format($dick25_total_order, 0, ',', ' ');
	$dick30_total_order_disp = number_format($dick30_total_order, 0, ',', ' ');
	$total_order_disp = number_format($total_order, 0, ',', ' ');
	$laying_total_order_disp = number_format($laying_total_order, 0, ',', ' ');
	$cooling_total_order_disp = number_format($cooling_total_order, 0, ',', ' ');

	$normal_total_finish_disp = number_format($normal_total_finish, 0, ',', ' ');
	$dick25_total_finish_disp = number_format($dick25_total_finish, 0, ',', ' ');
	$dick30_total_finish_disp = number_format($dick30_total_finish, 0, ',', ' ');
	$total_finish_disp = number_format($total_finish, 0, ',', ' ');
	$laying_total_finish_disp = number_format($laying_total_finish, 0, ',', ' ');
	$cooling_total_finish_disp = number_format($cooling_total_finish, 0, ',', ' ');

	echo "</tbody></table>";
	echo "</div></div>";


	// Show statistics
	echo '<div class="row"><div class="col-md-6">';
	echo '<h4 style="margin-top:80px;">Statisztika</h4>';
	echo "<table class='table table-bordered'>";
	echo "<tr class='title'><td class='border'></td><td>Megrendelés</td><td>Befejezett</td></tr>";

	echo "<tr><td><b>Normal</b></td>";
	if ($normal_amount_order > 0) {
		echo "<td>".$normal_amount_order." &nbsp; / &nbsp; ".$normal_total_order_disp." m&sup2;</td>";
	}
	else {
		echo "<td> - </td>";
	}
	if ($normal_amount_finish > 0) {
		echo "<td>".$normal_amount_finish." &nbsp; / &nbsp; ".$normal_total_finish_disp." m&sup2;</td></tr>";
	}
	else {
		echo "<td> - </td><tr>";
	}


	echo "<tr><td><b>Dick 2,5 cm</b></td>";
	if ($dick25_amount_order > 0) {
		echo "<td>".$dick25_amount_order." &nbsp; / &nbsp;  ".$dick25_total_order_disp." m&sup2;</td>";
	}
	else {
		echo "<td> - </td>";
	}
	if ($dick25_amount_finish > 0) {
		echo "<td>".$dick25_amount_finish." &nbsp; / &nbsp; ".$dick25_total_finish_disp." m&sup2;</b></td></tr>";
	}
	else {
		echo "<td> - </td><tr>";
	}


	echo "<tr><td><b>Dick 3 cm</b></td>";
	if ($dick30_amount_order > 0) {
		echo "<td>".$dick30_amount_order." &nbsp; / &nbsp; ".$dick30_total_order_disp." m&sup2;</td>";
	}
	else {
		echo "<td> - </td>";
	}
	if ($dick30_amount_finish > 0) {
		echo "<td>".$dick30_amount_finish." &nbsp; / &nbsp; ".$dick30_total_finish_disp." m&sup2;</td></tr>";
	}
	else {
		echo "<td> - </td><tr>";
	}


	echo "<tr class='tablesum'><td><b>TOTAL</b></td>";
	if ($amount_order > 0) {
		echo "<td>".$amount_order." &nbsp; / &nbsp; ".$total_order_disp." m&sup2;</td>";
	}
	else {
		echo "<td> - </td>";
	}
	if ($amount_finish > 0) {
		echo "<td>".$amount_finish." &nbsp; / &nbsp; ".$total_finish_disp." m&sup2;</td></tr>";
	}
	else {
		echo "<td> - </td><tr>";
	}


	echo "<tr><td><b>Telepítés</b></td>";
	if ($laying_amount_order > 0) {
		echo "<td>".$laying_amount_order." &nbsp; / &nbsp; ".$laying_total_order_disp." m&sup2;</td>";
	}
	else {
		echo "<td> - </td>";
	}
	if ($laying_amount_finish > 0) {
		echo "<td>".$laying_amount_finish." &nbsp; / &nbsp; ".$laying_total_finish_disp." m&sup2;</td></tr>";
	}
	else {
		echo "<td> - </td><tr>";
	}

	echo "<tr><td style='padding-bottom: 30px;'><b>Hűtő kamion</b></td>";
	if ($cooling_amount_order > 0) {
		echo "<td style='padding-bottom: 30px;'>".$cooling_amount_order." &nbsp; / &nbsp; ".$cooling_total_order_disp." m&sup2;</td>";
	}
	else {
		echo "<td style='padding-bottom: 30px;'> - </td>";
	}
	if ($cooling_amount_finish > 0) {
		echo "<td style='padding-bottom: 30px;'>".$cooling_amount_finish." &nbsp; / &nbsp;  ".$cooling_total_finish_disp." m&sup2;</td></tr>";
	}
	else {
		echo "<td style='padding-bottom: 30px;'> - </td><tr>";
	}


	foreach($fields as $field_id => $amounts) {
		$field_amount_order = $amounts[1];
		$field_amount_finish = $amounts[2];

		$query = $db->prepare("SELECT * FROM fields WHERE `id` = :id");
		$query->bindParam(":id", $field_id, PDO::PARAM_STR);
		$query->execute();
		$result = $query->fetch(PDO::FETCH_OBJ);
		$field_display = $result->name;

		if ($field_amount_order > 0 OR $field_amount_finish > 0) {
			echo "<tr><td><b>".$field_display."</b></td>";
			echo "<td>".number_format($field_amount_order, 0, ',', ' ')." m&sup2;</td>";
			echo "<td>".number_format($field_amount_finish, 0, ',', ' ')." m&sup2;</td></tr>";
		}
	}

	echo "<tr><td><b>?</b></td>";
	echo "<td>".number_format($unknown_total, 0, ',', ' ')." m&sup2;</td>";
	echo "<td>-</td></tr>";

	echo "</table></div>";


	echo '<div class="col-md-2"></div>';
	echo '<div class="col-md-2">';
	echo '<h4 style="margin-top:80px;">&nbsp;</h4>';
	echo "<table class='table table-bordered'>";
	echo "<tr class='title'><td class='border'></td><td>Befejezett</td></tr>";

	echo "<tr><td><b>Kamion</b></td>";
	echo "<td>".number_format($trucks_amount, 0, ',', ' ')."</td></tr>";

	echo "<tr><td><b>Cső</b></td>";
	echo "<td>".number_format($pipes_amount, 0, ',', ' ')."</td></tr>";

	echo "<tr><td><b>Raklap</b></td>";
	echo "<td>".number_format($pallets_amount, 0, ',', ' ')."</td></tr>";

	echo "</table></div></div>";



	echo "</div>";
}


?>