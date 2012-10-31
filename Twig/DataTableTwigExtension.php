<?php

namespace Knp\RadBundle\Twig;

class DataTableTwigExtension extends \Twig_Extension
{

    private $container;

    public function __construct($container)
    {
        $this->container = $container;
    } 

    public function getFunctions()
    {
        return array(
            'bootstrap_datatable'       => new \Twig_Function_Method($this, 'getDataTableRender', array('is_safe' => array('html'))),
            'bootstrap_datatable_row'   => new \Twig_Function_Method($this, 'getDataTableRowRender', array('is_safe' => array('html'))),
        );
    }

    public function getFilters()
    {
        return array(
            'toArray' => new \Twig_Filter_Method($this, 'toArrayFilter'),
            'toHeadersArray' => new \Twig_Filter_Method($this, 'toHeadersFilter'),
        );
    }

    public function getDataTableRender($elements, $fields = array())
    {
        return $this
            ->container
            ->get('templating')
            ->render(
                'KnpRadBundle:Twig:datatable.html.twig',
                array('elements' => $elements, 'fields' => $fields)
            )
        ;
    }

    public function getDataTableRowRender($element, $headers, $fields = array())
    {
        return $this
            ->container
            ->get('templating')
            ->render(
                'KnpRadBundle:Twig:datatable_row.html.twig',
                array('element' => $element, 'headers' => $headers, 'fields' => $fields)
            )
        ;
    }

    public function toHeadersFilter($els, $fields = array ())
    {
        $headers = array();

        foreach ($els as $el) {

            if (is_object($el)) {
            
                $rfl = new \ReflectionClass($el);

                $methods = array_filter(
                    $rfl->getMethods(\ReflectionMethod::IS_PUBLIC), 
                    function($method){
                        return preg_match('#get.*#', $method->name);
                    }
                );

                foreach ($methods as $method) {
                    preg_match('#get(?P<name>.*)#', $method->name, $matches);
                    $name = strtolower($matches['name']);
                    if (!in_array($name, $headers) && (0 === count($fields) || array_key_exists($name, $fields))) {
                        $headers[$name] = array_key_exists($name, $fields)
                            ? $fields[$name]
                            : $name
                        ;
                    }
                }

            } else if(is_array($el)) {

                foreach ($el as $key => $value) {
                    if (!in_array($key, $headers) && (0 === count($fields) || array_key_exists($key, $fields))) {
                        $headers[$key] = array_key_exists($key, $fields)
                            ? $fields[$key]
                            : $key
                        ;
                    }
                }

            }

        }

        return $headers;

    }

    public function toArrayFilter($el, $fields = array ())
    {

        $values = array();

        if (is_object($el)) {
        
            $rfl = new \ReflectionClass($el);

            $methods = array_filter(
                $rfl->getMethods(\ReflectionMethod::IS_PUBLIC), 
                function($method){
                    return preg_match('#get.*#', $method->name);
                }
            );

            foreach ($methods as $method) {
                preg_match('#get(?P<name>.*)#', $method->name, $matches);

                $name = strtolower($matches['name']);
                if (0 === count($fields) || array_key_exists($name, $fields)) {
                    $value = $el->{$method->name}();
                    if (!is_object($value) && !is_array($value)) {
                        $values[$name] = $value;
                    }
                }
            }

        } else if(is_array($el)) {

            foreach ($el as $key => $value) {
                if (!is_object($value) && !is_array($value)) {
                    $values[$key] = $value;
                }
            }

        }

        return $values;
    }

    public function getName()
    {
        return 'knp_rad.twig.datatable';
    }

}