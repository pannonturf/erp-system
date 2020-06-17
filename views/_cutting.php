<?php 
/////////////////////////////////////////////
// Cutting view for outside teams (modus 1)//
/////////////////////////////////////////////

include('views/_header'.$header.'.php');
include('tools/functions.php');

require_once('config/config.php');
$db = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.'', DB_USER, DB_PASS);
$db -> exec("set names utf8");
date_default_timezone_set('Europe/Budapest');

if (isset($_SESSION['userid'])) {
	$user = $_SESSION['userid'];
}
else {
	$user = $_COOKIE["userid"];
}

// display right list for right user
if ($user == 16) {
	$team = 1;
}
elseif ($user == 17) {
	$team = 2;
}

$now = date("Y-m-d H:i:s");
$today = date("Y-m-d");
$day = date('w', strtotime($today));
$today_midnight = date("Y-m-d")." 00:00:00";

// time from which all orders are visible
$today_break = date("Y-m-d")." 09:30:01";
// until when the orders are visible in the morning
$today_visible = date("Y-m-d")." 14:00:01";

$tomorrow_midnight = date('Y-m-d', strtotime('tomorrow'))." 00:00:00";
$yesterday_midnight = date('Y-m-d', strtotime('yesterday'))." 00:00:00";
$after = date('Y-m-d', strtotime('tomorrow'))." 10:00:01";

//check if order from yesterday has not been finished
$query = $db->prepare("SELECT * FROM `order` WHERE planneddate >= :start AND planneddate < :end AND `status` = 1 AND `type1` < 4 AND `project_id` = 0 AND `team` = :team ORDER BY `sort` DESC");
$query->bindParam(":start", $yesterday_midnight, PDO::PARAM_STR);
$query->bindParam(":end", $today_midnight, PDO::PARAM_STR);
$query->bindParam(":team", $team, PDO::PARAM_STR);
$query->execute(); 

if ($query->rowCount() > 0) {
	// Update planneddate to today 7:00
	$query2 = $db->prepare("SELECT * FROM `order` WHERE planneddate >= :start AND planneddate < :end AND `status` = 1 AND `type1` < 4 AND `project_id` = 0 AND `team` = :team ORDER BY `sort` ASC LIMIT 1");
	$query2->bindParam(":start", $today_midnight, PDO::PARAM_STR);
	$query2->bindParam(":end", $tomorrow_midnight, PDO::PARAM_STR);
	$query2->bindParam(":team", $team, PDO::PARAM_STR);
	$query2->execute(); 
	$result = $query2->fetch(PDO::FETCH_OBJ);
	$current_sort = $result->sort;
	$sort = $current_sort - 1;

	foreach ($query as $row) {
		$id_old = $row['id'];
		$new_planneddate = $today." 07:00:00";

		$query4 = $db->prepare("UPDATE `order` SET `sort` = :sort, `planneddate` = :planneddate WHERE `id` = :id");
		$query4->bindParam(":sort", $sort, PDO::PARAM_STR); 
		$query4->bindParam(":planneddate", $new_planneddate, PDO::PARAM_STR); 
		$query4->bindParam(":id", $id_old, PDO::PARAM_STR);
		$query4->execute();

		$sort++;
	}
	echo "<script type='text/javascript'> document.location = 'index.php'; </script>";
}

else {
	//check if order from today is in progress
	$change = 0;
	$checkpoint = 1;
	$bg = "";
	$status_change = 3;
	$btn = "finish";
	$btn_title = "Befejezés";

	$query = $db->prepare("SELECT * FROM `order` WHERE planneddate >= :start AND planneddate < :end AND `status` = 2 AND `type1` < 4 AND `project_id` = 0 AND `team` = :team ORDER BY `sort` ASC LIMIT 1");
	$query->bindParam(":start", $today_midnight, PDO::PARAM_STR);
	$query->bindParam(":end", $tomorrow_midnight, PDO::PARAM_STR);
	$query->bindParam(":team", $team, PDO::PARAM_STR);
	$query->execute(); 

	if ($query->rowCount() > 0) {
		include('views/_cutting_view.php');		// current order to cut
	}

	else {
		//get next order of today    
		$change = 1;
		$checkpoint = 1;
		$bg = " bg_start";
		$status_change = 2;
		$btn = "start";
		$btn_title = "Kezdés";

		$query = $db->prepare("SELECT * FROM `order` WHERE planneddate >= :start AND planneddate < :end AND `status` = 1 AND `type1` < 4 AND `project_id` = 0 AND `team` = :team ORDER BY `sort` ASC LIMIT 1");

		if ($now < $today_break) {
			$query->bindParam(":start", $today_midnight, PDO::PARAM_STR);
			$query->bindParam(":end", $today_visible, PDO::PARAM_STR);
		}
		else {
			$query->bindParam(":start", $today_midnight, PDO::PARAM_STR);
			$query->bindParam(":end", $tomorrow_midnight, PDO::PARAM_STR);
		}

		$query->bindParam(":team", $team, PDO::PARAM_STR);
		$query->execute(); 

		if ($query->rowCount() > 0) {
			echo '<div id="cutting_window">';
			include('views/_cutting_view.php');
		}
		elseif ($now < $today_break) {
			$query->bindParam(":start", $today_midnight, PDO::PARAM_STR);
			$query->bindParam(":end", $tomorrow_midnight, PDO::PARAM_STR);
			$query->bindParam(":team", $team, PDO::PARAM_STR);
			$query->execute(); 

			echo '<div id="cutting_window">';
			include('views/_cutting_view.php');
		}
		
		//// all orders from today are cut
		// check if order is in progress (from tomorrow)
		else {

			$tomorrow = date('Y-m-d', strtotime('tomorrow'));

			//only Mon - Thur
			if ($day > 0 AND $day < 5) {

				//check if order is in progress
				$change = 0;
				$checkpoint = 2;	
				$bg = "";
				$status_change = 3;
				$btn = "finish";
				$btn_title = "Befejezés";

				$query = $db->prepare("SELECT * FROM `order` WHERE planneddate >= :start AND planneddate < :end AND `status` = 2 AND `type1` < 4 AND `project_id` = 0 AND `team` = :team ORDER BY `sort` ASC LIMIT 1");
				$query->bindParam(":start", $tomorrow_midnight, PDO::PARAM_STR);
				$query->bindParam(":end", $after, PDO::PARAM_STR);
				$query->bindParam(":team", $team, PDO::PARAM_STR);
				$query->execute(); 

				if ($query->rowCount() > 0) {
					include('views/_cutting_view.php');
				}

				else {
					//get next order of tomorrow   
					$change = 1;
					$checkpoint = 2;
					$bg = " bg_start";
					$status_change = 2;
					$btn = "start";
					$btn_title = "Kezdés";

					$query = $db->prepare("SELECT * FROM `order` WHERE planneddate >= :start AND planneddate < :end AND `status` = 1 AND `type1` < 4 AND `project_id` = 0 AND `team` = :team ORDER BY `sort` ASC LIMIT 1");
					$query->bindParam(":start", $tomorrow_midnight, PDO::PARAM_STR);
					$query->bindParam(":end", $after, PDO::PARAM_STR);
					$query->bindParam(":team", $team, PDO::PARAM_STR);
					$query->execute(); 

					if ($query->rowCount() > 0) {
						echo '<div id="cutting_window">';
						include('views/_cutting_view.php');
					}
				}

			}
			
		}
	}
}
?>

<div class="row">
	<div class="col-sm-12" style="margin-top: 50px;">

		<table class='table'>
		<tr class='title'><td>Sz.</td><td><span class='glyphicon glyphicon-time'></span></td><td>m&sup2;</td><td>Terület</td><td><span class='glyphicon glyphicon-comment'></span></td></tr>
      
		<?php 
		$i = 1;
		$query = $db->prepare("SELECT * FROM `order` WHERE planneddate >= :start AND planneddate < :end AND `status` = 1 AND `type1` < 4 AND `project_id` = 0 AND `team` = :team ORDER BY `sort` ASC");

		// show list of orders of today
		if ($checkpoint == 1) {
			if ($now < $today_break) {
				$query->bindParam(":start", $today_midnight, PDO::PARAM_STR);
				$query->bindParam(":end", $today_visible, PDO::PARAM_STR);
			}
			else {
				$query->bindParam(":start", $today_midnight, PDO::PARAM_STR);
				$query->bindParam(":end", $tomorrow_midnight, PDO::PARAM_STR);
			}

			$query->bindParam(":team", $team, PDO::PARAM_STR);
			$query->execute(); 
		}
		else {
			if ($day > 0) {
				$query->bindParam(":start", $tomorrow_midnight, PDO::PARAM_STR);
				$query->bindParam(":end", $after, PDO::PARAM_STR);
				$query->bindParam(":team", $team, PDO::PARAM_STR);
				$query->execute(); 

				echo '<tr><td colspan="6">HOLNAP</td></tr>';
			}
		}

		include('views/_cutting_foreach.php');		// list of further orders to be cut
		?>
		
		</table>
		<br><br><br><br><br><br>
	</div>
</div>
<?php
if ($checkpoint == 2) {
	echo "</div>";
}

?>
