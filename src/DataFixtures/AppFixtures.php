<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Task;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {
        for ($i = 1; $i <= 5; ++$i) {
            $task = new Task();
            $task->setTitle("test {$i}");
            $task->setContent("content {$i}");
            $task->toggle(true);
            $manager->persist($task);
        }
        for ($i = 6; $i <= 10; ++$i) {
            $task = new Task();
            $task->setTitle("test {$i}");
            $task->setContent("content {$i}");
            $manager->persist($task);
        }

        $user = new User();
        $user->setUsername('test');
        $user->setPassword($this->hasher->hashPassword($user, 'password'));
        $user->setEmail('test@email.com');
        $manager->persist($user);

        for ($i = 1; $i <= 5; ++$i) {
            $task = new Task();
            $task->setTitle("test {$i}");
            $task->setContent("content {$i}");
            $task->setUser($user);
            $task->toggle(true);
            $manager->persist($task);
        }
        for ($i = 6; $i <= 10; ++$i) {
            $task = new Task();
            $task->setTitle("test {$i}");
            $task->setContent("content {$i}");
            $task->setUser($user);
            $manager->persist($task);
        }

        $user = new User();
        $user->setUsername('admin');
        $user->setPassword($this->hasher->hashPassword($user, 'password'));
        $user->setEmail('admin@email.com');
        $user->setRoles(['ROLE_ADMIN']);
        $manager->persist($user);

        for ($i = 11; $i <= 15; ++$i) {
            $task = new Task();
            $task->setTitle("test {$i}");
            $task->setContent("content {$i}");
            $task->setUser($user);
            $task->toggle(true);
            $manager->persist($task);
        }
        for ($i = 16; $i <= 20; ++$i) {
            $task = new Task();
            $task->setTitle("test {$i}");
            $task->setContent("content {$i}");
            $task->setUser($user);
            $manager->persist($task);
        }
        $manager->flush();
    }
}
