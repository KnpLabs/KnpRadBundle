<?php

namespace Knp\RadBundle\Doctrine;

use Doctrine\ORM\EntityRepository as BaseEntityRepository;
use Doctrine\ORM\QueryBuilder;

class EntityRepository extends BaseEntityRepository
{
    public function __call($method, $arguments)
    {
        if (0 === strpos($method, 'find')) {
            if (method_exists($this, $builder = 'build'.substr($method, 4))) {
                $qb = call_user_func_array(array($this, $builder), $arguments);

                return $qb->getQuery()->getResult();
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
        return $this->build();
    }

    protected function getAlias()
    {
        $reflection = new \ReflectionObject($this);

        return strtolower(
            preg_replace(array('/Repository$/', '/[a-z0-9]/'), '', $reflection->getShortName())
        );
    }
}
