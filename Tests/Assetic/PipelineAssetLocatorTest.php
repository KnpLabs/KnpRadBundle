<?php

namespace Knp\Bundle\RadBundle\Tests\Assetic;

use Knp\Bundle\RadBundle\Assetic\PipelineAssetLocator;

class PipelineAssetLocatorTest extends \PHPUnit_Framework_TestCase
{
    private $locator;
    private $paths;

    protected function setUp()
    {
        $this->locator = new PipelineAssetLocator($this->paths = array(
            __DIR__.'/fixtures/assets1',
            __DIR__.'/fixtures/assets2',
            __DIR__.'/fixtures/assets3',
        ), array('less', 'sass', 'coffee'));
    }

    public function testSimpleAssetsLocating()
    {
        $this->assertSame(
            array(array(
                'include' => false,
                'file'    => 'css/application.css',
                'filter'  => null,
                'root'    => __DIR__.'/fixtures/assets3'
            )),
            $this->locator->locatePipelinedAssets('application.css')
        );
    }

    public function testAssetsOverriding()
    {
        $this->assertSame(
            array(array(
                'include' => false,
                'file'    => 'css/overrider.css',
                'filter'  => null,
                'root'    => __DIR__.'/fixtures/assets1'
            )),
            $this->locator->locatePipelinedAssets('overrider.css')
        );
    }

    public function testAssetNotFound()
    {
        try {
            $this->locator->locatePipelinedAssets('unexisting.css');
            $this->fail('Unexisting asset should throw exception');
        } catch (\RuntimeException $e) {
            $this->assertInstanceOf('RuntimeException', $e);
            $this->assertSame(sprintf(
                'Asset "unexisting.css" could not be found anywhere in registered pipeline paths (%s)',
                implode(', ', $this->paths)
            ), $e->getMessage());
        }
    }

    public function testDirectoryIndexAsset()
    {
        $this->assertSame(
            array(array(
                'include' => false,
                'file'    => 'js/custom_library/index.js',
                'filter'  => null,
                'root'    => __DIR__.'/fixtures/assets2'
            )),
            $this->locator->locatePipelinedAssets('custom_library', 'js')
        );
    }

    public function testPreprocessorAsset()
    {
        $this->assertSame(
            array(array(
                'include' => false,
                'file'    => 'js/some_script.coffee',
                'filter'  => 'coffee',
                'root'    => __DIR__.'/fixtures/assets2'
            )),
            $this->locator->locatePipelinedAssets('some_script', 'js')
        );

        $this->assertSame(
            array(array(
                'include' => false,
                'file'    => 'css/sub/some_less_style.less',
                'filter'  => 'less',
                'root'    => __DIR__.'/fixtures/assets1'
            )),
            $this->locator->locatePipelinedAssets('sub/some_less_style.css', 'css')
        );
    }

    public function testJsRequireDirective()
    {
        $this->assertSame(
            array(
                array(
                    'include' => false,
                    'file'    => 'js/sub/asset_one.coffee',
                    'filter'  => 'coffee',
                    'root'    => __DIR__.'/fixtures/assets1'
                ),
                array(
                    'include' => false,
                    'file'    => 'js/asset_three.js',
                    'filter'  => null,
                    'root'    => __DIR__.'/fixtures/assets3'
                ),
                array(
                    'include' => false,
                    'file'    => 'js/sub/application.js',
                    'filter'  => null,
                    'root'    => __DIR__.'/fixtures/assets2'
                )
            ),
            $this->locator->locatePipelinedAssets('sub/application', 'js')
        );
    }

    public function testJsRequireSelfDirective()
    {
        $this->assertSame(
            array(
                array(
                    'include' => false,
                    'file'    => 'js/sub/asset_one.coffee',
                    'filter'  => 'coffee',
                    'root'    => __DIR__.'/fixtures/assets1'
                ),
                array(
                    'include' => false,
                    'file'    => 'js/sub/application_self.js',
                    'filter'  => null,
                    'root'    => __DIR__.'/fixtures/assets2'
                ),
                array(
                    'include' => false,
                    'file'    => 'js/asset_three.js',
                    'filter'  => null,
                    'root'    => __DIR__.'/fixtures/assets3'
                )
            ),
            $this->locator->locatePipelinedAssets('sub/application_self.js')
        );
    }

    public function testRecursiveRequireDirective()
    {
        $this->assertSame(
            array(
                array(
                    'include' => false,
                    'file'    => 'css/recursive_require_style.less',
                    'filter'  => 'less',
                    'root'    => __DIR__.'/fixtures/assets1'
                ),
                array(
                    'include' => false,
                    'file'    => 'css/overrider.css',
                    'filter'  => null,
                    'root'    => __DIR__.'/fixtures/assets1'
                ),
                array(
                    'include' => false,
                    'file'    => 'css/second_level_of_recursive_style.sass',
                    'filter'  => 'sass',
                    'root'    => __DIR__.'/fixtures/assets3'
                ),
                array(
                    'include' => false,
                    'file'    => 'css/sub/sub2/topbar.css',
                    'filter'  => null,
                    'root'    => __DIR__.'/fixtures/assets2'
                ),
            ),
            $this->locator->locatePipelinedAssets('recursive_require_style', 'css')
        );
    }

    public function testDontRequireSameAssetTwice()
    {
        $this->assertSame(
            array(
                array(
                    'include' => false,
                    'file'    => 'js/some_asset.js',
                    'filter'  => null,
                    'root'    => __DIR__.'/fixtures/assets2'
                ),
                array(
                    'include' => false,
                    'file'    => 'js/another_asset_that_require_same_asset.js',
                    'filter'  => null,
                    'root'    => __DIR__.'/fixtures/assets2'
                ),
                array(
                    'include' => false,
                    'file'    => 'js/require_same_assets.js',
                    'filter'  => null,
                    'root'    => __DIR__.'/fixtures/assets2'
                ),
            ),
            $this->locator->locatePipelinedAssets('require_same_assets.js')
        );
    }

    public function testCanIncludeSameAssetTwice()
    {
        $this->assertSame(
            array(
                array(
                    'include' => false,
                    'file'    => 'js/some_asset.js',
                    'filter'  => null,
                    'root'    => __DIR__.'/fixtures/assets2'
                ),
                array(
                    'include' => true,
                    'file'    => 'js/some_asset.js',
                    'filter'  => null,
                    'root'    => __DIR__.'/fixtures/assets2'
                ),
                array(
                    'include' => false,
                    'file'    => 'js/another_asset_that_include_same_asset.js',
                    'filter'  => null,
                    'root'    => __DIR__.'/fixtures/assets2'
                ),
                array(
                    'include' => false,
                    'file'    => 'js/include_same_assets.js',
                    'filter'  => null,
                    'root'    => __DIR__.'/fixtures/assets2'
                ),
            ),
            $this->locator->locatePipelinedAssets('include_same_assets.js')
        );
    }

    public function testAttemptToRequireUnexistingAsset()
    {
        try {
            $this->locator->locatePipelinedAssets('failed.js');
            $this->fail('Unexisting asset should throw exception');
        } catch (\RuntimeException $e) {
            $this->assertInstanceOf('RuntimeException', $e);
            $this->assertSame(sprintf(
                'Asset "unexisting.js" could not be found anywhere in registered pipeline paths (%s) in asset: %s',
                implode(', ', $this->paths), 'js/failed.js'
            ), $e->getMessage());
        }
    }

    public function testRequireRelativePaths()
    {
        $this->assertSame(
            array(
                array(
                    'include' => false,
                    'file'    => 'js/application/main/sub/script.js',
                    'filter'  => null,
                    'root'    => __DIR__.'/fixtures/assets3'
                ),
                array(
                    'include' => false,
                    'file'    => 'js/application/main/current_dir_script.coffee',
                    'filter'  => 'coffee',
                    'root'    => __DIR__.'/fixtures/assets3'
                ),
                array(
                    'include' => false,
                    'file'    => 'js/application/main/asset_with_relative_paths.js',
                    'filter'  => null,
                    'root'    => __DIR__.'/fixtures/assets3'
                ),
            ),
            $this->locator->locatePipelinedAssets('application/main/asset_with_relative_paths.js')
        );
    }

    public function testRequireDirectory()
    {
        $this->assertSame(
            array(
                array(
                    'include' => false,
                    'file'    => 'js/directory/1script.js',
                    'filter'  => null,
                    'root'    => __DIR__.'/fixtures/assets1'
                ),
                array(
                    'include' => false,
                    'file'    => 'js/directory/2script.coffee',
                    'filter'  => 'coffee',
                    'root'    => __DIR__.'/fixtures/assets1'
                ),
                array(
                    'include' => false,
                    'file'    => 'js/directory/index.js',
                    'filter'  => null,
                    'root'    => __DIR__.'/fixtures/assets1'
                ),
                array(
                    'include' => false,
                    'file'    => 'js/directory/tree.js',
                    'filter'  => null,
                    'root'    => __DIR__.'/fixtures/assets1'
                ),
            ),
            $this->locator->locatePipelinedAssets('directory', 'js')
        );
    }

    public function testRequireTree()
    {
        $this->assertSame(
            array(
                array(
                    'include' => false,
                    'file'    => 'js/directory/1script.js',
                    'filter'  => null,
                    'root'    => __DIR__.'/fixtures/assets1'
                ),
                array(
                    'include' => false,
                    'file'    => 'js/directory/2script.coffee',
                    'filter'  => 'coffee',
                    'root'    => __DIR__.'/fixtures/assets1'
                ),
                array(
                    'include' => false,
                    'file'    => 'js/directory/index.js',
                    'filter'  => null,
                    'root'    => __DIR__.'/fixtures/assets1'
                ),
                array(
                    'include' => false,
                    'file'    => 'js/directory/sub/fourth.coffee',
                    'filter'  => 'coffee',
                    'root'    => __DIR__.'/fixtures/assets1'
                ),
                array(
                    'include' => false,
                    'file'    => 'js/directory/sub/third.js',
                    'filter'  => null,
                    'root'    => __DIR__.'/fixtures/assets1'
                ),
                array(
                    'include' => false,
                    'file'    => 'js/directory/tree.js',
                    'filter'  => null,
                    'root'    => __DIR__.'/fixtures/assets1'
                ),
            ),
            $this->locator->locatePipelinedAssets('directory/tree', 'js')
        );
    }
}
