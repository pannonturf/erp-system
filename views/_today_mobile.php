<?php 
///////////////////////////////////////////////
// Cutting list of today mobile (management) //
///////////////////////////////////////////////

include('views/_header'.$header.'.php');
include('tools/functions.php');

require_once('config/config.php');
$db = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.'', DB_USER, DB_PASS);
$db -> exec("set names utf8");
date_default_timezone_set('Europe/Budapest');

$days = array("Vasárnap", "Hétfő", "Kedd", "Szerda", "Csütörtök", "Péntek", "Szombat");

$today = date('Y-m-d');
$day = date('w', strtotime($today));

$tomorrow = date('Y-m-d', strtotime('tomorrow'));
$yesterday = date('Y-m-d', strtotime('yesterday'));

$dayHeading = "Ma";
$dayHeading2 = $days[$day];

$datum = $today;

$today_midnight = date("Y-m-d")." 00:00:00";
$tomorrow_midnight = date('Y-m-d', strtotime('tomorrow'))." 00:00:00";
$yesterday_midday = date('Y-m-d', strtotime('yesterday'))." 13:00:00";

//get total amounts of the day     
$total = 0;
$total_open = 0;
$total_finish = 0;
$query = $db->prepare("SELECT * FROM `order` WHERE planneddate >= :today AND planneddate < :tomorrow AND `type1` = 1 AND `project_id` = 0 AND `status` < 5 ORDER BY `time` ASC");
$query->bindParam(":today", $today_midnight, PDO::PARAM_STR);
$query->bindParam(":tomorrow", $tomorrow_midnight, PDO::PARAM_STR);
$query->execute(); 
foreach ($query as $row) {
	$amount = amount_decrypt($row['amount'], $key2);
	$total += $amount;
	$status = $row['status'];

	if ($status < 3) {
		$total_open += $amount;
	}
	else {
		$total_finish += $amount;
	}
}
$total = number_format($total, 0, ',', ' ');
$total_open = number_format($total_open, 0, ',', ' ');
$total_finish = number_format($total_finish, 0, ',', ' ');
?>

<div class="row">
    <div class="col-xs-6">
      <h3 style="margin-top:10px;">Vágás lista</h3>  
    </div>
    <div class="col-xs-6" style="text-align:right; padding-top: 10px;">
    	<?php
    	echo "<i>".$total_finish." / <b>".$total." m&sup2;</b></i>";
    	?>
    </div>
</div>

<div class="row">
	<div class="col-sm-12">
		<div class="panel panel-success">
		<div class="panel-heading"><h4><span class="glyphicon glyphicon-edit"></span>&nbsp;&nbsp;1. Gép</h4> </div>

		<table class='table'>      
		<?php 
		$changeLabel = "2";
		$team = 2;

		//past orders     
		$query = $db->prepare("SELECT * FROM `order` WHERE planneddate >= :today AND planneddate < :tomorrow AND `type1` < 4 AND `project_id` = 0 AND `team` = 1 AND (`status` = 3 OR `status` = 4) ORDER BY `cutdate` ASC");
		$query->bindParam(":today", $today_midnight, PDO::PARAM_STR);
		$query->bindParam(":tomorrow", $tomorrow_midnight, PDO::PARAM_STR);
		$query->execute(); 

		include('views/_mobile_foreach.php');

		//order in progress 
		$query = $db->prepare("SELECT * FROM `order` WHERE planneddate >= :today AND planneddate < :tomorrow AND `type1` < 4 AND `project_id` = 0 AND `team` = 1 AND `status` = 2 ORDER BY `sort` ASC");
		$query->bindParam(":today", $today_midnight, PDO::PARAM_STR);
		$query->bindParam(":tomorrow", $tomorrow_midnight, PDO::PARAM_STR);
		$query->execute(); 

		include('views/_mobile_foreach.php');

		//next orders
		$query = $db->prepare("SELECT * FROM `order` WHERE planneddate >= :today AND planneddate < :tomorrow AND `type1` < 4 AND `project_id` = 0 AND `team` = 1 AND `status` = 1 ORDER BY `sort` ASC");
		$query->bindParam(":today", $today_midnight, PDO::PARAM_STR);
		$query->bindParam(":tomorrow", $tomorrow_midnight, PDO::PARAM_STR);
		$query->execute(); 

		include('views/_mobile_foreach.php');

		?>
		</table>
		</div>
	</div>
</div>

<div class="row">
	<div class="col-sm-12">
		<div class="panel panel-success">
		<div class="panel-heading"><h4><span class="glyphicon glyphicon-edit"></span>&nbsp;&nbsp;2. Gép</h4> </div>

		<table class='table'>      
		<?php 
		$changeLabel = "1";
		$team = 1;

		//past orders     
		$query = $db->prepare("SELECT * FROM `order` WHERE planneddate >= :today AND planneddate < :tomorrow AND `type1` < 4 AND `project_id` = 0 AND `team` = 2 AND (`status` = 3 OR `status` = 4) ORDER BY `cutdate` ASC");
		$query->bindParam(":today", $today_midnight, PDO::PARAM_STR);
		$query->bindParam(":tomorrow", $tomorrow_midnight, PDO::PARAM_STR);
		$query->execute(); 

		include('views/_mobile_foreach.php');

		//order in progress
		$query = $db->prepare("SELECT * FROM `order` WHERE planneddate >= :today AND planneddate < :tomorrow AND `type1` < 4 AND `project_id` = 0 AND `team` = 2 AND `status` = 2 ORDER BY `sort` ASC");
		$query->bindParam(":today", $today_midnight, PDO::PARAM_STR);
		$query->bindParam(":tomorrow", $tomorrow_midnight, PDO::PARAM_STR);
		$query->execute(); 

		include('views/_mobile_foreach.php');

		//next orders
		$query = $db->prepare("SELECT * FROM `order` WHERE planneddate >= :today AND planneddate < :tomorrow AND `type1` < 4 AND `project_id` = 0 AND `team` = 2 AND `status` = 1 ORDER BY `sort` ASC");
		$query->bindParam(":today", $today_midnight, PDO::PARAM_STR);
		$query->bindParam(":tomorrow", $tomorrow_midnight, PDO::PARAM_STR);
		$query->execute(); 

		include('views/_mobile_foreach.php');

		?>
		</table>
		</div>
	</div>
</div>

</body>

</html>


  


