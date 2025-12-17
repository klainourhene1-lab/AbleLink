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
        // SMTP Gmail Configuration
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        
        // Gmail credentials - Update these with your Gmail account
        // IMPORTANT: Use an App Password, NOT your regular Gmail password
        // Get App Password: https://myaccount.google.com/apppasswords
        $mail->Username = 'personinkonnu@gmail.com';  // Your Gmail address
        $mail->Password = 'mnxt kmnm udrs caaw';      // Your Gmail App Password (16 chars, no spaces)
        
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );

        // Sender
        $mail->setFrom('personinkonnu@gmail.com', 'AbleLink Support');
        
        // Receiver
        $mail->addAddress($to);

        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $html;
        $mail->CharSet = 'UTF-8';

        return $mail->send();

    } catch (Exception $e) {
        error_log("Email Error: " . $mail->ErrorInfo);
        error_log("Debug: Attempting to send to $to");
        
        // On localhost, use debug mode
        if (strpos($_SERVER['HTTP_HOST'], 'localhost') !== false) {
            error_log("Localhost detected - Email simulation mode active");
            return true; // Simulate success on localhost
        }
        return false;
    }
    
}

