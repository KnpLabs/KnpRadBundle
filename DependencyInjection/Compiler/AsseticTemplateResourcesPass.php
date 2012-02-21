<?php

/*
 * This file is part of the KnpRadBundle package.
 *
 * (c) KnpLabs <http://knplabs.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Knp\Bundle\RadBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Bundle\AsseticBundle\DependencyInjection\DirectoryResourceDefinition;
use Symfony\Bundle\AsseticBundle\DependencyInjection\Compiler\TemplateResourcesPass as BasePass;

/**
 * Adds application bundle view folder support to assetic parser.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
class AsseticTemplateResourcesPass extends BasePass
{
    /**
     * Patches kernel parameters.
     *
     * @param ContainerBuilder $container Container instance
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->getParameter('knp_rad.application_structure')) {
            return;
        }

        parent::process($container);
    }

    /**
     * {@inheritdoc}
     */
    protected function setBundleDirectoryResources(ContainerBuilder $container, $engine, $bundleDirName, $bundleName)
    {
    }

    /**
     * {@inheritdoc}
     */
    protected function setAppDirectoryResources(ContainerBuilder $container, $engine)
    {
        $container->setDefinition(
            'assetic.'.$engine.'_directory_resource.kernel',
            new DirectoryResourceDefinition('', $engine, array(
                $container->getParameter('kernel.project_dir').'/views'
            ))
        );
    }
}
