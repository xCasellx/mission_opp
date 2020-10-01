<?php
include_once '../block/include_header.php';

include_once '../config/data-base.php';
include_once '../objects/user.php';
include_once '../config/core.php';

$database = new Database();
$db = $database->getConnection();

$user = new User($db);
$data = json_decode(file_get_contents("php://input"));

$user_data = $user->validate($data->jwt, $config_jwt["key"]);
$res=$user->update($data->edit_name , $data->edit_text, $data->password, $data->confirm_password,
                    $user_data["jwt"]->id, $config_jwt);

if($res["status"] === "error") {
    http_response_code(400);
    echo json_encode($res);
    exit;
}
http_response_code(200);
echo json_encode($res);