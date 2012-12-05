<?php

namespace Knp\RadBundle\Twig;

use Knp\RadBundle\Flash\Message;
use Knp\RadBundle\Flash\MessageRenderer;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Twig extension to for flash messages rendering
 */
class FlashExtension extends \Twig_Extension
{
    private $renderer;
    private $session;

    public function __construct(MessageRenderer $renderer, Session $session)
    {
        $this->renderer = $renderer;
        $this->session  = $session;
    }

    public function getTokenParsers()
    {
        return array(
            new FlashesTokenParser()
        );
    }

    public function getName()
    {
        return 'flash';
    }

    public function renderMessage($message, $catalog = null)
    {
        if (!$message instanceof Message) {
            return $message;
        }

        return $this->renderer->render($message, $catalog);
    }

    public function getFlashes($types = null)
    {
        $flashBag = $this->session->getFlashBag();

        if (null === $types) {
            return $flashBag->all();
        }

        if (!is_array($types)) {
            $types = array($types);
        }

        $flashes = array();
        foreach ($types as $type) {
            $flashes[$type] = $flashBag->get($type, array());
        }

        return $flashes;
    }
}
