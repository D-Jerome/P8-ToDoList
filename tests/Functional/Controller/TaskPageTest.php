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

    private KernelBrowser|null $client = null;

    public function setUp() : void
 
  {
    $this->client = static::createClient();
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
            '_username' => 'test',
            '_password' => 'password'
        ];
    }

    public function testShowTaskListToDo(): void
    {
        // $this->client = self::createClient();
        $this->client->request(Request::METHOD_GET, '/login');

        $this->client->submitForm('Se connecter', self::createFormData());

        $this->client->enableProfiler();

        if (($profile = $this->client->getProfile()) instanceof Profile) {
            /** @var SecurityDataCollector $securityCollector */
            $securityCollector = $profile->getCollector('security');
            self::assertTrue($securityCollector->isAuthenticated());
        }

        $this->client->followRedirect();
        self::assertResponseIsSuccessful();
        // dd($this->client->getResponse()->getContent());
        self::assertRouteSame('homepage');

        $this->client->clickLink('Consulter la liste des tâches à faire');

        self::assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testShowTaskListClosed(): void
    {
        // $this->client = self::createClient();
        $this->client->request(Request::METHOD_GET, '/login');

        $this->client->submitForm('Se connecter', self::createFormData());

        $this->client->enableProfiler();

        if (($profile = $this->client->getProfile()) instanceof Profile) {
            /** @var SecurityDataCollector $securityCollector */
            $securityCollector = $profile->getCollector('security');
            self::assertTrue($securityCollector->isAuthenticated());
        }

        $this->client->followRedirect();
        self::assertResponseIsSuccessful();
        // dd($this->client->getResponse()->getContent());
        self::assertRouteSame('homepage');

        $this->client->clickLink('Consulter la liste des tâches terminées');

        self::assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testShowTaskToCreate(): void
    {
        // $this->client = self::createClient();
        $this->client->request(Request::METHOD_GET, '/login');

        $this->client->submitForm('Se connecter', self::createFormData());

        $this->client->enableProfiler();

        if (($profile = $this->client->getProfile()) instanceof Profile) {
            /** @var SecurityDataCollector $securityCollector */
            $securityCollector = $profile->getCollector('security');
            self::assertTrue($securityCollector->isAuthenticated());
        }

        $this->client->followRedirect();
        self::assertResponseIsSuccessful();
        // dd($this->client->getResponse()->getContent());
        self::assertRouteSame('homepage');

        $this->client->clickLink('Créer une nouvelle tâche');

        self::assertResponseStatusCodeSame(Response::HTTP_OK);
    }
}