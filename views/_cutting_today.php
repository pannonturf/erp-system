<?php 
////////////////////////////////////////////////////////
// Show list of orders to be cut today (outside team) //
////////////////////////////////////////////////////////

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
?>

<div class="inputform">

  	<div class="row">
	    <div class="col-sm-6">
	      <h3 style="margin-top:10px;">Utolsó 8</h3>  
	    </div>
	</div>

<div class="row">
	<div class="col-sm-12">

		<table class='table'>
		<tr class='title'><td>Sz</td><td><span class='glyphicon glyphicon-time'></span></td><td>m&sup2;</td><td>Terület</td><td><span class='glyphicon glyphicon-comment'></span></td></tr>
      
		<?php 
		$i = 1;
		//get next order     
		$query = $db->prepare("SELECT * FROM `order` WHERE (`status` = 3 OR `status` = 4) AND `type1` < 4 AND `project_id` = 0 AND `team` = :team ORDER BY `cutdate` DESC LIMIT 8");
		//$query->bindParam(":today", $today, PDO::PARAM_STR);
		//$query->bindParam(":tomorrow", $tomorrow, PDO::PARAM_STR);
		$query->bindParam(":team", $team, PDO::PARAM_STR);
		$query->execute(); 

		foreach ($query as $row) {
			$id = $row['id'];
			$id_display = substr($id, -3);
			$id2 = $row['id2'];
			$prefix = $row['prefix'];
			$plannedtime = substr($row['planneddate'], 11, 5);
			$amount = amount_decrypt($row['amount'], $key2);
			$type1 = $row['type1'];
			$type2 = $row['type2'];
			$field = $row['field'];
			$note = $row['note'];
			$status = $row['status'];
			$cutdate = $row['cutdate'];

			echo '<tr style="color: green;"><td>'.$prefix."-".$id2."</td>";
			echo '<td>'.$plannedtime."</td>";
			echo '<td><b>'.$amount."</b></td>";

			$query = $db->prepare("SELECT * FROM fields WHERE `id` = :id");
			$query->bindParam(":id", $field, PDO::PARAM_STR);
			$query->execute();
			$result = $query->fetch(PDO::FETCH_OBJ);

			if ($field == 111111) {
				$field_display = "?";
			}
			else {
				$field_display = $result->name;
			}
			echo "<td>".$field_display."</td>";

			if ($type2 == 1) {
				$type2_display = "";
			}
			elseif ($type2 == 2) {
			 	$type2_display = "MED";
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

  


