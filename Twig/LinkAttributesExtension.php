<?php

namespace Knp\RadBundle\Twig;

use Knp\RadBundle\Flash\Message;
use Knp\RadBundle\Flash\MessageRenderer;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Form\Extension\Csrf\CsrfProvider\CsrfProviderInterface;

class LinkAttributesExtension extends \Twig_Extension
{
    private $csrfProvider;

    public function __construct(CsrfProviderInterface $csrfProvider)
    {
        $this->csrfProvider = $csrfProvider;
    }

    public function getName()
    {
        return 'link_attributes';
    }

    public function getFunctions()
    {
        return array(
            'delete_attributes' => new \Twig_Function_Method($this, 'getDeleteAttributes', array('is_safe' => array('html'))),
            'post_attributes'   => new \Twig_Function_Method($this, 'getPostAttributes', array('is_safe'   => array('html'))),
            'put_attributes'    => new \Twig_Function_Method($this, 'getPutAttributes', array('is_safe'    => array('html'))),
            'patch_attributes'  => new \Twig_Function_Method($this, 'getPatchAttributes', array('is_safe'  => array('html'))),
        );
    }

    public function getDeleteAttributes($confirm = 'Are you sure?')
    {
        $html = 'data-method="delete"';

        if ($confirm !== false) {
            $html .= sprintf(' data-confirm="%s"', $confirm);
        } else {
            $html .= ' data-no-confirm';
        }

        return sprintf('%s data-csrf-token="%s"', $html, $this->csrfProvider->generateCsrfToken('delete'));
    }

    public function getPostAttributes()
    {
        return $this->getAttributesForMethod('post');
    }

    public function getPutAttributes()
    {
        return $this->getAttributesForMethod('put');
    }

    public function getPatchAttributes()
    {
        return $this->getAttributesForMethod('patch');
    }

    protected function getAttributesForMethod($method)
    {
        return sprintf(
            'data-method="%s" data-csrf-token="%s"',
            $method,
            $this->csrfProvider->generateCsrfToken($method)
        );
    }
}
