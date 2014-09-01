<?php

namespace Knp\RadBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller as BaseController;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Doctrine\ORM\EntityRepository;
use Knp\RadBundle\Flash;

class Controller extends BaseController
{
    protected function redirectToRoute($route, array $parameters = array(), $status = 302)
    {
        return $this->getHelper('response')->redirectToRoute($route, $parameters, $status);
    }

    protected function getRepository($object)
    {
        return $this->getHelper('doctrine')->getRepository($object);
    }

    protected function isGranted($attributes, $object = null)
    {
        return $this->getHelper('security')->isGranted($attributes, $object);
    }

    protected function isGrantedOr403($object, $criteria = array())
    {
        return $this->getHelper('security')->isGrantedOr403($object, $criteria);
    }

    protected function createMessage($name, array $parameters = array(), $from = null, $to = null)
    {
        return $this->getHelper('mail')->createMessage(get_class($this), $name, $parameters, $from, $to);
    }

    protected function send(\Swift_Mime_Message $message)
    {
        $this->getHelper('mail')->send($message);
    }

    protected function persist($object, $flush = false)
    {
        return $this->getHelper('doctrine')->persist($object, $flush);
    }

    protected function remove($object, $flush = false)
    {
        return $this->getHelper('doctrine')->remove($object, $flush);
    }

    protected function flush($object = null)
    {
        return $this->getHelper('doctrine')->flush($object);
    }

    protected function findBy($object, $criteria = array())
    {
        return $this->getHelper('doctrine')->findBy($object, $criteria);
    }

    protected function findOr404($object, $criteria = array())
    {
        return $this->getHelper('doctrine')->findOr404($object, $criteria);
    }

    protected function addFlash($type, $message = null, array $parameters = array(), $pluralization = null)
    {
        return $this->getHelper('session')->addFlash($type, $message, $parameters, $pluralization);
    }

    protected function createObjectForm($object, $purpose = null, array $options = array())
    {
        return $this->getHelper('form')->createObjectForm($object, $purpose, $options);
    }

    protected function createBoundObjectForm($object, $purpose = null, array $options = array())
    {
        return $this->getHelper('form')->createBoundObjectForm($object, $purpose, $options);
    }

    protected function getHelper($name)
    {
        return $this->get('knp_rad.controller.helper.'.$name);
    }

    protected function getSession()
    {
        return $this->get('session');
    }

    protected function getMailer()
    {
        return $this->get('mailer');
    }

    protected function getSecurity()
    {
        return $this->get('security.context');
    }

    protected function getManager()
    {
        return $this->getDoctrine()->getManager();
    }

    protected function getFlashBag()
    {
        return $this->getSession()->getFlashBag();
    }

    protected function getParameter($name)
    {
        return $this->container->getParameter($name);
    }
}
