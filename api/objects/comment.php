<?php
include_once '../config/config.php';
include_once '../objects/user.php';
include_once "../config/config-mail.php";

class Comment
{
    private $name_table = "comments";
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    function read()
    {
        $comments = array();
        $user = new User($this->conn);
        $query ="SELECT * FROM " . $this->name_table;
        $stmt = $this->conn->prepare($query);
        if (!$stmt->execute()) {
            $comments = array(
                "status" => "error",
                "massage" => "Unable to load comments"
            );
            return $comments;
        }
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            extract($row);
            $res = $user->find($user_id);
            array_push($comments,array(
                "id" => $id,
                "parent_id" => $parent_id,
                "user_id" => $user_id,
                "date" => $date,
                "text" => $text,
                "edit_check" => $edit_check,
                "user" => array(
                    "first_name" => $res["first_name"],
                    "second_name" => $res["second_name"],
                    "image" => ($res["image"] != null) ? URL . "/api/user-data/" . $res["image"] . "/user-imag.jpg" : null
                )
            )) ;
        }
        return $comments;

    }

    function create($user, $text ,$parent_id)
    {
        if (empty($text)) {
            return array(
                "status" => "error",
                "message" => "empty text."
            );
        }
        $query = "INSERT INTO " . $this->name_table . " 
        SET
            user_id=:user_id,
            parent_id=:parent_id,
            text=:text,
            date=:date";
        $text = htmlspecialchars(strip_tags($text));
        $date = date("Y-m-d H:i:s");
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":date", $date);
        $stmt->bindParam(":text", $text);
        $stmt->bindParam(":parent_id", $parent_id);
        $stmt->bindParam(":user_id", $user->id);
        if (!$stmt->execute()) {
            return array(
                "status" => "error",
                "message" => "error creating comment"
            );
        }
        $id = $this->conn->lastInsertId();
        $sendmail = new sendMail();
        $mail = $sendmail->getMail();
        $mail->addAddress($user->email, $user->first_name.' '.$user->second_name);

        $mail->Subject = 'Your comment';
        $mail->msgHTML("
                        <b>Date:</b> $date<br><br>
                        <b>Text:</b><br>$text
                    ");
        $mail->send();

        if ($parent_id != null) {
            $query = "SELECT user_id FROM " . $this->name_table . " WHERE id =:parent_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":parent_id", $parent_id);
            $stmt->execute();
            if ($stmt->rowCount() > 0) {
                $res=$stmt->fetch(PDO::FETCH_ASSOC);
                $user_recipient = new User($this->conn);
                $email_recipient = $user_recipient->find( $res["user_id"] );
                $sendmail = new sendMail();
                $mail = $sendmail->getMail();
                $mail->addAddress($email_recipient["email"], $email_recipient["first_name"]." ".$email_recipient["second_name"]);
                $mail->Subject = 'Your comment was answered';
                $mail->msgHTML("
                        <b>Name:</b>$user->first_name $user->second_name<br>
                        <b>Email:</b> $user->email<br><br>
                        <b>Date:</b> $date<br><br>
                        <b>Text:</b><br>$text
                    ");
                $mail->send();
            }
        }
        return array(
            "status" => "success",
            "id" => $id,
            "parent_id" => $parent_id,
            "user_id" => $user->id,
            "date" => $date,
            "text" => $text,
            "user" => array(
                "first_name" => $user->first_name,
                "second_name" => $user->second_name,
                "image" => $user->image
            )
        );
    }

    function delete ($comment_id,$user_id) {
        if (empty($comment_id)) {
            return array(
                "status" => "error",
                "message" => "not delete id empty"
            );
        }
            $query = "SELECT * FROM " . $this->name_table . " WHERE id = ? AND user_id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(1, $comment_id);
            $stmt->bindParam(2, $user_id);
            $stmt->execute();
            if ($stmt->rowCount() <= 0) {
                return array(
                    "status" => "error",
                    "message" => "not deleted"
                );
            }
        $sql = "DELETE FROM comments WHERE id =  :comment_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":comment_id", $comment_id);
        if ($stmt->execute()) {
            return array(
                "status" => "success",
                "message" => "successfully deleted"
            );
        }
        return array(
            "status" => "error",
            "message" => $stmt->error
        );
    }

    function update($comment_id, $user_id, $text)
    {
        if(empty($comment_id)&& empty($text)) {
            return array(
                "status" => "error",
                "message" => "empty text"
            );
        }
        $query = "UPDATE " . $this->name_table . " SET text = :text , edit_check = 1 WHERE id = :id AND user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $text = htmlspecialchars(strip_tags($text));
        $stmt->bindParam(":text", $text);
        $stmt->bindParam(":id", $comment_id);
        $stmt->bindParam(":user_id", $user_id);
        if ($stmt->execute()) {
            return array(
                "status" => "success",
                "message" => "success update"
            );
        }
        return array(
            "status" => "error",
            "message" => "error update"
        );

    }
}