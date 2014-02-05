<?php

namespace Knp\RadBundle\Controller\Helper;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Common\Persistence\ObjectRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Doctrine
{
    private $doctrine;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public function persist($object, $flush = false)
    {
        $this->getManager()->persist($object);

        if ($flush) {
            $this->flush($object);
        }
    }

    public function remove($object, $flush = false)
    {
        $this->getManager()->remove($object);

        if ($flush) {
            $this->flush();
        }
    }

    public function flush($object = null)
    {
        $this->getManager()->flush($object);
    }

    public function findOr404($object, $criterias = array())
    {
        $result = null;
        $findMethod = is_scalar($criterias) ? 'find' : 'findOneBy';

        if (is_object($object) && $object instanceof ObjectRepository) {
            $result = $object->$findMethod($criterias);
        } elseif (is_object($object) && $this->getManager()->contains($object)) {
            $result = $this->getManager()->refresh($object);
        } elseif (is_string($object)) {
            $repository = $this->getRepository($object);
            $result = $repository->$findMethod($criterias);
        }

        if (null !== $result) {
            return $result;
        }

        throw new NotFoundHttpException;
    }

    public function getManager()
    {
        return $this->doctrine->getManager();
    }

    public function getRepository($object)
    {
        return $this->getManager()->getRepository(is_object($object) ? get_class($object) : $object);
    }
}
