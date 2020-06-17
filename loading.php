<?php 
//check login
include_once('config/accesscontrol.php'); 

//check login
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

} elseif ($login == 1) { 
    $header = 1;
    include('views/_loading.php');
    include('views/_footer.php'); 

} elseif ($login == 2) {    
    $header = 1;
    if ($modus == 1) {
        include('views/_loading.php'); 
    }
    else {
        include('views/_loading.php'); 
    }
    include('views/_footer.php'); 

} elseif ($login == 3) {    
    $header = 3;
    include('views/_production_in.php'); 
    include('views/_footer.php');  

} elseif ($login == 4) {    
    $header = 4;
    include('views/_production_out.php'); 
    include('views/_footer.php');  

} elseif ($login == 5) {    
    $header = 1;
    if ($modus == 1) {
        include('views/_sales.php'); 
    }
    else {
        include('views/_sales.php'); 
    } 
    include('views/_footer.php'); 

} elseif ($login == 6) {    
    $header = 2;
    include('views/_cutting2.php');  
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