<?php

declare(strict_types=1);

namespace App\Tests\Functionnal\Controller;

use Webmozart\Assert\Assert;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * @internal
 * @coversNothing
 */
final class UsersPageTest extends WebTestCase
{
    private null | KernelBrowser $client = null;

    protected function setUp(): void
    {
        $this->client = self::createClient();
    }

    public function testShowCreateUserPage(): void
    {
        Assert::isInstanceOf($this->client, KernelBrowser::class);

        $this->client->request(Request::METHOD_GET, '/users/create');

        self::assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testShowEditUserPage(): void
    {
        Assert::isInstanceOf($this->client, KernelBrowser::class);

        $this->client->request(Request::METHOD_GET, '/users/1/edit');

        self::assertResponseStatusCodeSame(Response::HTTP_OK);
    }
}
