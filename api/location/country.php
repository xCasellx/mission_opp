<?php
include_once '../block/include_header.php';
include_once '../config/data-base.php';

$database = new Database();

$db = $database->getConnection();

$query = "SELECT * FROM country";
$stmt = $db->prepare($query);;

$stmt->execute();
$list_country=array();
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    extract($row);
    array_push($list_country ,array(
        "id" => $id,
        "name" => $name
    ));
}
echo json_encode($list_country);

