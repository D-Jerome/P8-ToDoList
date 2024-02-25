<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityRepository;

class UserRepository extends ServiceEntityRepository
{
        public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

  
}
