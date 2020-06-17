<?php 
////////////////////////////////////////////////////
// List of customers + all orders of each customer//
////////////////////////////////////////////////////

//$statistics = 2;
$edit_link = "customer";

include('views/_header'.$header.'.php');
include('tools/functions.php');

require_once('config/config.php');
$db = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.'', DB_USER, DB_PASS);
$db -> exec("set names utf8");

$lastMonthStart = date('Y-m-d', strtotime('first day of last month'));
$lastMonthEnd = date('Y-m-d', strtotime('last day of last month'));
$thisMonthStart = date('Y-m-d', strtotime('first day of this month'));
$thisMonthEnd = date('Y-m-d', strtotime('last day of this month'));
$lastYearStart = $lastyear."-01-01";
$lastYearEnd = $lastyear."-12-31";
$thisYearStart = $currentyear."-01-01";
$thisYearEnd = $currentyear."-12-31";

$fromDatum = $today;
$toDatum = $today;

include('views/_edit_database.php'); // Update database when order is edited


/////////
//if customer was edited
if (isset($_POST['editCustomerForm'])) {
  //Get variables from form
  $customer_id = $_POST['customer_id'];
  $name = $_POST['customer_name'];
  $payment_standard = $_POST['payment_standard'];
  $delivery_standard = $_POST['delivery_standard'];
  $country = $_POST['country'];
  $area = $_POST['area'];
  $type = $_POST['type'];

  //Update stock of agents
  $query = $db->prepare("UPDATE `customers` SET `name` = :name, `country` = :country, `area` = :area, `type` = :type, `delivery` = :delivery, `payment` = :payment WHERE `id` = :id");
  $query->bindParam(":id", $customer_id, PDO::PARAM_STR);
  $query->bindParam(":name", $name, PDO::PARAM_STR);
  $query->bindParam(":country", $country, PDO::PARAM_STR);
  $query->bindParam(":area", $area, PDO::PARAM_STR);
  $query->bindParam(":type", $type, PDO::PARAM_STR);
  $query->bindParam(":delivery", $delivery_standard, PDO::PARAM_STR);
  $query->bindParam(":payment", $payment_standard, PDO::PARAM_STR);
  $query->execute();   

  echo '<div class="alert alert-success center-block" role="alert">Modosítás sikerült!</div>';

}

///////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////

//Show edit view, if required        
if (isset($_GET['edit'])) {
	$edit = $_GET['edit'];
	$edit_link2 = "customer.php?customer_id=".$_GET["customer_id"]."&fromDatum=".$_GET["fromDatum"]."&toDatum=".$_GET["toDatum"]."&search-customer=Submit";
	
	include('views/_edit.php');	// edit view
}
//Show deliverynote view, if required        
elseif (isset($_GET['note'])) {
	$order_id = $_GET['note'];
	$edit_link2 = "customer.php?customer_id=".$_GET["customer_id"]."&fromDatum=".$_GET["fromDatum"]."&toDatum=".$_GET["toDatum"]."&search-customer=Submit";
	include('views/_deliverynote.php');	// edit view
}
else {		// show order lists
?>

	<div class="inputform">

		<h3 style="margin-top:10px;">Vevők</h3> <br> 

		<div class="row select hidden-print">
			<div class="col-md-3">
				<div class="form-group">
			        <label for="customer">Vevő</label>
					<?php
					if (isset($_GET['search-customer'])) {
						$customer_id = $_GET['customer_id'];
						
						$query = $db->prepare("SELECT * FROM customers WHERE `id` = :id");
						$query->bindParam(":id", $customer_id, PDO::PARAM_STR);
						$query->execute();
						$result = $query->fetch(PDO::FETCH_OBJ);
						$customer_name = $result->name;

						$fromDatum = $_GET['fromDatum'];
						$toDatum = $_GET['toDatum'];

						echo '<input class="form-control" type="text" name="customer" id="customer_input2" value="'.$customer_name.'" />';
						echo '<form method="get" action="'.$_SERVER['PHP_SELF'].'" accept-charset="utf-8">';
						echo '<input name="customer_id" id="customer_id" value="'.$customer_id.'" type="hidden"/>';
					}
					else {
						echo '<input class="form-control" type="text" name="customer" id="customer_input2" required />';
						echo '<form method="get" action="'.$_SERVER['PHP_SELF'].'" accept-charset="utf-8">';
						echo '<input name="customer_id" id="customer_id" value="0" type="hidden"/>';
					}
					?>			
			    </div>

			    <button type="button" class="btn btn-success" style:"float:left;" name="search-customer" onclick="lastYear()"><?php echo $lastyear; ?></button>
				<input id="lastYearStart" value="<?php echo $lastYearStart; ?>" type="hidden"/>
				<input id="lastYearEnd" value="<?php echo $lastYearEnd; ?>" type="hidden"/>

			    <button type="button" class="btn btn-success" style:"float:left;" name="search-customer" onclick="thisYear()"><?php echo $currentyear; ?></button>
				<input id="thisYearStart" value="<?php echo $thisYearStart; ?>" type="hidden"/>
				<input id="thisYearEnd" value="<?php echo $thisYearEnd; ?>" type="hidden"/>

				<button type="button" class="btn btn-success" style:"float:left;" name="search-customer" onclick="lastMonth()"><?php echo $months[$currentmonth-2]; ?></button>
				<input id="lastMonthStart" value="<?php echo $lastMonthStart; ?>" type="hidden"/>
				<input id="lastMonthEnd" value="<?php echo $lastMonthEnd; ?>" type="hidden"/>

				<button type="button" class="btn btn-success" style:"float:left;" name="search-customer" onclick="thisMonth()"><?php echo $months[$currentmonth-1]; ?></button>
				<input id="thisMonthStart" value="<?php echo $thisMonthStart; ?>" type="hidden"/>
				<input id="thisMonthEnd" value="<?php echo $thisMonthEnd; ?>" type="hidden"/>
		    </div>

		    <div class="col-md-2">
		    	<label for="fromDatum">-tól</label>
				<input type='date' class="form-control" style="padding: 0px 10px;" name="fromDatum" id="fromDatum" min="2018-01-01" value="<?php echo $fromDatum; ?>"><br>

			</div>

		    <div class="col-md-2">
		    	<label for="toDatum">-ig</label>
				<input type='date' class="form-control" style="padding: 0px 10px;" name="toDatum" id="toDatum" min="2018-01-01" value="<?php echo $toDatum; ?>">
			</div>

			<div class="col-md-2">
				<button type="submit" class="btn btn-primary" style="margin-top: 25px;" name="search-customer" value="Submit">Küldés</button>
			</div>

			<?php
			if (isset($_GET['search-customer'])) {
				$edit_link = "customer.php?customer_id=".$_GET["customer_id"]."&fromDatum=".$_GET["fromDatum"]."&toDatum=".$_GET["toDatum"]."&";
				?>
				<div class="col-md-3">
					<button type="button" style="font-size: 30px;margin-top: 20px;" class="close" onclick="document.location = 'customer.php'"><span aria-hidden="true">&times;</span></button>
				</div>
			<?php
			}
			echo "</form></div>";


		///////////////////////////////////////////////////////////////////////////////////////////////////////////////
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////

		if (isset($_GET['search-customer'])) {

			$total = 0;
			$total_type3_1 = 0;
			$total_type3_2 = 0;
			$count = 0;
			$query = $db->prepare("SELECT * FROM `order` WHERE `date` >= :startdate AND `date` <= :enddate AND `name` = :name AND `status` = 4 ORDER BY `date` ASC");
			$query->bindParam(":startdate", $fromDatum, PDO::PARAM_STR);
			$query->bindParam(":enddate", $toDatum, PDO::PARAM_STR);
			$query->bindParam(":name", $customer_id, PDO::PARAM_STR);
			$query->execute(); 

			foreach ($query as $row) {
				$amount = amount_decrypt($row['amount'], $key2);
				$total += $amount;
				$count ++;

				if ($row['type2'] == 1) {
		        	$total_type2_1 += $amount;
		        }
		        elseif ($row['type2'] == 2) {
		        	$total_type2_2 += $amount;
		        } 

				if ($row['type3'] == 1) {
		        	$total_type3_1 += $amount;
		        }
		        elseif ($row['type3'] == 2) {
		        	$total_type3_2 += $amount;
		        } 
			}
			$total = number_format($total, 0, ',', ' ');
			$total_type2_1 = number_format($total_type2_1, 0, ',', ' ');
			$total_type2_2 = number_format($total_type2_2, 0, ',', ' ');
			$total_type3_1 = number_format(getType3($total_type3_1, $total_type3_2, 1), 0, ',', ' ');
			$total_type3_2 = number_format(getType3($total_type3_1, $total_type3_2, 2), 0, ',', ' ');


			?>
			<div class="row">
			    <div class="col-md-4">
			      <h4 style="margin-top:10px;"><?php echo $customer_name; ?></h3>  
			    </div>
			    <div class="col-md-2">
			    	<?php
			    	// Button for list of tomorrows orders for Istvan
			    	if ($customer_id == 7) {
			    	?>
			    		<form method="post" action="overview_tomorrow.php" accept-charset="utf-8">
				    		<input type="hidden" name="id" value="<?php echo $customer_id; ?>">
				    		<button type="submit" class="btn btn-warning" style="margin-top: 5px;" name="search-customer" value="Submit">Holnapi lista</button>
			    		</form>
			    	<?php
			    	}
			    	?>


			    </div>
			     <div class="col-md-2">
				    <div class="panel panel-default">
						<div class="panel-body">
							<b>Összes: &nbsp;&nbsp; <?php echo $total; ?> m&sup2;</b>
						</div>
					</div>
				</div>
			    <div class="col-md-4">
				    <div class="row">
			    		<div class="col-md-4" style="padding-bottom: 0px;">
			    			<?php echo $count; ?> x
			    		</div>
			    		<div class="col-md-4" style="padding-bottom: 0px;">
						    <div class="panel panel-default">
								<div class="panel-body" style="text-align: center;">
									<?php echo $total_type3_1; ?> m&sup2;
								</div>
							</div>
						</div>
						<div class="col-md-4" style="padding-bottom: 0px; padding-right:10px;">
			    			<div class="panel panel-default">
								<div class="panel-body" style="text-align: center;">
									<span style="color:red;"><?php echo $total_type3_2; ?> m&sup2; (R)</span>
								</div>
							</div>
						</div>
					</div>
					<div class="row hidden-print">
			    		<div class="col-md-6" style="padding: 0px 10px;">
			    			<div class="panel panel-default">
								<div class="panel-body" style="text-align: center;">
									<i>Poa: &nbsp;&nbsp; <?php echo $total_type2_1; ?> m&sup2;</i>
								</div>
							</div>
						</div>
						<div class="col-md-6" style="padding: 0px 10px;">
							<div class="panel panel-default">
								<div class="panel-body" style="text-align: center;">
									<i>Mediterrán: &nbsp;&nbsp; <?php echo $total_type2_2; ?> m&sup2;</i>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>

			<?php


			echo '<div class="panel panel-warning">';
			echo '<div class="panel-heading"><h4><span class="glyphicon glyphicon-th-list"></span>&nbsp;&nbsp; Megrendelések</h4></div>';
			echo "<table class='table'>";

			echo "<tr class='title'><td>Sz.</td><td>Dátum</td><td>Időpont</td><td>Vevő</td><td>m&sup2;</td><td colspan='3'>Tipus</td><td>Terület</td><td>Szállitás</td><td>Fizetés</td><td class='note'><span class='glyphicon glyphicon-comment'></span></td><td class='more'>Státusz</td><td></td></tr>";


			$check = 1;
			$customer_page = 1;

			$query = $db->prepare("SELECT * FROM `order` WHERE `date` >= :startdate AND `date` <= :enddate AND `name` = :name AND `status` < 5 ORDER BY `date` DESC");
			$query->bindParam(":startdate", $fromDatum, PDO::PARAM_STR);
			$query->bindParam(":enddate", $toDatum, PDO::PARAM_STR);
			$query->bindParam(":name", $customer_id, PDO::PARAM_STR);
			$query->execute(); 

			if ($query->rowCount() > 0) {

				foreach ($query as $row) {
					
					include('views/_listpoints_foreach.php');	// include rows
				}
			}
			else {
				echo "<tr><td colspan='13'>Nincs</td></tr>";
			}
			

			echo "</table></div>";

			echo '<input type="hidden" id="from" value="'.$fromDatum.'">';
			echo '<input type="hidden" id="to" value="'.$toDatum.'">';

			echo '<div class="row hidden-print"><div class="col-md-12">';
	    	echo '<button type="button" class="btn btn-secondary" style="float: right; margin-right: 15px;" onclick="printPage()">Nyomtatás</button>';
	    	//echo '<button type="button" class="btn btn-secondary" style="float: right; margin-right: 15px;" onclick="allPaid('.$customer_id.')">Minden fizetett</button>';
	  		echo '</div></div>';

		}
		elseif (isset($_GET['statistics'])) {
			if (isset($_GET['year'])) {
				$year_main = $_GET['year'];
			}
			else {
				$year_main = $currentyear;
			}

			$year_main_last = $year_main - 1;
			$year_main_last2 = $year_main - 2;

			if ($year_main == $currentyear) {
				$btn_this = "btn-success";
				$btn_last = "btn-default";
			}
			elseif ($year_main == $lastyear) {
				$btn_this = "btn-default";
				$btn_last = "btn-success";
			}
			else {
				$btn_this = "btn-default";
				$btn_last = "btn-default";
			}

			$startdate = $year_main."-01-01 00:00:00";
			$enddate = $year_main."-12-31 23:59:59";

			echo '<div class="row">';
			echo '<div class="col-md-10"><h4>'.$year_main."</h4></div>";
			echo '<div class="col-md-2">';
			?>
			<button type="button" class="btn <?php echo $btn_this; ?>" style="float:right; margin-left:5px;" onclick="document.location = 'customer.php?statistics=1&year=<?php echo $currentyear; ?>'"><?php echo $currentyear; ?></button>

			<button type="button" class="btn <?php echo $btn_last; ?>" style="float:right;" onclick="document.location = 'customer.php?statistics=1&year=<?php echo $lastyear; ?>'"><?php echo $lastyear; ?></button>

			<?php
			echo "</div></div>";

			echo '<table id="myTable" class="tablesorter"><thead>';
			echo "<tr><th>Sz.</th><th>Vevő</th><th>Típus</th><th>Ország</th><th class='border'>Környék</th>";
			if ($login == 1) {
				echo "<th>Összes</th><th class='border hidden-print'>".$year_main_last."</th><th class='border hidden-print'>".$year_main_last2."</th><th>KR</th><th class='border'>GR</th><th >Poa</th><th class='border'>MED</th><th class='hidden-print'>I</th><th class='border hidden-print'>II</th><th class='hidden-print'>kp.</th><th class='hidden-print'>Átut.</th>";
				//echo "<th class='border'>ABH</th><th>Száll.</th>";
			}
			elseif ($login == 2) {
				echo "<th>Összes</th><th class='border hidden-print'>".$year_main_last."</th><th class='border hidden-print'>".$year_main_last2."</th><th>KR</th><th class='border'>GR</th><th>Poa</th><th class='border'>MED</th>";
				//echo "<th class='border'>ABH</th><th>Száll.</th>";
			}
			echo "<th class='hidden-print'><span class='glyphicon glyphicon-time'></span></th>";
			echo "</tr></thead><tbody>";
			

			$i = 1;
			$query = $db->prepare("SELECT * FROM `customers`");
			$query->execute(); 

			foreach ($query as $row) {
				$id = $row['id'];
				$name = $row['name'];
				$type = $row['type'];
				$country = $row['country'];
				$area = $row['area'];
				$delivery_standard = $row['delivery'];
				$payment_standard = $row['payment'];
				$added = $row['added'];
				$last = $row['2019'];
				$last2 = $row['2018'];
			
				echo '<tr><td>'.$id.'</td><td>';
				echo '<a href="https://turfgrass.site/customer.php?customer_id='.$id.'&fromDatum='.$currentyear.'-01-01&toDatum='.$currentyear.'-12-31&search-customer=Submit">';
				echo $name."</a>"; 
				echo "<button type='button' class='edit_button' data-toggle='modal' data-target='#editCustomerModal".$i."''><span class='glyphicon glyphicon-pencil hidden-print'></span></button></td>";
				echo "</td>";

				if ($type == 1) {
					echo '<td>KE</td>';
				}
				elseif ($type == 2) {
					echo '<td>NK</td>';
				}
				else {
					echo '<td>S</td>';
				}

				$query = $db->prepare("SELECT * FROM countries WHERE `id` = :id");
				$query->bindParam(":id", $country, PDO::PARAM_STR);
				$query->execute();
				$result = $query->fetch(PDO::FETCH_OBJ);
				echo '<td>'.$result->short.'</td>';

				if ($country == 0) {
					$query = $db->prepare("SELECT * FROM areas WHERE `id` = :id");
					$query->bindParam(":id", $area, PDO::PARAM_STR);
					$query->execute();
					$result = $query->fetch(PDO::FETCH_OBJ);
					echo '<td class="border">'.$result->short.'</td>';
				}
				else {
					echo '<td class="border">-</td>';
				}

				$total = 0;
				$total_type1_1 = 0;
				$total_type1_2 = 0;
				$total_type2_1 = 0;
				$total_type2_2 = 0;
				$total_type3_1 = 0;
				$total_type3_2 = 0;
				$payment_1 = 0;
				$payment_2 = 0;
				$delivery_1 = 0;
				$delivery_2 = 0;
				$customers1 = 0;
				$customers2 = 0;
				$customers3 = 0;
				$customers4 = 0;

				$query = $db->prepare("SELECT * FROM `order` WHERE `date` >= :startdate AND `date` <= :enddate AND `name` = :name AND `status` = 4");
				$query->bindParam(":startdate", $startdate, PDO::PARAM_STR);
				$query->bindParam(":enddate", $enddate, PDO::PARAM_STR);
				$query->bindParam(":name", $id, PDO::PARAM_STR);
				$query->execute(); 

				foreach ($query as $row) {
			        $type1 = $row['type1'];
			        $type2 = $row['type2'];
			        $type3 = $row['type3'];
			        $payment = $row['payment'];
			        $delivery = $row['delivery'];
			        $amount = amount_decrypt($row['amount'], $key2);
			        
			        $total += $amount;
			        
			        if ($type1 < 4) {
			        	$total_type1_1 += $amount;
			        }
			        else {
			        	$total_type1_2 += $amount;
			        }
			        if ($type2 == 1) {
			        	$total_type2_1 += $amount;
			        }
			        elseif ($type2 == 2) {
			        	$total_type2_2 += $amount;
			        }
			        if ($type3 == 1) {
			        	$total_type3_1 += $amount;
			        }
			        elseif ($type3 == 2) {
			        	$total_type3_2 += $amount;
			        } 
			        if ($payment == 1) {
			        	$payment_1 += $amount;
			        }
			        elseif ($payment == 2) {
			        	$payment_2 += $amount;
			        }
			        /*
			        if ($delivery == 1) {
			        	$delivery_1 += $amount;
			        }
			        elseif ($delivery == 2) {
			        	$delivery_2 += $amount;
			        }
			        */

			        if ($amount < 500) {
			        	$customers1 ++;
			        }
			        elseif ($amount < 1000) {
			        	$customers2 ++;
			        }
			        elseif ($amount < 2000) {
			        	$customers3 ++;
			        }
			        else {
			        	$customers4 ++;
			        }
			    }

			    /*
			    $startdate_last = $year_main_last."-01-01 00:00:00";
				$enddate_last = $year_main_last."-12-31 23:59:59";
			    $total_last = 0;
			    $query = $db->prepare("SELECT * FROM `order` WHERE `date` >= :startdate AND `date` <= :enddate AND `name` = :name AND `status` = 4");
				$query->bindParam(":startdate", $startdate_last, PDO::PARAM_STR);
				$query->bindParam(":enddate", $enddate_last, PDO::PARAM_STR);
				$query->bindParam(":name", $id, PDO::PARAM_STR);
				$query->execute(); 

				foreach ($query as $row) {
			        $type1 = $row['type1'];
			        $type2 = $row['type2'];
			        $type3 = $row['type3'];
			        $payment = $row['payment'];
			        $delivery = $row['delivery'];
			        $amount = amount_decrypt($row['amount'], $key2);
			        
			        $total_last += $amount;
			    }
			    */

			    if ($year_main == 2020) {
			    	$total_last = $last;
			    	$total_last2 = $last2;
			    }
			    elseif ($year_main == 2019) {
			    	$total_last = $last2;
			    	$total_last2 = "-";
			    }


				if ($login == 1) {
					echo "<td><b>".$total."</b></td>";
					echo "<td class='hidden-print'><i>".$total_last."</i></td>";
					echo "<td class='border hidden-print'><i>".$total_last2."</i></td>";
					echo "<td class='hidden-print'>".$total_type1_1."</td>";
					echo "<td class='border hidden-print'>".$total_type1_2."</td>";
					echo "<td class='hidden-print'>".$total_type2_1."</td>";
					echo "<td class='border hidden-print'>".$total_type2_2."</td>";
					echo "<td class='hidden-print'>".$total_type3_1."</td>";
					echo "<td class='border hidden-print'>".$total_type3_2."</td>";
					echo "<td class='hidden-print'>".$payment_1."</td>";
					echo "<td class='border hidden-print'>".$payment_2."</td>";
					//echo "<td>".$delivery_1."</td>";
					//echo "<td>".$delivery_2."</td>";
				}
				elseif ($login == 2) {
					echo "<td><b>".$total."</b></td>";
					echo "<td class='border hidden-print'><i>".$total_last."</i></td>";
					echo "<td>".$total_type1_1."</td>";
					echo "<td class='border'>".$total_type1_2."</td>";
					echo "<td>".$total_type2_1."</td>";
					echo "<td class='border'>".$total_type2_2."</td>";
				}

				echo "<td class='hidden-print'>".$added."</td>";


				?>
				<!-- MODAL -->
				<!-- Edit stock -->
				<div class="modal fade" id="editCustomerModal<?php echo $i; ?>" tabindex="-1" role="dialog">
				    <div class="modal-dialog" role="document" style="width: 700px;">
				      	<div class="modal-content">
					        <div class="modal-header">
					          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					          <h4 class="modal-title">Szerkesztés</h4>
					        </div>

				        	<div class="modal-body"> 
				        	<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" accept-charset="utf-8">
				        
				        		<div class="row">
				          			<div class="col-md-6">
				            			<div class="form-group">
				              				<label for="name">Név</label>
				              				<input type="text" name="customer_name" value="<?echo $name;?>">
				            			</div>
				          			</div>
				         			<div class="col-md-6">
					    				<b>Szállítás - Standard</b><br>
					    	
								    	<?php
								    	if ($delivery_standard == 1) {
								    		$text1 = " checked";
								    		$text2 = "";
								    	}
								    	elseif ($delivery_standard == 2) {
								    		$text1 = "";
								    		$text2 = " checked";
								    	}

								    	if ($payment_standard == 1) {
								    		$text3 = " checked";
								    		$text4 = "";
								    	}
								    	elseif ($payment_standard == 2) {
								    		$text3 = "";
								    		$text4 = " checked";
								    	}

								    	if ($type == 1) {
								    		$text5 = " checked";
								    		$text6 = "";
								    		$text7 = "";
								    	}
								    	elseif ($type == 2) {
								    		$text5 = "";
								    		$text6 = " checked";
								    		$text7 = "";
								    	}
								    	elseif ($type == 3) {
								    		$text5 = "";
								    		$text6 = "";
								    		$text7 = " checked";
								    	}
								    	?>

								    	<label class="radio-inline">
										  	<input type="radio" name="delivery_standard" value="1"<?echo $text1;?>> ABH
										</label>
										<label class="radio-inline">
										  	<input type="radio" name="delivery_standard" value="2"<?echo $text2;?>> Szállítás
										</label>
										
										<br><br>

										<b>Fizetési mód - Standard</b><br>
										<label class="radio-inline">
										  	<input type="radio" name="payment_standard" value="1"<?echo $text3;?>> Kézpénz
										</label>
										<label class="radio-inline">
										  	<input type="radio" name="payment_standard" value="2"<?echo $text4;?>> Átutalás
										</label>

										<br><br>

										<b>Vevö csapat</b><br>
										<label class="radio-inline">
										  	<input type="radio" name="type" value="1"<?echo $text5;?>> Kertépítő
										</label>
										<label class="radio-inline">
										  	<input type="radio" name="type" value="2"<?echo $text6;?>> Nagykereskedő
										</label>
										<label class="radio-inline">
										  	<input type="radio" name="type" value="3"<?echo $text7;?>> Sport
										</label>

										<br><br>

										<div class="form-group">
							          		<label for="country">Ország</label>
									        <?php

								            echo '<select class="form-control" id="country" name="country">';
								            echo '<option value="'.$country.'" selected>';
								            
								            $query = $db->prepare("SELECT * FROM countries WHERE `id` = :id");
											$query->bindParam(":id", $country, PDO::PARAM_STR);
											$query->execute();
											$result = $query->fetch(PDO::FETCH_OBJ);

								            echo $result->name;
								            echo "</option>";
								 
								            $j = 0;
								            $query = $db->prepare("SELECT * FROM countries");
								            $query->execute();
								            while($row = $query->fetch()) {
								                if ($j != $country) {
								                	echo "<option value='".$row['id']."'>".$row['name']."</option>";
								                }  
								                $j++;     
								            }

								            echo '</select>';
									        ?>
						        		</div>

						        		<br><br>

										<div class="form-group">
							          		<label for="country">Környék</label>
									        <?php

								            echo '<select class="form-control" name="area">';
								            echo '<option value="'.$area.'" selected>';
								            
								            $query = $db->prepare("SELECT * FROM areas WHERE `id` = :id");
											$query->bindParam(":id", $area, PDO::PARAM_STR);
											$query->execute();
											$result = $query->fetch(PDO::FETCH_OBJ);

								            echo $result->name;
								            echo "</option>";
								 
								            $j = 0;
								            $query = $db->prepare("SELECT * FROM areas");
								            $query->execute();
								            while($row = $query->fetch()) {
								                if ($j != $area) {
								                	echo "<option value='".$row['id']."'>".$row['name']."</option>";
								                }  
								                $j++;     
								            }

								            echo '</select>';
									        ?>
						        		</div>
				        			</div>
			        	   		</div>
			        	   	</div>

				        	<input type="hidden" name="customer_id" value="<?echo $id;?>"> 

					        <div class="modal-footer">
					          	<button type="submit" class="btn btn-primary center-block" name="editCustomerForm" value="Submit">Küldés</button>
					        </div>
					        </form>
					    </div><!-- /.modal-content -->
					</div><!-- /.modal-dialog -->
				</div><!-- /.modal -->
				<?php
				
				echo '</tr>'; 
			  	$i++;
			}
			
			echo '</tbody></table>'; 

			echo '<div class="row hidden-print"><div class="col-md-12">';
	    	echo '<button type="button" class="btn btn-secondary" style="float: right; margin-right: 15px;" onclick="printPage()">Nyomtatás</button>';
	  		echo '</div></div>';

		}
		else {
			?>
			<button type="button" class="btn btn-secondary" style="" onclick="document.location = 'customer.php?statistics=1'">Mutassa a statisztikát</button>
		<?php
		}
		?>

	</div>
<?php
}
?>



