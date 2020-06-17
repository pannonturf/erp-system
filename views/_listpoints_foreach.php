<?php
////////////////////////////////////////////////////
// Single rows of the order tables - foreach loop //
////////////////////////////////////////////////////

$id = $row['id'];
$id2 = $row['id2'];
$id3 = $row['id3'];
$projectid = $row['project_id'];
$prefix = $row['prefix'];
$datum = $row['date'];
$time = $row['time'];
$timedisplay = substr($time, 0, 5);
$name = $row['name'];
$type1 = $row['type1'];
$type2 = $row['type2'];
$type3 = $row['type3'];
$pallet = $row['pallet'];
$pickup = $row['pickup'];

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
$city = $row['city'];
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
$ekaer = $row['deliverynote'];
$licence = $row['licence'];

if ($projectid > 0) {
		$note2_display = '<mark style="background: #89ec7f;">'.$projectname.'</mark>';
}
else {
	if ($note2 == "") {
		$note2_display = $note2;
	}
	elseif ($customer_page == 1 OR $history_page == 1) {		// no highlighting on customer and history page
		$note2_display = $note2;
	}
	else {
		$note2_display = '<mark style="background: #89ec7f;">'.$note2.'</mark>';
	}
}

$planneddate = substr($row['planneddate'], 0, 16);
$cutdate = substr($row['cutdate'], 0, 16);
$completedate = substr($row['completedate'], 0, 16);
$receivedate = substr($row['receivedate'], 0, 16);
$pickupdate = substr($row['pickupdate'], 0, 16);
$completer = $row['completer'];
$receiver = $row['receiver'];
$loader = $row['loader'];
$team = $row['team'];


$status_display = "";
$check_project = 0;

// Get customer data
$query = $db->prepare("SELECT * FROM customers WHERE `id` = :id");
$query->bindParam(":id", $name, PDO::PARAM_STR);
$query->execute();
$result = $query->fetch(PDO::FETCH_OBJ);
$name_display = $result->name;
$contactperson = $result->contactperson;
$customer_street = $result->street;
$customer_plz = $result->plz;
$customer_city = $result->city;
$customer_country = $result->country;
$customer_phone = $result->phone;
$customer_email = $result->email;
$bulk_invoice = $result->bulk;


if ($customer_page != 1) {
	if ($projectid > 0 AND ($check < 3 OR $check2 == 1)) {		// only today, tomorrow and day after
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
}

if ($check_project == 0 AND ($check < 4 OR ($check == 4 AND ($bulk_invoice == 0 OR ($bulk_invoice == 1 AND $status == 0))))) {
	
	// check if enough pallets are cut (cutting mode 2)
	if ($cutting_modus == 2 AND $type1 < 4 AND $type2 == 1 AND $status > 0) {
		if ($pickup == 0) {
			$cumulated[$field][$pallet] += $amount;
		}

		$inventory = $cut[$field][$pallet] - $pickedup[$field][$pallet];	// inventory
		$amount_left = $inventory - $cumulated[$field][$pallet];			// consecutive order
		$amount_left2 = $inventory - $amount;								// enough inventory to hand out this order?

		if ($amount_left >= 0) {
			$status_calculated = 3;
		}
		else {
			$status_calculated = 1;
		}

		if ($amount_left2 >= 0) {
			$ready = 1;
		}
		else {
			$ready = 0;
		}

	}

	//////////////
	// Projekt view future and normal order view
	if ($projectid > 0) {
		
		if ($stripe == 1) {
			echo '<tr class="stripe" data-href="project.php?id='.$projectid.'">';
		}
		else {
			echo '<tr data-href="project.php?id='.$projectid.'">';
		}
		
		if ($customer_page == 1) {	// one column more for Project view
			echo '<td><mark style="background: #429741; color: white;">Projekt</mark></td>';

			echo '<td data-toggle="collapse" data-target="#order'.$i.'" class="accordion-toggle" colspan="2">'.$datum.'</td>';
		}
		else {
			echo '<td colspan="2"><mark style="background: #429741; color: white;">Projekt</mark></td>';
		}
	}
	else {		// normal view
		if ($status == 0) {		// check if order is put on hold
			echo '<tr class="paused2">';
		}
		elseif ($stripe == 1) {
			echo '<tr class="stripe">';		// stripped table
		}
		else {
			echo '<tr>';
		}

		// Show order number if already available
		if ($cutting_modus == 1) {
			$status1 = 2;
			$status2 = 3;
		}
		elseif ($cutting_modus == 2) {
			$status1 = 4;
			$status2 = 4;

			$id3_display = substr($id3, -2);

			if ($id3_display == "00") {
				$id3_display = 100;
			}
		}

		if ($id2 > 0 AND $cutting_modus == 1) {		// day prefix + number - cutting mode 1
			echo '<td data-toggle="collapse" data-target="#order'.$i.'" class="accordion-toggle"><b>'.$prefix."-".$id2."</b>";
			
		}
		elseif ($id3 > 0 AND $cutting_modus == 2) {		// number running from 1 - 100 - cutting mode 2
			echo '<td data-toggle="collapse" data-target="#order'.$i.'" class="accordion-toggle" style="padding-left: 10px;"><b>'.$id3_display."</b>";
		}
		
		elseif ($type1 < 4 AND $check < 3) {			// show button to assign an order number manually
			if ($check == 1) {
				echo '<td><button class="btn btn-default btn-xs" style="border-radius:10px;" onclick="statusFunction('.$id.', 1, '.$status1.')"><span class="glyphicon glyphicon-asterisk"></span></button>';
			}
			elseif ($check == 2) {
				echo '<td><button class="btn btn-default btn-xs" style="border-radius:10px;" onclick="statusFunction('.$id.', 1, '.$status2.')"><span class="glyphicon glyphicon-asterisk"></span></button>';
			}
		}

		else {
			echo '<td data-toggle="collapse" data-target="#order'.$i.'" class="accordion-toggle">';		// no order number yet
		}
		
		echo '</td>';

		// Show date on customer page
		if ($customer_page == 1) {
			echo '<td  data-toggle="collapse" data-target="#order'.$i.'" class="accordion-toggle">'.$datum.'</td>';
		}

		// Time
		echo '<td  data-toggle="collapse" data-target="#order'.$i.'" class="accordion-toggle">'.$timedisplay.'</td>';
	}

	echo '<td data-toggle="collapse" data-target="#order'.$i.'" class="accordion-toggle" style="max-width:200px;">';

	if ($customer_page != 1) {
		echo '<a href="https://turfgrass.site/customer.php?customer_id='.$name.'&fromDatum='.$currentyear.'-01-01&toDatum='.$currentyear.'-12-31&search-customer=Submit">';
	}
	echo $name_display."</a></td>";


	// Amount
	echo '<td><input type="checkbox" id="sum_'.$i.'" value="'.$amount.'" onclick="addAmounts()"> '.$amount." m&sup2;</td>";


	// Type 1
	if ($type1 == 1) {			// small rolls
		$type_display = "";
	}
	elseif ($type1 == 0) {		// error
	 	$type_display = "err1";
	}
	elseif ($type1 == 2) {		// small rolls stadion (not in ha penz)
	 	$type_display = "";
	}	
	elseif ($type1 == 3) {		// small rolls 2,5 cm
	 	$type_display = "<mark style='background: #468dc9; color: white;'>2,5 cm</mark>";
	} 
	elseif ($type1 == 4) {		// big rolls
	 	$type_display = "<mark style='background: #468dc9; color: white;'>Nagy</mark>";
	} 
	elseif ($type1 == 5) {		// big rolls thick
	 	$type_display = "<mark style='background: #468dc9; color: white;'>Nagy 2,5</mark>";
	} 
	elseif ($type1 == 6) {		// big rolls XL
	 	$type_display = "<mark style='background: #468dc9; color: white;'>Nagy 3</mark>";
	} 

	// echo '<td  data-toggle="collapse" data-target="#order'.$i.'" class="accordion-toggle">'.$type1_display."</td>";


	// Type 2
	if ($type2 == 1) {
		$type_display .= "";		// Poa
	}
	elseif ($type2 == 0) {
		$type_display .= "err2";	// error
	}
	elseif ($type2 == 2) {			// Mediterran
	 	$type_display .= "<mark style='background: #f62323; color: white;'>MED</mark>";
	} 

		// show pallet size
	if ($cutting_modus == 2) {
		if ($pallet == 2) {
			$type_display .= "<mark style='background: #2389f6; color: white;'>30</mark>";
		}
		elseif ($pallet == 3) {
			$type_display .= "<mark style='background: #2389f6; color: white;'>56</mark>";
		}

	}

	echo '<td  data-toggle="collapse" data-target="#order'.$i.'" class="accordion-toggle">'.$type_display."</td>";


	// Type 3
	if ($type3 == 1) {
		$type3_display = "";
	}
	elseif ($type3 == 0) {
		$type3_display = "err3";
	}
	elseif ($type3 == 2) {
		if ($modus == 2) {
	 		$type3_display = "II";
	 	}
	 	else {
	 		$type3_display = "<mark style='background: #ff9c2e;'>II</mark>";
	 	}
	} 
	elseif ($type3 == 3) {
	 	if ($modus == 2) {
	 		$type3_display = "Garancia";
	 	}
	 	else {
	 		$type3_display = "<mark style='background: #ff9c2e;'>Garancia</mark>";
	 	}
	} 
	echo '<td  data-toggle="collapse" data-target="#order'.$i.'" class="accordion-toggle">'.$type3_display."</td>";


				// Field
	$query = $db->prepare("SELECT * FROM fields WHERE `id` = :id");
	$query->bindParam(":id", $field, PDO::PARAM_STR);
	$query->execute();
	$result = $query->fetch(PDO::FETCH_OBJ);

	if ($field == 111111) {			// no field assigned yet
		$field_display = "?";
	}
	elseif ($field == 222222) {		// import product
		$field_display = "ZEHETBAUER";
	}
	else {
		$field_display = $result->name;
	}

	if ($cutting_modus == 2 AND $type1 < 4 AND $type2 == 1 AND $status == 1 AND $check < 3) {
		$field_display .= " <i>(".$inventory.")</i>";
	}

	echo '<td  data-toggle="collapse" data-target="#order'.$i.'" class="accordion-toggle">'.$field_display."</td>";


	// Delivery	method
	if ($delivery == 1) {			// self collection
		$delivery_display = "ABH";
	}
	elseif ($delivery == 2) {		// delivery
		$query = $db->prepare("SELECT * FROM forwarder WHERE `id` = :id");
        $query->bindParam(":id", $forwarder, PDO::PARAM_STR);
        $query->execute();
        $result = $query->fetch(PDO::FETCH_OBJ);
		$delivery_display = $result->name;

		if ($forwarder == 1) {
			$delivery_display = "<mark style='background: #f62323; color: white;'>".$result->name."</mark>";
		}
	} 
	echo '<td  data-toggle="collapse" data-target="#order'.$i.'" class="accordion-toggle">'.$delivery_display."</td>";


	// Payment method
	if ($payment == 1) {
		$payment_display = "kp";
	}
	elseif ($payment == 2) {
	 	$payment_display = "Átutalás";
	} 
	echo '<td  data-toggle="collapse" data-target="#order'.$i.'" class="accordion-toggle">'.$payment_display."</td>";


	// Notes for office staff
	if ($invoicenumber != 0) {		// show invoice number if available
		echo '<td  data-toggle="collapse" data-target="#order'.$i.'" class="accordion-toggle note"><i><mark style="background: #89ec7f;">'.$invoicenumber.'</mark>';

		if ($note2 != "") {
			echo " | ";
		}

		echo $note2_display."</i></td>";
	}
	else {
		echo '<td  data-toggle="collapse" data-target="#order'.$i.'" class="accordion-toggle note"><i>'.$note2_display."</i></td>";
	}


	// Order status
	if ($cutting_modus == 2 AND $type1 < 3  AND $check < 3 AND $type2 == 1) {
		if ($status == 0) {			// order is on hold
			$status_display = '<button class="btn btn-complete btn-sm paused_btn" onclick="statusFunction('.$id.', 1, 1)"><span class="glyphicon glyphicon-time"></span></button>';
		}
		elseif ($status == 4) {		// order has been collected in office
			$status_display = '<span class="glyphicon glyphicon-ok"></span>';
		}
		else {		// order is picked up
			$status_display .= ' &nbsp; &nbsp; ';
		}
		
	}

	else {

		if ($status == 0) {			// order is on hold
			$status_display = '<button class="btn btn-complete btn-sm paused_btn" onclick="statusFunction('.$id.', 1, 1)"><span class="glyphicon glyphicon-time"></span></button>';
		}
		elseif ($status == 1) {		// cutting has not started yet
		 	$status_display = '<img src="../img/yellow.png" class="picture_status">'; 
		} 
		elseif ($status == 2) {		// order in progress
		 	$status_display = '<img src="../img/orange.png" class="picture_status">'; 
		} 
		elseif ($status == 3 AND $type2 == 2) {		// order ready for collection
		 	$status_display = ' &nbsp; &nbsp; &nbsp; <img src="../img/green.png" class="picture_status">'; 
		} 
		elseif ($status == 3) {		// order ready for collection
		 	$status_display = '<img src="../img/green.png" class="picture_status">'; 
		} 
		elseif ($status == 4) {		// order has been collected
			$status_display = '<span class="glyphicon glyphicon-ok"></span>';
		} 
	}


	if ($cutting_modus == 2 AND $type1 < 3  AND $check < 3 AND $status > 0) {

		if ($type2 == 2) {		// show star also for MED
			if ($status == 4) {
				$status_display .= ' &nbsp; </span><span class="glyphicon glyphicon-asterisk"></span>';
			}
		}
		elseif ($pickup == 1) {		// order is picked up
			$status_display .= ' &nbsp; </span><span class="glyphicon glyphicon-asterisk"></span>';
		}
		elseif ($status_calculated == 1) {		// cutting has not started yet
		 	$status_display .= ' &nbsp; <img src="../img/yellow.png" class="picture_status">'; 
		} 
		elseif ($status_calculated == 3) {		// order ready for collection
		 	$status_display .= ' &nbsp; <img src="../img/green.png" class="picture_status">'; 
		} 
		
	}

	if ($paid == 1) {		// customer has already paid (only for cash payments) 
		$status_display .= "&nbsp; Fiz.";
	}
	else {
		$status_display .= " &nbsp; &nbsp; &nbsp; &nbsp; ";
	}

	echo '<td style="text-align:center;">'.$status_display."</td>";

	// Buttons
	//if ($sales == 1) {		// show edit only for sales.php
		if (isset($_GET['open'])) {		// set right link 
			$edit_pencil = '<button type="button" data-href="'.$edit_link.'open=1&edit='.$id.'" class="btn btn-default btn-xs" style="float: right;"><span class="glyphicon glyphicon-pencil"></span></button>';
			$link2 = $edit_link.'open=1&';
		}
		else {
			$edit_pencil = '<button type="button" data-href="'.$edit_link.'edit='.$id.'" class="btn btn-default btn-xs" style="float: right;"><span class="glyphicon glyphicon-pencil"></span></button>';
			$link2 = $edit_link;
		}
	/*
	}
	else {		// show edit modal for other pages
		$edit_pencil = '<button type="button" class="btn btn-default btn-xs" onclick="stopRefresh()" style="float: right;" role="group" data-toggle="modal" data-target="#editOrderModal'.$i.'" data-id="'.$id.'"><span class="glyphicon glyphicon-pencil"></span></button>';

	}
	*/
	// show delivery note
	if (empty($ekaer)) {	// no delivery note created yet
		$ekaer_btn = '<button type="button" data-href="'.$link2.'note='.$id.'" class="btn btn-default btn-xs" style="float: right; margin-right: 3px;"><span class="glyphicon glyphicon-print"></span></button>';
	}
	else {					
		// show delivery note
		$ekaer_btn = '<button type="button" data-href="'.$link2.'note='.$id.'" class="btn btn-success btn-xs" style="float: right; margin-right: 3px;"><span class="glyphicon glyphicon-print"></span></button>';
	}	
		

	if ($projectid > 0) {	// for projects in future, only show edit button
		echo "<td>";
		echo '<button type="button" class="btn btn-default btn-xs" style="float: right;"><span class="glyphicon glyphicon-pencil"></span></button>';

	}
	elseif ($check < 3 OR $check == 4) {	// all buttons only for today and open orders
		echo "<td style='min-width: 100px;'>";
		echo $edit_pencil;
		echo $ekaer_btn;

		if ($check == 1 OR $check == 4) {
			if ($type3 == 3 AND $status == 3) {		// for guarantee
				// Collected, not paid (not needed)
				echo '<button class="btn btn-warning btn-xs" style="float: right; margin-right: 5px; background-color: red;" onclick="statusFunction('.$id.', 4, 1)"><span class="glyphicon glyphicon-ok"></span></button>';
			}
			else {
				if ($status == 3 OR ($status == 1 AND $type1 > 2) OR ($status == 1 AND $type2 == 1 AND $cutting_modus == 2)) {		// if product is ready for collection OR big rolls in general 
					if ($payment == 1) {	// if cash payment
						if ($paid == 0) {	// if not paid yet
							// Collected, paid
							echo '<button class="btn btn-success btn-xs" style="float: right; margin-right: 5px" onclick="statusFunction('.$id.', 4, 2)"><span class="glyphicon glyphicon-ok"></span></button>';

							// Collected, not paid
							echo '<button class="btn btn-warning btn-xs" style="float: right; margin-right: 5px; background-color: red;" onclick="statusFunction('.$id.', 4, 1)"><span class="glyphicon glyphicon-ok"></span></button>';
						}
						else {		// already paid
							// Collected
							echo '<button class="btn btn-success btn-xs" style="float: right; margin-right: 5px" onclick="statusFunction('.$id.', 4, 5)"><span class="glyphicon glyphicon-ok"></span></button>';
						}

					}
					elseif ($payment == 2) {	// bank transfer
						
						if ($paid == 0 AND $type3 == 2 AND $bulk_invoice == 0) {		// collected, II paid
							echo '<button class="btn btn-success btn-xs" style="float: right; margin-right: 5px" onclick="statusFunction('.$id.', 4, 2)"><span class="glyphicon glyphicon-ok"></span></button>';
						}	
						

						// Collected, payment not known (bank)
					 	echo '<button class="btn btn-warning btn-xs" style="float: right; margin-right: 5px; background-color: red;" onclick="statusFunction('.$id.', 4, 1)"><span class="glyphicon glyphicon-ok"></span></button>';
					} 		
				}
				elseif ($status == 4 AND $paid == 0) {	// already collected, not paid yet
					if ($payment == 1) {		// cash
						// Payment button
						echo '<button class="btn btn-success btn-xs" style="float: right; margin-right: 5px" onclick="statusFunction('.$id.', 4, 3)">Fiz.</button>';
					}
					
					elseif ($payment == 2 AND $type3 == 2 AND $bulk_invoice == 0) { 	// already collected, II not paid yet
						// Payment button
					 	echo '<button class="btn btn-success btn-xs" style="float: right; margin-right: 5px" onclick="statusFunction('.$id.', 4, 3)">Fiz.</button>';
					} 
					
					else {
						echo "";
					}		
				}
			}
		}
		if ($status == 1 AND ($type1 > 2 OR ($type2 == 2 AND $cutting_modus == 2)) AND $check < 3) {		// button -> big rolls cutting finished + MED
			echo '<button class="btn btn-warning btn-xs" style="float: right; margin-right: 5px;" onclick="statusFunction('.$id.', 3, 1)"><span class="glyphicon glyphicon-scissors"></span></button>';

		}
	}
	else {			// orders after today - only edit button
		echo "<td>";

		if ($projectid == 0) {
			echo $edit_pencil;
			echo $ekaer_btn;
		}
	}
	
	echo "</td></tr>";


	//////////////////////////////
	// hidden row with details

	if ($invoicename != "") {
		$invoicename_disp = $invoicename;
	}
	else {
	 	$invoicename_disp = "-";
	} 
	if ($invoiceaddress != "") {
		$invoiceaddress_disp = $invoiceaddress;
	}
	else {
	 	$invoiceaddress_disp = "-";
	}
	if ($deliveryname != "") {
		$deliveryname_disp = $deliveryname;
	}
	else {
	 	$deliveryname_disp = "-";
	} 
	
	if ($country == 0 AND $city > 0) {
		$query = $db->prepare("SELECT * FROM cities WHERE `id` = :id");
        $query->bindParam(":id", $city, PDO::PARAM_STR);
        $query->execute();
        $result = $query->fetch(PDO::FETCH_OBJ);
		$city_disp = $result->name;
		$plz = $result->plz;

		$deliveryaddress_disp = $plz." ".$city_disp.", ".$deliveryaddress;
	}
	else {
		if ($deliveryaddress != "") {
			$deliveryaddress_disp = $deliveryaddress;
		}
		else {
		 	$deliveryaddress_disp = "-";
		} 
	}

	if ($country > 0) {
		$query = $db->prepare("SELECT * FROM countries WHERE `id` = :id");
        $query->bindParam(":id", $country, PDO::PARAM_STR);
        $query->execute();
        $result = $query->fetch(PDO::FETCH_OBJ);
		$country_disp = $result->name2;
	}


	if ($note != "") {
		$note_disp = " &nbsp;".$note;
	}
	else {
	 	$note_disp = " &nbsp;-";
	}

	if ($customer_page == 1) {
		echo '<tr><td colspan="14" class="hiddenRow"><div class="accordian-body collapse" id="order'.$i.'">';
	}
	else {
		echo '<tr><td colspan="13" class="hiddenRow"><div class="accordian-body collapse" id="order'.$i.'">';
	}
	echo '<div class="row" style="margin-left: 0px;">';
	
	echo '<div class="col-md-3 detailsRow"><b><u>Számla:</u></b><br>';
	
	/*
	echo ' &nbsp;<b>Név:</b> &nbsp;'.$invoicename_disp.'<br>';
	echo ' &nbsp;<b>Cím:</b> &nbsp;'.$invoiceaddress_disp.'<br><br>';
	*/

	echo ' &nbsp;&nbsp;<b>'.$name_display.'</b><br>';
	echo ' &nbsp;&nbsp;'.$customer_plz.' '.$customer_city.'<br>';
	echo ' &nbsp;&nbsp;'.$customer_street.'<br><br>';
	echo ' &nbsp;&nbsp;'.$contactperson.'</div>';

	echo '<div class="col-md-3 detailsRow"><b><u>Szállítás:</u></b><br>';
	echo ' &nbsp;<b>Név:</b> &nbsp;'.$deliveryname_disp.'<br>';
	echo ' &nbsp;<b>Cím:</b> &nbsp;'.$deliveryaddress_disp;

	if ($country > 0) {
		echo '<br> &nbsp;<b>Ország:</b> &nbsp;'.$country_disp;
	}

	if ($licence != "") {
		echo '<br> &nbsp;<b>Rendszám:</b> &nbsp;'.$licence;
	}

	echo '<br><br> &nbsp;<b>ID:</b> '.$id;
	echo '<br> &nbsp;<b>C-ID:</b> '.$name.'</div>';
	

	echo '<div class="col-md-2 detailsRow"><b><u>Elérhetőség:</u></b><br>';
	
	if ($customer_phone != "") {
		echo ' &nbsp;&nbsp;'.$customer_phone.'<br>';
	}

	if ($customer_email != "") {
		echo ' &nbsp;&nbsp;'.$customer_email.'<br>';
	}

	echo '<br>';

	if ($status < 3) {
		echo '<b><u>Jegyzet (kint - Gép '.$team.'): </u></b><br>';
	}
	else {
		echo '<b><u>Jegyzet (kint): </u></b><br>';
	}
	echo $note_disp.'</div>';

	echo '<div class="col-md-4 detailsRow" style="text-align: right; padding-right: 30px;"><b>Elkészített:</b>';

	$query = $db->prepare("SELECT * FROM users WHERE `id` = :id");
	$query->bindParam(":id", $creator, PDO::PARAM_STR);
	$query->execute();
	$result = $query->fetch(PDO::FETCH_OBJ);
	echo ' &nbsp;<i>('.$result->username.')';
	echo ' &nbsp;'.$created.'</i><br>';

	if ($status > 2 AND $status < 5) {	// if cutting is already finished
		echo '<b>Vágás befejezett:</b> &nbsp;<i>(Gép '.$team.') &nbsp;'.$cutdate;
	}
	if ($status == 4) {		// if collected (office)
		$query = $db->prepare("SELECT * FROM users WHERE `id` = :id");
		$query->bindParam(":id", $completer, PDO::PARAM_STR);
		$query->execute();
		$result = $query->fetch(PDO::FETCH_OBJ);
		echo '</i><br><b>Elvitt:</b>  &nbsp;<i>('.$result->username.') &nbsp;'.$completedate.'</i>';
	}
	if ($pickup == 1 AND $cutting_modus == 2) {		// if collected (loader)
		$query = $db->prepare("SELECT * FROM users WHERE `id` = :id");
		$query->bindParam(":id", $loader, PDO::PARAM_STR);
		$query->execute();
		$result = $query->fetch(PDO::FETCH_OBJ);
		echo '</i><br><b>Rakodás:</b>  &nbsp;<i>('.$result->username.') &nbsp;'.$pickupdate.'</i>';
	}
	if ($paid == 1) {		// if paid
		$query = $db->prepare("SELECT * FROM users WHERE `id` = :id");
		$query->bindParam(":id", $receiver, PDO::PARAM_STR);
		$query->execute();
		$result = $query->fetch(PDO::FETCH_OBJ);
		echo '</i><br><b>Fizetett:</b>  &nbsp;<i>('.$result->username.') &nbsp;'.$receivedate.'</i>';
	}

	echo '</div></td></tr>';

	

	$modal_action = $_SERVER['PHP_SELF'];
	
	$i++;

	if ($stripe == 1) {
		$stripe = 0;
	}
	elseif ($stripe == 0) {
		$stripe = 1;
	}
}