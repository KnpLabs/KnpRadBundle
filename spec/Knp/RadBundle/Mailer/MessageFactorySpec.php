<?php

namespace spec\Knp\RadBundle\Mailer;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class MessageFactorySpec extends ObjectBehavior
{
    /**
     * @param  Swift_Mailer               $mailer
     * @param  Twig_Environment           $twig
     * @param  Twig_ExistsLoaderInterface $loader
     * @param  Knp\RadBundle\AppBundle\BundleGuesser $bundleGuesser
     * @param  Symfony\Component\HttpKernel\Bundle\BundleInterface $bundle
     */
    function let($mailer, $twig, $loader, $bundleGuesser, $bundle)
    {
        $twig->getLoader()->willReturn($loader);

        $this->beConstructedWith($mailer, $twig, $bundleGuesser);

        $bundleGuesser->getBundleForClass(Argument::any())->willReturn($bundle);
        $bundle->getName()->willReturn('App');
    }

    /**
     * @param  Swift_Mime_Message $message
     * @param  Twig_Template      $template
     * @param  stdClass           $subject
     * @param  stdClass           $body
     */
    function it_should_create_a_txt_message_if_only_txt_template_available(
        $mailer, $twig, $loader, $message, $template, $subject, $body
    )
    {
        $loader->exists('App:Mails:contact_message.txt.twig')->willReturn(true);
        $twig->loadTemplate('App:Mails:contact_message.txt.twig')->willReturn($template);
        $loader->exists('App:Mails:contact_message.html.twig')->willReturn(false);

        $template->renderBlock('subject', array('foo' => 'bar'))->willReturn($subject);
        $template->renderBlock('body', array('foo' => 'bar'))->willReturn($body);

        $mailer->createMessage()->willReturn($message);
        $message->setSubject($subject)->shouldBeCalled();
        $message->setBody($body, 'text/plain')->shouldBeCalled();

        $this->createMessage('App\Controller\Message', 'contact_message', array('foo' => 'bar'))->shouldReturn($message);
    }

    /**
     * @param  Swift_Mime_Message $message
     * @param  Twig_Template      $template
     * @param  stdClass           $subject
     * @param  stdClass           $body
     */
    function it_should_create_an_html_message_if_only_html_template_available(
        $mailer, $twig, $loader, $message, $template, $subject, $body
    )
    {
        $loader->exists('App:Mails:contact_message.txt.twig')->willReturn(false);
        $loader->exists('App:Mails:contact_message.html.twig')->willReturn(true);
        $twig->loadTemplate('App:Mails:contact_message.html.twig')->willReturn($template);

        $template->renderBlock('subject', array('foo' => 'bar'))->willReturn($subject);
        $template->renderBlock('body', array('foo' => 'bar'))->willReturn($body);

        $mailer->createMessage()->willReturn($message);
        $message->setSubject($subject)->shouldBeCalled();
        $message->setBody($body, 'text/html')->shouldBeCalled();

        $this->createMessage('App\Controller\Test', 'contact_message', array('foo' => 'bar'))->shouldReturn($message);
    }

    /**
     * @param  Swift_Message      $message
     * @param  Twig_Template      $template1
     * @param  Twig_Template      $template2
     * @param  stdClass           $subject1
     * @param  stdClass           $body1
     * @param  stdClass           $subject2
     * @param  stdClass           $body2
     */
    function it_should_create_a_multipart_message_if_both_html_and_txt_templates_available(
        $mailer, $twig, $loader, $message, $template1, $template2, $subject1, $body1, $subject2, $body2
    )
    {
        $loader->exists('App:Mails:contact_message.txt.twig')->willReturn(true);
        $twig->loadTemplate('App:Mails:contact_message.txt.twig')->willReturn($template1);
        $loader->exists('App:Mails:contact_message.html.twig')->willReturn(true);
        $twig->loadTemplate('App:Mails:contact_message.html.twig')->willReturn($template2);

        $template1->renderBlock('subject', array('foo' => 'bar'))->willReturn($subject1);
        $template1->renderBlock('body', array('foo' => 'bar'))->willReturn($body1);

        $template2->renderBlock('subject', array('foo' => 'bar'))->willReturn($subject2);
        $template2->renderBlock('body', array('foo' => 'bar'))->willReturn($body2);

        $mailer->createMessage()->willReturn($message);
        $message->setSubject($subject1)->shouldBeCalled();
        $message->setBody($body1, 'text/plain')->shouldBeCalled();
        $message->addPart($body2, 'text/html')->shouldBeCalled();

        $this->createMessage('App\Controller\Message', 'contact_message', array('foo' => 'bar'))->shouldReturn($message);
    }

    /**
     * @param  Swift_Mime_Message $message
     */
    function it_should_throw_exception_if_no_template_available(
        $mailer, $twig, $loader, $message
    )
    {
        $loader->exists('App:Mails:contact_message.txt.twig')->willReturn(false);
        $loader->exists('App:Mails:contact_message.html.twig')->willReturn(false);

        $this->shouldThrow('Twig_Error_Loader')->duringCreateMessage('App\Controller\Test', 'contact_message', array());
    }

    /**
     * @param  Swift_Mime_Message $message
     * @param  Twig_Template      $template1
     * @param  Twig_Template      $template2
     * @param  stdClass           $subject1
     * @param  stdClass           $body1
     * @param  stdClass           $subject2
     * @param  stdClass           $body2
     */
    function it_should_throw_exception_if_both_templates_provide_no_subject_or_body(
        $mailer, $twig, $loader, $message, $template1, $template2
    )
    {
        $loader->exists('App:Mails:contact_message.txt.twig')->willReturn(true);
        $twig->loadTemplate('App:Mails:contact_message.txt.twig')->willReturn($template1);
        $loader->exists('App:Mails:contact_message.html.twig')->willReturn(true);
        $twig->loadTemplate('App:Mails:contact_message.html.twig')->willReturn($template2);

        $template1->renderBlock('subject', array('foo' => 'bar'))->willReturn(null);
        $template1->renderBlock('body', array('foo' => 'bar'))->willReturn(null);

        $template2->renderBlock('subject', array('foo' => 'bar'))->willReturn(null);
        $template2->renderBlock('body', array('foo' => 'bar'))->willReturn(null);

        $this->shouldThrow('Twig_Error_Loader')->duringCreateMessage('App\Controller\Test', 'contact_message', array(
            'foo' => 'bar'
        ));
    }
}
