<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

include $_SERVER['DOCUMENT_ROOT']."/api/libs/PHPMailer/PHPMailer.php";
include $_SERVER['DOCUMENT_ROOT']."/api/libs/PHPMailer/SMTP.php";
include $_SERVER['DOCUMENT_ROOT']."/api/libs/PHPMailer/Exception.php";


class sendMail
{
    public  $mail;

    public function getMail()
    {
        $this->mail = new PHPMailer;
        $this->mail->isSMTP();
        $this->mail->Host = 'smtp.gmail.com';
        $this->mail->Port = 587;
        $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $this->mail->SMTPAuth = true;
        $this->mail->Username = 'testalph55@gmail.com';
        $this->mail->Password = 'QqKSY7DSG';
        $this->mail->setFrom('testalph55@gmail.com', 'Casell');
        return $this->mail;
    }
}
