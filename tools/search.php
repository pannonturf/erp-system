
<?php
require_once('../config/config.php');

// Connect with the database
$db = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.'', DB_USER, DB_PASS);
$db -> exec("set names utf8");

// Get search term
$searchTerm = $_GET['term'];

// Get matched data from skills table
$query = $db->prepare("SELECT * FROM customers WHERE name LIKE '%".$searchTerm."%' AND status = 1 ORDER BY name ASC");
$query->execute();


// Generate skills data array
$nameData = array();
if($query->rowCount() > 0){
    while($row = $query->fetch()){
        $data['id'] = $row['id'];
        $data['value'] = $row['name'];
        $data['plz'] = $row['plz'];
        $data['city'] = $row['city'];
        $data['street'] = $row['street'];
        $data['contactperson'] = $row['contactperson'];
        $data['phone'] = $row['phone'];
        $data['email'] = $row['email'];
        $data['delivery'] = $row['delivery'];
        $data['licence'] = $row['licence'];
        $data['payment'] = $row['payment'];
        array_push($nameData, $data);
    }
}

// Return results as json encoded array
echo json_encode($nameData);

?>