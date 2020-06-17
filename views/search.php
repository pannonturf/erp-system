
<?php
require_once('../config/config.php');

// Connect with the database
$db = new mysqli($dbHost, $dbUsername, $dbPassword, $dbName);

// Get search term
$searchTerm = $_GET['term'];

// Get matched data from skills table
$query = $db->query("SELECT * FROM customers WHERE name LIKE '%".$searchTerm."%' AND status = '1' ORDER BY name ASC");

// Generate skills data array
$nameData = array();
if($query->num_rows > 0){
    while($row = $query->fetch_assoc()){
        $data['id'] = $row['id'];
        $data['value'] = $row['name'];
        $data['delivery'] = $row['delivery'];
        $data['payment'] = $row['payment'];
        array_push($nameData, $data);
    }
}

// Return results as json encoded array
echo json_encode($nameData);

?>