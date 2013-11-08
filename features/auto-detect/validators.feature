Feature: Auto detection of validators
    In order to make my life easier
    As a developer
    I need rad bundle to register DI services automatically

    Scenario: Add a validator that exists
        Given I write in "Validator/Constraints/Foo.php":
        """
        <?php

        namespace App\Validator\Constraints;

        use Symfony\Component\Validator\Constraint;

        /**
         * @Annotation
         */
        class Foo extends Constraint
        {
            public $message = 'Foo.';
        }
        """
        Given I write in "Validator/Constraints/FooValidator.php":
        """
        <?php

        namespace App\Validator\Constraints;

        use Symfony\Component\Validator\Constraint;

        class FooValidator extends ConstraintValidator
        {
            public function validate($value, Constraint $constraint)
            {
            }
        }
        """
        Then "foo" should be a registered validator

    Scenario: Validator that does not extends Constraint is not registered
        Given I write in "Validator/Constraints/Foo.php":
        """
        <?php

        namespace App\Validator\Constraints;

        use Symfony\Component\Validator\Constraint;

        /**
         * @Annotation
         */
        class Foo
        {
        }
        """
        Then "foo" should not be a registered validator
