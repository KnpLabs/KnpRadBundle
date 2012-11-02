<?php

namespace Knp\RadBundle\Doctrine;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

abstract class EntityRepository extends EntityRepository
{
    public function __call($method, array $arguments = array())
    {
        if (0 === strpos($method, 'find')) {
            if (method_exists($builder = 'build'.substr($method, 4))) {
                $qb = call_user_func_array(array($this, $builder), $arguments);

                return $qb->getQuery()->getResults();
            }
        }

        return parent::__call($method, $arguments);
    }

    protected function build()
    {
        return $this->createQueryBuilder($this->getAlias());
    }

    protected function buildOne($id)
    {
        return $this->build()->where($this->getAlias().'.id = '.intval($id));
    }

    protected function buildAll()
    {
        return $this->buld();
    }

    protected function getAlias()
    {
        $reflection = new \ReflectionObject($this);

        return preg_replace('/[a-z0-9]/', '', $reflection->getShortName());
    }
}
