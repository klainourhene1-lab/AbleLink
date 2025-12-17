<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../PHPMailer/PHPMailer.php';
require_once __DIR__ . '/../PHPMailer/SMTP.php';
require_once __DIR__ . '/../PHPMailer/Exception.php';

function sendMail($to, $subject, $html)
{
    $mail = new PHPMailer(true);

    try {
        // SMTP Gmail
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'personinkonnu@gmail.com';  // ← حط إيميلك
        $mail->Password = 'rnjt hmev gklb rpmv';      // ← حط App Password
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        // Sender
        $mail->setFrom('personinkonnu@gmail.com', 'AbleLink Support');
        
        // Receiver
        $mail->addAddress($to);

        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $html;

        return $mail->send();

    } catch (Exception $e) {
        error_log("Erreur Email: " . $mail->ErrorInfo);
        return false;
    }
    
}
