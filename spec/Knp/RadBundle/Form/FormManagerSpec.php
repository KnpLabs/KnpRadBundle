<?php

namespace spec\Knp\RadBundle\Form;

use PhpSpec\ObjectBehavior;
use PhpSpec\Exception\Example\PendingException;

class FormManagerSpec extends ObjectBehavior
{
    /**
     * @param Symfony\Component\HttpFoundation\Request $request
     * @param Knp\RadBundle\Form\FormCreatorInterface $creator1
     * @param Knp\RadBundle\Form\FormCreatorInterface $creator2
     * @param Knp\RadBundle\Form\FormCreatorInterface $creator3
     */
    function let($request, $creator1, $creator2, $creator3)
    {
        $this->beConstructedWith($request);

        $this->registerCreator($creator1, 2);
        $this->registerCreator($creator2, 3);
        $this->registerCreator($creator3, 1);
    }

    function it_should_be_able_to_register_creators()
    {
        $this->getCreators()->shouldHaveCount(3);
    }

    function it_should_be_able_to_register_creators_orderly($creator1, $creator2, $creator3)
    {
        $this->getCreators()->shouldReturn(array($creator2, $creator1, $creator3));
    }

    function it_should_be_able_to_return_creators_many_times($creator1, $creator2, $creator3)
    {
        $this->getCreators();
        $this->getCreators()->shouldReturn(array($creator2, $creator1, $creator3));
    }

    /**
     * @param stdClass $object
     */
    function it_should_try_to_create_form_with_registered_creators($object, $creator1, $creator2)
    {
        $creator2->create($object, 'edit', array())->willReturn(null)->shouldBeCalled();
        $creator1->create($object, 'edit', array())->willReturn(true)->shouldBeCalled();

        $this->createObjectForm($object, 'edit');
    }

    /**
     * @param stdClass $object
     */
    function its_createObjectForm_should_throw_exception_if_no_creator_fits($object, $creator1, $creator2, $creator3)
    {
        $creator1->create($object, 'edit', array())->willReturn(null)->shouldBeCalled();
        $creator2->create($object, 'edit', array())->willReturn(null)->shouldBeCalled();
        $creator3->create($object, 'edit', array())->willReturn(null)->shouldBeCalled();

        $this->shouldThrow()->duringCreateObjectForm($object, 'edit');
    }

    /**
     * @param stdClass $object
     */
    function it_should_return_first_non_null_result_from_creator($object, $creator1, $creator2, $creator3)
    {
        $creator2->create($object, 'edit', array())->willReturn(null)->shouldBeCalled();
        $creator1->create($object, 'edit', array())->willReturn(true)->shouldBeCalled();
        $creator3->create($object, 'edit', array())->shouldNotBeCalled();

        $this->createObjectForm($object, 'edit');
    }

    /**
     * @param stdClass $object
     * @param Symfony\Component\Form\Form $form
     */
    function it_should_be_able_to_bind_unsafe_requests($object, $request, $form, $creator2)
    {
        $request->isMethodSafe()->willReturn(false);
        $request->getMethod()->willReturn('PUT');
        $creator2->create($object, null, array('method' => 'PUT'))->willReturn($form)->shouldBeCalled();
        $form->handleRequest($request)->shouldBeCalled();
        $form->submit($request)->shouldNotBeCalled();

        $this->createBoundObjectForm($object);
    }

    /**
     * @param stdClass $object
     * @param Symfony\Component\Form\Form $form
     */
    function it_should_not_bind_get_safe_requests($object, $request, $form, $creator2)
    {
        $request->isMethodSafe()->willReturn(true);
        $request->getMethod()->willReturn('GET');
        $creator2->create($object, null, array())->willReturn($form)->shouldBeCalled();
        $form->handleRequest($request)->shouldBeCalled();
        $form->submit($request)->shouldNotBeCalled();

        $this->createBoundObjectForm($object);
    }
}
