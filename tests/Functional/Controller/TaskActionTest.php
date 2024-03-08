<?php

declare(strict_types=1);

namespace App\Tests\Functionnal\Controller;

use App\Entity\Task;
use App\Repository\TaskRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
        $taskDone = $taskRepository->findOneBy(['user' => $userId, 'isDone' => true]);
        $this->client->request(Request::METHOD_GET, "/tasks/{$task->getId()}/toggle");
        self::assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $this->client->followRedirect();

        self::assertResponseIsSuccessful();
        // dd($this->client->getResponse()->getContent());
        self::assertRouteSame('task_list');
        $task = $taskRepository->findOneBy(['id' => $task->getId()]);
        self::assertNotNull($task);

        self::assertTrue($task->isDone());

        $this->client->request(Request::METHOD_GET, "/tasks/{$taskDone->getId()}/toggle");
        self::assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $this->client->followRedirect();

        self::assertResponseIsSuccessful();

        self::assertRouteSame('task_list_done');
        $taskDone = $taskRepository->findOneBy(['id' => $taskDone->getId()]);

        self::assertNotNull($taskDone);
        self::assertFalse($taskDone->isDone());
    }

    public function testDeleteTask(): void
    {
        Assert::isInstanceOf($this->client, KernelBrowser::class);
        $this->client->loginUser($this->userTest);

        $this->client->request(Request::METHOD_GET, '/tasks/11/delete');

        $this->client->followRedirect();

        self::assertResponseIsSuccessful();
        self::assertResponseStatusCodeSame(Response::HTTP_OK);
        self::assertRouteSame('task_list');

        /**
         * @var TaskRepository $taskRepository
         */
        $taskRepository = $this->client->getContainer()->get(TaskRepository::class);

        /** @var Task|null $task */
        $task = $taskRepository->findOneBy(['id' => '11']);

        self::assertNull($task);
    }

    public function testEditTask(): void
    {
        $this->client->loginUser($this->userTest);

        $this->client->request(Request::METHOD_GET, '/tasks/11/edit');
        self::assertResponseStatusCodeSame(Response::HTTP_OK);

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
        $task = $taskRepository->find('11');

        self::assertNotNull($task);
        self::assertSame('Task modif', $task->getTitle());
        self::assertSame('test Task mod content', $task->getContent());
    }
}
