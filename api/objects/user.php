<?php

include_once '../libs/php-jwt-master/src/BeforeValidException.php';
include_once '../libs/php-jwt-master/src/ExpiredException.php';
include_once '../libs/php-jwt-master/src/SignatureInvalidException.php';
include_once '../libs/php-jwt-master/src/JWT.php';
use \Firebase\JWT\JWT;


class User {
    private $name_table = "user";
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function Creat($first_name,$second_name,$email,$number,
                            $date,$town,$password,$confirm_password) {
        if( empty($town)&&
            empty($number)&&
            empty($first_name)&&
            empty($second_name)&&
            empty($email)&&
            empty($password)) {

            http_response_code(400);
            echo json_encode(array("message" => "Empty text."));
            return false;
        }

        if($password != $confirm_password){
            http_response_code(400);
            echo json_encode(array("message" => "Password mismatch."));
            return false;
        }

        $query="SELECT email FROM ".$this->name_table." WHERE email = ? ";
        $stmt = $this->conn->prepare($query);
        $email=htmlspecialchars(strip_tags($email));
        $stmt->bindParam(1, $email);
        $stmt->execute();

        if($stmt->rowCount()>0) {
            http_response_code(400);
            echo json_encode(array("message" => "this email already exists."));
            return false;
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
            http_response_code(200);
            echo json_encode(array("message" => "Create success."));
            return true;
        }
        http_response_code(400);
        echo json_encode(array("message" => "Create error."));
        return  false;
    }

    public function Sign_in($password,$email,$key,$iss,$aud,$iat,$nbf) {

        if(empty($password) && empty($email)) {
            http_response_code(400);
            echo json_encode(array("message" => "Empty text.."));
            return false;
        }
        $query="SELECT * FROM ".$this->name_table." WHERE email = ?";
        $stmt = $this->conn->prepare($query);
        $email=htmlspecialchars(strip_tags($email));
        $stmt->bindParam(1, $email);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if(($stmt->rowCount() == 0) || !password_verify($password,$row["password"]) ) {
            http_response_code(401);
            echo json_encode(array("message" => "Incorrect email or passwords"));
            return false;
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
        echo json_encode(
            array(
                "message" => "Успешный вход в систему.",
                "jwt" => $jwt
            ));
        return true;
    }
    public  function  Validate($jwt, $key) {
        try {
            $decoded = JWT::decode($jwt, $key, array('HS256'));
            http_response_code(200);

            echo json_encode(array(
                "message" => "Access is allowed.",
                "data" => $decoded->data
            ));
        }
        catch (Exception $e){

            http_response_code(401);

            echo json_encode(array(
                "message" => "Access closed.",
                "error" => $e->getMessage()
            ));
        }
    }

    public function Sign_out() {

    }
}