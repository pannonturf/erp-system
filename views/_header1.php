<?php 
/////////////////////////////////////////
// Main header for sales (login 1 + 2) //
/////////////////////////////////////////
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/TableDnD/0.9.1/jquery.tablednd.js" integrity="sha256-d3rtug+Hg1GZPB7Y/yTcRixO/wlI78+2m08tosoRn7A=" crossorigin="anonymous"></script>
<script src="js/bootstrap.js"></script>
<script type="text/javascript" src="js/jquery.tablesorter.min.js"></script>

<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

<?php
// load graphs if necessary
if ($statistics == 1) {
  include('views/_header_statistics.php');
}
elseif ($statistics == 2 AND isset($_GET['statistics'])) {
  include('views/_header_customer.php');
}

?>

<script type="text/JavaScript">

  function AutoRefresh( t ) {
    timeout = setTimeout("location.reload(true);", t);
  }

  function printPage() {
      window.print();
  }
</script>


<title>Program | Pannon Turfgrass</title>

</head>

<?php
// Autorefreh to always show new orders
if ($refresh == 1) {  
  echo '<body onload="JavaScript:AutoRefresh(100000);">';
}
else {
  echo "<body>";
}


// wide view for cutting planning view
if ($wide == 1) {
  echo '<div class="container-wide">';
}
else {
  echo '<div class="container">';
}
?>

  <!-- NAVBAR -->
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
            <a class="navbar-brand" href="index.php"><img src="img/logo.png" alt="Pannon Turfgrass"></a>
          </div>

          <!-- Collect the nav links, forms, and other content for toggling -->
          <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
            
            <?php 
            if ($modus == 1) {    // standard
              echo '<ul class="nav navbar-nav navbar-right">';
            } 
            else {                // SOS
              echo '<ul class="nav navbar-nav navbar-right red-color">';
            }
            ?>
              <li><a href="sales.php"><span class='glyphicon glyphicon-home'></span>&nbsp;&nbsp;Kezd&#337;lap</a></li>

              <?php
              echo '<li><a href="order.php"><span class="glyphicon glyphicon-plus"></span>&nbsp;&nbsp;Új megrendelés</a></li>';

              if ($modus == 1) { 
      
                if ($login == 1 OR $login == 2) {   // management access
                ?>
                  <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><span class="glyphicon glyphicon-edit"></span>&nbsp;&nbsp;Vágás <span class="caret"></span></a>
                    <ul class="dropdown-menu">
                      <li><a href="today.php">Mai vágás</a></li>
                      <li><a href="plan.php">Holnapi vágás</a></li>

                      <?php
                      if ($cutting_modus == 1) {   // plan2 nur for cutting mode
                          echo '<li><a href="plan2.php">Vágás tervezés (+2)</a></li>';
                      }
                      ?>
                      
                      <li><a href="amounts.php">Kapacitás tervezés</a></li>
                      <li><a href="cutting2.php">Modus 2 - Gép</a></li>
                      <li><a href="loading.php">Modus 2 - Rakódo</a></li>
                    </ul>
                  </li>
                <?php
                }
              }

              echo '<li><a href="project.php"><span class="glyphicon glyphicon-list-alt"></span>&nbsp;&nbsp;Projektek</a></li>';
              echo '<li><a href="history.php?datum=0"><span class="glyphicon glyphicon-time"></span>&nbsp;&nbsp;Történet</a></li>';

              if ($login == 5) {  // office desk access
                echo ' <li><a href="inventory.php"><span class="glyphicon glyphicon-stats"></span>&nbsp;&nbsp;Raktár</a></li>';
              }

              if ($modus == 1) {
                if ($login == 1 OR $login == 2) {              
                  ?>
                  <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><span class="glyphicon glyphicon-signal"></span>&nbsp;&nbsp;Statisztika <span class="caret"></span></a>
                    <ul class="dropdown-menu">
                      <?php
                      if ($login == 1) {  // full access
                        echo '<li><a href="statistics.php">Eladás</a></li>';
                      }
                      ?>
                      <li><a href="customer.php">Vevők</a></li>
                      <?php
                      if ($login == 1) { 
                        echo '<li><a href="customer_statistics.php">Vevő statisztika</a></li>';
                        echo '<li><a href="week.php">Hét</a></li>';
                      }
                      ?>
                      <li><a href="bonus.php">ha pénz</a></li>
                      <li><a href="field_statistics.php">Terület</a></li>
                      <?php
                      if ($login == 1) { 
                        echo '<li><a href="areas.php">Terség</a></li>';
                      }
                      ?>
                    </ul>
                  </li>
                  <?php
                }
                else {
                  echo '<li><a href="customer.php"><span class="glyphicon glyphicon-signal"></span>&nbsp;&nbsp;Statisztika</a></li>';
                }
              }
              ?>
              <li><a href="logout.php"><span class='glyphicon glyphicon-off'></span>&nbsp;&nbsp;Logout</a></li>

              <?php
              if ($login == 1 OR $login == 2) { 
                echo '<li><a href="production.php"><i>TERMELÉS</i></a></li>';
              }
              ?>
            </ul>
          
          </div><!-- /.navbar-collapse -->
        </div><!-- /.container-fluid -->
    
      <?php
      }
      ?>
    </nav>
  </div>

    
