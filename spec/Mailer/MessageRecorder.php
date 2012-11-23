<?php

namespace spec\Knp\RadBundle\Mailer;

use PHPSpec2\ObjectBehavior;

class MessageRecorder extends ObjectBehavior
{
    /**
     * @param Knp\RadBundle\Mailer\MessagesLogger $store
     * @param Swift_Events_SendEvent              $event
     * @param Swift_Mime_Message                  $message
     */
    function let($store, $event, $message)
    {
        $this->beConstructedWith($store);

        $event->getMessage()->willReturn($message);
    }

    function it_should_be_a_swift_listener()
    {
        $this->shouldBeAnInstanceOf('Swift_Events_SendListener');
    }

    function it_should_save_sent_mails($store, $event, $message)
    {
        $store->storeMessage($message)->shouldBeCalled();

        $this->sendPerformed($event);
    }
}
