Feature: CSRF protection for unsafe requests
    In order to protect against CSRF attacks
    As a developer
    I need rad bundle to automatically verify request origin via CSRF tokens when asked

    Background:
        Given I write in "Controller/FooController.php":
        """
        <?php

        namespace App\Controller;

        class FooController extends \Knp\RadBundle\Controller\Controller
        {
            public function showAction()
            {
                return array();
            }

            public function deleteAction()
            {
                return new \Symfony\Component\HttpFoundation\Response('ok');
            }
        }
        """
        And I add route for "App:Foo:show"
        And I add route for "App:Foo:delete":
            | _check_csrf | true |
        And I write in "Resources/views/Foo/show.html.twig":
        """
            <a {{ link_attr('delete') }} href="{{ path('app_foo_delete') }}">Delete</a>
        """

    @javascript
    Scenario: valid token sent
        Given I visit "App:Foo:show" page
        When I follow "delete"
        Then I should see "ok"

    Scenario: no token sent
        When I visit "App:Foo:delete" page
        Then I should see "The CSRF token verification is activated but you did not send a token. Please submit a request with a valid csrf token."

    Scenario: token sent but invalid
        When I visit "App:Foo:delete" page
        Then I should see "The CSRF token is invalid. Please submit a request with a valid csrf token."
