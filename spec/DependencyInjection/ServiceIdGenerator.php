<?php

namespace spec\Knp\RadBundle\DependencyInjection;

use PHPSpec2\ObjectBehavior;

class ServiceIdGenerator extends ObjectBehavior
{
    function it_should_generate_a_service_id_for_a_class()
    {
        $this->generateForClassName('App\Rating\ArticleRater')->shouldReturn('app.rating.article_rater');
    }
}
