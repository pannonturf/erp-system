<?php 
/////////////////////////////////
// List of agents with details //
/////////////////////////////////

include('views/_header'.$header.'.php');

require_once('config/config.php');
$db = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.'', DB_USER, DB_PASS);
$db -> exec("set names utf8");


//////////////
//If stocktaking form has been sent
if (isset($_POST['stocktakingForm'])) {

  //Get array from form
  $datum = $_POST['date'];
  $stock = $_POST['stock'];
  if (isset($_SESSION['userid'])) {
    $user = $_SESSION['userid'];
  }
  else {
    $user = $_COOKIE["userid"];
  }

  //Write into stocktaking table
  $query = $db->prepare("INSERT INTO `stocktaking` (`id`, `datum`, `agent`, `amount`, `price`, `user`) VALUES (NULL, :datum, :agent, :amount, :price, :user)");
  $query2 = $db->prepare("SELECT * FROM agents WHERE `id` = :id");
  $query3 = $db->prepare("UPDATE `agents` SET `stock` = :stock WHERE `id` = :id");

  $sql5 = "INSERT INTO `movement` (`id`, `datum`, `agent`, `difference`, `total`, `type`, `link`, `user`) VALUES (NULL, :datum, :agent, :difference, :total, :type, :link, :user);";
  $query5 = $db->prepare($sql5);
  
  foreach($stock AS $agent => $amount) {
    $query2->bindParam(":id", $agent, PDO::PARAM_STR);
    $query2->execute();
    $result2 = $query2->fetch(PDO::FETCH_OBJ);
    $price = $result2->price;

    $query->bindParam(":datum", $datum, PDO::PARAM_STR);
    $query->bindParam(":agent", $agent, PDO::PARAM_STR);
    $query->bindParam(":amount", $amount, PDO::PARAM_STR);
    $query->bindParam(":price", $price, PDO::PARAM_STR);
    $query->bindParam(":user", $user, PDO::PARAM_STR);

    $query->execute();

    $query3->bindParam(":id", $agent, PDO::PARAM_STR);
    $query3->bindParam(":stock", $amount, PDO::PARAM_STR);
    $query3->execute(); 

    $type = 3;
    $difference = 0;
    $link = 0;

    $query5->bindParam(":datum", $datum, PDO::PARAM_STR);  
    $query5->bindParam(":agent", $agent, PDO::PARAM_STR); 
    $query5->bindParam(":difference", $difference, PDO::PARAM_STR);
    $query5->bindParam(":total", $amount, PDO::PARAM_STR);
    $query5->bindParam(":type", $type, PDO::PARAM_STR);
    $query5->bindParam(":link", $link, PDO::PARAM_STR);
    $query5->bindParam(":user", $user, PDO::PARAM_STR);

    $query5->execute();   
  }

  //Other message
  echo '<div class="alert alert-success center-block" role="alert">Sikerült!</div>';
}


//////////////
//If new agent is added
if (isset($_POST['newAgentForm'])) {

  //Get array from form
  $name = $_POST['name'];
  $type = $_POST['type'];
  $standard = $_POST['standard'];
  $min = $_POST['min'];
  $n = $_POST['n'];
  $p = $_POST['p'];
  $k = $_POST['k'];

  //Write into delivery table
  $query = $db->prepare("INSERT INTO `agents` (`id`, `name`, `type`, `min`, `standard`, `n`, `p`, `k`) VALUES (NULL, :name, :type, :min, :standard, :n, :p, :k)");
  $query->bindParam(":name", $name, PDO::PARAM_STR);
  $query->bindParam(":type", $type, PDO::PARAM_STR);
  $query->bindParam(":standard", $standard, PDO::PARAM_STR);
  $query->bindParam(":min", $min, PDO::PARAM_STR);
  $query->bindParam(":n", $n, PDO::PARAM_STR);
  $query->bindParam(":p", $p, PDO::PARAM_STR);
  $query->bindParam(":k", $k, PDO::PARAM_STR);

  $query->execute();

  //Other message
  echo '<div class="alert alert-success center-block" role="alert">Sikerült!</div>';
}

////////////////
//If form has been sent
if (isset($_POST['deliveryForm'])) {

  //Get array from form
  $datum = $_POST['date'];
  $delivery = $_POST['delivery'];
  if (isset($_SESSION['userid'])) {
    $user = $_SESSION['userid'];
  }
  else {
    $user = $_COOKIE["userid"];
  }

  //Write into delivery table
  $sql = "INSERT INTO `deliveries` (`id`, `datum`, `agent`, `amount`, `price`, `user`) VALUES (NULL, :datum, :agent, :amount, :price, :user);";
  $query = $db->prepare($sql);

  $sql5 = "INSERT INTO `movement` (`id`, `datum`, `agent`, `difference`, `total`, `type`, `link`, `user`) VALUES (NULL, :datum, :agent, :difference, :total, :type, :link, :user);";
  $query5 = $db->prepare($sql5);

  $query6 = $db->prepare("SELECT * FROM `deliveries` ORDER BY `id` DESC LIMIT 1");

  $query1 = $db->prepare("SELECT * FROM agents WHERE `id` = :id");
  $query2 = $db->prepare("UPDATE `agents` SET `stock` = :stock WHERE `id` = :id");
  
  foreach($delivery AS $agent => $array) {
    $pricetotal = $array[1];
    $amount =  $array[2];

    if ($amount > 0) {
          $price = $pricetotal / $amount;

      $query->bindParam(":datum", $datum, PDO::PARAM_STR);
      $query->bindParam(":agent", $agent, PDO::PARAM_STR);
      $query->bindParam(":amount", $amount, PDO::PARAM_STR);
      $query->bindParam(":price", $price, PDO::PARAM_STR);
      $query->bindParam(":user", $user, PDO::PARAM_STR);

      $query->execute();

      //Update stock and movement
      $query1->bindParam(":id", $agent, PDO::PARAM_STR);
      $query1->execute();
      $result = $query1->fetch(PDO::FETCH_OBJ);
      $x1 = $result->stock;
      $stock = $x1 + $amount;

      $query2->bindParam(":id", $agent, PDO::PARAM_STR);
      $query2->bindParam(":stock", $stock, PDO::PARAM_STR);
      $query2->execute(); 

      $type = 4;

      $query6->execute();
      $result6 = $query6->fetch(PDO::FETCH_OBJ);
      $link = $result6->id;

      $query5->bindParam(":datum", $datum, PDO::PARAM_STR);  
      $query5->bindParam(":agent", $agent, PDO::PARAM_STR); 
      $query5->bindParam(":difference", $amount, PDO::PARAM_STR);
      $query5->bindParam(":total", $stock, PDO::PARAM_STR);
      $query5->bindParam(":type", $type, PDO::PARAM_STR);
      $query5->bindParam(":link", $link, PDO::PARAM_STR);
      $query5->bindParam(":user", $user, PDO::PARAM_STR);

      $query5->execute();
    }     
  }

  //Other message
  echo '<div class="alert alert-success center-block" role="alert">Sikerült!</div>';
}


//If sale form has been sent
if (isset($_POST['saleForm'])) {

  //Get array from form
  $datum = $_POST['date'];
  $sale = $_POST['sale'];
  $customer = $_POST['customer'];
  if (isset($_SESSION['userid'])) {
    $user = $_SESSION['userid'];
  }
  else {
    $user = $_COOKIE["userid"];
  }

  //Write into sales and movement table
  $sql = "INSERT INTO `outbound` (`id`, `datum`, `agent`, `customer`, `amount`, `price`, `user`, `type`) VALUES (NULL, :datum, :agent, :customer, :amount, :price, :user, :type);";
  $query = $db->prepare($sql);

  foreach($sale AS $agent => $array) {
    $pricetotal = $array[1];
    $amount =  $array[2];
    $type = 2;

    if ($amount > 0) {
      $price = $pricetotal / $amount;

      $query->bindParam(":datum", $datum, PDO::PARAM_STR);
      $query->bindParam(":agent", $agent, PDO::PARAM_STR);
      $query->bindParam(":amount", $amount, PDO::PARAM_STR);
      $query->bindParam(":price", $price, PDO::PARAM_STR);
      $query->bindParam(":customer", $customer, PDO::PARAM_STR);
      $query->bindParam(":user", $user, PDO::PARAM_STR);
      $query->bindParam(":type", $type, PDO::PARAM_STR);

      $query->execute();
    }   
  }

  //Update stock of agents
  $query1 = $db->prepare("SELECT * FROM agents WHERE `id` = :id");
  $query2 = $db->prepare("UPDATE `agents` SET `stock` = :stock WHERE `id` = :id");

  $sql5 = "INSERT INTO `movement` (`id`, `datum`, `agent`, `difference`, `total`, `type`, `link`, `user`) VALUES (NULL, :datum, :agent, :difference, :total, :type, :link, :user);";
  $query5 = $db->prepare($sql5);

  $query6 = $db->prepare("SELECT * FROM `outbound` ORDER BY `id` DESC LIMIT 1");

  foreach($sale AS $agent => $array) {
    $amount=  $array[2];

    if ($amount > 0) {

      $query1->bindParam(":id", $agent, PDO::PARAM_STR);
      $query1->execute();

      $result = $query1->fetch(PDO::FETCH_OBJ);
      $x1 = $result->stock;
      $x = $x1 - $amount;

      $query2->bindParam(":id", $agent, PDO::PARAM_STR);
      $query2->bindParam(":stock", $x, PDO::PARAM_STR);
      $query2->execute(); 

      $type = 5;
      $difference = 0 - $amount;

      $query6->execute();
      $result6 = $query6->fetch(PDO::FETCH_OBJ);
      $link = $result6->id;

      $query5->bindParam(":datum", $datum, PDO::PARAM_STR);  
      $query5->bindParam(":agent", $agent, PDO::PARAM_STR); 
      $query5->bindParam(":difference", $difference, PDO::PARAM_STR);
      $query5->bindParam(":total", $x, PDO::PARAM_STR);
      $query5->bindParam(":type", $type, PDO::PARAM_STR);
      $query5->bindParam(":link", $link, PDO::PARAM_STR);
      $query5->bindParam(":user", $user, PDO::PARAM_STR);

      $query5->execute();
    }
  }

  //Other message
  echo '<div class="alert alert-success center-block" role="alert">Sikerült!</div>';
}


/////////
//if stock was edited
if (isset($_POST['editStockForm'])) {
  //Get variables from form
  $datum = date("Y-m-d");;
  $agent = $_POST['agent'];
  $stock = $_POST['stock'];
  $oldstock = $_POST['oldstock'];

  $difference = $oldstock - $stock;

  if (isset($_SESSION['userid'])) {
    $user = $_SESSION['userid'];
  }
  else {
    $user = $_COOKIE["userid"];
  }

  $type = 3;
  $link = 0;

  //Update stock of agents
  $query4 = $db->prepare("UPDATE `agents` SET `stock` = :stock WHERE `id` = :id");

  $sql5 = "INSERT INTO `movement` (`id`, `datum`, `agent`, `difference`, `total`, `type`, `link`, `user`) VALUES (NULL, :datum, :agent, :difference, :total, :type, :link, :user);";
  $query5 = $db->prepare($sql5);

  //Update movement
  $query5->bindParam(":datum", $datum, PDO::PARAM_STR);  
  $query5->bindParam(":agent", $agent, PDO::PARAM_STR); 
  $query5->bindParam(":difference", $difference, PDO::PARAM_STR);
  $query5->bindParam(":total", $stock, PDO::PARAM_STR);
  $query5->bindParam(":type", $type, PDO::PARAM_STR);
  $query5->bindParam(":link", $link, PDO::PARAM_STR);
  $query5->bindParam(":user", $user, PDO::PARAM_STR);

  $query5->execute();

  //Update stock
  $query4->bindParam(":id", $agent, PDO::PARAM_STR);
  $query4->bindParam(":stock", $stock, PDO::PARAM_STR);
  $query4->execute();   

  echo '<div class="alert alert-success center-block" role="alert">Sikerült!</div>';

}


/////////
//if delivery was edited
if (isset($_POST['editForm'])) {
  //Get variables from form
  $id = $_POST['id'];
  $datum = $_POST['date'];
  $agent = $_POST['agent'];
  $pricetotal = $_POST['pricetotal'];
  $x2 = $_POST['amount'];
  $oldx2 = $_POST['oldamount'];
  $oldp2 = $_POST['oldprice'];

  $p2 = $pricetotal / $x2;

  //Update stock of agents
  $query3 = $db->prepare("SELECT * FROM `agents` WHERE `id` = :id");
  $query4 = $db->prepare("UPDATE `agents` SET `stock` = :stock WHERE `id` = :id");
  $query5 = $db->prepare("UPDATE `agents` SET `price` = :price WHERE `id` = :id");

  //get price
  $query3->bindParam(":id", $agent, PDO::PARAM_STR);
  $query3->execute();

  $result3 = $query3->fetch(PDO::FETCH_OBJ);
  $oldx = $result3->stock;
  $oldp = $result3->price;
  $x1 = $oldx - $oldx2;
  $x = $x1 + $x2;

  if ($oldp2 == 0) {
    $p1 = $oldp;

    if ($x1 > 0) {
      $p = $p1 * ($x1 / $x) + $p2 * ($x2 / $x);
    }
    else {
      $p = $p2;
    }
  }

  if ($oldp2 > 0) {
    $p1 = ($oldp * $oldx - $oldx2 *$oldp2) / $x1;

    if ($x1 > 0) {
      $p = $p1 * ($x1 / $x) + $p2 * ($x2 / $x);
    }
    else {
      $p = $p2;
    }
  }
  

  //Update deliveries and movement
  $sql = "UPDATE `deliveries` SET `datum` = :datum, `agent` = :agent, `amount` = :amount, `price` = :price WHERE `id` = :id";
  $query = $db->prepare($sql);

  $sql2 = "UPDATE `movement` SET `datum` = :datum, `agent` = :agent, `difference` = :difference, `total` = :total WHERE `id` = :id";
  $query2 = $db->prepare($sql2);

  $query3 = $db->prepare("SELECT * FROM `movement` WHERE `link` = :id AND `type` = 4");

  $query->bindParam(":datum", $datum, PDO::PARAM_STR);
  $query->bindParam(":agent", $agent, PDO::PARAM_STR);
  $query->bindParam(":amount", $x2, PDO::PARAM_STR);
  $query->bindParam(":price", $p2, PDO::PARAM_STR);
  $query->bindParam(":id", $id, PDO::PARAM_STR);

  $query->execute();

  $query3->bindParam(":id", $id, PDO::PARAM_STR);
  $query3->execute();
  $result3 = $query3->fetch(PDO::FETCH_OBJ);
  $movement_id = $result3->id;

  $query2->bindParam(":datum", $datum, PDO::PARAM_STR);
  $query2->bindParam(":agent", $agent, PDO::PARAM_STR);
  $query2->bindParam(":difference", $x2, PDO::PARAM_STR);
  $query2->bindParam(":total", $x, PDO::PARAM_STR);
  $query2->bindParam(":id", $movement_id, PDO::PARAM_STR);

  $query2->execute();

  if ($oldx2 != $x2) {
    //Update stock
    $query4->bindParam(":id", $agent, PDO::PARAM_STR);
    $query4->bindParam(":stock", $x, PDO::PARAM_STR);
    $query4->execute();   
  } 

  if ($p2 != 0) {
    //Update stock
    $query5->bindParam(":id", $agent, PDO::PARAM_STR);
    $query5->bindParam(":price", $p, PDO::PARAM_STR);
    $query5->execute(); 
  } 


  echo '<div class="alert alert-success center-block" role="alert">Sikerült!</div>';

}

/*
/////////
//if delivery was deleted (Muss noch ausgebessert werden!!)
if (isset($_POST['deleteForm'])) {
    $user = $_SESSION['userid'];
    $id = $_POST['id'];
    $oldtotal = $_POST['oldtotal'];
    $agent = $_POST['agent'];

    //Update operations
    $sql = "DELETE FROM `deliveries` WHERE `id` = :id";
    $query = $db->prepare($sql);

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

    echo '<div class="alert alert-success center-block" role="alert">Sikerült!</div>';
}
*/
?>

  <div class="row">
    <div class="col-md-6">
      <h3 style='margin-top: 0px;'><span class='glyphicon glyphicon-stats'></span>&nbsp;&nbsp; Raktár</h3>
    </div>

    <div class="col-md-3">
        <button type="button" data-toggle="modal" data-target="#inventoryModal" class="modal_button"><img src="../img/delivery.png" class="picture_add"></button> 
    </div>

    <div class="col-md-3">
        <button type="button" data-toggle="modal" data-target="#saleModal" class="modal_button"><img src="../img/sales.png" class="picture_add"></button> 
    </div>
  </div>

<?php
/////////
// Show agents in inventory
echo '<div class="row"><div class="col-md-6" style="padding: 0px 20px 20px;">';
echo '<div class="panel panel-open">';
echo '<div class="panel-heading"><h4><span class="glyphicon glyphicon-filter"></span>&nbsp;&nbsp; Aktuális készlet</h4></div>';

echo '<div class="field">';

echo "<table class='table table-striped'>";
echo "<tr class='title'><td>Termék</td><td><span class='glyphicon glyphicon-scale'></span></td><td class='border'>Min</td><td style='padding-left: 20px;'>Ft<br><i>per kg</i><td>EFt<br><i>összes</i></td></tr>";


//get name of agent
$query = $db->prepare("SELECT * FROM agents WHERE `deleted` = 0 ORDER BY `name`");
$query->execute();

//Calculate costs
$pricetotalFertilizer = 0;
$pricetotalPesticide= 0;
$pricetotalBoth = 0;

$stockFertilizer = 0;
$stockPesticide= 0;
$stockBoth = 0;

$i = 1;

foreach ($query as $row) {
  $agent = $row['id'];
  $agentname = $row['name'];
  $price = $row['price'];
  $pricedisplay = number_format($price, 0, ',', ' ');
  $type = $row['type'];
  $stock = $row['stock'];
  $min = $row['min'];
  $pricetotal = $stock * $price;
  $pricetotaldisplay = number_format($pricetotal / 1000, 0, ',', ' ');
   
  if ($type == 1) {
    $stockdisplay = number_format($stock, 0, ',', ' ');

    //text for fertilizers
    $fertilizertext .= "<tr><td><a href='https://turfgrass.site/agent.php?id=".$agent."'>".$agentname."</a></td>";
    $fertilizertext .= "<td><b>".$stockdisplay." kg</b><button type='button' class='edit_button' data-toggle='modal' data-target='#editStockModal".$i."''><span class='glyphicon glyphicon-pencil'></span></button></td>";
    $fertilizertext .= "<td class='border'><i>".$min."</i></td>";
    $fertilizertext .= "<td style='padding-left: 20px;'>".$pricedisplay."</td>";
    $fertilizertext .= "<td>".$pricetotaldisplay."</td></tr>";

    $pricetotalFertilizer += $pricetotal;
    $stockFertilizer += $stock;
  }

  if ($type == 2) {
    $stockdisplay = number_format($stock, 2, ',', ' ');

    //text for fertilizers
    $pesticidetext .= "<tr><td><a href='https://turfgrass.site/agent.php?id=".$agent."'>".$agentname."</a></td>";
    $pesticidetext .= "<td><b>".$stockdisplay." l</b><button type='button' class='edit_button' data-toggle='modal' data-target='#editStockModal".$i."''><span class='glyphicon glyphicon-pencil'></span></button></td>";
    $pesticidetext .= "<td class='border'><i>".$min."</i></td>";
    $pesticidetext .= "<td style='padding-left: 20px;'>".$pricedisplay."</td>";
    $pesticidetext .= "<td>".$pricetotaldisplay."</td></tr>";

    $pricetotalPesticide += $pricetotal;
    $stockPesticide += $stock;
  }
  ?>
  
  <!-- MODAL -->
  <!-- Edit stock -->
  <div class="modal fade" id="editStockModal<?php echo $i; ?>" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document" style="width: 700px;">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title">Szerkesztés</h4>
        </div>

        <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" accept-charset="utf-8">
        <div class="row">
          <div class="col-md-6"><br>
            <div class="form-group">                         
              <label for="field">Termék</label>
              <p class="form-control-static"><?echo $agentname;?></p>
            </div>
          </div>
          <div class="col-md-6"><br>
            <div class="form-group">                          
            <label for="field">Mennyiség</label>
              <input class="form-control" style="width: 70%;" type="number" step="0.1" min="0"  value="<?echo $stock;?>" name="stock">
            </div>
          </div> 
        </div>

        <input type="hidden" name="agent" value="<?echo $agent;?>"> 
        <input type="hidden" name="oldstock" value="<?echo $stock;?>"> 

        <div class="modal-footer">
          <button type="submit" class="btn btn-primary center-block" name="editStockForm" value="Submit">Küldés</button>
        </div>
        </form>
      </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
  </div><!-- /.modal -->
<?php
  $i++;
}

$pricetotalFertilizerDisp = number_format($pricetotalFertilizer / 1000, 0, ',', ' ');
$pricetotalPesticideDisp = number_format($pricetotalPesticide / 1000, 0, ',', ' ');
$stockFertilizerDisp = number_format($stockFertilizer, 0, ',', ' ');
$stockPesticideDisp = number_format($stockPesticide, 1, ',', ' ');

$pricetotalSum = $pricetotalPesticide + $pricetotalFertilizer;
$pricetotalSumDisp = number_format($pricetotalSum / 1000, 0, ',', ' ');

echo "<tr class='subtitle'><td style ='padding-top: 25px;'>Műtrágya</td><td style ='padding-top: 25px;'>".$stockFertilizerDisp." kg</td><td class='border'></td><td></td><td style ='padding-top: 25px;'><b>".$pricetotalFertilizerDisp."</b></td></tr>";
echo $fertilizertext;

echo "<tr class='subtitle'><td style ='padding-top: 25px;'>Permet</td><td style ='padding-top: 25px;'>".$stockPesticideDisp." l</td><td class='border'></td><td></td><td style ='padding-top: 25px;'><b>".$pricetotalPesticideDisp."</b></td></tr>";
echo $pesticidetext;

echo "<tr class ='sum' style='background-color: #e1e1e1;'><td style='text-align:left'>Összes</td><td></td><td></td><td></td><td>".$pricetotalSumDisp." EFt</td></tr>";

echo "</table>";
echo "</div></div><br><br>";


/*
////////////
// Show agents out of stock
echo '<div class="panel panel-danger">';
echo '<div class="panel-heading"><h4><span class="glyphicon glyphicon-remove"></span>&nbsp;&nbsp; Készlet hiány</h4></div>';

echo '<div class="field">';

echo "<table class='table table-striped'>";
echo "<tr class='title'><td>Termék</td><td>Ft<br><i>per kg</i></td><td>Minimum</td></tr>";


$query = $db->prepare("SELECT * FROM agents WHERE stock = 0 ORDER BY `name`");
$query->execute();

foreach ($query as $row) {
  $agent = $row['id'];
  $agentname = $row['name'];
  $price = $row['price'];
  $pricedisplay = number_format($price, 0, ',', ' ');
  $type = $row['type'];
  $stock = $row['stock'];
  $min = $row['min'];
   
  echo "<tr><td>".$agentname."</td>";
  echo "<td>".$pricedisplay."</td>";
  echo "<td>".$min."</td></tr>";
}

echo "</table>";
echo "</div></div><br><br>";
*/
echo '<div class="col-md-6">';
echo '<button type="button" class="btn btn-default btn-lg" data-toggle="modal" data-target="#newModal"><span class="glyphicon glyphicon-plus"></span>&nbsp;&nbsp; Új termék</button>';
echo '</div><div class="col-md-6">';
echo '<button type="button" class="btn btn-default btn-lg" data-toggle="modal" data-target="#stocktakingModal"><span class="glyphicon glyphicon-plus"></span>&nbsp;&nbsp; Új inventúra</button>';
echo '</div></div>';



//////////////
//show last 10 entries (deliveries + sales)
echo '<div class="col-md-6" style="padding: 0px 20px 20px;">';

//deliveries
$query = $db->prepare("SELECT * FROM deliveries WHERE price = 0");
$query->execute();

/*
if ($query->rowCount() > 0) {
  echo '<div class="alert alert-danger center-block" role="alert" style="text-align:center;"><span class="glyphicon glyphicon-alert"></span>&nbsp;&nbsp;&nbsp;&nbsp;<b>Az árak még hiányoznak!</b>&nbsp;&nbsp;&nbsp;&nbsp;<span class="glyphicon glyphicon-alert"></span></div>';
}
*/

echo '<div class="panel panel-primary">';
echo '<div class="panel-heading"><h4><span class="glyphicon glyphicon-list"></span>&nbsp;&nbsp; Utolsó befejezések</h4></div>';

echo '<div class="field">';

echo "<table class='table table-striped'>";
echo "<tr class='subtitle'><td style ='padding-top: 25px;'>Tételek</td><td></td><td></td><td></td><td></td></tr>";
echo "<tr class='title'><td>Dátum</td><td>Termék</td><td>Mennyiség</td><td></td><td></td></tr>";

//deliveries
$query = $db->prepare("SELECT * FROM deliveries ORDER BY `datum` DESC LIMIT 10");
$query->execute();

foreach ($query as $row) {
  $deliveryid = $row['id'];
  $datum = $row['datum'];
  $agent = $row['agent'];
  $price = $row['price'];
  $pricedisplay = number_format($price, 0, ',', ' ');
  $amount = $row['amount'];
  $amountdisplay = number_format($amount, 0, ',', ' ');
  $oldamount = $amount;
  $pricetotal = $price * $amount;
  $oldprice = $price;
  $today = date("Y-m-d");

  echo "<tr><td>".$datum."</td>";

  //get name of agent
  $query = $db->prepare("SELECT * FROM agents WHERE `id` = :id");
  $query->bindParam(":id", $agent, PDO::PARAM_STR);
  $query->execute();
  $result = $query->fetch(PDO::FETCH_OBJ);
  $agentname = $result->name;
  echo "<td>".$agentname."</td>";

  echo "<td>".$amountdisplay."</td>";
  echo "<td></td>";
  echo '<td><button type="button" class="btn btn-default btn-xs" data-toggle="modal" data-target="#editModal'.$i.'" data-id="'.$id.'"><span class="glyphicon glyphicon-pencil"></span></button>';
  
  /*
  if ($_SESSION['userid'] == 1 OR $_SESSION['userid'] == 3 OR $datum == $today) {
    echo '&nbsp;&nbsp<button type="button" class="btn btn-danger btn-xs" data-toggle="modal" data-target="#deleteModal'.$i.'" data-id="'.$id.'"><span class="glyphicon glyphicon-remove"></span></button></td>';
  }
  */
  
  echo "</td></tr>";        
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
          <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" accept-charset="utf-8">
            <label for="date">Dátum</label>
            <div class="input-group date" style="width: 70%;" data-provide="datepicker" data-date-format="yyyy-mm-dd" data-date-startDate="2017-01-01" data-date-autoclose = "true" data-date-todayHighlight = "true">
              <input type="date" class="form-control" name="date" value="<?echo $datum;?>">
              <div class="input-group-addon">
                  <span class="glyphicon glyphicon-th"></span>
              </div>
            </div>
          </div>

          <div class="col-md-6"><br>
            <div class="form-group">
            <!-- fertilizers plus pesticides -->                            
            <label for="field">Termék</label>
              <select class="form-control" style="width: 70%;" name="agent">
                <option value="<?echo $agent;?>" selected><?echo $agentname;?></option>
                <?php  
                $query = $db->prepare("SELECT * FROM agents");
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
          <!--
          <div class="col-md-6">
              <div class="form-group">                            
              <label for="field">Ár összes</label>
                <input class="form-control" style="width: 70%;" type="number" step="1" min="0" value="<?echo $pricetotal;?>" name="pricetotal">
              </div>
          </div>  
        -->
          <div class="col-md-6">
            <div class="form-group">                          
            <label for="field">Mennyiség</label>
              <input class="form-control" style="width: 70%;" type="number" step="1" min="0"  value="<?echo $amount;?>" name="amount">
            </div>
          </div>  
        </div>
          
        <input type="hidden" name="oldamount" value="<?echo $oldamount;?>"> 
        <input type="hidden" name="oldprice" value="<?echo $oldprice;?>"> 
        <input type="hidden" name="id" value="<?echo $deliveryid;?>"> 

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
            <input type="hidden" name="id" value="<?echo $deliveryid;?>">  

            <div class="row">
              <div class="col-md-3"></div>
              <div class="col-md-3"><button type="submit" class="btn btn-primary" name="deletePlanForm" value="Submit">Igen</button></div></form>
              <div class="col-md-3"><button class="btn btn-danger" data-dismiss="modal">Nem</button></div>
            </div>
        </div>
      </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
  </div><!-- /.modal -->

<?php
$i++;

}


//sales
echo "<tr class='subtitle'><td style ='padding-top: 25px;'>Eladások</td><td></td><td></td><td></td><td></td></tr>";
echo "<tr class='title'><td>Dátum</td><td>Termék</td><td>Mennyiség</td><td>Netto ár</td><td>Vevő</td></tr>";

$query = $db->prepare("SELECT * FROM outbound WHERE `type` = 2 ORDER BY `datum` DESC LIMIT 10");
$query->execute();

foreach ($query as $row) {
  $datum = $row['datum'];
  $agent = $row['agent'];
  $price = $row['price'];
  $pricedisplay = number_format($price, 0, ',', ' ');
  $amount = $row['amount'];
  $amountdisplay = number_format($amount, 2, ',', ' ');
  $customer = $row['customer'];

  echo "<tr><td>".$datum."</td>";

  //get name of agent
  $query = $db->prepare("SELECT * FROM agents WHERE `id` = :id");
  $query->bindParam(":id", $agent, PDO::PARAM_STR);
  $query->execute();
  $result = $query->fetch(PDO::FETCH_OBJ);
  $agentname = $result->name;
  echo "<td>".$agentname."</td>";

  echo "<td>".$amountdisplay."</td>";
  echo "<td>".$price."</td>";
  echo "<td>".$customer."</td>";
  echo '</tr>';
  //echo '<button type="button" class="btn btn-danger btn-xs" data-toggle="modal" data-target="#deleteModal'.$i.'" data-id="'.$id.'"><span class="glyphicon glyphicon-remove"></span></button></td></tr>';
}

echo "</table>";
echo "</div></div></div></div>";
?>  


<div class="modal fade" id="newModal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document" style="width: 700px;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Új termék</h4>
      </div>

      <form method="post" action="<?echo $_SERVER['PHP_SELF'];?>" accept-charset="utf-8">
      <div class="modal-body"> 
        
        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label for="name">Termék neve</label>
              <input type="text" name="name">
            </div>
          </div>

          <div class="col-md-3" style="padding-right: 0px;">
            <div class="form-check">
              <label class="form-check-label center-block" style="padding-left: 60px;">
                <input type="radio" class="form-check-input" name="type" value="1">
                Műtragya
              </label>
            </div>
          </div>
          <div class="col-md-3">
            <div class="form-check">
              <label class="form-check-label center-block">
                <input type="radio" class="form-check-input" name="type" value="2" checked>
                Permetszer
              </label>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-md-3">
            <div class="form-group">
              <label for="standard">Standard</label>
              <input class="form-control" type="number" step=".5" value="0" min="0" name="standard">
            </div>
          </div>

          <div class="col-md-3">
            <div class="form-group">
              <label for="min">Minimum</label>
              <input class="form-control" type="number" step=".5" value="0" min="0" name="min">
            </div>
          </div>

          <div class="col-md-2">
            <div class="form-group">
              <label for="n">N</label>
              <input class="form-control" type="number" step=".5" value="0" min="0" name="n">
            </div>
          </div>

          <div class="col-md-2">
            <div class="form-group">
              <label for="p">P</label>
              <input class="form-control" type="number" step=".5" value="0" min="0" name="p">
            </div>
          </div>

          <div class="col-md-2">
            <div class="form-group">
              <label for="k">K</label>
              <input class="form-control" type="number" step=".5" value="0" min="0" name="k">
            </div>
          </div>
        </div>

        </table>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-default" name="newAgentForm" value="Submit">Küldés</button>
      </div>
      </form>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->


<div class="modal fade" id="stocktakingModal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document" style="width: 400px;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Új inventúra</h4>
      </div>

      <form method="post" action="<?echo $_SERVER['PHP_SELF'];?>" accept-charset="utf-8">
      <div class="modal-body"> 
        
        <label for="date">Dátum</label>
        <div class="input-group date" data-provide="datepicker" data-date-format="yyyy-mm-dd" data-date-startDate="2017-01-01" data-date-autoclose = "true" data-date-todayHighlight = "true">
          <input type="date" class="form-control" name="date" value="<?echo date("Y-m-d");?>">
          <div class="input-group-addon">
              <span class="glyphicon glyphicon-th"></span>
          </div>
        </div>
        <br><br>

        <table class="table table-striped">
        <tr class='title'><td>Termék</td><td style='text-align: center;'>Mennyiség</td></tr>
        
        <?php
        //get name of agent
        $query = $db->prepare("SELECT * FROM agents ORDER BY `name`");
        $query->execute();

        $fertilizertext = "<tr class='subtitle'><td style ='padding-top: 25px;'>Trágya</td><td></td></tr>";
        $pesticidetext = "<tr class='subtitle'><td style ='padding-top: 25px;'>Permetszer</td><td></td></tr>";

        foreach ($query as $row) {
          $agent = $row['id'];
          $agentname = $row['name'];
          $price = $row['price'];
          $type = $row['type'];
          $stock = $row['stock'];
           
          if ($type == 1) {
            //text for fertilizers
            $fertilizertext .= "<tr><td style='width: 50%;'>".$agentname."</td>";
            $fertilizertext .= '<td><input class="form-control" type="number" step=".5" value="'.$stock.'" min="0" name="stock['.$row['id'].']""></td></tr>';
          }

          if ($type == 2) {
            //text for fertilizers
            $pesticidetext .= "<tr><td style='width: 50%;'>".$agentname."</td>";
            $pesticidetext .= '<td><input class="form-control" type="number" step=".5" value="'.$stock.'" min="0" name="stock['.$row['id'].']""></td></tr>';
          }

        }
        echo $fertilizertext;
        echo $pesticidetext;

        echo "</table>";
        ?>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-default" name="stocktakingForm" value="Submit">Küldés</button>
      </div>
      </form>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div class="modal fade" id="inventoryModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Új tetél</h4>
      </div>

      <form method="post" action="<?echo $_SERVER['PHP_SELF'];?>" accept-charset="utf-8">
      <div class="modal-body"> 
        
        <div class="row">
          <div class="col-md-6">
            <label for="date">Dátum</label>
            <div class="input-group date" data-provide="datepicker" data-date-format="yyyy-mm-dd" data-date-startDate="2017-01-01" data-date-autoclose = "true" data-date-todayHighlight = "true">
              <input type="date" class="form-control" name="date" value="<?echo date("Y-m-d");?>">
              <div class="input-group-addon">
                  <span class="glyphicon glyphicon-th"></span>
              </div>
            </div>
          </div>
        </div>

        <div class="row">
             
          <?php
          //get name of agent
          $query = $db->prepare("SELECT * FROM agents WHERE `main` = 1 ORDER BY `name`");
          $query->execute();

          $fertilizertext = "<div class='col-md-6'><table class='table table-striped'><tr class='title'><td>Trágya</td><td style='text-align: center;'>Mennyiség</td></tr>";
          $pesticidetext = "<div class='col-md-6'><table class='table table-striped'><tr class='title'><td>Permetszér</td><td style='text-align: center;'>Mennyiség</td></tr>";

          foreach ($query as $row) {
            $agent = $row['id'];
            $agentname = $row['name'];
            $price = $row['price'];
            $type = $row['type'];
            $stock = $row['stock'];
             
            if ($type == 1) {
              //text for fertilizers
              $fertilizertext .= "<tr><td style='width: 50%;'>".$agentname."</td>";
              //$fertilizertext .= '<td><input class="form-control" type="number" value="0" name="delivery['.$row['id'].'][1]"></td>';
              $fertilizertext .= '<td><input class="form-control" type="number" value="0" name="delivery['.$row['id'].'][2]""></td></tr>';
            }

            if ($type == 2) {
              //text for fertilizers
              $pesticidetext .= "<tr><td style='width: 50%;'>".$agentname."</td>";
              //$pesticidetext .= '<td><input class="form-control" type="number" value="0" name="delivery['.$row['id'].'][1]"></td>';
              $pesticidetext .= '<td><input class="form-control" type="number" value="0" name="delivery['.$row['id'].'][2]""></td></tr>';
            }
          }
          
          $fertilizertext .= "</table></div>";
          $pesticidetext .= "</table></div>";

          echo $fertilizertext;
          echo $pesticidetext;
        ?>
        </div> 

        <div class="row">
          <div class="col-md-12">
            <div class="more">
              <a class="btn btn-default" role="button" data-toggle="collapse" href="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
                Tovabbi termék
              </a>
            </div>

            <div class="collapse" id="collapseExample">
              <div class="row">
                <?php
                //get name of agent
                $query = $db->prepare("SELECT * FROM agents WHERE `main` = 0 ORDER BY `name`");
                $query->execute();

                $fertilizertext = "<div class='col-md-6'><table class='table table-striped'><tr class='title'><td>Trágya</td><td style='text-align: center;'>Mennyiség</td></tr>";
                $pesticidetext = "<div class='col-md-6'><table class='table table-striped'><tr class='title'><td>Permetszér</td><td style='text-align: center;'>Mennyiség</td></tr>";

                foreach ($query as $row) {
                  $agent = $row['id'];
                  $agentname = $row['name'];
                  $price = $row['price'];
                  $type = $row['type'];
                  $stock = $row['stock'];
                   
                  if ($type == 1) {
                    //text for fertilizers
                    $fertilizertext .= "<tr><td style='width: 50%;'>".$agentname."</td>";
                    //$fertilizertext .= '<td><input class="form-control" type="number" value="0" name="delivery['.$row['id'].'][1]"></td>';
                    $fertilizertext .= '<td><input class="form-control" type="number" value="0" name="delivery['.$row['id'].'][2]""></td></tr>';
                  }

                  if ($type == 2) {
                    //text for fertilizers
                    $pesticidetext .= "<tr><td style='width: 50%;'>".$agentname."</td>";
                    //$pesticidetext .= '<td><input class="form-control" type="number" value="0" name="delivery['.$row['id'].'][1]"></td>';
                    $pesticidetext .= '<td><input class="form-control" type="number" value="0" name="delivery['.$row['id'].'][2]""></td></tr>';
                  }
                }
                
                $fertilizertext .= "</table></div>";
                $pesticidetext .= "</table></div>";

                echo $fertilizertext;
                echo $pesticidetext;
              ?>
              </div>
            </div>
          </div>
        </div>

      </div>
      <div class="modal-footer more">
        <button type="submit" class="btn btn-success" name="deliveryForm" value="Submit">Küldés</button>
      </div>
      </form>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->


<div class="modal fade" id="saleModal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Új eladás</h4>
      </div>

      <form method="post" action="<?echo $_SERVER['PHP_SELF'];?>" accept-charset="utf-8">
      <div class="modal-body"> 
        
        <label for="date">Dátum</label>
        <div class="input-group date" data-provide="datepicker" data-date-format="yyyy-mm-dd" data-date-startDate="2017-01-01" data-date-autoclose = "true" data-date-todayHighlight = "true">
          <input type="date" class="form-control" name="date" value="<?echo date("Y-m-d");?>">
          <div class="input-group-addon">
              <span class="glyphicon glyphicon-th"></span>
          </div>
        </div>
        <br><br>

        <label for="customer">Vevő</label>
        <input class="form-control" type="text" name="customer">
        <br><br>

        <table class="table table-striped">
        <tr class='title'><td>Termék</td><td style='text-align: center;'>Netto Ár összes</td><td style='text-align: center;'>Mennyiség</td></tr>
        
        <?php
        //get name of agent
        $query = $db->prepare("SELECT * FROM agents ORDER BY `name`");
        $query->execute();

        $fertilizertext = "<tr class='subtitle'><td style ='padding-top: 25px;'>Trágya</td><td></td><td></td><td></td></tr>";
        $pesticidetext = "<tr class='subtitle'><td style ='padding-top: 25px;'>Permetszer</td><td></td><td></td><td></td></tr>";

        foreach ($query as $row) {
          $agent = $row['id'];
          $agentname = $row['name'];
          $price = $row['price'];
          $type = $row['type'];
          $stock = $row['stock'];
           
          if ($type == 1) {
            //text for fertilizers
            $fertilizertext .= "<tr><td style='width: 50%;'>".$agentname."</td>";
            $fertilizertext .= '<td><input class="form-control" type="number" value="0" name="sale['.$row['id'].'][1]"></td>';
            $fertilizertext .= '<td><input class="form-control" type="number" value="0" step=".01" name="sale['.$row['id'].'][2]""></td></tr>';
          }

          if ($type == 2) {
            //text for fertilizers
            $pesticidetext .= "<tr><td style='width: 50%;'>".$agentname."</td>";
            $pesticidetext .= '<td><input class="form-control" type="number" value="0" name="sale['.$row['id'].'][1]"></td>';
            $pesticidetext .= '<td><input class="form-control" type="number" value="0" step=".01" name="sale['.$row['id'].'][2]""></td></tr>';
          }

        }
        echo $fertilizertext;
        echo $pesticidetext;

        echo "</table>";
        ?>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-default" name="saleForm" value="Submit">Küldés</button>
      </div>
      </form>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

</div>

