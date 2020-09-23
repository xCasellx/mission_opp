<?php
include_once '../block/include_header.php';
include_once '../config/data-base.php';

$database = new Database();

$db = $database->getConnection();

$query = "SELECT * FROM country";
$stmt = $db->prepare($query);;

$stmt->execute();
$list = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($list);
