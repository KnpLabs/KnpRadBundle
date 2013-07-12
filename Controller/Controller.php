<?php

namespace Knp\RadBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller as BaseController;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityRepository;
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

    protected function getRepository($entity)
    {
        if (is_object($entity) && $entity instanceof EntityRepository) {

            return $entity;
        }

        if (is_object($entity)) {

            return $this->getEntityManager()->getRepository(get_class($entity));
        }

        if (is_string($entity)) {

            return $this->getEntityManager()->getRepository($entity);
        }

        throw new \InvalidArgumentException(
<<<MESSAGE
The entity argument should be a Doctrine entity,
an EntityRepository, an entity class name (ex: App/Entity/MyEntity)
or a short entity name (ex: App:MyEntity).
MESSAGE
        );

    }

    protected function isGranted($attributes, $object = null)
    {
        return $this->getSecurity()->isGranted($attributes, $object);
    }

    protected function createMessage($name, array $parameters = array(), $from = null, $to = null)
    {
        $message = $this->get('knp_rad.mailer.message_factory')->createMessage($name, $parameters);

        if ($from) {
            $message->setFrom($from);
        }
        if ($to) {
            $message->setTo($to);
        }

        return $message;
    }

    protected function send(\Swift_Mime_Message $message)
    {
        $this->getMailer()->send($message);
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

    protected function findOr404($entity, $criterias)
    {
        $result = null;
        $findMethod = is_scalar($criterias) ? 'find' : 'findOneBy';

        if (is_object($entity) && $this->getEntityManager()->contains($entity)) {
            $result = $this->getEntityManager()->refresh($entity);
        } else {
            $repository = $this->getRepository($entity);
            $result = $repository->$findMethod($criterias);
        }

        if (null !== $result){
            return $result;
        }

        throw $this->createNotFoundException('Resource not found');
    }

    protected function addFlash($type, $message = null, array $parameters = array(), $pluralization = null)
    {
        $message = $message ?: sprintf('%s.%s', $this->getRequest()->attributes->get('_route'), $type);

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

    protected function getEntityManager()
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
