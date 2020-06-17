<?php 
// Database connection
require_once('config/config.php');
$db = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.'', DB_USER, DB_PASS);

session_start();            // Startet die PHP-Sitzung 

$query = $db->prepare("SELECT * FROM `system` WHERE `id` = 1");
$query->execute(); 
$result = $query->fetch(PDO::FETCH_OBJ);
$active = $result->active;

if ($active == 1) {
   
    //Login
    if (isset($_POST['btnLogin'])) {

        $username = $_POST['username'];
        $password = $_POST['password'];
     
        if ($username == "") {
            $login_error_message = 'Username field is required!';
        } else if ($password == "") {
            $login_error_message = 'Password field is required!';
        } else {
            
            //Check username and password
            $query = $db->prepare("SELECT * FROM `users` WHERE `username`= :username AND active = 1");
            $query->bindParam("username", $username, PDO::PARAM_STR);
            $query->execute();

            if ($query->rowCount() > 0) {
                $result = $query->fetch(PDO::FETCH_OBJ);

                $userid = $result->id; 

                ////Brute-Force-Function
                $now = time();
     
                // Alle Login-Versuche der letzten zwei Stunden werden gezählt.
                $valid_attempts = $now - (0.5 * 60 * 60);

                $query2 = $db->prepare("SELECT * FROM `login_attempts` WHERE `user_id`= :user_id AND `time` > :valid_attempts");
                $query2->bindParam("user_id", $userid, PDO::PARAM_STR);
                $query2->bindParam("valid_attempts", $valid_attempts, PDO::PARAM_STR);
                $query2->execute();


                if ($query2->rowCount() > 20) {
                    $login_error_message = 'Invalid login details! 1';
                }
                else {
                    // Check password
                    if(password_verify($password, $result->password)) {

                        $view = $result->view;
            			
            			if($view == 1) {
                            setcookie("login","1",time()+(60*60*6));    //6 hours login
                            setcookie("userid",$userid,time()+(60*60*3)); 
                            $_SESSION['login'] = 1;
                        }

                        if($view == 2) {
                            if ($_COOKIE["pc"] == "login2") {
                                setcookie("login","2",time()+(60*60*2));     //2 hours for planning the cutting
                                setcookie("userid",$userid,time()+(60*60*2)); 
                                $_SESSION['login'] = 2;
                            }                   
                            else {
                                setcookie("login","3",time()+(60*60*0.5));    
                                setcookie("userid",$userid,time()+(60*60*0.5)); 
                                $_SESSION['login'] = 3;        
                            }                
                        }

                        if($view == 3) {
                            setcookie("login","3",time()+(60*60*0.5));    
                            setcookie("userid",$userid,time()+(60*60*0.5));    
                            $_SESSION['login'] = 3;                   
                        }

                        if($view == 4) {
                            if ($_COOKIE["pc"] == "login4") {
                                $_SESSION['login'] = 4;
                                $_SESSION['userid'] = $userid;
                            }                   
                            else {
                                $login_error_message = 'Invalid login details! 2';      
                            }  
                        }

                        if($view == 5) {
                            if ($_COOKIE["pc"] == "login2") {
                                setcookie("login","5",time()+(60*60*12));    //12 hours for sales entries
                                setcookie("userid",$userid,time()+(60*60*12)); 
                                $_SESSION['login'] = 5;
                            }                   
                            else {
                                $login_error_message = 'Invalid login details! 2';     
                            }  
                        }

                        if($view == 6) {
                            if ($_COOKIE["pc"] == "login6") {
                                setcookie("login","6",time()+(60*60*14));    //14 hours for cutting
                                setcookie("userid",$userid,time()+(60*60*14)); 
                                $_SESSION['login'] = 6;
                            }  
                            elseif ($_COOKIE["pc"] == "login7") {
                                $login_error_message = 'Ez nem az 1-es telefon.';
                            }                 
                            else {  
                                $login_error_message = 'Invalid login details! 2';     
                            }  
                        }

                        if($view == 7) {
                            if ($_COOKIE["pc"] == "login7") {
                                setcookie("login","7",time()+(60*60*14));    //14 hours for cutting
                                setcookie("userid",$userid,time()+(60*60*14)); 
                                $_SESSION['login'] = 7;
                            }
                            elseif ($_COOKIE["pc"] == "login6") {
                                $login_error_message = 'Ez nem a 2-es telefon.';
                            }                     
                            else {  
                                $login_error_message = 'Invalid login details! 2';     
                            }  
                        }

                        if($view == 8) {
                            if ($_COOKIE["pc"] == "login8") {
                                setcookie("login","8",time()+(60*60*14));    //14 hours for loading
                                setcookie("userid",$userid,time()+(60*60*14)); 
                                $_SESSION['login'] = 8;
                            }  
                
                            else {  
                                $login_error_message = 'Invalid login details! 2';     
                            }  
                        }

                        //Update accesscontrol database
                        $start = date("Y-m-d H:i:s");

                        $query1 = $db->prepare("INSERT INTO `accesscontrol` (`id`, `user`, `start`) VALUES (NULL, :user, :start);");
                        $query1->bindParam(":user", $userid, PDO::PARAM_STR);
                        $query1->bindParam(":start", $start, PDO::PARAM_STR);

                        $query1->execute();

                        //get accessid
                        $query2 = $db->prepare("SELECT * FROM accesscontrol WHERE `user` = :user ORDER BY `start` DESC");
                        $query2->bindParam(":user", $userid, PDO::PARAM_STR);
                        $query2->execute();
                        $result2 = $query2->fetch(PDO::FETCH_OBJ);

                        $accessid = $result2->id;

                        $_SESSION['accessid'] = $accessid;  

                        echo "<script type='text/javascript'> document.location = 'index.php'; </script>"; 
                    }
                    else {
                        $login_error_message = 'Invalid username and/or password!';
                        $now = time();
                        $sql = "INSERT INTO `login_attempts` (`user_id`, `time`) VALUES (:user_id, :now);";
                        $query = $db->prepare($sql);

                        $query->bindParam(":user_id", $userid, PDO::PARAM_STR);
                        $query->bindParam(":now", $now, PDO::PARAM_STR);

                        $query->execute();
                    }
                }
            }

            else {
               $login_error_message = 'Invalid username and/or password!';
               $_SESSION['login'] = 0;
            } 
        }
    }

$query = $db->prepare("SELECT * FROM `system` WHERE `id` = 2");
$query->execute(); 
$result = $query->fetch(PDO::FETCH_OBJ);
$modus = $result->active;

$query = $db->prepare("SELECT * FROM `system` WHERE `id` = 3");
$query->execute(); 
$result = $query->fetch(PDO::FETCH_OBJ);
$cutting_modus = $result->active;

}
else {
    ?>
    <h3>-</h3>
    <?php
    exit;
}
    
?>



		
	