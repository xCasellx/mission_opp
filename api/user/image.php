<?php
include_once '../block/include_header.php';
include_once '../config/data-base.php';
include_once '../objects/user.php';
include_once '../config/core.php';

if(empty($_FILES)) {
    http_response_code(401);
    echo  json_encode(array(
       "status" => "error",
        "message" => "Empty file"
    ));
    exit();
}


$database = new Database();
$db = $database->getConnection();;
$user = new User($db);
$user_data = $user->validate($_POST["jwt"], $key);
$res = $user->updateImage($_FILES["image"], $user_data["jwt"]->id, $key, $iss, $aud, $iat, $nbf);

if($res["status"] === "error") {
    http_response_code(400);
    echo json_encode($res);
    exit;
}
http_response_code(200);
echo json_encode($res);