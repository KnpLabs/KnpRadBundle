<?php

namespace spec\Knp\RadBundle\Mailer;

use PHPSpec2\ObjectBehavior;

class MessagesLogger extends ObjectBehavior
{
    /**
     * @param Knp\RadBundle\ObjectStore\ObjectStoreInterface $objStore
     * @param Swift_Mime_Message                             $a
     * @param Swift_Mime_Message                             $b
     * @param Swift_Mime_Message                             $c
     */
    function let($objStore, $a, $b, $c)
    {
        $this->beConstructedWith($objStore);

        $objStore->findAll()->willReturn(array($a, $b, $c));
    }

    function it_should_store_a_given_message($objStore, $a)
    {
        $objStore->store($a)->shouldBeCalled();

        $this->storeMessage($a);
    }

    function it_should_get_messages_sent_to_a_specific_email($objStore, $a, $b, $c)
    {
        $a->getTo()->willReturn(array('john@gmail.com' => 'John'));
        $b->getTo()->willReturn(array('sarah@gmail.com' => 'Sarah'));
        $c->getTo()->willReturn(array('george@gmail.com' => 'George', 'john@gmail.com' => ''));

        $this->getMessagesSentTo('john@gmail.com')->shouldReturn(array($a, $c));
    }
}
