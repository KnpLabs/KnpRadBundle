<?php

namespace Knp\RadBundle\AppBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle as BaseBundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Knp\RadBundle\DependencyInjection\Compiler;

class Bundle extends BaseBundle
{
    protected $name = 'App';

    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new Compiler\RegisterDoctrineRepositoriesPass($this), PassConfig::TYPE_OPTIMIZE);
        $container->addCompilerPass(new Compiler\RegisterDoctrineDefaultRepositoryPass($this));
        $container->addCompilerPass(new Compiler\RegisterTwigExtensionsPass($this));
        $container->addCompilerPass(new Compiler\RegisterSecurityVotersPass($this));
        $container->addCompilerPass(new Compiler\RegisterAppBundlePass($this));
        $container->addCompilerPass(new Compiler\RegisterFormTypesPass($this));
        $container->addCompilerPass(new Compiler\RegisterFormTypeExtensionsPass($this));
        $container->addCompilerPass(new Compiler\RegisterValidatorConstraintsPass($this));
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
