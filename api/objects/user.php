<?php
include_once '../libs/php-jwt-master/src/BeforeValidException.php';
include_once '../libs/php-jwt-master/src/ExpiredException.php';
include_once '../libs/php-jwt-master/src/SignatureInvalidException.php';
include_once '../libs/php-jwt-master/src/JWT.php';
include_once "../config/config-mail.php";

use \Firebase\JWT\JWT;

class User {
    private $name_table = "user";
    private $conn;
    private $pattern_password = '/(?=.*[0-9])(?=.*[!@#$%^&*_])(?=.*[a-z])(?=.*[A-Z])[0-9a-zA-Z!@#$%^&*_]{6,}/';

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public  function updateImage($file, $id, $config_jwt)
    {
        $fold_name = "user-".$id;
        $fold = $_SERVER['DOCUMENT_ROOT']."/api/user-data/".$fold_name;
        if ( !file_exists($fold) ) {
            $oldumask = umask(0);
            mkdir($fold, 0777, true);
            umask($oldumask);
        }
        $tmp_name = $file["tmp_name"];
        $full_path_image = $fold . "/user-imag.jpg";
        move_uploaded_file($tmp_name, $full_path_image);
        $query = "UPDATE " . $this->name_table . " SET image = :image WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":image", $fold_name);
        $stmt->bindParam(":id", $id);
        if ($stmt->execute()) {
            $jwt = $this->createJWT($config_jwt, $id);
            $link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ?
                    "https" : "http") . "://" . $_SERVER['HTTP_HOST'];
            return array(
                "status" => "success",
                "image" => ($link . "/api/user-data/" . $fold_name . "/user-imag.jpg"),
                "jwt" => $jwt
            );
        }
        return array(
            "status" => "error",
            "message" => "Error update." . json_encode($stmt->errorInfo()));
        
    }

    public function creat( $first_name, $second_name, $email, $number,
                           $date, $town, $password, $confirm_password)
    {
        if ( empty($town)&&
            empty($number)&&
            empty($date)&&
            empty($first_name)&&
            empty($second_name)&&
            empty($email)&&
            empty($password)) {

            return array(
                "status" => "error",
                "message" => "Empty text."
            );
             }
        if (!preg_match($this->pattern_password, $password)) {
            return array(
                "status" => "error",
                "message" => "The password must be at least 6 or more.
                              Password must consist of letters of the Latin alphabet (A-z),
                              numbers (0-9) and special characters."
            );
        }
        if ($password != $confirm_password) {
            return array(
                "status" => "error",
                "message" => "Password mismatch."
            );
        }


        if (!$this->checkEmail($email)) {
            return array(
                "status" => "error",
                "message" => "this email already exists."
            );
        }

        $query = "INSERT INTO " . $this->name_table . "
        SET
            first_name = :first_name,
            second_name = :second_name,
            number = :number,
            password = :password,
            date = :date,
            email = :email,
            town = :town";

        $stmt = $this->conn->prepare($query);

        $first_name = htmlspecialchars(strip_tags($first_name));
        $second_name = htmlspecialchars(strip_tags($second_name));
        $town = htmlspecialchars(strip_tags($town));
        $date = htmlspecialchars(strip_tags($date));
        $number = htmlspecialchars(strip_tags($number));

        $stmt->bindParam(":first_name", $first_name);
        $stmt->bindParam(":second_name", $second_name);
        $stmt->bindParam(":email", $email);
        $stmt->bindParam(":number", $number);
        $stmt->bindParam(":date", $date);
        $stmt->bindParam(":town", $town);

        $password = password_hash($password, PASSWORD_BCRYPT);
        $stmt->bindParam(":password", $password);

        if ($stmt->execute()) {

            return array(
                "status" => "success",
                "message" => "Create success."
            );

        }
        return array(
            "status" => "error",
            "message" => "Create error."
        );
    }

    public function signIn($password, $email, $config_jwt)
    {

        if (empty($password) && empty($email)) {
            return array(
                "status" => "error",
                "message" => "Empty text.");
        }
        $query ='SELECT * FROM user WHERE email = ?';
        $stmt = $this->conn->prepare($query);
        $email = htmlspecialchars(strip_tags($email));
        $stmt->bindParam(1, $email);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (($stmt->rowCount() == 0) || !password_verify($password,$row["password"]) ) {
            return array(
                "status" => "error",
                "message" => "Incorrect email or passwords.");
        }
        $jwt = $this->createJWT($config_jwt, $row["id"]);
        return array(
            "status" => "success",
            "message" => "login successful.",
            "jwt" => $jwt);
    }

    public  function validate($jwt, $key) {
        try {
            $decoded = JWT::decode($jwt, $key, array('HS256'));
            return array(
                "status" => "success",
                "message" => "Access is allowed.",
                "jwt" => $decoded->data
            );
        } catch (Exception $e) {
            return array(
                "status" => "error",
                "message" => $e->getMessage()
            );
        }
    }

    public function update($edit_name ,$edit_text ,$password, $confirm_password,$id, $config_jwt)
    {
        if (empty($edit_text) && empty($edit_name)) {
            return array(
                "status" => "error",
                "message" => "Empty text."
            );
        }
        switch ($edit_name) {
            case "email":
                if (!$this->checkEmail($edit_text)) {
                    return array(
                        "status" => "error",
                        "message" => "This email already exists."
                    );
                }
                $query = "SELECT * FROM " . $this->name_table . " WHERE id = ?";
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(1, $id);
                $stmt->execute();
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                if (!password_verify($password, $row["password"])) {
                    return array(
                        "status" => "error",
                        "message" => "Incorrect passwords."
                    );
                }
                $query = "UPDATE $this->name_table  SET upd_email = :text WHERE id = :id";
                $stmt = $this->conn->prepare($query);
                $text = htmlspecialchars(strip_tags($edit_text));
                $stmt->bindParam(":text", $text);
                $stmt->bindParam(":id", $id);
                $stmt->execute();
                $res = $this->emailHash($text,"pages/upd-email.php",'Update Email');
                $jwt = $this->createJWT($config_jwt, $id);
                if($res["status"] === "error" ){
                    return  $res;
                }
                $res["jwt"] = $jwt;
                return $res;
            case "date":
                $temp_date = strtotime($edit_text);
                $temp_upd = strtotime((date('Y')-5).date('-m-d'));
                if ($temp_date > $temp_upd) {
                    return array(
                        "status" => "error",
                        "message" => "Wrong date."
                    );

                }
                break;
            case "password":

                if (empty($password) && empty($confirm_password)) {
                    return array(
                        "status" => "error",
                        "message" => "Empty password"
                    );
                }
                if (!preg_match($this->pattern_password, $edit_text)) {
                    return array(
                        "status" => "error",
                        "message" => "The password must be at least 6 or more.
                              Password must consist of letters of the Latin alphabet (A-z),
                              numbers (0-9) and special characters."
                    );
                }

                $query = "SELECT * FROM " . $this->name_table . " WHERE id = ?";
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(1, $id);
                $stmt->execute();
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                if (!password_verify($password, $row["password"])) {
                    return array(
                        "status" => "error",
                        "message" => "Incorrect passwords."
                    );
                }
                if ($edit_text !== $confirm_password) {
                    if (!password_verify($password, $row["password"])) {
                        return array(
                            "status" => "error",
                            "message" => "Password mismatch."
                        );
                    }
                }
                $edit_text = password_hash($edit_text, PASSWORD_BCRYPT);
                break;
            default:
                $edit_text = htmlspecialchars(strip_tags($edit_text));
                break;;
        }
        $component = htmlspecialchars(strip_tags($edit_name));
        $query = "UPDATE " . $this->name_table . " SET " . $component . " = :text WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $text = htmlspecialchars(strip_tags($edit_text));
        $stmt->bindParam(":text", $text);
        $stmt->bindParam(":id", $id);
        if ($stmt->execute()) {
            $jwt = $this->createJWT($config_jwt, $id);
            return array(
                "status" => "success",
                "jwt" => $jwt
            );
        }
        return array(
            "status" => "error",
            "message" => "Error update." . $stmt->errorInfo());
    }

    public function updateEmail($hash, $id, $config_jwt)
    {
        if (!$this->checkHash($hash)) {
            return array(
                "status" => "error",
                "message" => "link is invalid"
            );
        }
        if ($hash === null) {
            return array(
                "status" => "error",
                "message" => "link is invalid"
            );
        }

        $query = "SELECT * FROM $this->name_table  WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();
        $user_data = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user_data["upd_email"] === "") {
            return array(
                "status" => "error",
                "message" => "Incorrect new email."
            );
        }
        $email = $user_data["upd_email"];

        $query = "UPDATE ". $this->name_table ." SET email_confirm = 1 , hash = null , email = :email ,upd_email = '' WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->bindParam(":email", $email);
        if (!$stmt->execute()) {
            return array(
                "status" => "error",
                "message" => "error update email" . json_encode($stmt->errorInfo())
            );
        }

        $jwt = $this->createJWT($config_jwt,$id);
        return array(
            "status" => "success",
            "jwt" => $jwt
        );
    }

    public  function createJWT($config_jwt, $id)
    {
        $query ='SELECT user.*, city.name AS city , region.name as region , country.name as country FROM user 
            JOIN city ON city.id = user.town 
            JOIN region ON region.id = city.region_id 
            JOIN country ON country.id = region.country_id 
            WHERE user.id = ?';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ?
                "https" : "http") . "://" . $_SERVER['HTTP_HOST'];
        $token = array(
            "iss" => $config_jwt["iss"],
            "aud" => $config_jwt["aud"],
            "iat" => $config_jwt["iat"],
            "nbf" => $config_jwt["nbf"],
            "data" => array(
                "id" => $row["id"],
                "first_name" => $row["first_name"],
                "second_name" => $row["second_name"],
                "email" => $row["email"],
                "date" => $row["date"],
                "number" => $row["number"],
                "town" => $row["city"].",".$row["region"].",".$row["country"],
                "email_confirm" => $row["email_confirm"],
                "image" => ($row["image"] != null) ? $link."/api/user-data/" . $row["image"] . "/user-imag.jpg" : null,
            )
        );
        return JWT::encode($token, $config_jwt["key"]);
    }

    public function find($id)
    {
        if (empty($id)) {
            return null;
        }
        $query = "SELECT * FROM " . $this->name_table . " WHERE id = ?";
        $stmt =  $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        if (!$stmt->execute()) {
            return null;
        }
        if ($stmt->rowCount() == 0) {
            return null;
        }
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function checkEmail($email)
    {
        $query = "SELECT * FROM " . $this->name_table . " WHERE email = ?";
        $stmt = $this->conn->prepare($query);
        $edit_text = htmlspecialchars(strip_tags($email));
        $stmt->bindParam(1, $edit_text);
        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            return false;
        }
        return true;
    }

    public  function emailConfirm($hash, $id, $config_jwt)
    {
        if(!$this->checkHash($hash)) {
            return array(
                "status" => "error",
                "message" => "link is invalid"
            );
        }
        if($hash === null ) {
            return array(
                "status" => "error",
                "message" => "link is invalid"
            );
        }
        $query = "UPDATE " . $this->name_table . " SET email_confirm = 1 , hash = null WHERE hash = :hash and id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->bindParam(":hash", $hash);
        $stmt->execute();
        if(!$stmt->execute()){
            return array(
                "status" => "error",
                "message" => "error confirm email".json_encode($stmt->errorInfo())
            );
        }
        $jwt = $this->createJWT($config_jwt, $id);
        return array(
            "status" => "success",
            "jwt" => $jwt
        );
    }

    public function recoveryPassword($new_password, $confirm_password, $hash)
    {
        if (empty($new_password) && empty($confirm_password)) {
            return array(
                "status" => "error",
                "message" => "Empty password"
            );
        }

        if (!preg_match($this->pattern_password, $new_password)) {
            return array(
                "status" => "error",
                "message" => "The password must be at least 6 or more.
                              Password must consist of letters of the Latin alphabet (A-z),
                              numbers (0-9) and special characters."
            );
        }
        if ($new_password !== $confirm_password) {
                return array(
                    "status" => "error",
                    "message" => "Password mismatch."
                );
        }
        $new_password = password_hash($new_password, PASSWORD_BCRYPT);
        $query = "UPDATE " . $this->name_table . " SET password = :new_password , hash = null WHERE hash = :hash";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":new_password", $new_password);
        $stmt->bindParam(":hash", $hash);
        if(!$stmt->execute()){
            return array(
                "status" => "error",
                "message" => "error recovery password ->".json_encode($stmt->errorInfo())
            );
        }
        return array(
            "status" => "success",
            "message" => "Password edit."
        );
    }

    public  function checkHash($hash)
    {
        if(empty($hash)){
            return false;
        }
        $query = "SELECT * FROM " . $this->name_table . " WHERE hash = ?";
        $stmt = $this->conn->prepare($query);
        $edit_text = htmlspecialchars(strip_tags($hash));
        $stmt->bindParam(1, $edit_text);
        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            return true;
        }
        return false;
    }

    public function emailHash($email, $link_page, $subject)
    {
        if($this->checkEmail($email)) {
            return array(
                "status" => "success",
                "message" => "Sending password recovery data send on email"
            );
        }
        $hash = password_hash(microtime() .$email . time(),PASSWORD_BCRYPT);
        $link = $link = 'http://'.$_SERVER['HTTP_HOST']."/$link_page?hash=".$hash;
        $sendmail = new sendMail();
        $mail = $sendmail->getMail();
        $mail->addAddress($email, $email);
        $mail->Subject = $subject;
        $mail->msgHTML("
                        <b>link:</b>$link;
                    ");
        $mail->send();
        $query = "UPDATE " . $this->name_table . " SET hash = :hash WHERE email = :email";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":hash", $hash);
        $stmt->bindParam(":email", $email);
        if(!$stmt->execute()){
            return array(
                "status" => "error",
                "message" => "Error create hash");
        }
        return array(
            "status" => "success",
            "message" => "Sending password recovery data send on email"
        );
    }

    public function delete($id)
    {
        $query = "DELETE FROM " .$this->name_table. " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
}