<?php
include_once '../block/include_header.php';
include_once '../config/data-base.php';


$data = json_decode(file_get_contents("php://input"));
$database = new Database();
$db = $database->getConnection();
$query = "SELECT * FROM city WHERE region_id = ?";
$stmt = $db->prepare($query);;
$stmt->bindParam(1, $data->region_id);
$stmt->execute();
$list = array();
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    extract($row);
    array_push($list, array(
        "id" => $id,
        "name" => $name
    ));
}
echo json_encode($list);