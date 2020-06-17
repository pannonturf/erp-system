<?php
////////////////////////////////
//get total amount of the day // 
////////////////////////////////

$total_small = 0;   
$total_big = 0;
$total_paused = 0;

$query = $db->prepare("SELECT * FROM `order` WHERE `date` = :datum AND `status` < 5 ORDER BY `time` ASC, `id` ASC");
$query->bindParam(":datum", $datum, PDO::PARAM_STR);
$query->execute(); 

foreach ($query as $row) {
	$type1 = $row['type1'];
	$type3 = $row['type3'];
	$status = $row['status'];
	$project_id = $row['project_id'];
	$amount = getAmount(amount_decrypt($row['amount'], $key2), $type3, $modus);
	
	if ($project_id == 0) {
		if ($type1 < 4) {
	    	$total_small += $amount;

	    	if ($status == 0) {
	    		$total_paused += $amount;
	    	}
	    }
	    else {
	    	$total_big += $amount;
	    }
	}
}

// plus amount of projekt
$query2 = $db->prepare("SELECT * FROM `trucks` WHERE `datum` = :datum");
$query2->bindParam(":datum", $datum, PDO::PARAM_STR);
$query2->execute();
$truck_amount = $query2->rowCount();
foreach ($query2 as $row2) {
	$total_big += $row2['amount'];
}


$total_small_disp = number_format(($total_small - $total_paused), 0, ',', ' ');
$total_paused_disp = number_format($total_paused, 0, ',', ' ');
$total_big_disp = number_format($total_big, 0, ',', ' ');

if ($total_paused == 0) {
	$total_disp = $total_small_disp.' m&sup2';
}
else {
	$total_disp = $total_small_disp.' m&sup2 &nbsp; ( <span class="glyphicon glyphicon-time"></span> '.$total_paused_disp.' m&sup2;)';
}

if ($total_big > 0) {
	$total_disp .= ' &nbsp;(+ '.$total_big_disp.' m&sup2 GR)';
}

?>