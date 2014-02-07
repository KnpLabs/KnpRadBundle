<?php

namespace spec\Knp\RadBundle\Resource\Resolver;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ExpressionLanguageBasedSpec extends ObjectBehavior
{
    /**
     * @param Symfony\Component\DependencyInjection\ContainerInterface $container
     * @param Symfony\Component\HttpFoundation\Request $request
     * @param Symfony\Component\HttpFoundation\ParameterBag $attributes
     * @param spec\Knp\RadBundle\Resource\Resolver\CakeRepository $cakeRepository
     * @param stdClass $cake
     */
    function let($container, $request, $cakeRepository, $attributes, $cake)
    {
        $this->beConstructedWith($container);
        $container->get('orm.cake_repository')->willReturn($cakeRepository);
        $request->attributes = $attributes;
    }

    function its_resolveResource_uses_ExpressionLanguage($request, $attributes, $cake, $cakeRepository)
    {
        $attributes->get('test')->willReturn('value!');
        $cakeRepository->findBySlug('value!')->shouldBeCalled()->willReturn($cake);
        $this->resolveResource($request, array('expr' => "service('orm.cake_repository').findBySlug(request.attributes.get('test'))"))->shouldReturn($cake);
    }
}

class CakeRepository
{
    public function findBySlug($slug)
    {
    }

    public function findByRegionAndSlug($region, $slug)
    {
    }
}
