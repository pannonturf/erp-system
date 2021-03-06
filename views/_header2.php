<?php 
////////////////////////////////////////////////
// Header cutting teams outside (login 6 + 7) //
////////////////////////////////////////////////
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
<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css">
<link rel="shortcut icon" href="">

<link rel="apple-touch-icon" sizes="180x180" href="img/favicon/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="img/favicon/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="img/favicon/favicon-16x16.png">
<meta name="theme-color" content="#ffffff">

<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="js/bootstrap.js"></script>

<title>Program | Pannon Turfgrass</title>

<script type="text/JavaScript">

  function AutoRefresh( t ) {
     setTimeout("location.reload(true);", t);
  }

</script>

</head>

<body onload="JavaScript:AutoRefresh(10000);">

<div class="container">
  <div class="header clearfix">
    <nav class="navbar navbar-default">
     
      <?php
      if ($login == 0) {  //no access
        ?>
        <div class="container-fluid">
          <div class="navbar-header">
            <a class="navbar-brand" href="sales.php"><img src="img/logo.png" alt="Pannon Turfgrass"></a>
          </div>
        </div>
      <?php
      }
      else {
        ?>
        <div class="container-fluid">
          <!-- Brand and toggle get grouped for better mobile display -->
          <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
              <span class="sr-only">Toggle navigation</span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="sales.php"><img src="img/logo.png" alt="Pannon Turfgrass"></a>
          </div>

          <!-- Collect the nav links, forms, and other content for toggling -->
          <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
            <ul class="nav navbar-nav navbar-right">
              <li><a href="sales.php"><span class='glyphicon glyphicon-home'></span>&nbsp;&nbsp;Kezd&#337;lap</a></li>
              <li><a href="cutting_today.php"><span class='glyphicon glyphicon-time'></span>&nbsp;&nbsp;Utolsó 8</a></li>
              <li><a href="tomorrow.php"><span class='glyphicon glyphicon-list-alt'></span>&nbsp;&nbsp;Holnapi vágás</a></li>
              <li><a href="logout.php"><span class='glyphicon glyphicon-off'></span>&nbsp;&nbsp;Logout</a></li>
            </ul>
          
          </div><!-- /.navbar-collapse -->
        </div><!-- /.container-fluid -->
    
      <?php
      }
      ?>
    </nav>
  </div>

    
