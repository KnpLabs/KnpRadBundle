<?php

namespace Knp\Bundle\RadBundle\Tests\HttpKernel\Bundle;

use Knp\Bundle\RadBundle\HttpKernel\Bundle\AppBundle;

class AppBundleTest extends \PHPUnit_Framework_TestCase
{
    public function testCreationWithLongProjectNamespace()
    {
        $bundle = new AppBundle('Some\\Enormously\\Big\\Project\\Namespace', 'src');

        $this->assertSame('Some\\Enormously\\Big\\Project\\Namespace', $bundle->getNamespace());
        $this->assertSame('App', $bundle->getName());
        $this->assertSame('src/Some/Enormously/Big/Project/Namespace', $bundle->getPath());
    }

    public function testCreationWithShortProjectNamespace()
    {
        $bundle = new AppBundle('Namespace', 'src');

        $this->assertSame('Namespace', $bundle->getNamespace());
        $this->assertSame('App', $bundle->getName());
        $this->assertSame('src/Namespace', $bundle->getPath());
    }

    public function testCreationWithNormalProjectNamespace()
    {
        $bundle = new AppBundle('Acme\\Hello', 'src');

        $this->assertSame('Acme\\Hello', $bundle->getNamespace());
        $this->assertSame('App', $bundle->getName());
        $this->assertSame('src/Acme/Hello', $bundle->getPath());
    }

    public function testGetGenericAppExtension()
    {
        $bundle = new AppBundle('Knp\Bundle\RadBundle\AppBundle\Fixture1\App', '');

        $this->assertInstanceOf(
            'Knp\Bundle\RadBundle\DependencyInjection\Extension\AppExtension', $bundle->getContainerExtension()
        );
    }

    public function testGetCustomAppExtension()
    {
        $bundle = new AppBundle('Knp\Bundle\RadBundle\AppBundle\Fixture2\App', '');

        $this->assertInstanceOf(
            'Knp\Bundle\RadBundle\AppBundle\Fixture2\App\DependencyInjection\AppExtension',
            $bundle->getContainerExtension()
        );
    }
}

namespace Knp\Bundle\RadBundle\AppBundle\Fixture2\App\DependencyInjection;

use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class AppExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
    }
}
