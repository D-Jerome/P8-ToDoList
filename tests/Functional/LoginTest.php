<?php 

declare(strict_types=1);

namespace App\Tests\Functionnal;

use Generator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Profiler\Profile;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\SecurityBundle\DataCollector\SecurityDataCollector;

class loginTest extends WebTestCase
{
    public function testShouldAuthenticate():void
    {
        $client = self::createClient();

        $client->request(Request::METHOD_GET, '/login');

        $client->submitForm('Se connecter', self::createFormData());

        self::assertResponseStatusCodeSame(Response::HTTP_FOUND);

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
        $client = self::createClient();

        $client->request(Request::METHOD_GET, '/login');

        $client->submitForm('Se connecter',$formData);

        self::assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $client->enableProfiler();

        if (($profile = $client->getProfile()) instanceof Profile) {
            /** @var SecurityDataCollector $securityCollector */
            $securityCollector = $profile->getCollector('security');
        
            self::assertFalse($securityCollector->isAuthenticated());
        }

        $client->followRedirect();

        self::assertResponseIsSuccessful();
        // dd($client->getResponse()->getContent());
        self::assertRouteSame('login');

    }

}