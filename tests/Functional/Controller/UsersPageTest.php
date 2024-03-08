<?php

declare(strict_types=1);

namespace App\Tests\Functionnal\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class UsersPageTest extends WebTestCase
{
    private null | KernelBrowser $client = null;
    private $userTest;
    private $userAdmin;

    protected function setUp(): void
    {
        $this->client = self::createClient();
        $userRepository = self::getContainer()->get(UserRepository::class);
        $this->userAdmin = $userRepository->findOneBy(['username' => 'admin']);
        $userRepository = self::getContainer()->get(UserRepository::class);
        $this->userTest = $userRepository->findOneBy(['username' => 'test']);
    }

    public function testShowCreateUserPage(): void
    {
        $this->client->request(Request::METHOD_GET, '/users/create');

        self::assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testShowEditUserPageNotConnected(): void
    {
        $this->client->request(Request::METHOD_GET, '/users/10/edit');

        self::assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function testNoAccessUsersPageNotConnected(): void
    {
        $this->client->request(Request::METHOD_GET, '/users');

        self::assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $this->client->followRedirect();

        self::assertResponseIsSuccessful();

        self::assertRouteSame('login');
    }

    public function testNoAccessUsersPageRoleUser(): void
    {
        $this->client->loginUser($this->userTest);

        $this->client->request(Request::METHOD_GET, '/users');

        self::assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $this->client->followRedirect();

        self::assertResponseIsSuccessful();

        self::assertRouteSame('app_error_page');
    }

    public function testAccessUsersPageRoleAdmin(): void
    {
        // simulate $testUser being logged in
        $this->client->loginUser($this->userAdmin);
        // Assert::isInstanceOf($client, KernelBrowser::class);

        $this->client->request(Request::METHOD_GET, '/users');

        self::assertResponseStatusCodeSame(Response::HTTP_OK);

        self::assertRouteSame('user_list');
    }
}
