<?php 
////////////////////////////////////////////////////////
// Starting page for selection of production or sales //
////////////////////////////////////////////////////////

include('views/_header_full.php');
?>

<div class="inputform">

    <div class="row" style="margin-top: 200px;">
	    <?php
       	$browserAsString = $_SERVER['HTTP_USER_AGENT'];
        if (strstr($browserAsString, " AppleWebKit/") && strstr($browserAsString, " Mobile/")) {
          echo "";
        }
        else {
          echo '<div class="col-md-2"></div>';
        }
      ?>

	    <div class="col-xs-6 col-md-3">
	    	<a href="production.php"><img src="../img/icon_production.jpg" class="select"></a>
	    </div>
	    
	    <?php
	    if (strstr($browserAsString, " AppleWebKit/") && strstr($browserAsString, " Mobile/")) {
          echo "";
        }
        else {
          echo '<div class="col-md-2"></div>';
        }
        ?>

	    <div class="col-xs-6 col-md-3">
	    	<a href="sales.php"><img src="../img/icon_sales.jpg" class="select"></a>
	    </div>
	</div>
