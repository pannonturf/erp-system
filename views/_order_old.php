<?php 
//////////////////////////////////////
// Add missing orders from the past //
//////////////////////////////////////

// Only most important information

include('views/_header'.$header.'.php');
include('tools/functions.php');

require_once('config/config.php');
$db = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.'', DB_USER, DB_PASS);
$db -> exec("set names utf8");
date_default_timezone_set('Europe/Budapest');

$today = date("Y-m-d");

//////////
//If form has been sent
if (isset($_POST['orderForm'])) {
	$date = $_POST['moreDatum'];
	
	$time = "02:00:00";
	$planneddate = $date." ".$time;

	$name = $_POST['customer_id'];

	$amount = $_POST['amount'];
	$amount_encoded = encodeString($amount, $key);

	$type1 = $_POST['type1'];
	$type2 = $_POST['type2'];
	$field = 0;

	$delivery = 0;
	$deliveryname = "";
	$deliveryaddress = "";
	$deliverytime = "";
	$forwarder = "";

	$payment = 0;
	
	$email = "";
	$telephone = "";
	$invoicename = "";
	$invoiceaddress = "";
	$note = "";
	$note2 = "";

	$created = date("Y-m-d H:i:s");
	
	if (isset($_SESSION['userid'])) {
		$creator = $_SESSION['userid'];
	}
	else {
		$creator = $_COOKIE["userid"];
	}

	$status = 4;
	$paid = 1;
	$team = 1;
	$sort = 0;
           
	/////////////////////////////////
	$sql = "INSERT INTO `order` (`id`, `date`, `time`, `sort`, `name`, `amount1`, `type1`, `type2`, `field`, `delivery`, `deliveryname`, `deliveryaddress`, `deliverytime`, `forwarder`, `payment`, `invoicename`, `invoiceaddress`, `telephone`, `email`, `note`, `note2`, `created`, `creator`, `status`, `team`, `paid`, `planneddate`)  VALUES (NULL, :datum, :time, :sort, :name, :amount1, :type1, :type2, :field, :delivery, :deliveryname, :deliveryaddress, :deliverytime, :forwarder, :payment, :invoicename, :invoiceaddress, :telephone, :email, :note, :note2, :created, :creator, :status, :team, :paid, :planneddate);";

	$query = $db->prepare($sql);

	$query->bindParam(":datum", $date, PDO::PARAM_STR);  
	$query->bindParam(":time", $time, PDO::PARAM_STR); 
	$query->bindParam(":sort", $sort, PDO::PARAM_STR); 
	$query->bindParam(":name", $name, PDO::PARAM_STR);
	$query->bindParam(":amount1", $amount_encoded, PDO::PARAM_STR);
	$query->bindParam(":type1", $type1, PDO::PARAM_STR);
	$query->bindParam(":type2", $type2, PDO::PARAM_STR);
	$query->bindParam(":field", $field, PDO::PARAM_STR);
	$query->bindParam(":delivery", $delivery, PDO::PARAM_STR);
	$query->bindParam(":deliveryname", $deliveryname, PDO::PARAM_STR);
	$query->bindParam(":deliveryaddress", $deliveryaddress, PDO::PARAM_STR);
	$query->bindParam(":deliverytime", $deliverytime, PDO::PARAM_STR);
	$query->bindParam(":forwarder", $forwarder, PDO::PARAM_STR);
	$query->bindParam(":payment", $payment, PDO::PARAM_STR);
	$query->bindParam(":telephone", $telephone, PDO::PARAM_STR);
	$query->bindParam(":email", $email, PDO::PARAM_STR);
	$query->bindParam(":invoicename", $invoicename, PDO::PARAM_STR);
	$query->bindParam(":invoiceaddress", $invoiceaddress, PDO::PARAM_STR);
	$query->bindParam(":note", $note, PDO::PARAM_STR);
	$query->bindParam(":note2", $note2, PDO::PARAM_STR);
	$query->bindParam(":created", $created, PDO::PARAM_STR);
	$query->bindParam(":creator", $creator, PDO::PARAM_STR);
	$query->bindParam(":status", $status, PDO::PARAM_STR);
	$query->bindParam(":team", $team, PDO::PARAM_STR);
	$query->bindParam(":paid", $paid, PDO::PARAM_STR);
	$query->bindParam(":planneddate", $planneddate, PDO::PARAM_STR);

	$query->execute();
  
	//Back to frontpage
  	echo '<div class="alert alert-success center-block" role="alert">Sikerült!</div>';
}

?>


<div class="inputform">
	<div class="row">
		<div class="col-md-4">
		  <h3 style="margin-top:10px; color:red;">!!! RÈGI MEGRENDELÉS !!!</h3>  
		</div>
	</div>

	<form method="post" id="myForm" name="myForm" action="<?php echo $_SERVER['PHP_SELF']; ?>" accept-charset="utf-8" onsubmit="return validateForm3()">

	<?php
	//get last entry 
	$query = $db->prepare("SELECT * FROM `order` WHERE `status` = 4 ORDER BY `created` DESC");
	$query->execute(); 
	$result = $query->fetch(PDO::FETCH_OBJ);
	$lastdatum = $result->date;

	?>

	<div class="row first">
		<div class="col-md-3">
				<label for="orderdate">Dátum</label>
				<div id="orderdate" class="show">
					<input type='date' class="form-control date-field" name="moreDatum" max="<?php echo $today; ?>" value="<?php echo $lastdatum; ?>"><br>
				</div>
			</div>
			<div class="col-md-2"></div>

		</div>

		<div class="row first">
			<div class="col-md-3">
				<div class="form-group">
		          		<label for="customer">Vevő</label>
		          		<input class="form-control" type="text" name="customer" id="customer_input" required />
            			<input name="customer_id" id="customer_id" value="0" type="hidden"/>
		        </div>
		    </div>
		    <div class="col-md-7"><p id="message" style="margin-top: 15px;"></p></div>
		    <div class="col-md-2">
		        <br>
		        <button type="button" class="btn btn-default" data-toggle="modal" data-target="#newModal"><span class="glyphicon glyphicon-plus"></span>&nbsp;&nbsp; Új vevő</button>
		    </div>
		</div>

		<div class="row second">
			<div class="col-md-2">
				<div class="form-group">
	          		<label for="amount">Mennyiség (m&sup2;)</label>
	          		<input class="form-control" type="number" step="1" min="0" value="0" name="amount" required>
		        </div>
		    </div>
		    <div class="col-md-1"></div>
		    <div class="col-md-7">
		    	<label class="radio-inline">
				  	<input type="radio" name="type1" value="1" checked> Kistekercs
				</label>
				<label class="radio-inline">
				  	<input type="radio" name="type1" value="2"> Kistekercs sport
				</label>
				<label class="radio-inline">
				  	<input type="radio" name="type1" value="3"> Kistekercs vastag
				</label>

				<br>
				<label class="radio-inline">
				  	<input type="radio" name="type1" value="4"> Nagytekercs
				</label>
				<label class="radio-inline">
				  	<input type="radio" name="type1" value="5"> Nagytekercs 2,5 cm
				</label>
				<label class="radio-inline">
				  	<input type="radio" name="type1" value="6"> Nagytekercs 3 cm
				</label>
				<br><br>

				<label class="radio-inline">
				  	<input type="radio" name="type2" value="1" checked> Nórmal (Poa)
				</label>
				<label class="radio-inline">
				  	<input type="radio" name="type2" value="2"> Mediterrán
				</label>
				<br><br>

		    </div>
		</div>
	    <br><br>

	    <div class="row">
			<div class="col-md-2">
				<button type="submit" class="btn btn-primary" name="orderForm" value="Submit">Küldés</button>
			</div>
		</div>
    </form>


<!-- Modal for adding new customer -->
<div class="modal fade" id="newModal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document" style="width: 700px;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Új vevő</h4>
      </div>

      <div class="modal-body"> 
        
      	<form id="addForm" method="" action="" novalidate="novalidate">

        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label for="name">Név</label>
              <input type="text" id="customer_name">
            </div>
          </div>

          	<div class="col-md-6">
		    	<b>Szállítás - Standard</b><br>
		    	<label class="radio-inline">
				  	<input type="radio" name="delivery_standard" value="1" checked> ABH
				</label>
				<label class="radio-inline">
				  	<input type="radio" name="delivery_standard" value="2"> Szállítás
				</label>
				
				<br><br>

				<b>Fizetési mód - Standard</b><br>
				<label class="radio-inline">
				  	<input type="radio" name="payment_standard" value="1" checked> Kézpénz
				</label>
				<label class="radio-inline">
				  	<input type="radio" name="payment_standard" value="2"> Átutalás
				</label>

				<br><br>

				<div class="col-md-7" style="padding-left: 0px;">
					<div class="form-group">
		          		<label for="country">Ország</label>
				        <?php

			            echo '<select class="form-control" id="country">';
			            echo '<option value="0" selected>';
			            echo 'magyar';
			            echo "</option>";
			 
			            $i = 0;
			            $query = $db->prepare("SELECT * FROM countries");
			            $query->execute();
			            while($row = $query->fetch()) {
			                if ($i > 0) {
			                	echo "<option value='".$row['id']."'>".$row['name']."</option>";
			                }  
			                $i++;     
			            }

			            echo '</select>';
				        ?>
	        		</div>
	        	</div>

        	</div>
      </div>
      <div class="modal-footer">
      	<button type="button" class="btn btn-default" name="insert-data" id="insert-data" onclick="insertData()">Küldés</button>
      </div>
      </form>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->


	<br><br><br><br><br><br><br><br><br><br>

</div>
</div>
</div>

  


