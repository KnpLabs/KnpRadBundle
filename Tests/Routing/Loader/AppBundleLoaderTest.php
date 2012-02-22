<?php

namespace Knp\Bundle\RadBundle\Tests\Routing;

require_once __DIR__.'/../../../HttpKernel/RadAppKernel.php';

use Knp\Bundle\RadBundle\Routing\Loader\AppBundleLoader;

class AppBundleLoaderTest extends \PHPUnit_Framework_TestCase
{
    private $loader;
    private $bundle;

    protected function setUp()
    {
        $this->kernel = $this->getMockBuilder('RadAppKernel')
            ->disableOriginalConstructor()
            ->getMock();

        $this->bundle = new Bundle();

        $this->kernel
            ->expects($this->any())
            ->method('getBundle')
            ->with('App', false)
            ->will($this->returnValue(array($this->bundle)));

        $locator = $this->getMockBuilder('Symfony\Component\Config\FileLocatorInterface')
            ->getMock();

        $locator
            ->expects($this->any())
            ->method('locate')
            ->will($this->returnCallback(function($path){ return $path; }));

        $this->loader = new AppBundleLoader($this->kernel, $locator);
    }

    public function testLoadSingleRouteFile()
    {
        $this->bundle->path = __DIR__.'/Fixtures';
        $collection = $this->loader->load('.')->all();

        $this->assertCount(2, $collection);
        $this->assertSame('/blog/{slug}', $collection['blog_show']->getPattern());
        $this->assertSame('/blog/{slug}/edit', $collection['blog_edit']->getPattern());
    }
}

class Bundle
{
    public $path;

    public function getPath()
    {
        return $this->path;
    }
}
