<?php 
//////////////////////////////////////
// Statistics about customer groups //
//////////////////////////////////////

//$statistics = 2;	// graphs are deactivated
include('views/_header'.$header.'.php');
include('tools/functions.php');

require_once('config/config.php');
$db = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.'', DB_USER, DB_PASS);
$db -> exec("set names utf8");
?>

<div class="inputform">
	<h3 style="margin-top:10px;">Vevő statisztika</h3> <br> 

<?php
	// display right year
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

	$startdate = $year_main."-01-01 00:00:00";
	$enddate = $year_main."-12-31 23:59:59";

	$customers_total = 0;
	$customers1 = 0;
	$customers2 = 0;
	$customers3 = 0;
	$customers4 = 0;
	$customers = array();
	$hungary = 0;
	$austria = 0;
	$others = 0;
	$areas = array();
	$count = array();
	$gardeners_hu = 0;
	$wholesalers_hu = 0;
	$sport_hu = 0;
	$gardeners_other = 0;
	$wholesalers_other = 0;
	$sport_other = 0;

	$query = $db->prepare("SELECT * FROM areas");
	$query->execute();
	$number_fields = $query->rowCount();
	foreach ($query as $row) {
		$area_id = $row['id'];
		$areas[$area_id][1] = 0;
		$areas[$area_id][2] = 0;
	}

	for ($i=1; $i < 9; $i++) { 
		$count[$i] = 0;
		$customers[$i] = 0;
	}

	for ($i=1; $i < 5; $i++) { 
		$customers[$i] = 0;
	}

	// Customer statistics this year
	$query = $db->prepare("SELECT * FROM `customers` WHERE `status` = 1");
	$query->execute(); 

	foreach ($query as $row) {
		$id = $row['id'];
		$type = $row['type'];
		$country = $row['country'];
		$area = $row['area'];
		$total = 0;
		$kr = 0;

		$query = $db->prepare("SELECT * FROM `order` WHERE `date` >= :startdate AND `date` <= :enddate AND `name` = :name AND `status` = 4");
		$query->bindParam(":startdate", $startdate, PDO::PARAM_STR);
		$query->bindParam(":enddate", $enddate, PDO::PARAM_STR);
		$query->bindParam(":name", $id, PDO::PARAM_STR);
		$query->execute(); 

		foreach ($query as $row) {
	        $type1 = $row['type1'];
	        $amount = amount_decrypt($row['amount'], $key2);
	        $total += $amount;

	        if ($type1 < 4) {
	        	$kr += $amount;
	        }
	    }	

	    if ($total > 0) {
		    $customers_total ++;
	        if ($total < 500) {
	        	$customers1 ++;
	        	$customers[1] += $total;
	        }
	        elseif ($total < 1000) {
	        	$customers2 ++;
	        	$customers[2] += $total;
	        }
	        elseif ($total < 2000) {
	        	$customers3 ++;
	        	$customers[3] += $total;
	        }
	        elseif ($total > 2000) {
	        	$customers4 ++;
	        	$customers[4] += $total;
	        }

	        if ($country == 0) {
	        	$hungary += $total;
	        	$areas[$area][1] ++;
	        	$areas[$area][2] += $total;
	        	$count[1] ++;

	        	if ($type == 1) {
	        		$gardeners_hu += $total;
	        		$count[2] ++;
	        	}
	        	elseif ($type == 2) {
	        		$wholesalers_hu += $total;
	        		$count[3] ++;
	        	}
	        	elseif ($type == 3) {
	        		$sport_hu += $total;
	        		$count[4] ++;
	        	}
	        	elseif ($type == 4) {
	        		$gr = $total - $kr;
	        		$wholesalers_hu += $kr;
	        		$sport_hu += $gr;
	        		$count[3] ++;
	        		$count[4] ++;
	        	}
	        }
	        else {
	        	$foreign += $total;
	        	$count[5] ++;
	        	if ($type == 1) {
	        		$gardeners_other += $total;
	        		$count[6] ++;
	        	}
	        	elseif ($type == 2) {
	        		$wholesalers_other += $total;
	        		$count[7] ++;
	        	}
	        	elseif ($type == 3) {
	        		$sport_other += $total;
	        		$count[8] ++;
	        	}
	        	elseif ($type == 4) {
	        		$gr = $total - $kr;
	        		$wholesalers_other += $kr;
	        		$sport_other += $gr;
	        		$count[7] ++;
	        		$count[8] ++;
	        	}

		        if ($country == 2) {
		        	$austria += $total;
		        }
		        elseif ($country == 3) {	// Romania
		        	$romania += $total;
		        }
		        elseif ($country == 4 OR $country == 8 OR $country == 15) {	// Slovakia, Poland, Czech
		        	$area3 += $total;
		        }
		        else {
		        	$others += $total;
		        }
		    }
	    }
	}

	$all = $hungary + $austria + $others;
	$gardeners = $gardeners_hu + $gardeners_other;
	$wholesalers = $wholesalers_hu + $wholesalers_other;
	$sport = $sport_hu + $sport_other;

	$hungary_disp = number_format($hungary, 0, ',', ' ');
	$austria_disp = number_format($austria, 0, ',', ' ');
	$romania_disp = number_format($romania, 0, ',', ' ');
	$area3_disp = number_format($area3, 0, ',', ' ');
	$others_disp = number_format($others, 0, ',', ' ');
	$all_disp = number_format($all, 0, ',', ' ');

	///// Last year
	$startdate_last = $year_main_last."-01-01 00:00:00";
	$enddate_last = $year_main_last."-12-31 23:59:59";

	$customers_total_last = 0;
	$customers_last = array();
	$customers1_last = 0;
	$customers2_last = 0;
	$customers3_last = 0;
	$customers4_last = 0;
	$hungary_last = 0;
	$austria_last = 0;
	$others_last = 0;
	$areas_last = array();
	$count_last = array();

	$gardeners_hu_last = 0;
	$wholesalers_hu_last = 0;
	$sport_hu_last = 0;
	$gardeners_other_last = 0;
	$wholesalers_other_last = 0;
	$sport_other_last = 0;

	$query = $db->prepare("SELECT * FROM areas");
	$query->execute();
	$number_fields = $query->rowCount();
	foreach ($query as $row) {
		$area_id = $row['id'];
		$areas_last[$area_id][1] = 0;
		$areas_last[$area_id][2] = 0;
	}

	for ($i=1; $i < 9; $i++) { 
		$count_last[$i] = 0;
	}

	for ($i=1; $i < 5; $i++) { 
		$customers_last[$i] = 0;
	}

	// Customer statistics last year
	$query = $db->prepare("SELECT * FROM `customers` WHERE `status` = 1");
	$query->execute(); 

	foreach ($query as $row) {
		$id = $row['id'];
		$type = $row['type'];
		$country = $row['country'];
		$area = $row['area'];
		$total_last = 0;
		$kr_last = 0;

		$query = $db->prepare("SELECT * FROM `order` WHERE `date` >= :startdate AND `date` <= :enddate AND `name` = :name AND `status` = 4");
		$query->bindParam(":startdate", $startdate_last, PDO::PARAM_STR);
		$query->bindParam(":enddate", $enddate_last, PDO::PARAM_STR);
		$query->bindParam(":name", $id, PDO::PARAM_STR);
		$query->execute(); 

		foreach ($query as $row) {
	        $type1 = $row['type1'];
	        $amount = amount_decrypt($row['amount'], $key2);
	        
	        $total_last += $amount;

	        if ($type1 < 4) {
	        	$kr_last += $amount;
	        }
	    }

	    if ($total_last > 0) {
	    	$customers_total_last ++;
	        if ($total_last < 500) {
	        	$customers1_last ++;
	        	$customers_last[1] += $total_last;
	        }
	        elseif ($total_last < 1000) {
	        	$customers2_last ++;
	        	$customers_last[2] += $total_last;
	        }
	        elseif ($total_last < 2000) {
	        	$customers3_last ++;
	        	$customers_last[3] += $total_last;
	        }
	        elseif ($total_last > 2000) {
	        	$customers4_last ++;
	        	$customers_last[4] += $total_last;
	        }

	        if ($country == 0) {
	        	$hungary_last += $total_last;
	        	$count_last[1] ++;
	        	$areas_last[$area][1] ++;
	        	$areas_last[$area][2] += $total_last;

	        	if ($type == 1) {
	        		$gardeners_hu_last += $total_last;
	        		$count_last[2] ++;
	        	}
	        	elseif ($type == 2) {
	        		$wholesalers_hu_last += $total_last;
	        		$count_last[3] ++;
	        	}
	        	elseif ($type == 3) {
	        		$sport_hu_last += $total_last;
	        		$count_last[4] ++;
	        	}
	        	elseif ($type == 4) {
	        		$gr_last = $total_last - $kr_last;
	        		$wholesalers_hu_last += $kr_last;
	        		$sport_hu_last += $gr_last;
	        		$count_last[3] ++;
	        		$count_last[4] ++;
	        	}
	        }
	        else {
	        	$foreign_last += $total_last;
	        	$count_last[5] ++;
	        	if ($type == 1) {
	        		$gardeners_other_last += $total_last;
	        		$count_last[6] ++;
	        	}
	        	elseif ($type == 2) {
	        		$wholesalers_other_last += $total_last;
	        		$count_last[7] ++;
	        	}
	        	elseif ($type == 3) {
	        		$sport_other_last += $total_last;
	        		$count_last[8] ++;
	        	}
	        	elseif ($type == 4) {
	        		$gr_last = $total_last - $kr_last;
	        		$wholesalers_other_last += $kr_last;
	        		$sport_other_last += $gr_last;
	        		$count_last[7] ++;
	        		$count_last[8] ++;
	        	}

		        if ($country == 2) {
		        	$austria_last += $total_last;
		        }
		        elseif ($country == 3) {	// Romania
		        	$romania_last += $total_last;
		        }
		        elseif ($country == 4 OR $country == 8 OR $country == 15) {	// Slovakia, Poland, Czech
		        	$area3_last += $total_last;
		        }
		        else {
		        	$others_last += $total_last;
		        }
		    }
	    }  
	}

		$all_last = $hungary_last + $foreign_last;
		$gardeners_last = $gardeners_hu_last + $gardeners_other_last;
		$wholesalers_last = $wholesalers_hu_last + $wholesalers_other_last;
		$sport_last = $sport_hu_last + $sport_other_last;


		$hungary_last_disp = number_format($hungary_last, 0, ',', ' ');
		$austria_last_disp = number_format($austria_last, 0, ',', ' ');
		$romania_last_disp = number_format($romania_last, 0, ',', ' ');
		$area3_last_disp = number_format($area3_last, 0, ',', ' ');
		$others_last_disp = number_format($others_last, 0, ',', ' ');
		$all_last_disp = number_format($all_last, 0, ',', ' ');

		echo '<div class="row">';
		echo '<div class="col-md-10"><h4>'.$year_main."</h4></div>";
		echo '<div class="col-md-2">';
		?>
		<button type="button" class="btn <?php echo $btn_this; ?>" style="float:right; margin-left:5px;" onclick="document.location = 'customer_statistics.php?year=<?php echo $currentyear; ?>'"><?php echo $currentyear; ?></button>

		<button type="button" class="btn <?php echo $btn_last; ?>" style="float:right;" onclick="document.location = 'customer_statistics.php?year=<?php echo $lastyear; ?>'"><?php echo $lastyear; ?></button>

		<?php
		echo "</div></div>";

		echo '<div class="row">';

		echo '<div class="col-md-8"><table class="table">';
			echo "<tr class='title'><td></td><td colspan='3' class='border'>".$year_main."</td><td colspan='3'>".$year_main_last."</td></tr>";
			echo "<tr class='title'><td>Vevő csoport</td><td>&ast;</td><td>m&sup2;</td><td class='border'>%</td><td>&ast;</td><td>m&sup2;</td><td>%</td></tr>";
			echo "<tr><td>Kertépítők</td><td>".($count[2]+$count[6])."</td><td>".number_format($gardeners, 0, ',', ' ')."</td><td class='border'><i>".number_format($gardeners/$all*100, 1, ',', ' ')."%</i></td><td>".($count_last[2]+$count_last[6])."</td><td>".number_format($gardeners_last, 0, ',', ' ')."</td><td><i>".number_format($gardeners_last/$all_last*100, 1, ',', ' ')."%</i></td></tr>";
			echo "<tr><td>Nagykereskedő</td><td>".($count[3]+$count[7])."</td><td>".number_format($wholesalers, 0, ',', ' ')."</td><td class='border'><i>".number_format($wholesalers/$all*100, 1, ',', ' ')."%</i></td><td>".($count_last[3]+$count_last[7])."</td><td>".number_format($wholesalers_last, 0, ',', ' ')."</td><td><i>".number_format($wholesalers_last/$all_last*100, 1, ',', ' ')."%</i></td></tr>";
			echo "<tr><td>Sport</td><td>".($count[4]+$count[8])."</td><td>".number_format($sport, 0, ',', ' ')."</td><td class='border'><i>".number_format($sport/$all*100, 1, ',', ' ')."%</i></td><td>".($count_last[4]+$count_last[8])."</td><td>".number_format($sport_last, 0, ',', ' ')."</td><td><i>".number_format($sport_last/$all_last*100, 1, ',', ' ')."%</i></td></tr>";
			echo "<tr><td><b>Total</b></td><td></td><td><b>".$all_disp."</b></td><td class='border'><b><i>100%</i></b></td><td></td><td><b>".$all_last_disp."</b></td><td><b><i>100%</i></b></td></tr>";
		echo "</table></div>";

		echo "</div><br><br><br>";

		echo '<div class="row">';

		echo '<div class="col-md-8"><table class="table">';
			echo "<tr class='title'><td></td><td colspan='3' class='border'>".$year_main."</td><td colspan='3'>".$year_main_last."</td></tr>";
			echo "<tr class='title'><td>Vevő csoport</td><td>&ast;</td><td>m&sup2;</td><td class='border'>%</td><td>&ast;</td><td>m&sup2;</td><td>%</td></tr>";
			echo "<tr class='pad-top'><td>Magyar</td><td>".$count[1]."</td><td>".$hungary_disp."</td><td class='border'>".number_format($hungary/$all*100, 1, ',', ' ')."%</td><td>".$count_last[1]."</td><td>".$hungary_last_disp."</td><td>".number_format($hungary_last/$all_last*100, 1, ',', ' ')."%</td></tr>";
			echo "<tr><td><i>&nbsp; Kertépítők</i></td><td>".$count[2]."</td><td><i>&nbsp; ".number_format($gardeners_hu, 0, ',', ' ')."</i></td><td class='border'><i>&nbsp; ".number_format($gardeners_hu/$hungary*100, 1, ',', ' ')."%</i></td><td>".$count_last[2]."</td><td><i>&nbsp; ".number_format($gardeners_hu_last, 0, ',', ' ')."</i></td><td><i>&nbsp; ".number_format($gardeners_hu_last/$hungary_last*100, 1, ',', ' ')."%</i></td></tr>";
			echo "<tr><td><i>&nbsp; Nagykereskedő</i></td><td>".$count[3]."</td><td><i>&nbsp; ".number_format($wholesalers_hu, 0, ',', ' ')."</i></td><td class='border'><i>&nbsp; ".number_format($wholesalers_hu/$hungary*100, 1, ',', ' ')."%</i></td><td>".$count_last[3]."</td><td><i>&nbsp; ".number_format($wholesalers_hu_last, 0, ',', ' ')."</i></td><td><i>&nbsp; ".number_format($wholesalers_hu_last/$hungary_last*100, 1, ',', ' ')."%</i></td></tr>";
			echo "<tr><td><i>&nbsp; Sport</i></td><td>".$count[4]."</td><td><i>&nbsp; ".number_format($sport_hu, 0, ',', ' ')."</i></td><td class='border'><i>&nbsp; ".number_format($sport_hu/$hungary*100, 1, ',', ' ')."%</i></td><td>".$count_last[4]."</td><td><i>&nbsp; ".number_format($sport_hu_last, 0, ',', ' ')."</i></td><td><i>&nbsp; ".number_format($sport_hu_last/$hungary_last*100, 1, ',', ' ')."%</i></td></tr>";
			echo "<tr class='pad-top'><td>Külföld</td><td>".$count[5]."</td><td>".number_format($foreign, 0, ',', ' ')."</td><td class='border'>".number_format($foreign/$all*100, 1, ',', ' ')."%</td><td>".$count_last[5]."</td><td>".number_format($foreign_last, 0, ',', ' ')."</td><td>".number_format($foreign_last/$all_last*100, 1, ',', ' ')."%</td></tr>";
			echo "<tr><td><i>&nbsp; Kertépítők</i></td><td>".$count[6]."</td><td><i>&nbsp; ".number_format($gardeners_other, 0, ',', ' ')."</i></td><td class='border'><i>&nbsp; ".number_format($gardeners_other/$foreign*100, 1, ',', ' ')."%</i></td><td>".$count_last[6]."</td><td><i>&nbsp; ".number_format($gardeners_other_last, 0, ',', ' ')."</i></td><td><i>&nbsp; ".number_format($gardeners_other_last/$foreign_last*100, 1, ',', ' ')."%</i></td></tr>";
			echo "<tr><td><i>&nbsp; Nagykereskedő</i></td><td>".$count[7]."</td><td><i>&nbsp; ".number_format($wholesalers_other, 0, ',', ' ')."</i></td><td class='border'><i>&nbsp; ".number_format($wholesalers_other/$foreign*100, 1, ',', ' ')."%</i></td><td>".$count_last[7]."</td><td><i>&nbsp; ".number_format($wholesalers_other_last, 0, ',', ' ')."</i></td><td><i>&nbsp; ".number_format($wholesalers_other_last/$foreign_last*100, 1, ',', ' ')."%</i></td></tr>";
			echo "<tr><td><i>&nbsp; Sport</i></td><td>".$count[8]."</td><td><i>&nbsp; ".number_format($sport_other, 0, ',', ' ')."</i></td><td class='border'><i>&nbsp; ".number_format($sport_other/$foreign*100, 1, ',', ' ')."%</i></td><td>".$count_last[8]."</td><td><i>&nbsp; ".number_format($sport_other_last, 0, ',', ' ')."</i></td><td><i>&nbsp; ".number_format($sport_other_last/$foreign_last*100, 1, ',', ' ')."%</i></td></tr>";
			echo "<tr><td><b>Total</b></td><td></td><td><b>".$all_disp."</b></td><td class='border'><b>100%</b></td><td></td><td><b>".$all_last_disp."</b></td><td><b>100%</b></td></tr>";
		echo "</table></div>";

		echo "</div><br><br><br>";

		// country statistics
		echo '<div class="row">';

		echo '<div class="col-md-6"><table class="table">';
			echo "<tr class='title'><td></td><td colspan='2' class='border'>".$year_main."</td><td colspan='2'>".$year_main_last."</td></tr>";
			echo "<tr class='title'><td>Ország</td><td>m&sup2;</td><td class='border'>%</td><td>m&sup2;</td><td>%</td></tr>";
			echo "<tr><td>Magyarország</td><td>".$hungary_disp."</td><td class='border'><i>".number_format($hungary/$all*100, 1, ',', ' ')."%</i></td><td>".$hungary_last_disp."</td><td><i>".number_format($hungary_last/$all_last*100, 1, ',', ' ')."%</i></td></tr>";
			echo "<tr><td>Ausztria</td><td>".$austria_disp."</td><td class='border'><i>".number_format($austria/$all*100, 1, ',', ' ')."%</i></td><td>".$austria_last_disp."</td><td><i>".number_format($austria_last/$all_last*100, 1, ',', ' ')."%</i></td></tr>";
			echo "<tr><td>Románia</td><td>".$romania_disp."</td><td class='border'><i>".number_format($romania/$all*100, 1, ',', ' ')."%</i></td><td>".$romania_last_disp."</td><td><i>".number_format($romania_last/$all_last*100, 1, ',', ' ')."%</i></td></tr>";
			echo "<tr><td>SK, PL, CZ</td><td>".$area3_disp."</td><td class='border'><i>".number_format($area3/$all*100, 1, ',', ' ')."%</i></td><td>".$area3_last_disp."</td><td><i>".number_format($area3_last/$all_last*100, 1, ',', ' ')."%</i></td></tr>";
			echo "<tr><td>Maradék</td><td>".$others_disp."</td><td class='border'><i>".number_format($others/$all*100, 1, ',', ' ')."%</i></td><td>".$others_last_disp."</td><td><i>".number_format($others_last/$all_last*100, 1, ',', ' ')."%</i></td></tr>";
			echo "<tr><td><b>Total</b></td><td><b>".$all_disp."</b></td><td class='border'><b><i>100%</i></b></td><td><b>".$all_last_disp."</b></td><td><b><i>100%</i></b></td></tr>";
		echo "</table></div>";

		echo "</div><br><br><br>";


		echo '<div class="row">';

		//echo '<div class="col-md-1 hidden-print">';
			//echo '<div id="piechart1"></div>';
		//echo '</div>';

		$total_count = 0;
		$total_count2 = 0;
		echo '<div class="col-md-8"><table class="table">';
			echo "<tr class='title'><td></td><td colspan='3' class='border'>".$year_main."</td><td colspan='3'>".$year_main_last."</td></tr>";
			echo "<tr class='title'><td>Környék</td><td>&ast;</td><td>m&sup2;</td><td class='border'>%</td><td>&ast;</td><td>m&sup2;</td><td>%</td></tr>";
			
			foreach ($areas as $area_id => $value) {
				$count = $value[1];
				$amount2 = $value[2];

				$total_count += $count;
				$total_count2 += $areas_last[$area_id][1];

				$query = $db->prepare("SELECT * FROM areas WHERE `id` = :id");
				$query->bindParam(":id", $area_id, PDO::PARAM_STR);
				$query->execute();
				$result = $query->fetch(PDO::FETCH_OBJ);

				echo "<tr><td>".$result->name." (".$result->short.")</td><td>".$count."</td><td>".number_format($amount2, 0, ',', ' ')."</td><td class='border'><i>".number_format($amount2/$hungary*100, 1, ',', ' ')."%</i></td><td>".$areas_last[$area_id][1]."</td><td>".number_format($areas_last[$area_id][2], 0, ',', ' ')."</td><td><i>".number_format($areas_last[$area_id][2]/$hungary_last*100, 1, ',', ' ')."%</i></td></tr>";
			}
			echo "<tr><td><b>Total</b></td><td><b>".$total_count."</b></td><td><b>".$hungary_disp."</b></td><td class='border'></td><td><b>".$total_count2."</b></td><td><b>".$hungary_last_disp."</b></td><td></td></tr>";
		echo "</table></div>";

		//echo '<div class="col-md-1 hidden-print">';
			//echo '<div id="piechart2"></div></div>';


		echo "</div><br><br><br>";

		echo '<div class="row">';
		echo '<div class="col-md-8"><table class="table">';
			echo "<tr class='title'><td></td><td colspan='3' class='border'>".$year_main."</td><td colspan='3'>".$year_main_last."</td></tr>";
			echo "<tr class='title'><td>m&sup2; / vevő</td><td>&ast;</td><td>m&sup2;</td><td class='border'>%</td><td>&ast;</td><td>m&sup2;</td><td>%</td></tr>";
			echo "<tr><td>2000+</td><td>".$customers4."</td><td>".number_format($customers[4], 0, ',', ' ')."</td><td class='border'><i>".number_format($customers[4]/$all*100, 1, ',', ' ')."%</i></td><td>".$customers4_last."</td><td>".number_format($customers_last[4], 0, ',', ' ')."</td><td><i>".number_format($customers_last[4]/$all_last*100, 1, ',', ' ')."%</i></td></tr>";
			echo "<tr><td>1000 - 2000</td><td>".$customers3."</td><td>".number_format($customers[3], 0, ',', ' ')."</td><td class='border'><i>".number_format($customers[3]/$all*100, 1, ',', ' ')."%</i></td><td>".$customers3_last."</td><td>".number_format($customers_last[3], 0, ',', ' ')."</td><td><i>".number_format($customers_last[3]/$all_last*100, 1, ',', ' ')."%</i></td></tr>";
			echo "<tr><td>500 - 1000</td><td>".$customers2."</td><td>".number_format($customers[2], 0, ',', ' ')."</td><td class='border'><i>".number_format($customers[2]/$all*100, 1, ',', ' ')."%</i></td><td>".$customers2_last."</td><td>".number_format($customers_last[2], 0, ',', ' ')."</td><td><i>".number_format($customers_last[2]/$all_last*100, 1, ',', ' ')."%</i></td></tr>";
			echo "<tr><td>0 - 500</td><td>".$customers1."</td><td>".number_format($customers[1], 0, ',', ' ')."</td><td class='border'><i>".number_format($customers[1]/$all*100, 1, ',', ' ')."%</i></td><td>".$customers1_last."</td><td>".number_format($customers_last[1], 0, ',', ' ')."</td><td><i>".number_format($customers_last[1]/$all_last*100, 1, ',', ' ')."%</i></td></tr>";
			echo "<tr><td><b>Total</b></td><td><b>".$customers_total."</b></td><td><b>".$all_disp."</b></td><td class='border'><b>100%</b></td><td><b>".$customers_total_last."</b></td><td><b>".$all_last_disp."</b></td><td><b>100%</b></td></tr>";
		echo "</table></div>";
		echo "</div></div>";
