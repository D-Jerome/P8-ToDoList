<?php 

namespace App\Tests\Functionnal\Controller;

use Generator;
use App\Entity\User;
use App\Repository\UserRepository;


use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;


class UserCreateTest extends WebTestCase
{
    private KernelBrowser|null $client = null;

    public function setUp() : void
    {
        $this->client = static::createClient();
    }
    

    public function provideBadData(): Generator
    {
        yield 'empty username' => [self::createFormData(['user[username]' => ''] )];
        yield 'empty password' => [self::createFormData(['user[password][second]' => ''] )];
        yield 'empty email' => [self::createFormData(['user[email]' => ''] )];
        yield 'bad password' => [self::createFormData(['user[password][second]' => 'fail'])];
        yield 'bad email' => [self::createFormData(['user[email]' => 'fail.fail.fail'])];
    }
    
    /**
     * 
     * Undocumented function
     *
     * @param array<string,string> $overrideData
     * @return array<string,string>
     */
    private static function createFormData(array $overrideData = []): array
    {
        return $overrideData +[
            'user[username]' => 'testty',
            'user[password][first]' => 'password',
            'user[password][second]' => 'password',
            'user[email]' => 'test@test.email'
        ];
    }
    
    
    public function testShowUserCreatePageFromLogin():void
    {
        // $this->client = self::createClient();

        $this->client->request(Request::METHOD_GET, '/login');

        self::assertResponseStatusCodeSame(Response::HTTP_OK);

        $this->client->clickLink('Créer un utilisateur');

         self::assertResponseStatusCodeSame(Response::HTTP_OK);

    }

    public function testCreateUser():void
    {
        // $this->client = self::createClient();

        $this->client->request(Request::METHOD_GET, '/users/create');

        self::assertResponseStatusCodeSame(Response::HTTP_OK);

        
        $this->client->submitForm('Ajouter', self::createFormData());
        
        self::assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $this->client->followRedirect();

        self::assertResponseIsSuccessful();
        // dd($this->client->getResponse()->getContent());
        self::assertRouteSame('user_list');

        /**
         * @var UserRepository $userRepository
         */
        $userRepository = $this->client->getContainer()->get(UserRepository::class);
        
        /** @var User|null $user */
        $user = $userRepository->find('2');
        self::assertNotNull($user);
        self::assertEquals('testty', $user->getUsername());
        self::assertEquals('test@test.email', $user->getEmail());
        self::assertNotNull($user->getPassword());
    }

    /**
     * Undocumented function
     * @dataProvider provideBadData
     * @param array<string,string> $formData
     * @return void
     */
    public function testCreateUserWithErrors(array $formData):void
    {
        // $this->client = self::createClient();

        $this->client->request(Request::METHOD_GET, '/users/create');

        self::assertResponseStatusCodeSame(Response::HTTP_OK);

        $this->client->submitForm('Ajouter', $formData);

        self::assertResponseStatusCodeSame(Response::HTTP_OK);
        
        if ($formData['user[email]'] === 'fail.fail.fail'){
            self::assertAnySelectorTextContains('ul li', 'Le format');
        }else{
            if ($formData['user[password][second]'] !== 'password'){
                self::assertAnySelectorTextContains('ul li', 'Les deux mots de passe doivent correspondre.');
            }else{
                self::assertAnySelectorTextContains('ul li', 'Vous devez');
            }
        }
    }
}