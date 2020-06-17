<?php
///////////////////////////////////////////
// Update database when order is edited  //
/////////////////////////////////////////// 

//////////
// edit order in database 

if (isset($_POST['editOrderForm'])) {
	//Get variables from form
	$edit_link2 = $_POST['edit_link2'];

	$id = $_POST['id'];
	
	if ($_POST['date'] == 100) {
		$date = $_POST['moreDatum'];
	}
	else {
		$date = $_POST['date'];
	}

	$time = $_POST['time'];
	$olddate = $_POST['olddatum'];
	$oldtime = $_POST['oldtime'];
	$oldplanneddate = $_POST['oldplanneddate'];
	$planneddate = $date." ".$time.":00";
	$type1 = $_POST['type1'];
	$type2 = $_POST['type2'];
	$type3 = $_POST['type3'];
	$field = $_POST['field'];
	$pallet = $_POST['pallet'];
	$status = $_POST['status'];
	$oldstatus = $_POST['oldstatus'];

	$amount0 = $_POST['amount'];
	if ($modus == 2 AND $type3 == 2) {	
		$amount0 = $amount0 * 2;	
	}
	$amount = $amount0;
	$amount_e = amount_encrypt($amount, $key2);

	$delivery = $_POST['delivery'];

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

  	if (!empty($_POST['licence'])) {
		$licence = $_POST['licence'];
	}
	else {
		$licence = "";
  	}

  	$country = $_POST['country1'];
  	$city = $_POST['city_id'];

	if ($delivery == 2) {
	  	if (!empty($_POST['deliverytime'])) {
			$deliverytime = $_POST['deliverytime'].":00";
		}
		else {
			$deliverytime = "";
	  	}

	  	$forwarder = $_POST['forwarder'];
	}
	else {
		$deliverytime = "";
		$forwarder = 0;
	}

	$payment = $_POST['payment'];

	if (isset($_POST['paid'])) {
		$paid = 1;
	}
	else {
		$paid = 0;
	}
	
	// Get customer data
	$customer_id = $_POST['customer_id'];

	if (!empty($_POST['customer_name'])) {
  		$customer_name = $_POST['customer_name'];
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

  	if (!empty($_POST['customer_phone'])) {
		$customer_phone = $_POST['customer_phone'];
	}
	else {
		$customer_phone = "";
  	}
	
	if (!empty($_POST['customer_email'])) {
		$customer_email = $_POST['customer_email'];
	}
	else {
		$customer_email = "";
  	}

	$invoicenumber = $_POST['invoicenumber'];

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

  	// Check if customer data was edited -> checkdata = 0

	// Customer data
	$query = $db->prepare("SELECT * FROM customers WHERE `id` = :id");
	$query->bindParam(":id", $customer_id, PDO::PARAM_STR);
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

	if ($customer_name_old != $customer_name OR $contactperson_old != $contactperson OR $customer_street_old != $customer_street OR $customer_plz_old != $customer_plz OR $customer_city_old != $customer_city OR $telephone_old != $customer_phone OR $email_old != $customer_email) {
		$checkdata = 0;
	}
	else {
		$checkdata = $checkdata_old;
	}


  	// insert cutdate into order if status was changed manually
  	if ($oldstatus < 3 AND $status == 3) {
		$sql = "UPDATE `order` SET `cutdate` = :currentdate WHERE `id` = :id";
		$query = $db->prepare($sql);

		$query->bindParam(":currentdate", $currentdate, PDO::PARAM_STR);
		$query->bindParam(":id", $id, PDO::PARAM_STR);

		$query->execute();
  	}

  	if ($oldplanneddate != $planneddate) {
  		
		//Find the right sort number and update all orders behind it (+1)
		$startdate = $date." 00:00:00";
		$enddate = $date." 23:59:59";

		if ($type1 < 4) {
			$query = $db->prepare("SELECT * FROM `order` WHERE planneddate >= :start AND planneddate < :end AND `type1` < 4 AND `status` < 4 AND `team` = 1 ORDER BY `sort` DESC");
		}
		else {
			$query = $db->prepare("SELECT * FROM `order` WHERE planneddate >= :start AND planneddate < :end AND `type1` > 3 AND `status` < 4 AND `team` = 1 ORDER BY `sort` DESC");
		}
		$query->bindParam(":start", $startdate, PDO::PARAM_STR);
		$query->bindParam(":end", $enddate, PDO::PARAM_STR);
		$query->execute(); 
		$list_length = $query->rowCount();

		if ($list_length == 0) {
			$sort = 1;
		}
		else {
			if ($olddate == $date) {
				//Update list without edited point
				$sort = $list_length - 1;
				foreach ($query as $row) {
					$next_id = $row['id'];
					
					if ($next_id != $id) {
						$sql2 = "UPDATE `order` SET `sort` = :sort WHERE `id` = :id";
						$query2 = $db->prepare($sql2);
					  	$query2->bindParam(":sort", $sort, PDO::PARAM_STR);  
					  	$query2->bindParam(":id", $next_id, PDO::PARAM_STR);  
						$query2->execute();

						$sort = $sort - 1;
					}
				}
				$sort = $list_length;
			}
			else {
				$sort = $list_length + 1;
			}

			$query->bindParam(":start", $startdate, PDO::PARAM_STR);
			$query->bindParam(":end", $enddate, PDO::PARAM_STR);
			$query->execute(); 


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
  	}
  	else {
		$sort = $_POST['sort'];
	}

	//Update operations
	$sql = "UPDATE `order` SET `date` = :datum, `time` = :time, `sort` = :sort, `amount` = :amount, `type1` = :type1, `type2` = :type2, `type3` = :type3, `pallet` = :pallet, `field` = :field, `delivery` = :delivery, `deliveryname` = :deliveryname, `deliveryaddress` = :deliveryaddress, `forwarder` = :forwarder, `payment` = :payment, `invoicenumber` = :invoicenumber, `note` = :note, `note2` = :note2, `licence` = :licence, `status` = :status, `paid` = :paid, `planneddate` = :planneddate, `country` = :country, `city` = :city WHERE `id` = :id";
	$query = $db->prepare($sql);

  	$query->bindParam(":datum", $date, PDO::PARAM_STR);  
	$query->bindParam(":time", $time, PDO::PARAM_STR); 
	$query->bindParam(":sort", $sort, PDO::PARAM_STR);
	$query->bindParam(":amount", $amount_e, PDO::PARAM_STR); 
	$query->bindParam(":type1", $type1, PDO::PARAM_STR);
	$query->bindParam(":type2", $type2, PDO::PARAM_STR);
	$query->bindParam(":type3", $type3, PDO::PARAM_STR);
	$query->bindParam(":pallet", $pallet, PDO::PARAM_STR);
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
	$query->bindParam(":status", $status, PDO::PARAM_STR);
	$query->bindParam(":paid", $paid, PDO::PARAM_STR);
	$query->bindParam(":planneddate", $planneddate, PDO::PARAM_STR); 
	$query->bindParam(":country", $country, PDO::PARAM_STR); 
	$query->bindParam(":city", $city, PDO::PARAM_STR); 
  	$query->bindParam(":id", $id, PDO::PARAM_STR);

	$query->execute();


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
	$query->bindParam(":phone", $customer_phone, PDO::PARAM_STR);
	$query->bindParam(":email", $customer_email, PDO::PARAM_STR);

	if ($delivery == 1) {
		$query->bindParam(":licence", $licence, PDO::PARAM_STR);
	}

	$query->bindParam(":checkdata", $checkdata, PDO::PARAM_STR);
	$query->bindParam(":id", $customer_id, PDO::PARAM_STR);
	$query->execute();


	// show right view after refresh
 	if (isset($_GET['open'])) {
 		echo "<script type='text/javascript'> document.location = '".$edit_link2."'open=1'; </script>";
 	}
 	else {
 		echo "<script type='text/javascript'> document.location = '".$edit_link2."'; </script>";
 	}

}

if (isset($_POST['deleteOrderForm'])) {
	if (isset($_SESSION['userid'])) {
		$user = $_SESSION['userid'];
	}
	else {
		$user = $_COOKIE["userid"];
	}
	$id = $_POST['id'];
  	$status = 5;
	$deletedate = date("Y-m-d H:i:s");

	//Update operations
	$sql = "UPDATE `order` SET `status` = :status, `completer` = :deleter, `completedate` = :deletedate WHERE `id` = :id";
	$query = $db->prepare($sql);

	$query->bindParam(":status", $status, PDO::PARAM_STR);
	$query->bindParam(":deleter", $user, PDO::PARAM_STR);
	$query->bindParam(":deletedate", $deletedate, PDO::PARAM_STR);
	$query->bindParam(":id", $id, PDO::PARAM_STR);

	$query->execute();

	echo "<script type='text/javascript'> document.location = 'sales.php'; </script>";
}
?>


