<?php 

declare(strict_types=1);

namespace App\Tests\Functionnal\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;


class LoginPageTest extends WebTestCase
{
    private KernelBrowser|null $client = null;

    public function setUp() : void
 
  {
    $this->client = static::createClient();
  }
 
    public function testShowLoginPage():void
    {
        $this->client->request(Request::METHOD_GET, '/login');

        self::assertResponseStatusCodeSame(Response::HTTP_OK);
    }

}