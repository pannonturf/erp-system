<?php 
///////////////////////////////////////
// Sales statistics - base for bonus //
///////////////////////////////////////

include('views/_header'.$header.'.php');
include('tools/functions.php');

require_once('config/config.php');
$db = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.'', DB_USER, DB_PASS);
$db -> exec("set names utf8");

$lastMonthStart = date('Y-m-d', strtotime('first day of last month'));
$lastMonthEnd = date('Y-m-d', strtotime('last day of last month'));
$thisMonthStart = date('Y-m-d', strtotime('first day of this month'));
$thisMonthEnd = date('Y-m-d', strtotime('last day of this month'));

$fromDatum = $today;
$toDatum = $today;
?>

<div class="inputform">

	<div class="row">
		<div class="col-md-3"></div>
	    <div class="col-md-3">
	      	<h3 style="margin-top:10px;">ha pénz</h3>  
	    </div>
	    <div class="col-md-6">
	      	<h4 style="margin-top:10px;"><i>magyar kertepítők, kert gyep</i></h4>
	    </div>
	</div>


	<div class="row">
	    <div class="col-md-3"></div>
	    <div class="col-md-6">
	    	<table class='table'>
			<tr class='title'><td class='border'></td><td colspan='2' class="border" style="text-align: center;"><?echo $currentyear;?></td><td colspan='2' style="text-align: center;"><?echo $lastyear;?></td><tr>

			<?php

			////////////////
			/// this year
			echo "<tr><td class='border'></td><td>havi</td><td class='border'>összeadott</td><td>havi</td><td>összeadott</td></tr>";

			//get total amounts of the year     
			$startdate = $currentyear."-01-01 00:00:00";
			$enddate = $nextyear."-01-01 00:00:00";

			$total_this = 0;

		    $query = $db->prepare("SELECT * FROM `order` WHERE `date` >= :startdate AND `date` < :enddate AND `type1` = 1 AND `status` = 4 ORDER BY `date` ASC");
		    $query->bindParam(":startdate", $startdate, PDO::PARAM_STR);
		    $query->bindParam(":enddate", $enddate, PDO::PARAM_STR);
		    $query->execute(); 
		    foreach ($query as $row) {
		        $type2 = $row['type2'];
		        $type3 = $row['type3'];
		        $status = $row['status'];
		        $amount = amount_decrypt($row['amount'], $key2);
		        
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

			echo "<tr class='title'><td class='border'>Összes</td><td>-</td><td class='border'>".$total_this_display."</td>";	

			////////////////
			/// last year

			//get total amounts of the last year     
			$total_last = 0;
			$startdate = $lastyear."-01-01 00:00:00";
			$enddate = $currentyear."-01-01 00:00:00";

			$query = $db->prepare("SELECT * FROM `order` WHERE `date` >= :startdate AND `date` < :enddate AND `type1` = 1 AND `status` = 4 ORDER BY `time` ASC");
			$query->bindParam(":startdate", $startdate, PDO::PARAM_STR);
			$query->bindParam(":enddate", $enddate, PDO::PARAM_STR);
			$query->execute(); 

			if ($query->rowCount() > 0) {
				foreach ($query as $row) {
			        $type2 = $row['type2'];
			        $type3 = $row['type3'];
			        $amount = amount_decrypt($row['amount'], $key2);

			        $name = $row['name'];
			        $query = $db->prepare("SELECT * FROM customers WHERE `id` = :id");
			        $query->bindParam(":id", $name, PDO::PARAM_STR);
			        $query->execute();
			        $result = $query->fetch(PDO::FETCH_OBJ);
					$country = $result->country;
			        
			        if ($country == 0) {
				        $total_last += $amount;
				    }
			        
			    }

				$total_last_display = number_format($total_last, 0, ',', ' ');	

				echo "<td>-</td><td>".$total_last_display."</td>";

			}
			else {
				echo "<td>-</td><td>-</td>";
			}

			echo "</tr>";
			

			////////////////
			/// this year per month
			$cumulated_month_this = 0;
			$cumulated_month_last = 0;
	      	for ($j=0; $j < 12; $j++) { 
	      		$monthName = $months_long[$j];
	      		echo "<tr><td class='border'>".$monthName."</td>";

	      		$month = $j + 1;
	      		if ($month < 10) {
	      			$month = "0".$month;
	      		}

	      		//get total amounts of the month     
				$total_month_this = 0;
				$startdate = $currentyear."-".$month."-01 00:00:00";
				$enddate = $currentyear."-".($month + 1)."-01 00:00:00";

				$query = $db->prepare("SELECT * FROM `order` WHERE `date` >= :startdate AND `type1` = 1 AND `date` < :enddate AND `status` = 4 ORDER BY `time` ASC");
				$query->bindParam(":startdate", $startdate, PDO::PARAM_STR);
				$query->bindParam(":enddate", $enddate, PDO::PARAM_STR);
				$query->execute(); 
				foreach ($query as $row) {
					$type2 = $row['type2'];
		        	$type3 = $row['type3'];
					$amount = amount_decrypt($row['amount'], $key2);

					$name = $row['name'];
			        $query = $db->prepare("SELECT * FROM customers WHERE `id` = :id");
			        $query->bindParam(":id", $name, PDO::PARAM_STR);
			        $query->execute();
			        $result = $query->fetch(PDO::FETCH_OBJ);
					$country = $result->country;
			        
			        if ($country == 0) {
						$total_month_this += $amount;
						$cumulated_month_this += $amount;
					}
				}

				$total_month_this_display = number_format($total_month_this, 0, ',', ' ');
				$cumulated_month_this_display = number_format($cumulated_month_this, 0, ',', ' ');


				echo "<td>".$total_month_this_display."</td>";
				echo "<td class='border'>".$cumulated_month_this_display."</td>";


				////////////////
				/// last year per month

				//get total amounts of the month last year    
				$total_month_last = 0;
				$startdate = $lastyear."-".$month."-01 00:00:00";
				$enddate = $lastyear."-".($month + 1)."-01 00:00:00";

				$query = $db->prepare("SELECT * FROM `order` WHERE `date` >= :startdate AND `date` < :enddate AND `type1` = 1 AND `status` = 4 ORDER BY `time` ASC");
				$query->bindParam(":startdate", $startdate, PDO::PARAM_STR);
				$query->bindParam(":enddate", $enddate, PDO::PARAM_STR);
				$query->execute(); 

				if ($query->rowCount() > 0) {
					foreach ($query as $row) {
						$type2 = $row['type2'];
			        	$type3 = $row['type3'];
						$amount = amount_decrypt($row['amount'], $key2);

						$name = $row['name'];
				        $query = $db->prepare("SELECT * FROM customers WHERE `id` = :id");
				        $query->bindParam(":id", $name, PDO::PARAM_STR);
				        $query->execute();
				        $result = $query->fetch(PDO::FETCH_OBJ);
						$country = $result->country;
				        
				        if ($country == 0) {
							$total_month_last += $amount;
							$cumulated_month_last += $amount;
						}
					}
					$total_month_last_display = number_format($total_month_last, 0, ',', ' ');
					$cumulated_month_last_display = number_format($cumulated_month_last, 0, ',', ' ');

					echo "<td>".$total_month_last_display."</td>";
					echo "<td>".$cumulated_month_last_display."</td>";

		      	}
		      	else {
					echo "<td>-</td><td>-</td>";
				}

				echo "</tr>";
			}

			echo "</table><br><br>";

			?>
		</div>
	</div>


</div>



