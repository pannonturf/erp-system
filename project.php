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
    $browserAsString = $_SERVER['HTTP_USER_AGENT'];
    if (strstr($browserAsString, " AppleWebKit/") && strstr($browserAsString, " Mobile/"))
    {
        include('views/_project.php');
    }
    else {
         include('views/_project.php');
    }
    include('views/_footer.php'); 

} elseif ($login == 2) {    
    $header = 1;
    include('views/_project.php'); 
    include('views/_footer.php'); 

} elseif ($login == 3) {    
    if ($_COOKIE["pc"] == "login3") {
        $header = 5;
        include('views/_project.php'); 
        include('views/_footer.php'); 
    }
    else {
        $header = 3;
        include('views/_production_in.php'); 
        include('views/_footer.php'); 
    } 

} elseif ($login == 4) {    
    $header = 4;
    include('views/_production_out.php'); 
    include('views/_footer.php');  

} elseif ($login == 5) {    
    $header = 1;
    include('views/_project.php');  
    include('views/_footer.php'); 

} elseif ($login == 6) {    
    $header = 2;
    if ($modus == 1) {
        include('views/_cutting.php'); 
    }
    else {
        include('views/blank.php'); 
    } 
    include('views/_footer2.php'); 

}   


?>