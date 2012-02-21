<?php

/*
 * This file is part of the KnpRadBundle package.
 *
 * (c) KnpLabs <http://knplabs.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Knp\Bundle\RadBundle\Assetic;

use Symfony\Bundle\AsseticBundle\Factory\AssetFactory as BaseFactory;

use Assetic\Asset\FileAsset;
use Assetic\Asset\AssetCollection;

/**
 * Enhances asset factory with assets pipeline support.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
class PipelineAssetFactory extends BaseFactory
{
    private $locator;

    /**
     * Sets pipeline assets locator.
     *
     * @param PipelineAssetLocator $locator
     */
    public function setPipelineAssetLocator(PipelineAssetLocator $locator)
    {
        $this->locator = $locator;
    }

    /**
     * Adds support for pipeline assets.
     *
     * {@inheritdoc}
     */
    protected function parseInput($input, array $options = array())
    {
        if (is_string($input) && '|' == $input[0]) {
            switch (pathinfo($options['output'], PATHINFO_EXTENSION)) {
                case 'js':
                    $type = 'js';
                    break;
                case 'css':
                    $type = 'css';
                    break;
                default:
                    throw new \RuntimeException('Unsupported pipeline asset type provided: '.$input);
            }

            $assets = new AssetCollection();
            foreach ($this->locator->locatePipelinedAssets(substr($input, 1), $type) as $formula) {
                $filters = array();
                if ($formula['filter']) {
                    $filters[] = $this->getFilter($formula['filter']);
                }
                $asset = new FileAsset(
                    $formula['root'].'/'.$formula['file'],
                    $filters,
                    $options['root'][0],
                    $formula['file']
                );
                $asset->setTargetPath($formula['file']);
                $assets->add($asset);
            }

            return $assets;
        }

        return parent::parseInput($input, $options);
    }
}
