<?php
header("Access-Control-Allow-Origin: http://opp-site/");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../config/data-base.php';
include_once '../objects/user.php';

$database=new Database();
$db = $database->getConnection();

$user=new User($db);
$data = json_decode(file_get_contents("php://input"));


$user->Creat($data->first_name,$data->second_name,$data->email,$data->number,
                $data->date,$data->town,$data->password,$data->confirm_password);




