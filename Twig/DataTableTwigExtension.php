<?php

namespace Knp\RadBundle\Twig;

class DataTableTwigExtension extends \Twig_Extension
{

    public function getFilters()
    {
        return array(
            'toArray' => new \Twig_Filter_Method($this, 'toArrayFilter'),
            'toHeadersArray' => new \Twig_Filter_Method($this, 'toHeadersFilter'),
        );
    }

    public function toHeadersFilter($els)
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
                    if (!in_array(strtolower($matches['name']), $headers)) {
                        $headers[] = strtolower($matches['name']);
                    }
                }

            }

        }

        return $headers;

    }

    public function toArrayFilter($el)
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
                $value = $el->{$method->name}();
                if (!is_object($value) && !is_array($value)) {
                    $values[strtolower($matches['name'])] = $value;
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