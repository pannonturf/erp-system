<?php
//////////////////////
// Set right cookie //
//////////////////////

$login = 0;
include('views/_header1.php');

require_once('config/config.php');
$db = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.'', DB_USER, DB_PASS);
$db -> exec("set names utf8");
date_default_timezone_set('Europe/Budapest');


//////////
// edit project in database 
if (isset($_POST['cookieForm'])) {

	$id = $_POST['cookie'];
	$code_entered = $_POST['code'];

	//Check code
	$query = $db->prepare("SELECT * FROM `cookies` WHERE `id` = :id");
    $query->bindParam(":id", $id, PDO::PARAM_STR);
    $query->execute();
    $result = $query->fetch(PDO::FETCH_OBJ);
    $code = $result->code;
    $text = $result->text;

    if ($code_entered == $code) {
    	$text2 = "login".$id;
		setcookie("pc",$text2,time()+(60*60*24*365*10));
		echo '<div class="alert alert-success center-block" role="alert">Cookie set ('.$text.')</div>';
    }
    else {
    	echo '<div class="alert alert-danger center-block" role="alert">Hiba</div>';
    }
}

// Select right cookie
?>
<div class="cookie center-block">

<h3>Süti</h3>

<br><br>
<form method="post" action="pc.php">
	<label class="radio-inline">
	  	<input type="radio" name="cookie" value="2" checked> Iroda
	</label>
	<label class="radio-inline">
	  	<input type="radio" name="cookie" value="3"> Lajos mobil
	</label>
	<br>
	<label class="radio-inline">
	  	<input type="radio" name="cookie" value="4"> Laptop (permet)
	</label>
	<br>
	<label class="radio-inline">
	  	<input type="radio" name="cookie" value="6"> Gép 1
	</label>
	<label class="radio-inline">
	  	<input type="radio" name="cookie" value="7"> Gép 2
	</label>
	<label class="radio-inline">
	  	<input type="radio" name="cookie" value="8"> Rakodó
	</label>
	<br>
	

	<br><br>

	<div class="form-group">
		<label for="amount">Code</label>
		<input  style="width: 100px;" class="form-control" type="number" step="1" min="0" value="0" name="code" required>
	</div>

	<br><br>

	<button type="submit" class="btn btn-primary" name="cookieForm" value="Submit">Küldés</button>
</form>

</div>

