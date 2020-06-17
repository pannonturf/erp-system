<?php
require_once('../config/config.php');
$db = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.'', DB_USER, DB_PASS);

$project = $_POST['id'];
$olddatum = $_POST['olddatum'];
$newdatum = $_POST['newdatum'];
$type = $_POST['type'];
$planneddate = $newdatum." 00:00:00";


if ($type == 1) {
	//Update project order start date
	$sql = "UPDATE `order` SET `date` = :datum, `planneddate` = :planneddate WHERE `project_id` = :project";
	$query = $db->prepare($sql);

	$query->bindParam(":datum", $newdatum, PDO::PARAM_STR);
	$query->bindParam(":planneddate", $planneddate, PDO::PARAM_STR);
	$query->bindParam(":project", $project, PDO::PARAM_STR);
	$query->execute();


	// Update trucks of following days 
	$query = $db->prepare("SELECT * FROM `trucks` WHERE `project` = :project ORDER BY `sort` ASC");
	$query->bindParam(":project", $project, PDO::PARAM_STR);
	$query->execute(); 


	foreach ($query as $row) {
		$next_datum = $row['datum'];
		$next_id = $row['id'];

		echo $next_datum." / ".$olddatum." / ".$last_datum." // ";
		if ($next_datum > $olddatum) {
			echo "Ja /";
			if ($next_datum > $last_datum) {
				$newdatum2 = date('Y-m-d', strtotime($last_datum.' +1 day'));
				echo "Ja /";
			}

			$sql = "UPDATE `trucks` SET `datum` = :datum WHERE `id` = :id";
			$query = $db->prepare($sql);

			$query->bindParam(":datum", $newdatum2, PDO::PARAM_STR);
			$query->bindParam(":olddatum", $olddatum, PDO::PARAM_STR);
			$query->bindParam(":id", $next_id, PDO::PARAM_STR);

			$query->execute();
		}

		$last_datum = $next_datum;
	}
}

//Update operations
$sql = "UPDATE `trucks` SET `datum` = :datum WHERE `project` = :project AND `datum` = :olddatum";
$query = $db->prepare($sql);

$query->bindParam(":datum", $newdatum, PDO::PARAM_STR);
$query->bindParam(":olddatum", $olddatum, PDO::PARAM_STR);
$query->bindParam(":project", $project, PDO::PARAM_STR);

$query->execute();

?>
