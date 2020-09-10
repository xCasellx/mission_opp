<?php

include_once '../block/include_header.php';

include_once '../config/data-base.php';
include_once '../objects/comment.php';

$database = new Database();
$db = $database->getConnection();
$data = json_decode(file_get_contents("php://input"));

$comm = new Comment($db);
$res = $comm->delete($data->id);

if ($res["status"] === "error") {
    http_response_code(400);
    echo json_encode($res);
    exit;
}
http_response_code(201);
echo json_encode($res);
