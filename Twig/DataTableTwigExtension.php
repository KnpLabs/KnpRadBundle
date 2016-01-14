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
            new \Twig_SimpleFunction('bootstrap_datatable', array($this, 'getDataTableRender'), array('is_safe' => array('html'))),
            new \Twig_SimpleFunction('bootstrap_datatable_row', array($this, 'getDataTableRowRender'), array('is_safe' => array('html'))),
        );
    }

    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('toArray', array($this, 'toArrayFilter')),
            new \Twig_SimpleFilter('toHeadersArray', array($this, 'toHeadersFilter')),
        );
    }

    public function getDataTableRender($elements, $options = array())
    {
        $options = array_merge(array('bootstrap' => "Default"), $options);

        return $this
            ->container
            ->get('templating')
            ->render(
                'KnpRadBundle:Twig:Datatable/' . $options['bootstrap'] . '/datatable.html.twig',
                array('elements' => $elements, 'options' => $options)
            )
        ;
    }

    public function getDataTableRowRender($element, $headers, $options = array())
    {
        $options = array_merge(array('bootstrap' => "Default"), $options);

        $routes = isset($options['routes'])
            ? $options['routes']
            : array()
        ;

        return $this
            ->container
            ->get('templating')
            ->render(
                'KnpRadBundle:Twig:Datatable/' . $options['bootstrap'] . '/datatable_row.html.twig',
                array(
                    'element' => $element,
                    'headers' => $headers,
                    'options' => $options,
                    'routes'  => $routes
                )
            )
        ;
    }

    public function toHeadersFilter($els, $options = array ())
    {

        $fields = isset($options['fields'])
            ? $options['fields']
            : array()
        ;

        $headers = array();

        foreach ($els as $el) {

            if (is_object($el)) {

                foreach ($this->getGettersWithoutParameters($el) as $method) {
                    preg_match('#get(?P<name>.*)#', $method->name, $matches);
                    $name = strtolower($matches['name']);
                    if (!in_array($name, $headers) && (0 === count($fields) || array_key_exists($name, $fields))) {
                        $headers[$name] = array_key_exists($name, $fields)
                            ? $fields[$name]
                            : $name
                        ;
                    }
                }

            } elseif (is_array($el)) {

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

    public function toArrayFilter($el, $options = array ())
    {

        $fields = isset($options['fields'])
            ? $options['fields']
            : array()
        ;

        $values = array();

        if (is_object($el)) {

            foreach ($this->getGettersWithoutParameters($el) as $method) {
                preg_match('#get(?P<name>.*)#', $method->name, $matches);

                $name = strtolower($matches['name']);
                if (0 === count($fields) || array_key_exists($name, $fields)) {
                    $value = $el->{$method->name}();
                    if (!is_object($value) && !is_array($value)) {
                        $values[$name] = $value;
                    }
                }
            }

        } elseif (is_array($el)) {

            foreach ($el as $key => $value) {
                if (!is_object($value) && !is_array($value)) {
                    $values[$key] = $value;
                }
            }

        }

        return $values;
    }

    public function getGettersWithoutParameters($el)
    {
        $rfl = new \ReflectionClass($el);

        return array_filter(
            $rfl->getMethods(\ReflectionMethod::IS_PUBLIC),
            function ($method) {
                return preg_match('#get.*#', $method->name) && 0 === count($method->getParameters());
            }

        );
    }

    public function getName()
    {
        return 'knp_rad.twig.datatable';
    }

}
