<?php 
///////////////////////////////////////////////////
// List and detail views of every field (inside) //
///////////////////////////////////////////////////

include('views/_header'.$header.'.php');
include('tools/functions.php');

require_once('config/config.php');
$db = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.'', DB_USER, DB_PASS);
$db -> exec("set names utf8");


//If planned operation was submitted
if (isset($_POST['inputPlan'])) {  

  //Get variables from form
  if (isset($_SESSION['userid'])) {
    $creator = $_SESSION['userid'];
  }
  elseif(isset($_COOKIE['userid'])){
    $creator = $_COOKIE["userid"];
  }
  else {
    $creator = 0;
  }

  $user = $_POST['user'];
  $initialuser = $_POST['user'];
  
  if (isset($_POST['week'])) {
    $week = $_POST['week'];
    if ($week == 1) {
      $kw = date('W');
    }
    elseif ($week == 2) {
      $kw = date('W') + 1;
    }

  }
  else {
    $datum = new DateTime($_POST['date']);
    $kw = $datum->format('W');
  }
  
  $field = $_POST['field'];
  $agents = $_POST['agents'];
  $complete = 0;

  if (!empty($_POST['note'])) {
    $note = $_POST['note'];
  }
  else {
    $note = 0;
  }
            
  $sql = "INSERT INTO `plan` (`id`, `user`, `week`, `field`, `agent`, `amount`, `note`, `initialuser`, `complete`, `creator`) VALUES (NULL, :user, :week, :field, :agent, :amount, :note, :initialuser, :complete, :creator);";
  $query = $db->prepare($sql);
  
  foreach($agents AS $agent => $amount) {

    if ($amount > 0) {
      $query->bindParam(":user", $user, PDO::PARAM_STR);  
      $query->bindParam(":week", $kw, PDO::PARAM_STR);
      $query->bindParam(":field", $field, PDO::PARAM_STR);
      $query->bindParam(":agent", $agent, PDO::PARAM_STR);
      $query->bindParam(":amount", $amount, PDO::PARAM_STR);
      $query->bindParam(":note", $note, PDO::PARAM_STR);
      $query->bindParam(":initialuser", $initialuser, PDO::PARAM_STR);
      $query->bindParam(":complete", $complete, PDO::PARAM_STR);
      $query->bindParam(":creator", $creator, PDO::PARAM_STR);

      $query->execute();
    }     
  }

    //Other message
    echo '<div class="alert alert-success center-block" role="alert">Sikerült!</div>';
}

/////////
//if plan was edited
if (isset($_POST['editPlanForm'])) {
  //Get variables from form
  $id = $_POST['id'];
  $agent = $_POST['agent'];
  $oldnote = $_POST['oldnote'];
  $amount = $_POST['amount'];

  if (isset($_POST['week'])) {
  $week = $_POST['week'];
    if ($week == 1) {
      $kw = date('W');
    }
    elseif ($week == 2) {
      $kw = date('W') + 1;
    }
  }
  else {
    $datum = new DateTime($_POST['date']);
    $kw = $datum->format('W');
  }

  if (!empty($_POST['note'])) {
    if (!empty($_POST['oldnote'])) {
      $note = $oldnote." | ".$_POST['note'];
    }
    else {
      $note = $_POST['note'];
    }
  }
  else {
    $note = $oldnote;
  }

  //Update operations
  $sql = "UPDATE `plan` SET `week` = :week, `agent` = :agent, `amount` = :amount, `note` = :note WHERE `id` = :id";
  $query = $db->prepare($sql);

  $query->bindParam(":week", $kw, PDO::PARAM_STR);
  $query->bindParam(":agent", $agent, PDO::PARAM_STR);
  $query->bindParam(":amount", $amount, PDO::PARAM_STR);
  $query->bindParam(":note", $note, PDO::PARAM_STR);
  $query->bindParam(":id", $id, PDO::PARAM_STR);

  $query->execute();

  echo '<div class="alert alert-success center-block" role="alert">Sikerült!</div>';

 }

/////////
//if plan was deleted
if (isset($_POST['deletePlanForm'])) {
  if (isset($_SESSION['userid'])) {
    $user = $_SESSION['userid'];
  }
  elseif(isset($_COOKIE['userid'])){
    $user = $_COOKIE["userid"];
  }
  else {
    $user = 0;
  }

  $id = $_POST['id'];
  $deleted = 1;

  //Update operations
  $sql = "UPDATE `plan` SET `delete` = :deleted, `deleter` = :deleter WHERE `id` = :id";
  $query = $db->prepare($sql);

  $query->bindParam(":deleted", $deleted, PDO::PARAM_STR);
  $query->bindParam(":deleter", $user, PDO::PARAM_STR);
  $query->bindParam(":id", $id, PDO::PARAM_STR);

  $query->execute();

  echo '<div class="alert alert-success center-block" role="alert">Sikerült!</div>';
}


/////////
//if operation was edited
if (isset($_POST['editForm'])) {
  //Get variables from form
  $id = $_POST['id'];
  $datum = $_POST['date'];
  $field = $_GET["field"];
  $agent = $_POST['agent'];
  $total = $_POST['total'];
  $amount = $_POST['amount'];
  $oldtotal = $_POST['oldtotal'];
  $complete = $_POST['complete'] / 100;

  if (isset($_POST['small'])) {
    $small = 1;
    $complete = 1;
  }
  else {
    $small = 0;
  }

  if (!empty($_POST['note'])) {
    $note = $_POST['note'];
  }
  
  //Update stock of agents
  $query3 = $db->prepare("SELECT * FROM `agents` WHERE `id` = :id");
  $query4 = $db->prepare("UPDATE `agents` SET `stock` = :stock WHERE `id` = :id");

  //get price
  $query3->bindParam(":id", $agent, PDO::PARAM_STR);
  $query3->execute();

  $result3 = $query3->fetch(PDO::FETCH_OBJ);
  $p = $result3->price;
  $cost = $p * $total;
  $x1 = $result3->stock;
  $x = $x1 + $oldtotal - $total;

  //Update operations
  $sql = "UPDATE `operations` SET `datum` = :datum, `field` = :field, `agent` = :agent, `amount` = :amount, `total` = :total, `cost` = :cost, `small` = :small, `complete` = :complete, `note` = :note WHERE `id` = :id";
  $query = $db->prepare($sql);

  $query->bindParam(":datum", $datum, PDO::PARAM_STR);
  $query->bindParam(":field", $field, PDO::PARAM_STR);
  $query->bindParam(":agent", $agent, PDO::PARAM_STR);
  $query->bindParam(":amount", $amount, PDO::PARAM_STR);
  $query->bindParam(":total", $total, PDO::PARAM_STR);
  $query->bindParam(":cost", $cost, PDO::PARAM_STR);
  $query->bindParam(":small", $small, PDO::PARAM_STR);
  $query->bindParam(":complete", $complete, PDO::PARAM_STR);
  $query->bindParam(":note", $note, PDO::PARAM_STR);
  $query->bindParam(":id", $id, PDO::PARAM_STR);

  $query->execute();

  if (!$oldamount == $amount) {
    //Update stock
    $query4->bindParam(":id", $agent, PDO::PARAM_STR);
    $query4->bindParam(":stock", $x, PDO::PARAM_STR);
    $query4->execute(); 

    //Create new entry in movement (edit/delete)
    $sql5 = "INSERT INTO `movement` (`id`, `datum`, `agent`, `difference`, `total`, `type`, `link`, `user`) VALUES (NULL, :datum, :agent, :difference, :total, :type, :link, :user);";
    $query5 = $db->prepare($sql5);

    $type = 6;
    $difference = $total - $oldtotal;

    $query5->bindParam(":datum", $datum, PDO::PARAM_STR);  
    $query5->bindParam(":agent", $agent, PDO::PARAM_STR); 
    $query5->bindParam(":difference", $difference, PDO::PARAM_STR);
    $query5->bindParam(":total", $x, PDO::PARAM_STR);
    $query5->bindParam(":type", $type, PDO::PARAM_STR);
    $query5->bindParam(":link", $id, PDO::PARAM_STR);
    $query5->bindParam(":user", $user, PDO::PARAM_STR);

    $query5->execute();
  } 

  echo '<div class="alert alert-success center-block" role="alert">Sikerült!</div>';
 }

/////////
//if operation was deleted
if (isset($_POST['deleteForm'])) {
  if (isset($_SESSION['userid'])) {
    $user = $_SESSION['userid'];
  }
  elseif(isset($_COOKIE['userid'])){
    $user = $_COOKIE["userid"];
  }
  else {
    $user = 0;
  }

  $id = $_POST['id'];
  $oldtotal = $_POST['oldtotal'];
  $agent = $_POST['agent'];

  $query6 = $db->prepare("SELECT * FROM `operations` WHERE `id` = :id");
  $query6->bindParam(":id", $id, PDO::PARAM_STR);
  $query6->execute();
  $result6 = $query6->fetch(PDO::FETCH_OBJ);
  $datum = $result6->datum;

  /*
  //Update operations
  $sql = "DELETE FROM `operations` WHERE `id` = :id";
  $query = $db->prepare($sql);

  $query->bindParam(":id", $id, PDO::PARAM_STR);
  $query->execute();
  */

  $deleted = 1;

  //Update operations
  $sql = "UPDATE `operations` SET `delete` = :deleted, `deleter` = :deleter WHERE `id` = :id";
  $query = $db->prepare($sql);

  $query->bindParam(":deleted", $deleted, PDO::PARAM_STR);
  $query->bindParam(":deleter", $user, PDO::PARAM_STR);
  $query->bindParam(":id", $id, PDO::PARAM_STR);

  $query->execute();

  //Update stock of agents
  $query3 = $db->prepare("SELECT * FROM `agents` WHERE `id` = :id");
  $query4 = $db->prepare("UPDATE `agents` SET `stock` = :stock WHERE `id` = :id");

  //get stock
  $query3->bindParam(":id", $agent, PDO::PARAM_STR);
  $query3->execute();

  $result3 = $query3->fetch(PDO::FETCH_OBJ);
  $x1 = $result3->stock;
  $x = $x1 + $oldtotal;

  $query4->bindParam(":id", $agent, PDO::PARAM_STR);
  $query4->bindParam(":stock", $x, PDO::PARAM_STR);
  $query4->execute(); 

  //Create new entry in movement (edit/delete)
  $sql5 = "INSERT INTO `movement` (`id`, `datum`, `agent`, `difference`, `total`, `type`, `link`, `user`) VALUES (NULL, :datum, :agent, :difference, :total, :type, :link, :user);";
  $query5 = $db->prepare($sql5);

  $type = 6;
  $difference = 0 - $oldtotal;

  $query5->bindParam(":datum", $datum, PDO::PARAM_STR);  
  $query5->bindParam(":agent", $agent, PDO::PARAM_STR); 
  $query5->bindParam(":difference", $difference, PDO::PARAM_STR);
  $query5->bindParam(":total", $x, PDO::PARAM_STR);
  $query5->bindParam(":type", $type, PDO::PARAM_STR);
  $query5->bindParam(":link", $id, PDO::PARAM_STR);
  $query5->bindParam(":user", $user, PDO::PARAM_STR);

  $query5->execute();

  echo '<div class="alert alert-success center-block" role="alert">Sikerült!</div>';
}

/////////
//If note was added
if (isset($_POST['inputNote'])) {  
  //Get variables from form

  if (isset($_SESSION['userid'])) {
    $user = $_SESSION['userid'];
  }
  elseif (isset($_COOKIE['userid'])) {
    $user = $_COOKIE["userid"];
  }
  else {
    $user = 0;
  }

  $datum = $_POST['date'];
  $field = $_POST['field'];
  $note = $_POST['note'];
  $type = 1;

  $sql = "INSERT INTO `notes` (`id`, `field`, `date`, `note`, `type`, `user`) VALUES (NULL, :field, :datum, :note, :type, :user);";
  $query = $db->prepare($sql);

  $query->bindParam(":user", $user, PDO::PARAM_STR);  
  $query->bindParam(":datum", $datum, PDO::PARAM_STR);
  $query->bindParam(":field", $field, PDO::PARAM_STR);
  $query->bindParam(":type", $type, PDO::PARAM_STR);
  $query->bindParam(":note", $note, PDO::PARAM_STR);;

  $query->execute();

  //Other message
  echo '<div class="alert alert-success center-block" role="alert">Sikerült!</div>';
}

////////////
//If picture was uploaded
//retrieved from: https://www.php-einfach.de/php-tutorial/dateiupload/

if (isset($_POST['inputPicture'])) { 
  //Get variables from form
  if (isset($_SESSION['userid'])) {
    $user = $_SESSION['userid'];
  }
  elseif (isset($_COOKIE['userid'])) {
    $user = $_COOKIE["userid"];
  }
  else {
    $user = 0;
  }

  $datum = $_POST['date'];
  $field = $_POST['field'];
  $note = $_POST['note'];
  $type = 2;

  //upload picture to folder
  $upload_folder = 'img/upload/'; //Das Upload-Verzeichnis
  $filename = pathinfo($_FILES['picture']['name'], PATHINFO_FILENAME);
  $extension = strtolower(pathinfo($_FILES['picture']['name'], PATHINFO_EXTENSION));
   
  //Überprüfung der Dateiendung
  $allowed_extensions = array('png', 'jpg', 'jpeg', 'gif');
  if(!in_array($extension, $allowed_extensions)) {
    die("Csak png, jpg, jpeg és gif");
  }
   
  //Überprüfung der Dateigröße
  $max_size = 2048*1536; //500 KB
  if($_FILES['picture']['size'] > $max_size) {
    die("2 MB maximum");
  }
   
  //Überprüfung dass das Bild keine Fehler enthält
  if(function_exists('exif_imagetype')) { //Die exif_imagetype-Funktion erfordert die exif-Erweiterung auf dem Server
    $allowed_types = array(IMAGETYPE_PNG, IMAGETYPE_JPEG, IMAGETYPE_GIF);
    $detected_type = exif_imagetype($_FILES['picture']['tmp_name']);
    if(!in_array($detected_type, $allowed_types)) {
    die("Csak képek");
    }
  }
   
  //Pfad zum Upload
  $new_path = $upload_folder.$filename.'.'.$extension;
  $source = $filename.'.'.$extension;
   
  //Neuer Dateiname falls die Datei bereits existiert
  if(file_exists($new_path)) { //Falls Datei existiert, hänge eine Zahl an den Dateinamen
    $id = 1;
    do {
    $new_path = $upload_folder.$filename.'_'.$id.'.'.$extension;
    $source = $filename.'_'.$id.'.'.$extension;
    $id++;
    } while(file_exists($new_path));
  }
   
  //Alles okay, verschiebe Datei an neuen Pfad
  move_uploaded_file($_FILES['picture']['tmp_name'], $new_path);
  echo '<div class="alert alert-success center-block" role="alert">Sikerült: <a href="'.$new_path.'">'.$new_path.'</a></div>';


  //Additional entry into database
  $sql = "INSERT INTO `notes` (`id`, `field`, `date`, `note`, `type`, `source`, `user`) VALUES (NULL, :field, :datum, :note, :type, :source, :user);";
  $query = $db->prepare($sql);

  $query->bindParam(":user", $user, PDO::PARAM_STR);  
  $query->bindParam(":datum", $datum, PDO::PARAM_STR);
  $query->bindParam(":field", $field, PDO::PARAM_STR);
  $query->bindParam(":type", $type, PDO::PARAM_STR);
  $query->bindParam(":note", $note, PDO::PARAM_STR);
  $query->bindParam(":source", $source, PDO::PARAM_STR);

  $query->execute();
}



///////////////////////////////////////////
////////////////////
//get id of field
$id = $_GET["field"];
$user = $_SESSION['username'];

if ($id == 0) {

  ////////////
  // Show overview of fields
  echo '<div class="row"><div class="col-md-12">';
  echo '<div class="panel panel-open">';
  echo '<div class="panel-heading"><h4><span class="glyphicon glyphicon-th-large"></span>&nbsp;&nbsp; Területek</h4></div>';

  echo '<div class="field">';

  echo "<table class='table table-striped centertext'>";
  echo "<tr class='title'><td style='text-align:left'>Terület</td><td>ha</td><td>Vetve</td><td>Keverék</td><td class='border'>kg/ha</td><td>Műtragya<br><i>EFt/ha</i></td><td>Permet<br><i>EFt/ha</i></td><td>Összes<br><i>EFt/ha</i></td><td class='border'>Összes<br><i>EFt</i></td><td>N<br><i>kg/ha</i></td><td>P<br><i>kg/ha</i></td><td>K<br><i>kg/ha</i></td></tr>";

  $sizeSum = 0;
  $seeded = 0;
  $fieldsSum = 0;
  $costoverallFertilizerSum = 0;
  $costoverallPesticideSum= 0;
  $nTotalHaSum = 0;
  $pTotalHaSum = 0;
  $kTotalHaSum = 0;

  //get data of fields from database
  $query = $db->prepare("SELECT * FROM fields WHERE `complete` < 1 ORDER BY `name` ASC");
  $query->execute();

  foreach ($query as $row) {
    //get variables
    $id = $row['id'];
    $name = $row['name'];
    $size = $row['size'];
    $displaysize = number_format($size, 1, ",", ".");
    $seed = $row['seed'];
    $amount = $row['amount'];
    $sizeSum += $size;
    $fieldsSum ++;

    $complete = $row['complete'];
    $currentsize = $size * (1 - $complete);
    $currentsizeSum += $currentsize;
    $displaycurrentsize = number_format($currentsize, 1, ",", ".");

    if ($row['start'] == "0000-00-00") {
      $start = "Még nem vetve";
    }
    else {
      $start = $row['start'];
      $seeded ++;
    }
  
    $query = $db->prepare("SELECT * FROM seed WHERE `id` = :id");
    $query->bindParam(":id", $seed, PDO::PARAM_STR);
    $query->execute();
    $result = $query->fetch(PDO::FETCH_OBJ);
    $seedname = $result->name;

    echo "<tr><td style='text-align:left'><a href='https://turfgrass.site/fields.php?field=".$id."'>".$name."</a></td>";
    echo "<td>".$displaysize." <i>(".$displaycurrentsize.")</i></td>";
    echo "<td>".$start."</td>";
    echo "<td>".$seedname."</td>";
    echo "<td class='border'>".$amount."</td>";

    //Calculate costs
    $costoverallFertilizer = 0;
    $costoverallPesticide= 0;

    //get data of agent
    $query = $db->prepare("SELECT * FROM agents");
    $query->execute();

    $nTotal = 0;
    $pTotal = 0;
    $kTotal = 0;
    $nTotalHa = 0;
    $pTotalHa = 0;
    $kTotalHa = 0;

    foreach ($query as $row) {
      $agent = $row['id'];
      $price = $row['price'];
      $type = $row['type'];
      $n = $row['n'];
      $p = $row['p'];
      $k = $row['k'];

      //check if agent was applied   
      $query = $db->prepare("SELECT * FROM operations WHERE `field` = :fieldid AND `agent` = :agent AND `delete` = 0");
      $query->bindParam(":fieldid", $id, PDO::PARAM_STR);
      $query->bindParam(":agent", $agent, PDO::PARAM_STR);
      $query->execute();

      if ($query->rowCount() > 0) {
        $totalsum = 0;
        $haSum = 0;

        foreach ($query as $row) {
          $totalsum += $row['total'];
          $haSum += $row['amount'];
        }
        $costsum = $totalsum * $price;

        if ($type == 1) {
          $costoverallFertilizer += $costsum;

          //Nährstoffbilanz
          $nTotal += $totalsum * $n / 100;
          $pTotal += $totalsum * $p / 100;
          $kTotal += $totalsum * $k / 100;
          $nTotalHa += $haSum * $n / 100;
          $pTotalHa += $haSum * $p / 100;
          $kTotalHa += $haSum * $k / 100;
        }

        if ($type == 2) {
          $costoverallPesticide += $costsum;
        }
      }
    }

    $costoverallFertilizerSum += $costoverallFertilizer;
    $costoverallPesticideSum += $costoverallPesticide;
    $nTotalSum += $nTotal;
    $pTotalSum += $pTotal;
    $kTotalSum += $kTotal;

    $costhaFert = number_format($costoverallFertilizer / $size / 1000, 0, ',', ' ');
    $costhaPest = number_format($costoverallPesticide / $size / 1000, 0, ',', ' '); 
    $costoverallboth = $costoverallPesticide + $costoverallFertilizer;
    $costhaOverall = number_format($costoverallboth / $size / 1000, 0, ',', ' ');
    $costoverallboth = number_format($costoverallboth / 1000, 0, ',', ' ');
    $nTotalHa = number_format($nTotalHa, 0, ',', ' ');
    $pTotalHa = number_format($pTotalHa, 0, ',', ' ');
    $kTotalHa = number_format($kTotalHa, 0, ',', ' ');

    echo "<td><i>".$costhaFert."</i></td>";
    echo "<td><i>".$costhaPest."</i></td>";
    echo "<td><b>".$costhaOverall."</b></td>";
    echo "<td class='border'>".$costoverallboth."</td>";
    echo "<td>".$nTotalHa."</td>";
    echo "<td>".$pTotalHa."</td>";
    echo "<td>".$kTotalHa."</td></tr>";

  }

  $costhaFertSum = number_format($costoverallFertilizerSum / $sizeSum / 1000, 0, ',', ' ');
  $costhaPestSum = number_format($costoverallPesticideSum / $sizeSum / 1000, 0, ',', ' '); 
  $costoverallbothSum = $costoverallPesticideSum + $costoverallFertilizerSum;
  $costhaOverallSum = number_format($costoverallbothSum / $sizeSum / 1000, 0, ',', ' ');
  $costoverallbothSum = number_format($costoverallbothSum / 1000, 0, ',', ' ');
  $nTotalSum = number_format($nTotalSum, 0, ',', ' ');
  $pTotalSum = number_format($pTotalSum, 0, ',', ' ');
  $kTotalSum = number_format($kTotalSum, 0, ',', ' ');

  $sizeSum_disp = number_format($sizeSum, 1, ',', ' ');
  $currentsizeSum_disp = number_format($currentsizeSum, 1, ',', ' ');

  echo "<tr class ='sum' style='background-color: #e1e1e1;'><td style='text-align:left'>Összes</td><td>".$sizeSum_disp." <i>(".$currentsizeSum_disp.")</i></td><td>".$seeded."/".$fieldsSum."</td><td></td><td class='borderwhite'></td><td></td><td></td><td></td><td class='borderwhite'>".$costoverallbothSum."</td><td>".$nTotalSum."</td><td>".$pTotalSum."</td><td>".$kTotalSum."</td></tr>";
  echo "</table>";
  echo "</div></div></div></div><br>";


  ///////////////////
  ////////////
  // Show nutrition on all fields per year
  echo '<div class="row"><div class="col-md-6 hidden-print">';
  echo '<div class="panel panel-open">';
  echo '<div class="panel-heading"><h4><span class="glyphicon glyphicon-filter"></span>&nbsp;&nbsp; Tápanyagi mérleg</h4></div>';

  echo '<div class="field">';

  //get data of agent
  $currentyear = date("Y");
  $lastyear = $currentyear - 1;
  $nextyear = $currentyear + 1;  $lastYearStart = $lastyear."-01-01";
  $lastYearStart = $lastyear."-01-01";
  $lastYearEnd = $lastyear."-12-31";
  $thisYearStart = $currentyear."-01-01";
  $thisYearEnd = $currentyear."-12-31";

  $query = $db->prepare("SELECT * FROM agents");
  $query->execute();

  $nTotal_current = 0;
  $pTotal_current = 0;
  $kTotal_current = 0;
  $nTotal_last = 0;
  $pTotal_last = 0;
  $kTotal_last = 0;

  foreach ($query as $row) {
    $agent = $row['id'];
    $type = $row['type'];
    $n = $row['n'];
    $p = $row['p'];
    $k = $row['k'];

    //check if agent was applied   
    $query = $db->prepare("SELECT * FROM operations WHERE `agent` = :agent AND `delete` = 0 ");
    $query->bindParam(":agent", $agent, PDO::PARAM_STR);
    $query->execute();

    if ($query->rowCount() > 0) {
      $total_current = 0;
      $total_last = 0;

      foreach ($query as $row) {
        $parts = explode("-", $row['datum']);
        $year = $parts[0]; // Teil1
        if ($year == $currentyear) {
          $total_current += $row['total'];
        }
        if ($year == $lastyear) {
          $total_last += $row['total'];
        }
      }

      if ($type == 1) {
        //Nährstoffbilanz
        $nTotal_current += $total_current * $n / 100;
        $pTotal_current += $total_current * $p / 100;
        $kTotal_current += $total_current * $k / 100;

        $nTotal_last += $total_last * $n / 100;
        $pTotal_last += $total_last * $p / 100;
        $kTotal_last += $total_last * $k / 100;
      }  
    }
  }

  $totalHa_current = 164; //bérlet
  $totalHa_last = 164; //bérlet

  // This year
  $nTotalHa_current = $nTotal_current / $totalHa_current;
  $pTotalHa_current = $pTotal_current / $totalHa_current;
  $kTotalHa_current = $kTotal_current / $totalHa_current;

  $nTotal_current = number_format($nTotal_current, 0, ',', ' ');
  $pTotal_current = number_format($pTotal_current, 0, ',', ' ');
  $kTotal_current = number_format($kTotal_current, 0, ',', ' ');

  $nTotalHa_current = number_format($nTotalHa_current, 0, ',', ' ');
  $pTotalHa_current = number_format($pTotalHa_current, 0, ',', ' ');
  $kTotalHa_current = number_format($kTotalHa_current, 0, ',', ' ');

  // Last year
  $nTotalHa_last = $nTotal_last / $totalHa_last;
  $pTotalHa_last = $pTotal_last / $totalHa_last;
  $kTotalHa_last = $kTotal_last / $totalHa_last;

  $nTotal_last = number_format($nTotal_last, 0, ',', ' ');
  $pTotal_last = number_format($pTotal_last, 0, ',', ' ');
  $kTotal_last = number_format($kTotal_last, 0, ',', ' ');

  $nTotalHa_last = number_format($nTotalHa_last, 0, ',', ' ');
  $pTotalHa_last = number_format($pTotalHa_last, 0, ',', ' ');
  $kTotalHa_last = number_format($kTotalHa_last, 0, ',', ' ');

  echo "<table class='table table-striped'>";
  echo "<tr><td></td><td colspan='2' class='border'>".$currentyear."</td><td colspan='2'>".$lastyear."</td></tr>";
  echo "<tr><td>Total ha</td><td colspan='2' class='border'>".$totalHa_current." ha</td><td colspan='2'>".$totalHa_last." ha</td></tr>";
  echo "<tr class='title'><td>Tápanyag</td><td>kg<br><i>összes</i></td><td class='border'>kg<br><i>per ha</i></td><td>kg<br><i>összes</i></td><td>kg<br><i>per ha</i></td></tr>";
  echo "<tr><td>Nitrogén</td><td>".$nTotal_current."</td><td class='border'>".$nTotalHa_current."</td><td>".$nTotal_last."</td><td>".$nTotalHa_last."</td></tr>";
  echo "<tr><td>Foszfor</td><td>".$pTotal_current."</td><td class='border'>".$pTotalHa_current."</td><td>".$pTotal_last."</td><td>".$pTotalHa_last."</td></tr>";
  echo "<tr><td>Kálium</td><td>".$kTotal_current."</td><td class='border'>".$kTotalHa_current."</td><td>".$kTotal_last."</td><td>".$kTotalHa_last."</td></tr>";
  echo "</table>";
  echo "</div></div></div><br>";


/////////////////////////////////////////////////////
///////////////////
/////// Show fields from past
  echo '<div class="row"><div class="col-md-12 hidden-print">';
  echo '<div class="panel panel-open">';
  echo '<div class="panel-heading"><h4><span class="glyphicon glyphicon-time"></span>&nbsp;&nbsp; Történet</h4></div>';
  echo "<table class='table table-striped centertext'>";
  echo "<tr class='title'><td style='text-align:left'>Terület</td><td>ha</td><td>Vetve</td><td>Kész</td><td>Hónapok</td><td>Keverék</td><td class='border'>kg/ha</td><td>Műtragya<br><i>EFt/ha</i></td><td>Permet<br><i>EFt/ha</i></td><td>Összes<br><i>EFt/ha</i></td><td class='border'>Összes<br><i>EFt</i></td><td>N<br><i>kg/ha</i></td><td>P<br><i>kg/ha</i></td><td>K<br><i>kg/ha</i></td></tr>";

  $currentyear = date("Y");
  for ($j=0; $j < 10; $j++) { 
    $year = $currentyear - $j;
    $startdate = $year."-01-01 00:00:00";
    $enddate = $year."-12-31 23:59:59";

    //get data of fields from database
    $query = $db->prepare("SELECT * FROM fields WHERE `start` >= :startdate AND `start` < :enddate AND`complete` = 1 AND`cancel` = 0 ORDER BY `start` DESC");
    $query->bindParam(":startdate", $startdate, PDO::PARAM_STR);
    $query->bindParam(":enddate", $enddate, PDO::PARAM_STR);
    $query->execute();

    $count = $query->rowCount();
    if ($count > 0) {
      echo '<tr><td style="text-align: left; padding-top: 20px;"><b>'.$year.'</b></td><td colspan="13"></td></tr>';

      foreach ($query as $row) {
        //get variables
        $id = $row['id'];
        $name = $row['name'];
        $size = $row['size'];
        $displaysize = number_format($size, 1, ",", ".");
        $seed = $row['seed'];
        $amount = $row['amount'];
        $start = $row['start'];
        $end = $row['end'];

        if ($end == "0000-00-00") {
          $end_disp = "-";
        }
        else {
          $end_disp = $end;
        }

        $start_date = strtotime($start); 
        $end_date = strtotime($end); 
        $months = ($end_date - $start_date)/60/60/24/30;  
        $months_disp = number_format($months, 1, ',', ' ');
      
        $query = $db->prepare("SELECT * FROM seed WHERE `id` = :id");
        $query->bindParam(":id", $seed, PDO::PARAM_STR);
        $query->execute();
        $result = $query->fetch(PDO::FETCH_OBJ);
        $seedname = $result->name;

        echo "<tr><td style='text-align:left'><a href='https://turfgrass.site/fields.php?field=".$id."'>".$name."</a></td>";
        echo "<td>".$displaysize."</td>";
        echo "<td>".$start."</td>";
        echo "<td>".$end_disp."</td>";
        echo "<td>".$months_disp."</td>";
        echo "<td>".$seedname."</td>";
        echo "<td class='border'>".$amount."</td>";

        //Calculate costs
        $costoverallFertilizer = 0;
        $costoverallPesticide= 0;

        //get data of agent
        $query = $db->prepare("SELECT * FROM agents");
        $query->execute();

        $nTotal = 0;
        $pTotal = 0;
        $kTotal = 0;
        $nTotalHa = 0;
        $pTotalHa = 0;
        $kTotalHa = 0;

        foreach ($query as $row) {
          $agent = $row['id'];
          $price = $row['price'];
          $type = $row['type'];
          $n = $row['n'];
          $p = $row['p'];
          $k = $row['k'];

          //check if agent was applied   
          $query = $db->prepare("SELECT * FROM operations WHERE `field` = :fieldid AND `agent` = :agent AND `delete` = 0");
          $query->bindParam(":fieldid", $id, PDO::PARAM_STR);
          $query->bindParam(":agent", $agent, PDO::PARAM_STR);
          $query->execute();

          if ($query->rowCount() > 0) {
            $totalsum = 0;
            $haSum = 0;

            foreach ($query as $row) {
              $totalsum += $row['total'];
              $haSum += $row['amount'];
            }
            $costsum = $totalsum * $price;

            if ($type == 1) {
              $costoverallFertilizer += $costsum;

              //Nährstoffbilanz
              $nTotal += $totalsum * $n / 100;
              $pTotal += $totalsum * $p / 100;
              $kTotal += $totalsum * $k / 100;
              $nTotalHa += $haSum * $n / 100;
              $pTotalHa += $haSum * $p / 100;
              $kTotalHa += $haSum * $k / 100;
            }

            if ($type == 2) {
              $costoverallPesticide += $costsum;
            }
          }
        }

        $costoverallFertilizerSum += $costoverallFertilizer;
        $costoverallPesticideSum += $costoverallPesticide;
        $nTotalSum += $nTotal;
        $pTotalSum += $pTotal;
        $kTotalSum += $kTotal;

        $costhaFert = number_format($costoverallFertilizer / $size / 1000, 0, ',', ' ');
        $costhaPest = number_format($costoverallPesticide / $size / 1000, 0, ',', ' '); 
        $costoverallboth = $costoverallPesticide + $costoverallFertilizer;
        $costhaOverall = number_format($costoverallboth / $size / 1000, 0, ',', ' ');
        $costoverallboth = number_format($costoverallboth / 1000, 0, ',', ' ');
        $nTotalHa = number_format($nTotalHa, 0, ',', ' ');
        $pTotalHa = number_format($pTotalHa, 0, ',', ' ');
        $kTotalHa = number_format($kTotalHa, 0, ',', ' ');

        echo "<td><i>".$costhaFert."</i></td>";
        echo "<td><i>".$costhaPest."</i></td>";
        echo "<td><b>".$costhaOverall."</b></td>";
        echo "<td class='border'>".$costoverallboth."</td>";
        echo "<td>".$nTotalHa."</td>";
        echo "<td>".$pTotalHa."</td>";
        echo "<td>".$kTotalHa."</td></tr>";

      }
    }

  }
  
  echo "</table>";
  echo "</div></div></div></div><br>";

}
else {
  //////////
  //get data of field from database
  $query = $db->prepare("SELECT * FROM fields WHERE `id` = :id");
  $query->bindParam(":id", $id, PDO::PARAM_STR);
  $query->execute();

  $result = $query->fetch(PDO::FETCH_OBJ);

  //get variables
  $name = $result->name;
  $fieldname = $result->name;
  $start = $result->start;
  $size = $result->size;
  $displaysize = number_format($size, 1, ",", ".");
  $seed = $result->seed;
  $amount = $result->amount;
  $complete = 1 - $result->complete;
  $complete2 = $complete * 100;
  $cutting = $result->cutting;
  $last = $result->last;
  $year_start = substr($start, 0, 4);

  $currentsize = $size * $complete;
  $displaycurrentsize = number_format($currentsize, 1, ",", ".");

  echo '<div class="inputform"><div class="row"><div class="col-xs-12 col-md-4">';
  
  if (isset($_GET['oldfield'])) { 
    echo "<h3 style='padding-top: 10px; color:red'><i><span class='glyphicon glyphicon-unchecked'></span>&nbsp;&nbsp;".$name." (".$year_start.")</i></h3></div>";
  }
  else {
    echo "<h3 style='padding-top: 10px;'><span class='glyphicon glyphicon-unchecked'></span>&nbsp;&nbsp;".$name."</h3></div>";
  }   

  // show details about size
  echo '<div class="col-xs-6 col-md-2 property" style="padding-left: 0px;"><table class="table">';
  echo '<tr><td style="padding:3px; border-top: none;"><b>Vetve</b></td><td style="padding:3px; border-top: none; style="padding-left: 0px;"">'.$displaysize." ha</td></tr>";
  //echo '<tr><td style="padding:3px; border-top: none;"><b>– Eladva</b></td><td style="padding:3px; border-top: none; style="padding-left: 0px;"">'.$total_disp." ha</td></tr>";
  
  $query2 = $db->prepare("SELECT * FROM `progress` WHERE `field` = :field ORDER BY `datum` DESC LIMIT 1");
  $query2->bindParam(":field", $id, PDO::PARAM_STR);
  $query2->execute(); 

  $count = $query2->rowCount();
  if ($count > 0) {

    foreach ($query2 as $row) {
        $datum = $row['datum'];
        $complete_last = $row['complete'];
    }
    $size_last = $size * (1 - $complete_last);
    $show_size = round($size_last, 1);

    $text1 = substr($datum, 5, 5);;
    $text2 = number_format($size_last, 1, ",", ".")." ha";
  }
  else {
    $text1 = "-";
    $text2 = "-";
    $size_last = $size;
    $show_size = round($size_last, 1);
    $datum = $start;
  }
  echo '<tr><td style="padding:3px; border-top: none;"><b>'.$text1.'</b></td><td style="padding:3px; border-top: none; style="padding-left: 0px;""><b>'.$text2."</b></td></tr>";

  // get all harvesting since last inventory ($datum)
  $currentdate = date("Y-m-d H:i:s");

  $query = $db->prepare("SELECT * FROM `order` WHERE `field` = :field AND `date` < :end AND `date` > :start AND (`status` = 4 OR `status` = 3) ORDER BY `date` DESC");
  $query->bindParam(":field", $id, PDO::PARAM_STR);
  $query->bindParam(":end", $currentdate, PDO::PARAM_STR);
  $query->bindParam(":start", $datum, PDO::PARAM_STR);
  $query->execute(); 

  $count2 = $query->rowCount();
  if ($count2 > 0) {
    foreach ($query as $row) {
          $amount = amount_decrypt($row['amount'], $key2);
          $total += $amount;
      }

    $current_size = $size_last - ($total / 10000);
    $percentage = ($current_size / $size) * 100;

    $text3 = "<i>~ ".number_format($current_size, 1, ",", ".")." ha (".number_format($percentage, 0, ",", ".")."%)</i>";
  }
  else {
    $percentage = ($show_size / $size) * 100;
    $text3 = "<i>".number_format($show_size, 1, ",", "."). " ha ( ".number_format($percentage, 0, ",", ".")."%)</i>";
  }

  echo '<tr><td style="padding:6px;"><b>Aktuális</b></td><td style="padding:6px; style="padding-left: 0px;"">'.$text3."</td></tr>";

  echo '<tr>';
  //echo '<td style="padding-top:7px; border-top: none; padding-left: 0px;"><select id="complete_field" onchange="updateSize('.$id.')"><option value="'.$complete.'" selected>'.$complete2.'%';
  //echo '<option value="1">100%</option><option value="0.9">90%</option><option value="0.8">80%</option><option value="0.7">70%</option><option value="0.6">60%</option><option value="0.5">50%</option><option value="0.4">40%</option><option value="0.3">30%</option><option value="0.2">20%</option><option value="0.1">10%</option><option value="0">0%</option></select></td>';
  
  echo '<td style="border-top: none;"></td><td style="padding-top:7px; border-top: none; padding-left: 0px;"><input type="number" id="remaining_size" onchange="updateSize('.$id.')" style="width: 60px;" step="0.1" value="'.$show_size.'" min="0" max="'.$size_last.'"></td>';
  //echo '<td style="border-top: none;"><b><i>'.$displaycurrentsize.' ha</i></b></td></tr>';
  echo "</table>";

  echo '</div>';

  echo '<div class="col-xs-6 col-md-2 property"><span class="glyphicon glyphicon-time"></span>&nbsp;&nbsp;'.$start."<br>"; 
  echo '<span class="glyphicon glyphicon-scale"></span>&nbsp;&nbsp;'.$amount." kg/ha<br><br>";

  if ($cutting == 0) {
    echo '<div class="checkbox"><label><input type="checkbox" id="startCutting" onClick="startCutting('.$id.')"> Vágás kezdete</label></div></div>';  
  }
  else {
    echo '<div class="checkbox"><label><input type="checkbox" id="startCutting" onClick="startCutting('.$id.')" checked> Vágás kezdete</label></div></div>';  

  }

  $query2 = $db->prepare("SELECT * FROM seed WHERE `id` = :id");
  $query2->bindParam(":id", $seed, PDO::PARAM_STR);
  $query2->execute();

  $result2 = $query2->fetch(PDO::FETCH_OBJ);
  $seedname = $result2->name;
  $x1 = $result2->x1;
  $x2 = $result2->x2;
  $x3 = $result2->x3;
  $x4 = $result2->x4;

  echo '<div class="col-xs-6 col-md-2 property"><span class="glyphicon glyphicon-grain"></span>&nbsp;&nbsp;<b>'.$seedname."</b><br>";
  echo "&nbsp;&nbsp;– ".$x1;
  if (!empty($x2)) { 
    echo "<br>&nbsp;&nbsp;– ".$x2;
  }
  if (!empty($x3)) { 
    echo "<br>&nbsp;&nbsp;– ".$x3;
  }
  if (!empty($x4)) { 
    echo "<br>&nbsp;&nbsp;– ".$x4;
  }
  echo "</div>";
  echo '<div class="col-xs-6 col-md-2 hidden-print" style="padding-bottom: 5px;">';

  if (isset($_GET['oldfield'])) { 
    //get back to present field (link) 
    $return_field = $_GET['oldfield'];
    echo '<a href="https://turfgrass.site/fields.php?field='.$return_field.'"><button type="button" class="btn btn-default" style="float:right; margin-bottom:10px;">Vissza</button></a><br>';
  }
  else {
    //get operations of field in past (link)   
    if ($last > 0) {
      $query = $db->prepare("SELECT * FROM fields WHERE `id` = :id");
      $query->bindParam(":id", $last, PDO::PARAM_STR);
      $query->execute();
      $result = $query->fetch(PDO::FETCH_OBJ);
      $year_past = substr($result->start, 0, 4);

      echo '<a href="https://turfgrass.site/fields.php?field='.$last.'&oldfield='.$id.'"><button type="button" class="btn btn-default" style="float:right; margin-bottom:10px;"><span class="glyphicon glyphicon-time"></span>&nbsp;&nbsp; '.$year_past.'</button></a><br>';
    }
  }
  echo '<button type="button" class="btn btn-primary btn-lg" data-toggle="modal" data-target="#planModal" style="float:right;"><span class="glyphicon glyphicon-plus"></span>&nbsp;&nbsp; Új terv</button>';
  echo "</div></div>";


  //////////////////////////////////////////////////
  ////////////
  // Show plan on this field
  echo '<div class="row"><div class="col-md-9">';
  echo '<div class="panel panel-open hidden-print">';
  echo '<div class="panel-heading"><h4><span class="glyphicon glyphicon-list-alt"></span>&nbsp;&nbsp; Tervezett munka</h4></div>';

  echo '<div class="field">';

  echo "<table class='table table-striped'>";

  $costsum = 0;

  //get plans of the field     
  $query = $db->prepare("SELECT * FROM plan WHERE `field` = :fieldid AND `complete` = 0 AND `delete` = 0 ORDER BY `week` ASC");
  $query->bindParam(":fieldid", $id, PDO::PARAM_STR);
  $query->execute();

  if ($query->rowCount() > 0) {
    echo "<tr class='title'><td>Dátum</td><td>Termék</td><td><span class='glyphicon glyphicon-scale'></span><br><i>per ha</i></td><td><span class='glyphicon glyphicon-comment'></span></td><td>Szerző</td><td>Szerkesztés</td></tr>";

    foreach ($query as $row) {
      $planid = $row['id'];
      $week = $row['week'];
      $agent = $row['agent'];
      $amount = $row['amount'];
      //$assigneduser = $row['user'];
      //$initialuser = $row['initialuser'];
      $creator = $row['creator'];
      $note = $row['note'];
      $oldnote = $row['note'];

      $thisweek = date('W');
      if ($week == $thisweek) {
        $weekdisplay = "Ezen a héten";
      }
      elseif ($week == ($thisweek + 1)) {
        $weekdisplay = "Jövő héten";
      }
      elseif ($week == ($thisweek - 1)) {
        $weekdisplay = "Múlt héten";
      }
      else {
        $weekdisplay = $week.". héten";
      }
      
      echo "<tr><td>".$weekdisplay."</td>";
      
      //get name of agent
      $query = $db->prepare("SELECT * FROM agents WHERE `id` = :id");
      $query->bindParam(":id", $agent, PDO::PARAM_STR);
      $query->execute();
      $result = $query->fetch(PDO::FETCH_OBJ);
      $agentname = $result->name;
      echo "<td><a href='https://turfgrass.site/agent.php?id=".$agent."'>".$result->name."</a></td>";

      echo "<td>".$amount."</td>";

      /*
      $query = $db->prepare("SELECT * FROM users WHERE `id` = :id");
      $query->bindParam(":id", $assigneduser, PDO::PARAM_STR);
      $query->execute();
      $result = $query->fetch(PDO::FETCH_OBJ);
      echo "<td>".$result->username."</td>";
      */
      
      echo "<td>".$note."</td>";
      
      $query = $db->prepare("SELECT * FROM users WHERE `id` = :id");
      $query->bindParam(":id", $creator, PDO::PARAM_STR);
      $query->execute();
      $result = $query->fetch(PDO::FETCH_OBJ);
      echo "<td>".$result->username."</td>";

      echo '<td><button type="button" class="btn btn-default btn-xs" data-toggle="modal" data-target="#editPlanModal'.$i.'" data-id="'.$id.'"><span class="glyphicon glyphicon-pencil"></span></button>&nbsp;&nbsp';
      echo '<button type="button" class="btn btn-danger btn-xs" data-toggle="modal" data-target="#deletePlanModal'.$i.'" data-id="'.$id.'"><span class="glyphicon glyphicon-remove"></span></button></td></tr>';
      ?>

      <!-- MODAL -->
      <!-- Edit entry -->
      <div class="modal fade" id="editPlanModal<?php echo $i; ?>" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document" style="width: 700px;">
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
              <h4 class="modal-title">Szerkesztés</h4>
            </div>

            <form method="post" action="fields.php?field=<?echo $id;?>" accept-charset="utf-8">
            <br><label style="margin-left: 15px;" for="date">Dátum</label>
            <div class="row">
              <div class="col-md-5">
                <?php
                if ($week == $thisweek) {
                  echo '<input style="margin-left: 15px; margin-bottom: 15px;" type="checkbox" name="week" value="1" checked> Ezen a héten<br>';
                  echo '<input style="margin-left: 15px;"type="checkbox" name="week" value="2"> Jövő héten<br>';
                }
                elseif ($week == ($thisweek + 1)) {
                  echo '<input style="margin-left: 15px; margin-bottom: 15px;" type="checkbox" name="week" value="1"> Ezen a héten<br>';
                  echo '<input style="margin-left: 15px;"type="checkbox" name="week" value="2" checked> Jövő héten<br>';
                }
                else {
                  echo '<input style="margin-left: 15px; margin-bottom: 15px;" type="checkbox" name="week" value="1"> Ezen a héten<br>';
                  echo '<input style="margin-left: 15px;"type="checkbox" name="week" value="2"> Jövő héten<br>';
                }
                ?>
              </div>
              <div class="col-md-1"></div> 
              <div class="col-md-5">
                  <input type="date" class="form-control"  tyle="padding-top: 0;" name="date" value="<?echo date("Y-m-d");?>">
              </div>
            </div>

            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                <!-- fertilizers plus pesticides -->                            
                <label for="field">Termék</label>
                  <select class="form-control" style="width: 70%;" name="agent">
                    <option value="<?echo $agent;?>" selected><?echo $agentname;?></option>
                    <?php  
                    $query = $db->prepare("SELECT * FROM agents WHERE `stock` > 0");
                    $query->execute();
                    while($row = $query->fetch()) {
                      echo "<option value='".$row['id']."'>".$row['name']."</option>";
                    }
                    ?>
                  </select>
                </div>
              </div>  

              <div class="col-md-3">
                <div class="form-group">                            
                  <label for="field">Mennyiség <i>per ha</i></label>
                  <input class="form-control" style="width: 70%;" type="number" step=".1" min="0" value="<?echo $amount;?>" name="amount">
                </div>
              </div>  
            </div>
               
            <div class="row">
              <div class="col-md-12 formrow">
                <div class="form-group">
                  <label for="exampleTextarea">Jegyzet</label>
                  <textarea class="form-control" name="note" rows="3" placeholder="<?echo $note;?>"></textarea>
                </div>
              </div>
            </div>
                
            <input type="hidden" name="oldnote" value="<?echo $oldnote;?>">  
            <input type="hidden" name="id" value="<?echo $planid;?>"> 

            <div class="modal-footer">
              <button type="submit" class="btn btn-primary center-block" name="editPlanForm" value="Submit">Küldés</button>
            </div>
            </form>
          </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
      </div><!-- /.modal -->

      <!-- Delete entry -->
      <div class="modal fade" id="deletePlanModal<?php echo $i; ?>" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document" style="width: 300px;">
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
              <h4 class="modal-title">Töröl</h4>
            </div>

            <div class="row">
              <div class="col-md-12" style ="text-align: center; padding-top: 20px;">
                 <b>Biztos?</b>
              </div>
              <form method="post" action="fields.php?field=<?echo $id;?>" accept-charset="utf-8">  
                <input type="hidden" name="id" value="<?echo $planid;?>">  

                <div class="row">
                  <div class="col-md-3"></div>
                  <div class="col-md-3"><button type="submit" class="btn btn-primary" name="deletePlanForm" value="Submit">Igen</button></div></form>
                  <div class="col-md-3"><button class="btn btn-danger" data-dismiss="modal">Nem</button></div>
                </div>
              </form>
            </div>
          </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
      </div><!-- /.modal -->
<?php
      $i++;
    }
  }

  else {
    echo "<tr><td>Nincs tervezett munka</tr>";
  }

  echo "</table>";
  echo "</div></div>";


  //////////////////////////////////////////////////
  ////////////
  // Show operations on this field
    //check login
  if (isset($_COOKIE['login'])) {
      $login = $_COOKIE["login"];
  }
  elseif (isset($_SESSION['login'])) {
      $login = $_SESSION['login'];
  }

  echo '<div class="panel panel-open">';
  echo '<div class="panel-heading"><h4><span class="glyphicon glyphicon-list-alt"></span>&nbsp;&nbsp; Munkák</h4></div>';

  echo '<div class="field">';

  echo "<table class='table table-striped'>";

  $mobile = 0;
  $browserAsString = $_SERVER['HTTP_USER_AGENT'];
  if (strstr($browserAsString, " AppleWebKit/") && strstr($browserAsString, " Mobile/")) {
    $mobile = 1;
  }

  if ($mobile == 1) {
    echo "<tr class='title'><td>Dátum</td><td>Termék</td><td><span class='glyphicon glyphicon-scale'></span></td><td style='min-width: 7rem'>Kész %</td><td><span class='glyphicon glyphicon-user'></span></td>";
  }
  else {
    echo "<tr class='title'><td>Dátum</td><td>Termék</td><td><span class='glyphicon glyphicon-scale'></span><br><i>per ha</i></td><td style='min-width: 7rem'>Kész %</td><td><span class='glyphicon glyphicon-scale'></span><br><i>összes</i></td><td>EFt<br><i>összes</i></td><td class='hidden-print'>Terv</td><td><span class='glyphicon glyphicon-user'></span></td><td style='text-align: center; width: 100px'><span class='glyphicon glyphicon-comment'></span></td>";
  }

  if ($login == 1) {
    echo "<td class='hidden-print'></td>";
  }

  echo "</tr>";

  $costsum = 0;

  //get operations of the field     
  $query = $db->prepare("SELECT * FROM operations WHERE `field` = :fieldid AND `delete` = 0 ORDER BY `datum` DESC");
  $query->bindParam(":fieldid", $id, PDO::PARAM_STR);
  $query->execute();

  foreach ($query as $row) {
    $operationid = $row['id'];
    $datum = $row['datum'];
    $agent = $row['agent'];
    $amount = $row['amount'];
    $total = $row['total'];
    $small = $row['small'];
    $complete = $row['complete'];

    $complete_disp = $complete * 100;

    if ($row['note']=="0") {
      $note = "";
    }
    else {
      $note = $row['note'];
    }

    if ($small == 1) {
      $ha = $total / $amount;
      $hadisplay = number_format($ha, 1, ',', ' ');
      $complete_work = "Rész<br>".$hadisplay." ha";
    }
    else {
      $complete_nr = round($row['complete'] * 100, 1);
      $complete_work = $complete_nr."%";
    }

    $oldnote = $note;
    $oldtotal = $total;

    $query = $db->prepare("SELECT * FROM users WHERE `id` = :id");
    $query->bindParam(":id", $row['user'], PDO::PARAM_STR);
    $query->execute();
    $result = $query->fetch(PDO::FETCH_OBJ);
    $user = $result->username;
    
    if ($row['plan']==0) {
      $plan = "";
    }
    else {
      $plan = "<span class='glyphicon glyphicon-ok'></span>";
    }

    echo "<tr><td>".$datum."</td>";

    //get name of agent
    $query = $db->prepare("SELECT * FROM agents WHERE `id` = :id");
    $query->bindParam(":id", $agent, PDO::PARAM_STR);
    $query->execute();
    $result = $query->fetch(PDO::FETCH_OBJ);
    $agentname = $result->name;

    echo "<td><a href='https://turfgrass.site/agent.php?id=".$agent."'>".$result->name."</a></td>";

    $cost = $result->price * $total;
    $costdisplay = number_format($cost / 1000, 0, ',', ' ');

    echo "<td>".$amount."</td>";

    echo "<td><i>".$complete_work."</i></td>";
    if ($mobile == 0) {
      echo "<td>".$total."</td>";
      echo "<td>".$costdisplay."</td>";
      echo "<td class='hidden-print'>".$plan."</td>";
      echo "<td>".$user."</td>";
      echo "<td>".$note."</td>";
    }
    else {
      echo "<td>".$user."<br>";
      echo "<i>".$note."</i></td>";
    }

    if ($login == 1) {
      echo '<td class="hidden-print"><button type="button" class="btn btn-default btn-xs" data-toggle="modal" data-target="#editModal'.$i.'" data-id="'.$id.'"><span class="glyphicon glyphicon-pencil"></span></button>&nbsp;&nbsp';
      
      if ($mobile == 1) {
        echo "";
      }
      else {
        echo '<button type="button" class="btn btn-danger btn-xs" data-toggle="modal" data-target="#deleteModal'.$i.'" data-id="'.$id.'"><span class="glyphicon glyphicon-remove"></span></button></td>';
      }
    }
    
    echo '</tr>';    

    $costsum = $costsum + $cost;
?>
    <!-- MODAL -->
    <!-- Edit entry -->
    <div class="modal fade" id="editModal<?php echo $i; ?>" tabindex="-1" role="dialog">
      <div class="modal-dialog" role="document" style="width: 700px;">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">Szerkesztés</h4>
          </div>

          <div class="row">
            <div class="col-md-6"><br>
            <form method="post" action="fields.php?field=<?echo $id;?>" accept-charset="utf-8">
              <label for="date">Dátum</label>
                <input type="date" class="form-control" style="padding-top: 0;" name="date" value="<?echo $datum;?>">
            </div>

            <div class="col-md-6"><br>
              <div class="form-group">
                <label for="field">Terület</label>
                <select class="form-control" name="field" style="width: 70%;">
                  <option value="<?echo $field;?>" selected><?echo $fieldname;?></option>
                  <?php  
                  $query = $db->prepare("SELECT * FROM fields WHERE `complete` = 0");
                  $query->execute();
                  while($row = $query->fetch()) {
                      echo "<option value='".$row['id']."'>".$row['name']."</option>";
                  }
                  ?>
                </select>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
              <!-- fertilizers plus pesticides -->                            
              <label for="field">Termék</label>
                <select class="form-control" style="width: 70%;" name="agent">
                  <option value="<?echo $agent;?>" selected><?echo $agentname;?></option>
                  <?php  
                  $query = $db->prepare("SELECT * FROM agents WHERE `stock` > 0");
                  $query->execute();
                  while($row = $query->fetch()) {
                    echo "<option value='".$row['id']."'>".$row['name']."</option>";
                  }
                  ?>
                </select>
              </div>
            </div>  

            <div class="col-md-3">
                <div class="form-group">                            
                <label for="field">Mennyiség <i>per ha</i></label>
                  <input class="form-control" style="width: 70%;" type="number" step=".1" min="0" value="<?echo $amount;?>" name="amount">
                </div>
                <br>
                <div class="form-group">                            
                <label for="field">Kész %</label>
                  <input class="form-control" style="width: 70%;" type="number" step="1" min="0" max="100" value="<?echo $complete_disp;?>" name="complete">
                </div>
            </div>  
            <div class="col-md-3">
              <div class="form-group">                          
              <label for="field">Mennyiség <i>összes</i></label>
                <input class="form-control" style="width: 70%;" type="number" step=".1" min="0"  value="<?echo $total;?>" name="total">
              </div>
              <br>
              <div class="checkbox">
                <label>
                  <?php
                  if ($small == 0) {
                    echo '<input type="checkbox" name="small"> Részmunka';
                  }
                  elseif ($small == 1) {
                    echo '<input type="checkbox" name="small" checked> Részmunka';
                  }
                  ?>
                </label>
              </div>
            </div>  
          </div>
             
          <div class="row">
            <div class="col-md-12 formrow">
              <div class="form-group">
                <label for="exampleTextarea">Jegyzet</label>
                <textarea class="form-control" name="note" rows="3" placeholder=""><?echo $note;?></textarea>
              </div>
            </div>
          </div>
              
          <input type="hidden" name="oldnote" value="<?echo $oldnote;?>">  
          <input type="hidden" name="oldtotal" value="<?echo $oldtotal;?>"> 
          <input type="hidden" name="id" value="<?echo $operationid;?>"> 

          <div class="modal-footer">
            <button type="submit" class="btn btn-primary center-block" name="editForm" value="Submit">Küldés</button>
          </div>
          </form>
        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

    <!-- Delete entry -->
    <div class="modal fade" id="deleteModal<?php echo $i; ?>" tabindex="-1" role="dialog">
      <div class="modal-dialog" role="document" style="width: 300px;">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">Törlés</h4>
          </div>

          <div class="row">
            <div class="col-md-12" style ="text-align: center; padding-top: 20px;">
               <b>Biztos?</b>
            </div>
            <form method="post" action="fields.php?field=<?echo $id;?>" accept-charset="utf-8">  
              <input type="hidden" name="id" value="<?echo $operationid;?>"> 
              <input type="hidden" name="oldtotal" value="<?echo $oldtotal;?>">  
              <input type="hidden" name="agent" value="<?echo $agent;?>">

              <div class="row">
                <div class="col-md-3"></div>
                <div class="col-md-3"><button type="submit" class="btn btn-primary" name="deleteForm" value="Submit">Igen</button></div></form>
                <div class="col-md-3"><button class="btn btn-danger" data-dismiss="modal">Nem</button></div>
              </div>
            </form>
          </div>

        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
<?php

  $i++;
  }

  $costsum = number_format($costsum / 1000, 0, ',', ' ');
  if ($mobile == 1) {
    echo "";
  }
  else {
    echo "<tr class ='sum' style='background-color: #e1e1e1;'><td>Összes</td><td></td><td></td><td></td><td colspan='2'>".$costsum." EFt</td><td colspan='4'></td>";
    
    if ($userid == 1 OR $userid == 3) {
      echo "<td class='hidden-print'></td>";
    }

    echo "</tr>";
  }
  
  echo "</table>";
  echo "</div></div></div>";


  //////////////////////////////////////////////////
  ////////////
  // Show nutrition on this field
  echo '<div class="col-md-3 hidden-print">';
  echo '<div class="panel panel-open">';
  echo '<div class="panel-heading"><h4><span class="glyphicon glyphicon-filter"></span>&nbsp;&nbsp; Tápanyagi mérleg</h4></div>';

  echo '<div class="field">';

  //get data of agent
  $query = $db->prepare("SELECT * FROM agents");
  $query->execute();

  $nTotal = 0;
  $pTotal = 0;
  $kTotal = 0;
  $nTotalHa = 0;
  $pTotalHa = 0;
  $kTotalHa = 0;

  foreach ($query as $row) {
    $agent = $row['id'];
    $type = $row['type'];
    $n = $row['n'];
    $p = $row['p'];
    $k = $row['k'];

    //check if agent was applied   
    $query = $db->prepare("SELECT * FROM operations WHERE `field` = :fieldid AND `agent` = :agent AND `delete` = 0");
    $query->bindParam(":fieldid", $id, PDO::PARAM_STR);
    $query->bindParam(":agent", $agent, PDO::PARAM_STR);
    $query->execute();

    if ($query->rowCount() > 0) {
      $totalsum = 0;
      $haSum = 0;

      foreach ($query as $row) {
        $totalsum += $row['total'];
        $haSum += $row['amount'];
      }

      if ($type == 1) {
        //Nährstoffbilanz
        $nTotal += $totalsum * $n / 100;
        $pTotal += $totalsum * $p / 100;
        $kTotal += $totalsum * $k / 100;
        $nTotalHa += $haSum * $n / 100;
        $pTotalHa += $haSum * $p / 100;
        $kTotalHa += $haSum * $k / 100;
      }
    }
  }

  $nTotal = number_format($nTotal, 0, ',', ' ');
  $pTotal = number_format($pTotal, 0, ',', ' ');
  $kTotal = number_format($kTotal, 0, ',', ' ');
  $nTotalHa = number_format($nTotalHa, 0, ',', ' ');
  $pTotalHa = number_format($pTotalHa, 0, ',', ' ');
  $kTotalHa = number_format($kTotalHa, 0, ',', ' ');

  echo "<table class='table table-striped'>";
  echo "<tr class='title'><td>Tápanyag</td><td>kg<br><i>összes</i></td><td>kg<br><i>per ha</i></td></tr>";
  echo "<tr><td>Nitrogén</td><td>".$nTotal."</td><td>".$nTotalHa."</td></tr>";
  echo "<tr><td>Foszfor</td><td>".$pTotal."</td><td>".$pTotalHa."</td></tr>";
  echo "<tr><td>Kálium</td><td>".$kTotal."</td><td>".$kTotalHa."</td></tr>";
  echo "</table>";
  echo "</div></div>";


  //////////////////////////////////////////////////
  ////////////
  // Show notes and pictures on this field

  //buttons for adding pictures and notes
  echo '<div class="row hidden-print" style="margin-top:40px;"><div class="col-md-6" style="padding-left:15px;">';
  echo '<button type="button" class="btn btn-primary btn" data-toggle="modal" data-target="#noteModal"><span class="glyphicon glyphicon-plus"></span>&nbsp;&nbsp; Új jegyzet</button>';
  echo "</div>";
  echo '<div class="col-md-6">';
  echo '<button type="button" class="btn btn-primary btn" data-toggle="modal" data-target="#pictureModal"><span class="glyphicon glyphicon-plus"></span>&nbsp;&nbsp; Új kép</button>';
  echo "</div></div>";

  echo '<div class="row hidden-print"><div class="col-md-12">';
  echo '<div class="panel panel-open">';
  echo '<div class="panel-heading"><h4><span class="glyphicon glyphicon-comment"></span>&nbsp;&nbsp; Jegyzetek</h4></div>';

  echo '<div class="field">';

  echo "<table class='table table-striped'>";
  //echo "<tr class='title'><td>Dátum</td><td></td></tr>";

  ///Listing notes and pictures
  $query = $db->prepare("SELECT * FROM notes WHERE `field` = :fieldid ORDER BY `date` DESC");
  $query->bindParam(":fieldid", $id, PDO::PARAM_STR);
  $query->execute();

  foreach ($query as $row) {
    //get variables
    $datum = $row['date'];
    $note = $row['note'];
    $source = $row['source'];

    $query = $db->prepare("SELECT * FROM users WHERE `id` = :id");
    $query->bindParam(":id", $row['user'], PDO::PARAM_STR);
    $query->execute();
    $result = $query->fetch(PDO::FETCH_OBJ);
    $creator = $result->username;

    echo "<tr><td>".$datum."<br><i>".$creator."</i></td>";
    
    if ($row['type'] == 1) {
      echo "<td>".$note."</td>";
    }
    if ($row['type'] == 2) {
      echo '<td><a href="img/upload/'.$source.'" data-lightbox="pictures"><img class="galerie" src="img/upload/'.$source.'" alt="link"></a></td>';
    }

    echo "</tr>";
  }

  echo "</table>";

  echo "</div></div></div></div>";
  echo "</div></div></div>";


  //////////////////////////////////////////////////
  ////////////
  // Show agents on this field
  echo '<div class="row"><div class="col-md-7">';
  echo '<div class="panel panel-open">';
  echo '<div class="panel-heading"><h4><span class="glyphicon glyphicon-filter"></span>&nbsp;&nbsp; Termékek</h4></div>';

  echo '<div class="field">';

  echo "<table class='table table-striped'>";
  echo "<tr class='title'><td>Termék</td><td>Hányszor?</td><td><span class='glyphicon glyphicon-scale'></span><br><i>per ha</i></td><td>EFt<br><i>per ha</i></td></tr>";

  $totaloverallFertilizer = 0;
  $costoverallFertilizer = 0;
  $totaloverallPesticide = 0;
  $costoverallPesticide= 0;

  //get name of agent
  $query = $db->prepare("SELECT * FROM agents");
  $query->execute();

  $fertilizertext = "";
  $pesticidetext = "";

  $nTotal = 0;
  $pTotal = 0;
  $kTotal = 0;
  $nTotalHa = 0;
  $pTotalHa = 0;
  $kTotalHa = 0;
  $iFert = 0;
  $iPest = 0;

  foreach ($query as $row) {
    $agent = $row['id'];
    $agentname = $row['name'];
    $price = $row['price'];
    $pricedisplay = number_format($price, 0, ',', ' ');
    $type = $row['type'];
    $n = $row['n'];
    $p = $row['p'];
    $k = $row['k']; 
    $i = 0;

    //check if agent was applied   
    $query = $db->prepare("SELECT * FROM operations WHERE `field` = :fieldid AND `agent` = :agent AND `small` = 0 AND `delete` = 0");
    $query->bindParam(":fieldid", $id, PDO::PARAM_STR);
    $query->bindParam(":agent", $agent, PDO::PARAM_STR);
    $query->execute();

    if ($query->rowCount() > 0) {
      $totalsum = 0;

      foreach ($query as $row) {
        $totalsum = $totalsum + $row['total'];
        $i++;
      }

      $costsum = $totalsum * $price;
      $totaldisplay = number_format($totalsum, 0, ',', ' ');
      $totalhadisplay = number_format($totalsum / $size, 0, ',', ' ');
      $costdisplay = number_format($costsum / 1000, 0, ',', ' ');
      $costha = number_format($costsum / $size / 1000, 0, ',', ' ');

      if ($type == 1) {
        //text for fertilizers
        $fertilizertext .= "<tr class='subtitle'><td>&nbsp;&nbsp;<a href='https://turfgrass.site/agent.php?id=".$agent."'>".$agentname."</a></td>";
        $fertilizertext .= "<td>&nbsp;&nbsp;<b>".$i." x</b></td>";
        $fertilizertext .= "<td>&nbsp;&nbsp;".$totalhadisplay."</td>";
        $fertilizertext .= "<td>&nbsp;&nbsp;".$costha."</td>";

        $totaloverallFertilizer = $totaloverallFertilizer + $totalsum;
        $costoverallFertilizer = $costoverallFertilizer + $costsum;
        $iFert += $i;

        //Nährstoffbilanz
        $nTotal += $totalsum * $n / 100;
        $pTotal += $totalsum * $p / 100;
        $kTotal += $totalsum * $k / 100;
        $nTotalHa += $totalsum / $size * $n / 100;
        $pTotalHa += $totalsum / $size * $p / 100;
        $kTotalHa += $totalsum / $size * $k / 100;
      }

      if ($type == 2) {
        //text for fertilizers
        $pesticidetext .= "<tr class='subtitle'><td>&nbsp;&nbsp;<a href='http://turfgrass.site/agent.php?id=".$agent."'>".$agentname."</a></td>";
        $pesticidetext .= "<td>&nbsp;&nbsp;<b>".$i." x</b></td>";
        $pesticidetext .= "<td>&nbsp;&nbsp;".$totalhadisplay."</td>";
        $pesticidetext .= "<td>&nbsp;&nbsp;".$costha."</td>";

        $totaloverallPesticide = $totaloverallPesticide + $totalsum;
        $costoverallPesticide = $costoverallPesticide + $costsum;
        $iPest += $i;
      }
    }

  }

  $totaloverall = number_format($totaloverall, 0, ',', ' ');
  $costoverall = number_format($costoverall, 0, ',', ' ');

  $totaloverallFertDisplay = number_format($totaloverallFertilizer, 0, ',', ' ');
  $totaloverallhaFertDisplay = number_format($totaloverallFertilizer / $size, 0, ',', ' ');
  $costoverallFertDisplay = number_format($costoverallFertilizer / 1000, 0, ',', ' ');
  $costhaFert = number_format($costoverallFertilizer / $size / 1000, 0, ',', ' ');

  $totaloverallPestDisplay = number_format($totaloverallPesticide, 0, ',', ' ');
  $totaloverallhaPestDisplay = number_format($totaloverallPesticide / $size, 0, ',', ' ');
  $costoverallPestDisplay = number_format($costoverallPesticide / 1000, 0, ',', ' '); 
  $costhaPest = number_format($costoverallPesticide / $size / 1000, 0, ',', ' '); 

  echo "<tr class='mainpoint'><td><b>Műtragya</b></td><td><b>".$iFert." x</b></td><td>".$totaloverallhaFertDisplay."</td><td>".$costhaFert."</td></tr>";
  echo $fertilizertext;

  echo "<tr class='mainpoint'><td><b>Permet</b></td><td><b>".$iPest." x</b></td><td>".$totaloverallhaPestDisplay."</td><td>".$costhaPest."</td></tr>";
  echo $pesticidetext;

  $totaloverallboth = $totaloverallPesticide + $totaloverallFertilizer;
  $costoverallboth = $costoverallPesticide + $costoverallFertilizer;
  $iTotal = $iPest + $iFert;
  
  $costhaOverall = number_format($costoverallboth / $size / 1000, 0, ',', ' ');
  $totaloverallbothDisplay = number_format($totaloverallboth, 0, ',', ' ');
  $totaloverallhaboth = number_format($totaloverallboth / $size, 0, ',', ' ');
  $costoverallboth = number_format($costoverallboth / 1000, 0, ',', ' ');


  echo "<tr class ='sum' style='background-color: #e1e1e1;'><td>Összes</td><td>".$iTotal." x</td><td>".$totaloverallhaboth."</td><td>".$costhaOverall." EFt</td></tr>";
  echo "</table>";
  echo "</div></div></div></div>";
  ?>  

  <div class="row hidden-print"><div class="col-md-12">
    <a href="fields.php"><button type="button" class="btn btn-secondary" style="float: right;">Vissza</button></a>
    <button type="button" class="btn btn-secondary" style="float: right; margin-right: 15px;" onclick="printPage()">Nyomtatás</button>
  </div></div>

<?php
}


////////////
//Modals
?>

<!-- Add picture -->
<div class="modal fade" id="pictureModal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Új kép</h4>
      </div>

      <form method="post" action="fields.php?field=<?echo $id;?>" accept-charset="utf-8" enctype="multipart/form-data">
      <div class="modal-body"> 
        
        <label for="date">Dátum</label>
        <input type="date" class="form-control" name="date" value="<?echo date("Y-m-d");?>">
        <br>

        <input type="hidden" name="field" value="<?echo $id;?>">
        <input type="hidden" name="type" value="2">

        <div class="form-group">
          <label for="picture">Kép</label>
          <input type="file" name="picture">
        </div><br>

        <div class="form-group">
          <label for="exampleTextarea">Jegyzet</label>
          <textarea class="form-control" name="note" rows="3"></textarea>
        </div>
        
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-default" name="inputPicture" value="Submit">Küldés</button>
      </div>
      </form>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->


<!-- Add note -->
<div class="modal fade" id="noteModal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Új jegyzet</h4>
      </div>

      <form method="post" action="fields.php?field=<?echo $id;?>" accept-charset="utf-8">
      <div class="modal-body"> 
        
        <label for="date">Dátum</label>
        <input type="date" class="form-control" name="date" value="<?echo date("Y-m-d");?>">
        <br>

        <input type="hidden" name="field" value="<?echo $id;?>">
        <input type="hidden" name="type" value="1">

        <div class="form-group">
          <label for="exampleTextarea">Jegyzet</label>
          <textarea class="form-control" name="note" rows="3"></textarea>
        </div>
        
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-default" name="inputNote" value="Submit">Küldés</button>
      </div>
      </form>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->



<!-- Add plan -->
<div class="modal fade" id="planModal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Új terv</h4>
      </div>

      <form method="post" action="fields.php?field=<?echo $id;?>" accept-charset="utf-8">
      <div class="modal-body"> 
        
        <label for="date">Dátum</label>
        <div class="row">
          <div class="col-md-5">
            <input style="margin-left: 8px; margin-bottom: 15px;" type="checkbox" name="week" value="1"> Ezen a héten<br>
            <input style="margin-left: 8px;"type="checkbox" name="week" value="2"> Jövő héten<br>
          </div>
          <div class="col-md-1"></div> 
          <div class="col-md-5">
              <input type="date" class="form-control" style="padding-top: 0;" name="date" value="<?echo date("Y-m-d");?>">
          </div>
        </div>


        <input type="hidden" name="field" value="<?echo $id;?>">
        <input type="hidden" name="user" value="0">

        <div class="form-group">
          <!-- List all fertilizers in table with checkbox and amount -->
          <table class='table'>
            <tr><td class="first"><b>Műtragya</b></td><td class="next"><b>kg/ha</b></td><td><b>Standard</b></td></tr>

            <?php
            $query = $db->prepare("SELECT * FROM `agents` WHERE type = 1 AND stock > 0 AND main = 1 ORDER BY `name` ASC");
            $query->execute();
            while($row = $query->fetch()) {
              echo '<tr><td>';
              echo $row['name'];
              echo '</td><td>';
              echo '<input class="form-control" type="number" value="0" step=".1" min="0" name="agents['.$row['id'].']"></td>';
              echo '<td><i>'.$row['standard'].'</i></td>';
              echo "</tr>";
            }
            ?>
          </table>
          <br>

          <!-- List all pesticides in table with checkbox and amount -->
          <table class='table'>
            <tr><td class="first"><b>Permet</b></td><td class="next"><b>l/ha</b></td><td><b>Standard</b></td></tr>

            <?php
            $query = $db->prepare("SELECT * FROM agents WHERE type = 2 AND stock > 0 AND main = 1 ORDER BY `name` ASC");
            $query->execute();
            while($row = $query->fetch()) {
              echo '<tr><td>';
              echo $row['name'];
              echo '</td><td>';
              echo '<input class="form-control" type="number" value="0" step=".5" min="0" name="agents['.$row['id'].']"></td>';
              echo '<td><i>'.$row['standard'].'</i></td>';
              echo "</tr>";
            }
            ?>
          </table>
          <br>

          <div class="more">
            <a class="btn btn-default" role="button" data-toggle="collapse" href="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
              Tovabbi termék
            </a>
          </div>

          <div class="collapse" id="collapseExample">
            <?php
            echo '<table class="table"><tr><td class="first"><b>Műtragya</b></td><td class="next"><b>kg/ha</b></td></td><td><b>Standard</b></td></tr>';

            $query = $db->prepare("SELECT * FROM agents WHERE type = 1 AND stock > 0 AND main = 0 ORDER BY `name`");
            $query->execute();
            while($row = $query->fetch()) {
              echo '<tr><td>';
              echo $row['name'];
              echo '</td><td>';
              echo '<input class="form-control" type="number" value="0" step=".1" min="0" name="agents['.$row['id'].']"></td>';
              echo '<td><i>'.$row['standard'].'</i></td>';
              echo "</tr>";    
            }
            echo '</table><br>';
            ?>

            <?php
            echo '<table class="table"><tr><td class="first"><b>Permet</b></td><td class="next"><b>l/ha</b></td></td><td><b>Standard</b></td></tr>';

            $query = $db->prepare("SELECT * FROM agents WHERE type = 2 AND stock > 0 AND main = 0 ORDER BY `name`");
            $query->execute();
            while($row = $query->fetch()) {
              echo '<tr><td>';
              echo $row['name'];
              echo '</td><td>';
              echo '<input class="form-control" type="number" value="0" step=".1" min="0" name="agents['.$row['id'].']"></td>';
              echo '<td><i>'.$row['standard'].'</i></td>';
              echo "</tr>";  
            }
            echo '</table>';
            ?>
          </div>
        </div>

        <div class="form-group">
          <label for="exampleTextarea">Jegyzet</label>
          <textarea class="form-control" name="note" rows="3"></textarea>
        </div>
        <br>
        
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-default" name="inputPlan" value="Submit">Küldés</button>
      </div>
      </form>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

</div>




	
