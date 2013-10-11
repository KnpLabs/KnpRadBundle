Feature: Auto detection of forms
    In order to make my life easier
    As a developer
    I need rad bundle to register DI services automatically

    Scenario: Add a Form type that exists

        Given I write in "App/Form/TestType.php":
        """
        <?php

        namespace App\Form;

        use Symfony\Component\Form\AbstractType;

        class TestType extends AbstractType
        {
            function getName() {
                return 'test';
            }
        }
        """
        Then it should be registered

    Scenario: FormType class that does not implement correct Interface is not registered

    Scenario: If FormType implements ContainerAwareInterface, it should have the container
