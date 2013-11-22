Feature: CSRF protection for unsafe requests
    In order to protect against CSRF attacks
    As a developer
    I need rad bundle to automatically verify request origin via CSRF tokens when asked

    Background:
        Given I write in "App/Controller/FooController.php":
        """
        <?php namespace App\Controller {
            class FooController extends \Knp\RadBundle\Controller\Controller
            {
                public function showAction() {}

                public function deleteAction()
                {
                    return new \Symfony\Component\HttpFoundation\Response('ok');
                }
            }
        }
        """
        And I write in "App/Resources/config/routing.yml":
        """
        App:Foo:show: ~
        App:Foo:delete:
            defaults: { _check_csrf: true }
        App:Foo:deleteUnsafe:
            defaults: { _check_csrf: false, _controller: App:Foo:delete }
        """
        And I write in "App/Resources/views/Foo/show.html.twig":
        """
        {% extends 'KnpRadBundle:Layout:h5bp.html.twig' %}
        {% block body %}
            <a {{ link_attr('delete') }} href="{{ path('app_foo_delete') }}">Delete</a>
            <a data-method="delete" data-csrf-token="test" href="{{ path('app_foo_delete') }}">Invalid Delete</a>
            <a data-method="delete" href="{{ path('app_foo_deleteUnsafe') }}">Unsafe Delete</a>
        {% endblock body %}
        """

    @javascript
    Scenario: valid token sent
        Given I visit "app_foo_show" page
        When I follow "Delete"
        Then I should see "ok"

    Scenario: Invalid token sent
        Given I visit "app_foo_show" page
        When I follow "Invalid Delete"
        Then I should see "The CSRF token is invalid. Please submit a request with a valid csrf token."

    Scenario: no token sent
        When I visit "app_foo_delete" page
        Then I should see "The CSRF token verification is activated but you did not send a token. Please submit a request with a valid csrf token."

    Scenario: deactivated csrf protection
        When I visit "app_foo_show" page
        When I follow "Unsafe Delete"
        Then I should see "ok"

