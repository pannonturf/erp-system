<?php 
//////////////////////
// Sales statistics //
//////////////////////

$statistics = 1;	// graphs are loaded in header
include('views/_header'.$header.'.php');
include('tools/functions.php');

require_once('config/config.php');
$db = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.'', DB_USER, DB_PASS);
$db -> exec("set names utf8");

//Show buttons right for years    
if (isset($_GET['year'])) {
	$year_main = $_GET['year'];
}
else {
	$year_main = $currentyear;
}

$year_main_last = $year_main - 1;

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

//Show buttons right for types         
if (isset($_GET['type'])) {
	$type_main = $_GET['type'];
}
else {
	$type_main = 1;
}

if ($type_main == 1) {
	$btn_type1 = "btn-success";
	$btn_type2 = "btn-default";
	$btn_type3 = "btn-default";
	$title1 = "KR";
	$title2 = "GR";
}
elseif ($type_main == 2) {
	$btn_type1 = "btn-default";
	$btn_type2 = "btn-success";
	$btn_type3 = "btn-default";
	$title1 = "Poa";
	$title2 = "MED";
}
elseif ($type_main == 3) {
	$btn_type1 = "btn-default";
	$btn_type2 = "btn-default";
	$btn_type3 = "btn-success";
	$title1 = "I";
	$title2 = "II";
}
?>

<div class="inputform">
	<div class="row">
		<div class="col-md-8">
			<button type="button" class="btn <?php echo $btn_type3; ?>" style="float:right; margin-left:5px;" onclick="document.location = 'statistics.php?year=<?php echo $year_main; ?>&type=3'">Type 3</button>
			<button type="button" class="btn <?php echo $btn_type2; ?>" style="float:right; margin-left:5px;" onclick="document.location = 'statistics.php?year=<?php echo $year_main; ?>&type=2'">Type 2</button>
			<button type="button" class="btn <?php echo $btn_type1; ?>" style="float:right;" onclick="document.location = 'statistics.php?year=<?php echo $year_main; ?>&type=1'">Type 1</button>
		</div>
		<div class="col-md-4">
			<button type="button" class="btn <?php echo $btn_this; ?>" style="float:right; margin-left:5px;" onclick="document.location = 'statistics.php?year=<?php echo $currentyear; ?>&type=<?php echo $type_main; ?>'"><?php echo $currentyear; ?></button>
			<button type="button" class="btn <?php echo $btn_last; ?>" style="float:right;" onclick="document.location = 'statistics.php?year=<?php echo $lastyear; ?>&type=<?php echo $type_main; ?>'"><?php echo $lastyear; ?></button>
		</div>
	</div>

	<div class="row">
	    <div class="col-md-8">
	    	<table class='table'>
			<tr class='title'><td class='border'></td><td colspan='3' class="border" style="text-align: center;">Összes</td><td colspan='3' class='border' style="text-align: center;"><?php echo $title1; ?></td><td colspan='3' style="text-align: center;"><?php echo $title2; ?></td></tr>

			<?php

			////////////////
			/// Total 
			echo "<tr><td class='border'></td><td>".$year_main."</td><td>".$year_main_last."</td><td class='border'>%</td><td>".$year_main."</td><td>".$year_main_last."</td><td class='border'>%</td><td>".$year_main."</td><td>".$year_main_last."</td><td>%</td></tr>";

			$startdate = $year_main."-01-01 00:00:00";
			$enddate = $year_main."-12-31 23:59:59";

			$total_this = 0;
			$total_this_type1_1 = 0;
			$total_this_type1_2 = 0;
			$total_this_type2_1 = 0;
			$total_this_type2_2 = 0;
			$total_this_type3_1 = 0;
			$total_this_type3_2 = 0;
			$orders_total = 0;
			$orders_150 = 0;
			$orders_400 = 0;
			$orders_1000 = 0;
			$orders_above = 0;
			$total_orders_150 = 0;
			$total_orders_400 = 0;
			$total_orders_1000 = 0;
			$total_orders_above = 0;

		    $query = $db->prepare("SELECT * FROM `order` WHERE `date` >= :startdate AND `date` < :enddate AND `status` = 4 ORDER BY `date` ASC");
		    $query->bindParam(":startdate", $startdate, PDO::PARAM_STR);
		    $query->bindParam(":enddate", $enddate, PDO::PARAM_STR);
		    $query->execute(); 
		    foreach ($query as $row) {
		        $type1 = $row['type1'];
		        $type2 = $row['type2'];
		        $type3 = $row['type3'];
		        $status = $row['status'];
		        $amount = amount_decrypt($row['amount'], $key2);
		        
		        if ($status < 5) {
			        $total_this += $amount;
			        
			        if ($type1 < 4) {
			        	$total_this_type1_1 += $amount;
			        }
			        elseif ($type1 > 3) {
			        	$total_this_type1_2 += $amount;
			        }

			        if ($type2 == 1) {
			        	$total_this_type2_1 += $amount;
			        }
			        elseif ($type2 == 2) {
			        	$total_this_type2_2 += $amount;
			        }

			        if ($type3 == 1) {
			        	$total_this_type3_1 += $amount;
			        }
			        elseif ($type3 == 2) {
			        	$total_this_type3_2 += $amount;
			        } 
			    }

			    $orders_total ++;
			    if ($amount < 150) {
			    	$orders_150 ++;
			    	$total_orders_150 += $amount;
			    }
			    elseif ($amount < 400) {
			    	$orders_400 ++;
			    	$total_orders_400 += $amount;
			    }
			    elseif ($amount < 1000) {
			    	$orders_1000 ++;
			    	$total_orders_1000 += $amount;
			    }
			    else {
			    	$orders_above ++;
			    	$total_orders_above += $amount;
			    }

		    }

			$total_this_display = number_format($total_this, 0, ',', ' ');
			$total_this_type1_1_display = number_format($total_this_type1_1, 0, ',', ' ');
			$total_this_type1_2_display = number_format($total_this_type1_2, 0, ',', ' ');
			$total_this_type2_1_display = number_format($total_this_type2_1, 0, ',', ' ');
			$total_this_type2_2_display = number_format($total_this_type2_2, 0, ',', ' ');
			$total_this_type3_1_display = number_format(getType3($total_this_type3_1, $total_this_type3_2, 1), 0, ',', ' ');
			$total_this_type3_2_display = number_format(getType3($total_this_type3_1, $total_this_type3_2, 2), 0, ',', ' ');
			$orders_total_disp = number_format($orders_total, 0, ',', ' ');
			$orders_150_disp = number_format($orders_150, 0, ',', ' ');
			$orders_400_disp = number_format($orders_400, 0, ',', ' ');
			$orders_1000_disp = number_format($orders_1000, 0, ',', ' ');
			$orders_above_disp = number_format($orders_above, 0, ',', ' ');
			$total_orders_150_disp = number_format($total_orders_150, 0, ',', ' ');
			$total_orders_400_disp = number_format($total_orders_400, 0, ',', ' ');
			$total_orders_1000_disp = number_format($total_orders_1000, 0, ',', ' ');
			$total_orders_above_disp = number_format($total_orders_above, 0, ',', ' ');


			$today2 = substr(date("Y-m-d"), 5, 5);
			$title_html1 = "<tr class='title'><td class='border'>Összes</td><td>-</td>";	
			$title_html2 = "<tr class='title'><td class='border'>".$today2."</td><td>".$total_this_display."</td>";	

			//get total amounts of the last year     
			$total_last = 0;
			$total_last_type1_1 = 0;
			$total_last_type1_2 = 0;
			$total_last_type2_1 = 0;
			$total_last_type2_2 = 0;
			$total_last_type3_1 = 0;
			$total_last_type3_2 = 0;
			$orders_total_last = 0;
			$orders_150_last = 0;
			$orders_400_last = 0;
			$orders_1000_last = 0;
			$orders_above_last = 0;
			$total_orders_150_last = 0;
			$total_orders_400_last = 0;
			$total_orders_1000_last = 0;
			$total_orders_above_last = 0;

			$year_main_last = $year_main - 1;

			$startdate = $year_main_last."-01-01 00:00:00";
			$enddate = $year_main_last."-12-31 23:59:59";

			$query = $db->prepare("SELECT * FROM `order` WHERE `date` >= :startdate AND `date` < :enddate AND `status` = 4 ORDER BY `time` ASC");
			$query->bindParam(":startdate", $startdate, PDO::PARAM_STR);
			$query->bindParam(":enddate", $enddate, PDO::PARAM_STR);
			$query->execute(); 

			if ($query->rowCount() > 0) {
				foreach ($query as $row) {
			        $type1 = $row['type1'];
			        $type2 = $row['type2'];
			        $type3 = $row['type3'];
			        $amount = amount_decrypt($row['amount'], $key2);
			        $total_last += $amount;
			        
			        if ($type1 < 4) {
			        	$total_last_type1_1 += $amount;
			        }
			        elseif ($type1 > 3) {
			        	$total_last_type1_2 += $amount;
			        }

			        if ($type2 == 1) {
			        	$total_last_type2_1 += $amount;
			        }
			        elseif ($type2 == 2) {
			        	$total_last_type2_2 += $amount;
			        }

			        if ($type3 == 1) {
			        	$total_last_type3_1 += $amount;
			        }
			        elseif ($type3 == 2) {
			        	$total_last_type3_2 += $amount;
			        } 

			        $orders_total_last ++;
				    if ($amount < 150) {
				    	$orders_150_last ++;
				    	$total_orders_150_last += $amount;
				    }
				    elseif ($amount < 400) {
				    	$orders_400_last ++;
				    	$total_orders_400_last += $amount;
				    }
				    elseif ($amount < 1000) {
				    	$orders_1000_last ++;
				    	$total_orders_1000_last += $amount;
				    }
				    else {
				    	$orders_above_last ++;
				    	$total_orders_above_last += $amount;
				    }
			    }

				$total_last_display = number_format($total_last, 0, ',', ' ');
				$total_last_type1_1_display = number_format($total_last_type1_1, 0, ',', ' ');
				$total_last_type1_2_display = number_format($total_last_type1_2, 0, ',', ' ');
				$total_last_type2_1_display = number_format($total_last_type2_1, 0, ',', ' ');
				$total_last_type2_2_display = number_format($total_last_type2_2, 0, ',', ' ');
				$total_last_type3_1_display = number_format(getType3($total_last_type3_1, $total_last_type3_2, 1), 0, ',', ' ');
				$total_last_type3_2_display = number_format(getType3($total_last_type3_1, $total_last_type3_2, 2), 0, ',', ' ');
				$orders_total_last_disp = number_format($orders_total_last, 0, ',', ' ');
				$orders_150_last_disp = number_format($orders_150_last, 0, ',', ' ');
				$orders_400_last_disp = number_format($orders_400_last, 0, ',', ' ');
				$orders_1000_last_disp = number_format($orders_1000_last, 0, ',', ' ');
				$orders_above_last_disp = number_format($orders_above_last, 0, ',', ' ');
				$total_orders_last_150_disp = number_format($total_orders_150_last, 0, ',', ' ');
				$total_orders_400_last_disp = number_format($total_orders_400_last, 0, ',', ' ');
				$total_orders_1000_last_disp = number_format($total_orders_1000_last, 0, ',', ' ');
				$total_orders_above_last_disp = number_format($total_orders_above_last, 0, ',', ' ');	

				$title_html1 .= "<td>".$total_last_display."</td>";

				/*
				$percentage = number_format((($total_this / $total_last) - 1) * 100, 0, ',', ' ');
				if ($percentage < 0) {
					echo "<td class='border' style='color: red'>".$percentage." %</td>";
				}
				else {
					echo "<td class='border' style='color: green'>".$percentage." %</td>";
				}
				*/
				$title_html1 .= "<td class='border'>-</td>";
			}
			else {
				$title_html1 .= "<td>-</td><td class='border'>-</td>";
			}


			//get total cumulative amounts of the same day last year     
			$total_last2 = 0;
			$total_last2_type1_1 = 0;
			$total_last2_type1_2 = 0;
			$total_last2_type2_1 = 0;
			$total_last2_type2_2 = 0;
			$total_last2_type3_1 = 0;
			$total_last2_type3_2 = 0;
			$orders_total2_last = 0;

			$startdate2 = $year_main_last."-01-01 00:00:00";
			$currentmonth = date('m');
			$currentday = date('d');
			$enddate2 = $year_main_last."-".$currentmonth."-".$currentday." 23:59:00";

			$query2 = $db->prepare("SELECT * FROM `order` WHERE `date` >= :startdate AND `date` < :enddate AND `status` = 4 ORDER BY `time` ASC");
			$query2->bindParam(":startdate", $startdate2, PDO::PARAM_STR);
			$query2->bindParam(":enddate", $enddate2, PDO::PARAM_STR);
			$query2->execute(); 

			if ($query2->rowCount() > 0) {
				foreach ($query2 as $row) {
			        $type1 = $row['type1'];
			        $type2 = $row['type2'];
			        $type3 = $row['type3'];
			        $amount = amount_decrypt($row['amount'], $key2);
			        $total_last2 += $amount;
			        
			        if ($type1 < 4) {
			        	$total_last2_type1_1 += $amount;
			        }
			        elseif ($type1 > 3) {
			        	$total_last2_type1_2 += $amount;
			        }

			        if ($type2 == 1) {
			        	$total_last2_type2_1 += $amount;
			        }
			        elseif ($type2 == 2) {
			        	$total_last2_type2_2 += $amount;
			        }

			        if ($type3 == 1) {
			        	$total_last2_type3_1 += $amount;
			        }
			        elseif ($type3 == 2) {
			        	$total_last2_type3_2 += $amount;
			        } 

			        $orders_total_last2 ++;
			    }

				$total_last2_display = number_format($total_last2, 0, ',', ' ');
				$total_last2_type1_1_display = number_format($total_last2_type1_1, 0, ',', ' ');
				$total_last2_type1_2_display = number_format($total_last2_type1_2, 0, ',', ' ');
				$total_last2_type2_1_display = number_format($total_last2_type2_1, 0, ',', ' ');
				$total_last2_type2_2_display = number_format($total_last2_type2_2, 0, ',', ' ');
				$total_last2_type3_1_display = number_format(getType3($total_last2_type3_1, $total_last2_type3_2, 1), 0, ',', ' ');
				$total_last2_type3_2_display = number_format(getType3($total_last2_type3_1, $total_last2_type3_2, 2), 0, ',', ' ');
				$orders_total_last2_disp = number_format($orders_total_last2, 0, ',', ' ');

				$title_html2 .= "<td>".$total_last2_display."</td>";

				
				$percentage = number_format((($total_this / $total_last2) - 1) * 100, 0, ',', ' ');
				if ($percentage < 0) {
					$title_html2 .= "<td class='border' style='color: red'>".$percentage." %</td>";
				}
				else {
					$title_html2 .= "<td class='border' style='color: green'>+".$percentage." %</td>";
				}
				
			}
			else {
				$title_html2 .= "<td>-</td><td class='border'>-</td>";
			}


			////////////////
			/// I
			if ($type_main == 1) {
				$title_html1 .= "<td>-</td>";
			}
			elseif ($type_main == 2) {
				$title_html1 .= "<td>-</td>";
			}
			elseif ($type_main == 3) {
				$title_html1 .= "<td>-</td>";
			}

			if ($query->rowCount() > 0) {
				if ($type_main == 1) {
					$title_html1 .= "<td>".$total_last_type1_1_display."</td>";
					$percentage = number_format((($total_this_type1_1 / $total_last_type1_1) - 1) * 100, 0, ',', ' ');
				}
				elseif ($type_main == 2) {
					$title_html1 .= "<td>".$total_last_type2_1_display."</td>";
					$percentage = number_format((($total_this_type2_1 / $total_last_type2_1) - 1) * 100, 0, ',', ' ');
				}
				elseif ($type_main == 3) {
					$title_html1 .= "<td>".$total_last_type3_1_display."</td>";
					$percentage = number_format((($total_this_type3_1 / $total_last_type3_1) - 1) * 100, 0, ',', ' ');
				}

				/*
				if ($percentage < 0) {
					echo "<td class='border' style='color: red'>".$percentage." %</td>";
				}
				else {
					echo "<td class='border' style='color: green'>".$percentage." %</td>";
				}
				*/
				$title_html1 .=  "<td class='border'>-</td>";
			}
			else {
				$title_html1 .= "<td>-</td><td class='border'>-</td>";
			}

			////////////////
			/// I 2
			if ($type_main == 1) {
				$title_html2 .= "<td>".$total_this_type1_1_display."</td>";
			}
			elseif ($type_main == 2) {
				$title_html2 .= "<td>".$total_this_type2_1_display."</td>";
			}
			elseif ($type_main == 3) {
				$title_html2 .= "<td>".$total_this_type3_1_display."</td>";
			}

			if ($query2->rowCount() > 0) {
				if ($type_main == 1) {
					$title_html2 .= "<td>".$total_last2_type1_1_display."</td>";
					$percentage = number_format((($total_this_type1_1 / $total_last2_type1_1) - 1) * 100, 0, ',', ' ');
				}
				elseif ($type_main == 2) {
					$title_html2 .= "<td>".$total_last2_type2_1_display."</td>";
					$percentage = number_format((($total_this_type2_1 / $total_last2_type2_1) - 1) * 100, 0, ',', ' ');
				}
				elseif ($type_main == 3) {
					$title_html2 .= "<td>".$total_last2_type3_1_display."</td>";
					$percentage = number_format((($total_this_type3_1 / $total_last2_type3_1) - 1) * 100, 0, ',', ' ');
				}

				if ($percentage < 0) {
					$title_html2 .= "<td class='border' style='color: red'>".$percentage." %</td>";
				}
				else {
					$title_html2 .= "<td class='border' style='color: green'>+".$percentage." %</td>";
				}
			}
			else {
				$title_html2 .= "<td>-</td><td class='border'>-</td>";
			}

			////////////////
			/// II
			if ($type_main == 1) {
				$title_html1 .= "<td>-</td>";
			}
			elseif ($type_main == 2) {
				$title_html1 .= "<td>-</td>";
			}
			elseif ($type_main == 3) {
				$title_html1 .= "<td>-</td>";
			}

			if ($query->rowCount() > 0) {
				if ($type_main == 1) {
					$title_html1 .= "<td>".$total_last_type1_2_display."</td>";
					$percentage = number_format((($total_this_type1_2 / $total_last_type1_2) - 1) * 100, 0, ',', ' ');
				}
				elseif ($type_main == 2) {
					$title_html1 .= "<td>".$total_last_type2_2_display."</td>";
					$percentage = number_format((($total_this_type2_2 / $total_last_type2_2) - 1) * 100, 0, ',', ' ');
				}
				elseif ($type_main == 3) {
					$title_html1 .= "<td>".$total_last_type3_2_display."</td>";
					$percentage = number_format((($total_this_type3_2 / $total_last_type3_2) - 1) * 100, 0, ',', ' ');
				}

				/*
				if ($percentage < 0) {
					echo "<td style='color: red'>".$percentage." %</td></tr>";
				}
				else {
					echo "<td style='color: green'>".$percentage." %</td></tr>";
				}
				*/
				$title_html1 .= "<td>-</td>";
			}
			else {
				$title_html1 .= "<td>-</td><td>-</td></tr>";
			}


			////////////////
			/// II 2
			if ($type_main == 1) {
				$title_html2 .= "<td>".$total_this_type1_2_display."</td>";
			}
			elseif ($type_main == 2) {
				$title_html2 .= "<td>".$total_this_type2_2_display."</td>";
			}
			elseif ($type_main == 3) {
				$title_html2 .= "<td>".$total_this_type3_2_display."</td>";
			}

			if ($query2->rowCount() > 0) {
				if ($type_main == 1) {
					$title_html2 .= "<td>".$total_last2_type1_2_display."</td>";
					$percentage = number_format((($total_this_type1_2 / $total_last2_type1_2) - 1) * 100, 0, ',', ' ');
					$last2 = $total_last2_type1_2;
				}
				elseif ($type_main == 2) {
					$title_html2 .= "<td>".$total_last2_type2_2_display."</td>";
					$percentage = number_format((($total_this_type2_2 / $total_last2_type2_2) - 1) * 100, 0, ',', ' ');
					$last2 = $total_last2_type2_2;
				}
				elseif ($type_main == 3) {
					$title_html2 .= "<td>".$total_last2_type3_2_display."</td>";
					$percentage = number_format((($total_this_type3_2 / $total_last2_type3_2) - 1) * 100, 0, ',', ' ');
					$last2 = $total_last2_type3_2;
				}

				
				if ($percentage < 0) {
					if ($last2 == 0) {
						$title_html2 .= "<td>-</td></tr>";
					}
					else {
						$title_html2 .= "<td style='color: red'>".$percentage." %</td></tr>";
					}
				}
				else {
					$title_html2 .= "<td style='color: green'>+".$percentage." %</td></tr>";
				}
			}
			else {
				$title_html2 .= "<td>-</td><td>-</td></tr>";
			}

			echo $title_html1;
			echo $title_html2;

			
			////////////////
			/// Total per month
	      	for ($j=0; $j < 12; $j++) { 
	      		$monthName = $months_long[$j];
	      		echo "<tr><td class='border'>".$monthName."</td>";

	      		$month = $j + 1;
	      		if ($month < 10) {
	      			$month = "0".$month;
	      		}

	      		//get total amounts of the month     
				$total_month_this = 0;
				$total_month_this_type1_1 = 0;
				$total_month_this_type1_2 = 0;
				$total_month_this_type2_1 = 0;
				$total_month_this_type2_2 = 0;
				$total_month_this_type3_1 = 0;
				$total_month_this_type3_2 = 0;
				$startdate = $year_main."-".$month."-01 00:00:00";
				$enddate = $year_main."-".($month + 1)."-01 00:00:00";

				$query = $db->prepare("SELECT * FROM `order` WHERE `date` >= :startdate AND `date` < :enddate AND `status` = 4 ORDER BY `time` ASC");
				$query->bindParam(":startdate", $startdate, PDO::PARAM_STR);
				$query->bindParam(":enddate", $enddate, PDO::PARAM_STR);
				$query->execute(); 
				foreach ($query as $row) {
					$type1 = $row['type1'];
					$type2 = $row['type2'];
		        	$type3 = $row['type3'];
					$amount = amount_decrypt($row['amount'], $key2);
					$total_month_this += $amount;

					if ($type1 < 4) {
		        		$total_month_this_type1_1 += $amount;
			        }
			        elseif ($type1 > 3) {
			        	$total_month_this_type1_2 += $amount;
			        }

			        if ($type2 == 1) {
		        		$total_month_this_type2_1 += $amount;
			        }
			        elseif ($type2 == 2) {
			        	$total_month_this_type2_2 += $amount;
			        }

					if ($type3 == 1) {
		        		$total_month_this_type3_1 += $amount;
			        }
			        elseif ($type3 == 2) {
			        	$total_month_this_type3_2 += $amount;
			        } 
				}
				$total_month_this_display = number_format($total_month_this, 0, ',', ' ');
				$total_month_this_type1_1_display = number_format($total_month_this_type1_1, 0, ',', ' ');
				$total_month_this_type1_2_display = number_format($total_month_this_type1_2, 0, ',', ' ');
				$total_month_this_type2_1_display = number_format($total_month_this_type2_1, 0, ',', ' ');
				$total_month_this_type2_2_display = number_format($total_month_this_type2_2, 0, ',', ' ');
				$total_month_this_type3_1_display = number_format(getType3($total_month_this_type3_1, $total_month_this_type3_2, 1), 0, ',', ' ');
				$total_month_this_type3_2_display = number_format(getType3($total_month_this_type3_1, $total_month_this_type3_2, 2), 0, ',', ' ');

				echo "<td>".$total_month_this_display."</td>";

				//get total amounts of the month last year    
				$total_month_last = 0;
				$total_month_last_type1_1 = 0;
				$total_month_last_type1_2 = 0;
				$total_month_last_type2_1 = 0;
				$total_month_last_type2_2 = 0;
				$total_month_last_type3_1 = 0;
				$total_month_last_type3_2 = 0;
				$startdate = $year_main_last."-".$month."-01 00:00:00";
				$enddate = $year_main_last."-".($month + 1)."-01 00:00:00";

				$query = $db->prepare("SELECT * FROM `order` WHERE `date` >= :startdate AND `date` < :enddate AND `status` = 4 ORDER BY `time` ASC");
				$query->bindParam(":startdate", $startdate, PDO::PARAM_STR);
				$query->bindParam(":enddate", $enddate, PDO::PARAM_STR);
				$query->execute(); 

				if ($query->rowCount() > 0) {
					foreach ($query as $row) {
						$type1 = $row['type1'];
						$type2 = $row['type2'];
			        	$type3 = $row['type3'];
						$amount = amount_decrypt($row['amount'], $key2);
						$total_month_last += $amount;

						if ($type1 < 4) {
			        		$total_month_last_type1_1 += $amount;
				        }
				        elseif ($type1 > 3) {
				        	$total_month_last_type1_2 += $amount;
				        }

				        if ($type2 == 1) {
			        		$total_month_last_type2_1 += $amount;
				        }
				        elseif ($type2 == 2) {
				        	$total_month_last_type2_2 += $amount;
				        }

						if ($type3 == 1) {
			        		$total_month_last_type3_1 += $amount;
				        }
				        elseif ($type3 == 2) {
				        	$total_month_last_type3_2 += $amount;
				        }
					}
					$total_month_last_display = number_format($total_month_last, 0, ',', ' ');
					$total_month_last_type1_1_display = number_format($total_month_last_type1_1, 0, ',', ' ');
					$total_month_last_type1_2_display = number_format($total_month_last_type1_2, 0, ',', ' ');
					$total_month_last_type2_1_display = number_format($total_month_last_type2_1, 0, ',', ' ');
					$total_month_last_type2_2_display = number_format($total_month_last_type2_2, 0, ',', ' ');
					$total_month_last_type3_1_display = number_format(getType3($total_month_last_type3_1, $total_month_last_type3_2, 1), 0, ',', ' ');
					$total_month_last_type3_2_display = number_format(getType3($total_month_last_type3_1, $total_month_last_type3_2, 2), 0, ',', ' ');

					echo "<td>".$total_month_last_display."</td>";

		      		$percentage = number_format((($total_month_this / $total_month_last) - 1) * 100, 0, ',', ' ');

					if ($total_month_this > 0) {
						if ($percentage < 0) {
							echo "<td class='border' style='color: red'>".$percentage." %</td>";
						}
						else {
							echo "<td class='border' style='color: green'>+".$percentage." %</td>";
						}	
					}
					else {
						echo "<td class='border'>-</td>";
					}
		      	}
		      	else {
					echo "<td>-</td><td class='border'>-</td>";
				}

				////////////////
				/// I per month
				if ($type_main == 1) {
					echo "<td>".$total_month_this_type1_1_display."</td>";
				}
				elseif ($type_main == 2) {
					echo "<td>".$total_month_this_type2_1_display."</td>";
				}
				elseif ($type_main == 3) {
					echo "<td>".$total_month_this_type3_1_display."</td>";
				}

				if ($query->rowCount() > 0) {
					if ($type_main == 1) {
						echo "<td class='type3'>".$total_month_last_type1_1_display."</td>";
						$percentage = number_format((($total_month_this_type1_1 / $total_month_last_type1_1) - 1) * 100, 0, ',', ' ');
						$selected = $total_month_this_type1_1;
						$selected2 = $total_month_last_type1_1;
					}
					elseif ($type_main == 2) {
						echo "<td class='type3'>".$total_month_last_type2_1_display."</td>";
						$percentage = number_format((($total_month_this_type2_1 / $total_month_last_type2_1) - 1) * 100, 0, ',', ' ');
						$selected = $total_month_this_type2_1;
						$selected2 = $total_month_last_type2_1;
					}
					elseif ($type_main == 3) {
						echo "<td class='type3'>".$total_month_last_type3_1_display."</td>";
						$percentage = number_format((($total_month_this_type3_1 / $total_month_last_type3_1) - 1) * 100, 0, ',', ' ');
						$selected = $total_month_this_type3_1;
						$selected2 = $total_month_last_type3_1;
					}
					
					if ($selected > 0 AND $selected2 > 0) {	
						if ($percentage < 0) {
							echo "<td class='border type3' style='color: red'>".$percentage." %</td>";
						}
						else {
							echo "<td class='border type3' style='color: green'>+".$percentage." %</td>";
						}
					}
					else {
						echo "<td class='border'>-</td>";
					}
				}
				else {
					echo "<td>-</td><td class='border'>-</td>";
				}

				////////////////
				/// II per month
				if ($type_main == 1) {
					echo "<td>".$total_month_this_type1_2_display."</td>";
				}
				elseif ($type_main == 2) {
					echo "<td>".$total_month_this_type2_2_display."</td>";
				}
				elseif ($type_main == 3) {
					echo "<td>".$total_month_this_type3_2_display."</td>";
				}
				
				if ($query->rowCount() > 0) {
					if ($type_main == 1) {
						echo "<td class='type3'>".$total_month_last_type1_2_display."</td>";
						$percentage = number_format((($total_month_this_type1_2 / $total_month_last_type1_2) - 1) * 100, 0, ',', ' ');
						$selected = $total_month_this_type1_2;
						$selected2 = $total_month_last_type1_2;
					}
					elseif ($type_main == 2) {
						echo "<td class='type3'>".$total_month_last_type2_2_display."</td>";
						$percentage = number_format((($total_month_this_type2_2 / $total_month_last_type2_2) - 1) * 100, 0, ',', ' ');
						$selected = $total_month_this_type2_2;
						$selected2 = $total_month_last_type2_2;
					}
					elseif ($type_main == 3) {
						echo "<td class='type3'>".$total_month_last_type3_2_display."</td>";
						$percentage = number_format((($total_month_this_type3_2 / $total_month_last_type3_2) - 1) * 100, 0, ',', ' ');
						$selected = $total_month_this_type3_2;
						$selected2 = $total_month_last_type3_2;
					}

					if ($selected > 0 AND $selected2 > 0) {
						if ($percentage < 0) {
							echo "<td style='color: red'>".$percentage." %</td></tr>";
						}
						else {
							echo "<td style='color: green'>+".$percentage." %</td></tr>";
						}
					}
					else {
						echo "<td>-</td>";
					}
				}
				else {
					echo "<td>-</td><td>-</td></tr>";
				}
			}

			echo "</table><br><br><br>";

			?>
			<div id="barchart"></div>
			<br><br><br>
			<div id="linechart"></div>

			<div class="row">
	    		<div class="col-md-8">
	    			<h4 style="margin-top:80px;">Bestellungen - <?echo $year_main; ?></h4>
	    			<table class='table'>
						<tr class='title'><td class='border'></td><td>&lt; 150</td><td>150 - 399</td><td>400 - 999</td><td class='border'>&gt; 1000</td><td>TOTAL</td></tr>
						<?php
						echo "<tr><td class='border'>Menge</td>";
						echo "<td>".$orders_150_disp."</td>";
						echo "<td>".$orders_400_disp."</td>";
						echo "<td>".$orders_1000_disp."</td>";
						echo "<td class='border'>".$orders_above_disp."</td>";
						echo "<td><b>".$orders_total_disp."</b></td></tr>";

						echo "<tr><td class='border'>m&sup2;</td>";
						echo "<td>".$total_orders_150_disp."</td>";
						echo "<td>".$total_orders_400_disp."</td>";
						echo "<td>".$total_orders_1000_disp."</td>";
						echo "<td class='border'>".$total_orders_above_disp."</td>";
						echo "<td><b>".$total_this_display."</b></td></tr>";
						?>
					</table>
				</div>
			</div>
		</div>
		<div class="col-md-4">
			<h4 style="text-align: center;"><?echo $year_main; ?></h4>
			<div id="piechart1" style="float: right;"></div>
			<br>
			<div id="piechart2" style="float: right;"></div>
			<br>
			<div id="piechart3" style="float: right;"></div>
			<br>
			<div id="piechart4" style="float: right;"></div>
			<br>
			<div id="piechart5" style="float: right;"></div>
		</div>
		</div>
	</div>



</div>




