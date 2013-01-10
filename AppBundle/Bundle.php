<?php

namespace Knp\RadBundle\AppBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle as BaseBundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Knp\RadBundle\DependencyInjection\Compiler\RegisterDoctrineRepositoriesPass;
use Knp\RadBundle\DependencyInjection\Compiler\RegisterTwigExtensionsPass;
use Knp\RadBundle\DependencyInjection\Compiler\RegisterSecurityVotersPass;
use Knp\RadBundle\DependencyInjection\Compiler\RegisterAppBundlePass;
use Knp\RadBundle\DependencyInjection\Compiler\RegisterFormTypesCompilerPass;
use Knp\RadBundle\DependencyInjection\Compiler\RegisterFormCreatorsPass;

class Bundle extends BaseBundle
{
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new RegisterDoctrineRepositoriesPass($this));
        $container->addCompilerPass(new RegisterTwigExtensionsPass($this));
        $container->addCompilerPass(new RegisterSecurityVotersPass($this));
        $container->addCompilerPass(new RegisterAppBundlePass($this));
        $container->addCompilerPass(new RegisterFormTypesCompilerPass($this));
        $container->addCompilerPass(new RegisterFormCreatorsPass);
    }

    public function getContainerExtension()
    {
        if ($extension = parent::getContainerExtension()) {
            return $extension;
        }

        return $this->extension = new ContainerExtension($this->getPath());
    }
}
