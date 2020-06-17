<?php
include('functions.php');

require_once('../config/config.php');
$db = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.'', DB_USER, DB_PASS);

$datum = $_POST['datum'];
$projectid = $_POST['project'];
$type = $_POST['type'];

$status = 1;

if (isset($_SESSION['userid'])) {
	$creator = $_SESSION['userid'];
}
elseif (isset($_COOKIE['userid'])) {
	$creator = $_COOKIE["userid"];
}
else {
	$creator = 0;
}



//get current sort of the day
$query = $db->prepare("SELECT * FROM `trucks` WHERE `project` = :projectid AND `datum` = :datum ORDER BY `sort` DESC LIMIT 1");
$query->bindParam(":projectid", $projectid, PDO::PARAM_STR);
$query->bindParam(":datum", $datum, PDO::PARAM_STR);
$query->execute();
$result = $query->fetch(PDO::FETCH_OBJ);
$old_sort = $result->sort;
$old_id = $result->id;


$query = $db->prepare("SELECT * FROM `trucks` WHERE `project` = :project ORDER BY `sort` DESC");
$query->bindParam(":project", $projectid, PDO::PARAM_STR);

//echo $datum." / ".$projectid." / ".$type." / ".$creator." / ".$status." / ".$old_sort." / ".$old_id." / ";

//// if truck is removed
if ($type == 1) {

	// delete truck
	$query2 = $db->prepare("DELETE FROM `trucks` WHERE `id` = :truck_id");
	$query2->bindParam(":truck_id", $old_id, PDO::PARAM_STR);
	$query2->execute(); 

	// Update sort of other trucks
	$query->execute(); 
	$last_sort = $query->rowCount();
	$sort = $last_sort;
	foreach ($query as $row) {
		$next_id = $row['id'];
		$next_sort = $row['sort'];
		if ($next_sort > $old_sort) {
			$sql2 = "UPDATE `trucks` SET `sort` = :sort WHERE `id` = :id";
			$query2 = $db->prepare($sql2);
		  	$query2->bindParam(":sort", $sort, PDO::PARAM_STR);  
		  	$query2->bindParam(":id", $next_id, PDO::PARAM_STR);  
			$query2->execute();

			$sort = $sort - 1;

			//echo $sort." / ";
		}
	}
}


//// if truck is added
elseif ($type == 2) {

	// Update sort of other trucks
	$query->execute(); 
	$last_sort = $query->rowCount();
	$sort = $last_sort + 1;
	//echo " / ".$sort." // ";
	foreach ($query as $row) {
		$next_id = $row['id'];
		$next_sort = $row['sort'];
		if ($next_sort > $old_sort) {
			$sql2 = "UPDATE `trucks` SET `sort` = :sort WHERE `id` = :id";
			$query2 = $db->prepare($sql2);
		  	$query2->bindParam(":sort", $sort, PDO::PARAM_STR);  
		  	$query2->bindParam(":id", $next_id, PDO::PARAM_STR);  
			$query2->execute();

			//echo $sort." / ";
			$sort = $sort - 1;
	
		}
	}

	$newsort = $old_sort + 1;

	$query2 = $db->prepare("SELECT * FROM `order` WHERE `project_id` = :project_id");
	$query2->bindParam(":project_id", $projectid, PDO::PARAM_STR);
	$query2->execute(); 
	$result = $query2->fetch(PDO::FETCH_OBJ);
	$cooling = $result->cooling;
	$length = $result->length;

	if ($cooling == 0) {
		$pipes = 48;
		$pallets = 0;
	}
	elseif ($cooling == 1) {
		$pipes = 38;
		$pallets = 26;
	}

	$truck_amount = ceil($pipes * $length * 1.2); 


	$query = $db->prepare("INSERT INTO `trucks` (`id`, `project`, `sort`, `datum`, `status`, `creator`, `pipes`, `pallets`, `amount`) VALUES (NULL, :projectid, :sort, :datum, :status, :creator, :pipes, :pallets, :amount)");

	$query->bindParam(":projectid", $projectid, PDO::PARAM_STR);  
	$query->bindParam(":sort", $newsort, PDO::PARAM_STR);
	$query->bindParam(":datum", $datum, PDO::PARAM_STR);
	$query->bindParam(":status", $status, PDO::PARAM_STR);
	$query->bindParam(":creator", $creator, PDO::PARAM_STR);
	$query->bindParam(":pipes", $pipes, PDO::PARAM_STR);
	$query->bindParam(":pallets", $pallets, PDO::PARAM_STR);
	$query->bindParam(":amount", $truck_amount, PDO::PARAM_STR);
	$query->execute(); 
}


?>
