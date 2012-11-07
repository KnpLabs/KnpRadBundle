<?php

namespace Knp\RadBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller as BaseController;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;

class Controller extends BaseController
{
    protected function redirectRoute($route, $parameters = array(), $status = 302)
    {
        return $this->redirect($this->generateUrl($route, $parameters), $status);
    }

    protected function createAccessDeniedException($message = 'Access Denied', \Exception $previous = null)
    {
        return new AccessDeniedException($message, $previous);
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

    protected function persist($entity, $flush = false)
    {
        $em = $this->getEntityManager();
        $em->persist($entity);

        if ($flush) {
            $em->flush($entity);
        }
    }

    protected function remove($entity, $flush = false)
    {
        $em = $this->getEntityManager();
        $em->remove($entity);

        if ($flush) {
            $em->flush();
        }
    }

    protected function flush()
    {
        $this->getEntityManager()->flush();
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

    protected function getSession()
    {
        return $this->get('session');
    }

    protected function addFlashf()
    {
        $args    = func_get_args();
        $type    = array_shift($args);
        $message = array_shift($message);

        $this->addFlash($type, sprintf($message, $args));
    }

    protected function addFlash($type, $message)
    {
        $this->getFlashBag()->add($type, $message);
    }

    protected function getFlashBag()
    {
        return $this->getSession()->getFlashBag();
    }

    protected function createObjectForm($object, $purpose = null, array $options = array())
    {
        return $this->get('knp_rad.form.manager')->createObjectForm($object, $purpose, $options);
    }
}
