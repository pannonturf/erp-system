<?php
//////////////////////////////////////
// Single rows of the order tables  //
//////////////////////////////////////   

$stripe = 1;
$project_count = 0;

if ($check < 4) {		// today, next day and other orders	
	$query6 = $db->prepare("SELECT * FROM `order` WHERE `date` = :datum AND `status` < 5 ORDER BY `time` ASC, `id` ASC");
}
elseif ($check == 4) {   	// open orders	 
	//query6 = $db->prepare("SELECT * FROM `order` WHERE `date` = :datum AND ((`status` = 4 AND `paid` = 0 AND `payment` = 1) OR `status` < 4)  ORDER BY `time` ASC");
	$query6 = $db->prepare("SELECT * FROM `order` WHERE `date` = :datum AND ((`status` = 4 AND `paid` = 0 AND (`payment` = 1 OR (`payment` = 2 AND `type3` = 2))) OR `status` < 4)  ORDER BY `time` ASC");
}

$query6->bindParam(":datum", $datum, PDO::PARAM_STR);
$query6->execute(); 

$count = $query6->rowCount();

// exclude customers from open orders, who are allowed to have bulk payments 
if ($check == 4) {

	$count = 0;
	$query7 = $db->prepare("SELECT * FROM `order` WHERE `date` = :datum AND ((`status` = 4 AND `paid` = 0 AND (`payment` = 1 OR (`payment` = 2 AND `type3` = 2))) OR `status` < 4)  ORDER BY `time` ASC");
	$query7->bindParam(":datum", $datum, PDO::PARAM_STR);
	$query7->execute(); 
	foreach ($query7 as $row) {
		$name1 = $row['name'];
		$status = $row['status'];

		$query = $db->prepare("SELECT * FROM customers WHERE `id` = :id");
	    $query->bindParam(":id", $name1, PDO::PARAM_STR);
	    $query->execute();
	    $result = $query->fetch(PDO::FETCH_OBJ);
		$bulk = $result->bulk;

		if ($bulk == 0 OR ($bulk == 1 AND $status == 0)) {
			$count ++;
		}
	}
}


if ($check < 3) {
	// headings just for today and next day
	echo "<tr class='title'><td>Sz.</td><td>Időpont</td><td>Vevő</td><td>m&sup2;</td><td colspan='2'>Tipus</td><td>Terület</td><td>Szállitás</td><td>Fizetés</td><td class='note'><span class='glyphicon glyphicon-comment'></span></td><td class='more'>Státusz</td><td></td></tr>";

	// always show today and next day
	$count = 1;
}

else {
	if ($t == 1) {	// show project view only for the first day of the further orders
		$query5 = $db->prepare("SELECT * FROM `trucks` WHERE `datum` = :datum ORDER BY `sort` ASC");
		$query5->bindParam(":datum", $datum, PDO::PARAM_STR);
		$query5->execute();
		$project_count = $query5->rowCount();
		$border = "";
		
		if ($project_count > 0) {
			$count = 1;		// also show day, if only project
			$check2 = 1;
		}
	}
}

if ($count > 0) {		// only show, if orders are on that day

	if ($check > 2) {
		echo '<tr><td colspan="8" class="day_row">'.$dayHeading2.",&nbsp;".$datum.'</td>';
		if ($check == 3) {
			include('tools/get-amounts.php');	// get small and big roll amounts of the day

			echo '<td class="day_row" colspan="4" style="text-align:right;"><i>'.$total_disp.'</i></td></tr>';
		}
		else {
			echo '<td colspan="4"></td></tr>';
		}

		if ($project_count > 0) {
			include('views/_listpoints_projekt.php');	// show project row
			$border = "truck_lower";
		}

	}

	if ($query6->rowCount() > 0) {

		// check if enough pallets are cut (cutting mode 2)
		if ($cutting_modus == 2) {
			$pickedup = array();
			// set up array for fields
			$query = $db->prepare("SELECT * FROM fields WHERE `cutting` = 1 AND `complete` < 1");
			$query->execute();
			foreach ($query as $row) {
				$field_id = $row['id'];

				// check, if order is assigned to field today
				$query2 = $db->prepare("SELECT * FROM `order` WHERE `date` = :datum AND `field` = :field AND `type1` = 1");
				$query2->bindParam(":datum", $datum, PDO::PARAM_STR);
				$query2->bindParam(":field", $field_id, PDO::PARAM_STR);
				$query2->execute();
				$count = $query2->rowCount();

				if ($count > 0) {

					// get amount already picked up
					foreach ($query2 as $row) {
						$amount = getAmount(amount_decrypt($row['amount'], $key2), $type3, $modus);
						$type1 = $row['type1'];
						$pallet = $row['pallet'];
						$status = $row['status'];
						$pickup = $row['pickup'];
						
						if ($type1 == 1 AND $pickup == 1) {
							$pickedup[$field_id][$pallet] += $amount;
						}
					}

					for ($k=1; $k < 4; $k++) { 
						$cut[$field_id][$k] = 0; 				// pallets finished
						$cumulated[$field_id][$k] = 0;			// cumulated amounts
					}
				}
			}

			//print_r($pickedup);

			// get amounts already cut
			$query = $db->prepare("SELECT * FROM `pallets` WHERE `datum` = :datum ORDER BY `id` ASC");
			$query->bindParam(":datum", $datum, PDO::PARAM_STR);
			$query->execute(); 

			foreach ($query as $row) {
				$field_id = $row['field'];
				$type = $row['type'];
				$amount = $row['amount'];

				$cut[$field_id][$type] += $amount;
			}
		}

		foreach ($query6 as $row) {
			include('views/_listpoints_foreach.php');	// include rows
		}

	}
	else {
		echo "<tr><td colspan='13'>Nincs</td></tr>";
	}


	if ($check == 3 AND $t == 1 AND $cutting_modus == 1) {
		?>
		<tr style="padding-top:10px;">
			<td colspan="13">
				<button type="button" class="btn btn-info" onclick="document.location = 'plan2.php'">Vágás - <?php echo $dayHeading2; ?></button>
			</td>
		</tr>
	<?php
	}

	$c++;
}

?>