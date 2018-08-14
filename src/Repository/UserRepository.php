<?php

namespace App\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use App\Entity\User;

/**
 * Created by PhpStorm.
 * User: eschlegel
 * Date: 14/08/2018
 * Time: 17:41
 */

class UserRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * return all users with limit
     * @return User[]
     */
    public function findAllWithLimit($offset,$limit, $sort, $filter): array
    {
        $qb = $this->createQueryBuilder('u');

        if ($filter != "") {
            $qb->where('u.firstname LIKE :nameLike')
                ->orWhere('u.lastname LIKE :nameLike')
                ->setParameter('nameLike', '%'.$filter.'%');
        }
        if ($offset != "") {
            $qb->setFirstResult($offset);
        }

        if ($limit != "") {
            $qb->setMaxResults($limit);
        }

        if (in_array($sort, ['asc', 'desc'])) {
            $qb->orderBy('u.id', $sort);
        }

        return $qb->getQuery()->execute();
    }

    /**
     * return a user by email
     * @return User
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findOneByEmail($email)
    {
        $qb = $this->createQueryBuilder('u')
            ->where('u.email = :email')
            ->setParameter('email', $email)
            ->getQuery();

        return $qb->setMaxResults(1)->getOneOrNullResult();
    }

    /**
     * return a user by email
     * @return User
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findOneById($id)
    {
        $qb = $this->createQueryBuilder('u')
            ->where('u.id = :id')
            ->setParameter('id', $id)
            ->getQuery();

        return $qb->setMaxResults(1)->getOneOrNullResult();
    }
}