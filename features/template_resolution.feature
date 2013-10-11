Feature: Template resolution
    In order to load templates by convention
    As a developer
    I need rad bundle to auto resolve template location

    Scenario:
        Given I write in Foo controller:
        """
        public function barAction() {
            return array();
        }
        """
        And I write in "Resources/Foo/bar.html.twig":
        """
        Hello from bar action.
        """
        And I add route for "App:Foo:bar"
        When I visit "App:Foo:bar" page
        Then I should see "Hello from bar action."
