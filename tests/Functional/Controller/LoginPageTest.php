<?php 

declare(strict_types=1);

namespace App\Tests\Functionnal\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Profiler\Profile;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\SecurityBundle\DataCollector\SecurityDataCollector;

class LoginPageTest extends WebTestCase
{
    public function testShowLoginPage():void
    {
        $client = self::createClient();

        $client->request(Request::METHOD_GET, '/login');

        self::assertResponseStatusCodeSame(Response::HTTP_OK);
    }

}