<?php 

declare(strict_types=1);

namespace App\Tests\Functionnal;

use Generator;


use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\HttpKernel\Profiler\Profile;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\SecurityBundle\DataCollector\SecurityDataCollector;

class loginTest extends WebTestCase
{

    private KernelBrowser|null $client = null;

    public function setUp() : void
 
  {
    $this->client = static::createClient();
  }
    
    
    public function testShouldAuthenticate():void
    {
        // $client = self::createClient();
        
        $this->client->request(Request::METHOD_GET, '/login');

        $this->client->submitForm('Se connecter', self::createFormData());

        self::assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $this->client->enableProfiler();

        if (($profile = $this->client->getProfile()) instanceof Profile) {
            /** @var SecurityDataCollector $securityCollector */
            $securityCollector = $profile->getCollector('security');
            self::assertTrue($securityCollector->isAuthenticated());
        }

        $this->client->followRedirect();

        self::assertResponseIsSuccessful();
        // dd($client->getResponse()->getContent());
        self::assertRouteSame('homepage');

    }

    public function provideBadData(): Generator
    {
        yield 'bad username' => [self::createFormData(['_username' => 'fail'])];
        yield 'bad password' => [self::createFormData(['_password' => 'fail'])];
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
      /**
       * Undocumented function
       * @dataProvider provideBadData
       * @param array<string,string> $formData
       * @return void
       */
    public function testShouldShowErrors(array $formData):void
    {
        // $client = self::createClient();
        $this->client->request(Request::METHOD_GET, '/login');

        $this->client->submitForm('Se connecter',$formData);

        self::assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $this->client->enableProfiler();

        if (($profile = $this->client->getProfile()) instanceof Profile) {
            /** @var SecurityDataCollector $securityCollector */
            $securityCollector = $profile->getCollector('security');
        
            self::assertFalse($securityCollector->isAuthenticated());
        }

        $this->client->followRedirect();

        self::assertResponseIsSuccessful();
        // dd($client->getResponse()->getContent());
        self::assertRouteSame('login');

    }

    public function testLogoutPageWithUserConnected(): void
    {
        // $client = self::createClient();
        
        $this->client->request(Request::METHOD_GET, '/login');

        $this->client->submitForm('Se connecter', self::createFormData());

        self::assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $this->client->enableProfiler();

        if (($profile = $this->client->getProfile()) instanceof Profile) {
            /** @var SecurityDataCollector $securityCollector */
            $securityCollector = $profile->getCollector('security');
            self::assertTrue($securityCollector->isAuthenticated());
        }

        $this->client->followRedirect();

        self::assertResponseIsSuccessful();
        // dd($client->getResponse()->getContent());
        self::assertRouteSame('homepage');

        $this->client->request(Request::METHOD_GET, '/logout');

        $this->client->followRedirect();

        self::assertRouteSame('homepage');
    }
    
    
    public function testLogout(): void
    {
        // $client = self::createClient();
        
        $this->client->request(Request::METHOD_GET, '/login');

        $this->client->submitForm('Se connecter', self::createFormData());

        self::assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $this->client->enableProfiler();

        if (($profile = $this->client->getProfile()) instanceof Profile) {
            /** @var SecurityDataCollector $securityCollector */
            $securityCollector = $profile->getCollector('security');
            self::assertTrue($securityCollector->isAuthenticated());
        }

        $this->client->followRedirect();

        self::assertResponseIsSuccessful();
        // dd($client->getResponse()->getContent());
        self::assertRouteSame('homepage');

        $this->client->clickLink('Se déconnecter');

        $this->client->followRedirect();


        self::assertRouteSame('homepage');
    }
}