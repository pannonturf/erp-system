<?php 
/////////////////////////////////////////////
// Overview of production for inside users //
/////////////////////////////////////////////

include('views/_header'.$header.'.php');

require_once('config/config.php');
$db = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.'', DB_USER, DB_PASS);
$db -> exec("set names utf8");

//////////////
// if operation is edited
if (isset($_POST['editForm'])) {
  //Get variables from form
  $id = $_POST['id'];
  $field = $_POST['field'];
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
  $sql = "UPDATE `plan` SET `week` = :week, `field` = :field, `agent` = :agent, `amount` = :amount, `note` = :note WHERE `id` = :id";
  $query = $db->prepare($sql);

  $query->bindParam(":week", $kw, PDO::PARAM_STR);
  $query->bindParam(":field", $field, PDO::PARAM_STR);
  $query->bindParam(":agent", $agent, PDO::PARAM_STR);
  $query->bindParam(":amount", $amount, PDO::PARAM_STR);
  $query->bindParam(":note", $note, PDO::PARAM_STR);
  $query->bindParam(":id", $id, PDO::PARAM_STR);

  $query->execute();

  echo '<div class="alert alert-success center-block" role="alert">Sikerült!</div>';
}

//////////////
// if operation is deleted
if (isset($_POST['deleteForm'])) {
  if (isset($_SESSION['userid'])) {
    $user = $_SESSION['userid'];
  }
  else {
    $user = $_COOKIE["userid"];
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

?>
<div class="inputform">
  <div class="row">
    <div class="col-md-4">
      <?php
      // show link to map if mobile view
       $browserAsString = $_SERVER['HTTP_USER_AGENT'];
        if (strstr($browserAsString, " AppleWebKit/") && strstr($browserAsString, " Mobile/"))
        {
          ?>
          <button type="submit" class="btn btn-primary" style="margin: 20px 140px;" onclick="document.location='fields.php';">Terkép</button>
        <?php
        }
        else {
          echo '<h3 style="margin-top:10px;">Áttekintés</h3>';
        }
      ?>

      
    </div>

  <?php
  //////////////////
  // check if certain inventories are low

  $user = $_SESSION['username'];

  $query = $db->prepare("SELECT * FROM agents WHERE `stock` < `min` ORDER BY `name` ASC");
  $query->execute();

  $days = array("Vasárnap", "Hétfő", "Kedd", "Szerda", "Csütörtök", "Péntek", "Szombat");
  $today = date('Y-m-d');
  $thisday = date('w');
  $thisweek = date('W'); 

  if ($query->rowCount() > 0) {
   ?> 
    <div class="col-md-4">
      <div class="panel panel-danger danger">
      <div class="panel-heading"><h4><span class="glyphicon glyphicon-exclamation-sign"></span>&nbsp;&nbsp; Raktár figyelmeztetés</h4></div>

      <?php
      echo "<table class='table table-striped centertext'>";
      echo "<tr class='title'><td>Termék</td><td>Mennyiség</td></tr>";
      
      foreach ($query as $row) {
        $agentid = $row['id'];
        $agent = $row['name'];
        $stock = $row['stock'];
        $min = $row['min'];

        echo "<tr><td><a href='https://turfgrass.site/agent.php?id=".$agentid."'>".$agent."</a></td>";
        echo "<td>".$stock."</td>";
        echo "</tr>";
      }

    echo "</table>";
    echo "</div></div>";
    echo "<div class='col-md-4' style='text-align:right;'><b>Ma:</b>&nbsp;&nbsp;&nbsp;".$days[$thisday].", &nbsp;".$today."<br>("."Naptári hét: ".$thisweek.")";
    echo "</div></div>";

  }
  else {
    echo '<div class="col-md-4"></div>';
    echo "<div class='col-md-4' style='text-align:right;'><b>Ma:</b>&nbsp;&nbsp;&nbsp;".$days[$thisday].", &nbsp;".$today."<br>("."Naptári hét: ".$thisweek.")";
    echo "</div></div>";

  }
   
  ////////////////////
  ///// Check last nitrogen
  $weeks_check = 3;
  $today = date("Y-m-d");
  $firstday = date('Y-m-d', strtotime($today.' -'.$weeks_check.' weeks'));
  $count = 0;
  $nitrogen = array();

  $query = $db->prepare("SELECT * FROM fields WHERE `complete` < 0.9 ORDER BY `name` ASC");
  $query->execute();
  
  foreach ($query as $row) {
    $id = $row['id'];

    $query2 = $db->prepare("SELECT * FROM operations WHERE `field` = :fieldid AND (`agent` = 1 OR `agent` = 5) AND `delete` = 0 AND `complete` = 1 AND `small` = 0 ORDER BY `datum` DESC LIMIT 1");
    $query2->bindParam(":fieldid", $id, PDO::PARAM_STR);
    $query2->execute();

    $result2 = $query2->fetch(PDO::FETCH_OBJ);
    $last_datum = $result2->datum;
    $last_agent = $result2->agent;

    if ($last_datum < $firstday AND $last_datum != "") {
      $nitrogen[$id][1] = $last_datum;
      $nitrogen[$id][2] = $last_agent;
      $count++;
    }
  } 

  if ($count > 0) {
   
   ?> 
    <div class="row">
    <div class="col-md-6">
      <div class="panel panel-danger danger">
      <div class="panel-heading"><h4><span class="glyphicon glyphicon-exclamation-sign"></span>&nbsp;&nbsp; Nitrogén figyelmeztetés (<?php echo $weeks_check;?> hét +)</h4></div>

      <?php
      echo "<table class='table table-striped centertext'>";
      echo "<tr class='title'><td>Terület</td><td>Múlt nitrogén</td><td>Termék</td></tr>";
      
      foreach($nitrogen as $field_id => $data) {
        $datum_last = $data[1];
        $agent_last = $data[2];

        $query = $db->prepare("SELECT * FROM fields WHERE `id` = :id");
        $query->bindParam(":id", $field_id, PDO::PARAM_STR);
        $query->execute();
        $result = $query->fetch(PDO::FETCH_OBJ);
        $field_display = $result->name;

        $query = $db->prepare("SELECT * FROM agents WHERE `id` = :id");
        $query->bindParam(":id", $agent_last, PDO::PARAM_STR);
        $query->execute();
        $result = $query->fetch(PDO::FETCH_OBJ);
        $agent_display = $result->name;

        echo "<tr><td><a href='https://turfgrass.site/fields.php?field=".$field_id."'>".$field_display."</a></td>";
        echo "<td>".$datum_last."</td>";
        echo "<td>".$agent_display."</td>";
        echo "</tr>";
      }

    echo "</table>";
    echo "</div></div>"; 
  }

  ///////////////
  ///// Check last funghizid
  $weeks_check2 = 4;
  $firstday2 = date('Y-m-d', strtotime($today.' -'.$weeks_check2.' weeks'));
  $count2 = 0;
  $funghizid = array();

  $query = $db->prepare("SELECT * FROM fields WHERE `complete` < 0.9 ORDER BY `name` ASC");
  $query->execute();
  
  foreach ($query as $row) {
    $id = $row['id'];

    $query2 = $db->prepare("SELECT * FROM operations WHERE `field` = :fieldid AND (`agent` = 7 OR `agent` = 8 OR `agent` = 19 OR `agent` = 54 OR `agent` = 55 OR `agent` = 20) AND `delete` = 0 AND `complete` = 1 ORDER BY `datum` DESC LIMIT 1");
    $query2->bindParam(":fieldid", $id, PDO::PARAM_STR);
    $query2->execute();

    $result2 = $query2->fetch(PDO::FETCH_OBJ);
    $last_datum = $result2->datum;
    $last_agent = $result2->agent;

    if ($last_datum < $firstday2 AND $last_datum != "") {
      $funghizid[$id][1] = $last_datum;
      $funghizid[$id][2] = $last_agent;
      $count2++;
    }

  }

  if ($count2 > 0) {
   ?> 
    <div class="row">
    <div class="col-md-6">
      <div class="panel panel-danger danger">
      <!-- Default panel contents -->
      <div class="panel-heading"><h4><span class="glyphicon glyphicon-exclamation-sign"></span>&nbsp;&nbsp; Gombaölő figyelmeztetés (<?php echo $weeks_check2;?> hét +)</h4></div>

      <?php
      //show operations of all users that have not been completed
      echo "<table class='table table-striped centertext'>";
      echo "<tr class='title'><td>Terület</td><td>Múlt gombaölő</td><td>Termék</td></tr>";
      
      foreach($funghizid as $field_id => $data) {
        $datum_last = $data[1];
        $agent_last = $data[2];

        $query = $db->prepare("SELECT * FROM fields WHERE `id` = :id");
        $query->bindParam(":id", $field_id, PDO::PARAM_STR);
        $query->execute();
        $result = $query->fetch(PDO::FETCH_OBJ);
        $field_display = $result->name;

        $query = $db->prepare("SELECT * FROM agents WHERE `id` = :id");
        $query->bindParam(":id", $agent_last, PDO::PARAM_STR);
        $query->execute();
        $result = $query->fetch(PDO::FETCH_OBJ);
        $agent_display = $result->name;

        echo "<tr><td><a href='https://turfgrass.site/fields.php?field=".$field_id."'>".$field_display."</a></td>";
        echo "<td>".$datum_last."</td>";
        echo "<td>".$agent_display."</td>";
        echo "</tr>";
      }

    echo "</table>";
    echo "</div></div>"; 
  
  }

  if ($count > 0 OR $count2 > 0) {
    echo "</div>"; 
  }


  //////////////////
  // show open operations
  ?>
  <div class="row">
    <div class="col-md-12">
      
      <div class="panel panel-open">
        <div class="panel-heading"><h4><span class="glyphicon glyphicon-bell"></span>&nbsp;&nbsp; Nyított munka</h4></div>

          <?php
          $days = array("Vasárnap", "Hétfő", "Kedd", "Szerda", "Csütörtök", "Péntek", "Szombat");

          //show operations of all users that have not been completed
          echo "<table class='table table-striped centertext'>";

          $query = $db->prepare("SELECT * FROM operations WHERE `complete` < 1 AND `delete` = 0 ORDER BY `datum` ASC");
          $query->execute();

          if ($query->rowCount() > 0) {
            
            echo "<tr class='title'><td>Dátum</td><td>Terület</td><td><span class='glyphicon glyphicon-user'></span></td><td>Termék</td><td><span class='glyphicon glyphicon-scale'></span><br><i>per ha</i></td><td><span class='glyphicon glyphicon-scale'></span><br><i>összes</i></td><td>Kész %</td><td>Terv</td><td><span class='glyphicon glyphicon-comment'></span></td></tr>";

            foreach ($query as $row) {
              $id = $row['id'];
              $assigneduser = $row['user'];
              $datum = $row['datum'];
              $field = $row['field'];
              $agent = $row['agent'];
              $total = $row['total'];
              $amount = $row['amount'];
              
              if ($row['note']=="0") {
                $note = "";
              }
              else {
                $note = $row['note'];
              }

              if ($row['plan']==0) {
                $plan = "";
              }
              else {
                $plan = "<span class='glyphicon glyphicon-ok'></span>";
              }

              $complete = $row['complete'] * 100;

              $day = date('w', strtotime($datum));

              echo "<tr><td>".$days[$day].", &nbsp;".$datum."</td>";
              
              $query = $db->prepare("SELECT * FROM fields WHERE `id` = :id");
              $query->bindParam(":id", $field, PDO::PARAM_STR);
              $query->execute();
              $result = $query->fetch(PDO::FETCH_OBJ);
              echo "<td><a href='https://turfgrass.site/fields.php?field=".$field."'>".$result->name."</a></td>";

              $query = $db->prepare("SELECT * FROM users WHERE `id` = :id");
              $query->bindParam(":id", $assigneduser, PDO::PARAM_STR);
              $query->execute();
              $result = $query->fetch(PDO::FETCH_OBJ);
              echo "<td>".$result->username."</td>";

              //get name of agent
              $query = $db->prepare("SELECT * FROM agents WHERE `id` = :id");
              $query->bindParam(":id", $agent, PDO::PARAM_STR);
              $query->execute();
              $result = $query->fetch(PDO::FETCH_OBJ);

              echo "<td><a href='https://turfgrass.site/agent.php?id=".$result->id."'>".$result->name."</a></td>";

              echo "<td>".$amount."</td>";
              echo "<td>".$total."</td>";
              echo "<td>".$complete." %</td>";
              echo "<td>".$plan."</td>";
              //echo "<td>".$small."</td>";
              echo "<td>".$note."</td></tr>";

            }
          }
          else {
            echo "<tr><td colspan='9' style='text-align:left;'>Nincs nyított munka</td></tr>";
          }

        echo "</table>";
        ?>
      </div>
    </div>
  </div>
  
  <?php
  ///////////////////////
  //show planned operations
  ?>
  <div class="row">
    <div class="col-md-12">
      
      <div class="panel panel-open">
        <div class="panel-heading"><h4><span class="glyphicon glyphicon-list-alt"></span>&nbsp;&nbsp; Tervezett munka</h4></div>

        <?php
        //show operations that are planned and assigned to a user
        echo "<table class='table table-striped centertext'>";

        //get operations of the field     
        $query = $db->prepare("SELECT * FROM plan WHERE `complete` = 0 AND `delete` = 0 ORDER BY `week` ASC");
        $query->execute();

        if ($query->rowCount() > 0) {
          $i = 1;

          echo "<tr class='title'><td>Dátum</td><td>Terület</td><td>Termék</td><td><span class='glyphicon glyphicon-scale'></span><br><i>per ha</i></td><td><span class='glyphicon glyphicon-comment'></span></td><td>Szerkesztés</td></tr>"; 

          foreach ($query as $row) {
            $id = $row['id'];
            $assigneduser = $row['user'];
            $week = $row['week'];
            $field = $row['field'];
            $agent = $row['agent'];
            $amount = $row['amount'];
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
            
            if ($row['note']=="0") {
              $note = "";
            }
            else {
              $note = $row['note'];
            }
            
            $query = $db->prepare("SELECT * FROM fields WHERE `id` = :id");
            $query->bindParam(":id", $field, PDO::PARAM_STR);
            $query->execute();
            $result = $query->fetch(PDO::FETCH_OBJ);
            $fieldname = $result->name;
            echo "<td><a href='https://turfgrass.site/fields.php?field=".$field."'>".$result->name."</a></td>";

            //get name of agent
            $query = $db->prepare("SELECT * FROM agents WHERE `id` = :id");
            $query->bindParam(":id", $agent, PDO::PARAM_STR);
            $query->execute();
            $result = $query->fetch(PDO::FETCH_OBJ);
            $agentname = $result->name;

            echo "<td><a href='https://turfgrass.site/agent.php?id=".$result->id."'>".$result->name."</a></td>";

            echo "<td>".$amount."</td>";
            echo "<td>".$note."</td>";
            echo '<td><button type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#editModal'.$i.'" data-id="'.$id.'"><span class="glyphicon glyphicon-pencil"></span></button>&nbsp;&nbsp';
            echo '<button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#deleteModal'.$i.'" data-id="'.$id.'"><span class="glyphicon glyphicon-remove"></span></button></td></tr>';
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

                  <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" accept-charset="utf-8">
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
                      <div class="input-group date" data-provide="datepicker" data-date-format="yyyy-mm-dd" data-date-startDate="2017-01-01" data-date-autoclose = "true" data-date-todayHighlight = "true">
                        <input type="date" class="form-control" name="date" value="<?echo date("Y-m-d");?>">
                        <div class="input-group-addon">
                            <span class="glyphicon glyphicon-th"></span>
                        </div>
                      </div>
                    </div>
                  </div>

                  <div class="row">
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
                        <input class="form-control" style="width: 70%;" type="number" step=".1" value="<?echo $amount;?>" name="amount">
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
                  <input type="hidden" name="id" value="<?echo $id;?>"> 

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
                    <h4 class="modal-title">Töröl</h4>
                  </div>

                  <div class="row">
                    <div class="col-md-12" style ="text-align: center; padding-top: 20px;">
                       <b>Biztos?</b>
                    </div>
                    <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" accept-charset="utf-8">  
                      <input type="hidden" name="id" value="<?echo $id;?>">  

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
        }

        else {
          echo "<tr><td colspan='6' style='text-align:left'>Nincs tervezett munka</td><tr>";
        }

      echo "</table>";
      ?>

      </div>
    </div>
  </div>


  <?php
  ///////////////////////
  //show last finished operations
  ?>
  <div class="row">
    <div class="col-md-12"> 
      <div class="panel panel-open">
        <div class="panel-heading"><h4><span class="glyphicon glyphicon-ok"></span>&nbsp;&nbsp; Befejezett munka - Utolsó 14 nap</h4></div>

        <?php
        //show operations that have been finished within the last two weeks
        echo "<table class='table table-striped centertext'>";
        echo "<tr class='title'><td>Dátum</td><td>Terület</td><td><span class='glyphicon glyphicon-user'></span></td><td>Termék</td><td><span class='glyphicon glyphicon-scale'></span><br><i>per ha</i></td><td><span class='glyphicon glyphicon-scale'></span><br><i>összes</i></td><td>Terv</td><td>Rész</td><td><span class='glyphicon glyphicon-comment'></span></td></tr>";

        $minus = strtotime("-14days");
        $break = date("Y-m-d H:i:s", $minus);

        $query = $db->prepare("SELECT * FROM operations WHERE `complete` = 1 AND `delete` = 0 AND `datum` >= :break ORDER BY `datum` DESC");
        $query->bindParam(":break", $break, PDO::PARAM_STR);
        $query->execute();

        if ($query->rowCount() > 0) {
          
          foreach ($query as $row) {
            $id = $row['id'];
            $assigneduser = $row['user'];
            $datum = $row['datum'];
            $field = $row['field'];
            $agent = $row['agent'];
            $amount = $row['amount'];
            $total = $row['total'];
            
            if ($row['note']=="0") {
              $note = "";
            }
            else {
              $note = $row['note'];
            }

            if ($row['plan']==0) {
              $plan = "";
            }
            else {
              $plan = "<span class='glyphicon glyphicon-ok'></span>";
            }

            if ($row['small']==0) {
              $small = "";
            }
            else {
              $ha = $total / $amount;
              $hadisplay = number_format($ha, 1, ',', ' ');
              $small = "<span class='glyphicon glyphicon-ok'></span> &nbsp;<i>(".$hadisplay." ha)</i>";
            }

            $day = date('w', strtotime($datum));
            echo "<tr><td>".$days[$day].", &nbsp;".$datum."</td>";
            
            $query = $db->prepare("SELECT * FROM fields WHERE `id` = :id");
            $query->bindParam(":id", $field, PDO::PARAM_STR);
            $query->execute();
            $result = $query->fetch(PDO::FETCH_OBJ);
            echo "<td><a href='https://turfgrass.site/fields.php?field=".$field."'>".$result->name."</a></td>";

            $query = $db->prepare("SELECT * FROM users WHERE `id` = :id");
            $query->bindParam(":id", $assigneduser, PDO::PARAM_STR);
            $query->execute();
            $result = $query->fetch(PDO::FETCH_OBJ);
            echo "<td>".$result->username."</td>";
            
            //get name of agent
            $query = $db->prepare("SELECT * FROM agents WHERE `id` = :id");
            $query->bindParam(":id", $agent, PDO::PARAM_STR);
            $query->execute();
            $result = $query->fetch(PDO::FETCH_OBJ);

            echo "<td><a href='https://turfgrass.site/agent.php?id=".$result->id."'>".$result->name."</a></td>";

            echo "<td>".$amount."</td>";
            echo "<td>".$total."</td>";
            echo "<td>".$plan."</td>";
            echo "<td>".$small."</td>";
            echo "<td>".$note."</td></tr>";

          }
        }
        else {
          echo "<tr><td colspan='9'>Nincs kész munka</td>";
        }

        echo "</table>";
        ?>
      </div>
    </div>
  </div>

</div>

  


