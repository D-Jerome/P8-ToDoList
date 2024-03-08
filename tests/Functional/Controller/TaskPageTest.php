<?php

declare(strict_types=1);

namespace App\Tests\Functionnal\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class TaskPageTest extends WebTestCase
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

    /**
     * Undocumented function
     *
     * @param  array<string,string> $overrideData
     * @return array<string,string>
     */
    private static function createFormData(array $overrideData = []): array
    {
        return $overrideData + [
            '_username' => 'test',
            '_password' => 'password',
        ];
    }

    public function testShowTaskListToDo(): void
    {
        $this->client->loginUser($this->userTest);

        $this->client->request(Request::METHOD_GET, '/');

        $this->client->clickLink('Consulter la liste des tâches à faire');

        self::assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testShowTaskListClosed(): void
    {
        $this->client->loginUser($this->userTest);
        $this->client->request(Request::METHOD_GET, '/');
        $this->client->clickLink('Consulter la liste des tâches terminées');

        self::assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testShowTaskToCreate(): void
    {
        $this->client->loginUser($this->userTest);
        $this->client->request(Request::METHOD_GET, '/');

        $this->client->clickLink('Créer une nouvelle tâche');

        self::assertResponseStatusCodeSame(Response::HTTP_OK);
    }
}
