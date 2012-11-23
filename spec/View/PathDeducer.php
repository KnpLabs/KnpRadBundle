<?php

namespace spec\Knp\RadBundle\View;

use PHPSpec2\ObjectBehavior;

class PathDeducer extends ObjectBehavior
{
    /**
     * @param  Symfony\Component\Templating\TemplateNameParserInterface $nameParser
     * @param  Symfony\Component\Templating\TemplateReferenceInterface  $tplRef
     * @param  Knp\RadBundle\Filesystem\PathExpander                    $pathExpander
     */
    function let($nameParser, $pathExpander, $tplRef)
    {
        $this->beConstructedWith($nameParser, $pathExpander);
    }

    function it_should_deduce_path($nameParser, $pathExpander, $tplRef)
    {
        $nameParser->parse('App:Cheeses:eat.html.twig')->willReturn($tplRef);
        $tplRef->getPath()->willReturn('@App/Resources/views/Cheeses/eat.html.twig');

        $pathExpander->expand('@App/Resources/views/Cheeses/eat.html.twig')->willReturn('/expanded/path');

        $this->deducePath('App:Cheeses:eat.html.twig')->shouldReturn('/expanded/path');
    }
}
