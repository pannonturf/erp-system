<?php 
// Database connection
require_once('../config/config.php');
$db = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.'', DB_USER, DB_PASS);


if (isset($_POST['btnRestart'])) {
	$password = $_POST['password'];
     
	if ($password == "") {
        $login_error_message = 'Password field is required!';
    } 
    else {
        $username = "restart";
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
            $valid_attempts = $now - (2 * 60 * 60);

            $query2 = $db->prepare("SELECT * FROM `login_attempts` WHERE `user_id`= :user_id AND `time` > :valid_attempts");
            $query2->bindParam("user_id", $userid, PDO::PARAM_STR);
            $query2->bindParam("valid_attempts", $valid_attempts, PDO::PARAM_STR);
            $query2->execute();


            if ($query2->rowCount() > 3) {
                $login_error_message = 'Invalid!';
            }
            else {
                // Check password
                if(password_verify($password, $result->password)) {
                	if ($_COOKIE["pc"] == "login1" OR $_COOKIE["pc"] == "login2" OR $_COOKIE["pc"] == "login6") {
						$id = 2;

						$sql = "UPDATE `system` SET `active` = 1 WHERE `id` = :id";
						$query = $db->prepare($sql);
						$query->bindParam(":id", $id, PDO::PARAM_STR);
						$query->execute();

						echo "<h3>OK</h3>";
						exit();
					}
                }
                else {
                    $login_error_message = 'Invalid!';
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
           $login_error_message = 'Invalid!';
        } 
    }
	
}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Kezdés</title>
<link href="../style/bootstrap3.css" type="text/css" rel="stylesheet">
<link rel="stylesheet" href="../style/style.css" type="text/css"  />
</head>
<body>
<div class="container">

	<div class="login center-block">

		<?php
		//Display error messages
	    if ($login_error_message != "") {
	        echo '<div class="alert alert-danger"><strong>Error: </strong> ' . $login_error_message . '</div>';
	    }
	    ?>

	    
	  	<form method="post" action="index.php">

			<div class="form-group">
		  		<div class="col-10">
		    		<input class="form-control" type="password" placeholder="Jelszó" name="password" autocomplete="off">
		  		</div>
			</div>

	    	<button type="submit" class="btn btn-default" name="btnRestart">Küldés</button>
	  </form>

	</div>
</div>

</body>
</html>
