<?php

namespace spec\Knp\RadBundle\Routing\Loader;

use PhpSpec\ObjectBehavior;
use PhpSpec\Exception\Example\PendingException;

use InvalidArgumentException;

class ConventionalLoaderSpec extends ObjectBehavior
{
    /**
     * @param Symfony\Component\Config\FileLocatorInterface $locator
     * @param Knp\RadBundle\Routing\Loader\YamlParser       $yaml
     */
    function let($locator, $yaml)
    {
        $this->beConstructedWith($locator, $yaml);

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

    function it_should_load_simple_collection_by_conventions($yaml)
    {
        $yaml->parse('yaml file')->willReturn(array(
            'App:Cheeses' => null
        ));

        $routes = $this->load('routing.yml');

        $index = $routes->get('app_cheeses_index');
        $index->getPattern()->shouldReturn('/cheeses/');
        $index->getDefaults()->shouldReturn(array('_controller' => 'App:Cheeses:index'));
        $index->getRequirements()->shouldReturn(array('_method' => 'GET'));

        $new = $routes->get('app_cheeses_new');
        $new->getPattern()->shouldReturn('/cheeses/new');
        $new->getDefaults()->shouldReturn(array('_controller' => 'App:Cheeses:new'));
        $new->getRequirements()->shouldReturn(array('_method' => 'GET'));

        $new = $routes->get('app_cheeses_create');
        $new->getPattern()->shouldReturn('/cheeses/');
        $new->getDefaults()->shouldReturn(array('_controller' => 'App:Cheeses:new'));
        $new->getRequirements()->shouldReturn(array('_method' => 'POST'));

        $show = $routes->get('app_cheeses_show');
        $show->getPattern()->shouldReturn('/cheeses/{id}');
        $show->getDefaults()->shouldReturn(array('_controller' => 'App:Cheeses:show'));
        $show->getRequirements()->shouldReturn(array('_method' => 'GET'));

        $edit = $routes->get('app_cheeses_edit');
        $edit->getPattern()->shouldReturn('/cheeses/{id}/edit');
        $edit->getDefaults()->shouldReturn(array('_controller' => 'App:Cheeses:edit'));
        $edit->getRequirements()->shouldReturn(array('_method' => 'GET'));

        $edit = $routes->get('app_cheeses_update');
        $edit->getPattern()->shouldReturn('/cheeses/{id}');
        $edit->getDefaults()->shouldReturn(array('_controller' => 'App:Cheeses:edit'));
        $edit->getRequirements()->shouldReturn(array('_method' => 'PUT'));

        $delete = $routes->get('app_cheeses_delete');
        $delete->getPattern()->shouldReturn('/cheeses/{id}');
        $delete->getDefaults()->shouldReturn(array('_controller' => 'App:Cheeses:delete'));
        $delete->getRequirements()->shouldReturn(array('_method' => 'DELETE'));

        $routes->shouldHaveCount(7);
    }

    function it_should_load_collections_with_custom_prefix($yaml)
    {
        $yaml->parse('yaml file')->willReturn(array(
            'App:Cheeses' => '/custom/prefix'
        ));

        $routes = $this->load('routing.yml');

        $index = $routes->get('app_cheeses_index');
        $index->getPattern()->shouldReturn('/custom/prefix/');
        $index->getDefaults()->shouldReturn(array('_controller' => 'App:Cheeses:index'));
        $index->getRequirements()->shouldReturn(array('_method' => 'GET'));

        $new = $routes->get('app_cheeses_new');
        $new->getPattern()->shouldReturn('/custom/prefix/new');
        $new->getDefaults()->shouldReturn(array('_controller' => 'App:Cheeses:new'));
        $new->getRequirements()->shouldReturn(array('_method' => 'GET'));

        $new = $routes->get('app_cheeses_create');
        $new->getPattern()->shouldReturn('/custom/prefix/');
        $new->getDefaults()->shouldReturn(array('_controller' => 'App:Cheeses:new'));
        $new->getRequirements()->shouldReturn(array('_method' => 'POST'));

        $show = $routes->get('app_cheeses_show');
        $show->getPattern()->shouldReturn('/custom/prefix/{id}');
        $show->getDefaults()->shouldReturn(array('_controller' => 'App:Cheeses:show'));
        $show->getRequirements()->shouldReturn(array('_method' => 'GET'));

        $edit = $routes->get('app_cheeses_edit');
        $edit->getPattern()->shouldReturn('/custom/prefix/{id}/edit');
        $edit->getDefaults()->shouldReturn(array('_controller' => 'App:Cheeses:edit'));
        $edit->getRequirements()->shouldReturn(array('_method' => 'GET'));

        $edit = $routes->get('app_cheeses_update');
        $edit->getPattern()->shouldReturn('/custom/prefix/{id}');
        $edit->getDefaults()->shouldReturn(array('_controller' => 'App:Cheeses:edit'));
        $edit->getRequirements()->shouldReturn(array('_method' => 'PUT'));

        $delete = $routes->get('app_cheeses_delete');
        $delete->getPattern()->shouldReturn('/custom/prefix/{id}');
        $delete->getDefaults()->shouldReturn(array('_controller' => 'App:Cheeses:delete'));
        $delete->getRequirements()->shouldReturn(array('_method' => 'DELETE'));

        $routes->shouldHaveCount(7);
    }

    function it_should_load_collections_with_specified_actions($yaml)
    {
        $yaml->parse('yaml file')->willReturn(array(
            'App:Cheeses' => array(
                'prefix'      => '/custom/prefix',
                'resources'   => array('show', 'bam'),
                'collections' => array('index', 'paf'),
            )
        ));

        $routes = $this->load('routing.yml');

        $index = $routes->get('app_cheeses_index');
        $index->getPattern()->shouldReturn('/custom/prefix/');
        $index->getDefaults()->shouldReturn(array('_controller' => 'App:Cheeses:index'));
        $index->getRequirements()->shouldReturn(array('_method' => 'GET'));

        $paf = $routes->get('app_cheeses_paf');
        $paf->getPattern()->shouldReturn('/custom/prefix/paf');
        $paf->getDefaults()->shouldReturn(array('_controller' => 'App:Cheeses:paf'));
        $paf->getRequirements()->shouldReturn(array('_method' => 'GET'));

        $show = $routes->get('app_cheeses_show');
        $show->getPattern()->shouldReturn('/custom/prefix/{id}');
        $show->getDefaults()->shouldReturn(array('_controller' => 'App:Cheeses:show'));
        $show->getRequirements()->shouldReturn(array('_method' => 'GET'));

        $bam = $routes->get('app_cheeses_bam');
        $bam->getPattern()->shouldReturn('/custom/prefix/{id}/bam');
        $bam->getDefaults()->shouldReturn(array('_controller' => 'App:Cheeses:bam'));
        $bam->getRequirements()->shouldReturn(array('_method' => 'PUT'));

        $routes->shouldHaveCount(4);
    }

    function it_should_load_collections_with_specified_action_patterns($yaml)
    {
        $yaml->parse('yaml file')->willReturn(array(
            'App:Cheeses' => array(
                'prefix'    => '/custom/prefix',
                'resources' => array(
                    'show' => '/please/{id}/show',
                    'bam'  => '/please/{id}/bam',
                ),
                'collections' => array(
                    'index'  => '/list',
                    'paf'    => '/ouch',
                )
            )
        ));

        $routes = $this->load('routing.yml');

        $index = $routes->get('app_cheeses_index');
        $index->getPattern()->shouldReturn('/custom/prefix/list');
        $index->getDefaults()->shouldReturn(array('_controller' => 'App:Cheeses:index'));
        $index->getRequirements()->shouldReturn(array('_method' => 'GET'));

        $paf = $routes->get('app_cheeses_paf');
        $paf->getPattern()->shouldReturn('/custom/prefix/ouch');
        $paf->getDefaults()->shouldReturn(array('_controller' => 'App:Cheeses:paf'));
        $paf->getRequirements()->shouldReturn(array('_method' => 'GET'));

        $show = $routes->get('app_cheeses_show');
        $show->getPattern()->shouldReturn('/custom/prefix/please/{id}/show');
        $show->getDefaults()->shouldReturn(array('_controller' => 'App:Cheeses:show'));
        $show->getRequirements()->shouldReturn(array('_method' => 'GET'));

        $bam = $routes->get('app_cheeses_bam');
        $bam->getPattern()->shouldReturn('/custom/prefix/please/{id}/bam');
        $bam->getDefaults()->shouldReturn(array('_controller' => 'App:Cheeses:bam'));
        $bam->getRequirements()->shouldReturn(array('_method' => 'PUT'));

        $routes->shouldHaveCount(4);
    }

    function it_should_load_collections_with_custom_parameters($yaml)
    {
        $yaml->parse('yaml file')->willReturn(array(
            'App:Cheeses' => array(
                'prefix'    => '/custom/prefix',
                'resources' => array(
                    'show' => '/please/{id}/show',
                    'bam'  => array(
                        'pattern'      => '/please/{id}/bam',
                        'requirements' => array('_method' => 'GET'),
                        'defaults'     => array('_is_secured' => true)
                    ),
                ),
                'collections' => array(
                    'index'  => '/list',
                    'paf'    => array(
                        'pattern'      => '/pif-paf',
                        'requirements' => array('_method' => 'POST'),
                        'defaults'     => array('_top_menu' => 'guns')
                    ),
                )
            )
        ));

        $routes = $this->load('routing.yml');

        $index = $routes->get('app_cheeses_index');
        $index->getPattern()->shouldReturn('/custom/prefix/list');
        $index->getDefaults()->shouldReturn(array('_controller' => 'App:Cheeses:index'));
        $index->getRequirements()->shouldReturn(array('_method' => 'GET'));

        $paf = $routes->get('app_cheeses_paf');
        $paf->getPattern()->shouldReturn('/custom/prefix/pif-paf');
        $paf->getDefaults()->shouldReturn(array('_controller' => 'App:Cheeses:paf', '_top_menu' => 'guns'));
        $paf->getRequirements()->shouldReturn(array('_method' => 'POST'));

        $show = $routes->get('app_cheeses_show');
        $show->getPattern()->shouldReturn('/custom/prefix/please/{id}/show');
        $show->getDefaults()->shouldReturn(array('_controller' => 'App:Cheeses:show'));
        $show->getRequirements()->shouldReturn(array('_method' => 'GET'));

        $bam = $routes->get('app_cheeses_bam');
        $bam->getPattern()->shouldReturn('/custom/prefix/please/{id}/bam');
        $bam->getDefaults()->shouldReturn(array('_controller' => 'App:Cheeses:bam', '_is_secured' => true));
        $bam->getRequirements()->shouldReturn(array('_method' => 'GET'));

        $routes->shouldHaveCount(4);
    }

    function it_should_merge_defaults_and_requirements_into_child_routes($yaml)
    {
        $yaml->parse('yaml file')->willReturn(array(
            'App:Cheeses' => array(
                'prefix'       => '/custom/prefix',
                'defaults'     => array('_cheeses_filter' => 'french'),
                'requirements' => array('_format' => 'html|xml'),

                'resources' => array(
                    'defaults'     => array('_is_secured' => true),
                    'requirements' => array('id' => '\\d+'),

                    'show' => '/please/{id}/show',
                    'bam'  => array(
                        'pattern'      => '/please/{id}/bam',
                        'requirements' => array('_method' => 'POST')
                    ),
                ),
                'collections' => array(
                    'defaults'     => array('_top_menu' => 'guns'),
                    'requirements' => array('_stuff' => 'DELETE'),

                    'index'  => '/list',
                    'paf'    => '/pif-paf'
                )
            )
        ));

        $routes = $this->load('routing.yml');

        $index = $routes->get('app_cheeses_index');
        $index->getPattern()->shouldReturn('/custom/prefix/list');
        $index->getDefaults()->shouldReturn(array(
            '_cheeses_filter' => 'french',
            '_top_menu'       => 'guns',
            '_controller'     => 'App:Cheeses:index',
        ));
        $index->getRequirements()->shouldReturn(array(
            '_format' => 'html|xml',
            '_stuff'  => 'DELETE',
            '_method' => 'GET',
        ));

        $paf = $routes->get('app_cheeses_paf');
        $paf->getPattern()->shouldReturn('/custom/prefix/pif-paf');
        $paf->getDefaults()->shouldReturn(array(
            '_cheeses_filter' => 'french',
            '_top_menu'       => 'guns',
            '_controller'     => 'App:Cheeses:paf',
        ));
        $paf->getRequirements()->shouldReturn(array(
            '_format' => 'html|xml',
            '_stuff'  => 'DELETE',
            '_method' => 'GET',
        ));

        $show = $routes->get('app_cheeses_show');
        $show->getPattern()->shouldReturn('/custom/prefix/please/{id}/show');
        $show->getDefaults()->shouldReturn(array(
            '_cheeses_filter' => 'french',
            '_is_secured'     => true,
            '_controller'     => 'App:Cheeses:show',
        ));
        $show->getRequirements()->shouldReturn(array(
            '_format' => 'html|xml',
            'id'      => '\\d+',
            '_method' => 'GET',
        ));

        $bam = $routes->get('app_cheeses_bam');
        $bam->getPattern()->shouldReturn('/custom/prefix/please/{id}/bam');
        $bam->getDefaults()->shouldReturn(array(
            '_cheeses_filter' => 'french',
            '_is_secured'     => true,
            '_controller'     => 'App:Cheeses:bam',
        ));
        $bam->getRequirements()->shouldReturn(array(
            '_format' => 'html|xml',
            'id'      => '\\d+',
            '_method' => 'POST',
        ));

        $routes->shouldHaveCount(4);
    }

    function it_should_load_simple_routes_with_pattern($yaml)
    {
        $yaml->parse('yaml file')->willReturn(array(
            'App:Cheeses:list' => '/cheeses/{id}/list'
        ));

        $routes = $this->load('routing.yml');

        $list = $routes->get('app_cheeses_list');
        $list->getPattern()->shouldReturn('/cheeses/{id}/list');
        $list->getDefaults()->shouldReturn(array('_controller' => 'App:Cheeses:list'));
        $list->getRequirements()->shouldReturn(array('_method' => 'GET'));

        $routes->shouldHaveCount(1);
    }

    function it_should_load_simple_routes_with_params($yaml)
    {
        $yaml->parse('yaml file')->willReturn(array(
            'App:Cheeses:list' => array(
                'pattern'  => '/cheeses/list-cheeses',
                'defaults' => array('_menu' => 'list')
            )
        ));

        $routes = $this->load('routing.yml');

        $list = $routes->get('app_cheeses_list');
        $list->getPattern()->shouldReturn('/cheeses/list-cheeses');
        $list->getDefaults()->shouldReturn(array('_controller' => 'App:Cheeses:list', '_menu' => 'list'));
        $list->getRequirements()->shouldReturn(array('_method' => 'GET'));

        $routes->shouldHaveCount(1);
    }

    function it_should_create_proper_route_for_namespaced_controller($yaml)
    {
        $yaml->parse('yaml file')->willReturn(array(
            'App:Admin\Cheeses:list' => '/admin/cheeses/list-cheeses'
        ));

        $routes = $this->load('routing.yml');

        $list = $routes->get('app_admin_cheeses_list');
        $list->getPattern()->shouldReturn('/admin/cheeses/list-cheeses');

        $routes->shouldHaveCount(1);
    }

    function it_should_generate_proper_prefix_for_namespaced_controller($yaml)
    {
        $yaml->parse('yaml file')->willReturn(array(
            'App:Admin\Cheeses' => null
        ));

        $routes = $this->load('routing.yml');

        $index = $routes->get('app_admin_cheeses_index');
        $index->getPattern()->shouldReturn('/admin/cheeses/');
        $index->getDefaults()->shouldReturn(array('_controller' => 'App:Admin\Cheeses:index'));
        $index->getRequirements()->shouldReturn(array('_method' => 'GET'));

        $routes->shouldHaveCount(7);
    }

    function it_should_properly_transform_camel_cased_classes_and_groups($yaml)
    {
        $yaml->parse('yaml file')->willReturn(array(
            'SuperAppBundle:AdminControllers\Cheeses' => null
        ));

        $routes = $this->load('routing.yml');
        $routes->get('superAppBundle_adminControllers_cheeses_index')->shouldNotReturn(null);
    }

    function it_should_use_classic_loader_scheme_for_basic_routes($yaml)
    {
        $yaml->parse('yaml file')->willReturn(array(
            'blog_show' => array(
                'pattern'  => '/blog/{slug}',
                'defaults' => array('_controller' => 'AcmeBlogBundle:Blog:show'),
            ),
        ));

        $routes = $this->load('routing.yml');
        $route  = $routes->get('blog_show');

        $route->shouldNotBe(null);
        $route->getPattern()->shouldReturn('/blog/{slug}');
        $route->getDefaults()->shouldReturn(array('_controller' => 'AcmeBlogBundle:Blog:show'));
    }

    function it_should_throw_exception_if_unsupported_controller_route_param_provided($yaml)
    {
        $yaml->parse('yaml file')->willReturn(array(
            'App:Admin\Cheeses' => array(
                'unsopported_key' => true
            )
        ));

        $this->shouldThrow(new InvalidArgumentException(
            '`unsopported_key` parameter is not supported by `App:Admin\Cheeses` controller route. Use one of [prefix, defaults, requirements, options, collections, resources].'
        ))->duringLoad('routing.yml');
    }

    function it_should_throw_exception_if_unsupported_action_route_param_provided($yaml)
    {
        $yaml->parse('yaml file')->willReturn(array(
            'App:Admin\Cheeses:show' => array(
                'unsopported_key' => true
            )
        ));

        $this->shouldThrow(new InvalidArgumentException(
            '`unsopported_key` parameter is not supported by `App:Admin\Cheeses:show` action route. Use one of [pattern, defaults, requirements, options].'
        ))->duringLoad('routing.yml');
    }

    function it_should_throw_exception_if_user_uses_pattern_key_instead_of_prefix($yaml)
    {
        $yaml->parse('yaml file')->willReturn(array(
            'App:Cheeses' => array(
                'pattern' => '/custom/prefix'
            )
        ));

        $this->shouldThrow(new InvalidArgumentException(
            'The `pattern` is only supported for actions, if you want to prefix all the routes of the controller, use `prefix` instead.'
        ))->duringLoad('routing.yml');
    }

    function it_should_not_fail_when_loading_empty_resource($yaml)
    {
        $yaml->parse('yaml file')->willReturn(null);

        $routes = $this->load('routing.yml');
        $routes->shouldHaveCount(0);
    }
}
