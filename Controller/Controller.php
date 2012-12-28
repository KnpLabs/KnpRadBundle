<?php

namespace Knp\RadBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller as BaseController;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpFoundation\Request;
use Knp\RadBundle\Flash;

class Controller extends BaseController
{
    protected function redirectToRoute($route, $parameters = array(), $status = 302)
    {
        return $this->redirect($this->generateUrl($route, $parameters), $status);
    }

    protected function createAccessDeniedException($message = 'Access Denied', \Exception $previous = null)
    {
        return new AccessDeniedHttpException($message, $previous);
    }

    protected function getEntityManager()
    {
        return $this->getDoctrine()->getManager();
    }

    protected function getRepository($entity)
    {
        return is_object($entity)
            ? $this->getEntityManager()->getRepository(get_class($entity))
            : $this->getEntityManager()->getRepository($entity)
        ;
    }

    protected function getSecurity()
    {
        return $this->get('security.context');
    }

    protected function isGranted($attributes, $object = null)
    {
        return $this->getSecurity()->isGranted($attributes, $object);
    }

    protected function getMailer()
    {
        return $this->get('mailer');
    }

    protected function createMessageFor($name, array $parameters)
    {
        return $this->get('knp_rad.mailer.message_factory')->createMessage($name, $parameters);
    }

    protected function sendMessage($from, $to, $name, array $parameters)
    {
        $message = $this->createMessageFor($name, $parameters);
        $message->setTo($to);
        $message->setFrom($from);

        $this->getMailer()->send($message);
    }

    protected function getSession()
    {
        return $this->get('session');
    }

    protected function getFlashBag()
    {
        return $this->getSession()->getFlashBag();
    }

    protected function persist($entity, $flush = false)
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->flush($entity);
        }
    }

    protected function remove($entity, $flush = false)
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->flush();
        }
    }

    protected function flush($entity = null)
    {
        $this->getEntityManager()->flush($entity);
    }

    protected function findOr404($entity, $criterias = array())
    {
        $result = null;

        if (is_object($entity) && $this->getEntityManager()->contains($entity)) {
            $result = $this->getEntityManager()->refresh($entity);
        } elseif (is_string($entity)) {
            $repository = $this->getRepository($entity);
            $result = $repository->findOneBy($criterias);
        }

        if (null !== $result){
            return $result;
        }

        throw $this->createNotFoundException('Resource not found');
    }

    protected function addFlash($type, $message, array $parameters = array(), $pluralization = null)
    {
        $this->getFlashBag()->add($type, new Flash\Message($message, $parameters, $pluralization));
    }

    protected function createObjectForm($object, $purpose = null, array $options = array())
    {
        return $this->get('knp_rad.form.manager')->createObjectForm($object, $purpose, $options);
    }

    protected function createBoundObjectForm($object, $purpose = null, array $options = array())
    {
        return $this->get('knp_rad.form.manager')->createBoundObjectForm($object, $purpose, $options);
    }

    protected function emailText($to, $text)
    {
        $message = $this->getMailer()->createMessage();
        $message->setTo($to);
        $message->setBody($text);

        return $this->getMailer()->send($message);
    }
}
