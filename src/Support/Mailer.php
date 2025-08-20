<?php
namespace App\Support;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

class Mailer
{
    public static function send(string $to, string $subject, string $htmlBody): bool
    {
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = Settings::get('mail_host', $_ENV['MAIL_HOST'] ?? 'localhost');
            $mail->SMTPAuth = true;
            $mail->Username = Settings::get('mail_username', $_ENV['MAIL_USERNAME'] ?? '');
            $mail->Password = Settings::get('mail_password', $_ENV['MAIL_PASSWORD'] ?? '');
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = (int)(Settings::get('mail_port', (string)($_ENV['MAIL_PORT'] ?? 587)));

            $fromAddress = Settings::get('mail_from_address', $_ENV['MAIL_FROM_ADDRESS'] ?? 'no-reply@example.com');
            $fromName = Settings::get('mail_from_name', $_ENV['MAIL_FROM_NAME'] ?? 'GameTopUp');

            $mail->setFrom($fromAddress, $fromName);
            $mail->addAddress($to);
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $htmlBody;
            $mail->AltBody = strip_tags($htmlBody);

            $mail->send();
            return true;
        } catch (\Throwable $e) {
            return false;
        }
    }
}
