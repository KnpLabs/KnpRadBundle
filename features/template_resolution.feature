Feature: Template resolution
    In order to load templates by convention
    As a developer
    I need rad bundle to auto resolve template location

    Scenario: Auto resolution
        Given I write in Foo controller:
        """
        public function barAction() {
            return array();
        }
        """
        And I write in "Resources/views/Foo/bar.html.twig":
        """
        Hello from bar action.
        """
        And I add route for "App:Foo:bar"
        When I visit "App:Foo:bar" page
        Then I should see "Hello from bar action."

    Scenario: Missing template
        Given I write in Foo controller:
        """
        public function bazAction() {
            return array();
        }
        """
        And I add route for "App:Foo:baz"
        When I visit "App:Foo:baz" page
        Then I should see 'The view "<strong>App:Foo:baz.html.twig</strong>" is missing.'
