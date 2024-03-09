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

final class TaskCreateTest extends WebTestCase
{
    private null | KernelBrowser $client = null;
    private $userTest;


    protected function setUp(): void
    {
        $this->client = self::createClient();

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
            'task[title]'   => 'Task 1',
            'task[content]' => 'test Task 1 content',
        ];
    }

    public function testCreateTask(): void
    {
        Assert::isInstanceOf($this->client, KernelBrowser::class);
        $this->client->loginUser($this->userTest);

        $this->client->request(Request::METHOD_GET, '/');

        $this->client->clickLink('Consulter la liste des tâches à faire');
        self::assertResponseStatusCodeSame(Response::HTTP_OK);
        // dd($this->client->getResponse()->getContent());
        $this->client->clickLink('Créer une tâche');

        self::assertResponseStatusCodeSame(Response::HTTP_OK);

        $this->client->submitForm('Ajouter', self::createFormData());
        // dd($this->client->getResponse()->getContent());
        self::assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $this->client->followRedirect();

        $this->assertSelectorTextContains('div.alert.alert-success', 'Superbe !');

        self::assertRouteSame('task_list');

        /**
         * @var TaskRepository $taskRepository
         */
        $taskRepository = $this->client->getContainer()->get(TaskRepository::class);

        $nbtask = $taskRepository->count();

        /** @var Task|null $task */
        $task = $taskRepository->findOneBy(['id' => $nbtask]);

        self::assertNotNull($task);
        self::assertSame('Task 1', $task->getTitle());
        self::assertSame('test Task 1 content', $task->getContent());
        self::assertNotNull($task->getCreatedAt());
    }
}
