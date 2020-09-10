<?php
include "user.php";

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
                    "image"=>$res["image"]
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
        if()
        return array(
            "status" => "success",
            "text" => $text,
            "user_id" => $user_id,
            "parent_id" => $parent_id,
            "date" => $date
        );
    }
    function delete ($comment_id) {

    }
    function update($comment_id, $user_id, $text) {

    }
}