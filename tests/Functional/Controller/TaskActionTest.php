<?php

declare(strict_types=1);

namespace App\Tests\Functionnal\Controller;

use App\Entity\Task;
use App\Repository\TaskRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\SecurityBundle\DataCollector\SecurityDataCollector;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Profiler\Profile;
use Webmozart\Assert\Assert;

/**
 * @internal
 * @coversNothing
 */
final class TaskActionTest extends WebTestCase
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
    private static function userFormData(array $overrideData = []): array
    {
        return $overrideData + [
            '_username' => 'test',
            '_password' => 'password',
        ];
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
            'task[title]'   => 'Task modif',
            'task[content]' => 'test Task mod content',
        ];
    }

    public function testToggleTask(): void
    {
        Assert::isInstanceOf($this->client, KernelBrowser::class);
        $this->client->loginUser($this->userTest);
        $this->client->request(Request::METHOD_GET, '/');

        self::assertResponseStatusCodeSame(Response::HTTP_OK);

        $this->client->clickLink('Consulter la liste des tâches à faire');
        self::assertResponseStatusCodeSame(Response::HTTP_OK);

        /**
         * @var TaskRepository $taskRepository
         */
        $taskRepository = $this->client->getContainer()->get(TaskRepository::class);
        $userId = $this->userTest->getId();
        /** @var Task|null $task */
        $task = $taskRepository->findOneBy(['user' => $userId, 'isDone' => false]);

        $this->client->request(Request::METHOD_GET, "/tasks/{$task->getId()}/toggle");
        self::assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $this->client->followRedirect();

        self::assertResponseIsSuccessful();
        // dd($this->client->getResponse()->getContent());
        self::assertRouteSame('task_list');

        self::assertNotNull($task);

        self::assertTrue($task->isDone());

        $this->client->request(Request::METHOD_GET, "/tasks/{$task->getId()}/toggle");
        self::assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $this->client->followRedirect();

        self::assertResponseIsSuccessful();

        self::assertRouteSame('task_list_done');

        self::assertNotNull($task);
        self::assertFalse($task->isDone());
    }

    public function testDeleteTask(): void
    {
        Assert::isInstanceOf($this->client, KernelBrowser::class);
        $this->client->request(Request::METHOD_GET, '/login');

        $this->client->submitForm('Se connecter', self::userFormData());

        $this->client->enableProfiler();

        if (($profile = $this->client->getProfile()) instanceof Profile) {
            /** @var SecurityDataCollector $securityCollector */
            $securityCollector = $profile->getCollector('security');
            self::assertTrue($securityCollector->isAuthenticated());
        }

        $this->client->followRedirect();

        $this->client->clickLink('Consulter la liste des tâches à faire');
        self::assertResponseStatusCodeSame(Response::HTTP_OK);
        // dd($this->client->getResponse()->getContent());
        $this->client->request(Request::METHOD_GET, '/tasks/1/delete');

        $this->client->followRedirect();

        self::assertResponseIsSuccessful();

        self::assertRouteSame('task_list');

        /**
         * @var TaskRepository $taskRepository
         */
        $taskRepository = $this->client->getContainer()->get(TaskRepository::class);

        /** @var Task|null $task */
        $task = $taskRepository->find('1');

        self::assertNull($task);
    }

    public function testEditTask(): void
    {
        Assert::isInstanceOf($this->client, KernelBrowser::class);
        $this->client->request(Request::METHOD_GET, '/login');

        $this->client->submitForm('Se connecter', self::userFormData());

        $this->client->enableProfiler();

        if (($profile = $this->client->getProfile()) instanceof Profile) {
            /** @var SecurityDataCollector $securityCollector */
            $securityCollector = $profile->getCollector('security');
            self::assertTrue($securityCollector->isAuthenticated());
        }

        $this->client->followRedirect();

        $this->client->clickLink('Consulter la liste des tâches à faire');
        self::assertResponseStatusCodeSame(Response::HTTP_OK);
        // dd($this->client->getResponse()->getContent());
        $this->client->request(Request::METHOD_GET, '/tasks/1/edit');

        self::assertResponseIsSuccessful();

        self::assertRouteSame('task_edit');

        $this->client->submitForm('Modifier', self::createFormData());

        $this->client->followRedirect();

        self::assertResponseIsSuccessful();
        // dd($this->client->getResponse()->getContent());
        self::assertRouteSame('task_list');

        /**
         * @var TaskRepository $taskRepository
         */
        $taskRepository = $this->client->getContainer()->get(TaskRepository::class);

        /** @var Task|null $task */
        $task = $taskRepository->find('1');

        self::assertNotNull($task);
        self::assertSame('Task modif', $task->getTitle());
        self::assertSame('test Task mod content', $task->getContent());
    }
}
