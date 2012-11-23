<?php

namespace spec\Knp\RadBundle\Finder;

use PHPSpec2\ObjectBehavior;
use Symfony\Component\Filesystem\Filesystem;

class ClassFinder extends ObjectBehavior
{
    /**
     * @param  Symfony\Component\Finder\Finder $finder
     * @param  Symfony\Component\Filesystem\Filesystem $filesystem
     */
    function let($finder, $filesystem)
    {
        $this->beConstructedWith($finder, $filesystem);
    }

    function it_should_find_classes_from_specified_the_namespace_directory($finder, $filesystem)
    {
        $filesystem->exists('/my/project/src/App/Entity')->willReturn(true);

        $finder->name('*.php')->shouldBeCalled();
        $finder->in('/my/project/src/App/Entity')->shouldBeCalled();
        $finder->getIterator()->willReturn(array(
            '/my/project/src/App/Entity/Cheese.php',
            '/my/project/src/App/Entity/CheeseRepository.php',
            '/my/project/src/App/Entity/Customer.php',
            '/my/project/src/App/Entity/CustomerRepository.php',
            '/my/project/src/App/Entity/Customer/Address.php',
        ));

        $this->findClasses('/my/project/src/App/Entity', 'App\Entity')->shouldReturn(array(
            'App\Entity\Cheese',
            'App\Entity\CheeseRepository',
            'App\Entity\Customer',
            'App\Entity\CustomerRepository',
            'App\Entity\Customer\Address',
        ));
    }

    function it_should_return_empty_array_when_directory_does_not_exist($finder, $filesystem)
    {
        $filesystem->exists('/my/project/src/App/Entity')->willReturn(false);

        $finder->in(ANY_ARGUMENTS)->shouldNotBeCalled();
        $finder->getIterator()->shouldNotBeCalled();

        $this->findClasses('/my/project/src/App/Entity', 'App\Entity')->shouldReturn(array());
    }

    function it_should_allow_to_filter_by_name_pattern($finder, $filesystem)
    {
        $filesystem->exists('/my/project/src/App/Entity')->willReturn(true);

        $finder->name('*.php')->shouldBeCalled();
        $finder->in('/my/project/src/App/Entity')->shouldBeCalled();
        $finder->getIterator()->willReturn(array(
            '/my/project/src/App/Entity/Cheese.php',
            '/my/project/src/App/Entity/CheeseRepository.php',
            '/my/project/src/App/Entity/Customer.php',
            '/my/project/src/App/Entity/CustomerRepository.php',
            '/my/project/src/App/Entity/Customer/Address.php',
        ));

        $this->findClassesMatching('/my/project/src/App/Entity', 'App\Entity', '(?<!Repository)$')->shouldReturn(array(
            'App\Entity\Cheese',
            'App\Entity\Customer',
            'App\Entity\Customer\Address',
        ));
    }
}
