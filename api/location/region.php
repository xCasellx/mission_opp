<?php
include_once '../block/include_header.php';
include_once '../config/data-base.php';


$data = json_decode(file_get_contents("php://input"));
$database = new Database();
$db = $database->getConnection();
$query = "SELECT * FROM region WHERE country_id = ?";
$stmt = $db->prepare($query);
$stmt->bindParam(1, $data->country_id);
$stmt->execute();
$list = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($list);