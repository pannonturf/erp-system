<?php 
////////////////////////////////////////////////////////////
// Include edit, delete and finish modals for project.php //
////////////////////////////////////////////////////////////
?>

<!-- MODAL -->
<!-- Edit project -->
<div class="modal fade" id="editProjectModal" tabindex="-1" role="dialog">
<?php
if ($mobile == 1) {		// smaller view for mobile
	echo '<div class="modal-dialog" role="document">';
}
else {
	echo '<div class="modal-dialog" role="document" style="width: 1200px;">';
}
?>
    <div class="modal-content">
      	<div class="modal-header">
        	<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        	<h4 class="modal-title">Módosítás</h4>
      	</div>

      	<form method="post" action="<?php echo $modal_action; ?>" accept-charset="utf-8">

		<div class="row modal_row">
			<div class="col-md-5">
			</div>

			<div class="col-md-5"><br>
				<div class="form-horizontal">
					<div class="form-group" style="margin-top:4px;">
						<label for="projectname" class="col-sm-4 control-label" style="padding-bottom: 10px;">Projekt név</label>
						<div class="col-sm-6" style="padding-bottom: 10px;">
						  <input type="text" class="form-control" id="projectname" name="projectname" value="<?echo $projectname;?>">
						</div>
					</div>
				</div>
			</div>

			<div class="col-md-2" style="padding-bottom: 0px;">
		    	<div class="form-group">
	          		<label for="status">Státusz</label>
			        			
			        <?php
		        	echo '<select class="form-control" name="project_status">';
		        	if ($project_status == 0) {
			        	 echo '<option value="0" selected>Tervezett</option>';
			        	 echo '<option value="1">Megrendelt</option>';
			        	 echo '<option value="2">Vágás befejezett</option>';
			        	 echo '<option value="3">Üzem befejezett</option>';
			        }
			        elseif ($project_status == 1) {
			        	 echo '<option value="0">Tervezett</option>';
			        	 echo '<option value="1" selected>Megrendelt</option>';
			        	 echo '<option value="2">Vágás befejezett</option>';
			        	 echo '<option value="3">Üzem befejezett</option>';
			        }
			        elseif ($project_status == 2) {
			        	 echo '<option value="0">Tervezett</option>';
			        	 echo '<option value="1">Megrendelt</option>';
			        	 echo '<option value="2" selected>Vágás befejezett</option>';
			        	 echo '<option value="3">Üzem befejezett</option>';
			        }
			        elseif ($project_status == 3) {
			        	 echo '<option value="0">Tervezett</option>';
			        	 echo '<option value="1">Megrendelt</option>';
			        	 echo '<option value="2">Vágás befejezett</option>';
			        	 echo '<option value="3" selected>Üzem befejezett</option>';
			        }
			        echo '<input type="hidden" name="status" value="'.$status.'">';

		            echo '</select>';
					
					if ($datum_type == 0) {
					?>
					<div style="margin-top: 15px;">
						<label class="radio-inline">
						  <input type="radio" name="datum_type" value="0" checked> Nap
						</label>
						<label class="radio-inline">
						  <input type="radio" name="datum_type" value="1"> Hónap
						</label>
					</div>
					<?php
					}
					elseif ($datum_type == 1) {
					?>
					<div style="margin-top: 15px;">
						<label class="radio-inline">
						  <input type="radio" name="datum_type" value="0"> Nap
						</label>
						<label class="radio-inline">
						  <input type="radio" name="datum_type" value="1" checked> Hónap
						</label>
					</div>
					<?php
					}
					?>
        		</div>
		    </div>
		</div>

		<div class="row modal_row">
			<div class="col-md-6">
				<div class="form-group">
					<?php
					$query = $db->prepare("SELECT * FROM customers WHERE `id` = :id");
					$query->bindParam(":id", $name, PDO::PARAM_STR);
					$query->execute();
					$result = $query->fetch(PDO::FETCH_OBJ);
					$customer_name = $result->name;
					?>

					<label for="customer" class="col-sm-4 control-label" style="padding-bottom: 0px; padding-left: 0px;">Vevő</label>
					<div class="col-sm-5" style="padding-bottom: 0px;">
					  	<p><?echo $customer_name;?></p>
					</div>    			
		        </div>
		    </div>

		</div>

		<div class="row modal_row">
			<div class="col-md-2">
				<div class="form-group">
	          		<label for="amount">Mennyiség (m&sup2;)</label>
	          			<?php
	          			$amount_disp = intval($amount_total);
						echo '<input class="form-control" type="number" step="1" min="0" value="'.$amount_disp.'" name="amount">';
						?>	          		
		        </div>

		        <br>
		        <div class="form-group">
	          		<label for="amount">Hosszúság (m)</label>
	          		<input class="form-control" type="number" step="0.1" min="0" value="<?echo $length;?>" name="length">
		        </div>
		    </div>
		    <div class="col-md-1"></div>
		    <div class="col-md-7">
		    	<label class="radio-inline">
				<?php
				  	if ($type1 == 1) {
				  		echo '<input type="radio" name="type1" value="1" checked> Kistekercs';
				  	} else {
				  		echo '<input type="radio" name="type1" value="1"> Kistekercs';
				  	}
				  	?>
				</label>
				
				<label class="radio-inline">
				  	<?php
				  	if ($type1 == 3) {
				  		echo '<input type="radio" name="type1" value="3" checked> Kistekercs 2,5 cm';
				  	} else {
				  		echo '<input type="radio" name="type1" value="3"> Kistekercs 2,5 cm';
				  	}
				  	?>
				</label>
				<br>
				<label class="radio-inline">
				  	<?php
				  	if ($type1 == 4) {
				  		echo '<input type="radio" name="type1" value="4" checked> Nagytekercs';
				  	} else {
				  		echo '<input type="radio" name="type1" value="4"> Nagytekercs';
				  	}
				  	?>
				</label>
				<label class="radio-inline">
				  	<?php
				  	if ($type1 == 5) {
				  		echo '<input type="radio" name="type1" value="5" checked> Nagytekercs 2,5 cm';
				  	} else {
				  		echo '<input type="radio" name="type1" value="5"> Nagytekercs 2,5 cm';
				  	}
				  	?>

				</label>
				<label class="radio-inline">
				  	<?php
				  	if ($type1 == 6) {
				  		echo '<input type="radio" name="type1" value="6" checked> Nagytekercs 3 cm';
				  	} else {
				  		echo '<input type="radio" name="type1" value="6"> Nagytekercs 3 cm';
				  	}
				  	?>
				</label>
				<br><br>

				<label class="radio-inline">
				  	<?php
				  	if ($type2 == 1) {
				  		echo '<input type="radio" name="type2" value="1" checked> Nórmal (Poa)';
				  	} else {
				  		echo '<input type="radio" name="type2" value="1"> Nórmal (Poa)';
				  	}
				  	?>
				</label>
				<label class="radio-inline">	
				  	<?php
				  	if ($type2 == 2) {
				  		echo '<input type="radio" name="type2" value="2" checked> Mediterrán';
				  	} else {
				  		echo '<input type="radio" name="type2" value="2"> Mediterrán';
				  	}
				  	?>
				</label>
				<br><br>

	    		<input name="type3" value="1" type="hidden"/>

				<div class="checkbox">
				    <label>
				      	<?php
					  	if ($cooling == 1) {
					  		echo '<input type="checkbox" value="1" name="cooling" checked> Hűtő kamion';
					  	} else {
					  		echo '<input type="checkbox" value="1" name="cooling"> Hűtő kamion';
					  	}
				  	?>
				      
				    </label>
				</div>

				<div class="checkbox">
				    <label>
				    	<?php
					  	if ($laying == 1) {
					  		echo '<input type="checkbox" value="1" name="laying" checked> Telepítés';
					  	} else {
					  		echo '<input type="checkbox" value="1" name="laying"> Telepítés';
					  	}
					  	?>
				    </label>
				</div>
		    </div>
		    <div class="col-md-2">
		    	<div class="form-group">
	          		<label for="field">Terület</label>
			        <?php

    				$query = $db->prepare("SELECT * FROM fields WHERE `id` = :id");
    				$query->bindParam(":id", $field, PDO::PARAM_STR);
		            $query->execute();
		            $result = $query->fetch(PDO::FETCH_OBJ);

		            if ($field == 111111) {
						$lastfieldname = "?";
					}
					else {
						$lastfieldname = $result->name;
					}

		            echo '<select class="form-control" name="field">';
		            echo '<option value="'.$field.'" selected>';
		            echo $lastfieldname;
		            echo "</option>";
		 
		            $query = $db->prepare("SELECT * FROM fields WHERE `complete` < 1 AND `cutting` = 1");
		            $query->execute();
		            while($row = $query->fetch()) {
		                if ($field != $row['id']) {
		                	echo "<option value='".$row['id']."'>".$row['name']."</option>";
		                }
		            }

		            echo '<option value="111111">?</option>';

		            echo '</select>';
			        ?>
        		</div>
		    </div>
		</div>

		<div class="row modal_row">
			
			<div class="col-md-2">
		    </div>

		    <div class="col-md-10" style="padding-bottom: 0;">
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

				            echo '<select class="form-control" name="country1">';
				            echo '<option value="'.$country.'" selected>';
				            echo $countryname;
				            echo "</option>";
				 
				            $query = $db->prepare("SELECT * FROM countries WHERE `id` > 0 AND (`type` = 1 OR `type` = 3) ORDER BY `name2` ASC");
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
							<div class="col-sm-6" style="padding-bottom: 10px;">
							  <input type="text" class="form-control" name="deliveryaddress" id="deliveryaddress_<?php echo $i; ?>" value="<?echo $deliveryaddress;?>">
							</div>
						</div>						
					</div>
			    </div>
		    </div>
		


		<div class="row modal_row">
			
			<div class="col-md-3">
				<label for="payment">Fizetési mód</label><br>
				<label class="radio-inline">
				  	<?php
				  	if ($payment == 1) {
				  		echo '<input type="radio" name="payment" value="1" checked> Kézpénz';
				  	} else {
				  		echo '<input type="radio" name="payment" value="1"> Kézpénz';
				  	}
				  	?>

				</label>
				<label class="radio-inline">
				  	<?php
				  	if ($payment == 2) {
				  		echo '<input type="radio" name="payment" value="2" checked> Átutalás';
				  	} else {
				  		echo '<input type="radio" name="payment" value="2"> Átutalás';
				  	}
				  	?>
				</label>

				<br><br><br>
				<div class="form-horizontal">
					<div class="form-group">
						<label for="invoicenumber" class="col-sm-3 control-label" style="padding-bottom: 10px;">Sz.sz.: </label>
						<div class="col-sm-6" style="padding-bottom: 10px;">
						  <input type="number" class="form-control" name="invoicenumber" step="1" min="0" value="<?echo $invoicenumber;?>">
						</div>
					</div>
				</div>
		    </div>
		</div>

		<div class="row modal_row_last">
	        <div class="col-md-3 formrow" style="padding-bottom: 10px;">
		        <div class="form-group">
		            <label for="exampleTextarea">Jegyzet (iroda)</label>
		            <textarea class="form-control" name="note2" id="note2_<?php echo $i; ?>" rows="3"><?echo $note2;?></textarea>
		        </div>

	        </div>
	        <div class="col-md-1"></div>
	        <div class="col-md-5" style="padding-bottom: 10px;">
		        <div class="form-horizontal">
					<br>
					<div class="form-group">
						<label for="telephone" class="col-sm-4 control-label" style="padding-bottom: 10px;">Telefon</label>
						<div class="col-sm-7" style="padding-bottom: 10px;">
						  <input type="tel" class="form-control" name="telephone" value="<?echo $telephone;?>">
						</div>
					</div>

				</div>
	        </div>
	    </div>

	    <input type="hidden" name="id" value="<?echo $id;?>">
	    <input type="hidden" name="projectid" value="<?echo $projectid;?>">
	    <input type="hidden" name="oldlength" value="<?echo $length;?>";>
	    <input type="hidden" name="oldcooling" value="<?echo $cooling;?>";>

      	<div class="modal-footer">
        	<button type="submit" class="btn btn-primary edit-btn" name="editOrderForm" value="Submit">Küldés</button>

        	<?php      	
        	echo '<button type="submit" class="btn btn-danger btn-sm delete-btn" onclick="deleteProject('.$id.');" value="Submit">Törlés (egész megrendelés)</button>';
      		?>
      	</div>
      	</form>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->


<!-- MODAL -->
	<!-- Edit customer -->
	<div class="modal fade" id="editCustomerModal<?php echo $i; ?>" tabindex="-1" role="dialog">
	    <div class="modal-dialog" role="document" style="width: 700px;">
	      	<div class="modal-content">
		        <div class="modal-header">
		          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		          <h4 class="modal-title">Szerkesztés</h4>
		        </div>

	        	<div class="modal-body"> 
	        	<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" accept-charset="utf-8">
	        
	        		<div class="row">
	          			<div class="col-md-6">
	            			<div class="form-group">
	              				<label for="name">Név</label>
	              				<input type="text" name="customer_name" value="<?echo $customer_name;?>">
	            			</div>
	          			</div>
	         			<div class="col-md-6">
							<div class="form-group">
								<label for="name">Irányítószám</label>
	              				<input style="margin-left: 10px; width: 70px;" type="number" name="plz" value="<?echo $customer_plz;?>">			    
			        		</div>
			        		<br>
			        		<div class="form-group">
								<label for="name">Város</label>
	              				<input style="margin-left: 10px;" type="text" name="city" value="<?echo $customer_city;?>">			    
			        		</div>
			        		<br>
			        		<div class="form-group">
								<label for="name">Utca, házszám</label>
	              				<input style="margin-left: 10px;" type="text" name="street" value="<?echo $customer_street;?>">			    
			        		</div>
			        		<br>
			        		<div class="form-group">
				          		<label for="country">Ország</label>
						        <?php

					            echo '<select class="form-control" id="country" name="country">';
					            echo '<option value="'.$customer_country.'" selected>';
					            echo $country_disp;
					            echo "</option>";
					 
					            $j = 0;
					            $query = $db->prepare("SELECT * FROM countries");
					            $query->execute();
					            while($row = $query->fetch()) {
					                if ($j != $customer_country) {
					                	echo "<option value='".$row['id']."'>".$row['name2']."</option>";
					                }  
					                $j++;     
					            }

					            echo '</select>';
						        ?>
			        		</div>
	        			</div>
        	   		</div>
        	   	</div>

	        	<input type="hidden" name="customer_id" value="<?echo $customer;?>"> 
	        	<input type="hidden" name="projectid" value="<?echo $projectid;?>"> 

		        <div class="modal-footer">
		          	<button type="submit" class="btn btn-primary center-block" name="editCustomerForm" value="Submit">Küldés</button>
		        </div>
		        </form>
		    </div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->


	<!-- MODAL -->
	<!-- Finish project -->
	<div class="modal fade" id="finishProjectModal" tabindex="-1" role="dialog">
	    <div class="modal-dialog" role="document" style="width: 700px;">
	      	<div class="modal-content">
		        <div class="modal-header">
		          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		          <h4 class="modal-title">Projekt befejezése</h4>
		        </div>

	        	<div class="modal-body"> 
	        	<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" accept-charset="utf-8">
	        
	        		<div class="row">
						<div class="col-md-1"></div>
				      
				      <div class="col-md-3">
				         <div class="form-group">                         
				          	<label for="amount">m&sup2;</label>
				          	<input type="number" class="form-control" name="finalamount" value="<?echo $finish_amount;?>">
				        </div>
				      </div> 
        	   		</div>
        	   	</div>

	        	<input type="hidden" name="projectid" value="<?echo $projectid;?>"> 

		        <div class="modal-footer">
		          	<button type="submit" class="btn btn-primary center-block" name="finishProject" value="Submit">Küldés</button>
		        </div>
		        </form>
		    </div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->
  
