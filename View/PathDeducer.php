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

    public function __construct(TemplateNameParserInterface $nameParser, PathExpander $pathExpander)
    {
        $this->nameParser   = $nameParser;
        $this->pathExpander = $pathExpander;
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
}
