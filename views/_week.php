<?php 
////////////////////////////////////
// Statistics about certain weeks //
////////////////////////////////////

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

if (isset($_GET['week'])) {
	$selectedweek = $_GET['week'];
}
else {
	$selectedweek = date("W");
}

$previousweek = $selectedweek - 1;
$nextweek = $selectedweek + 1;

// get all future orders of small rolls
$query = $db->prepare("SELECT * FROM `order` WHERE `date` > :startdate AND `status` = 1 AND `type1` < 4 ORDER BY `date` ASC");
$query->bindParam(":startdate", $today, PDO::PARAM_STR);
$query->execute(); 
foreach ($query as $row) {
    $amount = amount_decrypt($row['amount'], $key2);
    $kr_total += $amount;
}
?>

<div class="inputform">

	<div class="row">
	    <div class="col-md-4">
	      	<h3 style="margin-top:10px;">Héti statisztika</h3>  
	    </div>
	    <form method="get" action="<?php echo $_SERVER['PHP_SELF']; ?>" accept-charset="utf-8">
	    <div class="col-md-1">
	    	<input type='number' class="form-control" style="padding: 0px 10px; margin-top: 5px;" name="week" min="1" max="52" value="<?php echo $selectedweek; ?>">
	    </div>
	    <div class="col-md-2">
			<button type="submit" class="btn btn-primary" style="margin-top: 5px;">Küldés</button>
		</div>
		</form>
		<div class="col-md-2">
	    </div>
	    <div class="col-md-3" style="padding-top: 10px;">
	      	<div>Megrendelt KR: &nbsp; &nbsp;<? echo number_format($kr_total, 0, ',', ' '); ?> m&sup2;</div>  
	    </div>
		
	</div>

	<div class="row">
	    <div class="col-md-3" style="color:grey; border: 1px solid gray;">
	    	<h4><i>Hét <?echo $previousweek;?></i></h4>

	    	<?php
			$start = new DateTime();
			$start->setISODate($currentyear,$previousweek);
			$startday = $start->format('Y-m-d'); 
			$endday = date('Y-m-d', strtotime($startday.' +1 week'));
	    	
	    	echo $startday;
			echo "<table class='table table-bordered'>";
			echo "<tr class='title'><td></td><td class='border' style='text-align: center;'>".$currentyear.'</td><td colspan="2" style="text-align: center;">'.$lastyear.'</td><tr>';

			////////////////
			/// THIS YEAR
			//get total amounts of the year     
			$startdate = $startday." 00:00:00";
			$enddate = $endday." 00:00:00";

			$total = 0;
			$kr = 0;
			$bonus = 0;
			$gr = 0;
			$poa = 0;
			$med = 0;
			$orders = 0;
			$orders_50 = 0;
			$orders_150 = 0;
			$orders_400 = 0;
			$orders_above = 0;
			$customers = 0;
			$areas = array();
			$foreign = 0;

			$query = $db->prepare("SELECT * FROM areas");
			$query->execute();
			foreach ($query as $row) {
				$area_id = $row['id'];
				$areas[$area_id] = 0;
			}

		    $query = $db->prepare("SELECT * FROM `order` WHERE `date` >= :startdate AND `date` < :enddate AND `status` < 5 AND `status` > 0 ORDER BY `date` ASC");
		    $query->bindParam(":startdate", $startdate, PDO::PARAM_STR);
		    $query->bindParam(":enddate", $enddate, PDO::PARAM_STR);
		    $query->execute(); 
		    foreach ($query as $row) {
		    	$type1 = $row['type1'];
		        $type2 = $row['type2'];
		        $amount = amount_decrypt($row['amount'], $key2);

		        $name = $row['name'];
		        $query = $db->prepare("SELECT * FROM customers WHERE `id` = :id");
		        $query->bindParam(":id", $name, PDO::PARAM_STR);
		        $query->execute();
		        $result = $query->fetch(PDO::FETCH_OBJ);
				$country = $result->country;

		        $total += $amount;

		        if ($type1 < 4) {
		        	$kr += $amount;
		        }
		        elseif ($type1 > 3) {
		        	$gr += $amount;
		        }

		        if ($type1 == 1 AND $country == 0) {
			        $bonus += $amount;
			    }

		        if ($type2 == 1) {
		        	$poa += $amount;
		        }
		        elseif ($type2 == 2) {
		        	$med += $amount;
		        }

		        $orders ++;

		        if ($amount < 50) {
			    	$orders_50 ++;
			    }
		        elseif ($amount < 150) {
			    	$orders_150 ++;
			    }
			    elseif ($amount < 400) {
			    	$orders_400 ++;
			    }
			    else {
			    	$orders_above ++;
			    }
		    }

		    /*
		    // Customer statistics this year
			$query = $db->prepare("SELECT * FROM `customers` WHERE `status` = 1");
			$query->execute(); 

			foreach ($query as $row) {
				$id = $row['id'];
				$country = $row['country'];
				$area = $row['area'];
				$total2 = 0;

				$query = $db->prepare("SELECT * FROM `order` WHERE `date` >= :startdate AND `date` <= :enddate AND `name` = :name AND `status` < 5");
				$query->bindParam(":startdate", $startdate, PDO::PARAM_STR);
				$query->bindParam(":enddate", $enddate, PDO::PARAM_STR);
				$query->bindParam(":name", $id, PDO::PARAM_STR);
				$query->execute(); 

				foreach ($query as $row) {
			        $amount = amount_decrypt($row['amount'], $key2);
			        $total2 += $amount;
			    }	

			    if ($total2 > 0) {
				    $customers ++;

			        if ($country == 0) {
			        	$areas[$area] += $total2;
			        }
			        else {
			        	$foreign += $total2;
			        }  
			    }
			}
			*/


			////////////////
			/// LAST YEAR
			//get total amounts of the year     
			$start = new DateTime();
			$start->setISODate($lastyear,$previousweek);
			$startday = $start->format('Y-m-d'); 
			$endday = date('Y-m-d', strtotime($startday.' +1 week'));
			$startdate = $startday." 00:00:00";
			$enddate = $endday." 00:00:00";

			$total_last = 0;
			$kr_last = 0;
			$bonus_last = 0;
			$gr_last = 0;
			$poa_last = 0;
			$med_last = 0;
			$orders_last = 0;
			$orders_50_last = 0;
			$orders_150_last = 0;
			$orders_400_last = 0;
			$orders_above_last = 0;
			$customers_last = 0;
			$areas_last = array();
			$foreign_last = 0;

			$query = $db->prepare("SELECT * FROM areas");
			$query->execute();
			foreach ($query as $row) {
				$area_id = $row['id'];
				$areas_last[$area_id] = 0;
			}

		    $query = $db->prepare("SELECT * FROM `order` WHERE `date` >= :startdate AND `date` < :enddate AND `status` < 5 AND `status` > 0 ORDER BY `date` ASC");
		    $query->bindParam(":startdate", $startdate, PDO::PARAM_STR);
		    $query->bindParam(":enddate", $enddate, PDO::PARAM_STR);
		    $query->execute(); 
		    foreach ($query as $row) {
		    	$type1 = $row['type1'];
		        $type2 = $row['type2'];
		        $amount = amount_decrypt($row['amount'], $key2);

		        $name = $row['name'];
		        $query = $db->prepare("SELECT * FROM customers WHERE `id` = :id");
		        $query->bindParam(":id", $name, PDO::PARAM_STR);
		        $query->execute();
		        $result = $query->fetch(PDO::FETCH_OBJ);
				$country = $result->country;

		        $total_last += $amount;

		        if ($type1 < 4) {
		        	$kr_last += $amount;
		        }
		        elseif ($type1 > 3) {
		        	$gr_last += $amount;
		        }

		        if ($type1 == 1 AND $country == 0) {
			        $bonus_last += $amount;
			    }

		        if ($type2 == 1) {
		        	$poa_last += $amount;
		        }
		        elseif ($type2 == 2) {
		        	$med_last += $amount;
		        }

		        $orders_last ++;

		        if ($amount < 50) {
			    	$orders_50_last ++;
			    }
		        elseif ($amount < 150) {
			    	$orders_150_last ++;
			    }
			    elseif ($amount < 400) {
			    	$orders_400_last ++;
			    }
			    else {
			    	$orders_above_last ++;
			    }
		    }

		    /*
		    // Customer statistics this year
			$query = $db->prepare("SELECT * FROM `customers` WHERE `status` = 1");
			$query->execute(); 

			foreach ($query as $row) {
				$id = $row['id'];
				$country = $row['country'];
				$area = $row['area'];
				$total2 = 0;

				$query = $db->prepare("SELECT * FROM `order` WHERE `date` >= :startdate AND `date` <= :enddate AND `name` = :name AND `status` < 5");
				$query->bindParam(":startdate", $startdate, PDO::PARAM_STR);
				$query->bindParam(":enddate", $enddate, PDO::PARAM_STR);
				$query->bindParam(":name", $id, PDO::PARAM_STR);
				$query->execute(); 

				foreach ($query as $row) {
			        $amount = amount_decrypt($row['amount'], $key2);
			        $total2 += $amount;
			    }	

			    if ($total2 > 0) {
				    $customers_last ++;

			        if ($country == 0) {
			        	$areas_last[$area] += $total2;
			        }
			        else {
			        	$foreign_last += $total2;
			        }  
			    }
			}
			*/

			echo "<tr><td>KR</td><td>".number_format($kr, 0, ',', ' ')."</td><td>".number_format($kr_last, 0, ',', ' ')."</td></tr>";
			echo "<tr><td><i>ha pénz</i></td><td><i>".number_format($bonus, 0, ',', ' ')."</i></td><td><i>".number_format($bonus_last, 0, ',', ' ')."</i></td></tr>";
			echo "<tr><td>GR</td><td>".number_format($gr, 0, ',', ' ')."</td><td>".number_format($gr_last, 0, ',', ' ')."</td></tr>";
			echo "<tr><td><b>TOTAL</b></td><td><b>".number_format($total, 0, ',', ' ')."</b></td><td><b>".number_format($total_last, 0, ',', ' ')."</b></td></tr>";
			echo "<tr><td>&nbsp;</td><td></td><td></td></tr>";
			echo "<tr><td>Poa</td><td>".number_format($poa, 0, ',', ' ')."</td><td>".number_format($poa_last, 0, ',', ' ')."</td></tr>";
			echo "<tr><td>MED</td><td>".number_format($med, 0, ',', ' ')."</td><td>".number_format($med_last, 0, ',', ' ')."</td></tr>";
			echo "<tr><td>&nbsp;</td><td></td><td></td></tr>";
			//echo "<tr><td>Vevő</td><td>".number_format($customers, 0, ',', ' ')."</td><td>".number_format($customers_last, 0, ',', ' ')."</td></tr>";
			echo "<tr><td>Megrendelés</td><td>".number_format($orders, 0, ',', ' ')."</td><td>".number_format($orders_last, 0, ',', ' ')."</td></tr>";
			echo "<tr><td>0 - 50 m&sup2;</td><td>".number_format($orders_50, 0, ',', ' ')."</td><td>".number_format($orders_50_last, 0, ',', ' ')."</td></tr>";
			echo "<tr><td>50 - 150 m&sup2;</td><td>".number_format($orders_150, 0, ',', ' ')."</td><td>".number_format($orders_150_last, 0, ',', ' ')."</td></tr>";
			echo "<tr><td>150 - 400 m&sup2;</td><td>".number_format($orders_400, 0, ',', ' ')."</td><td>".number_format($orders_400_last, 0, ',', ' ')."</td></tr>";
			echo "<tr><td>400+ m&sup2;</td><td>".number_format($orders_above, 0, ',', ' ')."</td><td>".number_format($orders_above_last, 0, ',', ' ')."</td></tr>";
			echo "<tr><td>&nbsp;</td><td></td><td></td></tr>";

			/*
			foreach ($areas as $area_id => $area_amount) {
				$query = $db->prepare("SELECT * FROM areas WHERE `id` = :id");
				$query->bindParam(":id", $area_id, PDO::PARAM_STR);
				$query->execute();
				$result = $query->fetch(PDO::FETCH_OBJ);

				echo "<tr><td>".$result->short."</td><td class='border'>".number_format($area_amount, 0, ',', ' ')."</td><td>".number_format($areas_last[$area_id], 0, ',', ' ')."</td></tr>";
			}
			*/

		    ?>
			</table>
	    </div>

	    <div class="col-md-1"><a href="week.php?week=<?echo $previousweek;?>"><button type="submit" class="btn btn-complete btn-sm" style="float:right; margin-top: 6px;"><span class="glyphicon glyphicon-chevron-left"></span></button></a></div>

	    <div class="col-md-4" style="background-color: #f4fbf3; border: 1px solid gray;">
	    	<h4><i>Hét <?echo $selectedweek;?></i></h4>
	    	<?php
			$start = new DateTime();
			$start->setISODate($currentyear,$selectedweek);
			$startday = $start->format('Y-m-d'); 
			$endday = date('Y-m-d', strtotime($startday.' +1 week'));
	    	
	    	echo $startday;
			echo "<table class='table table-bordered'>";
			echo "<tr class='title'><td></td><td class='border' style='text-align: center;'>".$currentyear.'</td><td colspan="2" style="text-align: center;">'.$lastyear.'</td><tr>';

			////////////////
			/// THIS YEAR
			//get total amounts of the year     
			$startdate = $startday." 00:00:00";
			$enddate = $endday." 00:00:00";

			$total = 0;
			$kr = 0;
			$bonus = 0;
			$gr = 0;
			$poa = 0;
			$med = 0;
			$orders = 0;
			$orders_50 = 0;
			$orders_150 = 0;
			$orders_400 = 0;
			$orders_above = 0;
			$customers = 0;
			$areas = array();
			$foreign = 0;

			$query = $db->prepare("SELECT * FROM areas");
			$query->execute();
			foreach ($query as $row) {
				$area_id = $row['id'];
				$areas[$area_id] = 0;
			}

		    $query = $db->prepare("SELECT * FROM `order` WHERE `date` >= :startdate AND `date` < :enddate AND `status` < 5 AND `status` > 0 ORDER BY `date` ASC");
		    $query->bindParam(":startdate", $startdate, PDO::PARAM_STR);
		    $query->bindParam(":enddate", $enddate, PDO::PARAM_STR);
		    $query->execute(); 
		    foreach ($query as $row) {
		    	$type1 = $row['type1'];
		        $type2 = $row['type2'];
		        $amount = amount_decrypt($row['amount'], $key2);

		        $name = $row['name'];
		        $query = $db->prepare("SELECT * FROM customers WHERE `id` = :id");
		        $query->bindParam(":id", $name, PDO::PARAM_STR);
		        $query->execute();
		        $result = $query->fetch(PDO::FETCH_OBJ);
				$country = $result->country;

		        $total += $amount;

		        if ($type1 < 4) {
		        	$kr += $amount;
		        }
		        elseif ($type1 > 3) {
		        	$gr += $amount;
		        }

		        if ($type1 == 1 AND $country == 0) {
			        $bonus += $amount;
			    }

		        if ($type2 == 1) {
		        	$poa += $amount;
		        }
		        elseif ($type2 == 2) {
		        	$med += $amount;
		        }

		        $orders ++;

		        if ($amount < 50) {
			    	$orders_50 ++;
			    }
		        elseif ($amount < 150) {
			    	$orders_150 ++;
			    }
			    elseif ($amount < 400) {
			    	$orders_400 ++;
			    }
			    else {
			    	$orders_above ++;
			    }
		    }

		    /*
		    // Customer statistics this year
			$query = $db->prepare("SELECT * FROM `customers` WHERE `status` = 1");
			$query->execute(); 

			foreach ($query as $row) {
				$id = $row['id'];
				$country = $row['country'];
				$area = $row['area'];
				$total2 = 0;

				$query = $db->prepare("SELECT * FROM `order` WHERE `date` >= :startdate AND `date` <= :enddate AND `name` = :name AND `status` < 5");
				$query->bindParam(":startdate", $startdate, PDO::PARAM_STR);
				$query->bindParam(":enddate", $enddate, PDO::PARAM_STR);
				$query->bindParam(":name", $id, PDO::PARAM_STR);
				$query->execute(); 

				foreach ($query as $row) {
			        $amount = amount_decrypt($row['amount'], $key2);
			        $total2 += $amount;
			    }	

			    if ($total2 > 0) {
				    $customers ++;

			        if ($country == 0) {
			        	$areas[$area] += $total2;
			        }
			        else {
			        	$foreign += $total2;
			        }  
			    }
			}
			*/


			////////////////
			/// LAST YEAR
			//get total amounts of the year     
			$start = new DateTime();
			$start->setISODate($lastyear,$selectedweek);
			$startday = $start->format('Y-m-d'); 
			$endday = date('Y-m-d', strtotime($startday.' +1 week'));
			$startdate = $startday." 00:00:00";
			$enddate = $endday." 00:00:00";

			$total_last = 0;
			$kr_last = 0;
			$bonus_last = 0;
			$gr_last = 0;
			$poa_last = 0;
			$med_last = 0;
			$orders_last = 0;
			$orders_50_last = 0;
			$orders_150_last = 0;
			$orders_400_last = 0;
			$orders_above_last = 0;
			$customers_last = 0;
			$areas_last = array();
			$foreign_last = 0;

			$query = $db->prepare("SELECT * FROM areas");
			$query->execute();
			foreach ($query as $row) {
				$area_id = $row['id'];
				$areas_last[$area_id] = 0;
			}

		    $query = $db->prepare("SELECT * FROM `order` WHERE `date` >= :startdate AND `date` < :enddate AND `status` < 5 AND `status` > 0 ORDER BY `date` ASC");
		    $query->bindParam(":startdate", $startdate, PDO::PARAM_STR);
		    $query->bindParam(":enddate", $enddate, PDO::PARAM_STR);
		    $query->execute(); 
		    foreach ($query as $row) {
		    	$type1 = $row['type1'];
		        $type2 = $row['type2'];
		        $amount = amount_decrypt($row['amount'], $key2);

		        $name = $row['name'];
		        $query = $db->prepare("SELECT * FROM customers WHERE `id` = :id");
		        $query->bindParam(":id", $name, PDO::PARAM_STR);
		        $query->execute();
		        $result = $query->fetch(PDO::FETCH_OBJ);
				$country = $result->country;

		        $total_last += $amount;

		        if ($type1 < 4) {
		        	$kr_last += $amount;
		        }
		        elseif ($type1 > 3) {
		        	$gr_last += $amount;
		        }

		        if ($type1 == 1 AND $country == 0) {
			        $bonus_last += $amount;
			    }

		        if ($type2 == 1) {
		        	$poa_last += $amount;
		        }
		        elseif ($type2 == 2) {
		        	$med_last += $amount;
		        }

		        $orders_last ++;

		        if ($amount < 50) {
			    	$orders_50_last ++;
			    }
		        elseif ($amount < 150) {
			    	$orders_150_last ++;
			    }
			    elseif ($amount < 400) {
			    	$orders_400_last ++;
			    }
			    else {
			    	$orders_above_last ++;
			    }
		    }

		    /*
		    // Customer statistics this year
			$query = $db->prepare("SELECT * FROM `customers` WHERE `status` = 1");
			$query->execute(); 

			foreach ($query as $row) {
				$id = $row['id'];
				$country = $row['country'];
				$area = $row['area'];
				$total2 = 0;

				$query = $db->prepare("SELECT * FROM `order` WHERE `date` >= :startdate AND `date` <= :enddate AND `name` = :name AND `status` < 5");
				$query->bindParam(":startdate", $startdate, PDO::PARAM_STR);
				$query->bindParam(":enddate", $enddate, PDO::PARAM_STR);
				$query->bindParam(":name", $id, PDO::PARAM_STR);
				$query->execute(); 

				foreach ($query as $row) {
			        $amount = amount_decrypt($row['amount'], $key2);
			        $total2 += $amount;
			    }	

			    if ($total2 > 0) {
				    $customers_last ++;

			        if ($country == 0) {
			        	$areas_last[$area] += $total2;
			        }
			        else {
			        	$foreign_last += $total2;
			        }  
			    }
			}
			*/

			echo "<tr><td>KR</td><td>".number_format($kr, 0, ',', ' ')."</td><td>".number_format($kr_last, 0, ',', ' ')."</td></tr>";
			echo "<tr><td><i>ha pénz</i></td><td><i>".number_format($bonus, 0, ',', ' ')."</i></td><td><i>".number_format($bonus_last, 0, ',', ' ')."</i></td></tr>";
			echo "<tr><td>GR</td><td>".number_format($gr, 0, ',', ' ')."</td><td>".number_format($gr_last, 0, ',', ' ')."</td></tr>";
			echo "<tr><td><b>TOTAL</b></td><td><b>".number_format($total, 0, ',', ' ')."</b></td><td><b>".number_format($total_last, 0, ',', ' ')."</b></td></tr>";
			echo "<tr><td>&nbsp;</td><td></td><td></td></tr>";
			echo "<tr><td>Poa</td><td>".number_format($poa, 0, ',', ' ')."</td><td>".number_format($poa_last, 0, ',', ' ')."</td></tr>";
			echo "<tr><td>MED</td><td>".number_format($med, 0, ',', ' ')."</td><td>".number_format($med_last, 0, ',', ' ')."</td></tr>";
			echo "<tr><td>&nbsp;</td><td></td><td></td></tr>";
			//echo "<tr><td>Vevő</td><td>".number_format($customers, 0, ',', ' ')."</td><td>".number_format($customers_last, 0, ',', ' ')."</td></tr>";
			echo "<tr><td>Megrendelés</td><td>".number_format($orders, 0, ',', ' ')."</td><td>".number_format($orders_last, 0, ',', ' ')."</td></tr>";
			echo "<tr><td>0 - 50 m&sup2;</td><td>".number_format($orders_50, 0, ',', ' ')."</td><td>".number_format($orders_50_last, 0, ',', ' ')."</td></tr>";
			echo "<tr><td>50 - 150 m&sup2;</td><td>".number_format($orders_150, 0, ',', ' ')."</td><td>".number_format($orders_150_last, 0, ',', ' ')."</td></tr>";
			echo "<tr><td>150 - 400 m&sup2;</td><td>".number_format($orders_400, 0, ',', ' ')."</td><td>".number_format($orders_400_last, 0, ',', ' ')."</td></tr>";
			echo "<tr><td>400+ m&sup2;</td><td>".number_format($orders_above, 0, ',', ' ')."</td><td>".number_format($orders_above_last, 0, ',', ' ')."</td></tr>";
			echo "<tr><td>&nbsp;</td><td></td><td></td></tr>";

			/*
			foreach ($areas as $area_id => $area_amount) {
				$query = $db->prepare("SELECT * FROM areas WHERE `id` = :id");
				$query->bindParam(":id", $area_id, PDO::PARAM_STR);
				$query->execute();
				$result = $query->fetch(PDO::FETCH_OBJ);

				echo "<tr><td>".$result->name." (".$result->short.")</td><td class='border'>".number_format($area_amount, 0, ',', ' ')."</td><td>".number_format($areas_last[$area_id], 0, ',', ' ')."</td></tr>";
			}
			*/

		    ?>
			</table>
		</div>

	    <div class="col-md-1"><a href="week.php?week=<?echo $nextweek;?>"><button type="submit" class="btn btn-complete btn-sm" style="float:left; margin-top: 6px;"><span class="glyphicon glyphicon-chevron-right"></span></button></a></div>
	    
	    <div class="col-md-3" style="color:grey; border: 1px solid gray;">
	    	<h4><i>Hét <?echo $nextweek;?></i></h4>

	    	<?php
			$start = new DateTime();
			$start->setISODate($currentyear,$nextweek);
			$startday = $start->format('Y-m-d'); 
			$endday = date('Y-m-d', strtotime($startday.' +1 week'));
	    	
	    	echo $startday;
			echo "<table class='table table-bordered'>";
			echo "<tr class='title'><td></td><td class='border' style='text-align: center;'>".$currentyear.'</td><td colspan="2" style="text-align: center;">'.$lastyear.'</td><tr>';

			////////////////
			/// THIS YEAR
			//get total amounts of the year     
			$startdate = $startday." 00:00:00";
			$enddate = $endday." 00:00:00";

			$total = 0;
			$kr = 0;
			$bonus = 0;
			$gr = 0;
			$poa = 0;
			$med = 0;
			$orders = 0;
			$orders_50 = 0;
			$orders_150 = 0;
			$orders_400 = 0;
			$orders_above = 0;
			$customers = 0;
			$areas = array();
			$foreign = 0;

			$query = $db->prepare("SELECT * FROM areas");
			$query->execute();
			foreach ($query as $row) {
				$area_id = $row['id'];
				$areas[$area_id] = 0;
			}

		    $query = $db->prepare("SELECT * FROM `order` WHERE `date` >= :startdate AND `date` < :enddate AND `status` < 5 AND `status` > 0 ORDER BY `date` ASC");
		    $query->bindParam(":startdate", $startdate, PDO::PARAM_STR);
		    $query->bindParam(":enddate", $enddate, PDO::PARAM_STR);
		    $query->execute(); 
		    foreach ($query as $row) {
		    	$type1 = $row['type1'];
		        $type2 = $row['type2'];
		        $amount = amount_decrypt($row['amount'], $key2);

		        $name = $row['name'];
		        $query = $db->prepare("SELECT * FROM customers WHERE `id` = :id");
		        $query->bindParam(":id", $name, PDO::PARAM_STR);
		        $query->execute();
		        $result = $query->fetch(PDO::FETCH_OBJ);
				$country = $result->country;

		        $total += $amount;

		        if ($type1 < 4) {
		        	$kr += $amount;
		        }
		        elseif ($type1 > 3) {
		        	$gr += $amount;
		        }

		        if ($type1 == 1 AND $country == 0) {
			        $bonus += $amount;
			    }

		        if ($type2 == 1) {
		        	$poa += $amount;
		        }
		        elseif ($type2 == 2) {
		        	$med += $amount;
		        }

		        $orders ++;

		        if ($amount < 50) {
			    	$orders_50 ++;
			    }
		        elseif ($amount < 150) {
			    	$orders_150 ++;
			    }
			    elseif ($amount < 400) {
			    	$orders_400 ++;
			    }
			    else {
			    	$orders_above ++;
			    }
		    }

		    /*
		    // Customer statistics this year
			$query = $db->prepare("SELECT * FROM `customers` WHERE `status` = 1");
			$query->execute(); 

			foreach ($query as $row) {
				$id = $row['id'];
				$country = $row['country'];
				$area = $row['area'];
				$total2 = 0;

				$query = $db->prepare("SELECT * FROM `order` WHERE `date` >= :startdate AND `date` <= :enddate AND `name` = :name AND `status` < 5");
				$query->bindParam(":startdate", $startdate, PDO::PARAM_STR);
				$query->bindParam(":enddate", $enddate, PDO::PARAM_STR);
				$query->bindParam(":name", $id, PDO::PARAM_STR);
				$query->execute(); 

				foreach ($query as $row) {
			        $amount = amount_decrypt($row['amount'], $key2);
			        $total2 += $amount;
			    }	

			    if ($total2 > 0) {
				    $customers ++;

			        if ($country == 0) {
			        	$areas[$area] += $total2;
			        }
			        else {
			        	$foreign += $total2;
			        }  
			    }
			}
			*/


			////////////////
			/// LAST YEAR
			//get total amounts of the year     
			$start = new DateTime();
			$start->setISODate($lastyear,$nextweek);
			$startday = $start->format('Y-m-d'); 
			$endday = date('Y-m-d', strtotime($startday.' +1 week'));
			$startdate = $startday." 00:00:00";
			$enddate = $endday." 00:00:00";

			$total_last = 0;
			$kr_last = 0;
			$bonus_last = 0;
			$gr_last = 0;
			$poa_last = 0;
			$med_last = 0;
			$orders_last = 0;
			$orders_50_last = 0;
			$orders_150_last = 0;
			$orders_400_last = 0;
			$orders_above_last = 0;
			$customers_last = 0;
			$areas_last = array();
			$foreign_last = 0;

			$query = $db->prepare("SELECT * FROM areas");
			$query->execute();
			foreach ($query as $row) {
				$area_id = $row['id'];
				$areas_last[$area_id] = 0;
			}

		    $query = $db->prepare("SELECT * FROM `order` WHERE `date` >= :startdate AND `date` < :enddate AND `status` < 5 AND `status` > 0 ORDER BY `date` ASC");
		    $query->bindParam(":startdate", $startdate, PDO::PARAM_STR);
		    $query->bindParam(":enddate", $enddate, PDO::PARAM_STR);
		    $query->execute(); 
		    foreach ($query as $row) {
		    	$type1 = $row['type1'];
		        $type2 = $row['type2'];
		        $amount = amount_decrypt($row['amount'], $key2);

		        $name = $row['name'];
		        $query = $db->prepare("SELECT * FROM customers WHERE `id` = :id");
		        $query->bindParam(":id", $name, PDO::PARAM_STR);
		        $query->execute();
		        $result = $query->fetch(PDO::FETCH_OBJ);
				$country = $result->country;

		        $total_last += $amount;

		        if ($type1 < 4) {
		        	$kr_last += $amount;
		        }
		        elseif ($type1 > 3) {
		        	$gr_last += $amount;
		        }

		        if ($type1 == 1 AND $country == 0) {
			        $bonus_last += $amount;
			    }

		        if ($type2 == 1) {
		        	$poa_last += $amount;
		        }
		        elseif ($type2 == 2) {
		        	$med_last += $amount;
		        }

		        $orders_last ++;

		        if ($amount < 50) {
			    	$orders_50_last ++;
			    }
		        elseif ($amount < 150) {
			    	$orders_150_last ++;
			    }
			    elseif ($amount < 400) {
			    	$orders_400_last ++;
			    }
			    else {
			    	$orders_above_last ++;
			    }
		    }

		    /*
		    // Customer statistics this year
			$query = $db->prepare("SELECT * FROM `customers` WHERE `status` = 1");
			$query->execute(); 

			foreach ($query as $row) {
				$id = $row['id'];
				$country = $row['country'];
				$area = $row['area'];
				$total2 = 0;

				$query = $db->prepare("SELECT * FROM `order` WHERE `date` >= :startdate AND `date` <= :enddate AND `name` = :name AND `status` < 5");
				$query->bindParam(":startdate", $startdate, PDO::PARAM_STR);
				$query->bindParam(":enddate", $enddate, PDO::PARAM_STR);
				$query->bindParam(":name", $id, PDO::PARAM_STR);
				$query->execute(); 

				foreach ($query as $row) {
			        $amount = amount_decrypt($row['amount'], $key2);
			        $total2 += $amount;
			    }	

			    if ($total2 > 0) {
				    $customers_last ++;

			        if ($country == 0) {
			        	$areas_last[$area] += $total2;
			        }
			        else {
			        	$foreign_last += $total2;
			        }  
			    }
			}
			*/

			echo "<tr><td>KR</td><td>".number_format($kr, 0, ',', ' ')."</td><td>".number_format($kr_last, 0, ',', ' ')."</td></tr>";
			echo "<tr><td><i>ha pénz</i></td><td><i>".number_format($bonus, 0, ',', ' ')."</i></td><td><i>".number_format($bonus_last, 0, ',', ' ')."</i></td></tr>";
			echo "<tr><td>GR</td><td>".number_format($gr, 0, ',', ' ')."</td><td>".number_format($gr_last, 0, ',', ' ')."</td></tr>";
			echo "<tr><td><b>TOTAL</b></td><td><b>".number_format($total, 0, ',', ' ')."</b></td><td><b>".number_format($total_last, 0, ',', ' ')."</b></td></tr>";
			echo "<tr><td>&nbsp;</td><td></td><td></td></tr>";
			echo "<tr><td>Poa</td><td>".number_format($poa, 0, ',', ' ')."</td><td>".number_format($poa_last, 0, ',', ' ')."</td></tr>";
			echo "<tr><td>MED</td><td>".number_format($med, 0, ',', ' ')."</td><td>".number_format($med_last, 0, ',', ' ')."</td></tr>";
			echo "<tr><td>&nbsp;</td><td></td><td></td></tr>";
			//echo "<tr><td>Vevő</td><td>".number_format($customers, 0, ',', ' ')."</td><td>".number_format($customers_last, 0, ',', ' ')."</td></tr>";
			echo "<tr><td>Megrendelés</td><td>".number_format($orders, 0, ',', ' ')."</td><td>".number_format($orders_last, 0, ',', ' ')."</td></tr>";
			echo "<tr><td>0 - 50 m&sup2;</td><td>".number_format($orders_50, 0, ',', ' ')."</td><td>".number_format($orders_50_last, 0, ',', ' ')."</td></tr>";
			echo "<tr><td>50 - 150 m&sup2;</td><td>".number_format($orders_150, 0, ',', ' ')."</td><td>".number_format($orders_150_last, 0, ',', ' ')."</td></tr>";
			echo "<tr><td>150 - 400 m&sup2;</td><td>".number_format($orders_400, 0, ',', ' ')."</td><td>".number_format($orders_400_last, 0, ',', ' ')."</td></tr>";
			echo "<tr><td>400+ m&sup2;</td><td>".number_format($orders_above, 0, ',', ' ')."</td><td>".number_format($orders_above_last, 0, ',', ' ')."</td></tr>";
			echo "<tr><td>&nbsp;</td><td></td><td></td></tr>";

			/*
			foreach ($areas as $area_id => $area_amount) {
				$query = $db->prepare("SELECT * FROM areas WHERE `id` = :id");
				$query->bindParam(":id", $area_id, PDO::PARAM_STR);
				$query->execute();
				$result = $query->fetch(PDO::FETCH_OBJ);

				echo "<tr><td>".$result->short."</td><td class='border'>".number_format($area_amount, 0, ',', ' ')."</td><td>".number_format($areas_last[$area_id], 0, ',', ' ')."</td></tr>";
			}
			*/
	
		    ?>
			</table>
	    </div>
	</div>


</div>



