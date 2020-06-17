<?php 
/////////////////////////////////////////
// Statistics about geographical areas //
/////////////////////////////////////////

require_once('config/config.php');
include('views/_header'.$header.'.php');
include('tools/functions.php');

$db = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.'', DB_USER, DB_PASS);
$db -> exec("set names utf8");

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


echo '<div class="inputform">';
echo '<div class="row">';
echo '<div class="col-md-10"><h3 style="margin-top:10px;">Terség statisztika</h3></div>';
echo '<div class="col-md-2">';
?>
<button type="button" class="btn <?php echo $btn_this; ?>" style="float:right; margin-left:5px;" onclick="document.location = 'areas.php?year=<?php echo $currentyear; ?>'"><?php echo $currentyear; ?></button>

<button type="button" class="btn <?php echo $btn_last; ?>" style="float:right;" onclick="document.location = 'areas.php?year=<?php echo $lastyear; ?>'"><?php echo $lastyear; ?></button>

<?php
echo "</div></div>";


echo '<div class="row">';
echo '<div class="col-md-10"><table class="table"><thead>';
	echo "<tr class='title'><th></th><th colspan='3' class='border'>Összes</th><th colspan='3' class='border'>Nagykereskedő</th><th colspan='3'>Kertépítő</th></tr>";
	echo "<tr class='title'><th class='border'>Megye</th>";
	echo "<th>&ast;</th>";
	echo "<th>m&sup2;</th>";
	echo "<th class='border'>%</th>";
	echo "<th>&ast;</th>";
	echo "<th>m&sup2;</th>";
	echo "<th class='border'>%</th>";
	echo "<th>&ast;</th>";
	echo "<th>m&sup2;</th>";
	echo "<th>%</th>";
	echo "</tr></thead><tbody>";

$i = 1;

$total_count = 0;
$total_m2 = 0;
$total1_count = 0;
$total1_m2 = 0;
$total2_count = 0;
$total2_m2 = 0;

$query = $db->prepare("SELECT * FROM districts");
$query->execute();
$number_districts = $query->rowCount();
foreach ($query as $row) {
	$area_id = $row['id'];
	$areas[$area_id][1] = 0;
	$areas[$area_id][2] = 0;
	$areas[$area_id][3] = 0;
	$areas[$area_id][4] = 0;
	$areas[$area_id][5] = 0;
	$areas[$area_id][6] = 0;
}

$query = $db->prepare("SELECT * FROM `order` WHERE `date` >= :startdate AND `date` <= :enddate AND `type1` < 4 AND `country` = 0 AND `status` = 4");
$query->bindParam(":startdate", $startdate, PDO::PARAM_STR);
$query->bindParam(":enddate", $enddate, PDO::PARAM_STR);
$query->execute(); 

foreach ($query as $row) {
	$name = $row['name'];
	$city = $row['city'];
    $amount = amount_decrypt($row['amount'], $key2);

    if ($city > 0) {
	    $query2 = $db->prepare("SELECT * FROM cities WHERE `id` = :id");
	    $query2->bindParam(":id", $city, PDO::PARAM_STR);
	    $query2->execute();
	    $result2 = $query2->fetch(PDO::FETCH_OBJ);
	    $district = $result2->district;

	    $query3 = $db->prepare("SELECT * FROM customers WHERE `id` = :id");
	    $query3->bindParam(":id", $name, PDO::PARAM_STR);
	    $query3->execute();
	    $result3 = $query3->fetch(PDO::FETCH_OBJ);
	    $customer_type = $result3->type;
    
    	$total_m2 += $amount;
    	$total_count ++;

		$areas[$district][1] ++;
	    $areas[$district][2] += $amount;

	    if ($customer_type == 2) {
	    	$total1_count ++;
	    	$total1_m2 += $amount;
	    	
	    	$areas[$district][3] ++;
	    	$areas[$district][4] += $amount;
	    }
	    elseif ($customer_type == 1) {
	    	$total2_count ++;
	    	$total2_m2 += $amount;
	    	
	    	$areas[$district][5] ++;
	    	$areas[$district][6] += $amount;
	    }
	}
}

$query = $db->prepare("SELECT * FROM districts");
$query->execute();
$number_districts = $query->rowCount();
foreach ($query as $row) {
	$area_id = $row['id'];
	$district_name = $row['name'];

	echo "<tr>";
	echo '<td class="border">'.$district_name."</td>";
    echo '<td>'.$areas[$area_id][1]."</td>";
	echo '<td>'.number_format($areas[$area_id][2], 0, ',', ' ')."</td>";	
	echo '<td class="border"><i>'.number_format($areas[$area_id][2]/$total_m2*100, 1, ',', ' ')."</i></td>";	
    echo '<td>'.$areas[$area_id][3]."</td>";
	echo '<td>'.number_format($areas[$area_id][4], 0, ',', ' ')."</td>";	
	echo '<td class="border"><i>'.number_format($areas[$area_id][4]/$total1_m2*100, 1, ',', ' ')."</i></td>";	
    echo '<td>'.$areas[$area_id][5]."</td>";
	echo '<td>'.number_format($areas[$area_id][6], 0, ',', ' ')."</td>";
	echo '<td><i>'.number_format($areas[$area_id][6]/$total2_m2*100, 1, ',', ' ')."</i></td>";		
	echo "</tr>";
}

echo "<tr>";
echo '<td class="border"><b>ÖSSZES</b></td>';
echo '<td><b>'.number_format($total_count, 0, ',', ' ')."</b></td>";
echo '<td><b>'.number_format($total_m2, 0, ',', ' ')."</b></td>";
echo '<td class="border"><b><i>100%</i></b></td>';
echo '<td><b>'.number_format($total1_count, 0, ',', ' ')."</b></td>";
echo '<td><b>'.number_format($total1_m2, 0, ',', ' ')."</b></td>";	
echo '<td class="border"><b><i>100%</i></b></td>';	
echo '<td><b>'.number_format($total2_count, 0, ',', ' ')."</b></td>";
echo '<td><b>'.number_format($total2_m2, 0, ',', ' ')."</b></td>";
echo '<td><b><i>100%</i></b></td>';
echo "</tr>";

echo '</tbody></table></div></div>'; 

?>

</div>