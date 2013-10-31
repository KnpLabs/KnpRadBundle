<?php

namespace Knp\RadBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller as BaseController;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityRepository;
use Knp\RadBundle\Flash;

class Controller extends BaseController
{
    /**
     * @param string  $route
     * @param mixed   $parameters
     * @param integer $status
     * 
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    protected function redirectToRoute($route, $parameters = array(), $status = 302)
    {
        return $this->redirect($this->generateUrl($route, $parameters), $status);
    }

    /**
     * @param string     $message
     * @param \Exception $previous
     * 
     * @return \Symfony\Component\Security\Core\Exception\AccessDeniedException
     */
    protected function createAccessDeniedException($message = 'Access Denied', \Exception $previous = null)
    {
        return new AccessDeniedException($message, $previous);
    }

    /**
     * @param object $entity
     * 
     * @return \Doctrine\ORM\EntityRepository
     */
    protected function getRepository($entity)
    {
        return $this->getEntityManager()->getRepository(is_object($entity) ? get_class($entity) : $entity);
    }

    /**
     * @param mixed      $attributes
     * @param mixed|null $object
     * 
     * @return boolean
     */
    protected function isGranted($attributes, $object = null)
    {
        return $this->getSecurity()->isGranted($attributes, $object);
    }

    /**
     * @param string      $name Template name
     * @param array       $parameters
     * @param string|null $from
     * @param string|null $to
     * 
     * @return object
     */
    protected function createMessage($name, array $parameters = array(), $from = null, $to = null)
    {
        $message = $this->get('knp_rad.mailer.message_factory')->createMessage(get_class($this), $name, $parameters);

        if ($from) {
            $message->setFrom($from);
        }
        if ($to) {
            $message->setTo($to);
        }

        return $message;
    }

    /**
     * @param \Swift_Mime_Message $message
     */
    protected function send(\Swift_Mime_Message $message)
    {
        $this->getMailer()->send($message);
    }

    /**
     * @param object  $entity
     * @param boolean $flush
     */
    protected function persist($entity, $flush = false)
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->flush($entity);
        }
    }

    /**
     * @param object  $entity
     * @param boolean $flush
     */
    protected function remove($entity, $flush = false)
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->flush();
        }
    }

    /**
     * @param object|null $entity
     */
    protected function flush($entity = null)
    {
        $this->getEntityManager()->flush($entity);
    }

    /**
     * @param EntityRepository|object $entity
     * @param mixed                   $criterias
     * 
     * @return array|object|null
     */
    protected function findOr404($entity, $criterias = array())
    {
        $result = null;
        $findMethod = is_scalar($criterias) ? 'find' : 'findOneBy';

        if (is_object($entity) && $entity instanceof EntityRepository) {
            $result = $entity->$findMethod($criterias);
        } elseif (is_object($entity) && $this->getEntityManager()->contains($entity)) {
            $result = $this->getEntityManager()->refresh($entity);
        } elseif (is_string($entity)) {
            $repository = $this->getRepository($entity);
            $result = $repository->$findMethod($criterias);
        }

        if (null !== $result){
            return $result;
        }

        throw $this->createNotFoundException('Resource not found');
    }

    /**
     * @param string      $type
     * @param string      $message
     * @param array       $parameters
     * @param string|null $pluralization
     */
    protected function addFlash($type, $message = null, array $parameters = array(), $pluralization = null)
    {
        $message = $message ?: sprintf('%s.%s', $this->getRequest()->attributes->get('_route'), $type);

        $this->getFlashBag()->add($type, new Flash\Message($message, $parameters, $pluralization));
    }

    /**
     * @param object $object An entity or document object
     * @param \Symfony\Component\Form\FormTypeInterface|string|null $purpose If null, it will try to guess the form
     * @param array $options
     * 
     * @return \Symfony\Component\Form\FormInterface
     */
    protected function createObjectForm($object, $purpose = null, array $options = array())
    {
        return $this->get('knp_rad.form.manager')->createObjectForm($object, $purpose, $options);
    }

    /**
     * @param object $object An entity or document object
     * @param \Symfony\Component\Form\FormTypeInterface|string|null $purpose If null, it will try to guess the form
     * @param array $options
     * 
     * @return \Symfony\Component\Form\FormInterface
     */
    protected function createBoundObjectForm($object, $purpose = null, array $options = array())
    {
        return $this->get('knp_rad.form.manager')->createBoundObjectForm($object, $purpose, $options);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Session\Session
     */
    protected function getSession()
    {
        return $this->get('session');
    }

    /**
     * @return \Swift_Mailer
     */
    protected function getMailer()
    {
        return $this->get('mailer');
    }

    /**
     * @return \Symfony\Component\Security\Core\SecurityContext
     */
    protected function getSecurity()
    {
        return $this->get('security.context');
    }

    /**
     * @return \Doctrine\Common\Persistence\ObjectManager
     */
    protected function getEntityManager()
    {
        return $this->getDoctrine()->getManager();
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Session\Flash\FlashBag
     */
    protected function getFlashBag()
    {
        return $this->getSession()->getFlashBag();
    }

    /**
     * @param string $name Name of the parameter
     * 
     * @return string
     */
    protected function getParameter($name)
    {
        return $this->container->getParameter($name);
    }
}
