<?php

namespace Knp\RadBundle\tests\fixtures;

use Knp\RadBundle\Doctrine\EntityRepository;

class RADRepository extends EntityRepository
{
    public function buildValid()
    {
        return $this
            ->build()
            ->where($this->getAlias().'.foo = bar')
        ;
    }

    public function buildOneValid($id)
    {
        return $this
            ->buildOne($id)
            ->where($this->getAlias().'.foo = bar')
        ;
    }
}
