<?php

namespace App\Repository;

use App\Entity\Task;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityRepository;

class TaskRepository extends ServiceEntityRepository
{
        public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Task::class);
    }

}
