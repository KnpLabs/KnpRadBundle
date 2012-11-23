<?php

namespace Knp\RadBundle\Mailer;

class MessageRecorder implements \Swift_Events_SendListener
{
    private $logger;

    public function __construct(MessagesLogger $logger)
    {
        $this->logger = $logger;
    }

    public function beforeSendPerformed(\Swift_Events_SendEvent $evt)
    {
    }

    public function sendPerformed(\Swift_Events_SendEvent $evt)
    {
        $this->logger->storeMessage($evt->getMessage());
    }
}
