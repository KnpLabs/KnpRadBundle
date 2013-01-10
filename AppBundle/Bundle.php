<?php

namespace Knp\RadBundle\AppBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle as BaseBundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Knp\RadBundle\DependencyInjection\Compiler;

class Bundle extends BaseBundle
{
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new Compiler\RegisterDoctrineRepositoriesPass($this));
        $container->addCompilerPass(new Compiler\RegisterTwigExtensionsPass($this));
        $container->addCompilerPass(new Compiler\RegisterSecurityVotersPass($this));
        $container->addCompilerPass(new Compiler\RegisterAppBundlePass($this));
        $container->addCompilerPass(new Compiler\RegisterFormTypesPass($this));
        $container->addCompilerPass(new Compiler\RegisterFormCreatorsPass);
    }

    public function getContainerExtension()
    {
        if ($extension = parent::getContainerExtension()) {
            return $extension;
        }

        return $this->extension = new ContainerExtension($this->getPath());
    }
}
