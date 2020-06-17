<?php 
//////////////////////////////////////////////////
// List of all applications with a single agent //
//////////////////////////////////////////////////

include('views/_header'.$header.'.php');

require_once('config/config.php');
$db = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.'', DB_USER, DB_PASS);
$db -> exec("set names utf8");

//get id of agent
$id = $_GET["id"];

if (isset($_SESSION['userid'])) {
  $user = $_SESSION['userid'];
}
else {
  $user = $_COOKIE["userid"];
}

//////////
//get data of field from database
$query = $db->prepare("SELECT * FROM agents WHERE `id` = :id");
$query->bindParam(":id", $id, PDO::PARAM_STR);
$query->execute();

$result = $query->fetch(PDO::FETCH_OBJ);

//get variables
$name = $result->name;
$stock = $result->stock;
$price = $result->price;
$min = $result->min;
$type = $result->type;

if ($type == 1) {
  $unit = "kg";
}
elseif ($type == 2) {
  $unit = "l";
}

echo '<div class="inputform"><div class="row"><div class="col-md-4">';
echo "<h3 style='margin-top: 0px;'><span class='glyphicon glyphicon-unchecked'></span>&nbsp;&nbsp;".$name."</h3></div>";
echo '<div class="col-md-2 property"><span class="glyphicon glyphicon-scale"></span>&nbsp;&nbsp;'.$stock." ".$unit; 
echo "</div></div></div>";


echo '<div class="panel panel-open">';
echo '<div class="panel-heading"><h4><span class="glyphicon glyphicon-list-alt"></span>&nbsp;&nbsp; Folyamat</h4></div>';

echo '<div class="field">';

echo "<table class='table'>";
echo "<tr class='title'><td>DÁTUM</td><td>MÓD</td><td>+/–</td><td class='border'>KÉSZLET</td><td>ELHAJLÁS</td><td class='border'>%</td><td>TERÜLET</td><td>USER</td><td>LINK</td>";
echo "</tr>";


//get operations of the field     
$query = $db->prepare("SELECT * FROM movement WHERE `agent` = :id ORDER BY `datum` ASC, `id` ASC");
$query->bindParam(":id", $id, PDO::PARAM_STR);
$query->execute();

foreach ($query as $row) {
  $movementid = $row['id'];
  $datum = $row['datum'];
  $type = $row['type'];
  $difference = $row['difference'];
  $total = $row['total'];
  $link = $row['link'];
  $user = $row['user'];
  $deviation = 0;

  $query = $db->prepare("SELECT * FROM operations WHERE `id` = :id");
  $query->bindParam(":id", $link, PDO::PARAM_STR);
  $query->execute();
  $result = $query->fetch(PDO::FETCH_OBJ);
  $field = $result->field;

  $query = $db->prepare("SELECT * FROM fields WHERE `id` = :id AND `complete` < 1");
  $query->bindParam(":id", $field, PDO::PARAM_STR);
  $query->execute();
  $result = $query->fetch(PDO::FETCH_OBJ);
  $fieldname = $result->name;
  
  $query = $db->prepare("SELECT * FROM users WHERE `id` = :id");
  $query->bindParam(":id", $user, PDO::PARAM_STR);
  $query->execute();
  $result = $query->fetch(PDO::FETCH_OBJ);
  $username = $result->username;



  //Get type
  switch ($type) {
    case 1:
      $typename = "<i>Fogyasztás</i>";
      $style = "use";
      $break = "&nbsp;&nbsp;";
      $prefix = "- ";
      $deviation = 0;
      $percenttext = "";
      $fieldtext = "<a href='https://turfgrass.site/fields.php?field=".$field."'>".$fieldname."</a>";
      break;
    case 2:
      $typename = "<b>Naptár</b>";
      $style = "real";
      $break = "";
      $prefix = "- ";
      $deviation = round($previous - $total, 2);
      $percent = round($deviation / $difference * 100, 1) ;
      $percenttext = $percent." %";
      $fieldtext = "";
      break;
    case 3:
      $typename = "<b>Inventura</b>";
      $style = "real";
      $break = "";
      $prefix = "- ";
      $deviation = round($previous - $total, 2);
      $percent = round($deviation / $difference * 100, 1);
      $percenttext = $percent." %";
      $fieldtext = "";
      break;
    case 4:
      $typename = "Szállitás";
      $style = "green";
      $break = "";
      $prefix = "+ ";
      $deviation = 0;
      $percenttext = "";
      $fieldtext = "";
      break;
    case 5:
      $typename = "Eladás";
      $style = "yellow";
      $break = "";
      $prefix = "- ";
      $deviation = 0;
      $percenttext = "";
      $fieldtext = "";
      break;
    case 6:
      $typename = "Igazítás";
      $style = "normal";
      $break = "";
      $prefix = "";
      $deviation = 0;
      $percenttext = "";
      $fieldtext = "";
      break;
  }

  echo "<tr class='".$style."'><td>".$datum."</td>";
  echo "<td>".$typename."</td>";
  echo "<td>".$break.$prefix.$difference."</td>";
  echo "<td class='border'>".$break.$total."</td>";
  echo "<td>".$deviation."</td>";
  echo "<td class='border'>".$percenttext."</td>";
  echo "<td>".$fieldtext."</td>";
  echo "<td>".$username."</td>";
  echo "<td>".$link."</td>";
  echo '</tr>';    

  $previous = $total;
}

echo "</table>";
echo "</div></div>";






	
