<?php
require_once('../config/config.php');
$db = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.'', DB_USER, DB_PASS);
date_default_timezone_set('Europe/Budapest');

// get cutting modus
$query = $db->prepare("SELECT * FROM `system` WHERE `id` = 3");
$query->execute(); 
$result = $query->fetch(PDO::FETCH_OBJ);
$cutting_modus = $result->active;

$id = $_POST['id'];
$status = $_POST['status'];
$type = $_POST['type'];
$currentdate = date("Y-m-d H:i:s");

if (isset($_SESSION['userid'])) {
	$user = $_SESSION['userid'];
}
else {
	$user = $_COOKIE["userid"];
}

$today = date("Y-m-d");
$day_today = date('w', strtotime($today));
$tomorrow = date('Y-m-d', strtotime('tomorrow'));
$nextday = date('Y-m-d', strtotime('tomorrow'));
$day = date('w', strtotime($nextday));

$days_short = array("V", "H", "K", "S", "C", "P", "S");

if ($day == 6) {
	$nextday = date('Y-m-d', strtotime($nextday.' +2 days'));
}
elseif ($day == 0) {
	$nextday = date('Y-m-d', strtotime($nextday.' +1 days'));
}

if ($status == 0) {
	//Update operations
	$sql = "UPDATE `order` SET `status` = :status WHERE `id` = :id";
	$query = $db->prepare($sql);
	$query->bindParam(":id", $id, PDO::PARAM_STR);
	$query->bindParam(":status", $status, PDO::PARAM_STR);
	$query->execute();
}

elseif ($status == 1) {
	
	if ($type == 1) {
		$today_midnight = date("Y-m-d")." 00:00:00";
		$tomorrow_midnight = date('Y-m-d', strtotime('tomorrow'))." 00:00:00";

		/*
		//get next order in list    
		$query = $db->prepare("SELECT * FROM `order` WHERE planneddate >= :today AND planneddate < :tomorrow AND `status` = 1 AND `type1` = 1 AND `team` = 1 ORDER BY `sort` ASC LIMIT 1");
		$query->bindParam(":today", $today_midnight, PDO::PARAM_STR);
		$query->bindParam(":tomorrow", $tomorrow_midnight, PDO::PARAM_STR);
		$query->execute(); 
		$result = $query->fetch(PDO::FETCH_OBJ);
		$current_sort = $result->sort;

		$sort = $current_sort - 1;
		*/

		//Update operations
		$sql = "UPDATE `order` SET `status` = :status WHERE `id` = :id";
		$query = $db->prepare($sql);
		$query->bindParam(":id", $id, PDO::PARAM_STR);
		$query->bindParam(":status", $status, PDO::PARAM_STR);
		$query->execute();
	}
	elseif ($type < 4) {
		/// today
		if ($type == 2) {
			$order_datum = $today;
		}
		elseif ($type == 3) {
			$order_datum = $tomorrow;
		}

		//get last id2 of the day
		$query = $db->prepare("SELECT * FROM `order` WHERE `date` = :datum AND `id2` > 0 AND `type1` < 4 ORDER BY `id2` DESC LIMIT 1");
		$query->bindParam(":datum", $order_datum, PDO::PARAM_STR);
		$query->execute(); 
		
		if ($query->rowCount() > 0) {
			$result = $query->fetch(PDO::FETCH_OBJ);
			$current_id2 = $result->id2;
			$id2 = $current_id2 + 1;
		}
		else {
			$id2 = 1;
		}

		// get datum of selected order
		$query3 = $db->prepare("SELECT * FROM `order` WHERE `id` = :id");
		$query3->bindParam(":id", $id, PDO::PARAM_STR);
		$query3->execute(); 
		$result3 = $query3->fetch(PDO::FETCH_OBJ);
		$datum = $result3->date;

		$day1 = date('w', strtotime($datum));
		$dayPrefix = $days_short[$day1];

		//Update operations
		$sql = "UPDATE `order` SET `id2` = :id2, `prefix` = :prefix WHERE `id` = :id";
		$query = $db->prepare($sql);
		$query->bindParam(":id", $id, PDO::PARAM_STR);
		$query->bindParam(":id2", $id2, PDO::PARAM_STR); 
		$query->bindParam(":prefix", $dayPrefix, PDO::PARAM_STR);
		$query->execute();
	}
	elseif ($type == 4) {		// cutting mode 2
		
		// get last assigned id3
		$query = $db->prepare("SELECT * FROM `order` WHERE `id3` > 0 AND `type1` < 4 ORDER BY `id3` DESC LIMIT 1");
		$query->execute(); 

		$result = $query->fetch(PDO::FETCH_OBJ);
		$current_id3 = $result->id3;
		$id3 = $current_id3 + 1;

		//Update operations
		$sql = "UPDATE `order` SET `id3` = :id3 WHERE `id` = :id";
		$query = $db->prepare($sql);
		$query->bindParam(":id", $id, PDO::PARAM_STR);
		$query->bindParam(":id3", $id3, PDO::PARAM_STR); 
		$query->execute();
	}	

}

elseif ($status == 2) {
	// get datum of selected order
	$query3 = $db->prepare("SELECT * FROM `order` WHERE `id` = :id");
	$query3->bindParam(":id", $id, PDO::PARAM_STR);
	$query3->execute(); 
	$result3 = $query3->fetch(PDO::FETCH_OBJ);
	$datum = $result3->date;
	$current_id2 = $result3->id2;

	if ($current_id2 == 0) {
		//get last id2 of the day
		$query = $db->prepare("SELECT * FROM `order` WHERE `date` = :datum AND `id2` > 0 AND `type1` < 4 ORDER BY `id2` DESC LIMIT 1");
		$query->bindParam(":datum", $datum, PDO::PARAM_STR);
		$query->execute(); 
		
		if ($query->rowCount() > 0) {
			$result = $query->fetch(PDO::FETCH_OBJ);
			$current_id2 = $result->id2;
			$id2 = $current_id2 + 1;
		}
		else {
			$id2 = 1;
		}
	}
	else {
		$id2 = $current_id2;
	}

	$day1 = date('w', strtotime($datum));
	$dayPrefix = $days_short[$day1];

	/*
	if ($day_today > 0 AND $day_today < 6) {
		//check max_id2 and refresh if new day
		// get earlier date
		$query3 = $db->prepare("SELECT * FROM `max_id2` ORDER BY `date` ASC");
		$query3->execute(); 
		$result3 = $query3->fetchAll(PDO::FETCH_OBJ);
		$lower_datum = $result3[0]->datum;
		
		if ($lower_datum < $today) {
			$lower_id = $result3[0]->id;
			$higher_id = $result3[1]->id;

			/*
			$query = $db->prepare("UPDATE `max_id2` SET `datum` = :datum WHERE `id` = :id");
			$query->bindParam(":datum", $today, PDO::PARAM_STR);
			$query->bindParam(":id", $higher_id, PDO::PARAM_STR);
			$query->execute(); 
			

			$query = $db->prepare("UPDATE `max_id2` SET `datum` = :datum AND `id2` = 0 WHERE `id` = :id");
			$query->bindParam(":datum", $nextday, PDO::PARAM_STR);
			$query->bindParam(":id", $lower_id, PDO::PARAM_STR);
			$query->execute(); 
		}
	}


	//get last id2 of the day
	$query = $db->prepare("SELECT * FROM `max_id2` WHERE `date` = :datum");
	$query->bindParam(":datum", $datum, PDO::PARAM_STR);
	$query->execute();
	$result = $query->fetch(PDO::FETCH_OBJ);
	$current_id2 = $result->id2;
	$id2_test = $current_id2 + 1;
	*/

	//Update operations
	$sql = "UPDATE `order` SET `status` = :status, `cutstartdate` = :startdate, `id2` = :id2, `prefix` = :prefix WHERE `id` = :id";
	$query = $db->prepare($sql);
	$query->bindParam(":id", $id, PDO::PARAM_STR);
	$query->bindParam(":status", $status, PDO::PARAM_STR);
	$query->bindParam(":startdate", $currentdate, PDO::PARAM_STR);
	$query->bindParam(":id2", $id2, PDO::PARAM_STR);
	$query->bindParam(":prefix", $dayPrefix, PDO::PARAM_STR);
	$query->execute();

	/*
	//max_id2 plus 1
	$query = $db->prepare("UPDATE `max_id2` SET `id2` = :id2 WHERE `id` = :id");
	$query->bindParam(":id2", $id2_test, PDO::PARAM_STR);
	$query->bindParam(":id", $higher_id, PDO::PARAM_STR);
	$query->execute(); 
	*/
	
}

elseif ($status == 3) {

	$sql = "UPDATE `order` SET `status` = :status, `cutdate` = :completedate WHERE `id` = :id";
	$query = $db->prepare($sql);

	$query->bindParam(":status", $status, PDO::PARAM_STR);
	$query->bindParam(":completedate", $currentdate, PDO::PARAM_STR);
	$query->bindParam(":id", $id, PDO::PARAM_STR);

	$query->execute();
}

elseif ($status == 4) {

	// for cutting mode 2 assign next number (id3)
	// get last assigned id3
	if ($cutting_modus == 2) {
		// get type1 of selected order
		$query3 = $db->prepare("SELECT * FROM `order` WHERE `id` = :id");
		$query3->bindParam(":id", $id, PDO::PARAM_STR);
		$query3->execute(); 
		$result3 = $query3->fetch(PDO::FETCH_OBJ);
		$type1 = $result3->type1;
		$id3 = $result3->id3;

		if ($id3 == 0) {			// check, if id3 was already assigned
			if ($type1 < 4) {
			$query = $db->prepare("SELECT * FROM `order` WHERE `id3` > 0 AND `type1` < 4 ORDER BY `id3` DESC LIMIT 1");
			$query->execute(); 

			$result = $query->fetch(PDO::FETCH_OBJ);
			$current_id3 = $result->id3;
			$id3 = $current_id3 + 1;
			}
			else {
				$id3 = 0;
			}
		}	
	}
	else {
		$id3 = 0;
	}


	//complete, not paid
	if ($type == 1) {
		$sql = "UPDATE `order` SET `id3` = :id3, `status` = :status, `completer` = :completer, `completedate` = :completedate WHERE `id` = :id";
		$query = $db->prepare($sql);

		$query->bindParam(":id3", $id3, PDO::PARAM_STR);
		$query->bindParam(":status", $status, PDO::PARAM_STR);
		$query->bindParam(":completer", $user, PDO::PARAM_STR);
		$query->bindParam(":completedate", $currentdate, PDO::PARAM_STR);
		$query->bindParam(":id", $id, PDO::PARAM_STR);

		$query->execute();
	}

	//complete, paid
	if ($type == 2) {
		$paid = 1;
		$invoicenumber = 0;

		$sql = "UPDATE `order` SET `id3` = :id3, `status` = :status, `completer` = :completer, `completedate` = :completedate, `receiver` = :receiver, `receivedate` = :receivedate, `paid` = :paid WHERE `id` = :id";
		$query = $db->prepare($sql);

		$query->bindParam(":id3", $id3, PDO::PARAM_STR);
		$query->bindParam(":status", $status, PDO::PARAM_STR);
		$query->bindParam(":completer", $user, PDO::PARAM_STR);
		$query->bindParam(":completedate", $currentdate, PDO::PARAM_STR);
		$query->bindParam(":receiver", $user, PDO::PARAM_STR);
		$query->bindParam(":receivedate", $currentdate, PDO::PARAM_STR);
		//$query->bindParam(":invoicenumber", $invoicenumber, PDO::PARAM_STR);
		$query->bindParam(":paid", $paid, PDO::PARAM_STR);
		$query->bindParam(":id", $id, PDO::PARAM_STR);

		$query->execute();
	}

	//was already completed, now paid
	if ($type == 3) {
		$paid = 1;
		$invoicenumber = 0;
		
		$sql = "UPDATE `order` SET `receiver` = :receiver, `receivedate` = :receivedate, `paid` = :paid WHERE `id` = :id";
		$query = $db->prepare($sql);

		$query->bindParam(":receiver", $user, PDO::PARAM_STR);
		$query->bindParam(":receivedate", $currentdate, PDO::PARAM_STR);
		//$query->bindParam(":invoicenumber", $invoicenumber, PDO::PARAM_STR);
		$query->bindParam(":paid", $paid, PDO::PARAM_STR);
		$query->bindParam(":id", $id, PDO::PARAM_STR);

		$query->execute();
	}

	//not complete, already paid
	if ($type == 4) {
		$paid = 1;

		$sql = "UPDATE `order` SET `paid` = :paid, `receiver` = :receiver, `receivedate` = :receivedate WHERE `id` = :id";
		$query = $db->prepare($sql);

		$query->bindParam(":receiver", $user, PDO::PARAM_STR);
		$query->bindParam(":receivedate", $currentdate, PDO::PARAM_STR);
		$query->bindParam(":paid", $paid, PDO::PARAM_STR);
		$query->bindParam(":id", $id, PDO::PARAM_STR);

		$query->execute();

		echo "Fiz";
	}

	// already paid, now completed
	if ($type == 5) {
		$invoicenumber = 0;
		
		$sql = "UPDATE `order` SET `id3` = :id3, `status` = :status, `completer` = :completer, `completedate` = :completedate WHERE `id` = :id";
		$query = $db->prepare($sql);

		$query->bindParam(":id3", $id3, PDO::PARAM_STR);
		$query->bindParam(":status", $status, PDO::PARAM_STR);
		$query->bindParam(":completer", $user, PDO::PARAM_STR);
		$query->bindParam(":completedate", $currentdate, PDO::PARAM_STR);
		//$query->bindParam(":invoicenumber", $invoicenumber, PDO::PARAM_STR);
		$query->bindParam(":id", $id, PDO::PARAM_STR);

		$query->execute();
	}
}


elseif ($status == 5) {

	$sql = "UPDATE `order` SET `status` = :status, `completer` = :deleter, `completedate` = :deletedate WHERE `id` = :id";
	$query = $db->prepare($sql);

	$query->bindParam(":status", $status, PDO::PARAM_STR);
	$query->bindParam(":deleter", $user, PDO::PARAM_STR);
	$query->bindParam(":deletedate", $currentdate, PDO::PARAM_STR);
	$query->bindParam(":id", $id, PDO::PARAM_STR);

	$query->execute();
}


?>
