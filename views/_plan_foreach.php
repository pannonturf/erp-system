<?php
//////////////////////////////////////////////////////
// Single rows for cutting lists for today and plan //
//////////////////////////////////////////////////////

foreach ($query as $row) {
	$id = $row['id'];
	$id2 = $row['id2'];
	$prefix = $row['prefix'];
	$datum = $row['date'];
	$time = $row['time'];
	$planneddate = $row['planneddate'];
	$name = $row['name'];
	$amount = amount_decrypt($row['amount'], $key2);
	$type1 = $row['type1'];
	$type2 = $row['type2'];
	$field = $row['field'];
	$note = $row['note'];
	$sort = $row['sort'];
	$status = $row['status'];
	$forwarder = $row['forwarder'];
	$nowDate = date('Y-m-d H:i:s');

	if ($status == 0) {		// order is put on hold
		echo '<tr class="paused" id="'.$id.'">';
	}
	elseif ($status == 2) {		// order is in progress
		echo '<tr class="order_progress nodrop nodrag" id="'.$id.'">';
	}
	else {		// next orders
		echo '<tr id="'.$id.'">';
	}

	// today
	if ($id2 > 0) {		// show order number if already assigned
		echo "<td><b>".$prefix."-".$id2."</b></td>";
	}
	else {
		echo "<td></td>";
	}


	$timedisplay = substr($time, 0, 5);
	echo "<td>".$timedisplay;

	if ($datum == $tomorrow) {		// mark if date is not today
		echo " <mark style='background: #ff0;'>(+1)</mark>";
	}
	elseif ($datum == $yesterday) {
		echo " <mark style='background: #ff0;'>(-1)</mark>";
	}
	echo "</td>";

	echo '<td>';
	if ($status != 2) {		// no control buttons if order is already in progress

		if ($status == 0) {		// show right control to put on/off hold
			echo '<button class="btn btn-complete btn-sm paused_btn" style="margin-right: 5px;" onclick="statusFunction('.$id.', 1, 1)"><span class="glyphicon glyphicon-time"></span></button>';
		}
		else {
			echo '<button class="btn btn-complete btn-sm" style="margin-right: 5px;" onclick="statusFunction('.$id.', 0, 1)"><span class="glyphicon glyphicon-time"></span></button>';
		}

		if ($now == 1) {		// today
			if ($datum == $tomorrow) {		// show button to put order back to tomorrow
				//echo '<button type="submit" class="btn btn-complete btn-sm"" name="changeDay2" value="'.$id.'">Holnap</button>';
				echo '<button class="btn btn-complete btn-sm" onclick="changeDay('.$id.', 2)">Holnap</button>';
			}
		}

		if ($check == 2) {		// plan	
			if ($day > 1) {					// no option to move to previous day on Monday
				//echo '<button type="submit" class="btn btn-complete btn-sm"" name="changeDay" value="'.$id.'">Ma</button></td>';
				echo '<button class="btn btn-complete btn-sm" onclick="changeDay('.$id.', 1)">Ma</button>';
			}
		}
	}
	echo "</td>";

	$query = $db->prepare("SELECT * FROM customers WHERE `id` = :id");
	$query->bindParam(":id", $name, PDO::PARAM_STR);
	$query->execute();
	$result = $query->fetch(PDO::FETCH_OBJ);
	echo "<td>".$result->name."</td>";

	echo "<td>".$amount."</td>";

	if ($type2 == 1) {
		$type2_display = "";
	}
	elseif ($type2 == 2) {
	 	$type2_display = "<mark style='background: #f62323; color: white;'>MED</mark>";
	} 

	if ($type1 == 1) {
	 	$type1_display = "";
	}
	elseif ($type1 == 2) {
	 	$type1_display = "Stadion";
	}
	elseif ($type1 == 3) {
	 	$type1_display = "<mark style='background: #468dc9; color: white;'>2,5 cm</mark>";
	} 
	echo "<td>".$type2_display." ".$type1_display."</td>";

	$query = $db->prepare("SELECT * FROM fields WHERE `id` = :id");
	$query->bindParam(":id", $field, PDO::PARAM_STR);
	$query->execute();
	$result = $query->fetch(PDO::FETCH_OBJ);

	if ($field == 111111) {		// field still unknown
		$field_display = "?";
	}
	else {
		$field_display = $result->name;
	}
	
	if ($status == 2) {
		echo '<td style="padding-left: 25px;">'.$field_display."</td>";
	}
	else {		// if order not in progress yet, show checkbox to change field
		echo '<td><input type="checkbox" id="editField'.$i.'" name="editField'.$id.'" onClick="untoggle()"> '.$field_display."</td>";
		echo '<input type="hidden" name="id['.$id.']" value="1">';
		echo '<input type="hidden" name="datum" value="'.$nextday.'">';
	}
	
	echo "<td style='width: 80px;'>";

	if ($note != "") {			// add separation sign, if there is a note
		echo "<i>".$note."</i>";
		if ($forwarder == 1) {
			echo " | <mark style='background: #ff0;'>Androvic</mark>";
		}
	}
	elseif ($forwarder == 1) {
		echo "<mark style='background: #ff0;'>Androvic</mark>";
	}
	echo "</td>";

	if ($status == 2) {		// show button to cancel order in progress
		echo '<td> <button class="btn btn-danger btn-sm" style="float: right;" onclick="statusFunction('.$id.', 1, 1)">Mégse</button></td>';
	}	
	else {					// show button to change team
		echo '<td> <button class="btn btn-complete btn-sm" style="float: right;" onclick="teamFunction('.$id.', '.$team.')">'.$changeLabel.'</button></td>';
	}
	
	echo '</tr>';

	$previoustime = $time;
	$i++;

}
?>