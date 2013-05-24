<?php

namespace spec\Knp\RadBundle\DependencyInjection\Compiler;

use PhpSpec\ObjectBehavior;

class RegisterValidatorConstraintsPassSpec extends ObjectBehavior
{
    /**
     * @param Symfony\Component\HttpKernel\Bundle\BundleInterface                               $bundle
     * @param Symfony\Component\DependencyInjection\ContainerBuilder                            $container
     * @param Knp\RadBundle\Finder\ClassFinder                                                  $classFinder
     * @param Knp\RadBundle\DependencyInjection\Definition\ValidatorConstraintDefinitionFactory $definitionFactory
     * @param Knp\RadBundle\DependencyInjection\ServiceIdGenerator                              $servIdGen
     * @param Symfony\Bundle\FrameworkBundle\Validator\ConstraintValidatorFactory               $validatorFactory
     */
    function let($bundle, $classFinder, $definitionFactory, $container, $servIdGen, $validatorFactory)
    {
        $this->beConstructedWith($bundle, $classFinder, $definitionFactory, $servIdGen);

        $bundle->getPath()->shouldBeCalled()->willReturn('/my/project/src/App');
        $bundle->getNamespace()->shouldBeCalled()->willReturn('App');

        $servIdGen->generateForBundleClass($bundle, 'App\Validator\Constraints\Taste', 'validator')->willReturn('app.validator.constraints.taste_validator');
        $servIdGen->generateForBundleClass($bundle, 'App\Validator\Constraints\MinimumHole', 'validator')->willReturn('app.validator.constraints.minimum_hole_validator');

        $validatorFactory->getArgument(1)->willReturn(array());
        $container->hasDefinition('validator.validator_factory')->willReturn(true);
        $container->getDefinition('validator.validator_factory')->willReturn($validatorFactory);
    }

    /**
     * @param Symfony\Component\DependencyInjection\Definition $tasteValidatorDef
     * @param Symfony\Component\DependencyInjection\Definition $minimumHoleValidatorDef
     **/
    function it_should_add_tagged_service_for_each_validator_constraint($container, $classFinder, $definitionFactory, $tasteValidatorDef, $minimumHoleValidatorDef)
    {
        $classes = array(
            'App\Validator\Constraints\Taste',
            'App\Validator\Constraints\MinimumHole',
        );
        $classFinder->findClassesMatching('/my/project/src/App/Validator/Constraints', 'App\Validator\Constraints', '(?<!Validator)$')->shouldBeCalled()->willReturn($classes);
        $classFinder->filterClassesImplementing($classes, 'Symfony\Component\Validator\Constraint')->willReturn($classes);

        $container->hasDefinition('app.validator.constraints.taste_validator')->willReturn(false);
        $definitionFactory->createDefinition('App\Validator\Constraints\TasteValidator')->shouldBeCalled()->willReturn($tasteValidatorDef);
        $container->setDefinition('app.validator.constraints.taste_validator', $tasteValidatorDef)->shouldBeCalled();

        $container->hasDefinition('app.validator.constraints.minimum_hole_validator')->willReturn(false);
        $definitionFactory->createDefinition('App\Validator\Constraints\MinimumHoleValidator')->shouldBeCalled()->willReturn($minimumHoleValidatorDef);
        $container->setDefinition('app.validator.constraints.minimum_hole_validator', $minimumHoleValidatorDef)->shouldBeCalled();

        $this->process($container);
    }

    /**
     * @param Symfony\Component\DependencyInjection\Definition $tasteValidatorDef
     **/
    function it_should_not_add_service_with_same_id_and_tag_alias($container, $classFinder, $definitionFactory, $tasteValidatorDef)
    {
        $classes = array(
            'App\Validator\Constraints\Taste',
            'App\Validator\Constraints\MinimumHole',
        );
        $classFinder->findClassesMatching('/my/project/src/App/Validator/Constraints', 'App\Validator\Constraints', '(?<!Validator)$')->shouldBeCalled()->willReturn($classes);
        $classFinder->filterClassesImplementing($classes, 'Symfony\Component\Validator\Constraint')->willReturn($classes);

        $container->hasDefinition('app.validator.constraints.taste_validator')->willReturn(true);
        $definitionFactory->createDefinition('App\Validator\Constraints\TasteValidator')->shouldNotBeCalled();

        $container->hasDefinition('app.validator.constraints.minimum_hole_validator')->willReturn(true);
        $definitionFactory->createDefinition('App\Validator\Constraints\MinimumHoleValidator')->shouldNotBeCalled();

        $this->process($container);
    }

    /**
     * @param Symfony\Component\DependencyInjection\Definition $tasteValidatorDef
     **/
    function it_should_not_add_service_for_classes_not_extending_constraint($container, $classFinder, $definitionFactory, $tasteValidatorDef)
    {
        $classes = array(
            'App\Validator\Constraints\Taste',
            'App\Validator\Constraints\MinimumHole',
        );
        $classFinder->findClassesMatching('/my/project/src/App/Validator/Constraints', 'App\Validator\Constraints', '(?<!Validator)$')->shouldBeCalled()->willReturn($classes);
        $classFinder->filterClassesImplementing($classes, 'Symfony\Component\Validator\Constraint')->willReturn((array) $classes[0]);

        $container->hasDefinition('app.validator.constraints.taste_validator')->willReturn(false);
        $definitionFactory->createDefinition('App\Validator\Constraints\TasteValidator')->willReturn($tasteValidatorDef);
        $container->setDefinition('app.validator.constraints.taste_validator', $tasteValidatorDef)->shouldBeCalled();

        $definitionFactory->createDefinition('App\Validator\Constraints\MinimumHoleValidator')->shouldNotBeCalled();

        $this->process($container);
    }
}
