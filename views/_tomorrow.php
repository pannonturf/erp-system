<?php 
///////////////////////////////////////////////////////////
// Show list of orders to be cut tomorrow (outside team) //
///////////////////////////////////////////////////////////

include('views/_header2.php');
include('tools/functions.php');

require_once('config/config.php');
$db = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.'', DB_USER, DB_PASS);
$db -> exec("set names utf8");

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

$days = array("Vasárnap", "Hétfő", "Kedd", "Szerda", "Csütörtök", "Péntek", "Szombat");

$nextday = date('Y-m-d', strtotime('tomorrow'));
$day = date('w', strtotime($nextday));

if ($day == 6) {
	$nextday = date('Y-m-d', strtotime($nextday.' +2 days'));
	$nextdayname = date('w', strtotime($nextday));
	$dayHeading = $days[$nextdayname];
	$dayHeading2 = $days[$nextdayname];
	$dayHeading3 = "Hétfői";
}
elseif ($day == 0) {
	$nextday = date('Y-m-d', strtotime($nextday.' +1 days'));
	$nextdayname = date('w', strtotime($nextday));
	$dayHeading = $days[$nextdayname];
	$dayHeading2 = $days[$nextdayname];
	$dayHeading3 = "Hétfői";
}
else {
	$dayHeading = "Holnap";
	$dayHeading2 = $days[$day];
	$dayHeading3 = "Holnapi";
}

$nextday_midnight = $nextday." 00:00:00";
$after = $nextday." 09:00:01";
?>

<div class="inputform">

  	<div class="row">
	    <div class="col-md-10">
	      <h3 style="margin-top:10px;"><? echo $dayHeading3;?> kistekercs vágás (reggel)</h3>  
	    </div>
	</div>

<div class="row">
	<div class="col-sm-12">

		<table class='table'>
		<tr class='title'><td><span class='glyphicon glyphicon-time'></span></td><td>m&sup2;</td><td>Terület</td><td><span class='glyphicon glyphicon-comment'></span></td></tr>
      
		<?php 
		$i = 1;
		//get next order     
		$query = $db->prepare("SELECT * FROM `order` WHERE planneddate >= :start AND planneddate < :end AND `status` = 1 AND `type1` < 4 AND `project_id` = 0 AND `team` = :team ORDER BY `sort` ASC");
		$query->bindParam(":start", $nextday_midnight, PDO::PARAM_STR);
		$query->bindParam(":end", $after, PDO::PARAM_STR);
		$query->bindParam(":team", $team, PDO::PARAM_STR);
		$query->execute(); 

		foreach ($query as $row) {
			$id = $row['id'];
			$id_display = substr($id, -3);
			$plannedtime = substr($row['planneddate'], 11, 5);
			$amount = amount_decrypt($row['amount'], $key2);
			$type1 = $row['type1'];
			$type2 = $row['type2'];
			$field = $row['field'];
			$note = $row['note'];

			echo '<tr>';
			echo '<td>'.$plannedtime."</td>";
			echo '<td><b>'.$amount."</b></td>";

			$query = $db->prepare("SELECT * FROM fields WHERE `id` = :id");
			$query->bindParam(":id", $field, PDO::PARAM_STR);
			$query->execute();
			$result = $query->fetch(PDO::FETCH_OBJ);
			echo "<td>".$result->name."</td>";

			if ($type2 == 1) {
				$type2_display = "";
			}
			elseif ($type2 == 2) {
			 	$type2_display = "<mark style='background: #f62323; color: white;'>MED</mark>";
			} 
			if ($type1 == 1) {
			 	$type1_display = "";
			}
			elseif ($type1 == 3) {
			 	$type1_display = "<mark style='background: #468dc9; color: white;'>vastag</mark>";
			} 
			echo "<td>".$type2_display." ".$type1_display;

			if ($note == "") {
				echo "</td></tr>";
			}
			else {
				if ($type2 == 1) {
					echo $note."</td></tr>";
				}
				else {
					echo "<br>".$note."</td></tr>";
				} 	
			} 
			
			$i++;
		}
		?>
		</table>
		<br><br><br><br><br><br>
	</div>
</div>

</body>

</html>


  


