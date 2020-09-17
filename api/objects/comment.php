<?php
include "user.php";
include_once '../config/config.php';

class Comment {
    private $name_table = "comments";
    private $conn;

    public function __construct($db) {
        $this->conn=$db;
    }
    function read() {
        $comments= array();
        $user=new User($this->conn);
        $query="SELECT * FROM ".$this->name_table;
        $stmt = $this->conn->prepare($query);
        if(!$stmt->execute()) {
            $comments=array(
                "status"=>"error",
                "massage"=>"Unable to load comments"
            );
            return $comments;
        }
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            extract($row);
            $res=$user->find($user_id);
            array_push($comments,array(
                "id" => $id,
                "parent_id" => $parent_id,
                "user_id" => $user_id,
                "date" => $date,
                "text" => $text,
                "user" => array(
                    "first_name"=>$res["first_name"],
                    "second_name"=>$res["second_name"],
                    "image"=>(URL."/api/user-data/".$res["image"]."/user-imag.jpg")
                )
            )) ;
        }
        return $comments;

    }
    function create($user_id, $text ,$parent_id) {
        if(empty($user_id)&&empty($text)) {
            return array(
                "status" => "error",
                "message" => "empty text."
            );
        }
        $query="INSERT INTO ".$this->name_table." 
        SET
            user_id=:user_id,
            parent_id=:parent_id,
            text=:text,
            date=:date";
        $text=htmlspecialchars(strip_tags($text));
        $date = date("Y-m-d H:i:s");
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":date", $date);
        $stmt->bindParam(":text", $text);
        $stmt->bindParam(":parent_id", $parent_id);
        $stmt->bindParam(":user_id", $user_id);
        if(!$stmt->execute()) {
            return array(
                "status" => "error",
                "message" => "error creating comment"
            );
        }
        $user=new User($this->conn);
        $user_data=$user->find($user_id);
        mail($user_data["email"], "You comment", ('Date:'.$date." ".$text),
            'From: testalph55@gmail.com');
        if($parent_id != null) {
            $query="SELECT user_id FROM ".$this->name_table." WHERE id =:parent_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":parent_id", $parent_id);
            $stmt->execute();
            if($stmt->rowCount()>0) {
                $res=$stmt->fetch(PDO::FETCH_ASSOC);
                $user_recipient=$user->find($res["user_id"]);
                mail($user_recipient["email"], "Your comment was answered by",
                    ('Who-'.$user_recipient["first_name"]." ".'Date:'.$date."text: ".$text),
                    'From: testalph55@gmail.com');
            }
        }
        return array(
            "status" => "success",
            "text" => $text,
            "user_id" => $user_id,
            "parent_id" => $parent_id,
            "date" => $date
        );
    }
    function delete ($comment_id) {
        $query="SELECT * FROM ".$this->name_table." WHERE parent_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $comment_id);
        if (!$stmt->execute()) {
            return array(
                "status" => "error",
                "message" => "1");
        }
        while($res=$stmt->fetch(PDO::FETCH_ASSOC)) {

            $this->delete($res["id"]);
        }

        $sql = "DELETE FROM comments WHERE id =  :comment_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":comment_id", $comment_id);
        if ($stmt->execute()) {
            return array(
                "status" => "success",
                "message" => "successfully deleted");
        }
        return array(
            "status" => "error",
            "message" => $stmt->error);
    }
    function update($comment_id, $user_id, $text) {

    }
}