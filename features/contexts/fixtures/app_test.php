<?php

use Symfony\Component\HttpFoundation\Request;

$loader = require __DIR__.'/../../../vendor/autoload.php';
$loader->add('App', __DIR__.'/tmp');

require __DIR__.'/AppKernel.php';

$kernel = new fixtures\AppKernel;
$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
