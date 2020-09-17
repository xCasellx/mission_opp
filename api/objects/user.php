<?php
include_once '../config/config.php';
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
    public  function updateImage($file, $id,$key,$iss,$aud,$iat,$nbf) {
        $fold_name="user-".$id;
        $fold="../user-data/user-".$id;
        if(!file_exists($fold)) {
            mkdir($fold, 0777, true);
        }
        $tmp_name = $file["tmp_name"];
        $full_path_image= $fold."/user-imag.jpg";
        move_uploaded_file($tmp_name, $full_path_image);
        $query="UPDATE ".$this->name_table." SET image = :image WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":image", $fold_name);
        $stmt->bindParam(":id", $id);

        if($stmt->execute()) {
            $query="SELECT * FROM ".$this->name_table." WHERE id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(1, $id);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $jwt=$this->createJWT($key,$iss,$aud,$iat,$nbf,$row);
            return array(
                "status" => "success",
                "image" => (URL."/api/user-data/".$fold_name."/user-imag.jpg"),
                "jwt" => $jwt);
        }
        return array(
            "status" => "error",
            "message" => "Error update.".json_encode($stmt->errorInfo()));
        
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
        $jwt=$this->createJWT($key,$iss,$aud,$iat,$nbf,$row);
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
    public function update($edit_name ,$edit_text ,$password, $confirm_password,$id,$key,$iss,$aud,$iat,$nbf) {
        if(empty($edit_text) && empty($edit_name)) {
            return array(
                "status" => "error",
                "message" => "Empty text.");
        }
        switch ($edit_name) {
            case "email":
                $query="SELECT * FROM ".$this->name_table." WHERE email = ?";
                $stmt = $this->conn->prepare($query);
                $edit_text=htmlspecialchars(strip_tags($edit_text));
                $stmt->bindParam(1, $edit_text);
                $stmt->execute();
                if($stmt->rowCount()>0) {
                    return array(
                        "status" => "error",
                        "message" => "This email already exists.");
                }
                break;
            case "date":
                $temp_date = strtotime($edit_text);
                $temp_upd=strtotime((date('Y')-5).date('-m-d'));
                if($temp_date > $temp_upd) {
                    return array(
                        "status" => "error",
                        "message" => "Wrong date.");
                }
                break;
            case "password":
                if(empty($password) && empty($confirm_password)){
                    return array(
                        "status" => "error",
                        "message" => "Empty password");
                }
                $query="SELECT * FROM ".$this->name_table." WHERE id = ?";
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(1, $id);
                $stmt->execute();
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                if(!password_verify($password,$row["password"])) {
                    return array(
                        "status" => "error",
                        "message" => "Incorrect passwords.");
                }
                if($edit_text !== $confirm_password){
                    if(!password_verify($password,$row["password"])) {
                        return array(
                            "status" => "error",
                            "message" => "Password mismatch.");
                    }
                }
                $edit_text=password_hash($edit_text,PASSWORD_BCRYPT);
                break;
            default:
                $edit_text=htmlspecialchars(strip_tags($edit_text));
                break;;
        }
        $component=htmlspecialchars(strip_tags($edit_name));
        $query="UPDATE ".$this->name_table." SET ".$component." = :text WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $text=htmlspecialchars(strip_tags($edit_text));
        $stmt->bindParam(":text", $text);
        $stmt->bindParam(":id", $id);
        if($stmt->execute()) {
            $query="SELECT * FROM ".$this->name_table." WHERE id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(1, $id);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $jwt=$this->createJWT($key,$iss,$aud,$iat,$nbf,$row);
            return array(
                "status" => "success",
                "jwt" => $jwt);
        }
        return array(
            "status" => "error",
            "message" => "Error update.".json_encode($stmt->errorInfo()));
    }
    public  function createJWT($key,$iss,$aud,$iat,$nbf,$row){
        $token = array(
            "iss" => $iss,
            "aud" => $aud,
            "iat" => $iat,
            "nbf" => $nbf,
            "data" => array(
                "id" => $row["id"],
                "first_name" => $row["first_name"],
                "second_name" => $row["second_name"],
                "email" => $row["email"],
                "date" => $row["date"],
                "number" => $row["number"],
                "town" => $row["town"],
                "image" => ($row["image"] != null)? URL."/api/user-data/".$row["image"]."/user-imag.jpg": null,
            )
        );
        return JWT::encode($token, $key);
    }
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