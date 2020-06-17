<?php 
//////////////////////////////////////////////
// Overview of production for outside users //
//////////////////////////////////////////////

include('views/_header'.$header.'.php');

require_once('config/config.php');
$db = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.'', DB_USER, DB_PASS);
$db -> exec("set names utf8");


//If form (offene arbeiten) has been sent
if (isset($_POST['completeForm']) OR isset($_POST['completeFormB'])) {
	//Get variables from form
	$user = $_SESSION['userid'];
	$datum = date("Y-m-d");
	$today = date("Y-m-d");
	$startdate = substr($_POST['startdate'], 5, 9);
	$id = $_POST['id'];
	$agent = $_POST['agent'];
	$oldtotal = $_POST['oldtotal'];
	$amount = $_POST['amount'];
	$completeHa = $_POST['complete'];
	$oldcomplete = $_POST['oldcomplete'] * 100;
	$field = $_POST['field'];
	$oldnote = $_POST['oldnote'];
	$olduser = $_POST['olduser'];

	$query = $db->prepare("SELECT * FROM fields WHERE `id` = :field");
  	$query->bindParam(":field", $field, PDO::PARAM_STR);
  	$query->execute();
  	$result = $query->fetch(PDO::FETCH_OBJ);
  	$size = $result->size;
  	$field_complete = 1 - $result->complete;
  	$current_size = $size * $field_complete;

  	// calculate total area which was worked on for different cases 
	if ($complete_check == 1) {		// field complete
		$complete = 1;
		$small = 0;
	}
	else {							// field not complete
		if (isset($_POST['completeFormB'])) {		// not finished yet -> open operation
			$complete = round($completeHa / $current_size, 2);
			$small = 0;
		}
		elseif ($current_size > $completeHa) {		// finished - only one part (small work)
			$small = 1;
			$complete = 1;
		}
	}

  	if ($small == 1) {
     	$total = round($amount * $completeHa, 2);
    }
    else {
  		$total = round($amount * $current_size * $complete, 2);
    }

    // added area
  	$addtotal = $total - $oldtotal;

  	if (!empty($_POST['note'])) {
		if ($user == $olduser) {
			$note = $_POST['note']." | ".$startdate." -> ".$oldcomplete."%";
		}
		else {
			$note = $_POST['note']." | ".$startdate." -> ".$oldcomplete."% (".$olduser.")";
		}
	}
	else {
		if ($user == $olduser) {
	  		$note = $startdate." -> ".$oldcomplete."%";
	  	}
	  	else {
	  		$note = $startdate." -> ".$oldcomplete."% (".$olduser.")";
	  	}
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
	$x = $x1 - $addtotal;

	//Update operations
	$sql = "UPDATE `operations` SET `datum` = :datum, `total` = :total, `cost` = :cost, `complete` = :complete, `note` = :note, `created` = :created WHERE `id` = :id";
	$query = $db->prepare($sql);

	$query->bindParam(":datum", $datum, PDO::PARAM_STR);
	$query->bindParam(":total", $total, PDO::PARAM_STR);
	$query->bindParam(":cost", $cost, PDO::PARAM_STR);
	$query->bindParam(":note", $note, PDO::PARAM_STR);
	$query->bindParam(":complete", $complete, PDO::PARAM_STR);
	$query->bindParam(":created", $today, PDO::PARAM_STR);
	$query->bindParam(":id", $id, PDO::PARAM_STR);

	$query->execute();

	//Update stock
    $query4->bindParam(":id", $agent, PDO::PARAM_STR);
    $query4->bindParam(":stock", $x, PDO::PARAM_STR);
    $query4->execute(); 

    //Create new entry in movement
    $sql5 = "INSERT INTO `movement` (`id`, `datum`, `agent`, `difference`, `total`, `type`, `link`, `user`) VALUES (NULL, :datum, :agent, :difference, :total, :type, :link, :user);";
	$query5 = $db->prepare($sql5);

  	$type = 1;

	$query5->bindParam(":datum", $datum, PDO::PARAM_STR);  
	$query5->bindParam(":agent", $agent, PDO::PARAM_STR); 
	$query5->bindParam(":difference", $addtotal, PDO::PARAM_STR);
	$query5->bindParam(":total", $x, PDO::PARAM_STR);
	$query5->bindParam(":type", $type, PDO::PARAM_STR);
	$query5->bindParam(":link", $id, PDO::PARAM_STR);
	$query5->bindParam(":user", $user, PDO::PARAM_STR);

	$query5->execute();


	echo '<div class="alert alert-success center-block" role="alert">Sikerült!</div>';
}


// if operation was edited
if (isset($_POST['editForm']) OR isset($_POST['editFormB'])) {

 	//Get variables from form
	$id = $_POST['id'];
	$datum = $_POST['date'];
	$field = $_POST['field'];
	$agent = $_POST['agent'];
	$oldnote = $_POST['oldnote'];
	$oldamount = $_POST['oldamount'];
	$amount = $_POST['amount'];
	$oldtotal = $_POST['oldtotal'];
	$completeHa = $_POST['complete'];
	$complete_check = $_POST['complete_check'];

	$query = $db->prepare("SELECT * FROM fields WHERE `id` = :field");
  	$query->bindParam(":field", $field, PDO::PARAM_STR);
  	$query->execute();
  	$result = $query->fetch(PDO::FETCH_OBJ);
  	$size = $result->size;
  	$field_complete = 1 - $result->complete;
  	$current_size = $size * $field_complete;

  	// calculate total area which was worked on for different cases 
	if ($complete_check == 1) {		// field complete
		$complete = 1;
		$small = 0;
	}
	else {							// field not complete
		if (isset($_POST['editFormB'])) {		// not finished yet -> open operation
			$complete = round($completeHa / $current_size, 2);
			$small = 0;
		}
		elseif ($current_size > $completeHa) {		// finished - only one part (small work)
			$small = 1;
			$complete = 1;
		}
	}

  	if ($small == 1) {
     	$total = round($amount * $completeHa, 2);
    }
    else {
  		$total = round($amount * $current_size * $complete, 2);
    }


  	if (isset($_SESSION['userid'])) {
		$user = $_SESSION['userid'];
	}
	else {
		$user = $_COOKIE["userid"];
	}

	if (!empty($_POST['note'])) {
		$note = $_POST['note'];
	}
	else {
		$note = "";
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
	$sql = "UPDATE `operations` SET `datum` = :datum, `field` = :field, `agent` = :agent, `amount` = :amount, `total` = :total, `cost` = :cost, `complete` = :complete, `small` = :small, `note` = :note WHERE `id` = :id";
    $query = $db->prepare($sql);

    $query->bindParam(":datum", $datum, PDO::PARAM_STR);
    $query->bindParam(":field", $field, PDO::PARAM_STR);
    $query->bindParam(":agent", $agent, PDO::PARAM_STR);
    $query->bindParam(":amount", $amount, PDO::PARAM_STR);
    $query->bindParam(":total", $total, PDO::PARAM_STR);
    $query->bindParam(":cost", $cost, PDO::PARAM_STR);
    $query->bindParam(":complete", $complete, PDO::PARAM_STR);
    $query->bindParam(":small", $small, PDO::PARAM_STR);
    $query->bindParam(":note", $note, PDO::PARAM_STR);
    $query->bindParam(":id", $id, PDO::PARAM_STR);

    $query->execute();

	//Update stock
    $query4->bindParam(":id", $agent, PDO::PARAM_STR);
    $query4->bindParam(":stock", $x, PDO::PARAM_STR);
    $query4->execute(); 

/* (Funktioniert aus irgendeinem Grund nicht!)
  	if ($oldamount <> $amount) {
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

  		//echo $datum." | ".$agent." | ".$difference." | ".$x." | ".$type." | ".$id." | ".$user; 
   	} 
*/
    echo '<div class="alert alert-success center-block" role="alert">Sikerült!</div>';
}


/////////
//if operation was deleted
if (isset($_POST['deleteForm'])) {
  if (isset($_SESSION['userid'])) {
    $user = $_SESSION['userid'];
  }
  else {
    $user = $_COOKIE["userid"];
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

  $query5->bindParam(":datum", $datum, PDO::PARAM_STR);  
  $query5->bindParam(":agent", $agent, PDO::PARAM_STR); 
  $query5->bindParam(":difference", $oldtotal, PDO::PARAM_STR);
  $query5->bindParam(":total", $x, PDO::PARAM_STR);
  $query5->bindParam(":type", $type, PDO::PARAM_STR);
  $query5->bindParam(":link", $id, PDO::PARAM_STR);
  $query5->bindParam(":user", $user, PDO::PARAM_STR);

  $query5->execute();

  echo '<div class="alert alert-success center-block" role="alert">Sikerült!</div>';
}

?>

<div class="inputform">

	<?
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

	?>

	<div class="row">
		<div class="col-md-1">
			<img src="../img/home1.png" class="count">
		</div>
		<div class="col-md-2"></div>
		<div class="col-md-6" style='text-align:right;'>
			<?php
			$days = array("Vasárnap", "Hétfő", "Kedd", "Szerda", "Csütörtök", "Péntek", "Szombat");
			$today = date('Y-m-d');
			$thisday = date('w');
			$thisweek = date('W'); 

			echo "<br><b>Ma:</b>&nbsp;&nbsp;&nbsp;".$days[$thisday].", &nbsp;".$today."<br> ("."Naptári hét: ".$thisweek.")";
			?>
		</div>
		<div class="col-md-1">
			<img src="../img/home2.png" class="count">
		</div>
	</div>


	<div class="row">
		<div class="col-md-9" style="padding-bottom:0px;">
			<div class="panel panel-open">
			  	<div class="panel-heading"><h4><span class="glyphicon glyphicon-bell"></span>&nbsp;&nbsp; Nyított munka</h4></div>

		      	<?php
		      	///////////////////
		      	//show operations of user that have not been completed
		      	echo "<table class='table table-striped centertext'>";
				
				//get operations from database   
				$query = $db->prepare("SELECT * FROM operations WHERE `complete` < 1 AND `delete` = 0 ORDER BY `datum` ASC");
				$query->bindParam(":user", $user, PDO::PARAM_STR);
				$query->execute();

				if ($query->rowCount() > 0) {
					echo "<tr class='title'><td>Dátum</td><td>Terület</td><td>Termék</td><td>Mennyiség<br><i>per ha</i></td><td>%</td><td><span class='glyphicon glyphicon-user'></td><td>Jegyzet</td><td></td></tr>";
					$i = 1;
					foreach ($query as $row) {
						$id = $row['id'];
						$datum = $row['datum'];
						$field = $row['field'];
						$agent = $row['agent'];
						$oldtotal = $row['total'];
						$amount = $row['amount'];
						$olduser = $row['user'];
						$oldnote = $row['note'];

						$query = $db->prepare("SELECT * FROM users WHERE `id` = :id");
						$query->bindParam(":id", $olduser, PDO::PARAM_STR);
						$query->execute();
						$result = $query->fetch(PDO::FETCH_OBJ);
						$oldusername = $result->username;
						
						if ($row['note']=="0") {
						    $note = "";
						  }
						  else {
						    $note = $row['note'];
						  }

						$completePercentage = $row['complete'];

						$day = date('w', strtotime($datum));

						echo "<tr><td>".$days[$day].", &nbsp;".$datum."</td>";
						
						$query = $db->prepare("SELECT * FROM fields WHERE `id` = :id");
				        $query->bindParam(":id", $field, PDO::PARAM_STR);
				        $query->execute();
				        $result = $query->fetch(PDO::FETCH_OBJ);
				        $size = $result->size;
				        $field_complete = 1 - $result->complete;
						$current_size = $size * $field_complete;
				        $sizedisplay = number_format($current_size, 2, ',', ' ');

				        echo "<td><a href='https://turfgrass.site/fields.php?field=".$field."'>".$result->name."</a></td>";

				        $oldcomplete = round($completePercentage * $current_size, 1);
				        $oldcomplete2 = round($completePercentage * $current_size, 2);

						//get name of agent
						$query = $db->prepare("SELECT * FROM agents WHERE `id` = :id");
						$query->bindParam(":id", $agent, PDO::PARAM_STR);
						$query->execute();
						$result = $query->fetch(PDO::FETCH_OBJ);

						if ($result->type == 1) {
							$unit = "kg";
						}
						if ($result->type == 2) {
							$unit = "l";
						}

						echo "<td>".$result->name."</td>";
						echo "<td>".$amount." ".$unit."</td>";
						echo "<td>".($completePercentage * 100)." % (".$oldcomplete." ha)</td>";
						echo "<td>".$oldusername."</td>";
						echo "<td>".$note."</td>";
						echo '<td><button type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#completeModal'.$i.'">Módosítás</button></td></tr>';

						?>
					    <!-- Edit entry modal-->
					    <div class="modal fade" id="completeModal<?php echo $i; ?>" tabindex="-1" role="dialog">
					      <div class="modal-dialog" role="document" style="width: 700px;">
					        <div class="modal-content">
					          <div class="modal-header">
					            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					            <h4 class="modal-title">Módosítás</h4>
					          </div>

					            <?php
					              	echo '<form method="post" name="myForm'.$i.'" action="'.$_SERVER['PHP_SELF'].'" accept-charset="utf-8" onsubmit="return validateForm2('.$i.')">';
									echo '<input type="hidden" name="id" value="'.$id.'">';
									echo '<input type="hidden" name="startdate" value="'.$datum.'">';
									echo '<input type="hidden" name="amount" value="'.$amount.'">';
									echo '<input type="hidden" name="oldtotal" value="'.$oldtotal.'">';
									echo '<input type="hidden" name="field" value="'.$field.'">';
									echo '<input type="hidden" name="agent" value="'.$agent.'">';
									echo '<input type="hidden" name="oldcomplete" value="'.$completePercentage.'">';
									echo '<input type="hidden" name="olduser" value="'.$olduser.'">';
								?>
								<br>
								<div class="row">
							      <div class="col-md-2"></div>
							      <div class="col-md-3" style="margin-left: 15px;">
							        <label for="complete">Befejezett ha</label>
							        <input class="form-control" id="fixinput<?echo $i;?>" type="number" step=".01" min="0" max="<?echo $currentsize;?>" value="<?echo $oldcomplete2;?>" name="complete">
							      	<input type="hidden" name="complete_check" id="complete_check<?echo $i;?>" value="0">
							      </div>
							      
							      <div class="col-md-2">
							        <label for="complete">&nbsp;</label>
							        <button type="button" class="btn btn-complete" id="changebutton<?echo $i;?>" onclick="completeFunction2(<?echo $current_size;?>, <?echo $i;?>)"><b>Egész terület (<?echo $sizedisplay;?> ha)</b></button>
							      </div>
							    </div>

							   <br>
							    <div class="row">
							      <div class="col-md-2"></div>
							      <div class="col-md-8 formrow" style="padding-bottom: 10px;">
							        <div class="form-group">
							          <label for="exampleTextarea">Jegyzet</label>
							          <textarea class="form-control" name="note" rows="3"><?echo $oldnote;?></textarea>
							        </div>
							      </div>
							      <div class="col-md-3"></div>
							    </div>

							    <div class="row">
							      <div class="col-md-3"></div>
							      <div class="col-md-3">
							        <button type="submit" class="btn btn-primary" name="completeForm" value="Submit">Munka befejezett</button>
							      </div>
							      <div class="col-md-3" id="optionb<?echo $i;?>">
							        <button type="submit" class="btn btn-primary" name="completeFormB" value="Submit">Munka még nyított</button>
							      </div>
							    </div>
					            </form>
					          </div>

					        </div><!-- /.modal-content -->
					      </div><!-- /.modal-dialog -->
					    </div><!-- /.modal -->
						<?php
					 	
					 	$i++;

					} //foreach	
				}	//if rowCount
				else {
					echo "<tr><td>Nincs nyított munka</td>";
				}

				echo "</table>";

			echo "</div>";


			////////////////////
			// Planned operations
     
			$query = $db->prepare("SELECT * FROM plan WHERE `complete` = 0 AND `delete` = 0 ORDER BY `week` ASC");
			$query->bindParam(":user", $user, PDO::PARAM_STR);
			$query->execute();

			if ($query->rowCount() > 0) {
			?>

			<div class="row">
				<div class="col-md-12">
			      	<div class="panel panel-open">
					  	<div class="panel-heading"><h4><span class="glyphicon glyphicon-list-alt"></span>&nbsp;&nbsp; Tervezett munka</h4></div>

				      	<?php
				      	//show operations that are planned and assigned to a user
				      	echo "<table class='table table-striped centertext'>";
						echo "<tr class='title'><td>Dátum</td><td>Terület</td><td>Termék</td><td>Mennyiség<br><i>per ha</i></td><td class='note'>Jegyzet</td><td>Valassz</td></tr>";	

						$results = $query->fetchAll(PDO::FETCH_ASSOC);
						foreach($results as $row) {
							$id = $row['id'];
							$week = $row['week'];
							$field = $row['field'];
							$agent = $row['agent'];
							$amount = $row['amount'];
							
							if ($row['note']=="0") {
							    $note = "";
							}
							else {
							    $note = $row['note'];
							}

							$thisweek = date('W');
							$timedifference = $week- $thisweek;
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
			
							echo '<form method="post" action="add.php?mode=1" accept-charset="utf-8">';
							echo '<input type="hidden" name="id" value="'.$id.'">';
							echo '<input type="hidden" name="field" value="'.$field.'">';
							echo '<input type="hidden" name="agent" value="'.$agent.'">';
							echo '<input type="hidden" name="amount" value="'.$amount.'">';
							echo '<input type="hidden" name="note" value="'.$note.'">';
							
							echo "<tr><td>".$weekdisplay."</td>";
							
							$query = $db->prepare("SELECT * FROM fields WHERE `id` = :id");
		            		$query->bindParam(":id", $field, PDO::PARAM_STR);
		            		$query->execute();
		            		$result = $query->fetch(PDO::FETCH_OBJ);
		            		echo "<td><a href='https://turfgrass.site/fields.php?field=".$field."'>".$result->name."</a></td>";

							//get name of agent
							$query = $db->prepare("SELECT * FROM agents WHERE `id` = :id");
							$query->bindParam(":id", $agent, PDO::PARAM_STR);
							$query->execute();
							$result = $query->fetch(PDO::FETCH_OBJ);

							if ($result->type == 1) {
								$unit = "kg";
							}
							if ($result->type == 2) {
								$unit = "l";
							}

							echo "<td>".$result->name."</td>";

							echo "<td>".$amount." ".$unit."</td>";
							echo "<td>".$note."</td>";
							echo '<td><button type="submit" class="btn btn-default btn-sm" name="inputPlan" value="Submit"><span class="glyphicon glyphicon-ok"></span></button></td></tr></form>';

						}	//foreach

						echo "</table>";

					?>
					</div>
	   			</div>
			</div>

			<?php
			}
			?>

		</div>

		<div class="col-md-3">
			<a href="add.php"><img src="../img/entry.png" class="picture_add"></a>
		</div>
	</div>

	<div class="row">
		<div class="col-md-1"></div>

		<div class="col-md-10">
			<img src="../img/separator.png" class="separator">
		</div>

		<div class="col-md-1"></div>
	</div>

	<div class="row">
		<div class="col-md-1">
			<img src="../img/home3.png" class="count">
		</div>
		<div class="col-md-3"></div>

		<div class="col-md-4">
			<img src="../img/control.png" class="picture_add">
		</div>

		<div class="col-md-4"></div>
	</div>


	<?php
	//////////////
	// Show operations that were entered today from this user

	$user = $_SESSION['userid'];
	$query = $db->prepare("SELECT * FROM users WHERE `id` = :id");
    $query->bindParam(":id", $user, PDO::PARAM_STR);
    $query->execute();
    $result = $query->fetch(PDO::FETCH_OBJ);
	$username = $result->username;

	$today = date("Y-m-d");

	$query = $db->prepare("SELECT * FROM operations WHERE `user` = :user AND `created` = :created AND `delete` = 0");
	$query->bindParam(":user", $user, PDO::PARAM_STR);
	$query->bindParam(":created", $today, PDO::PARAM_STR);
	$query->execute();

	if ($query->rowCount() > 0) {
	?>
		<div class="row">
	      	<div class="col-md-12">
	      	
		      	<div class="panel panel-primary">
				  	<!-- Default panel contents -->
				  	<div class="panel-heading"><h4><span class="glyphicon glyphicon-send"></span>&nbsp;&nbsp; Beírt munka - <?php echo $username;?></h4></div>

			      	<?php
			      	$days = array("Vasárnap", "Hétfő", "Kedd", "Szerda", "Csütörtök", "Péntek", "Szombat");

			      	//show operations of user that have not been completed
			      	echo "<table class='table table-striped centertext'>";
					
					$query = $db->prepare("SELECT * FROM operations WHERE `user` = :user AND `created` = :created AND `delete` = 0 ORDER BY `id` ASC");
					$query->bindParam(":user", $user, PDO::PARAM_STR);
					$query->bindParam(":created", $today, PDO::PARAM_STR);
					$query->execute();

					if ($query->rowCount() > 0) {
						echo "<tr class='title'><td>Dátum</td><td>Terület</td><td>Termék</td><td>Mennyiség<br><i>per ha</i></td><td>%</td><td>Jegyzet</td><td>Módosítás</td><td>Törlés</td></tr>";
						
						foreach ($query as $row) {
							$id = $row['id'];
							$datum = $row['datum'];
							$field = $row['field'];
							$agent = $row['agent'];
							$total = $row['total'];
							$amount = $row['amount'];
							$small = $row['small'];
							
							if ($row['note']=="0") {
							    $note = "";
							}
							else {
							    $note = $row['note'];
							}

							$oldnote = $note;
							$oldtotal = $total;
							$oldamount = $amount;

							$completePercentage = $row['complete'];

							$day = date('w', strtotime($datum));

							echo '<form method="post" action="'.$_SERVER['PHP_SELF'].'" accept-charset="utf-8">';
							echo "<tr><td>".$days[$day].", &nbsp;".$datum."</td>";
							
							$query = $db->prepare("SELECT * FROM fields WHERE `id` = :id");
					        $query->bindParam(":id", $field, PDO::PARAM_STR);
					        $query->execute();
					        $result = $query->fetch(PDO::FETCH_OBJ);
					        $fieldname = $result->name;
					        $size = $result->size;
					        $field_complete = 1 - $result->complete;
							$current_size = $size * $field_complete;
					        $sizedisplay = number_format($current_size, 2, ',', ' ');
				        	echo "<td><a href='https://turfgrass.site/fields.php?field=".$field."'>".$fieldname."</a></td>";

					        $oldcomplete = round($completePercentage * $current_size, 1);
					        $oldcomplete2 = round($completePercentage * $current_size, 2);

					        if ($small == 1) {
								$oldcomplete = $oldtotal / $oldamount;
								$complete = "Részmunka (".$oldcomplete." ha)";
							}
							else {
								$complete_nr = round($row['complete'] * 100, 1);
								$complete = $complete_nr."% (".$oldcomplete." ha)";
							}

							//get name of agent
							$query = $db->prepare("SELECT * FROM agents WHERE `id` = :id");
							$query->bindParam(":id", $agent, PDO::PARAM_STR);
							$query->execute();
							$result = $query->fetch(PDO::FETCH_OBJ);
							$agentname = $result->name;

							if ($result->type == 1) {
								$unit = "kg";
							}
							if ($result->type == 2) {
								$unit = "l";
							}

							echo "<td>".$result->name."</td>";
							echo "<td>".$amount." ".$unit."</td>";
							echo "<td>".$complete."</td>";
							echo "<td>".$note."</td>";
							echo '<td><button type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#editModal'.$i.'" data-id="'.$id.'"><span class="glyphicon glyphicon-pencil"></span></button></td>';
							echo '<td><button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#deleteModal'.$i.'" data-id="'.$id.'"><span class="glyphicon glyphicon-remove"></span></button></td></tr>';
							?>

							<!-- MODAL -->
							<!-- Edit entry -->
							<div class="modal fade" id="editModal<?php echo $i; ?>" tabindex="-1" role="dialog">
							  <div class="modal-dialog" role="document" style="width: 700px;">
							    <div class="modal-content">
							      	<div class="modal-header">
							        	<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
							        	<h4 class="modal-title">Módosítás</h4>
							      	</div>

									<div class="row">
										<div class="col-md-6"><br>
											<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" accept-charset="utf-8">
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
									    </div>	
									</div>

									<br>
									<div class="row">
								      	<div class="col-md-2"></div>
								      	<div class="col-md-3" style="margin-left: 15px;">
								        	<label for="complete">Befejezett ha</label>
								        	<input class="form-control" id="fixinput<?echo $i;?>" type="number" step=".01" min="0" max="<?echo $currentsize;?>" value="<?echo $oldcomplete2;?>" name="complete">
								      		<?php
								      		if ($completePercentage == 1) {
								      			echo '<input type="hidden" name="complete_check" id="complete_check'.$i.'" value="1">';
								      		}
								      		else {
								      			echo '<input type="hidden" name="complete_check" id="complete_check'.$i.'" value="0">';
								      		}
								      		?>
								      	</div>
								      
								      	<div class="col-md-2">
								        	<label for="complete">&nbsp;</label>
											<?php
								        	if ($completePercentage == 1) {
								      			$btn_check = "btn-active";
								      		}
								      		else {
								      			$btn_check = "btn-complete";
								      		}
								      		?>
								        	<button type="button" class="btn <?echo $btn_check;?>" id="changebutton<?echo $i;?>" onclick="completeFunction2(<?echo $current_size;?>, <?echo $i;?>)"><b>Egész terület (<?echo $sizedisplay;?> ha)</b></button>
								      	</div>
								    </div>

								   	<br>
								    <div class="row">
								      	<div class="col-md-2"></div>
									    <div class="col-md-8 formrow" style="padding-bottom: 10px;">
									        <div class="form-group">
									          	<label for="exampleTextarea">Jegyzet</label>
									          	<textarea class="form-control" name="note" rows="3"><?echo $oldnote;?></textarea>
									        </div>
								      	</div>
								      	<div class="col-md-3"></div>
								    </div>

								    <div class="row">
								      	<div class="col-md-3"></div>
								      	<div class="col-md-3">
								        	<button type="submit" class="btn btn-primary" name="editForm" value="Submit">Munka befejezett</button>
								      	</div>
								      	<?php
								      	if ($completePercentage == 1) {
							      			$display_check = "none";
							      		}
							      		else {
							      			$display_check = "block";
							      		}
							      		?>
								      	<div class="col-md-3" id="optionb<?echo $i;?>" style="display:<?echo $display_check;?>">
								        	<button type="submit" class="btn btn-primary" name="editFormB" value="Submit">Munka még nyított</button>
								      	</div>
								    </div>
								        
								    <input type="hidden" name="oldtotal" value="<?echo $oldtotal;?>">	
								    <input type="hidden" name="oldamount" value="<?echo $oldamount;?>">
								    <input type="hidden" name="small" value="<?echo $small;?>"> 
								    <input type="hidden" name="id" value="<?echo $id;?>">
								    <input type="hidden" name="oldcomplete" value="<?echo $completePercentage;?>">	

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
						            <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" accept-charset="utf-8">  
						              <input type="hidden" name="id" value="<?echo $id;?>"> 
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
					}
					else {
						echo "<tr><td>Nincs nyított munka</td>";
					}

					echo "</table>";
				?>
				</div>
		   	</div>
	    </div>
<?php
	}
?>
</div>



