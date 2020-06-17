<?php 
/////////////////////////////////////////////////
// List of operations on every field (outside) //
/////////////////////////////////////////////////

include('views/_header'.$header.'.php');

require_once('config/config.php');
$db = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.'', DB_USER, DB_PASS);
$db -> exec("set names utf8");

//get id of field
$id = $_GET["field"];
$user = $_SESSION['username'];

//get data of field from database
$query = $db->prepare("SELECT * FROM fields WHERE `id` = :id AND `complete` < 1");
$query->bindParam(":id", $id, PDO::PARAM_STR);
$query->execute();

$result = $query->fetch(PDO::FETCH_OBJ);

//get variables
$name = $result->name;
$start = $result->start;
$size = number_format($result->size, 1, ",", ".");
$seed = $result->seed;
$complete = $result->complete * 100;

echo '<div class="inputform"><div class="row"><div class="col-md-4">';
echo "<h3 style='margin-top: 0px;'><span class='glyphicon glyphicon-unchecked'></span>&nbsp;&nbsp;".$name."</h3></div>";
echo '<div class="col-md-2 property"><span class="glyphicon glyphicon-fullscreen"></span>&nbsp;&nbsp;'.$size." ha</div>";
echo '<div class="col-md-2 property"><span class="glyphicon glyphicon-time"></span>&nbsp;&nbsp;'.$start."</div>";
echo '<div class="col-md-2 property"><span class="glyphicon glyphicon-minus"></span>&nbsp;&nbsp;Szedett: '.$complete.'%</div>';
//echo '<div class="col-md-1 property"><span class="glyphicon glyphicon-grain"></span>&nbsp;&nbsp;'.$seed."</div>";
//echo '<div class="col-md-3"><button type="button" class="btn btn-primary btn-lg" data-toggle="modal" data-target="#planModal" style="float:right;"><span class="glyphicon glyphicon-plus"></span>&nbsp;&nbsp; Neuer Plan</button></div>';
echo "</div>";

echo '<div class="row"><div class="col-md-12">';
echo '<div class="panel panel-open">';
echo '<div class="panel-heading"><h4><span class="glyphicon glyphicon-list-alt"></span>&nbsp;&nbsp; Munkák</h4></div>';

echo '<div class="field">';

echo "<table class='table table-striped'>";
echo "<tr class='title'><td>Dátum</td><td>Termék</td><td>Mennyiség/ha</td><td>Mennyiség összes</td><td>Kész %</td><td><span class='glyphicon glyphicon-user'></span></td><td>Jegyzet</td></tr>";

$costsum = 0;

//get operations of the field     
$query = $db->prepare("SELECT * FROM operations WHERE `field` = :fieldid AND `delete` = 0 ORDER BY `datum` DESC");
$query->bindParam(":fieldid", $id, PDO::PARAM_STR);
$query->execute();

foreach ($query as $row) {
  $datum = $row['datum'];
  $agent = $row['agent'];
  $amount = $row['amount'];
  $total = $row['total'];
  $small = $row['small'];
  
  if ($small == 1) {
    $complete_work = "Rész";
  }
  else {
    $complete_nr = round($row['complete'] * 100, 1);
    $complete_work = $complete_nr."%";
  }

  if ($row['note']=="0") {
    $note = "";
  }
  else {
    $note = $row['note'];
  }

  echo "<tr><td>".$datum."</td>";
  //get name of agent
  $query = $db->prepare("SELECT * FROM agents WHERE `id` = :id");
  $query->bindParam(":id", $agent, PDO::PARAM_STR);
  $query->execute();
  $result = $query->fetch(PDO::FETCH_OBJ);

  echo "<td>".$result->name."</td>";

  //$cost = $result->price * $total;
  //$costdisplay = number_format($cost, 0, ',', ' ');

  echo "<td>".$amount."</td>";
  echo "<td>".$total."</td>";
  //echo "<td>".$costdisplay."</td>";
  echo "<td>".$complete_work."</td>";
  
  $query = $db->prepare("SELECT * FROM users WHERE `id` = :id");
  $query->bindParam(":id", $row['user'], PDO::PARAM_STR);
  $query->execute();
  $result = $query->fetch(PDO::FETCH_OBJ);
  $user = $result->username;

  echo "<td>".$user."</td>";
  echo "<td>".$note."</td></tr>";
}

echo "</table>";
echo "</div></div></div></div><br>";

?>

<div class="row"><div class="col-md-12">
<a href="fields.php"><button type="button" class="btn btn-secondary" style="float: right;">Vissza</button></a>
</div></div>

</div>



	
