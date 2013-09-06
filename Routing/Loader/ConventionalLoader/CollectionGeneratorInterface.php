<?php

namespace Knp\RadBundle\Routing\Loader\ConventionalLoader;

interface CollectionGeneratorInterface
{
    function generateRoute($name, $mapping);

    function generate($name, $mapping);
}
