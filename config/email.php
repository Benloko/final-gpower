<?php
// Email configuration
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', 'votre-email@gmail.com'); // Change this
define('SMTP_PASSWORD', 'votre-mot-de-passe-app'); // Change this
define('SMTP_FROM_EMAIL', 'votre-email@gmail.com'); // Change this
define('SMTP_FROM_NAME', 'Gpower');

/**
 * Function to send an email using PHPMailer
 */
function sendEmail($to, $toName, $subject, $body, $isHTML = false) {
    require_once __DIR__ . '/../vendor/autoload.php';
    
    $mail = new PHPMailer\PHPMailer\PHPMailer(true);
    
    try {
        // SMTP configuration
        $mail->isSMTP();
        $mail->Host = SMTP_HOST;
        $mail->SMTPAuth = true;
        $mail->Username = SMTP_USERNAME;
        $mail->Password = SMTP_PASSWORD;
        $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = SMTP_PORT;
        $mail->CharSet = 'UTF-8';
        
        // Sender and recipient
        $mail->setFrom(SMTP_FROM_EMAIL, SMTP_FROM_NAME);
        $mail->addAddress($to, $toName);
        $mail->addReplyTo(SMTP_FROM_EMAIL, SMTP_FROM_NAME);
        
        // Content
        $mail->isHTML($isHTML);
        $mail->Subject = $subject;
        $mail->Body = $body;
        
        if (!$isHTML) {
            $mail->AltBody = $body;
        }
        
        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Email error: {$mail->ErrorInfo}");
        return false;
    }
}
