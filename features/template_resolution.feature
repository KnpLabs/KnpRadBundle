Feature: Template resolution
    In order to load templates by convention
    As a developer
    I need rad bundle to auto resolve template location

    Background:
        Given I write in "App/Controller/FooController.php":
        """
        <?php namespace App\Controller {
            class FooController
            {
                public function barAction() {}
                public function bazAction() {}
            }
        }
        """
        And I write in "App/Resources/config/rad_convention.yml":
        """
        App:Foo:bar: ~
        App:Foo:baz: ~
        """

    Scenario: Auto resolution
        Given I write in "App/Resources/views/Foo/bar.html.twig":
        """
        Hello from bar action.
        """
        When I visit "app_foo_bar" page
        Then I should see "Hello from bar action."

    Scenario: Missing template
        Given I visit "app_foo_baz" page
        Then I should see text matching "The view .*App:Foo:baz.html.twig.* is missing"
