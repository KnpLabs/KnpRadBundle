<?php

namespace spec\Knp\RadBundle\Routing\Conventional;

use PhpSpec\ObjectBehavior;
use Symfony\Component\Config\FileLocatorInterface;
use Knp\RadBundle\Routing\Conventional\Config\Factory as Configs;
use Knp\RadBundle\Routing\Conventional\Config\Parser;

class LoaderSpec extends ObjectBehavior
{
    function let(FileLocatorInterface $locator, Parser $yaml)
    {
        $locator->locate('routing.yml')->willReturn('yaml/file/path');
    }

    function it_supports_rad_resource_type($locator, $yaml)
    {
        $this->beConstructedWith($locator, new Configs($yaml->getWrappedObject()));
        $this->supports('yaml/file/path', 'rad')->shouldReturn(true);
    }

    function it_generates_7_conventional_routes_by_default($locator, $yaml)
    {
        $yaml->parse('yaml/file/path')->willReturn(array(
            'App:Cheese' => null
        ));
        $this->beConstructedWith($locator, new Configs($yaml->getWrappedObject()));
        $collection = $this->load('routing.yml');

        $collection->shouldHaveCount(7);
    }

    function it_generates_conventional_patterns($locator, $yaml)
    {
        $yaml->parse('yaml/file/path')->willReturn(array(
            'App:Cheese' => null
        ));
        $this->beConstructedWith($locator, new Configs($yaml->getWrappedObject()));
        $collection = $this->load('routing.yml');

        $collection->get('app_cheese_index')->getPattern()->shouldReturn('/cheese.{_format}');
        $collection->get('app_cheese_new')->getPattern()->shouldReturn('/cheese/new.{_format}');
        $collection->get('app_cheese_create')->getPattern()->shouldReturn('/cheese/new.{_format}');
        $collection->get('app_cheese_edit')->getPattern()->shouldReturn('/cheese/{id}/edit.{_format}');
        $collection->get('app_cheese_update')->getPattern()->shouldReturn('/cheese/{id}/edit.{_format}');
        $collection->get('app_cheese_show')->getPattern()->shouldReturn('/cheese/{id}.{_format}');
        $collection->get('app_cheese_delete')->getPattern()->shouldReturn('/cheese/{id}.{_format}');
    }

    function it_generates_conventional_controller_names($locator, $yaml)
    {
        $yaml->parse('yaml/file/path')->willReturn(array(
            'App:Cheese' => null
        ));
        $this->beConstructedWith($locator, new Configs($yaml->getWrappedObject()));
        $collection = $this->load('routing.yml');

        $collection->get('app_cheese_index')->getDefaults()->shouldReturn(array(
            '_controller' => 'App:Cheese:index',
            '_view' => 'App:Cheese:index',
        ));
        $collection->get('app_cheese_new')->getDefaults()->shouldReturn(array(
            '_controller' => 'App:Cheese:new',
            '_view' => 'App:Cheese:new',
        ));
        $collection->get('app_cheese_create')->getDefaults()->shouldReturn(array(
            '_controller' => 'App:Cheese:new',
            '_view' => 'App:Cheese:create',
        ));
        $collection->get('app_cheese_edit')->getDefaults()->shouldReturn(array(
            '_controller' => 'App:Cheese:edit',
            '_view' => 'App:Cheese:edit',
        ));
        $collection->get('app_cheese_update')->getDefaults()->shouldReturn(array(
            '_controller' => 'App:Cheese:edit',
            '_view' => 'App:Cheese:update',
        ));
        $collection->get('app_cheese_show')->getDefaults()->shouldReturn(array(
            '_controller' => 'App:Cheese:show',
            '_view' => 'App:Cheese:show',
        ));
        $collection->get('app_cheese_delete')->getDefaults()->shouldReturn(array(
            '_controller' => 'App:Cheese:delete',
            '_view' => 'App:Cheese:delete',
        ));
    }

    function it_cascades_default_config($locator, $yaml)
    {
        $yaml->parse('yaml/file/path')->willReturn(array(
            'App:Cheese' => array(
                'defaults' => array(
                    '_resources' => array(
                        'object' => array('expr' => "request.get('id')"),
                    ),
                ),
                'requirements' => array('_format' => 'html'),
            ),
        ));
        $this->beConstructedWith($locator, new Configs($yaml->getWrappedObject()));
        $collection = $this->load('routing.yml');

        $collection->get('app_cheese_index')->getDefaults()->shouldReturn(array(
            '_controller' => 'App:Cheese:index',
            '_view' => 'App:Cheese:index',
            '_resources' => array(
                'object' => array('expr' => "request.get('id')"),
            ),
        ));
    }

    function it_overrides_default_config_explicitly($locator, $yaml)
    {
        $yaml->parse('yaml/file/path')->willReturn(array(
            'App:Cheese' => array(
                'defaults' => array(
                    '_resources' => array(
                        'object' => array('expr' => "request.get('id')"),
                    ),
                ),
                'index' => array(
                    'defaults' => array('_resources' => array()),
                ),
                'requirements' => array('_format' => 'html'),
            ),
        ));
        $this->beConstructedWith($locator, new Configs($yaml->getWrappedObject()));
        $collection = $this->load('routing.yml');

        $collection->get('app_cheese_index')->getDefaults()->shouldReturn(array(
            '_controller' => 'App:Cheese:index',
            '_view' => 'App:Cheese:index',
            '_resources' => array(),
        ));
    }

    function its_controller_can_be_a_service($locator, $yaml)
    {
        $yaml->parse('yaml/file/path')->willReturn(array(
            'App:Cheese' => array(
                'controller' => 'knp_rad.controller.crud_controller',
            ),
        ));
        $this->beConstructedWith($locator, new Configs($yaml->getWrappedObject()));
        $collection = $this->load('routing.yml');

        $collection->get('app_cheese_index')->getDefaults()->shouldReturn(array(
            '_controller' => 'knp_rad.controller.crud_controller:indexAction',
            '_view' => 'App:Cheese:index',
        ));
    }

    function it_allows_to_add_new_routes($locator, $yaml)
    {
        $yaml->parse('yaml/file/path')->willReturn(array(
            'App:Cheese' => array(
                'custom' => null,
            ),
        ));
        $this->beConstructedWith($locator, new Configs($yaml->getWrappedObject()));
        $collection = $this->load('routing.yml');

        $collection->get('app_cheese_custom')->getDefaults()->shouldReturn(array(
            '_controller' => 'App:Cheese:custom',
            '_view' => 'App:Cheese:custom',
        ));
        $collection->get('app_cheese_custom')->getPattern()->shouldReturn('/cheese/custom');
    }

    function it_allows_to_limit_number_of_generated_routes($locator, $yaml)
    {
        $yaml->parse('yaml/file/path')->willReturn(array(
            'App:Cheese' => array(
                'custom' => null,
                'elements' => array('index', 'show'),
            ),
        ));
        $this->beConstructedWith($locator, new Configs($yaml->getWrappedObject()));
        $collection = $this->load('routing.yml');

        $collection->shouldHaveCount(3);
    }

    function it_allows_to_disable_all_routes($locator, $yaml)
    {
        $yaml->parse('yaml/file/path')->willReturn(array(
            'App:Cheese' => array(
                'elements' => array(),
            ),
        ));
        $this->beConstructedWith($locator, new Configs($yaml->getWrappedObject()));
        $collection = $this->load('routing.yml');

        $collection->shouldHaveCount(0);
    }

    function it_allows_new_routes_to_be_configured($locator, $yaml)
    {
        $yaml->parse('yaml/file/path')->willReturn(array(
            'App:Cheese' => array(
                'custom' => array('controller' => 'a different one'),
            ),
        ));
        $this->beConstructedWith($locator, new Configs($yaml->getWrappedObject()));
        $collection = $this->load('routing.yml');

        $collection->get('app_cheese_custom')->getDefaults()->shouldReturn(array(
            '_controller' => 'a different one:customAction',
            '_view' => 'App:Cheese:custom',
        ));
        $collection->get('app_cheese_custom')->getPattern()->shouldReturn('/cheese/custom');
    }

    function it_uses_pattern_as_default_string_value($locator, $yaml)
    {
        $yaml->parse('yaml/file/path')->willReturn(array(
            'App:Cheese' => array(
                'custom' => '/patt{ern}/de/ouf'
            ),
        ));
        $this->beConstructedWith($locator, new Configs($yaml->getWrappedObject()));
        $collection = $this->load('routing.yml');

        $collection->get('app_cheese_custom')->getDefaults()->shouldReturn(array(
            '_controller' => 'App:Cheese:custom',
            '_view' => 'App:Cheese:custom',
        ));
        $collection->get('app_cheese_custom')->getPattern()->shouldReturn('/cheese/patt{ern}/de/ouf');
    }

    public function it_can_be_prefixed($locator, $yaml)
    {
        $yaml->parse('yaml/file/path')->willReturn(array(
            'App:Cheese' => array(
                'custom' => array(
                    'pattern' => '/patt{ern}/de/ouf',
                    'prefix' => 'test:sub',
                ),
            ),
        ));
        $this->beConstructedWith($locator, new Configs($yaml->getWrappedObject()));
        $collection = $this->load('routing.yml');

        $collection->get('app_cheese_custom')->getPattern()->shouldReturn('/sub/patt{ern}/de/ouf');
        $collection->get('app_cheese_custom')->getDefaults()->shouldReturn(array(
            '_controller' => 'App:Cheese:custom',
            '_view' => 'App:Cheese:custom',
        ));
    }

    public function it_can_be_nested($locator, $yaml)
    {
        $yaml->parse('yaml/file/path')->willReturn(array(
            'App:Food' => array(
                'elements' => array('show'),
            ),
            'App:Cheese' => array(
                'parent' => 'App:Food',
                'elements' => array('show', 'index'),
            ),
            'App:Cantal' => array(
                'parent' => 'App:Cheese',
                'elements' => array('show', 'index'),
            ),
        ));
        $this->beConstructedWith($locator, new Configs($yaml->getWrappedObject()));
        $collection = $this->load('routing.yml');

        $collection->get('app_food_cheese_show')->getPattern()->shouldReturn('/food/{foodId}/cheese/{id}.{_format}');
        $collection->get('app_food_cheese_index')->getPattern()->shouldReturn('/food/{foodId}/cheese.{_format}');

        $collection->get('app_food_cheese_cantal_show')->getPattern()->shouldReturn('/food/{foodId}/cheese/{cheeseId}/cantal/{id}.{_format}');
    }

    public function its_controllers_can_be_nested($locator, $yaml)
    {
        $yaml->parse('yaml/file/path')->willReturn(array(
            'App:Food' => array(
                'elements' => array('show'),
            ),
            'App:Cheese' => array(
                'parent' => 'App:Food',
                'elements' => array('show', 'index'),
            ),
            'App:Cantal' => array(
                'parent' => 'App:Cheese',
                'elements' => array('show', 'index'),
            ),
        ));
        $this->beConstructedWith($locator, new Configs($yaml->getWrappedObject()));
        $collection = $this->load('routing.yml');

        $collection->get('app_food_cheese_cantal_show')->getDefaults()
            ->shouldContain('App:Food/Cheese/Cantal:show')
        ;
    }

    public function its_custom_controllers_should_not_be_nested($locator, $yaml)
    {
        $yaml->parse('yaml/file/path')->willReturn(array(
            'App:Food' => array(
                'controller' => 'app.controller.food',
                'elements' => array('show'),
            ),
            'App:Cheese' => array(
                'controller' => 'app.controller.food.cheese',
                'parent' => 'App:Food',
                'elements' => array('show', 'index'),
            ),
            'App:Cantal' => array(
                'controller' => 'app.controller.food.cheese.cantal',
                'parent' => 'App:Cheese',
                'elements' => array('show', 'index'),
            ),
        ));
        $this->beConstructedWith($locator, new Configs($yaml->getWrappedObject()));
        $collection = $this->load('routing.yml');

        $collection->get('app_food_cheese_cantal_show')->getDefaults()
            ->shouldContain('app.controller.food.cheese.cantal:showAction')
        ;
    }
}
