<?php 

namespace App\Tests\Functionnal\Controller;

use Generator;
use App\Entity\User;
use App\Repository\UserRepository;


use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;


class UserCreateTest extends WebTestCase
{
    
    public function provideBadData(): Generator
    {
        yield 'empty username' => [self::createFormData(['user[username]' => ''] )];
        yield 'empty password' => [self::createFormData(['user[password]' => ''] )];
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
        $client = self::createClient();

        $client->request(Request::METHOD_GET, '/login');

        self::assertResponseStatusCodeSame(Response::HTTP_OK);

        $client->clickLink('Créer un utilisateur');

         self::assertResponseStatusCodeSame(Response::HTTP_OK);

    }

    public function testCreateUser():void
    {
        $client = self::createClient();

        $client->request(Request::METHOD_GET, '/users/create');

        self::assertResponseStatusCodeSame(Response::HTTP_OK);

        
        $client->submitForm('Ajouter', self::createFormData());
        
        self::assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $client->followRedirect();

        self::assertResponseIsSuccessful();
        // dd($client->getResponse()->getContent());
        self::assertRouteSame('user_list');

        /**
         * @var UserRepository $userRepository
         */
        $userRepository = $client->getContainer()->get(UserRepository::class);
        
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
        $client = self::createClient();

        $client->request(Request::METHOD_GET, '/users/create');

        self::assertResponseStatusCodeSame(Response::HTTP_OK);

        $client->submitForm('Ajouter', $formData);

        self::assertAnySelectorTextContains()


    }
}