<?php

namespace Knp\RadBundle\View;

use Symfony\Component\Templating\TemplateNameParserInterface;
use Knp\RadBundle\Filesystem\PathExpander;

/**
 * The path deducer is responsible of deducing paths from logical view names
 */
class PathDeducer
{
    private $nameParser;
    private $pathExpander;
    private $logicalNames = array();
    private $defaultLogicalName;

    public function __construct(TemplateNameParserInterface $nameParser, PathExpander $pathExpander, array $logicalNames, $defaultLogicalName)
    {
        $this->nameParser   = $nameParser;
        $this->pathExpander = $pathExpander;
        $this->logicalNames = $logicalNames;
        $this->defaultLogicalName = $defaultLogicalName;
    }

    /**
     * Returns the path corresponding to the specified view name
     *
     * @param  string $logicalName A logical view name (i.e FooBundle:Bar:baz.html.twig)
     *
     * @return string The path of the view
     */
    public function deducePath($logicalName)
    {
        $path = $this->nameParser->parse($logicalName)->getPath();

        return $this->pathExpander->expand($path);
    }

    public function deduceViewLogicalName($logicalName)
    {
        $name = $this->nameParser->parse($logicalName)->get('name');

        if (isset($this->logicalNames[$name])) {
            return $this->logicalNames[$name];
        }

        return $this->defaultLogicalName;
    }
}
