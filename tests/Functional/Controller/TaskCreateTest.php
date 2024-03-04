<?php 

namespace App\Tests\Functionnal\Controller;

use App\Entity\Task;
use App\Repository\TaskRepository;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\HttpKernel\Profiler\Profile;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\SecurityBundle\DataCollector\SecurityDataCollector;

class TaskCreateTest extends WebTestCase
{

    /**
     * Undocumented function
     *
     * @param array<string,string> $overrideData
     * @return array<string,string>
     */
    private static function userFormData(array $overrideData = []): array
    {
        return $overrideData +[
            '_username' => 'test',
            '_password' => 'password'
        ];
    }


    /**
     * Undocumented function
     *
     * @param array<string,string> $overrideData
     * @return array<string,string>
     */
    private static function createFormData(array $overrideData = []): array
    {
        return $overrideData +[
            'task[title]' => 'Task 1',
            'task[content]' => 'test Task 1 content'
        ];
    }

    public function testCreateTask(): void
    {
        $client = self::createClient();
        $client->request(Request::METHOD_GET, '/login');

        $client->submitForm('Se connecter', self::userFormData());

        $client->enableProfiler();

        if (($profile = $client->getProfile()) instanceof Profile) {
            /** @var SecurityDataCollector $securityCollector */
            $securityCollector = $profile->getCollector('security');
            self::assertTrue($securityCollector->isAuthenticated());
        }

        $client->followRedirect();

        $client->clickLink('Consulter la liste des tâches à faire');
        self::assertResponseStatusCodeSame(Response::HTTP_OK);
        // dd($client->getResponse()->getContent());
        $client->clickLink('Créer une tâche');

        self::assertResponseStatusCodeSame(Response::HTTP_OK);

        $client->submitForm('Ajouter', self::createFormData());
        // dd($client->getResponse()->getContent());
        $client->followRedirect();

        self::assertResponseIsSuccessful();
         
        self::assertRouteSame('task_list');

        /**
         * @var TaskRepository $taskRepository
         */
        $taskRepository = $client->getContainer()->get(TaskRepository::class);
        
        /** @var Task|null $task */
        $task = $taskRepository->find('11');

        self::assertNotNull($task);
        self::assertEquals('Task 1', $task->getTitle());
        self::assertEquals('test Task 1 content', $task->getContent());
        self::assertNotNull($task->getCreatedAt());
    }


}