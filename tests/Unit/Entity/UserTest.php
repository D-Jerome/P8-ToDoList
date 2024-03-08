<?php

declare(strict_types=1);

namespace App\Tests\Unit\Entity;

use App\Entity\User;
use PHPUnit\Framework\TestCase;

final class UserTest extends TestCase
{
    public function testUsername(): void
    {
        $user = new User();
        $user->setUsername('TestUsername');
        self::assertSame($user->getUsername(), 'TestUsername');
    }

    public function testgetUserIdentifier(): void
    {
        $user = new User();
        $user->setUsername('TestUsername');
        self::assertSame($user->getUsername(), $user->getUserIdentifier());
    }

    public function testPassword(): void
    {
        $user = new User();
        $user->setPassword('TestPassword');
        self::assertSame($user->getPassword(), 'TestPassword');
    }

    public function testEmail(): void
    {
        $user = new User();
        $user->setEmail('TestEmail');
        self::assertSame($user->getEmail(), 'TestEmail');
    }

    public function testIdentifier(): void
    {
        $user = new User();
        $user->setUsername('TestIdentifier');
        self::assertSame($user->getUserIdentifier(), 'TestIdentifier');
    }
}
