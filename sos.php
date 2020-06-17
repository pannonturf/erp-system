<?php 
// Database connection
require_once('config/config.php');
$db = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.'', DB_USER, DB_PASS);

if ($_COOKIE["pc"] == "login1" OR $_COOKIE["pc"] == "login2" OR $_COOKIE["pc"] == "login6") {
	$id = 2;

	$now = date("Y-m-d H:i:s");
	$login = $_COOKIE["pc"];
	
	if (! isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
		$client_ip = $_SERVER['REMOTE_ADDR'];
	}

	else {
		$client_ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
	} 

	$sql = "UPDATE `system` SET `active` = 2 WHERE `id` = :id";
	$query = $db->prepare($sql);
	$query->bindParam(":id", $id, PDO::PARAM_STR);
	$query->execute();

	$sql = "INSERT INTO `sos_log` (`id`, `login`, `datum`, `ip`) VALUES (NULL, :login, :datum, :ip)";
	$query = $db->prepare($sql);
	$query->bindParam(":login", $login, PDO::PARAM_STR);
	$query->bindParam(":datum", $now, PDO::PARAM_STR);
	$query->bindParam(":ip", $client_ip, PDO::PARAM_STR);
	$query->execute();

	echo "<script type='text/javascript'> document.location = 'sales.php'; </script>";
}
?>
