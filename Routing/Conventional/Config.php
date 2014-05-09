<?php

namespace Knp\RadBundle\Routing\Conventional;

class Config
{
    public $name;
    public $parent;
    private $config;

    public function __construct($name, $config = null, Config $parent = null)
    {
        $this->name = $name;
        if (!is_array($config)) {
            $config = array('pattern' => $config);
        }
        $this->config = $config;
        $this->parent = $parent;
    }

    public function getController()
    {
        if ($this->get('controller')) {
            return $this->get('controller');
        }
        if ($this->parent) {
            return $this->parent->getController();
        }
    }

    public function getView()
    {
        if ($this->get('view')) {
            return $this->get('view');
        }
        if ($this->parent) {
            return $this->parent->getView();
        }

        return $this->name;
    }

    public function getPattern()
    {
        return $this->get('pattern');
    }

    public function getPrefix()
    {
        return $this->get('prefix');
    }

    public function getDefaults()
    {
        $defaults = $this->get('defaults', array(
            '_format' => 'html'
        ));
        if ($this->parent) {
            $defaults = array_merge($this->parent->getDefaults(), $defaults);
        }

        return $defaults;
    }

    public function getRequirements()
    {
        $requirements = $this->get('requirements', array());
        if ($this->parent) {
            $requirements = array_merge($this->parent->getRequirements(), $requirements);
        }

        return $requirements;
    }

    public function getMethods()
    {
        $default = array();
        if ($this->parent) {
            $default = $this->parent->getMethods();
        }

        return $this->get('methods', $default);
    }

    public function getElements()
    {
        $elements = $this->getDefaultElements();

        foreach ($elements as $key => $element) {
            if (!in_array($key, $this->get('elements', array_keys($elements)))) {
                unset($elements[$key]); // TODO improve this filter, couldn't get it work with array_intersect_*
            }
        }

        $ignore = array_flip(array(
            'parent',
            'elements',
            'pattern',
            'prefix',
            'controller',
            'methods',
            'defaults',
            'requirements',
        ));

        $others = array_keys(array_diff_key($this->config, $ignore, $elements));
        foreach ($others as $name) {
            $config = $this->config[$name] ?: array();
            if (!is_array($config)) {
                $config = array('pattern' => $config);
            }
            $prefix = false === $this->getPrefix() ?: $this->name;
            $elements[$name] = new static(
                $name,
                array_merge(array('pattern' => $name, 'prefix' => $prefix), $config),
                $this
            );
        }

        return $elements;
    }

    public function isRepresentant()
    {
        return $this->get('is_representant', false);
    }

    public function getRepresentant()
    {
        $elements = $this->getElements();
        return current(array_filter($elements, function($element) {
            return $element->isRepresentant();
        }));
    }

    private function merge($path, array $config)
    {
        return array_merge($config, $this->get($path, array()));
    }

    private function get($path, $default = null)
    {
        if (isset($this->config[$path])) {
            return $this->config[$path];
        }

        return $default;
    }

    private function getDefaultElements()
    {
        return array(
            'index' => new static('index', $this->merge('index', array(
                'methods'      => array('GET'),
                'prefix'       => $this->name,
            )), $this),
            'new' => new static('new', $this->merge('new', array(
                'methods'      => array('GET'),
                'prefix'       => $this->name,
            )), $this),
            'create' => new static('create', $this->merge('create', array(
                'methods'      => array('POST'),
                'prefix'       => $this->name,
            )), $this),
            'edit' => new static('edit', $this->merge('edit', array(
                'methods'      => array('GET'),
                'requirements' => array('id' => '\d+'),
                'prefix'       => $this->name,
            )), $this),
            'update' => new static('update', $this->merge('update', array(
                'methods'      => array('PUT'),
                'requirements' => array('id' => '\d+'),
                'prefix'       => $this->name,
            )), $this),
            'show' => new static('show', $this->merge('show', array(
                'methods'      => array('GET'),
                'requirements' => array('id' => '\d+'),
                'is_representant' => true,
                'prefix'       => $this->name,
            )), $this),
            'delete' => new static('delete', $this->merge('delete', array(
                'methods'      => array('DELETE'),
                'requirements' => array('id' => '\d+'),
                'prefix'       => $this->name,
            )), $this),
        );
    }
}
