<?php

namespace spec\Knp\RadBundle\View;

use PhpSpec\ObjectBehavior;

class PathDeducerSpec extends ObjectBehavior
{
    /**
     * @param  Symfony\Component\Templating\TemplateNameParserInterface $nameParser
     * @param  Symfony\Component\Templating\TemplateReferenceInterface  $tplRef
     * @param  Knp\RadBundle\Filesystem\PathExpander                    $pathExpander
     */
    function let($nameParser, $pathExpander, $tplRef)
    {
        $this->beConstructedWith(
            $nameParser,
            $pathExpander,
            array('new' => 'KnpRadBundle:Assistant/skeleton:_new.html.twig',),
            'KnpRadBundle:Assistant/skeleton:_viewBody.html.twig'
        );
    }

    function it_should_deduce_path($nameParser, $pathExpander, $tplRef)
    {
        $nameParser->parse('App:Cheeses:eat.html.twig')->willReturn($tplRef);
        $tplRef->getPath()->willReturn('@App/Resources/views/Cheeses/eat.html.twig');

        $pathExpander->expand('@App/Resources/views/Cheeses/eat.html.twig')->willReturn('/expanded/path');

        $this->deducePath('App:Cheeses:eat.html.twig')->shouldReturn('/expanded/path');
    }

    function it_should_deduce_the_skeleton_for_registered_names($nameParser, $pathExpander, $tplRef)
    {
        $nameParser->parse('App:Cheeses:new.html.twig')->willReturn($tplRef);
        $tplRef->get('name')->willReturn('new');

        $this->deduceViewLogicalName('App:Cheeses:new.html.twig')->shouldReturn('KnpRadBundle:Assistant/skeleton:_new.html.twig');
    }

    function it_should_deduce_the_fallback_skeleton_name($nameParser, $pathExpander, $tplRef)
    {
        $nameParser->parse('App:Cheeses:eat.html.twig')->willReturn($tplRef);
        $tplRef->get('name')->willReturn('eat');

        $this->deduceViewLogicalName('App:Cheeses:eat.html.twig')->shouldReturn('KnpRadBundle:Assistant/skeleton:_viewBody.html.twig');
    }
}
