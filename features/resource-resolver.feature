Feature: Resolve request parameters to their corresponding resources
    In order to normalize the way resources as resolved
    As a RAD developer
    I need rad bundle to automatically resolve parameters given routing configuration

    Background:
        Given I write in "App/Controller/FooController.php":
        """
        <?php namespace App\Controller {
            class FooController {
                public function indexAction($object) { return ['object' => get_class($object)]; }
                public function showAction($object) { return ['object' => $object]; }
            }
        }
        """
        And I write in "App/Resources/views/Foo/index.html.twig":
        """
        {{ object }}
        """
        And I write in "App/Resources/views/Foo/show.html.twig":
        """
        {{ object == true ? 'true' }}
        """

    Scenario: static service resolution
        Given I write in "App/Resources/config/rad_convention.yml":
        """
        App:Foo:
            defaults:
                _resources: {'object': {service: service_container, method: get, arguments: [{ value: router}] }}
        """
        And I visit "app_foo_index" page
        Then I should see "Symfony\Bundle\FrameworkBundle\Routing\Router"

    Scenario: dynamic resolution
        Given I write in "App/Resources/config/rad_convention.yml":
        """
        App:Foo:
            defaults:
                _resources: {'object': {service: service_container, method: has, arguments: [{ name: id}] }}
        """
        And I go to "/foo/templating"
        Then I should see "true"

    Scenario: expression language resolution
        Given I write in "App/Resources/config/rad_convention.yml":
        """
        App:Foo:
            defaults:
                _resources: {object: {expr: "service('service_container').has(request.attributes.get('id'))"}}
        """
        And I go to "/foo/templating"
        Then I should see "true"
