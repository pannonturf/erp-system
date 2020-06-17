<?php
include('../tools/functions.php');

require_once('../config/config.php');
$db = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.'', DB_USER, DB_PASS);
$db -> exec("set names utf8");
date_default_timezone_set('Europe/Budapest');

if (isset($_SESSION['userid'])) {
	$user = $_SESSION['userid'];
}
else {
	$user = $_COOKIE["userid"];
}

if ($user == 16) {
	$team = 1;
}
elseif ($user == 17) {
	$team = 2;
}

$today = date("Y-m-d");
$tomorrow = date('Y-m-d', strtotime('tomorrow'));
$yesterday = date('Y-m-d', strtotime('yesterday'));
$today_midnight = date("Y-m-d")." 00:00:00";
$tomorrow_midnight = $tomorrow." 00:00:00";
$yesterday_midnight = $yesterday." 00:00:00";
$after_midnight = date('Y-m-d', strtotime($tomorrow.' +1 day'))." 00:00:00";
$after = $tomorrow." 09:00:01";

$next_id = $_POST['next_id'];

// get planneddate of selected order
$query3 = $db->prepare("SELECT * FROM `order` WHERE `id` = :id");
$query3->bindParam(":id", $next_id, PDO::PARAM_STR);
$query3->execute(); 
$result3 = $query3->fetch(PDO::FETCH_OBJ);
$next_planneddate = $result3->planneddate;

$query = $db->prepare("SELECT * FROM `order` WHERE planneddate >= :start AND planneddate < :end AND `status` = 1 AND `type1` < 4 AND `team` = :team ORDER BY `sort` ASC LIMIT 1");

if ($next_planneddate < $tomorrow_midnight) {
	$query->bindParam(":start", $today_midnight, PDO::PARAM_STR);
	$query->bindParam(":end", $tomorrow_midnight, PDO::PARAM_STR);
}
else {
	$query->bindParam(":start", $tomorrow_midnight, PDO::PARAM_STR);
	$query->bindParam(":end", $after_midnight, PDO::PARAM_STR);
}

$query->bindParam(":team", $team, PDO::PARAM_STR);
$query->execute(); 
$result = $query->fetch(PDO::FETCH_OBJ);
$current_sort = $result->sort;
$sort = $current_sort - 1;

$query2 = $db->prepare("UPDATE `order` SET `sort` = :sort WHERE `id` = :id");
$query2->bindParam(":sort", $sort, PDO::PARAM_STR); 
$query2->bindParam(":id", $next_id, PDO::PARAM_STR);
$query2->execute();


// show next cutting view immediately
$change = 1;
$bg = " bg_start";
$status_change = 2;
$btn = "start";
$btn_title = "Kezdés";

$query->execute(); 

if ($query->rowCount() > 0) {
	include('../views/_cutting_view.php');
}

?>

<div class="row">
	<div class="col-sm-12" style="margin-top: 50px;">

		<table class='table'>
		<tr class='title'><td>Sz.</td><td><span class='glyphicon glyphicon-time'></span></td><td>m&sup2;</td><td>Terület</td><td><span class='glyphicon glyphicon-comment'></span></td></tr>
      
		<?php 
		$i = 1;
		$query = $db->prepare("SELECT * FROM `order` WHERE planneddate >= :start AND planneddate < :end AND `status` = 1 AND `type1` < 4 AND `team` = :team ORDER BY `sort` ASC");

		// show list of orders of today
		if ($next_planneddate < $tomorrow_midnight) {
			$query->bindParam(":start", $today_midnight, PDO::PARAM_STR);
			$query->bindParam(":end", $tomorrow_midnight, PDO::PARAM_STR);
			$query->bindParam(":team", $team, PDO::PARAM_STR);
			$query->execute(); 
		}
		else {
			$query->bindParam(":start", $tomorrow_midnight, PDO::PARAM_STR);
			$query->bindParam(":end", $after, PDO::PARAM_STR);
			$query->bindParam(":team", $team, PDO::PARAM_STR);
			$query->execute(); 

			echo '<tr><td colspan="5">HOLNAP</td></tr>';
		}

		include('../views/_cutting_foreach.php');

		?>
		</table>
		<br><br><br><br><br><br>
	</div>
</div>
