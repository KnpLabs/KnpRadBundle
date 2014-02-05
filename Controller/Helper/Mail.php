<?php

namespace Knp\RadBundle\Controller\Helper;

use Knp\RadBundle\Mailer\MessageFactory;
use Swift_Mailer;
use Swift_Mime_Message;

class Mail
{
    private $factory;
    private $mailer;

    public function __construct(MessageFactory $factory, Swift_Mailer $mailer)
    {
        $this->factory = $factory;
        $this->mailer = $mailer;
    }

    public function createMessage($name, array $parameters = array(), $from = null, $to = null)
    {
        $message = $this->factory->createMessage(get_class($this), $name, $parameters);

        if ($from) {
            $message->setFrom($from);
        }
        if ($to) {
            $message->setTo($to);
        }

        return $message;
    }

    public function send(Swift_Mime_Message $message)
    {
        $this->mailer->send($message);
    }

    public function getMailer()
    {
        return $this->mailer;
    }
}
