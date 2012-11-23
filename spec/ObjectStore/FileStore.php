<?php

namespace spec\Knp\RadBundle\ObjectStore;

use PHPSpec2\ObjectBehavior;

class FileStore extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(tmpfile());
    }

    function it_should_be_an_object_store()
    {
        $this->shouldHaveType('Knp\RadBundle\ObjectStore\ObjectStoreInterface');
    }
}
