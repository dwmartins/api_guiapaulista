<?php

namespace App\Services;

use App\Class\EmailConfig;
use App\Http\Request;
use PHPMailer\PHPMailer\PHPMailer;

class SendEmail {
    private string $to;
    private string $subject;
    private string $message;
    private string $template;

    public function __construct(array $email = null) {
        $this->to       = $email['to'] ?? '';
        $this->subject  = $email['subject'] ?? '';
        $this->message  = $email['message'] ?? '';
        $this->template = $email['template'] ?? '';
    }

    public function getTo(): string {
        return $this->to;
    }

    public function setTo(string $to): void {
        $this->to = $to;
    }

    public function getSubject(): string {
        return $this->subject;
    }

    public function setSubject(string $subject): void {
        $this->subject = $subject;
    }

    public function getMessage(): string {
        return $this->message;
    }

    public function setMessage(string $message): void {
        $this->message = $message;
    }

    public function getTemplate(): string {
        return $this->template;
    }

    public function setTemplate(string $template): void {
        $this->template = $template;
    }

    public function send() {
        $mail = new PHPMailer(true);
    
        $emailConfig = Request::getAttribute('emailConfig');

        if(!$emailConfig->getActivated()) {
            return;
        }
    
        try {
            $mail->setLanguage("br");
            $mail->isSMTP();
            $mail->Host       = $emailConfig->getServer();
            $mail->SMTPAuth   = true;
            $mail->Username   = $emailConfig->getUsername();
            $mail->Password   = $emailConfig->getPassword();
            $mail->SMTPSecure = strtolower($emailConfig->getAuthentication());
            $mail->Port       = $emailConfig->getPort();
            $mail->CharSet = 'UTF-8';
    
            $mail->setFrom($emailConfig->getEmailAddress(), $emailConfig->getEmailAddress());
            $mail->addAddress($this->to);
    
            $mail->isHTML(true);
            $mail->Subject = $this->subject;
            $mail->Body    = $this->message ? $this->message : $this->template;
    
            $mail->send();
    
            return true;
    
        } catch (\Exception $e) {
            logError("Error sending the e-mail. ". $e->getMessage());
            throw $e;
        }
    }
}

