<?php

namespace Knp\RadBundle\Mailer;

use Knp\RadBundle\ObjectStore\ObjectStoreInterface;

class MessagesLogger
{
    private $objectStore;

    public function __construct(ObjectStoreInterface $objectStore)
    {
        $this->objectStore = $objectStore;
    }

    public function getMessagesSentTo($email)
    {
        $messages = [];

        foreach ($this->objectStore->findAll() as $message) {
            $emails = array_keys($message->getTo());
            if (in_array($email, $emails)) {
                $messages[] = $message;
            }
        }

        return $messages;
    }

    public function storeMessage(\Swift_Mime_Message $message)
    {
        $this->objectStore->store($message);
    }
}
