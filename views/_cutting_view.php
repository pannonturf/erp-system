<?php 
////////////////////////////////
// Show the order in progress //
////////////////////////////////


$result = $query->fetch(PDO::FETCH_OBJ);
						
$next_id = $result->id;
$next_id_display = substr($next_id, -3);
$next_id2 = $result->id2;
$next_prefix = $result->prefix;
$next_plannedtime = substr($result->planneddate, 11, 5);
$next_amount = amount_decrypt($result->amount, $key2);
$next_field = $result->field;
$next_type1 = $result->type1;
$next_type2 = $result->type2;
$next_note = $result->note;
$next_forwarder = $result->forwarder;
$today_midnight = date("Y-m-d")." 00:00:00";

echo '<div class="row'.$bg.'">';
	echo '<div class="col-xs-7">';
		if ($next_id2 > 0) {
			echo "<div class='next_id'>".$next_prefix."-".$next_id2."</div>";
		}
		else {
			echo "";
		}
	echo '</div>';
	echo '<div class="col-xs-5">';
		echo "<div class='next_time' style='margin-top:40px;'><i>".$next_plannedtime;

		if ($result->planneddate < $today_midnight) {
			echo " <mark style='background: #ff0;'>(-1)</mark>";
		}

		echo "</i></div>";
	echo "</div>";
echo "</div>";
echo '<div class="row'.$bg.'">';
	echo '<div class="col-xs-12">';
		echo "<div class='next_amount' style='margin-top:10px;'>".$next_amount." m&sup2;</div>";

		$query = $db->prepare("SELECT * FROM fields WHERE `id` = :id");
		$query->bindParam(":id", $next_field, PDO::PARAM_STR);
		$query->execute();
		$result = $query->fetch(PDO::FETCH_OBJ);

		if ($field == 111111) {
			$field_display = "?";
		}
		else {
			$field_display = $result->name;
		}
		echo "<div class='next_details'>".$field_display."</div>";

		if ($next_type2 == 1) {
			$type2_display = "";
		}
		elseif ($next_type2 == 2) {
		 	$type2_display = "<mark style='background: #ff0;'><b>MED</b></mark>";
		} 
		if ($next_type1 == 1) {
		 	$type1_display = "";
		}
		elseif ($next_type1 == 3) {
		 	$type1_display = "<mark style='background: #468dc9; color: white;'>vastag</mark>";
		} 
		echo "<div class='next_details'><i>".$type2_display." ".$type1_display."</i></div>";

		if ($next_note != "") {
			echo "<div class='next_details'><mark style='background: #ff0;'><i>".$next_note."</i>";

			if ($next_forwarder == 1) {
				echo ' | <b>Androvic</b>';
			}
			echo "</mark></div>";
		}
		elseif ($next_forwarder == 1) {
			echo "<div class='next_details'><mark style='background: #ff0;'><b>Androvic</b></mark></div>";
		}

		echo '<div class="complete_button"><button class="btn btn-'.$btn.' btn-lg" onclick="statusFunction('.$next_id.', '.$status_change.', 1)">'.$btn_title.'</button>';
		
		if ($change == 0) {
			echo '<button class="btn btn-default btn-lg" style="border:none; border-radius:10px; margin-left:20px;" onclick="statusFunction('.$next_id.', 1, 1)"><span class="glyphicon glyphicon-remove"></span></button>';

		}
		echo '</div>';
	echo '</div>';
echo '</div>';

?>