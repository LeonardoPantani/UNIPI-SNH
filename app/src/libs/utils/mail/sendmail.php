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
 * @param string $template_name Name of template HTML.
 * @param array  $placeholders Array to replace variables into.
 * 
 * @return bool|string True if the email is sent successfully, or an error message on failure.
 */
function sendEmail(string $to, string $subject, string $template_name = "default", array $placeholders = []): bool|string
{
    $mail = new PHPMailer(true);

    $host = getenv('MAIL_HOST');
    $port = getenv('MAIL_PORT');
    $address = getenv('MAIL_ADDRESS');
    $password = getenv('MAIL_PASSWORD');

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = $host;
        $mail->SMTPAuth = true;
        $mail->Username = $address;
        $mail->Password = $password;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = $port;
        $mail->CharSet = 'UTF-8';

        // Sender settings
        $mail->setFrom($address, "StoryForge");
        $mail->addAddress($to);

        // Content
        $mail->isHTML();
        $mail->Subject = $subject;

        // Loading template
        $template = file_get_contents(__DIR__ . "/templates/".$template_name."_template.html");

        // Replacing variables set inside template
        foreach ($placeholders as $key => $value) {
            $template = str_replace("{{" . $key . "}}", $value, $template);
        }
        $mail->Body = $template;

        $mail->send();
        return true;
    } catch (Exception) {
        return "Mailer Error: $mail->ErrorInfo";
    }
}