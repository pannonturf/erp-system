<?php
///////////////////////
// Add new operation //
///////////////////////

include('tools/functions.php');
require_once('config/config.php');
$db = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.'', DB_USER, DB_PASS);
$db -> exec("set names utf8");

///////////////
//If form has been sent
if (isset($_POST['inputForm']) OR isset($_POST['inputFormB'])) {

  //Get variables from form
  if (isset($_SESSION['userid'])) {
    $user = $_SESSION['userid'];
  }
  else {
    $user = $_COOKIE["userid"];
  }

  $datum = $_POST['date'];

  $field = $_POST['field'];
  $agents = $_POST['agents'];
  $oldnote = $_POST['oldnote'];
  $today = date("Y-m-d");
  $completeHa = $_POST['complete'];
  
  $query = $db->prepare("SELECT * FROM fields WHERE `id` = :field");
  $query->bindParam(":field", $field, PDO::PARAM_STR);
  $query->execute();
  $result = $query->fetch(PDO::FETCH_OBJ);
  $size = $result->size;
  //$field_complete = 1 - $result->complete;
  //$current_size = $size * $field_complete;

  // get all harvesting since last inventory ($datum)
  $query2 = $db->prepare("SELECT * FROM `progress` WHERE `field` = :field ORDER BY `datum` DESC LIMIT 1");
  $query2->bindParam(":field", $field, PDO::PARAM_STR);
  $query2->execute(); 

  $count = $query2->rowCount();
  if ($count > 0) {

    foreach ($query2 as $row) {
        $progress_datum = $row['datum'];
        $complete_last = $row['complete'];
    }
    $size_last = $size * (1 - $complete_last);
  }
  else {
    $size_last = $size;
  }

  $currentdate = date("Y-m-d H:i:s");

  $query = $db->prepare("SELECT * FROM `order` WHERE `field` = :field AND `date` < :end AND `date` > :start AND (`status` = 4 OR `status` = 3) ORDER BY `date` DESC");
  $query->bindParam(":field", $field, PDO::PARAM_STR);
  $query->bindParam(":end", $currentdate, PDO::PARAM_STR);
  $query->bindParam(":start", $progress_datum, PDO::PARAM_STR);
  $query->execute(); 

  foreach ($query as $row) {
      $amount = amount_decrypt($row['amount'], $key2);
      $total2 += $amount;
  }

  $current_size = ($size_last - ($total2 / 10000));

  $complete = 1;
  $small = 0;
  $small_multiple = 1;
  
  if ($complete_check == 0) {
    if (isset($_POST['inputFormB'])) {
      $complete = round($completeHa / $current_size, 2);
    }
    elseif ($current_size > $completeHa) {
      $small_multiple = $completeHa / $current_size;
      $small = 1;
    }
  }

  if (!empty($_POST['plan'])) {
    $plan = $_POST['plan'];
  }
  else {
    $plan = 0;
  }

  if (!empty($_POST['note'])) {
    $note = $_POST['note'];
  }
  else {
    $note = "";
  }           

  $sql = "INSERT INTO `operations` (`id`, `user`, `datum`, `field`, `agent`, `amount`, `total`, `cost`, `complete`, `note`, `created`, `plan`, `small`) VALUES (NULL, :user, :datum, :field, :agent, :amount, :total, :cost, :complete, :note, :created, :plan, :small);";
  $query = $db->prepare($sql);

  //Update plan
  $query2 = $db->prepare("UPDATE `plan` SET `complete` = :plancomplete WHERE `id` = :plan");

  //Update stock of agents
  $query3 = $db->prepare("SELECT * FROM `agents` WHERE `id` = :id");
  $query4 = $db->prepare("UPDATE `agents` SET `stock` = :stock WHERE `id` = :id");

  $sql5 = "INSERT INTO `movement` (`id`, `datum`, `agent`, `difference`, `total`, `type`, `link`, `user`) VALUES (NULL, :datum, :agent, :difference, :total, :type, :link, :user);";
  $query5 = $db->prepare($sql5);

  $query6 = $db->prepare("SELECT * FROM `operations` ORDER BY `id` DESC LIMIT 1");

  
  foreach($agents AS $agent => $array) {
    $amount = $array[1];
    //$total =  $array[2];
    $total = round($amount * $current_size * $complete * $small_multiple, 2);

    if ($small == 1) {
      $total = $amount * $completeHa;
    } 

    if ($amount > 0) {
      //get price
      $query3->bindParam(":id", $agent, PDO::PARAM_STR);
      $query3->execute();

      $result3 = $query3->fetch(PDO::FETCH_OBJ);
      $p = $result3->price;
      $cost = $p * $total;
      $x1 = $result3->stock;
      $x = $x1 - $total;

      $query->bindParam(":user", $user, PDO::PARAM_STR);  
      $query->bindParam(":datum", $datum, PDO::PARAM_STR);
      $query->bindParam(":field", $field, PDO::PARAM_STR);
      $query->bindParam(":agent", $agent, PDO::PARAM_STR);
      $query->bindParam(":amount", $amount, PDO::PARAM_STR);
      $query->bindParam(":total", $total, PDO::PARAM_STR);
      $query->bindParam(":cost", $cost, PDO::PARAM_STR);
      $query->bindParam(":complete", $complete, PDO::PARAM_STR);
      $query->bindParam(":note", $note, PDO::PARAM_STR);
      $query->bindParam(":created", $today, PDO::PARAM_STR);
      $query->bindParam(":plan", $plan, PDO::PARAM_STR);
      $query->bindParam(":small", $small, PDO::PARAM_STR);

      $query->execute();

      $type = 1;

      $query6->execute();
      $result6 = $query6->fetch(PDO::FETCH_OBJ);
      $link = $result6->id;

      $query5->bindParam(":datum", $datum, PDO::PARAM_STR);  
      $query5->bindParam(":agent", $agent, PDO::PARAM_STR); 
      $query5->bindParam(":difference", $total, PDO::PARAM_STR);
      $query5->bindParam(":total", $x, PDO::PARAM_STR);
      $query5->bindParam(":type", $type, PDO::PARAM_STR);
      $query5->bindParam(":link", $link, PDO::PARAM_STR);
      $query5->bindParam(":user", $user, PDO::PARAM_STR);

      $query5->execute();

      if ($plan > 0) {
        $plancomplete = 1;
        
        $query2->bindParam(":plan", $plan, PDO::PARAM_STR);  
        $query2->bindParam(":plancomplete", $plancomplete, PDO::PARAM_STR);

        $query2->execute();
      }

      //Update stock
      $query4->bindParam(":id", $agent, PDO::PARAM_STR);
      $query4->bindParam(":stock", $x, PDO::PARAM_STR);
      $query4->execute(); 

    }     
  }
  
  //Back to frontpage
  echo "<script type='text/javascript'> document.location = 'production.php'; </script>";

}

  
else {
  include('views/_header'.$header.'.php');

  //If field has been selected
  if (isset($_GET['field'])) {

    $fieldid = $_GET['field'];

    //Get name of field
    $query = $db->prepare("SELECT * FROM fields WHERE `id` = :id");
    $query->bindParam(":id", $fieldid, PDO::PARAM_STR);
    $query->execute();
    $result = $query->fetch(PDO::FETCH_OBJ);

    $field = $result->name;
    $size = $result->size;
    //$field_complete = 1 - $result->complete;
    //$current_size = $size * $field_complete;

    $agent = "";
    $amount = 0;
    $note = "";
    $plan = 0;
    $checked = "";
  }

  //If plan has been selected
  if (isset($_POST['inputPlan'])) {

    $fieldid = $_POST['field'];

    //Get name of field
    $query = $db->prepare("SELECT * FROM fields WHERE `id` = :id");
    $query->bindParam(":id", $fieldid, PDO::PARAM_STR);
    $query->execute();
    $result = $query->fetch(PDO::FETCH_OBJ);

    $field = $result->name;
    $size = $result->size;
    //$field_complete = 1 - $result->complete;
    //$current_size = $size * $field_complete;

    $agent = $_POST['agent'];
    $amount = $_POST['amount'];
    $plan = $_POST['id'];
    $checked = " checked";
    $oldnote = $_POST['note'];
  }

  // get all harvesting since last inventory ($datum)
  $query2 = $db->prepare("SELECT * FROM `progress` WHERE `field` = :field ORDER BY `datum` DESC LIMIT 1");
  $query2->bindParam(":field", $fieldid, PDO::PARAM_STR);
  $query2->execute(); 

  $count = $query2->rowCount();
  if ($count > 0) {

    foreach ($query2 as $row) {
        $progress_datum = $row['datum'];
        $complete_last = $row['complete'];
    }
    $size_last = $size * (1 - $complete_last);
  }
  else {
    $size_last = $size;
  }

  $currentdate = date("Y-m-d H:i:s");

  $query = $db->prepare("SELECT * FROM `order` WHERE `field` = :field AND `date` < :end AND `date` > :start AND (`status` = 4 OR `status` = 3) ORDER BY `date` DESC");
  $query->bindParam(":field", $fieldid, PDO::PARAM_STR);
  $query->bindParam(":end", $currentdate, PDO::PARAM_STR);
  $query->bindParam(":start", $progress_datum, PDO::PARAM_STR);
  $query->execute(); 

  foreach ($query as $row) {
      $amount = amount_decrypt($row['amount'], $key2);
      $total2 += $amount;
  }

  $current_size = round($size_last - ($total2 / 10000), 2);

  $sizedisplay = number_format($current_size, 2, ',', ' ');
  
?>   

  <div class="inputform">

    <div class="row">
      <div class="col-md-3">
        <img src="../img/entry.png" class="picture_add">
      </div>

      <div class="col-md-2">
      <form method="post" name="myForm" action="<?php echo $_SERVER['PHP_SELF']; ?>" accept-charset="utf-8" onsubmit="return validateForm()">
        <label for="date">Dátum</label>
        <input type="date" class="form-control" style="padding-top: 0;" name="date" value="<?echo date("Y-m-d");?>">
      </div>

       <div class="col-md-1"></div>

      <div class="col-md-3">
        <div class="form-group">
          <label for="field">Terület</label>
          <?php
          if ($plan > 0) {
            echo '<p class="form-control-static">'.$field.'</p>';
            echo '<input type="hidden" name="field" value="'.$fieldid.'">';
          }
          else {
            echo '<select class="form-control" name="field">';
            echo '<option value="'.$fieldid.'" selected>';
            echo $field;
            echo "</option>";
 
            $query = $db->prepare("SELECT * FROM fields WHERE `complete` = 0");
            $query->execute();
            while($row = $query->fetch()) {
                echo "<option value='".$row['id']."'>".$row['name']."</option>";
            }

            echo '</select>';
          } 
        ?>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-md-6">
        <div class="form-group">

          <!-- List all fertilizers in table with checkbox and amount -->
          <?php
          if ($plan > 0) {
            $query = $db->prepare("SELECT * FROM agents WHERE id = :id AND type = 1 AND `deleted` = 0");
            $query->bindParam(":id", $agent, PDO::PARAM_STR);
            $query->execute();
            $result = $query->fetch(PDO::FETCH_OBJ);

            if ($query->rowCount() > 0) {
              $value = $amount;
              echo '<table class="table"><tr><td><b>Műtragya</b></td><td class="next"><b>kg/ha</b></td></tr>';
              echo '<tr><td>';
              echo $result->name;
              echo '</td><td>';
              echo '<input class="form-control" type="number" step=".1" min="0" value="'.$value.'" name="agents['.$agent.'][1]">';
              //echo '</td><td>';
              //echo '<input class="form-control" type="number" step=".1" min="0" value="0" name="agents['.$agent.'][2]"">';
              echo "</td></tr>";

              //Show Folifert Super, if Karbamid was planned
              if ($agent == 1) {
                echo '<tr><td>';
                echo 'Folifert Super';
                echo '</td><td>';
                echo '<input class="form-control" type="number" step=".1" min="0" value="0" name="agents[6][1]">';
                //echo '</td><td>';
                //echo '<input class="form-control" type="number" step=".1" min="0" value="0" name="agents[6][2]">';
                echo "</td></tr>";
              }

              echo '</table>';
            }
          }

          else {
            echo '<table class="table"><tr><td><b>Műtragya</b></td><td class="next"><b>kg/ha</b></td></tr>';

            $query = $db->prepare("SELECT * FROM agents WHERE type = 1 AND main = 1 AND `deleted` = 0 ORDER BY `name`");
            $query->execute();
            while($row = $query->fetch()) {
              echo '<tr><td>';
              echo $row['name'];
              echo '</td><td>';
              echo '<input class="form-control" type="number" step=".1" min="0" value="0" name="agents['.$row['id'].'][1]">';
              //echo '</td><td>';
              //echo '<input class="form-control" type="number" step=".1" min="0" value="0" name="agents['.$row['id'].'][2]"">';
              echo "</td></tr>";
              
            }
            echo '</table>';
          }
          ?>
        </div>
      </div>
      
      <div class="col-md-6">
        <div class="form-group">

          <!-- List all pesticides in table with checkbox and amount -->
          <?php
          if ($plan > 0) {
            $query = $db->prepare("SELECT * FROM agents WHERE id = :id AND type = 2 AND `deleted` = 0");
            $query->bindParam(":id", $agent, PDO::PARAM_STR);
            $query->execute();
            $result = $query->fetch(PDO::FETCH_OBJ);

            if ($query->rowCount() > 0) {
 
              $value = $amount;

              echo '<table class="table"><tr><td><b>Permet</b></td><td class="next"><b>l/ha</b></td></tr>';
              echo '<tr><td>';
              echo $result->name;
              echo '</td><td>';
              echo '<input class="form-control" type="number" step=".1" min="0" value="'.$value.'" name="agents['.$row['id'].'][1]">';
              //echo '</td><td>';
              //echo '<input class="form-control" type="number" step=".1" min="0" value="0" name="agents['.$row['id'].'][2]"">';
              echo "</td></tr>";
              echo '</table>';
            } 
          }

          else {
            echo '<table class="table"><tr><td><b>Permet</b></td><td class="next"><b>l/ha</b></td></tr>';

            $query = $db->prepare("SELECT * FROM agents WHERE type = 2 AND main = 1 AND `deleted` = 0 ORDER BY `name`");
            $query->execute();
            while($row = $query->fetch()) {
              echo '<tr><td>';
              echo $row['name'];
              echo '</td><td>';
              echo '<input class="form-control" type="number" step=".1" min="0" value="0" name="agents['.$row['id'].'][1]">';
              //echo '</td><td>';
              //echo '<input class="form-control" type="number" step=".1" min="0" value="0" name="agents['.$row['id'].'][2]"">';
              echo "</td></tr>";
              
            }
            echo '</table>';
          }
          ?>

        </div>
      </div>
    </div>

    <?php
    if ($plan == 0) {
    ?>

      <div class="row">
        <div class="col-md-12">
          <div class="more">
            <a class="btn btn-default" role="button" data-toggle="collapse" href="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
              Tovabbi termék
            </a>
          </div>

          <div class="collapse" id="collapseExample">
            <div class="row">
              <div class="col-md-6">
                <?php
                echo '<table class="table"><tr><td><b>Műtragya</b></td><td class="next"><b>kg/ha</b></td></tr>';

                $query = $db->prepare("SELECT * FROM agents WHERE type = 1 AND main = 0 AND `deleted` = 0 ORDER BY `name`");
                $query->execute();
                while($row = $query->fetch()) {
                  echo '<tr><td>';
                  echo $row['name'];
                  echo '</td><td>';
                  echo '<input class="form-control" type="number" step=".1" min="0" value="0" name="agents['.$row['id'].'][1]">';
                  //echo '</td><td>';
                  //echo '<input class="form-control" type="number" step=".1" min="0" value="0" name="agents['.$row['id'].'][2]"">';
                  echo "</td></tr>";
                  
                }
                echo '</table>';
                ?>
              </div>

              <div class="col-md-6">
                <?php
                  echo '<table class="table"><tr><td><b>Permet</b></td><td class="next"><b>l/ha</b></td></tr>';

                  $query = $db->prepare("SELECT * FROM agents WHERE type = 2 AND main = 0 AND `deleted` = 0 ORDER BY `name`");
                  $query->execute();
                  while($row = $query->fetch()) {
                    echo '<tr><td>';
                    echo $row['name'];
                    echo '</td><td>';
                    echo '<input class="form-control" type="number" step=".1" min="0" value="0" name="agents['.$row['id'].'][1]">';
                    //echo '</td><td>';
                    //echo '<input class="form-control" type="number" step=".1" min="0" value="0" name="agents['.$row['id'].'][2]"">';
                    echo "</td></tr>";
                    
                  }
                  echo '</table>';
                ?>
              </div>
            </div>
          </div>
        </div>
      </div>

    <?php
    }

    if ($plan > 0) {
      echo "<br><br>";
    }
    ?>
    <br><br>
    <div class="row">
      <div class="col-md-3"></div>
      <div class="col-md-2" style="margin-left: 15px;">
        <label for="complete">Befejezett ha</label>
        <input class="form-control" id="fixinput" type="number" step=".01" min="0" max="<?echo $current_size;?>" value="0" name="complete">
        <input type="hidden" name="complete_check" id="complete_check" value="0">
      </div>
      
      <div class="col-md-2">
        <label for="complete">&nbsp;</label>
        <button type="button" class="btn btn-complete" id="changebutton" onclick="completeFunction(<?echo $current_size;?>)"><b>Egész terület (<?echo $sizedisplay;?> ha)</b></button>
      </div>
    </div>

   <br>
    <div class="row">
      <div class="col-md-3"></div>
      <div class="col-md-6 formrow" style="padding-bottom: 10px;">
        <div class="form-group">
          <label for="exampleTextarea">Jegyzet</label>
          <textarea class="form-control" name="note" rows="3" placeholder="<?echo $oldnote;?>"></textarea>
        </div>
      </div>
      <div class="col-md-3"></div>
    </div>
        
    <input type="hidden" name="plan" value="<?echo $plan;?>">
    <input type="hidden" name="oldnote" value="<?echo $oldnote;?>">

    <div class="row">
      <div class="col-md-4"></div>
      <div class="col-md-2">
        <button type="submit" class="btn btn-primary" name="inputForm" value="Submit">Munka befejezett</button>
      </div>
      <div class="col-md-2" id="optionb">
        <button type="submit" class="btn btn-primary" name="inputFormB" value="Submit">Munka még nyított</button>
      </div>
    </div>
    </form>

 </div>

  <?php 
}
  ?>