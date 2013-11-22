Feature: Auto detection of Twig extensions
    In order to make my life easier
    As a developer
    I need rad bundle to register DI services automatically

    Scenario: Add a Twig extension that exists
        Given I write in "Twig/FooExtension.php":
        """
        <?php

        namespace App\Twig;

        class FooExtension extends \Twig_Extension
        {
            public function getFunctions()
            {
                return array(
                    'foo' => new \Twig_Function_Method($this, 'getName'),
                );
            }

            public function getName()
            {
                return 'foo';
            }
        }
        """
        Then "foo" should be a registered twig extension

    Scenario: Twig extension that does not implement correct Interface is not registered
        Given I write in "Twig/FooExtension.php":
        """
        <?php

        namespace App\Twig;

        class FooExtension
        {
        }
        """
        Then "foo" should not be a registered twig extension
