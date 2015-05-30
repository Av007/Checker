<?php

namespace Checker\Modules;

class Email
{
    /** @var array $output output format */
    protected $output = array();
    /** @var array $config */
    protected $config;

    public function __construct()
    {
        $classname = __CLASS__;

        if (preg_match('@\\\\([\w]+)$@', $classname, $matches)) {
            $classname = $matches[1];
        }

        $this->output['title'] = $classname;
        $this->output['operation'] = 'check connection';
    }

    public function init()
    {
        $this->config = \Checker\Helper::getConfig();
        return $this->output;
    }

    public function run()
    {
        $result = array();

        $result[] = (bool)fsockopen($this->config['email.host'], $this->config['email.port']);

        $transport = \Swift_SmtpTransport::newInstance($this->config['email.host'], $this->config['email.port'])
            ->setUsername($this->config['email.username'])
            ->setPassword($this->config['email.password']);
        try {
            $transport->start();
            $result[] = true;
        } catch (\Exception $e) {
            $result[] = false;
        }

        $message = \Swift_Message::newInstance()
            ->setSubject('Test email')
            ->setFrom('test@test.com')
            ->setContentType("text/html")
            ->setTo($this->config['email.to'])
            ->setBody('Test!<br>test','text/html');

        $mailer = \Swift_Mailer::newInstance($transport);
        $result[] = (bool)$mailer->send($message);

        $this->output['result'] = in_array(false, $result, true) ? 'False' : 'Ok';

        return $this->output;
    }
}
