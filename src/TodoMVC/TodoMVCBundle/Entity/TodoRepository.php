<?php

namespace TodoMVC\TodoMVCBundle\Entity;

use Doctrine\ORM\EntityRepository;

class TodoRepository extends EntityRepository
{
    public function all($sort = '-createdAt')
    {
        $order = '-' === substr($sort, 0, 1) ? 'DESC' : 'ASC';
        $field = 't.' . ('-' === substr($sort, 0, 1) ? substr($sort, 1) : $sort);

        return $this->createQueryBuilder('t')
            ->orderBy($field, $order)
            ->getQuery()
            ->getResult();
    }
}
