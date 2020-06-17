
<?php
require_once('../config/config.php');

// Connect with the database
$db = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.'', DB_USER, DB_PASS);
$db -> exec("set names utf8");

// Get search term
$searchTerm = $_GET['term'];

// Get matched data from skills table
$query = $db->prepare("SELECT * FROM cities WHERE plz LIKE '".$searchTerm."%' ORDER BY name ASC");
$query->execute();

// Generate skills data array
$nameData = array();
if($query->rowCount() > 0){
    while($row = $query->fetch()){
        $data['id'] = $row['id'];
        $data['value'] = $row['plz']." ".$row['name'];
        $data['city'] = $row['name'];
        array_push($nameData, $data);
    }
}

// Get matched data from skills table
$query = $db->prepare("SELECT * FROM cities WHERE name LIKE '".$searchTerm."%' ORDER BY name ASC");
$query->execute();

// Generate skills data array
if($query->rowCount() > 0){
    while($row = $query->fetch()){
        $data['id'] = $row['id'];
        $data['value'] = $row['plz']." ".$row['name'];
        $data['city'] = $row['name'];
        array_push($nameData, $data);
    }
}

// Return results as json encoded array
echo json_encode($nameData);

?>