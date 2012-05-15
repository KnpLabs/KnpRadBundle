<?php

use Symfony\Component\HttpFoundation\Request;
use Knp\Bundle\RadBundle\HttpKernel\RadKernel;

$loader = require(__DIR__.'/../vendor/autoload.php');
$kernel = RadKernel::createAppKernel($loader, 'prod', false);
$kernel->loadClassCache();
$kernel->handle(Request::createFromGlobals())->send();
