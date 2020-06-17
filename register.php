<?php 

// start session
session_start();

//Update operations
require_once('config/config.php');
$db = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.'', DB_USER, DB_PASS);
$db -> exec("set names utf8");

if(isset($_POST['btn-signup']))
{

   $uname = $_POST['txt_uname'];
   $upass = $_POST['txt_upass'];
 
   if($uname=="") {
      $error[] = "provide username !"; 
   }
   else if($upass=="") {
      $error[] = "provide password !";
   }
   else if(strlen($upass) < 6){
      $error[] = "Password must be at least 6 characters"; 
   }
   else
   {
		$new_password = password_hash($upass, PASSWORD_DEFAULT);

		$name = "New";
		$view = 4;
		$today = date("Y-m-d");
		$active = 1;
		
		$sql = "INSERT INTO `users` (`id`, `username`, `password`, `name`, `view`, `added`, `active`) VALUES (NULL, :uname, :upass, :name, :view, :added, :active);";
  		$query = $db->prepare($sql);

		$query->bindparam(":uname", $uname);
		$query->bindparam(":upass", $new_password);
		$query->bindparam(":name", $name);  
		$query->bindparam(":view", $view);  
		$query->bindparam(":added", $today);  
		$query->bindparam(":active", $active);              
		$query->execute(); 

		echo '<div class="alert alert-success center-block" role="alert">Sikerült!</div>';

  	} 
}


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Sign up</title>
<link href="style/bootstrap.css" type="text/css" rel="stylesheet">
<link rel="stylesheet" href="style/style.css" type="text/css"  />
</head>
<body>
<div class="container">
     <div class="form-container" style="width: 200px;">
        <form method="post">

            <h2>Sign up.</h2><hr />
            <?php
            if(isset($error))
            {
               foreach($error as $error)
               {
                  ?>
                  <div class="alert alert-danger">
                      <i class="glyphicon glyphicon-warning-sign"></i> &nbsp; <?php echo $error; ?>
                  </div>
                  <?php
               }
            }
            else if(isset($_GET['joined']))
            {
                 ?>
                 <div class="alert alert-info">
                      <i class="glyphicon glyphicon-log-in"></i> &nbsp; Successfully registered <a href='index.php'>login</a> here
                 </div>
                 <?php
            }
            ?>
            <div class="form-group">
            <input type="text" class="form-control" name="txt_uname" placeholder="Enter Username" />
            </div>
      
            <div class="form-group">
             <input type="password" class="form-control" name="txt_upass" placeholder="Enter Password" />
            </div>
            <div class="clearfix"></div><hr />
            <div class="form-group">
             <button type="submit" class="btn btn-block btn-primary" name="btn-signup">
               SIGN UP
                </button>
            </div>
        </form>
       </div>
</div>

</body>
</html>