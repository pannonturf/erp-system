<?php
///////////////////////////////////
// List of next orders to be cut //
///////////////////////////////////

foreach ($query as $row) {
	$id = $row['id'];
	$id2 = $row['id2'];
	$prefix = $row['prefix'];
	$plannedtime = substr($row['planneddate'], 11, 5);
	$amount = amount_decrypt($row['amount'], $key2);
	$type1 = $row['type1'];
	$type2 = $row['type2'];
	$field = $row['field'];
	$note = $row['note'];
	$forwarder = $row['forwarder'];

	if ($i == 1 AND $change == 1) {
		echo "";
	}
	else {
		//if not in progress -> clickable
		if ($change == 1) {
			echo '<tr class="clickable" id="'.$id.'">';
		}
		else {
			echo '<tr class="nonclickable">';
		}
		
		if ($id2 > 0) {
			echo "<td><b>".$prefix."-".$id2."</b></td>";
		}
		else {
			echo "<td></td>";
		}

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
		 	$type2_display = "<mark style='background: #f62323; color: white;'>MED</mark>";
		} 

		if ($type1 == 1) {
		 	$type1_display = "";
		}
		elseif ($type1 == 3) {
		 	$type1_display = "<mark style='background: #468dc9; color: white;'>vastag</mark>";
		} 
		echo "<td>".$type2_display." ".$type1_display;

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
		echo "</td></tr>";
	}
	
	$i++;
}
?>