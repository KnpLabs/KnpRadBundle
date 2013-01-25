<?php

// if you don't want to setup permissions the proper way, just uncomment the following PHP line
// read http://symfony.com/doc/current/book/installation.html#configuration-and-setup for more information
//umask(0000);

// this check prevents access to debug front controllers that are deployed by accident to production servers.
// feel free to remove this, extend it, or make something more sophisticated.
if (!in_array(@$_SERVER['REMOTE_ADDR'], array(
    '127.0.0.1',
    '::1',
))) {
    header('HTTP/1.0 403 Forbidden');
    exit('You are not allowed to access this file. Check '.basename(__FILE__).' for more information.');
}

use Symfony\Component\HttpFoundation\Request;
use Knp\Bundle\RadBundle\HttpKernel\RadKernel;

$loader = require(__DIR__.'/../vendor/autoload.php');
$kernel = RadKernel::createAppKernel($loader, 'test', true);
$kernel->loadClassCache();
$kernel->handle(Request::createFromGlobals())->send();
