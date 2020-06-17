<?php 
/////////////////////////////////////////////////////////
// Main header for production inside (login 1 + 2 + 3) //
/////////////////////////////////////////////////////////
?>

<!DOCTYPE html>
<html lang="hu">
<head>
<meta charset="UTF-8"> 
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex">
    
<link href="style/bootstrap3.css" type="text/css" rel="stylesheet">
<link href="style/style.css" type="text/css" rel="stylesheet">
<link href="style/lightbox.css" rel="stylesheet">
<link rel="shortcut icon" href="">

<link rel="apple-touch-icon" sizes="180x180" href="img/favicon/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="img/favicon/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="img/favicon/favicon-16x16.png">
<meta name="theme-color" content="#ffffff">

<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
<script src="js/bootstrap.js"></script>


<title>Program | Pannon Turfgrass</title>

<!-- CSS for Google maps plugin -->
<style>
  html, body, #map-canvas {
    height: 100%;
    margin: 0px;
    padding: 0px
  }
</style>

<!-- plugins for modals -->
<script>
$('#inventoryModal').on('shown.bs.modal', function () {
  $('#myInput').focus()
})
$('#saleModal').on('shown.bs.modal', function () {
    $('#myInput').focus()
  })
$('#noteModal').on('shown.bs.modal', function () {
    $('#myInput').focus()
  })
$('#pictureModal').on('shown.bs.modal', function () {
    $('#myInput').focus()
  })

function printPage() {
    window.print();
}

</script>

</head>

<body>

<div class="container">     
  <div class="header clearfix">

    <nav class="navbar navbar-default">
      <div class="container-fluid">
        <!-- Brand and toggle get grouped for better mobile display -->
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="index.php"><img src="img/logo.png" alt="Pannon Turfgrass"></a>
        </div>

        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
          <ul class="nav navbar-nav navbar-right">
             

            <li><a href="production.php"><span class='glyphicon glyphicon-home'></span>&nbsp;&nbsp;Kezd&#337;lap</a></li>
            <li><a href="fields.php"><span class='glyphicon glyphicon-map-marker'></span>&nbsp;&nbsp;Térkép</a></li>
            <li><a href="fields.php?field=0"><span class='glyphicon glyphicon-th-large'></span>&nbsp;&nbsp;Területek</a></li>

            <?php
            if ($login == 1) { 
              echo "<li><a href='add.php'><span class='glyphicon glyphicon-plus'></span>&nbsp;&nbsp;Hozzáad</a></li>";
            }
            ?>

            <li><a href="inventory.php"><span class='glyphicon glyphicon-stats'></span>&nbsp;&nbsp;Raktár</a></li>
            <li><a href="logout.php"><span class='glyphicon glyphicon-off'></span>&nbsp;&nbsp;Logout</a></li>

            <?php
            if ($login == 1 OR $login == 2 OR $_COOKIE["pc"] == "login3") {  
              echo '<li><a href="sales.php"><i>ELADÁS</i></a></li>';
            }
            ?>
          </ul>
        
        </div><!-- /.navbar-collapse -->
      </div><!-- /.container-fluid -->
    </nav>

  </div>

    
