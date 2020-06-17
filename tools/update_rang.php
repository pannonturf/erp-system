<?php

require_once('../config/config.php');
$db = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.'', DB_USER, DB_PASS);


$param = urldecode ($_POST['parameter']);
$arrParam = array();
parse_str($param,$arrParam);

$arrParam = array_values($arrParam);
$arrParam = $arrParam[0];


foreach ($arrParam AS $sort=>$id) {
    
    $sortid = $sort + 1;

    $query = $db->prepare("UPDATE `order` SET `sort` = :sortid WHERE `id` = :id");

    $query->bindParam(":sortid", $sortid, PDO::PARAM_STR); 
    $query->bindParam(":id", $id, PDO::PARAM_STR);

    $query->execute();
}

?>