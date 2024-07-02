<?php

namespace App\Controllers;

use App\Services\SendEmail;
use Exception;

class SendEmailController {
    private string $to;
    private string $subject;

    private $mailSend;

    public function __construct(array $emailInfo = null) {
        $this->to       = $emailInfo['to'] ?? '';
        $this->subject  = $emailInfo['subject'] ?? '';

        $this->mailSend = new SendEmail();
    }

    public function recoverPassword(string $username, string $link) {
        try {
            $siteName = "dwmcode"; // teste, sera desenvolvido os métodos de informações do site.

            $template = file_get_contents(__DIR__."/../EmailTemplates/recoverPassword.html");
            $template = str_replace('{SITENAME}', $siteName, $template);
            $template = str_replace('{USERNAME}', $username, $template);
            $template = str_replace('{LINK}', $link, $template);

            $this->mailSend->setTo($this->to);
            $this->mailSend->setSubject($this->subject);
            $this->mailSend->setTemplate($template);
            $this->mailSend->send();

        } catch (Exception $e) {
            throw $e;
        }
    }
}