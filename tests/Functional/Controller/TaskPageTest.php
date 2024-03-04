<?php 

namespace App\Tests\Functionnal\Controller;



use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\HttpKernel\Profiler\Profile;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\SecurityBundle\DataCollector\SecurityDataCollector;

class TaskPageTest extends WebTestCase
{

    /**
     * Undocumented function
     *
     * @param array<string,string> $overrideData
     * @return array<string,string>
     */
    private static function createFormData(array $overrideData = []): array
    {
        return $overrideData +[
            '_username' => 'test',
            '_password' => 'password'
        ];
    }

    public function testShowTaskListToDo(): void
    {
        $client = self::createClient();
        $client->request(Request::METHOD_GET, '/login');

        $client->submitForm('Se connecter', self::createFormData());

        $client->enableProfiler();

        if (($profile = $client->getProfile()) instanceof Profile) {
            /** @var SecurityDataCollector $securityCollector */
            $securityCollector = $profile->getCollector('security');
            self::assertTrue($securityCollector->isAuthenticated());
        }

        $client->followRedirect();
        self::assertResponseIsSuccessful();
        // dd($client->getResponse()->getContent());
        self::assertRouteSame('homepage');

        $client->clickLink('Consulter la liste des tâches à faire');

        self::assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testShowTaskListClosed(): void
    {
        $client = self::createClient();
        $client->request(Request::METHOD_GET, '/login');

        $client->submitForm('Se connecter', self::createFormData());

        $client->enableProfiler();

        if (($profile = $client->getProfile()) instanceof Profile) {
            /** @var SecurityDataCollector $securityCollector */
            $securityCollector = $profile->getCollector('security');
            self::assertTrue($securityCollector->isAuthenticated());
        }

        $client->followRedirect();
        self::assertResponseIsSuccessful();
        // dd($client->getResponse()->getContent());
        self::assertRouteSame('homepage');

        $client->clickLink('Consulter la liste des tâches terminées');

        self::assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testShowTaskToCreate(): void
    {
        $client = self::createClient();
        $client->request(Request::METHOD_GET, '/login');

        $client->submitForm('Se connecter', self::createFormData());

        $client->enableProfiler();

        if (($profile = $client->getProfile()) instanceof Profile) {
            /** @var SecurityDataCollector $securityCollector */
            $securityCollector = $profile->getCollector('security');
            self::assertTrue($securityCollector->isAuthenticated());
        }

        $client->followRedirect();
        self::assertResponseIsSuccessful();
        // dd($client->getResponse()->getContent());
        self::assertRouteSame('homepage');

        $client->clickLink('Créer une nouvelle tâche');

        self::assertResponseStatusCodeSame(Response::HTTP_OK);
    }
}