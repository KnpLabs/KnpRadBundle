<?php

namespace spec\Knp\RadBundle\DependencyInjection;

use PHPSpec2\ObjectBehavior;

class ServiceIdGenerator extends ObjectBehavior
{
    /**
     * @param  Symfony\Component\HttpKernel\Bundle\BundleInterface $bundle
     * @param  Symfony\Component\DependencyInjection\Extension\ExtensionInterface $extension
     */
    function let($bundle, $extension)
    {
        $bundle->getNamespace()->willReturn('Knp\BlogBundle');
        $extension->getAlias()->willReturn('knp_blog');
        $bundle->getContainerExtension()->willReturn($extension);

    }

    function it_should_generate_a_service_id_for_a_bundle_class($bundle)
    {
        $this->generateForBundleClass($bundle, 'Knp\BlogBundle\Rating\ArticleRater')->shouldReturn('knp_blog.rating.article_rater');
    }

    function it_should_generate_a_service_id_with_a_suffix_if_provided($bundle)
    {
        $this->generateForBundleClass($bundle, 'Knp\BlogBundle\Entity\BlogPost', 'repository')->shouldReturn('knp_blog.entity.blog_post_repository');
    }
}
