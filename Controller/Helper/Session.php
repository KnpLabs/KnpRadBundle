<?php

namespace Knp\RadBundle\Controller\Helper;

use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Knp\RadBundle\Flash\Message;

class Session
{
    private $session;
    private $requestStack;

    public function __construct(SessionInterface $session, RequestStack $requestStack)
    {
        $this->session = $session;
        $this->requestStack = $requestStack;
    }

    public function addFlash($type, $message = null, array $parameters = array(), $pluralization = null)
    {
        $message = $message ?: sprintf('%s.%s', $this->requestStack->getMasterRequest()->attributes->get('_route'), $type);
        $this->getFlashBag()->add($type, new Message($message, $parameters, $pluralization));
    }

    public function getSession()
    {
        return $this->session;
    }

    public function getFlashBag()
    {
        return $this->getSession()->getFlashBag();
    }
}
