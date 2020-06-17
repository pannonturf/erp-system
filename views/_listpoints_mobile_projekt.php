<?php
/////////////////////////////////////////////////////////////
// Single project rows of the order tables for mobile view //
/////////////////////////////////////////////////////////////

// compact view

$a = array();

$query = $db->prepare("SELECT * FROM `trucks` WHERE `datum` = :datum ORDER BY `sort` DESC");
$query->bindParam(":datum", $datum, PDO::PARAM_STR);
$query->execute();

foreach ($query as $row) {
	$projectid = $row['project'];

	if (!in_array($projectid, $a)) {
	    array_push($a, $projectid);
	}
}

foreach ($a as $projectid) {
	$query = $db->prepare("SELECT * FROM `order` WHERE `project_id` = :projectid");
	$query->bindParam(":projectid", $projectid, PDO::PARAM_STR);
	$query->execute(); 
	$result = $query->fetch(PDO::FETCH_OBJ);

	
	$today_amount = 0;
	$query2 = $db->prepare("SELECT * FROM `trucks` WHERE `datum` = :datum AND `project` = :projectid");
	$query2->bindParam(":datum", $datum, PDO::PARAM_STR);
	$query2->bindParam(":projectid", $projectid, PDO::PARAM_STR);
	$query2->execute();
	$truck_amount = $query2->rowCount();
	foreach ($query2 as $row2) {
		$today_amount += $row2['amount'];
	}
	
	
	$id = $result->id;
	$projectname = $result->projectname;
	$customer = $result->name;
	$amount = amount_decrypt($result->amount, $key2);
	$type1 = $result->type1;
	$type2 = $result->type2;
	$field = $result->field;
	$delivery = $result->delivery;
	$forwarder = $result->forwarder;
	$payment = $result->payment;
	$paid = $result->paid;
	$status = $result->status;
	$length = $result->length;

	$deliveryname = $result->deliveryname;
	$deliveryaddress = $result->deliveryaddress;
	$country = $result->country;
	$telephone = $result->telephone;
	$email = $result->email;
	$invoicenumber = $result->invoicenumber;
	$created = $result->created;
	$creator = $result->creator;
	$note2 = $result->note2;

	if ($note2 == "") {
		$note2_display = $note2;
	}
	else {
		$note2_display = '<mark style="background: #89ec7f;">'.$note2.'</mark>';
	}
	
	$completedate = substr($result->completedate, 0, 16);
	$completer = $result->completer;

	$query2 = $db->prepare("SELECT * FROM customers WHERE `id` = :id");
	$query2->bindParam(":id", $customer, PDO::PARAM_STR);
	$query2->execute();
	$result2 = $query2->fetch(PDO::FETCH_OBJ);
	$customer_display = $result2->name;
	echo '<tr data-href="project.php?id='.$projectid.'" class="project_title"><td colspan="2">Projekt<br><b>'.$projectname."</b><br>";
	echo "<i>".$customer_display."</i></td>";

	echo '<td><b>'.$today_amount." m&sup2;</b><br>";

	$query = $db->prepare("SELECT * FROM fields WHERE `id` = :id");
	$query->bindParam(":id", $field, PDO::PARAM_STR);
	$query->execute();
	$result = $query->fetch(PDO::FETCH_OBJ);
	echo $result->name."<br>";

	if ($type1 == 1) {
		$type1_display = "<mark style='background: #ff9c2e; color: white;'>KR</mark>";
	}
	elseif ($type1 == 0) {
	 	$type1_display = "err1";
	}
	elseif ($type1 == 2) {
	 	$type1_display = "<mark style='background: #ff9c2e; color: white;'>KR</mark>";
	}
	elseif ($type1 == 3) {
	 	$type1_display = "<mark style='background: #ff9c2e; color: white;'>KR 2,5 cm</mark>";
	} 
	elseif ($type1 == 4) {
	 	$type1_display = "<mark style='background: #468dc9; color: white;'>GR</mark>";
	} 
	elseif ($type1 == 5) {
	 	$type1_display = "<mark style='background: #468dc9; color: white;'>GR 2,5</mark>";
	} 
	elseif ($type1 == 6) {
	 	$type1_display = "<mark style='background: #468dc9; color: white;'>GR 3</mark>";
	} 
	

	if ($type1 > 3) {
		echo $type1_display."<br>(";
		echo "<i>".$length." m)</i></td>";
	}

	echo "<td colspan='2' style='min-width: 100px;'>";

	// display pictures of trucks
	$query2 = $db->prepare("SELECT * FROM `trucks` WHERE  `project` = :projectid AND `datum` = :datum ORDER BY `sort` DESC");
	$query2->bindParam(":projectid", $projectid, PDO::PARAM_STR);
	$query2->bindParam(":datum", $datum, PDO::PARAM_STR);
	$query2->execute();

	$trucks_fix = 0;
	$trucks_amount = $query2->rowCount();
	$truck_disp = "";
	
	foreach ($query2 as $row) {
		$truck_status = $row['status'];
		
		if ($truck_status == 3) {
		 	$truck_disp .= '<img src="../img/truck_green.png" class="truck_m"">';
		 	$trucks_fix++;
		}
		elseif ($truck_status == 2) {
		 	$truck_disp .= '<img src="../img/truck_orange.png" class="truck_m"">';
		 	$trucks_fix++;
		}
		else {
			$truck_disp .= '<img src="../img/truck.png" class="truck_m">';
		} 	
	}

	echo $truck_disp;

	echo "</td></tr>";

	$i++;
}

?>