<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/../../vendor/autoload.php';

/**
 * Sends an email.
 * 
 * The following environment variables must be set:
 * 
 * - MAIL_HOST: The SMTP server address.
 * - MAIL_PORT: The SMTP server port.
 * - MAIL_ADDRESS: The sender's email address.
 * - MAIL_PASSWORD: The password for the sender's email.
 * 
 * @param string $to Recipient email address.
 * @param string $subject Subject of the email.
 * @param string $body HTML content of the email.
 * 
 * @return bool|string True if the email is sent successfully, or an error message on failure.
 */
function sendEmail($to, $subject, $body)
{
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = getenv('MAIL_HOST');
        $mail->SMTPAuth = true;
        $mail->Username = getenv('MAIL_ADDRESS');
        $mail->Password = getenv('MAIL_PASSWORD');
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = getenv('MAIL_PORT');

        // Sender settings
        $mail->setFrom(getenv('MAIL_ADDRESS'), "StoryForge");
        $mail->addAddress($to);

        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $body;

        $mail->send();
        return true;
    } catch (Exception $e) {
        return "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}
