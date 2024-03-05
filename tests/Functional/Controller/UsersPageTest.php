<?php 

declare(strict_types=1);

namespace App\Tests\Functionnal\Controller;



use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\HttpKernel\Profiler\Profile;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\SecurityBundle\DataCollector\SecurityDataCollector;

class UsersPageTest extends WebTestCase
{
    private KernelBrowser|null $client = null;


    public function setUp() : void
 
  {
    $this->client = static::createClient();
  }
    
    
    public function testShowCreateUserPage():void
    {
        // $this->client = self::createClient();
        
        $this->client->request(Request::METHOD_GET, '/users/create');

        self::assertResponseStatusCodeSame(Response::HTTP_OK);


    }

    public function testShowEditUserPage():void
    {
        // $this->client = self::createClient();

        $this->client->request(Request::METHOD_GET, '/users/1/edit');

        self::assertResponseStatusCodeSame(Response::HTTP_OK);


    }
}