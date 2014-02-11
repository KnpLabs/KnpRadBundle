<?php

namespace Knp\RadBundle\Routing\Conventional\Generator;

use Knp\RadBundle\Routing\Conventional\Generator;
use Knp\RadBundle\Routing\Conventional\Config;

class Pattern implements Generator
{
    private $map = array(
        'index'  => '.{_format}',
        'new'    => '/new.{_format}',
        'create' => '/new.{_format}',
        'edit'   => '/{id}/edit.{_format}',
        'update' => '/{id}/edit.{_format}',
        'show'   => '/{id}.{_format}',
        'delete' => '/{id}.{_format}',
    );

    public function __construct(array $map = null)
    {
        $this->map = $map ?: $this->map;
    }

    /**
     * recursively create a pattern, prepending parent "representant"(i.e: "show") pattern
     * this parent pattern is modified to replace {id} with {<name>Id}, and remove {_format} if needed
     *
     * @return string
     **/
    public function generate(Config $config)
    {
        $parts = array();
        if ($config->parent) {
            $parts[] = str_replace(
                array('{id}', '.{_format}'),
                array(sprintf('{%sId}', $this->getPrefix($config->parent)), ''),
                $this->generate($config->parent)
            );
        }
        $parts[] = $this->getPrefix($config);

        $prefix = implode('/', array_filter($parts));
        $pattern = $this->getPattern($config);

        if (isset($pattern[0]) && '/' !== $pattern[0] && '.' !== $pattern[0]) {
            return rtrim($prefix, '/').'/'.$pattern;
        }

        return $prefix.$pattern;
    }

    /**
     * prefix is the name (by default). This name can be of form: "App:Something"
     *
     * @return string
     **/
    private function getPrefix(Config $config)
    {
        $parts = explode(':', $config->getPrefix());
        array_shift($parts);

        return strtolower(str_replace(array('/', '\\'), '_', implode('_', $parts)));
    }

    private function getPattern(Config $config)
    {
        $pattern = $config->getPattern();
        if (!empty($pattern)) {
            return $pattern;
        }

        if (isset($this->map[$config->name])) {
            return $this->map[$config->name];
        }
    }
}
