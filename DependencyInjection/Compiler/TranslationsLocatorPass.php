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
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\Finder\Finder;

/**
 * Adds application bundle i18n folder support.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
class TranslationsLocatorPass implements CompilerPassInterface
{
    /**
     * Processes application bundle i18n files.
     *
     * @param ContainerBuilder $container Container instance
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('translator.default')) {
            return;
        }
        if (!$container->getParameter('knp_rad.application_structure')) {
            return;
        }

        $translator = $container->findDefinition('translator');
        $projectDir = $container->getParameter('kernel.project_dir');

        $dirs = array();
        if (is_dir($dir = $projectDir.'/translations')) {
            $dirs[] = $dir;
        }

        // Register translation resources
        if ($dirs) {
            $finder = new Finder();
            $finder->files()->filter(function (\SplFileInfo $file) {
                return 2 === substr_count($file->getBasename(), '.') && preg_match('/\.\w+$/', $file->getBasename());
            })->in($dirs);

            foreach ($finder as $file) {
                // filename is domain.locale.format
                list($domain, $locale, $format) = explode('.', $file->getBasename(), 3);

                $translator->addMethodCall('addResource', array($format, (string) $file, $locale, $domain));
            }
        }
    }
}
