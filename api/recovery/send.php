<?php
include_once '../block/include_header.php';
include_once '../objects/user.php';
include_once '../config/data-base.php';
include_once '../config/core.php';

$data = json_decode(file_get_contents("php://input"));
$database = new Database();
$db = $database->getConnection();
$user = new User($db);
http_response_code(401);

$res = $user->emailHash($data->email,"pages/recovery.php",'Recovery Password');

if($res["status"] === "error") {
    http_response_code(401);
    echo json_encode($res);
    exit;
}
http_response_code(201);
echo json_encode($res);







