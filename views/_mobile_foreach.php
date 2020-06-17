<?php
////////////////////////////////////////////////////
// Single rows for cutting lists for today mobile //
////////////////////////////////////////////////////

foreach ($query as $row) {
	$id = $row['id'];
	$id2 = $row['id2'];
	$prefix = $row['prefix'];
	$name = $row['name'];
	$sort = $row['sort'];
	$team = $row['team'];
	$plannedtime = substr($row['planneddate'], 11, 5);
	$amount = amount_decrypt($row['amount'], $key2);
	$type1 = $row['type1'];
	$type2 = $row['type2'];
	$field = $row['field'];
	$note = $row['note'];
	$status = $row['status'];
	$forwarder = $row['forwarder'];
	$cutdate = substr($row['cutdate'], 0, 10);
	$cuttime = substr($row['cutdate'], 11, 5);

	if ($status == 1) {
		echo '<tr style="color: red;"><td colspan="2">';
	}
	elseif ($status == 2) {
		echo '<tr style="color: orange;"><td colspan="2"><b><u>'.$prefix."-".$id2."</u></b><br>";
	}
	else {
		echo '<tr style="color: green;"><td colspan="2"><b><u>'.$prefix."-".$id2."</u></b><br>";
	}
	
	echo $plannedtime.'</td>';

	$query = $db->prepare("SELECT * FROM customers WHERE `id` = :id");
	$query->bindParam(":id", $name, PDO::PARAM_STR);
	$query->execute();
	$result = $query->fetch(PDO::FETCH_OBJ);
	echo '<td>'.$result->name."</td>";

	echo '<td><b>'.$amount." m&sup2;</b><br>";

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

	echo $field_display."</td>";

	if ($type1 < 3) {
		$type1_display = "";
	}
	elseif ($type1 == 3) {
	 	$type1_display = "<mark style='background: #468dc9; color: white;'>FH</mark>";
	}

	if ($type2 == 1) {
		$type2_display = "";
	}
	elseif ($type2 == 2) {
	 	$type2_display = "<mark style='background: #f62323; color: white;'>MED</mark>";
	} 
	echo "<td>".$type1_display." ".$type2_display;

	if ($note == "" AND $forwarder != 1) {
		echo "";
	}
	else {
		if ($type2 == 2) {
			echo "<br>";
		}
		if ($note == "") {
			echo '<b>Androvic</b>';
		}					
		elseif ($forwarder != 1){
			echo $note;
		}
		else {
			echo $note." | <b>Androvic</b>";
		}
	} 

	// check if order is first or last in list
	$today = date("Y-m-d");
	$tomorrow = date('Y-m-d', strtotime('tomorrow'));
	$today_midnight = date("Y-m-d")." 00:00:00";
	$tomorrow_midnight = $tomorrow." 00:00:00";

	$query = $db->prepare("SELECT * FROM `order` WHERE planneddate >= :start AND planneddate < :end AND `status` = 1 AND `type1` < 4 AND `team` = :team ORDER BY `sort` ASC LIMIT 1");
	$query->bindParam(":start", $today_midnight, PDO::PARAM_STR);
	$query->bindParam(":end", $tomorrow_midnight, PDO::PARAM_STR);
	$query->bindParam(":team", $team, PDO::PARAM_STR);
	$query->execute(); 
	$result = $query->fetch(PDO::FETCH_OBJ);
	$first_sort = $result->sort;

	$query = $db->prepare("SELECT * FROM `order` WHERE planneddate >= :start AND planneddate < :end AND `status` = 1 AND `type1` < 4 AND `team` = :team ORDER BY `sort` DESC LIMIT 1");
	$query->bindParam(":start", $today_midnight, PDO::PARAM_STR);
	$query->bindParam(":end", $tomorrow_midnight, PDO::PARAM_STR);
	$query->bindParam(":team", $team, PDO::PARAM_STR);
	$query->execute(); 
	$result = $query->fetch(PDO::FETCH_OBJ);
	$last_sort = $result->sort;

	if ($status == 1) {
		echo '<td><button class="btn btn-complete btn-sm" style="float: right;" onclick="teamFunction('.$id.', '.$team.')">'.$changeLabel.'</button>';

		echo '<td class="landscape">';

		if ($sort != $first_sort) {
			echo '<button class="btn btn-complete btn-sm" style="float: right;" onclick="moveFunction('.$id.', 1)"><span class="glyphicon glyphicon-arrow-up"></span></button>';
		}
		if ($sort != $last_sort) {
			echo '<button class="btn btn-complete btn-sm" style="float: right;" onclick="moveFunction('.$id.', 2)"><span class="glyphicon glyphicon-arrow-down"></span></button>';
		}
		
	}
	else {
		echo '<td>';
	}


	echo '</td>';

	echo "</td></tr>";
}
?>