<?php

namespace spec\Knp\RadBundle\tests\fixtures;

use PHPSpec2\ObjectBehavior;
use Doctrine\ORM\NonUniqueResultException;

class RADRepository extends ObjectBehavior
{
    /**
     * @param Doctrine\ORM\EntityManager         $em
     * @param Doctrine\ORM\Mapping\ClassMetadata $class
     * @param Doctrine\ORM\QueryBuilder          $qb
     * @param Doctrine\ORM\AbstractQuery         $query
     */
    function let($em, $class, $qb, $query)
    {
        $em->createQueryBuilder()->willReturn($qb);
        $qb->select(ANY_ARGUMENT)->willReturn($qb);
        $qb->from(ANY_ARGUMENTS)->willReturn($qb);
        $qb->where('rad.foo = bar')->willReturn($qb);
        $class->name = 'rad';
        $qb->getQuery()->willReturn($query);

        $this->beConstructedWith($em, $class);
    }

    function it_should_be_a_rad_entity_repository()
    {
        $this->shouldHaveType('Knp\RadBundle\Doctrine\EntityRepository');
    }

    function it_should_find_all_valid_entities($qb, $query)
    {
        $query->getResult()->willReturn(['foo', 'bar']);

        $this->findValid()->shouldReturn(['foo', 'bar']);
    }

    function it_should_find_only_one_result_when_method_contains_One($qb, $query)
    {
        $qb->where('rad.id = 1')->willReturn($qb);
        $query->getOneOrNullResult()->willReturn('foo');
        $query->getResult()->shouldNotBeCalled();

        $this->findOneValid(1)->shouldReturn('foo');
    }

    function it_should_throw_non_unique_result_exception_when_finding_one_entity_and_multiple_results_were_found($qb, $query)
    {
        $qb->where('rad.id = 1')->willReturn($qb);
        $query->getOneOrNullResult()->willThrow(new NonUniqueResultException);

        $this->shouldThrow(new NonUniqueResultException)->duringFindOneValid(1);
    }
}
