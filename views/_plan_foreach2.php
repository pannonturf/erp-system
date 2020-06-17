<?php
//////////////////////////////////////////////////////
// Single rows for cutting lists for today and plan //
//////////////////////////////////////////////////////

foreach ($query as $row) {
	$id = $row['id'];
	$id3 = $row['id3'];
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
	if ($id3 > 0) {		// show order number if already assigned
		$id3_display = substr($id3, -2);

		if ($id3_display == "00") {
			$id3_display = 100;
		}
		echo "<td><b>".$id3_display."</b></td>";
	}
	else {
		echo "<td></td>";
	}


	$timedisplay = substr($time, 0, 5);
	echo "<td>".$timedisplay."</td>";

	echo '<td>';
	if ($status == 0) {		// show right control to put on/off hold
		echo '<button class="btn btn-complete btn-sm paused_btn" style="margin-right: 5px;" onclick="statusFunction('.$id.', 1, 1)"><span class="glyphicon glyphicon-time"></span></button>';
	}
	else {
		echo '<button class="btn btn-complete btn-sm" style="margin-right: 5px;" onclick="statusFunction('.$id.', 0, 1)"><span class="glyphicon glyphicon-time"></span></button>';
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
	
	// show checkbox to change field
	echo '<td><input type="checkbox" id="editField'.$i.'" name="editField'.$id.'" onClick="untoggle()"> '.$field_display."</td>";
	echo '<input type="hidden" name="id['.$id.']" value="1">';
	echo '<input type="hidden" name="datum" value="'.$nextday.'">';
	
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

	if ($status == 4) {		// order is finished in the office
		echo '<td><span class="glyphicon glyphicon-ok"></span></td>';
	}
	else {
		echo "<td></td>";
	}
	
	echo '</tr>';

	$previoustime = $time;
	$i++;

}
?>