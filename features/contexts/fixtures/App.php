<?php

namespace fixtures;

use Knp\RadBundle\AppBundle\Bundle;

class App extends Bundle
{
    public function getNamespace()
    {
        return 'App';
    }

    public function getPath()
    {
        return __DIR__.'/tmp/App';
    }
}
