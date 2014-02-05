<?php

namespace Knp\RadBundle\Controller\Helper;

use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

class Response
{
    private $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    public function redirectToRoute($route, array $parameters = array(), $status = 302)
    {
        return new RedirectResponse($this->router->generate($route, $parameters), $status);
    }
}
