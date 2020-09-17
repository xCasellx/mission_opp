<?php

include_once '../block/include_header.php';
include_once '../objects/user.php';
include_once '../config/data-base.php';
include_once '../objects/comment.php';
include_once '../config/core.php';

$database = new Database();
$db = $database->getConnection();
$data = json_decode(file_get_contents("php://input"));


$comm = new Comment($db);
$user = new User($db);
$user_data = $user->validate($data->jwt, $key);
if ($user_data["status"] === "error") {
    http_response_code(400);
    echo json_encode($user_data);
    exit;
}

$res = $comm->create($user_data["jwt"] ,$data->text,$data->parent_id);

if ($res["status"] === "error") {
    http_response_code(400);
    echo json_encode($res);
    exit;
}
http_response_code(201);
echo json_encode($res);
