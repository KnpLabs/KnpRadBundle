<?php

namespace spec\Knp\RadBundle\View;

use PhpSpec\ObjectBehavior;

class NameDeducerSpec extends ObjectBehavior
{
    /**
     * @param Symfony\Component\DependencyInjection\ContainerInterface               $container
     * @param Symfony\Component\HttpFoundation\Request                               $request
     * @param Symfony\Bundle\FrameworkBundle\Templating\EngineInterface              $engine
     * @param Symfony\Bundle\FrameworkBundle\Controller\ControllerNameParser         $cnp
     * @param Knp\RadBundle\HttpFoundation\RequestManipulator                        $reqManip
     * @param Knp\RadBundle\AppBundle\BundleGuesser                                  $bundleGuesser
     * @param Symfony\Component\HttpKernel\Bundle\BundleInterface                    $bundle
     */
    function let($container, $request, $engine, $cnp, $reqManip, $bundleGuesser, $bundle)
    {
        $bundleGuesser->hasBundleForClass(\Prophecy\Argument::any())->willReturn(true);
        $bundleGuesser->getBundleForClass(\Prophecy\Argument::any())->willReturn($bundle);
        $bundle->getName()->willReturn('App');

        $this->beConstructedWith($container, $engine, $cnp, $bundleGuesser, $reqManip,  'twig');
    }

    function it_should_deduce_standard_controller_names($request, $reqManip, $engine)
    {
        $reqManip->hasAttribute($request, 'view')->willReturn(false);
        $reqManip->hasAttribute($request, '_controller')->willReturn(true);
        $reqManip->getAttribute($request, '_controller')->willReturn('App\Controller\CheeseController::eatAction');
        $request->getRequestFormat()->willReturn('html');

        $this->deduce($request)->shouldReturn('App:Cheese:eat.html.twig');
    }

    function it_should_deduce_service_controller_names($container, $request, $reqManip, $engine)
    {
        $reqManip->hasAttribute($request, 'view')->willReturn(false);
        $reqManip->hasAttribute($request, '_controller')->willReturn(true);
        $reqManip->getAttribute($request, '_controller')->willReturn('app.controller.assistant:eatAction');
        $container->get('app.controller.assistant')->willReturn(new \Knp\RadBundle\Controller\AssistantController);
        $request->getRequestFormat()->willReturn('html');

        $this->deduce($request)->shouldReturn('App:Assistant:eat.html.twig');
    }

    function it_should_use_view_attribute_if_given($request, $reqManip, $engine)
    {
        $reqManip->hasAttribute($request, 'view')->willReturn(true);
        $reqManip->getAttribute($request, 'view')->willReturn('App:Cheese:eat');
        $request->getRequestFormat()->willReturn('html');

        $this->deduce($request)->shouldReturn('App:Cheese:eat.html.twig');
    }
}
