<?php

require_once('../config/config.php');
$db = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.'', DB_USER, DB_PASS);
$db -> exec("set names utf8");

$type2 = $_POST['type2'];

echo '<select class="form-control" name="field">';
echo '<option value="0">Válassz ki...</option>';
$query = $db->prepare("SELECT * FROM fields WHERE `complete` < 1 AND `cutting` = 1");
$query->execute();
while($row = $query->fetch()) {
    $seed = $row['seed'];

    $query2 = $db->prepare("SELECT * FROM seed WHERE `id` = :id");
    $query2->bindParam(":id", $seed, PDO::PARAM_STR);
    $query2->execute();
    $result = $query2->fetch(PDO::FETCH_OBJ);
    $field_type2 = $result->type2;

    if ($field_type2 == $type2) {
    	echo "<option value='".$row['id']."'>".$row['name']."</option>";
    }
}
echo '</select>';
?>