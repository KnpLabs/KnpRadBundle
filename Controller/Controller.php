<?php

namespace Knp\RadBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller as BaseController;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class Controller extends BaseController
{

    public function redirectRoute($route, $parameters = array(), $status = 302)
    {

        return $this->redirect($this->generateUrl($route, $parameters), $status);

    }

    public function createAccessDeniedException($message = 'Access Denied', \Exception $previous = null)
    {

        return new AccessDeniedException($message, $previous);

    }

    protected function getEntityManager()
    {

        return $this->getDoctrine()->getEntityManager();

    }

    protected function getRepository($entity)
    {

        return is_object($entity)
            ? $this->getEntityManager()->getRepository(get_class($entity))
            : $this->getEntityManager()->getRepository($entity)
        ;

    }

    public function persistAndFlush($entity)
    {
        $em = $this->getEntityManager();
        $em->persist($entity);
        $em->flush();
    }

    public function removeAndFlush($entity)
    {
        $em = $this->getEntityManager();
        $em->remove($entity);
        $em->flush();
    }

    protected function findEntityOr404($entity, $criterias = array()) {

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

    public function getSession($name, $default = null)
    {
        return $this->get('session')->get($name, $default);
    }

    public function setSession($name, $value)
    {
        $this->get('session')->set($name, $value);
    }

    public function setFlash($type, $message)
    {
        $this->get('session')->setFlash($type, $message);
    }

}
