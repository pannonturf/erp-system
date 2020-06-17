<?php 
// start database and check login
include_once('config/accesscontrol.php'); 

if (isset($_COOKIE['login'])) {
    $login = $_COOKIE["login"];
}
elseif (isset($_SESSION['login'])) {
    $login = $_SESSION['login'];
}
else {
    $login = 0;
}

//show different views according to access level
if ($login == 0) {  //no access
    include('views/_login.php');
    include('views/_footer.php'); 

} elseif ($login == 1) {     //full access
    include('views/_index_full.php');
    include('views/_footer.php'); 

} elseif ($login == 2) {       // management access in office
    include('views/_index_full.php'); 
    include('views/_footer.php'); 

} elseif ($login == 3) {    // management access anywhere else
    if ($_COOKIE["pc"] == "login3") {   // selection if mobile view is allowed
        include('views/_index_full.php'); 
        include('views/_footer.php'); 
    }
    else {
        $header = 3;
        include('views/_production_in.php'); 
        include('views/_footer.php'); 
    } 

} elseif ($login == 4) {      // production worker access
    $header = 4;
    include('views/_production_out.php'); 
    include('views/_footer.php');  

} elseif ($login == 5) {     // office desk access
    $header = 1;
    include('views/_sales.php');  
    include('views/_footer.php'); 

} elseif ($login == 6 OR $login == 7) {     // cutting access (team 1 -> 6; team 2 -> 7)
    
    if ($cutting_modus == 1) {
        $header = 2;
        include('views/_cutting.php');  
    }
    elseif ($cutting_modus == 2) {
        $header = 6;
        include('views/_cutting2.php');  
    }
    include('views/_footer2.php'); 

}  

elseif ($login == 8) {     // Loader
    $header = 6;
    if ($cutting_modus == 1) {
        include('views/_blank.html');  
    }
    elseif ($cutting_modus == 2) {
        include('views/_loading.php');  
    }
    include('views/_footer2.php'); 
}  

?>