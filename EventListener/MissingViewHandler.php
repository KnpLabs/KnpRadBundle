<?php

namespace Knp\RadBundle\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class MissingViewHandler
{
    public function handleMissingView(GetResponseEvent $event, $viewName, array $viewParams = null)
    {
        $kernel = $event->getKernel();

        $response = $kernel->forward('KnpRadBundle:Assistant:missingView', array(
            'viewName'   => $viewName,
            'viewParams' => $viewParams,
        ));

        $event->setResponse($response);
    }
}
