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

class TaskActionTest extends WebTestCase
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
            'task[title]' => 'Task modif',
            'task[content]' => 'test Task mod content'
        ];
    }

    public function testToggleTask(): void
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
        $client->request(Request::METHOD_GET, '/tasks/1/toggle');

        $client->followRedirect();

        self::assertResponseIsSuccessful();
         
        self::assertRouteSame('task_list');

         /**
         * @var TaskRepository $taskRepository
         */
        $taskRepository = $client->getContainer()->get(TaskRepository::class);
        
        /** @var Task|null $task */
        $task = $taskRepository->find('1');

        self::assertNotNull($task);
        self::assertEquals(false, $task->isDone());


        $client->request(Request::METHOD_GET, '/tasks/1/toggle');

        $client->followRedirect();

        self::assertResponseIsSuccessful();
         
        self::assertRouteSame('task_list');

        /**
         * @var TaskRepository $taskRepository
         */
        $taskRepository = $client->getContainer()->get(TaskRepository::class);
        
        /** @var Task|null $task */
        $task = $taskRepository->find('1');

        self::assertNotNull($task);
        self::assertEquals(true, $task->isDone());
    }

    public function testDeleteTask(): void
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
        $client->request(Request::METHOD_GET, '/tasks/1/delete');

        $client->followRedirect();

        self::assertResponseIsSuccessful();
         
        self::assertRouteSame('task_list');

         /**
         * @var TaskRepository $taskRepository
         */
        $taskRepository = $client->getContainer()->get(TaskRepository::class);
        
        /** @var Task|null $task */
        $task = $taskRepository->find('1');

        self::assertNull($task);
    
    }

    public function testEditTask(): void
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
        $client->request(Request::METHOD_GET, '/tasks/1/edit');

        self::assertResponseIsSuccessful();
         
        self::assertRouteSame('task_edit');

        $client->submitForm('Modifier', self::createFormData());

        $client->followRedirect();

        self::assertResponseIsSuccessful();
        // dd($client->getResponse()->getContent());
        self::assertRouteSame('task_list');

        /**
         * @var TaskRepository $taskRepository
         */
        $taskRepository = $client->getContainer()->get(TaskRepository::class);
        
        /** @var Task|null $task */
        $task = $taskRepository->find('1');

        self::assertNotNull($task);
        self::assertEquals('Task modif', $task->getTitle());
        self::assertEquals('test Task mod content', $task->getContent());
    
    }

}