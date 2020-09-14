<?php

include_once '../libs/php-jwt-master/src/BeforeValidException.php';
include_once '../libs/php-jwt-master/src/ExpiredException.php';
include_once '../libs/php-jwt-master/src/SignatureInvalidException.php';
include_once '../libs/php-jwt-master/src/JWT.php';
use \Firebase\JWT\JWT;

class User {
    private $name_table = "user";
    private $conn;
    private $path="../user-data/";

    public function __construct($db) {
        $this->conn = $db;
    }
    public  function addImage($file, $id) {
        $full_path=$this->path."user-".$id;
        if(!file_exists($full_path)) {
            mkdir('path/to/directory', 0777, true);
        }
        
    }
    public function creat($first_name,$second_name,$email,$number,
                            $date,$town,$password,$confirm_password) {
        if( empty($town)&&
            empty($number)&&
            empty($date)&&
            empty($first_name)&&
            empty($second_name)&&
            empty($email)&&
            empty($password)) {

            return array(
                "status" => "error",
                "message" => "Empty text.");
             }

        if($password != $confirm_password) {
            return array(
                "status" => "error",
                "message" => "Password mismatch.");
        }

        $query="SELECT email FROM ".$this->name_table." WHERE email = ? ";
        $stmt = $this->conn->prepare($query);
        $email=htmlspecialchars(strip_tags($email));
        $stmt->bindParam(1, $email);
        $stmt->execute();

        if($stmt->rowCount()>0) {
            return array(
                "status" => "error",
                "message" => "this email already exists.");
        }

        $query="INSERT INTO ".$this->name_table."
        SET
            first_name=:first_name,
            second_name=:second_name,
            number=:number,
            password=:password,
            date=:date,
            email=:email,
            town=:town";

        $stmt = $this->conn->prepare($query);

        $first_name=htmlspecialchars(strip_tags($first_name));
        $second_name=htmlspecialchars(strip_tags($second_name));
        $town=htmlspecialchars(strip_tags($town));
        $date=htmlspecialchars(strip_tags($date));
        $number=htmlspecialchars(strip_tags($number));

        $stmt->bindParam(":first_name", $first_name);
        $stmt->bindParam(":second_name", $second_name);
        $stmt->bindParam(":email", $email);
        $stmt->bindParam(":number", $number);
        $stmt->bindParam(":date", $date);
        $stmt->bindParam(":town", $town);

        $password=password_hash($password,PASSWORD_BCRYPT);
        $stmt->bindParam(":password", $password);

        if($stmt->execute()) {

            return array(
                "status" => "success",
                "message" => "Create success.");

        }
        return array(
            "status" => "error",
            "message" => "Create error.");
    }

    public function signIn($password,$email,$key,$iss,$aud,$iat,$nbf) {

        if(empty($password) && empty($email)) {
            return array(
                "status" => "error",
                "message" => "Empty text.");
        }
        $query="SELECT * FROM ".$this->name_table." WHERE email = ?";
        $stmt = $this->conn->prepare($query);
        $email=htmlspecialchars(strip_tags($email));
        $stmt->bindParam(1, $email);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if(($stmt->rowCount() == 0) || !password_verify($password,$row["password"]) ) {
            return array(
                "status" => "error",
                "message" => "Incorrect email or passwords.");
        }
        http_response_code(201);
        $token = array(
            "iss" => $iss,
            "aud" => $aud,
            "iat" => $iat,
            "nbf" => $nbf,
            "data" => array(
                "id" => $row["id"],
                "first_name" => $row["first_name"],
                "second_name" => $row["second_name"],
                "email" => $email,
                "date" => $row["date"],
                "number" => $row["number"],
                "town" => $row["town"],
            )
        );
        $jwt = JWT::encode($token, $key);
        return array(
            "status" => "success",
            "message" => "login successful.",
            "jwt" => $jwt);
    }
    public  function  validate($jwt, $key) {
        try {
            $decoded = JWT::decode($jwt, $key, array('HS256'));
            return array(
                "status" => "success",
                "message" => "Access is allowed.",
                "jwt" => $decoded->data);
        }
        catch (Exception $e) {

            return array(
                "status" => "error",
                "message" => $e->getMessage());
        }
    }
    public function update($edit_name ,$edit_text ,$jwt) {}

    public function find($id) {
        if(empty($id)) {
            return null;
        }
        $query="SELECT * FROM ".$this->name_table." WHERE id = ?";
        $stmt =  $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        if(!$stmt->execute()){
            return null;
        }
        if($stmt->rowCount() == 0) {
            return null;
        }
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function delete($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
}