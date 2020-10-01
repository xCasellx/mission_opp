<?php
include_once '../config/data-base.php';
include_once '../block/include_header.php';
include_once '../objects/user.php';

$database=new Database();
$db = $database->getConnection();

$user = new User($db);
$data = json_decode(file_get_contents("php://input"));


$res = $user->checkHash($data->hash);
if($res) {
    http_response_code(201);
    echo json_encode(array(
        "status" => "success",
        "hash" => $data->hash
    ));
    exit;
}
http_response_code(401);
echo json_encode(array(
    "status" => "error",
    "message" => "Hash not found"
));



