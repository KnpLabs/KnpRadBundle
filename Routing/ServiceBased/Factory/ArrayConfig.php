<?php

namespace Knp\RadBundle\Routing\ServiceBased\Factory;

use Symfony\Component\Routing\Route;

class ArrayConfig
{
    public function create($id, array $config)
    {
        $config = array_merge(array(
            'path' => '/',
            'defaults' => array(),
            'requirements' => array(),
            'options' => array(),
            'host' => '',
            'schemes' => array(),
            'methods' => array(),
            'condition' => null,
        ), $config);

        return new Route(
            $config['path'],
            array_merge(array('_controller' => $id), $config['defaults']),
            $config['requirements'],
            $config['options'],
            $config['host'],
            $config['schemes'],
            $config['methods'],
            $config['condition']
        );
    }
}
