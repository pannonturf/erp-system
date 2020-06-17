<?php
include('functions.php');

require_once('../config/config.php');
$db = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.'', DB_USER, DB_PASS);

$datum = $_POST['datum'];
$length = $_POST['length'];


$datum3= date('Y-m-d', strtotime($datum.' +2 day'));

//Icons made by <a href="https://www.flaticon.com/authors/plainicon" title="Plainicon">Plainicon</a> from <a href="https://www.flaticon.com/"
?>

<input id="selecteddate" value="<?php echo $datum; ?>" type="hidden"/>

<table class="table" style="width: 800px">
	<tr>
		<td style="border-top: 0; width: 20%;"><input type='date'  id="moreDatum" value="<?php echo $datum; ?>" name="trucks[1][1]"></td>
		<td style="border-top: 0; width: 15%;"><button type="button" class="btn btn-complete btn-sm" style="float:left; margin-top: 5px;" onclick="changeTruck(1,1)"><span class="glyphicon glyphicon-minus"></span></button>
		<button type="button" class="btn btn-complete btn-sm" style="float:left; margin-left: 5px; margin-top: 5px;" onclick="changeTruck(1,2)"><span class="glyphicon glyphicon-plus"></span></button></td>
		<td style="border-top: 0; width: 65%;" id="truckpics_1"><img src="../img/truck.png" class="truck"><img src="../img/truck.png" class="truck"><img src="../img/truck.png" class="truck"></td>
		<input id="truckdatum_1" name="trucks[1][2]" value="3" type="hidden"/>
	</tr>
	<?php
	for ($i=1; $i < $length; $i++) { 
		$nextdatum = date('Y-m-d', strtotime($datum.' +'.$i.' day'));
		$t = $i + 1;

		echo '<tr><td style="border-top: 0; width: 20%;"><input type="date" id="moreDatum" value="'.$nextdatum.'" name="trucks['.$t.'][1]"></td>';
		echo '<td style="border-top: 0;  width: 15%;"><button type="button" class="btn btn-complete btn-sm" style="float:left; margin-top: 5px;" onclick="changeTruck('.$t.',1)"><span class="glyphicon glyphicon-minus"></span></button>';
		echo '<button type="button" class="btn btn-complete btn-sm" style="float:left; margin-left: 5px; margin-top: 5px;" onclick="changeTruck('.$t.',2)"><span class="glyphicon glyphicon-plus"></span></button>';
		echo '</td><td style="border-top: 0; width: 65%;" id="truckpics_'.$t.'"><img src="../img/truck.png" class="truck"><img src="../img/truck.png" class="truck"><img src="../img/truck.png" class="truck"></td></tr>';
		echo '<input id="truckdatum_'.$t.'" name="trucks['.$t.'][2]" value="3" type="hidden"/>';
	}

	?>
</table>

</div>

 
