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
        return $this->get('knp_rad.controller.helper.response')->redirectToRoute($route, $parameters, $status);
    }

    public function createAccessDeniedException($message = 'Access Denied', \Exception $previous = null)
    {
        return $this->get('knp_rad.controller.helper.security')->createAccessDeniedException($message, $exception);
    }

    protected function getRepository($object)
    {
        return $this->get('knp_rad.controller.helper.doctrine')->getRepository($object);
    }

    protected function isGranted($attributes, $object = null)
    {
        return $this->get('knp_rad.controller.helper.security')->isGranted($attributes, $object);
    }

    protected function isGrantedOr403($object, $criteria = array())
    {
        return $this->get('knp_rad.controller.helper.security')->isGrantedOr403($object, $criteria);
    }

    protected function createMessage($name, array $parameters = array(), $from = null, $to = null)
    {
        return $this->get('knp_rad.controller.helper.mail')->createMessage($name, $parameters, $from, $to);
    }

    protected function send(\Swift_Mime_Message $message)
    {
        $this->get('knp_rad.controller.helper.mail')->send($message);
    }

    protected function persist($object, $flush = false)
    {
        return $this->get('knp_rad.controller.helper.doctrine')->persist($object, $flush);
    }

    protected function remove($object, $flush = false)
    {
        return $this->get('knp_rad.controller.helper.doctrine')->remove($object, $flush);
    }

    protected function flush($object = null)
    {
        return $this->get('knp_rad.controller.helper.doctrine')->flush($object);
    }

    protected function findOr404($object, $criteria = array())
    {
        return $this->get('knp_rad.controller.helper.doctrine')->findOr404($object, $criteria);
    }

    protected function addFlash($type, $message = null, array $parameters = array(), $pluralization = null)
    {
        return $this->get('knp_rad.controller.helper.session')->addFlash($type, $message, $parameters, $pluralization);
    }

    protected function createObjectForm($object, $purpose = null, array $options = array())
    {
        return $this->get('knp_rad.controller.helper.form')->createObjectForm($object, $purpose, $options);
    }

    protected function createBoundObjectForm($object, $purpose = null, array $options = array())
    {
        return $this->get('knp_rad.controller.helper.form')->createBoundObjectForm($object, $purpose, $options);
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
