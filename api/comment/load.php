<?php
include_once '../block/include_header.php';

include_once '../config/data-base.php';
include_once '../objects/comment.php';

$database=new Database();
$db = $database->getConnection();

$comm=new Comment($db);
$res=$comm->read();
if($res["status"]==="error"){
    http_response_code(400);
    echo json_encode($res);
    exit;
}
http_response_code(201);
echo json_encode($res);
