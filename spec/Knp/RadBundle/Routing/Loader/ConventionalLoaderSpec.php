<?php

namespace spec\Knp\RadBundle\Routing\Loader;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument as Arg;

use InvalidArgumentException;
use Knp\RadBundle\Routing\Loader\ConventionalLoader\CollectionGenerator\Symfony22;

class ConventionalLoaderSpec extends ObjectBehavior
{
    /**
     * @param Symfony\Component\Config\FileLocatorInterface $locator
     * @param Knp\RadBundle\Routing\Loader\ConventionalLoader\CollectionGeneratorInterface $collectionGenerator
     $ @param Symfony\Component\Routing\Loader\YamlFileLoader $classicalLoader
     * @param Knp\RadBundle\Routing\Loader\YamlParser $yaml
     */
    function let($locator, $classicalLoader, $collectionGenerator, $yaml)
    {
        $this->beConstructedWith($locator, $collectionGenerator, $classicalLoader, $yaml);

        $locator->locate('routing.yml')->willReturn('yaml file');
    }

    function it_should_support_conventional_resources()
    {
        $this->supports('', 'rad_convention')->shouldReturn(true);
    }

    function it_should_not_support_other_resources()
    {
        $this->supports('')->shouldNotReturn(true);
    }

    function it_should_not_fail_when_loading_empty_resource($yaml)
    {
        $yaml->parse('yaml file')->willReturn(null);

        $routes = $this->load('routing.yml');
        $routes->shouldHaveCount(0);
    }

    /**
     * @param Symfony\Component\Routing\RouteCollection $collection
     **/
    function it_should_use_classic_loader_scheme_for_basic_routes($yaml, $classicalLoader, $collection)
    {
        $yaml->parse('yaml file')->willReturn(array(
            'blog_show' => array(
                'pattern'  => '/blog/{slug}',
                'defaults' => array('_controller' => 'AcmeBlogBundle:Blog:show'),
            )
        ));
        $yaml->dump(Arg::any('array'), Arg::type('string'))->shouldBeCalled();

        $collection->getResources()->willReturn(array());
        $collection->all()->willReturn(array());
        $classicalLoader->load(Arg::type('string'), null)->willReturn($collection);
        $routes = $this->load('routing.yml');
    }
}
