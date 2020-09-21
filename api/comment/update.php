<?php
include_once '../block/include_header.php';
include_once '../objects/user.php';
include_once '../config/data-base.php';
include_once '../objects/comment.php';
include_once '../config/core.php';

$database = new Database();
$db = $database->getConnection();
$data = json_decode(file_get_contents("php://input"));

$user = new User($db);
$user_data = $user->validate($data->jwt, $key);
if ($user_data["status"] === "error") {
    http_response_code(400);
    echo json_encode($user_data);
    exit;
}

$comm = new Comment($db);
$res = $comm->update($data->id,$user_data["jwt"]->id,$data->text);

if ($res["status"] === "error") {
    http_response_code(400);
    echo json_encode($res);
    exit;
}
http_response_code(201);
echo json_encode($res);

