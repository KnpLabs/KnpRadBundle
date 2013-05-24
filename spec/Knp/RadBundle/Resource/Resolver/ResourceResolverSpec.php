<?php

namespace spec\Knp\RadBundle\Resource\Resolver;

use PhpSpec\ObjectBehavior;

class ResourceResolverSpec extends ObjectBehavior
{
    /**
     * @param Symfony\Component\DependencyInjection\ContainerInterface $container
     * @param Knp\RadBundle\Resource\Resolver\ArgumentResolver $argumentResolver
     * @param Symfony\Component\HttpFoundation\Request $request
     * @param stdClass $cheeseRepository
     * @param stdClass $cheese
     */
    function let($container, $argumentResolver, $request, $cheese, $cheeseRepository)
    {
        $this->beConstructedWith($container, $argumentResolver);
        $container->get('orm.cheese_repository')->willReturn($cheeseRepository);
    }

    /**
     * @param stdClass $cheese
     * @param stdClass $repository
     */
    function it_should_resolve_resource($request, $argumentResolver, $container, $cheese, $cheeseRepository)
    {
        $argumentResolver->resolveArgument($request, array('value' => 'normandie'))->willReturn('normandie');
        $argumentResolver->resolveArgument($request, array('name' => 'slug'))->willReturn('neufchatel');

        $cheeseRepository->findByRegionAndSlug('normandie', 'neufchatel')->shouldBeCalled()->willReturn($cheese);

        $this
            ->resolveResource($request, array(
                'service'   => 'orm.cheese_repository',
                'method'    => 'findByRegionAndSlug',
                'arguments' => array(
                    array('value' => 'normandie'),
                    array('name' => 'slug'),
                )
            ))
            ->shouldReturn($cheese)
        ;
    }

    function it_should_throw_resolution_failure_when_resolved_resource_is_null($request, $argumentResolver, $cheeseRepository)
    {
        $argumentResolver->resolveArgument($request, array('name' => 'slug'))->willReturn('neufchatel');
        $cheeseRepository->findBySlug('neufchatel')->willReturn(null);

        $this->shouldThrow()->duringResolveResource($request, array(
            'service'   => 'orm.cheese_repository',
            'method'    => 'findBySlug',
            'arguments' => array(
                array('name' => 'slug')
            )
        ));
    }

    function it_should_throw_exception_when_resouce_has_extra_options($request)
    {
        $this->shouldThrow()->duringResolveResource($request, array(
                'extra'     => 'the extra value',
                'service'   => 'orm.cheese_repository',
                'method'    => 'findByRegionAndSlug',
                'arguments' => array(
                    array('value' => 'normandie'),
                    array('name' => 'slug'),
                ),
        ));
    }

    function it_should_throw_exception_when_resource_is_missing_service_option($request)
    {
        $this->shouldThrow()->duringResolveResource($request, array(
                'method'    => 'findByRegionAndSlug',
                'arguments' => array(
                    array('value' => 'normandie'),
                    array('name' => 'slug'),
                ),
        ));
    }

    function it_should_throw_exception_when_resource_is_missing_method_option($request)
    {
        $this->shouldThrow()->duringResolveResource($request, array(
            'service' => 'orm.cheese_repository',
            'arguments' => array(
                    array('name' => 'slug'),
            ),
        ));
    }
}
