<?php
include_once '../block/include_header.php';

include_once '../config/data-base.php';
include_once '../objects/user.php';

$database=new Database();
$db = $database->getConnection();

$user=new User($db);
$data = json_decode(file_get_contents("php://input"));


$res=$user->creat($data->first_name,$data->second_name,$data->email,$data->number,
                $data->date,$data->town,$data->password,$data->confirm_password);
if($res["status"]==="error") {
    http_response_code(401);
    echo json_encode($res);
    exit;
}
http_response_code(201);
echo json_encode($res);



