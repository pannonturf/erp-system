<?php
///////////////////////////////////////
// Single rows for trucks in project //
///////////////////////////////////////

foreach ($query as $row) {
	$truck_id = $row['id'];
	$sort = $row['sort'];
	$time = substr($row['time'], 0, 5);
	$note = $row['note'];
	$truck_status = $row['status'];
	$licence1 = $row['licence1'];
	$licence2 = $row['licence2'];
	$amount = $row['amount'];
	$pipes = $row['pipes'];
	$pallets = $row['pallets'];
	$come_day = substr($row['come'], 0, 10);
	$come_time = substr($row['come'], 11, 5);
	$go = substr($row['go'], 11, 5);
	$deliverynote = $row['deliverynote'];

	// Get ekaer from deliverynotes
	$query = $db->prepare("SELECT * FROM `deliverynotes` WHERE `id` = :id");
	$query->bindParam(":id", $deliverynote, PDO::PARAM_STR);
	$query->execute(); 
	$result = $query->fetch(PDO::FETCH_OBJ);
	$ekaer = $result->ekaer;

	$deliverytime = substr($row['deliverytime'],0,5);

	$current_amount = ceil($pipes * $length * 1.2);

	if ($come_day == $today) {		
		$come_disp = $come_time;
	}
	else {		// show that truck came on another day
		$come_disp = $come_time." (".substr($come_day, 5, 5).")";
	}

	if ($truck_status == 1) {		// planned
		echo '<tr class="truck_center"><td></td><td style="padding-top: 8px !important;"><input type="time" class="form-control truck_time" onfocusout="truckTimeFunction('.$truck_id.')" id="plannedtime'.$truck_id.'" min="7:00" max="20:00" value="'.$time.'" /></td>';
	} 
	elseif ($truck_status == 2) {	// arrived
		echo '<tr><td></td><td style="padding-left: 24px;">'.$come_disp.'</td>';
	} 
	elseif ($truck_status == 3) {	// gone
		echo '<tr><td></td><td style="padding-left: 24px; color:green;">'.$go.'</td>';
	} 

	echo "<td>".$sort."</td>";
	echo "<td>".$amount." m&sup2;</td>";
	echo "<td>".$pipes." db.</td>";

	if ($cooling == 1) {		// pallets only for cooling trucks
		echo "<td>".$pallets." db.</td>";
	}

	// show licence plates
	if ($licence1 == "" AND $licence2 == "") {	
		echo "<td></td>";
	}
	else {
		echo "<td>".$licence1." / ".$licence2."</td>";
	}

	echo "<td><i>".$note."</i></td>";


	if (empty($ekaer)) {	// no delivery note created yet
		$ekaer_btn = '<button class="btn btn-info btn-xs" onclick="stopRefresh()" style="float: right; margin-right: 5px;" role="group" data-toggle="modal" data-target="#deliveryNote'.$truck_id.'" data-id="'.$truck_id.'">Szállítólevél</span></button>';
		$go_btn = "";
	}
	else {					
		// show delivery note
		$ekaer_btn = '<button class="btn btn-info btn-xs" onclick="stopRefresh()" style="float: right; margin-right: 5px; width: 50px;" role="group" data-toggle="modal" data-target="#deliveryNote'.$truck_id.'" data-id="'.$truck_id.'"><span class="glyphicon glyphicon-print"></span></button>';
		
		if ($truck_status == 2) {		// button for leaving
			$go_btn = '<button class="btn btn-success btn-xs" style="float: right; margin-right: 5px;" onclick="truckLeave('.$truck_id.')"><span class="glyphicon glyphicon-home"> <span class="glyphicon glyphicon-arrow-right"></span></button>';
		}
		else {
			$go_btn = "";
		}
	}
	
	// show right buttons
	if ($truck_status == 1) {
		echo '<td class="truck_btn"><button class="btn btn-warning btn-xs" onclick="stopRefresh()" style="float: right; margin-right: 5px" role="group" data-toggle="modal" data-target="#truckArrive'.$truck_id.'" data-id="'.$truck_id.'"><span class="glyphicon glyphicon-arrow-right"> <span class="glyphicon glyphicon-home"></button></td>';
		echo '<td class="truck_btn">'.$ekaer_btn.'</td>';
		echo '<td class="truck_btn">'.$go_btn.'</td>';
	} 
	elseif ($truck_status == 2) {
		echo '<td class="truck_btn"></td>';
		echo '<td class="truck_btn">'.$ekaer_btn.'</td>';
		echo '<td class="truck_btn">'.$go_btn.'</td>';
	} 
	elseif ($truck_status == 3) {
		echo '<td class="truck_btn"></td>';
		echo '<td class="truck_btn">'.$ekaer_btn.'</td>';
		echo '<td class="truck_btn" style="text-align: center;"><span class="glyphicon glyphicon-ok"></span></td>';
	} 

	echo '<td><button type="button" class="btn btn-default btn-xs" onclick="stopRefresh()" style="float: right;" role="group" data-toggle="modal" data-target="#truckEdit'.$truck_id.'"><span class="glyphicon glyphicon-pencil"></span></button>';
	echo "</td></tr>";
	?>

	<!-- MODAL -->
	<!-- Truck arrives -->
	<div class="modal fade" id="truckArrive<?php echo $truck_id; ?>" tabindex="-1" role="dialog">
	<div class="modal-dialog" role="document" style="width: 500px;">
	  <div class="modal-content">
	    <div class="modal-header">
	      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	      <h4 class="modal-title">Kamion érkezése</h4>
	    </div>

	    <?php

	    ?>

	    <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" accept-charset="utf-8">
	    <div class="row">
	    	<div class="col-md-12"><br>
	    		<p style="padding-left: 23px;">Kamion 
		    	<?php
				echo $sort; 
				?>
				</p>
			</div>
		</div>
		<div class="row">
	      <div class="col-md-6">
	        <div class="form-group">                         
	          	<label for="licence1">Rendszám 1</label>
	          	<input type="text" class="form-control" name="licence1" value="<?echo $licence1;?>">
	        </div>
	      </div>
	      <div class="col-md-6">
	         <div class="form-group">                         
	          	<label for="licence2">Rendszám 2</label>
	          	<input type="text" class="form-control" name="licence2" value="<?echo $licence2;?>">
	        </div>
	      </div> 
	    </div>

	    <input type="hidden" name="truck_id" value="<?echo $truck_id;?>">
	    <input type="hidden" name="projectid" value="<?echo $projectid;?>">

	    <div class="modal-footer">
	      <button type="submit" class="btn btn-primary center-block" name="truckArrive" value="Submit">Befejezés</button>
	    </div>
	    </form>
	  </div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->


	<!-- MODAL -->
	<!-- Edit truck -->
	<div class="modal fade" id="truckEdit<?php echo $truck_id; ?>" tabindex="-1" role="dialog">
	<div class="modal-dialog" role="document" style="width: 600px;">
	  <div class="modal-content">
	    <div class="modal-header">
	      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	      <h4 class="modal-title">Szerkesztés</h4>
	    </div>

	    <?php

	    ?>

	    <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" accept-charset="utf-8">
	    <div class="row">
	    	<div class="col-md-7"><br>
	    		<p style="padding-left: 23px;"><b>Kamion 
		    	<?php
				echo $sort; 
				?>
				</b>
				</p>
			</div>

			<div class="col-md-4" style="padding-bottom: 0px;"><br>
		    	<div class="form-group">
	          		<label for="status">Státusz</label>
			        			
			        <?php
		        	echo '<select class="form-control" name="truck_status">';
		        	if ($truck_status == 1) {
			        	 echo '<option value="1" selected>Tervezett</option>';
			        	 echo '<option value="2">Érkezett</option>';
			        	 echo '<option value="3">Befejezett</option>';
			        }
			        elseif ($truck_status == 2) {
			        	 echo '<option value="1">Tervezett</option>';
			        	 echo '<option value="2" selected>Érkezett</option>';
			        	 echo '<option value="3">Befejezett</option>';
			        }
			        elseif ($truck_status == 3) {
			        	 echo '<option value="1">Tervezett</option>';
			        	 echo '<option value="2">Érkezett</option>';
			        	 echo '<option value="3" selected>Befejezett</option>';
			        }
					echo '</select>';			        
					?>
        		</div>
		    </div>
		</div>

	    <div class="row">
			<div class="col-md-1"></div>
	      
	      <div class="col-md-3">
	         <div class="form-group">                         
	          	<label for="pipes">db. / Csövek</label>
	          	<input type="number" class="form-control" name="pipes" id="pipes2_<?echo $truck_id;?>" onchange="updateCalculation2(<?echo $truck_id;?>)" value="<?echo $pipes;?>">
	        </div>
	      </div> 

	      <?php 
	      if ($cooling == 1) {
	      	?>
	      	<div class="col-md-3">
	         <div class="form-group">                         
	          	<label for="pallets">Raklapok</label>
	          	<input type="number" class="form-control" name="pallets" value="<?echo $pallets;?>">
	        </div>
	      </div> 
	      <?php
	      }
	      ?>
	    </div>

	    <div class="row">
			<div class="col-md-1"></div>
	      
	      <div class="col-md-3">
	         <div class="form-group">                         
	          	<label for="amount">m&sup2;</label>
	          	<input type="number" class="form-control" id="finalamount2_<?echo $truck_id;?>" name="amount" value="<?echo $amount;?>">
	        </div>
	      </div> 

	      <div class="col-md-4">
	        <div class="form-group">                         
	          	<br>
	          	<p id="calculation2_<?echo $truck_id;?>">
	          	<?php
	          	echo $pipes." db <b>x</b> ".$length." m <b>x</b> 1.2 m =<br><b>".$current_amount." m&sup2;</b>";
	          	?>

	          	</p>
	        </div>
	      </div>

	    </div>

	    <div class="row">
			<div class="col-md-1"></div>
	      <div class="col-md-5">
	        <div class="form-group">                         
	          	<label for="licence1">Rendszám 1</label>
	          	<input type="text" class="form-control" name="licence1" value="<?echo $licence1;?>">
	        </div>
	      </div>
	      <div class="col-md-5">
	         <div class="form-group">                         
	          	<label for="licence2">Rendszám 2</label>
	          	<input type="text" class="form-control" name="licence2" value="<?echo $licence2;?>">
	        </div>
	      </div> 
	    </div>

	    <div class="row">
	    	<div class="col-md-3"></div>
		    <div class="col-md-6">
		        <div class="form-group">
		            <label for="exampleTextarea">Jegyzet</label>
		            <textarea class="form-control" name="note" rows="3" placeholder=""><?echo $note;?></textarea>
		        </div>
	        </div>
	    </div>


	    <input type="hidden" name="truck_id" value="<?echo $truck_id;?>">
	    <input type="hidden" id="truck_length<?echo $truck_id;?>" value="<?echo $length;?>">
	    <input type="hidden" name="projectid" value="<?echo $projectid;?>">
	    

	    <div class="modal-footer">
	      <button type="submit" class="btn btn-primary center-block" name="truckEdit" value="Submit">Befejezés</button>
	    </div>
	    </form>
	  </div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->


	<!-- MODAL -->
	<!-- Create delivery note -->
	<div class="modal fade" id="deliveryNote<?php echo $truck_id; ?>" tabindex="-1" role="dialog">
	<div class="modal-dialog" role="document" style="width: 600px;">
	  <div class="modal-content">
	    <div class="modal-header">
	      <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="window.location.reload()"><span aria-hidden="true">&times;</span></button>
	      <h4 class="modal-title">Szállítólevél</h4>
	    </div>

	    <?php
	    if ($ekaer == 1111) {
	    	$ekaer_disp = "";
	    }
	    else {
	    	$ekaer_disp = $ekaer;
	    }

	    
	    ?>

	    <form method="post" action="deliverynote.php" accept-charset="utf-8" target="_blank">

	    <div class="row">
	    	<div class="col-md-12"><br>
	    		<p style="padding-left: 23px;"><b>Kamion 
		    	<?php
				echo $sort; 
				?>
				</b>
				</p>
			</div>
		</div>

		<div class="row">
			<div class="col-md-1"></div>
	      
	     	<div class="col-md-3">
		      	<div class="form-group">
					<label for="datum">Dátum</label>
					<input type="date" class="form-control" style="padding:0px 10px;" name="datum" value="<?echo $datum;?>">
				</div>
	      	</div> 
	    </div> 

		<div class="row">
			<div class="col-md-1"></div>
	      
	      <div class="col-md-3">
	         <div class="form-group">                         
	          	<label for="pipes">db. / Csövek</label>
	          	<input type="number" class="form-control" name="pipes" id="pipes<?echo $truck_id;?>" onchange="updateCalculation(<?echo $truck_id;?>)" value="<?echo $pipes;?>">
	        </div>
	      </div> 

	      <div class="col-md-3">
	        <div class="form-group">                         
	          	<label for="length">Hosszúság</label>
	          	<input type="number" class="form-control" name="length" id="length<?echo $truck_id;?>" onchange="updateCalculation(<?echo $truck_id;?>)" step="0.1" value="<?echo $length;?>">
	        </div>
	      </div>

	      <?php 
	      if ($cooling == 1) {
	      	?>
	      	<div class="col-md-3">
	         <div class="form-group">                         
	          	<label for="pallets">Raklapok</label>
	          	<input type="number" class="form-control" name="pallets" value="<?echo $pallets;?>">
	        </div>
	      </div> 
	      <?php
	      }

	      ?>

	    </div>

	    <div class="row">
			<div class="col-md-1"></div>
	      
	      <div class="col-md-3">
	         <div class="form-group">                         
	          	<label for="amount">m&sup2;</label>
	          	<input type="number" class="form-control" id="finalamount<?echo $truck_id;?>" name="amount" value="<?echo $amount;?>">
	        </div>
	      </div> 

	      <div class="col-md-4">
	        <div class="form-group">                         
	          	<br>
	          	<p id="calculation<?echo $truck_id;?>">
	          	<?php
	          	echo $pipes." db <b>x</b> ".$length." m <b>x</b> 1.2 m =<br><b>".$current_amount." m&sup2;</b>";
	          	?>

	          	</p>
	        </div>
	      </div>

	    </div>

		<div class="row">
			<div class="col-md-1"></div>
	      <div class="col-md-5">
	        <div class="form-group">                         
	          	<label for="licence1">Rendszám 1</label>
	          	<input type="text" class="form-control" name="licence1" value="<?echo $licence1;?>">
	        </div>
	      </div>
	      <div class="col-md-5">
	         <div class="form-group">                         
	          	<label for="licence2">Rendszám 2</label>
	          	<input type="text" class="form-control" name="licence2" value="<?echo $licence2;?>">
	        </div>
	      </div> 
	    </div>

	    <div class="row">
	    	<div class="col-md-1"></div>
	      <div class="col-md-7">
	        <div class="form-group">                         
	          	<label for="ekaer">EKAÉR szám</label>
	          	<input type="text" class="form-control" name="ekaer" value="<?echo $ekaer_disp;?>">
	        </div>
	      </div>
	      <div class="col-md-3">
	         <div class="form-group">                         
	          	<label for="licence2">Szállítási időpont</label>
	          	<input type="time" class="form-control" style="padding-top: 0;" name="deliverytime" value="<?echo $deliverytime;?>">
	        </div>
	      </div> 
	    </div>

	    <div class="row">
	    	<div class="col-md-1"></div>
	      	<div class="col-md-3">
	      		<?php
	      		if ($customer_country == 0) {		// Hungary
	      			$hu = "checked";
	      			$de = "";
	      			$en = "";
	      			$kft = "checked";
	      			$gmbh = "";
	      			$zurrgurt_standard = "";
	      		}
	      		elseif ($customer_country == 2) {	// Austria
	      			$hu = "";
	      			$de = "checked";
	      			$en = "";
	      			$kft = "checked";
	      			$gmbh = "";
	      			$zurrgurt_standard = "checked";
	      		}
	      		elseif ($customer_country == 10 OR $customer_country == 12) {		// Germany and Switzerland
	      			$hu = "";
	      			$de = "checked";
	      			$en = "";
	      			$kft = "";
	      			$gmbh = "checked";
	      			$zurrgurt_standard = "checked";
	      		}
	      		else {				// English
	      			$hu = "";
	      			$de = "";
	      			$en = "checked";
	      			$kft = "checked";
	      			$gmbh = "";
	      			$zurrgurt_standard = "";
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
			<div class="col-md-5">
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
			<div class="col-md-2">
				<div class="checkbox">
				  <label>
				    <input type="checkbox" name="zurrgurt" value="1" <?echo $zurrgurt_standard;?>>
				    Zurrgurt
				  </label>
				</div>
				<div class="checkbox">
				  <label>
				  	<?php 
				  	if ($cooling == 1) {
				  		echo '<input type="checkbox" name="showtime" value="1" checked>';
				  	}
				  	else {
				  		echo '<input type="checkbox" name="showtime" value="1">';
				  	}
				  	?>
				    Időpont
				  </label>
				</div>
			</div>
		</div>


	    <input type="hidden" name="customer_name" value="<?echo $customer_name;?>">
	    <input type="hidden" name="customer_street" value="<?echo $customer_street;?>">
	    <input type="hidden" name="customer_plz" value="<?echo $customer_plz;?>">
	    <input type="hidden" name="customer_city" value="<?echo $customer_city;?>">
	    <input type="hidden" name="country_disp" value="<?echo $country_disp;?>">
	    <input type="hidden" name="country_disp_en" value="<?echo $country_disp_en;?>">
	    <input type="hidden" name="telephone" value="<?echo $telephone;?>">
	    <input type="hidden" name="deliveryaddress" value="<?echo $deliveryaddress;?>">
	    <input type="hidden" name="sort" value="<?echo $sort;?>">
	    <input type="hidden" name="type1" value="<?echo $type1;?>">
	    <input type="hidden" name="cooling" value="<?echo $cooling;?>">
	    <input type="hidden" name="truck_id" value="<?echo $truck_id;?>">


	    <div class="modal-footer">
	      <button type="submit" class="btn btn-primary center-block" name="deliveryNote" value="Submit">Mutassa a szállítólevélet</button>
	    </div>
	    </form>
	  </div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->


	<?php
}