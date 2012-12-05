<?php

namespace Knp\RadBundle\Flash;

use Symfony\Component\Translation\TranslatorInterface;

class MessageRenderer
{
    private $translator;
    private $transCatalog;

    public function __construct(TranslatorInterface $translator, $transCatalog = 'messages')
    {
        $this->translator   = $translator;
        $this->transCatalog = $transCatalog;
    }

    public function render(Message $message, $transCatalog = null)
    {
        return $this->translator->trans(
            $message->getTemplate(),
            $message->getParameters(),
            $transCatalog ?: $this->transCatalog,
            $message->getPluralization()
        );
    }
}
