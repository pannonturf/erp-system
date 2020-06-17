<?php
//////////////////////////////////////////////////
// Include deliverynote creation for sales.php  //
//////////////////////////////////////////////////   
?>

<div class="inputform">

<div class="row">
	<div class="col-md-10">
		<h3 style="margin-top:10px;">Szállítólevél</h3>
	</div>
	<div class="col-md-2">
		<?php
		// check if open orders were displayed to show again after closing the edit
		if (isset($_GET['open'])) {
			echo '<button type="button" style="font-size: 30px;margin-top: 20px;" data-href="'.$edit_link2.'open=1" class="close"><span aria-hidden="true">&times;</span></button>';
		}
		else {
			echo '<button type="button" style="font-size: 30px;margin-top: 20px;" data-href="'.$edit_link2.'" class="close"><span aria-hidden="true">&times;</span></button>';
		}
		?>
	</div>
</div>

<?php
// get data for order to be edited
$query = $db->prepare("SELECT * FROM `order` WHERE `id` = :id");
$query->bindParam(":id", $order_id, PDO::PARAM_STR);
$query->execute(); 

foreach ($query as $row) {
	$id2 = $row['id2'];
	$id3 = $row['id3'];
	$prefix = $row['prefix'];
	$datum = $row['date'];
	$time = $row['time'];
	$sort = $row['sort'];
	$time_original = $time;
	$timedisplay = substr($time, 0, 5);
	$planneddate = substr($row['planneddate'], 0, 16);
	$name = $row['name'];
	$type1_original = $row['type1'];
	$type2 = $row['type2'];
	$type3_original = $row['type3'];
	$pipes = $row['pipes'];
	$amount = getAmount(amount_decrypt($row['amount'], $key2), $type3_original, $modus);
	$amount_original = $amount;
	$field = $row['field'];
	$delivery = $row['delivery'];
	$forwarder = $row['forwarder'];
	$payment = $row['payment'];
	$paid = $row['paid'];
	$status = $row['status'];

	$deliveryname = $row['deliveryname'];
	$deliveryaddress = $row['deliveryaddress'];
	$country = $row['country'];
	$city = $row['city'];
	$telephone = $row['telephone'];
	$email = $row['email'];
	$invoicename = $row['invoicename'];
	$invoiceaddress = $row['invoiceaddress'];
	$invoicenumber = $row['invoicenumber'];
	$note = $row['note'];
	$note2 = $row['note2'];
	$licenceplate = $row['licence'];
	$deliverynote = $row['deliverynote'];

	// Get ekaer from deliverynotes
	$query = $db->prepare("SELECT * FROM `deliverynotes` WHERE `id` = :id");
	$query->bindParam(":id", $deliverynote, PDO::PARAM_STR);
	$query->execute(); 
	$result = $query->fetch(PDO::FETCH_OBJ);
	$ekaer = $result->ekaer;
	$lang = $result->lang;
	$company = $result->company;
	$zurrgurt = $result->zurrgurt;
	$showtime = $result->showtime;
	$note = $result->note;

	if ($ekaer == 1111) {
    	$ekaer_disp = "";
    }
    else {
    	$ekaer_disp = $ekaer;
    }

	$query = $db->prepare("SELECT * FROM cities WHERE `id` = :id");
    $query->bindParam(":id", $city, PDO::PARAM_STR);
    $query->execute();
    $result = $query->fetch(PDO::FETCH_OBJ);
	$city_disp = $result->name;
	$plz = $result->plz;

	// Get customer data
	$query = $db->prepare("SELECT * FROM customers WHERE `id` = :id");
	$query->bindParam(":id", $name, PDO::PARAM_STR);
	$query->execute();
	$result = $query->fetch(PDO::FETCH_OBJ);
	$customer_name = $result->name;
	$contactperson = $result->contactperson;
	$customer_street = $result->street;
	$customer_plz = $result->plz;
	$customer_city = $result->city;
	$customer_country = $result->country;
	$customer_phone = $result->phone;
	$customer_email = $result->email;

	// get country data
	$query = $db->prepare("SELECT * FROM countries WHERE `id` = :id");
    $query->bindParam(":id", $customer_country, PDO::PARAM_STR);
    $query->execute();
    $result = $query->fetch(PDO::FETCH_OBJ);
	$country_disp = $result->name2;
	$country_disp_en = $result->international;

	if (isset($_GET['open'])) {
		$target = $edit_link2.'open=1';
	}
	else {
		$target = $edit_link2;
	}

	?>

	<form method="post" action="deliverynote2.php" accept-charset="utf-8" target="_blank">

	<div class="row modal_row">
		<div class="col-md-4">
			<div class="form-group">
				<div class="col-sm-11" style="padding-bottom: 10px;">
				  <input type="text" class="form-control" name="customer_name" value="<?echo $customer_name;?>">
				</div>
			</div>
	        <br>
	        <div class="form-group">
				<div class="col-sm-4" style="padding-bottom: 10px;">
				  <input type="number" class="form-control" name="customer_plz" value="<?echo $customer_plz;?>">
				</div>
				<div class="col-sm-7" style="padding-bottom: 10px;">
				  <input type="text" class="form-control" name="customer_city" value="<?echo $customer_city;?>">
				</div>
			</div>
			<br>
	        <div class="form-group">
				<div class="col-sm-11" style="padding-bottom: 10px;">
				  <input type="text" class="form-control" name="customer_street" value="<?echo $customer_street;?>">
				</div>
			</div>
	    </div>
	    <div class="col-md-2"></div>

	    <div class="col-md-3">
	    	<div class="form-group">
				<div class="col-sm-6" style="padding-bottom: 0px;">
				  	<p><b>Kapcsolattartó</b></p>
				</div>    			
	        </div>
	        <br>
	        <div class="form-group">
				<div class="col-sm-12" style="padding-bottom: 10px;">
				  <input type="text" class="form-control" name="contactperson" value="<?echo $contactperson;?>">
				</div>
			</div>
			<br>
	        <div class="form-group">
				<div class="col-sm-12" style="padding-bottom: 10px;">
				  <input type="text" class="form-control" name="customer_phone" value="<?echo $customer_phone;?>">
				</div>
			</div>
	    </div>
	</div>

	<div class="row modal_row">
		<div class="col-md-6" style="padding-bottom: 0;">
			<div class="form-horizontal">
				<br>
				
				<div class="form-group">
	          		<label for="country1" class="col-sm-4 control-label">Ország</label>
	          		<div class="col-sm-4" style="padding-bottom: 10px;">
				        <?php

	    				$query = $db->prepare("SELECT * FROM countries WHERE `id` = :id");
	    				$query->bindParam(":id", $country, PDO::PARAM_STR);
			            $query->execute();
			            $result = $query->fetch(PDO::FETCH_OBJ);
	    				$countryname = $result->name2;

	    				if ($country > 0 AND $city == 0) {
			            	echo '<select class="form-control" id="country1" name="country1" onchange="countryFunction2()">';
			            }
			            else {
			            	echo '<select class="form-control" id="country1" name="country1" onchange="countryFunction2()">';
			            }
			            echo '<option value="'.$country.'" selected>';
			            echo $countryname;
			            echo "</option>";
			 
			            $query = $db->prepare("SELECT * FROM countries WHERE (`type` = 1 OR `type` = 3) ORDER BY `name2` ASC");
			            $query->execute();
			            while($row = $query->fetch()) {
			                if ($country != $row['id']) {
			                	echo "<option value='".$row['id']."'>".$row['name2']."</option>";
			                }
			            }

			            echo '</select>';
				        ?>

				    </div>
        		</div>

				<div class="form-group">
					<label for="deliveryaddress" class="col-sm-4 control-label" style="padding-bottom: 10px;">Szállítási cím</label>
					<div class="col-sm-8" style="padding-bottom: 10px;">
					  <input type="text" class="form-control" name="deliveryaddress" id="deliveryaddress" value="<?echo $deliveryaddress;?>">
					</div>
				</div>

				<?php
				if ($country == 0 AND $city > 0) {
					echo '<div id="plz-group" class="form-group show">';
				}
				else {
					echo '<div id="plz-group" class="form-group hide">';
				}
				?>
		          		<label for="customer" class="col-sm-4 control-label" style="padding-bottom: 10px;">&nbsp;</label>
		          		<div class="col-sm-6" style="padding-bottom: 10px;">
		          			<input class="form-control" id="plz_input" type="text" name="plz" value="<?echo $plz." ".$city_disp;?>"/>
		          		</div>

            			<input name="city_id" id="city_id" value="<?php echo $city; ?>" type="hidden"/>
		        	</div>

		        <?php
				if ($type1_original > 3) {
				?>
			        <br>
		         	<div class="form-group">                         
		          		<label for="pipes" class="col-sm-4 control-label">db. / Csövek</label>
		          		<div class="col-sm-2" style="padding-bottom: 10px;">
		          			<input type="number" class="form-control" name="pipes" value="<?echo $pipes;?>">
		          		</div>
		        	</div>
		        <?php
				}
				?>
	      
				
			</div>
	    </div>

	    <div class="col-md-5" style="padding-bottom: 0;">
	    	<div class="form-horizontal">
				
				<?php
				/*
				<div class="form-group">                         
		          	<label for="time" class="col-sm-5 control-label" style="padding-bottom: 10px;">Szállítási időpont</label>
		          	<div class="col-sm-3" style="padding-bottom: 10px;">
		          		<input type="time" class="form-control" name="deliverytime" style="padding-top: 0;" value="<?echo $timedisplay;?>">
		        	</div>
		        </div>
		        <br>
		    	*/
		    	?>
		        <div class="form-group">                         
		          	<label for="time" class="col-sm-5 control-label" style="padding-bottom: 10px;">Rendszám</label>
		          	<div class="col-sm-5" style="padding-bottom: 10px;">
		          		<input type="text" class="form-control" name="licence" value="<?echo $licenceplate;?>">
		        	</div>
		        </div>
		        <br>
		        <div class="form-group">                         
		          	<label for="time" class="col-sm-5 control-label" style="padding-bottom: 10px;">EKAÉR szám</label>
		          	<div class="col-sm-7" style="padding-bottom: 10px;">
	          			<input type="text" class="form-control" name="ekaer" value="<?echo $ekaer_disp;?>">
		        	</div>
		        </div>
		    </div>
		</div>
	</div>

	<div class="row modal_row">
	    	<div class="col-md-1"></div>
	      	<div class="col-md-2">
	      		<?php
	      		if ($deliverynote == 0) {
		      		if ($customer_country == 0) {		// Hungary
		      			$hu = "checked";
		      			$de = "";
		      			$en = "";
		      			$kft = "checked";
		      			$gmbh = "";
		      		}
		      		elseif ($customer_country == 2) {	// Austria
		      			$hu = "";
		      			$de = "checked";
		      			$en = "";
		      			$kft = "checked";
		      			$gmbh = "";
		      		}
		      		elseif ($customer_country == 10 OR $customer_country == 12) {		// Germany and Switzerland
		      			$hu = "";
		      			$de = "checked";
		      			$en = "";
		      			$kft = "";
		      			$gmbh = "checked";
		      		}
		      		else {				// English
		      			$hu = "";
		      			$de = "";
		      			$en = "checked";
		      			$kft = "checked";
		      			$gmbh = "";
		      		}
		      	}
		      	else {
		      		if ($lang == 1) {
		      			$hu = "checked";
		      			$de = "";
		      			$en = "";
		      		}
		      		elseif ($lang == 2) {
		      			$hu = "";
		      			$de = "checked";
		      			$en = "";
		      		}
		      		elseif ($lang == 3) {
		      			$hu = "";
		      			$de = "";
		      			$en = "checked";
		      		}

		      		if ($company == 1) {
		      			$kft = "checked";
		      			$gmbh = "";
		      		}
		      		elseif ($company == 2) {
		      			$kft = "";
		      			$gmbh = "checked";
		      		}

		      	}

	      		?>


	    		<div class="radio">
				  <label>
				    <input type="radio" name="langRadios" id="langRadios1" value="1" <?echo $hu;?>>
				    Magyarul
				  </label>
				</div>
				<div class="radio">
				  <label>
				    <input type="radio" name="langRadios" id="langRadios2" value="2" <?echo $de;?>>
				    Németül
				  </label>
				</div>
				<div class="radio">
				  <label>
				    <input type="radio" name="langRadios" id="langRadios3" value="3" <?echo $en;?>>
				    Angolul
				  </label>
				</div>
			</div>
			<div class="col-md-3">
	    		<div class="radio">
				  <label>
				    <input type="radio" name="companyRadios" id="companyRadios1" value="1" <?echo $kft;?>>
				   Pannon Turfgrass Kft.
				  </label>
				</div>
				<div class="radio">
				  <label>
				    <input type="radio" name="companyRadios" id="companyRadios2" value="2" <?echo $gmbh;?>>
				    Pannon Turfgrass GmbH.
				  </label>
				</div>
			</div>
			<div class="col-md-3">
				<div class="form-group">
		            <label for="note">Jegyzet</label>
		            <textarea class="form-control" name="note" rows="2"><?echo $note;?></textarea>
		        </div>
			</div>
		</div>

		<input type="hidden" name="order_id" value="<?echo $order_id;?>">
		<input type="hidden" name="deliverynote" value="<?echo $deliverynote;?>">
		<input type="hidden" name="datum" value="<?echo $datum;?>">
		<input type="hidden" name="amount" value="<?echo $amount;?>">
		<input type="hidden" name="country_disp" value="<?echo $country_disp;?>">
	    <input type="hidden" name="country_disp_en" value="<?echo $country_disp_en;?>">
	    <input type="hidden" name="deliverytime" value="<?echo $time;?>">
	    <input type="hidden" name="customer" value="<?echo $name;?>">
	    <input type="hidden" name="type1" value="<?echo $type1_original;?>">
	    <input type="hidden" name="type2" value="<?echo $type2;?>">
	    <input type="hidden" name="type3" value="<?echo $type3_original;?>">
	    <input type="hidden" name="prefix" value="<?echo $prefix;?>">
	    <input type="hidden" name="id2" value="<?echo $id2;?>">
	    <input type="hidden" name="id3" value="<?echo $id3;?>">
	    <input type="hidden" name="invoicenumber" value="<?echo $invoicenumber;?>">


		<br><br>
		<button type="submit" class="btn btn-primary center-block" name="deliveryNote" value="Submit">Mutassa a szállítólevélet</button>
	</div>
	</form>

	<br><br><br><br><br><br><br><br><br><br><br><br><br><br>

<?php
}
?>

