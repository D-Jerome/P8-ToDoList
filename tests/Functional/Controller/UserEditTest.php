<?php 

namespace App\Tests\Functionnal\Controller;

use Generator;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;


class UserEditTest extends WebTestCase
{
    
    public function provideBadData(): Generator
    {
        yield 'empty username' => [self::createFormData(['user[username]' => ''] )];
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
            'user[username]' => 'testo',
            'user[password][first]' => 'password',
            'user[password][second]' => 'password',
            'user[email]' => 'testo@test.email'
        ];
    }
    
    
    public function testShowUserEditPageFromUsers():void
    {
        $client = self::createClient();

        $client->request(Request::METHOD_GET, '/users');

        self::assertResponseStatusCodeSame(Response::HTTP_OK);

        $client->clickLink('Edit');

         self::assertResponseStatusCodeSame(Response::HTTP_OK);

        

    }

    public function testEditUser():void
    {
        $client = self::createClient();

        $client->request(Request::METHOD_GET, '/users/1/edit');

        self::assertResponseStatusCodeSame(Response::HTTP_OK);


        $client->submitForm('Modifier', self::createFormData());

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
        $user = $userRepository->find('1');
        self::assertNotNull($user);
        self::assertEquals('testo', $user->getUsername());
        self::assertEquals('testo@test.email', $user->getEmail());
        self::assertNotNull($user->getPassword());
    }

    /**
     * Undocumented function
     * @dataProvider provideBadData
     * @param array<string,string> $formData
     * @return void
     */
    public function testUpdateUserWithErrors(array $formData):void
    {
        $client = self::createClient();

        $client->request(Request::METHOD_GET, '/users/1/edit');

        self::assertResponseStatusCodeSame(Response::HTTP_OK);

        $client->submitForm('Modifier', $formData);

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