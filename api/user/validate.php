<?php
include_once '../block/include_header.php';

include_once '../config/data-base.php';
include_once '../objects/user.php';
include_once '../config/core.php';

$database=new Database();
$db = $database->getConnection();

$user=new User($db);

$data = json_decode(file_get_contents("php://input"));

$res=$user->validate($data->jwt,$key);

if($res["status"]==="error") {
    http_response_code(401);
    echo json_encode($res);
    exit;
}
http_response_code(202);
echo json_encode($res);