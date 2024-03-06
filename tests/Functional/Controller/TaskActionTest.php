<?php

declare(strict_types=1);

namespace App\Tests\Functionnal\Controller;

use App\Entity\Task;
use Webmozart\Assert\Assert;
use App\Repository\TaskRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\HttpKernel\Profiler\Profile;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\SecurityBundle\DataCollector\SecurityDataCollector;

/**
 * @internal
 * @coversNothing
 */
final class TaskActionTest extends WebTestCase
{
    private null | KernelBrowser $client = null;

    protected function setUp(): void
    {
        $this->client = self::createClient();
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
        $this->client->request(Request::METHOD_GET, '/tasks/1/toggle');

        $this->client->followRedirect();

        self::assertResponseIsSuccessful();

        self::assertRouteSame('task_list');

        /**
         * @var TaskRepository $taskRepository
         */
        $taskRepository = $this->client->getContainer()->get(TaskRepository::class);

        /** @var Task|null $task */
        $task = $taskRepository->find('1');

        self::assertNotNull($task);
        self::assertFalse($task->isDone());

        $this->client->request(Request::METHOD_GET, '/tasks/1/toggle');

        $this->client->followRedirect();

        self::assertResponseIsSuccessful();

        self::assertRouteSame('task_list');

        /**
         * @var TaskRepository $taskRepository
         */
        $taskRepository = $this->client->getContainer()->get(TaskRepository::class);

        /** @var Task|null $task */
        $task = $taskRepository->find('1');

        self::assertNotNull($task);
        self::assertTrue($task->isDone());
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
