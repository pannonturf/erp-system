<?php 
///////////////////////
// History of orders //
///////////////////////

include('views/_header'.$header.'.php');
include('tools/functions.php');

require_once('config/config.php');
$db = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.'', DB_USER, DB_PASS);
$db -> exec("set names utf8");
date_default_timezone_set('Europe/Budapest');

include('views/_edit_database.php'); // Update database when order is edited


//Show edit view, if required        
if (isset($_GET['edit'])) {
	$edit = $_GET['edit'];
	$edit_link2 = "history.php?datum=".$_GET["datum"];
	include('views/_edit.php');	// edit view
}
//Show deliverynote view, if required        
elseif (isset($_GET['note'])) {
	$order_id = $_GET['note'];
	$edit_link2 = "history.php?datum=".$_GET["datum"];
	include('views/_deliverynote.php');	// edit view
}
else {		// show order lists
	$edit_link = "history.php?datum=".$_GET["datum"]."&";

	// get variables for buttons
	$datum_get = $_GET["datum"];
	$lastday = $yesterday;
	$dayHeading = "Tegnap";

	$day_today = date('w', strtotime($today));
	if ($day_today == 1) {
		$lastday = date('Y-m-d', strtotime($yesterday.' -2 day'));
		$dayHeading = "Péntek";
	}

	if ($datum_get == 0) {
		$datum = $lastday;
	}
	else {
		$datum = $datum_get;
	}

	$day = date('w', strtotime($datum));


	$previousday = date('Y-m-d', strtotime($datum.' -1 day'));
	$previousday_nr = date('w', strtotime($previousday));
	if ($previousday_nr == 0) {
		$previousday = date('Y-m-d', strtotime($previousday.' -2 day'));
	}
	$nextday = date('Y-m-d', strtotime($datum.' +1 day'));
	$nextday_nr = date('w', strtotime($nextday));
	if ($nextday_nr == 6) {
		$nextday = date('Y-m-d', strtotime($nextday.' +2 day'));
	}
	?>

	<div class="inputform">

	    <div class="row">
	    	<div class="col-md-3">
		      	<h3 style="margin-top:10px;">Történet</h3>  
		    </div>

		    <div class="col-md-1"><button type="submit" class="btn btn-complete btn-sm" style="float:right;" id="changePrevious" onclick="historyRefresh(2)" value="<?php echo $previousday; ?>"><span class="glyphicon glyphicon-chevron-left"></span></div>

		    <div class="col-md-2">
				<div id="orderdate">
					<input type='date' class="form-control" style="padding-top: 0;" id="historyDatum" onfocusout="historyRefresh(1)" name="historyDatum" max="<?php echo $today; ?>" value="<?php echo $datum; ?>"><br>
				</div>
		    </div>
		    <div class="col-md-1"><button type="submit" class="btn btn-complete btn-sm" id="changeNext" onclick="historyRefresh(3)" value="<?php echo $nextday; ?>"><span class="glyphicon glyphicon-chevron-right"></span></div>
		   	<div class="col-md-1"><button type="submit" class="btn btn-complete btn-sm" style="float:right;" id="changeYesterday" onclick="historyRefresh(4)" value="<?php echo $lastday; ?>"><?php echo $dayHeading; ?></button></td></div>

		    <div class="col-md-1"><button type="submit" class="btn btn-complete btn-sm" id="changeToday" onclick="historyRefresh(5)" value="<?php echo $today; ?>">Ma</button></td></div>

		    <?php
		    if ($login == 1) { 
		    	echo '<div class="col-md-1"><button type="button" class="btn btn-complete btn-sm" role="group" data-toggle="modal" data-target="#kassa">Kassa</button></td></div>';
		    }
		    ?>
	  	</div>

	  	<div class="row">
		    <div class="col-md-12">
		    	<div id="history-output">
		    		<?php
		    		$day = date('w', strtotime($datum));
					$days = array("Vasárnap", "Hétfő", "Kedd", "Szerda", "Csütörtök", "Péntek", "Szombat");

					include('tools/get-amounts.php');	// get small and big roll amounts of the day

					$total_this = 0;

					//ha penz for this day
				    $query = $db->prepare("SELECT * FROM `order` WHERE `date` = :datum AND `type1` = 1 AND `status` = 4 ORDER BY `time` ASC");
				    $query->bindParam(":datum", $datum, PDO::PARAM_STR);
				    $query->execute(); 
				    foreach ($query as $row) {
				        $type2 = $row['type2'];
				        $type3 = $row['type3'];
				        $status = $row['status'];
						$amount = getAmount(amount_decrypt($row['amount'], $key2), $type3, $modus);
				        
				        $name = $row['name'];
				        $query = $db->prepare("SELECT * FROM customers WHERE `id` = :id");
				        $query->bindParam(":id", $name, PDO::PARAM_STR);
				        $query->execute();
				        $result = $query->fetch(PDO::FETCH_OBJ);
						$country = $result->country;
				        
				        if ($country == 0) {
					        $total_this += $amount;
					    }
				    }

					$total_this_display = number_format($total_this, 0, ',', ' ');

					?>

					<div class="panel panel-primary">
					<!-- Default panel contents -->
					<div class="panel-heading"><h4><span class="glyphicon glyphicon-time"></span>&nbsp;&nbsp;Megrendelések - <?echo $datum;?></h4> </div>

					<?php

					echo "<table class='table'>";
					echo '<tr><td colspan="9">'.$days[$day].",&nbsp;".$datum.'</td><td colspan="4" style="text-align:right;"><b>'.$total_disp.'</b> &nbsp;|&nbsp; <i>'.$total_this_display.' m&sup2</i></td></tr>';

					$check = 1;
					$history_page = 1;

					include('views/_listpoints.php');	// include rows


					echo "</table></div>";
					?>
		    	</div>
		    </div>
	  	</div>
	</div>

	<!-- MODAL -->
	<!-- Kassa check -->
	<div class="modal fade" id="kassa" tabindex="-1" role="dialog">
	<div class="modal-dialog" role="document" style="width: 500px;">
	  <div class="modal-content">
	    <div class="modal-header">
	      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	      <h4 class="modal-title">Kassa check - <?php echo $today; ?></h4>
	    </div>

	    <?php
	    echo '<div class="row">';
	    	echo '<div class="col-md-2"></div>';
    		echo '<div class="col-md-8">';
	    		echo "<table class='table'>";

			    echo "<tr class='title'><td></td><td>ID</td><td>m&sup2;</td><td>Ft.</td><td>Sz.</td></tr>";

			    $query = $db->prepare("SELECT * FROM `order` WHERE `date` = :datum AND `status` = 4 AND `type3` = 2 AND `paid` = 1 ORDER BY `id` ASC");
			    $query->bindParam(":datum", $datum, PDO::PARAM_STR);
			    $query->execute(); 

			    foreach ($query as $row) {
			        $id = $row['id'];
			        $id2 = $row['id2'];
			        $id3 = $row['id3'];
			        $prefix = $row['prefix'];
			        $name = $row['name'];
			        $amount = getAmount(amount_decrypt($row['amount'], $key2), $type3, $modus);

			        echo '<tr>';

			        echo '<td><input type="checkbox"></td>';

			        // ID
			        echo '<td><b>'.$id."</b></td>";


			        // Amount
			        $amount2 = getAmount3($amount);
			        echo '<td>'.$amount2.' m&sup2;</td>';

			        // Amount 3
			        $amount3 = number_format(($amount2 * 1080), 0, ',', ' ');
			        echo '<td>'.$amount3.' Ft</td>';

			        // ID 2/3
			        $id3_display = substr($id3, -2);
			        if ($id3_display == "00") {
			            $id3_display = 100;
			        }

			        if ($id2 > 0 AND $cutting_modus == 1) {     // day prefix + number - cutting mode 1
			            echo '<td><b>'.$prefix."-".$id2."</td>";
			            
			        }
			        elseif ($id3 > 0 AND $cutting_modus == 2) {     // number running from 1 - 100 - cutting mode 2
			            echo '<td>'.$id3_display."</td>";
			        }
			        else {
			            echo "<td></td>";
			        }

			       

			        echo "</tr>";
			    }
				    
			    echo "</table>";
		echo "</div></div>";

	    ?>
	  </div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->
<?php
}
?>

<br><br><br><br><br><br>