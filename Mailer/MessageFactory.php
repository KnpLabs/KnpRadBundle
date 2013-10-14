<?php

namespace Knp\RadBundle\Mailer;

use Swift_Mailer;
use Twig_Environment;
use Knp\RadBundle\AppBundle\BundleGuesser;

class MessageFactory
{
    private $mailer;
    private $twig;
    private $bundleGuesser;

    public function __construct(Swift_Mailer $mailer, Twig_Environment $twig, BundleGuesser $bundleGuesser)
    {
        $this->mailer     = $mailer;
        $this->twig       = $twig;
        $this->bundleGuesser = $bundleGuesser;
    }

    public function createMessage($class, $name, array $parameters)
    {
        $subject = $txtBody = $htmlBody = null;
        $bundle  = $this->bundleGuesser->getBundleForClass($class);
        $txtTpl  = sprintf('%s:Mails:%s.txt.twig', $bundle->getName(), $name);
        $htmlTpl = sprintf('%s:Mails:%s.html.twig', $bundle->getName(), $name);

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
