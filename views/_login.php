<?php
////////////////
// Login page //
////////////////

include('views/_header1.php');

?>

<div class="login center-block">

	<h3>Login</h3>
	<br> 

	<?php
	//Display error messages
    if ($login_error_message != "") {
        echo '<div class="alert alert-danger"><strong>Error: </strong> ' . $login_error_message . '</div>';
    }

    //echo $_COOKIE["pc"];
    ?>

    
  	<form method="post" action="index.php">
    	<div class="form-group">
		  	<div class="col-10">
		    	<input class="form-control" type="text" placeholder="Felhasználónév" name="username" autocomplete="off">
		  	</div>
		</div>

		<div class="form-group">
	  		<div class="col-10">
	    		<input class="form-control" type="password" placeholder="Jelszó" name="password" autocomplete="off">
	  		</div>
		</div>

    	<button type="submit" class="btn btn-default" name="btnLogin" value="Login">Küldés</button>
  </form>

</div>


<?php 


		
	