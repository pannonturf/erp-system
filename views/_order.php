<?php 
////////////////////
// Add new orders //
////////////////////

require_once('config/config.php');
include('views/_header'.$header.'.php');
include('tools/functions.php');

$db = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.'', DB_USER, DB_PASS);
$db -> exec("set names utf8");
date_default_timezone_set('Europe/Budapest');


////////////
//Add order to database after form has been sent
if (isset($_POST['orderForm'])) {

	//Get variables from form
	if ($_POST['date'] == 100) {
		$date = $_POST['moreDatum'];
	}
	else {
		$date = $_POST['date'];
	}
	
	$time = $_POST['time'];
	$planneddate = $date." ".$time;

	$name = $_POST['customer_id'];

	$amount = $_POST['amount'];
	$amount_e = amount_encrypt($amount, $key2);

	$type1 = $_POST['type1'];
	$type2 = $_POST['type2'];
	$type3 = $_POST['type3'];
	$field = $_POST['field'];

	$trucks = $_POST['trucks'];
	$city = $_POST['city_id'];
	$country = $_POST['country1'];
	$length = $_POST['length'];

	$delivery = $_POST['delivery'];

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

	$projectid = 0;
	$project_status = 0;
	$datum_type = 0;

	$pallet = $_POST['pallet'];

	/*
	if ($name == 12) {
		$pallet = 2;
	}
	else {
		$pallet = 1;
	}
	*/

	// Check if order is a project
	if (!empty($_POST['projectname'])) {
		$projectname = $_POST['projectname'];
		$project_status = $_POST['project_status'];
		$datum_type = $_POST['datum_type'];
		$delivery = 1;

		//Get last project_id
		$query = $db->prepare("SELECT * FROM `order` WHERE `project_id` > 0 ORDER BY `project_id` DESC LIMIT 1");
	    $query->execute();
	    $result = $query->fetch(PDO::FETCH_OBJ);
	    $lastid = $result->project_id;
	    $projectid = $lastid + 1;
	}
	else {
		$projectname = "";
  	}

  	if (!empty($_POST['customer'])) {
  		$customer_name = $_POST['customer'];
  	}
	else {
		$customer_name = "ERR";
  	}
  	
  	if (!empty($_POST['customer_plz'])) {
  		$customer_plz = $_POST['customer_plz'];
  	}
	else {
		$customer_plz = "";
  	}

  	if (!empty($_POST['customer_city'])) {
  		$customer_city = $_POST['customer_city'];
  	}
	else {
		$customer_city = "";
  	}

  	if (!empty($_POST['customer_street'])) {
  		$customer_street = $_POST['customer_street'];
  	}
	else {
		$customer_street = "";
  	}

  	if (!empty($_POST['contactperson'])) {
  		$contactperson = $_POST['contactperson'];
  	}
	else {
		$contactperson = "";
  	}

  	if (!empty($_POST['email'])) {
		$email = $_POST['email'];
	}
	else {
		$email = "";
  	}
	
	if (!empty($_POST['telephone'])) {
		$telephone = $_POST['telephone'];
	}
	else {
		$telephone = "";
  	}

  	if (!empty($_POST['licence'])) {
		$licence = $_POST['licence'];
	}
	else {
		$licence = "";
  	}


	if (!empty($_POST['deliveryname'])) {
		$deliveryname = $_POST['deliveryname'];
	}
	else {
		$deliveryname = "";
  	}

	if (!empty($_POST['deliveryaddress'])) {
		$deliveryaddress = $_POST['deliveryaddress'];
	}
	else {
		$deliveryaddress = "";
  	}

	if ($delivery == 2) {
	  	if (!empty($_POST['forwarder'])) {
			$forwarder = $_POST['forwarder'];
		}
		else {
			$forwarder = "";
	  	}  	
	}
	else {
		$deliverytime = "";
		$forwarder = "";
	}

	$payment = $_POST['payment'];
	
	if (!empty($_POST['email'])) {
		$email = $_POST['email'];
	}
	else {
		$email = "";
  	}
	
	if (!empty($_POST['telephone'])) {
		$telephone = $_POST['telephone'];
	}
	else {
		$telephone = "";
  	}

  	$invoicenumber = $_POST['invoicenumber'];
	
	if (!empty($_POST['invoicename'])) {
		$invoicename = $_POST['invoicename'];
	}
	else {
		$invoicename = "";
  	}
	
	if (!empty($_POST['invoiceaddress'])) {
		$invoiceaddress = $_POST['invoiceaddress'];
	}
	else {
		$invoiceaddress = "";
  	}

	if (!empty($_POST['note'])) {
		$note = $_POST['note'];
	}
	else {
		$note = "";
  	}

  	if (!empty($_POST['note2'])) {
		$note2 = $_POST['note2'];
	}
	else {
		$note2 = "";
  	}

	$created = date("Y-m-d H:i:s");

	if (isset($_COOKIE['userid'])) {
		$creator = $_COOKIE["userid"];
	}
	elseif (isset($_SESSION['userid'])) {
		$creator = $_SESSION['userid'];
	}
	else {
		$creator = 0;
	}

	$status = 1;

	if ($type2 == 2 AND $date > $today) {
		$team = 2;
	}
	else {
		$team = 1;
	}

	// Check if customer data was edited -> checkdata = 0

	// Customer data
	$query = $db->prepare("SELECT * FROM customers WHERE `id` = :id");
	$query->bindParam(":id", $name, PDO::PARAM_STR);
	$query->execute();
	$result = $query->fetch(PDO::FETCH_OBJ);
	$customer_name_old = $result->name;
	$contactperson_old = $result->contactperson;
	$customer_street_old = $result->street;
	$customer_plz_old = $result->plz;
	$customer_city_old = $result->city;
	$telephone_old = $result->phone;
	$email_old = $result->email;
	$checkdata_old = $result->checkdata;

	if ($customer_name_old != $customer_name OR $contactperson_old != $contactperson OR $customer_street_old != $customer_street OR $customer_plz_old != $customer_plz OR $customer_city_old != $customer_city OR $telephone_old != $telephone OR $email_old != $email) {
		$checkdata = 0;
	}
	else {
		$checkdata = $checkdata_old;
	}
	

	//create trucks if project
	if (!empty($_POST['projectname'])) {

		$query = $db->prepare("INSERT INTO `trucks` (`id`, `project`, `sort`, `datum`, `status`, `creator`, `pipes`, `pallets`, `amount`) VALUES (NULL, :projectid, :sort, :datum, :status, :creator, :pipes, :pallets, :amount)");

		$query->bindParam(":projectid", $projectid, PDO::PARAM_STR);

		$sort = 1;
		$status = 1;

		if ($cooling == 0) {
			$pipes = 48;
			$pallets = 0;
		}
		elseif ($cooling == 1) {
			$pipes = 38;
			$pallets = 26;
		}

		$amount = ceil($pipes * $length * 1.2); 

		foreach($trucks AS $truck_day) {
		  	$truck_datum = $truck_day[1];
		  	$truck_count = $truck_day[2];

		  	for($t=0; $t<$truck_count; $t++) {
			  
		  		$query->bindParam(":sort", $sort, PDO::PARAM_STR);
		  		$query->bindParam(":datum", $truck_datum, PDO::PARAM_STR);
		  		$query->bindParam(":status", $status, PDO::PARAM_STR);
		  		$query->bindParam(":creator", $creator, PDO::PARAM_STR);
		  		$query->bindParam(":pipes", $pipes, PDO::PARAM_STR);
		  		$query->bindParam(":pallets", $pallets, PDO::PARAM_STR);
		  		$query->bindParam(":amount", $amount, PDO::PARAM_STR);
		  		$query->execute(); 

			  	$sort ++;
			}

		}
	}

	//Find the right sort number and update all orders behind it (+1)
	$startdate = $date." 00:00:00";
	$enddate = $date." 23:59:59";

	if ($type1 < 4) {
		$query = $db->prepare("SELECT * FROM `order` WHERE planneddate >= :start AND planneddate < :end AND `type1` < 4 AND `status` < 4 AND `team` = :team ORDER BY `sort` DESC");
	}
	else {
		$query = $db->prepare("SELECT * FROM `order` WHERE planneddate >= :start AND planneddate < :end AND `type1` > 3 AND `status` < 4 AND `team` = :team ORDER BY `sort` DESC");
	}
	$query->bindParam(":start", $startdate, PDO::PARAM_STR);
	$query->bindParam(":end", $enddate, PDO::PARAM_STR);
	$query->bindParam(":team", $team, PDO::PARAM_STR);
	$query->execute(); 

	if ($query->rowCount() == 0) {
		$sort = 1;
	}
	else {
		$sort = $query->rowCount() + 1;
		foreach ($query as $row) {
			$next_id = $row['id'];
			$next_planneddate = $row['planneddate'];
			if ($planneddate < $next_planneddate) {
				$sql2 = "UPDATE `order` SET `sort` = :sort WHERE `id` = :id";
				$query2 = $db->prepare($sql2);
			  	$query2->bindParam(":sort", $sort, PDO::PARAM_STR);  
			  	$query2->bindParam(":id", $next_id, PDO::PARAM_STR);  
				$query2->execute();

				$sort = $sort - 1;
			}
		}
	}      

	// Update customer data
	if ($delivery == 1) {
		$query = $db->prepare("UPDATE `customers` SET `name` = :name, `contactperson` = :contactperson, `street` = :street, `plz` = :plz, `city` = :city, `phone` = :phone, `email` = :email, `licence` = :licence, `checkdata` = :checkdata WHERE `id` = :id");
	}
	else {
		$query = $db->prepare("UPDATE `customers` SET `name` = :name, `contactperson` = :contactperson, `street` = :street, `plz` = :plz, `city` = :city, `phone` = :phone, `email` = :email, `checkdata` = :checkdata WHERE `id` = :id");
	}
	$query->bindParam(":name", $customer_name, PDO::PARAM_STR);
	$query->bindParam(":contactperson", $contactperson, PDO::PARAM_STR);
	$query->bindParam(":street", $customer_street, PDO::PARAM_STR);
	$query->bindParam(":plz", $customer_plz, PDO::PARAM_STR);
	$query->bindParam(":city", $customer_city, PDO::PARAM_STR);
	$query->bindParam(":phone", $telephone, PDO::PARAM_STR);
	$query->bindParam(":email", $email, PDO::PARAM_STR);

	if ($delivery == 1) {
		$query->bindParam(":licence", $licence, PDO::PARAM_STR);
	}

	$query->bindParam(":checkdata", $checkdata, PDO::PARAM_STR);
	$query->bindParam(":id", $name, PDO::PARAM_STR);
	$query->execute();


	/////////////////////////////////
	$sql = "INSERT INTO `order` (`id`, `project_id`, `date`, `time`, `sort`, `name`, `amount`, `type1`, `type2`, `type3`, `pallet`, `length`, `field`, `delivery`, `deliveryname`, `deliveryaddress`, `forwarder`, `payment`, `invoicenumber`, `note`, `note2`, `licence`, `created`, `creator`, `status`, `team`, `planneddate`, `laying`, `country`, `city`, `cooling`, `projectname`, `project_status`, `datum_type`)  VALUES (NULL, :project_id, :datum, :time, :sort, :name, :amount, :type1, :type2, :type3, :pallet, :length, :field, :delivery, :deliveryname, :deliveryaddress, :forwarder, :payment, :invoicenumber, :note, :note2, :licence, :created, :creator, :status, :team, :planneddate, :laying, :country, :city, :cooling, :projectname, :project_status, :datum_type);";

	$query = $db->prepare($sql);

	$query->bindParam(":project_id", $projectid, PDO::PARAM_STR); 
	$query->bindParam(":datum", $date, PDO::PARAM_STR);  
	$query->bindParam(":time", $time, PDO::PARAM_STR); 
	$query->bindParam(":sort", $sort, PDO::PARAM_STR); 
	$query->bindParam(":name", $name, PDO::PARAM_STR);
	$query->bindParam(":amount", $amount_e, PDO::PARAM_STR);
	$query->bindParam(":type1", $type1, PDO::PARAM_STR);
	$query->bindParam(":type2", $type2, PDO::PARAM_STR);
	$query->bindParam(":type3", $type3, PDO::PARAM_STR);
	$query->bindParam(":pallet", $pallet, PDO::PARAM_STR);
	$query->bindParam(":length", $length, PDO::PARAM_STR);
	$query->bindParam(":field", $field, PDO::PARAM_STR);
	$query->bindParam(":delivery", $delivery, PDO::PARAM_STR);
	$query->bindParam(":deliveryname", $deliveryname, PDO::PARAM_STR);
	$query->bindParam(":deliveryaddress", $deliveryaddress, PDO::PARAM_STR);
	$query->bindParam(":forwarder", $forwarder, PDO::PARAM_STR);
	$query->bindParam(":payment", $payment, PDO::PARAM_STR);
	$query->bindParam(":invoicenumber", $invoicenumber, PDO::PARAM_STR);
	$query->bindParam(":note", $note, PDO::PARAM_STR);
	$query->bindParam(":note2", $note2, PDO::PARAM_STR);
	$query->bindParam(":licence", $licence, PDO::PARAM_STR);
	$query->bindParam(":created", $created, PDO::PARAM_STR);
	$query->bindParam(":creator", $creator, PDO::PARAM_STR);
	$query->bindParam(":status", $status, PDO::PARAM_STR);
	$query->bindParam(":team", $team, PDO::PARAM_STR);
	$query->bindParam(":planneddate", $planneddate, PDO::PARAM_STR);
	$query->bindParam(":laying", $laying, PDO::PARAM_STR);
	$query->bindParam(":country", $country, PDO::PARAM_STR);
	$query->bindParam(":city", $city, PDO::PARAM_STR);
	$query->bindParam(":cooling", $cooling, PDO::PARAM_STR);
	$query->bindParam(":projectname", $projectname, PDO::PARAM_STR);
	$query->bindParam(":project_status", $project_status, PDO::PARAM_STR);
	$query->bindParam(":datum_type", $datum_type, PDO::PARAM_STR);

	$query->execute();
  

	//Back to frontpage
	echo "<script type='text/javascript'> document.location = 'sales.php'; </script>";	
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
	$title = "Új project";
	$function = 2;
}
elseif ($project == 0) {
	$btn_type1 = "btn-default";
	$btn_type2 = "btn-success";
	$title = "Új megrendelés";
	$function = 1;
}

?>

<form method="post" name="myForm" action="<?php echo $_SERVER['PHP_SELF']; ?>" accept-charset="utf-8" onsubmit="return validateForm3()">

<div class="inputform">
	<div class="row">
		<div class="col-md-5">
		  <h3 style="margin-top:10px;"><?php echo $title; ?></h3>  
		</div>

		<div class="col-md-2">
		  	<button type="button" class="btn <?php echo $btn_type1; ?>" style="float:right; margin-left:5px; margin-top:5px;" onclick="document.location = 'order.php?project=1'">Projekt</button>
		  	<button type="button" class="btn <?php echo $btn_type2; ?>" style="float:right; margin-top:5px;" onclick="document.location = 'order.php'">Egyes</button>
			 
		</div>

		<?php
		if ($project == 1) {		// show additional fields for projects
		?>
			<div class="col-md-5">
				<div class="form-horizontal">
					<div class="form-group" style="margin-top:4px; margin-bottom: 0px;">
						<label for="projectname" class="col-sm-4 control-label" style="padding-bottom: 10px;">Projekt név</label>
						<div class="col-sm-6" style="padding-bottom: 10px;">
						  <input type="text" class="form-control" id="projectname" name="projectname" required>
						</div>
					</div>
				</div>

				<label class="radio-inline" style="margin-left: 200px;">
				  <input type="radio" name="project_status" value="0"> Ajánlat
				</label>
				<label class="radio-inline">
				  <input type="radio" name="project_status" value="1" checked> Megrendelés
				</label>
			</div>
		<?php
		}
		?>
	</div>


	<div class="row first">
		<div class="col-md-8">
			<label for="date">Nap<sup>*</sup></label>
			<table class="table table-bordered table-condensed" id="dateTable">
				<?php
				$days = array("Vasárnap", "Hétfő", "Kedd", "Szerda", "Csütörtök", "Péntek", "Szombat");
				$currentWeekNumber = date('W');
				$currentYear = date('Y');
				$today = date("Y-m-d");
				$currentWeekDay = date('w');

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

				    //get operations of the field     
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
						echo '<td class="active" id="datebutton'.$k.'"><label class="radio-inline"><input type="radio" id="datum'.$k.'" name="date" onclick="dateFunction('.$k.', '.$project.', '.$available_total.')" value="'.$fulldate.'">Ma<br><b>'.$date.'</b>';
						$k++;
					}
					elseif ($i == $currentWeekDay + 1) {
						echo '<td class="normal" id="datebutton'.$k.'"><label class="radio-inline"><input type="radio" id="datum'.$k.'" name="date" onclick="dateFunction('.$k.', '.$project.', '.$available_total.')" value="'.$fulldate.'">Holnap<br><b>'.$date.'</b>';
						$k++;
					}
					else {
						echo '<td class="normal" id="datebutton'.$k.'"><label class="radio-inline"><input type="radio" id="datum'.$k.'" name="date" onclick="dateFunction('.$k.', '.$project.', '.$available_total.')" value="'.$fulldate.'"> '.$days[$day].'<br><b>'.$date.'</b>';
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


						echo '<td class="normal" id="datebutton'.$k.'"><label class="radio-inline"><input type="radio" id="datum'.$k.'" name="date" onclick="dateFunction('.$k.', '.$project.', '.$available_total.')" value="'.$fulldate.'"> '.$days[$day].'<br><b>'.$date.'</b>';
					
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
			echo "<input type='hidden' value='".$n."' id='selectedtime'>";
				?>
			<div class="col-md-1"></div>
			<div class="col-md-2">
				<input type="radio" name="date" id="more" onclick="moreFunction()" value="100"> Egyedi dátum<br><br>
				<div id="orderdate" class="hide">
					<input type='date' class="form-control date-field" style="padding: 0px 10px;" id="moreDatum" onchange="moreRefresh(<?php echo $project; ?>)" name="moreDatum" min="<?php echo $today; ?>" value="<?php echo $today; ?>"><br>
				</div>

				<?php
				if ($project == 1) {		// decide if the date of the project is already fixed to a day or only planned within a certain month
				?>
					<br><br><br><br><br><br>
					<label class="radio-inline">
					  <input type="radio" name="datum_type" value="0" checked> Nap
					</label>
					<label class="radio-inline">
					  <input type="radio" name="datum_type" value="1"> Hónap
					</label>
					</div>
				<?php
				}
				?>
				<div class="col-md-2"></div>

		</div>

		<?php
		if ($project == 0) { 		// no time necessary for projects
		?>

		<div class="row second">
			<div class="col-md-12">
				<label for="date">Teljesítési időpont<sup>*</sup></label>
				<div id="time-output">
					
				</div>
			</div>
		</div>

		<input name="project" value="0" type="hidden"/>

		<?php
		}
		elseif ($project == 1) {		// set the variables for correct database entry
			echo '<input name="time" value="00:00:00" type="hidden"/>';
			echo '<input name="project" value="1" type="hidden"/>';
		}
		?>

		<div class="row first">
			<div class="col-md-4">
				<div class="form-group">
	          		<label for="customer">Vevő<sup>*</sup></label>
	          		<div class="col-sm-12" style="padding-bottom: 10px;">
		          		<?php
						if ($project == 0) { 		// no automatic selection of standard delivery and payment for projects
		          			echo '<input class="form-control" type="text" name="customer" id="customer_input" required />';
						}
						elseif ($project == 1) {
	        				echo '<input class="form-control" type="text" name="customer" id="customer_input2" required />';
						}
						?>
					</div>
        			<input name="customer_id" id="customer_id" value="0" type="hidden"/>
            	</div>
    			<br>
		        <div class="form-group hide" id="customer-group-1">
					<div class="col-sm-4" style="padding-bottom: 10px;">
					  <input type="number" class="form-control" name="customer_plz" id="customer_plz">
					</div>
					<div class="col-sm-8" style="padding-bottom: 10px;">
					  <input type="text" class="form-control" name="customer_city" id="customer_city">
					</div>
				</div>
				<br>
		        <div class="form-group hide" id="customer-group-2">
					<div class="col-sm-12" style="padding-bottom: 10px;">
					  <input type="text" class="form-control" name="customer_street" id="customer_street">
					</div>
				</div>
		    </div>
		    <div class="col-md-1"></div>
		    <div class="col-md-4">
		    	<div class="form-horizontal">
			        <div class="form-group hide" style="margin-bottom: 10px;" id="customer-group-3">
						<label for="contactperson" class="col-sm-4 control-label" style="padding-bottom: 0px; margin-top: 10px;">Kapcsolattartó</label>
						<div class="col-sm-8" style="padding-bottom: 0px; margin-top: 10px;">
						  <input type="text" class="form-control" id="contactperson" name="contactperson">
						</div>
					</div>
			        <div class="form-group hide" style="margin-bottom: 10px;" id="customer-group-4">
						<label for="telephone" class="col-sm-4 control-label" style="padding-bottom: 0px;">Telefon</label>
							<div class="col-sm-8" style="padding-bottom: 0px">
							  <input type="tel" class="form-control" id="telephone" name="telephone">
							</div>
					</div>
					<div class="form-group hide" id="customer-group-5">
						<label for="email" class="col-sm-4 control-label">Email</label>
						<div class="col-sm-8">
						  <input type="email" class="form-control" name="email" id="email">
						</div>
					</div>
				</div>
		    </div>
		    <div class="col-md-1"></div>
		    <div class="col-md-2">
		        <br>
		        <button type="button" class="btn btn-default" style="margin-top: 10px;" data-toggle="modal" data-target="#newModal"><span class="glyphicon glyphicon-plus"></span>&nbsp;&nbsp; Új vevő</button>
		    	<br>
		    	<p id="message" style="margin-top: 15px; width: 100px;"></p>
		    </div>
		</div>

		<div class="row second">
			<div class="col-md-2">

		        <?php
				if ($project == 1) {		// input also used for calculation of the number of trucks needed
				?>
					<div class="form-group">
		          		<label for="amount">Mennyiség (m&sup2;)<sup>*</sup></label>
		          		<input class="form-control" type="number" step="1" min="0" value="0" name="amount" id="amount" onchange="updateCalculation3()" required>
		        	</div>
			        <br>
			        <div class="form-group">
		          		<label for="amount">Hosszúság (m)</label>
		          		<input class="form-control" type="number" step="0.1" min="0" value="0" id="length" onchange="updateCalculation3()" name="length">
			        </div>
			    <?php
				}	
				elseif ($project == 0) {	// amount
					?>
					<div class="form-group">
		          		<label for="amount">Mennyiség (m&sup2;)<sup>*</sup></label>
		          		<input class="form-control" type="number" step="1" min="0" value="0" name="amount" required>
			        </div>
					<input name="length" value="0" type="hidden"/>
				<?php
				}
				?>

		    </div>
		    <div class="col-md-1"></div>
		    <div class="col-md-7">
		    	<label class="radio-inline">
				  	<input type="radio" name="type1" value="1" checked> Kistekercs
				</label>
				<!--
				<label class="radio-inline">
				  	<input type="radio" name="type1" value="2"> Kistekercs stadion
				</label>
				-->
				<label class="radio-inline">
				  	<input type="radio" name="type1" value="3"> Kistekercs vastag
				</label>

				<br>
				<label class="radio-inline">
				  	<input type="radio" name="type1" value="4"> Nagytekercs
				</label>
				<label class="radio-inline">
				  	<input type="radio" name="type1" value="5"> Nagytekercs 2,5 cm
				</label>
				<label class="radio-inline">
				  	<input type="radio" name="type1" value="6"> Nagytekercs 3 cm
				</label>
				<br><br>

				<label class="radio-inline">
				  	<input type="radio" name="type2" onchange="fieldRefresh()" value="1" checked> Nórmal (Poa)
				</label>
				<label class="radio-inline">
				  	<input type="radio" name="type2" onchange="fieldRefresh()" value="2"> Mediterrán
				</label>
				<br><br>

				<?php
				if ($project == 0 AND $modus == 1) {

					if ($cutting_modus == 2) {
				?>

					<label class="radio-inline">
					  	<input type="radio" name="pallet" value="1" checked> 50
					</label>
					<label class="radio-inline">
					  	<input type="radio" name="pallet" value="2"> 30
					</label>
					<label class="radio-inline">
					  	<input type="radio" name="pallet" value="3"> 56
					</label>

					<br><br>

				<?php
					}
					else {
						echo '<input name="pallet" value="1" type="hidden"/>';
					}
				?>

				<label class="radio-inline">
				  	<input type="radio" name="type3" value="1" required> I
				</label>
				<label class="radio-inline">
				  	<input type="radio" name="type3" value="2"> II
				</label>
				<label class="radio-inline">
				  	<input type="radio" name="type3" value="3"> Garancia
				</label>

				<input name="cooling" value="0" type="hidden"/>
				<input name="laying" value="0" type="hidden"/>

				<?php
				}
				elseif ($project == 1) {		// more options for projects
					echo '<input name="type3" value="1" type="hidden"/>';
					?>

					<div class="checkbox">
					    <label>
					      <input type="checkbox" value="1" name="cooling" id="cooling" onchange="updateCalculation3()"> Hűtő kamion
					    </label>
					</div>

					<div class="checkbox">
					    <label>
					      <input type="checkbox" value="1" name="laying"> Telepítés
					    </label>
					</div>

				<?php
				}
				else {		// mode 2
					echo '<input name="type3" value="1" type="hidden"/>';
					echo '<input name="pallet" value="1" type="hidden"/>';
				}
				?>
		    </div>
		    <div class="col-md-2">
		    	<div class="form-group">
	          		<label for="field">Terület</label>
			        <?php
			        $today = date('Y-m-d');

			        echo "<div id='field_select'>";		
		            echo '<select class="form-control" name="field">';
		            //echo '<option value="0">Válassz ki...</option>';
		            echo '<option value="111111">?</option>';
		            //echo '<option value="222222" selected>ZEHETBAUER</option>';

					if ($project == 0) {		// get all fields which are ready for cutting
		            	$query = $db->prepare("SELECT * FROM fields WHERE `complete` < 1 AND `cutting` = 1");
		            }
		            elseif ($project == 1) {	// get all fields for projects
		            	$query = $db->prepare("SELECT * FROM fields WHERE `complete` < 1");
		            }

		            $query->execute();
		            
		            // changed by javascript if type2 Mediterran is selected -> get only fields with right type2
		            while($row = $query->fetch()) {
		                $seed = $row['seed'];

			            $query2 = $db->prepare("SELECT * FROM seed WHERE `id` = :id");
			            $query2->bindParam(":id", $seed, PDO::PARAM_STR);
			            $query2->execute();
			            $result = $query2->fetch(PDO::FETCH_OBJ);
			            $field_type2 = $result->type2;

			            if ($field_type2 == 1) {
			            	echo "<option value='".$row['id']."'>".$row['name']."</option>";
			            }
		            }
		            echo '</select>';
		            echo "</div>";
			        ?>
        		</div>
		    </div>
		</div>

		<?php
		if ($project == 1) {	// set how many trucks on which day (javascript)
		?>

			<div class="row second">
				<div class="col-md-1">
					<label for="date">Tartam</label>
					<input type='number' class="form-control" id="projectLength" onchange="changeLength()" min="1" value="3">
			    </div>

			    <div class="col-md-1"></div>
			    <div class="col-md-2">
			    	<div id="calculation-output">
						
					</div>
					<br>
					<div id="count-output" class="hide">
						9 kamion tervezett
					</div>
					<input id="truckcount" value="9" type="hidden"/>
				</div>

			    <div class="col-md-8">
			    	<div id="truck-output">
						
					</div>
			    </div>

			</div>
		<?php
		}
		?>

		<div class="row first">
			
			<div class="col-md-2">
				<label for="delivery">Szállítás</label><br>

				<?php
				if ($project == 0) { 		// no delivery choice for projects
				?>
					<label class="radio-inline">
					  	<input type="radio" id="delivery1" name="delivery" onclick="deliveryFunction2()" value="1" checked> ABH
					</label>
					<label class="radio-inline">
					  	<input type="radio" id="delivery2" name="delivery" onclick="deliveryFunction()" value="2"> Szállítás
					</label>
				<?php
				}
				?>
		    </div>

		    <div class="col-md-6" style="padding-bottom: 0;">
				<div class="form-horizontal">
					<br>
					<?php
					if ($project == 0) { 		// not for projects
					?>
						<div class="form-group">
							<label for="deliveryname" class="col-sm-4 control-label" style="padding-bottom: 10px;">Szállítási név</label>
							<div class="col-sm-6" style="padding-bottom: 10px;">
							  <input type="text" class="form-control" id="deliveryname" name="deliveryname" placeholder="Név">
							</div>
						</div>
					<?php
					}
					?>

					<div class="form-group">
		          		<label for="country1" class="col-sm-4 control-label">Ország</label>
		          		<div class="col-sm-4" style="padding-bottom: 10px;">
					        <?php

				            echo '<select class="form-control" id="country1" name="country1" onchange="countryFunction()">'; // post code and city only for Hungarian orders
				            echo '<option value="0" selected>';
				            echo 'Magyarország';
				            echo "</option>";
				 
				            $query = $db->prepare("SELECT * FROM countries WHERE `id` > 0 AND (`type` = 1 OR `type` = 3) ORDER BY `name2` ASC");
				            $query->execute();
				            while($row = $query->fetch()) {
				                echo "<option value='".$row['id']."'>".$row['name2']."</option>";    
				            }

				            echo '</select>';
					        ?>
					    </div>
	        		</div>

					<div class="form-group">
						<label for="deliveryaddress" class="col-sm-4 control-label" style="padding-bottom: 10px;">Szállítási cím<sup>*</sup></label>
						<div id="address_input" class="col-sm-6" style="padding-bottom: 10px;">

						<?php
						if ($project == 1) { 	// for projects only one adress field instead of post code and city
							echo '<input type="text" class="form-control" id="deliveryaddress" name="deliveryaddress" placeholder="Cím" required>';
						}
						else {
							echo '<input type="text" class="form-control" id="deliveryaddress" name="deliveryaddress" placeholder="Utca" required>';
						}
						?>
						  
						</div>
					</div>

					<?php
					if ($project == 1) {
						echo '<div id="plz-group" class="form-group hide">';
					}
					else {
						echo '<div id="plz-group" class="form-group show">';
					}
					?>
					
			          		<label for="customer" class="col-sm-4 control-label" style="padding-bottom: 10px;">&nbsp;</label>
			          		<div id="plz_input2" class="col-sm-6" style="padding-bottom: 10px;">
			          			<?php
								if ($project == 1) {
									echo '<input class="form-control" type="text" name="plz" id="plz_input" placeholder="Irányítószám / Város" />';
								}
								else {
									echo '<input class="form-control" type="text" name="plz" id="plz_input" placeholder="Irányítószám / Város" required />';
								}
								?>
			          			
			          		</div>

	            			<input name="city_id" id="city_id" value="0" type="hidden"/>
			        </div>
					<?php
					if ($project == 0) { 	// enter licence number not for projects
					?>
				        <div class="form-group">
							<label for="time" class="col-sm-4 control-label" style="padding-bottom: 10px;">Rendszám</label>
				          	<div class="col-sm-5" style="padding-bottom: 10px;">
				          		<input type="text" class="form-control" name="licence" id="licence">
				        	</div>
						</div>
					<?php
					}
					?>
					
				</div>
		    </div>

		    <div class="col-md-1"></div>
		    <div id="deliveryAgent" class="col-md-3 hide">
		    	<div class="col-md-6">
			    	<br><label for="forwarder">Fuvaros</label><br>

			    	<?php
			    	$query = $db->prepare("SELECT * FROM forwarder WHERE `id` = 1");
		            $query->execute();
		            $result = $query->fetch(PDO::FETCH_OBJ);
	    			echo '<div class="radio"><label>';
		            echo '<input type="radio" name="forwarder" onclick="forwarderFunction(1)" value="1" checked>'.$result->name."</label></div>";

		            $k = 1;
			    	$query = $db->prepare("SELECT * FROM forwarder WHERE `status` = 1 AND `id` > 1 ORDER BY `id` ASC");
		            $query->execute();
		            while($row = $query->fetch()) {
		                echo '<div class="radio"><label>';
		                echo '<input type="radio" name="forwarder" onclick="forwarderFunction('.$row['id'].')" value="'.$row['id'].'">'.$row['name']."</label></div>";
		                
		                if ($k == 7) {		// get second column
		                	echo '</div><br><br><div class="col-md-6">';
		                }
		                $k++;
		            }
			    	?>
			    </div>
		    </div>
		</div>


		<div class="row second">
			
			<div class="col-md-3">
				<label for="payment">Fizetési mód</label><br>
				<label class="radio-inline">
				  	<?php
					if ($project == 0) { 		// atutalas as standard for projects for projects
				  		echo '<input type="radio" id="payment1" name="payment" value="1" checked> Kézpénz';
				  	} 
				  	else {
				  		echo '<input type="radio" id="payment1" name="payment" value="1"> Kézpénz';
				  	}
				  	?>

				</label>
				<label class="radio-inline">
					<?php
					if ($project == 0) { 		// atutalas as standard for projects for projects
				  		echo '<input type="radio" id="payment2" name="payment" value="2"> Átutalás';
				  	} 
				  	else {
				  		echo '<input type="radio" id="payment2" name="payment" value="2" checked> Átutalás';
				  	}
				  	?>
				</label>

				<br>
				<div class="form-horizontal">
					<div class="form-group" style="margin-top: 32px;">
						<label for="invoicenumber" class="col-sm-3 control-label" style="padding-bottom: 10px;">Sz.sz.: </label>
						<div class="col-sm-6" style="padding-bottom: 10px;">
						  <input type="number" class="form-control" name="invoicenumber" step="1" min="0" value="0">
						</div>
					</div>
				</div>
		    </div>

		    <?php
		    /*
			if ($project == 0) { 		// no payment details for projects
			?>
			    <div id="paymentDetails" class="col-md-6">
					<div class="form-horizontal">
						<br>
						<div class="form-group">
							<label for="invoicename" class="col-sm-4 control-label" style="padding-bottom: 10px;">Számlázási név</label>
							<div class="col-sm-6" style="padding-bottom: 10px;">
							  <input type="text" class="form-control" id="invoicename" name="invoicename" placeholder="Név">
							</div>
						</div>

						<div class="form-group">
							<label for="invoiceaddress" class="col-sm-4 control-label" style="padding-bottom: 10px;">Számlázási cím</label>
							<div class="col-sm-8" style="padding-bottom: 10px;">
							  <input type="text" class="form-control" id="invoiceaddress" name="invoiceaddress" placeholder="Cím">
							</div>
						</div>
					</div>
			    </div>

			    <div class="col-md-3">
					<button type="button" class="btn btn-default" style="margin-top: 20px;" onclick="invoiceRefresh()">Vevő neve</button>
					<br><br>

					<button type="button" class="btn btn-default" style="margin-top: 5px;" onclick="invoiceRefresh2()">Ugyanaz, mint a szállítási</button>
			    </div>
		    <?php
			}
			*/
			?>
		</div>

		<div class="row first">
	        
			<?php
			if ($project == 0) {
			?>
	        <div class="col-md-3 formrow" style="padding-bottom: 10px;">
		        <div class="form-group">
		            <label for="exampleTextarea">Jegyzet (kint)</label>
		            <textarea class="form-control" id="note" name="note" rows="3" placeholder="A vágás csoportnak"></textarea>
		        </div>

		        <button type="button" class="btn btn-default" onclick="noteRefresh2(1)">még visz</button>
		        <button type="button" class="btn btn-default" onclick="noteRefresh2(2)">folytatás</button>
	        </div>

	        <?php
			}
			elseif ($project == 1) {	// no note for team outside possible
				echo '<input name="note" value="" type="hidden"/>';
			}
			?>
	        <div class="col-md-3 formrow" style="padding-bottom: 10px;">
		        <div class="form-group">
		            <label for="exampleTextarea">Jegyzet (iroda)</label>
		            <textarea class="form-control" id="note2" name="note2" rows="3" placeholder=""></textarea>
		        </div>

		        <?php
				if ($project == 0) {
				?>
		        	<button type="button" class="btn btn-default" onclick="noteRefresh()">Ugyanaz, mint kint</button>
		        <?php
				}
				?>
	        </div>
	        <div class="col-md-1"></div>
	        <div class="col-md-5" style="padding-bottom: 10px;">
		        <?php
				/*

		        <div class="form-horizontal">
					<br>
					<div class="form-group">
						<label for="telephone" class="col-sm-4 control-label" style="padding-bottom: 10px;">Telefon</label>
						<div class="col-sm-7" style="padding-bottom: 10px;">
						  <input type="tel" class="form-control" name="telephone" placeholder="Telefon">
						</div>
					</div>

					<?php
					if ($project == 0) { 		// not for projects
					?>
						<div class="form-group">
							<label for="email" class="col-sm-4 control-label" style="padding-bottom: 10px;">Email</label>
							<div class="col-sm-7" style="padding-bottom: 10px;">
							  <input type="email" class="form-control" name="email" placeholder="Email">
							</div>
						</div>
					<?php
					}
					?>
				</div>
				*/
				?>
	        </div>
	    </div>
	    <br><br>

	    <div class="row">
			<div class="col-md-2">
				<button type="submit" class="btn btn-primary" name="orderForm" value="Submit">Küldés</button>
			</div>
		</div>
    </form>


<!-- Modal for adding new customer -->
<div class="modal fade" id="newModal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document" style="width: 600px;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Új vevő</h4>
      </div>

      <div class="modal-body"> 
        
      	<form id="addForm" method="" action="" novalidate="novalidate">

        <div class="row">
          	<div class="col-md-7">
	          	<div class="form-horizontal">
	            	<div class="form-group">
	              		<label for="name" class="col-sm-4 control-label" style="padding-bottom: 10px;">Név <sup>*</sup></label>
	              		<div class="col-sm-8" style="padding-bottom: 10px;">
	              			<input type="text" id="customer_name">
	              		</div>
	            	</div>
	            	<div class="form-group">
	              		<label for="contactperson2" class="col-sm-4 control-label" style="padding-bottom: 10px;">Kapcsolattartó</label>
	              		<div class="col-sm-8" style="padding-bottom: 10px;">
	              			<input type="text" id="contactperson2">
	              		</div>
	            	</div>
	            	<div class="form-group">
	              		<label for="customer_plz2" class="col-sm-4 control-label" style="padding-bottom: 10px;">Irányítószám</label>
	              		<div class="col-sm-4" style="padding-bottom: 10px;">
	              			<input type="number" id="customer_plz2" style="width: 100px;">
	              		</div>
	            	</div>
	            	<div class="form-group">
	              		<label for="customer_city2" class="col-sm-4 control-label" style="padding-bottom: 10px;">Város</label>
	              		<div class="col-sm-8" style="padding-bottom: 10px;">
	              			<input type="text" id="customer_city2">
	              		</div>
	            	</div>
	            	<div class="form-group">
	              		<label for="customer_street2" class="col-sm-4 control-label" style="padding-bottom: 10px;">Utca</label>
	              		<div class="col-sm-8" style="padding-bottom: 10px;">
	              			<input type="text" id="customer_street2">
	              		</div>
	            	</div>
	            	<div class="form-group">
	              		<label for="telephone2" class="col-sm-4 control-label" style="padding-bottom: 10px;">Telefon</label>
	              		<div class="col-sm-8" style="padding-bottom: 10px;">
	              			<input type="text" id="telephone2">
	              		</div>
	            	</div>
	            	<div class="form-group">
	              		<label for="email2" class="col-sm-4 control-label" style="padding-bottom: 10px;">Email</label>
	              		<div class="col-sm-8" style="padding-bottom: 10px;">
	              			<input type="text" id="email2">
	              		</div>
	            	</div>
	          	</div>
        	</div>


          	<div class="col-md-5">
		    	<b>Szállítás - Standard</b><br>
		    	<label class="radio-inline">
				  	<input type="radio" name="delivery_standard" value="1" checked> ABH
				</label>
				<label class="radio-inline">
				  	<input type="radio" name="delivery_standard" value="2"> Szállítás
				</label>
				
				<br><br>

				<b>Fizetési mód - Standard</b><br>
				<label class="radio-inline">
				  	<input type="radio" name="payment_standard" value="1" checked> Kézpénz
				</label>
				<label class="radio-inline">
				  	<input type="radio" name="payment_standard" value="2"> Átutalás
				</label>

				<br><br>

				<div class="form-group">
	          		<label for="country">Ország</label>
			        <?php

		            echo '<select class="form-control" id="country">';
		            echo '<option value="0" selected>';
		            echo 'magyar';
		            echo "</option>";
		 
		            $i = 0;
		            $query = $db->prepare("SELECT * FROM countries WHERE `type` < 3");
		            $query->execute();
		            while($row = $query->fetch()) {
		                if ($i > 0) {
		                	echo "<option value='".$row['id']."'>".$row['name']."</option>";
		                }  
		                $i++;     
		            }

		            echo '</select>';
			        ?>
        		</div>

	        	<br>

				<div class="form-group">
	          		<label for="country">Környék</label>
			        <?php

		            echo '<select class="form-control" name="area" id="area">';
		            echo '<option value="0" selected>';
		            echo "Budapest";
		            echo "</option>";
		 
		            $j = 0;
		            $query = $db->prepare("SELECT * FROM areas");
		            $query->execute();
		            while($row = $query->fetch()) {
		                if ($j > 0) {
		                	echo "<option value='".$row['id']."'>".$row['name']."</option>";
		                }  
		                $j++;     
		            }

		            echo '</select>';
			        ?>
        		</div>

        	</div>
      </div>
      <div class="modal-footer">
      	<button type="button" class="btn btn-default" name="insert-data" id="insert-data" onclick="insertData()">Küldés</button>
      </div>
      </form>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->


	<br><br><br><br><br><br><br><br><br><br>

</div>
</div>
</div>

  


