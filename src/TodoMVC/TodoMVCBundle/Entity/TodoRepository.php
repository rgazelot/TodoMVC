<?php

namespace TodoMVC\TodoMVCBundle\Entity;

use Doctrine\ORM\EntityRepository;

class TodoRepository extends EntityRepository
{
    public function all($sort = '-createdAt', $filter = null)
    {
        $order = '-' === substr($sort, 0, 1) ? 'DESC' : 'ASC';
        $field = 't.' . ('-' === substr($sort, 0, 1) ? substr($sort, 1) : $sort);

        $qb = $this->createQueryBuilder('t');

        if (null !== $filter) {
            $value = '-' === substr($filter, 0, 1) ? false : true;
            $field = 't.' . ('-' === substr($filter, 0, 1) ? substr($filter, 1) : $filter);

            $qb->andWhere($qb->expr()->eq($field, ':filterValue'))
                ->setParameter('filterValue', $value);
        }

        return $qb->orderBy($field, $order)
            ->getQuery()
            ->getResult();
    }
}
