<?php

namespace Knp\RadBundle\Mailer;

use Swift_Mailer;
use Twig_Environment;

class MessageFactory
{
    private $mailer;
    private $twig;

    public function __construct(Swift_Mailer $mailer, Twig_Environment $twig)
    {
        $this->mailer = $mailer;
        $this->twig   = $twig;
    }

    public function createMessage($name, array $parameters)
    {
        $subject = $txtBody = $htmlBody = null;
        $txtTpl  = sprintf('App:Mails:%s.txt.twig', $name);
        $htmlTpl = sprintf('App:Mails:%s.html.twig', $name);

        if (true === $this->twig->getLoader()->exists($txtTpl)) {
            $template = $this->twig->loadTemplate($txtTpl);
            $subject  = $template->renderBlock('subject', $parameters);
            $txtBody  = $template->renderBlock('body', $parameters);
        }

        if (true === $this->twig->getLoader()->exists($htmlTpl)) {
            $template = $this->twig->loadTemplate($htmlTpl);
            $subject  = $subject ?: $template->renderBlock('subject', $parameters);
            $htmlBody = $template->renderBlock('body', $parameters);
        }

        if (!$subject) {
            throw new \Twig_Error_Loader(sprintf(
                'Can not find mail subject in "%s" or "%s".', $txtTpl, $htmlTpl
            ));
        }

        if (!$txtBody && !$htmlBody) {
            throw new \Twig_Error_Loader(sprintf(
                'Can not find mail body in "%s" or "%s".', $txtTpl, $htmlTpl
            ));
        }

        $message = $this->mailer->createMessage();
        $message->setSubject($subject);

        if ($txtBody) {
            $message->setBody($txtBody, 'text/plain');
        } else {
            $message->setBody($htmlBody, 'text/html');
        }

        if ($txtBody && $htmlBody) {
            $message->addPart($htmlBody, 'text/html');
        }

        return $message;
    }
}
