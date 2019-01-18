<?php

namespace EasyHQ;

class Mail {

    private $transport;

    public function __construct() {
        $smtp = Config::getField('SMTP');
        $this->transport = \Swift_SmtpTransport::newInstance($smtp['host'], $smtp['port']);
    }

    public function send($from, $to, $title, $mail) {
        $msg = \Swift_Message::newInstance($title);
        $msg->setFrom($from);
        $msg->setBody($mail[0], 'text/html');

        $msg->setTo($to);
        $msg->addPart($mail[1], 'text/plain');

        $swift = \Swift_Mailer::newInstance($this->transport);
        $swift->send($msg);
    }

}
