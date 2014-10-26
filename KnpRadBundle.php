<?php

namespace Knp\RadBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Knp\RadBundle\DependencyInjection\Compiler\Routing\ServiceBased;

class KnpRadBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new ServiceBased);
    }
}
