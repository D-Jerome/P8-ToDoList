<?php

declare(strict_types=1);

namespace App\Tests\Functionnal\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Webmozart\Assert\Assert;

/**
 * @internal
 * @coversNothing
 */
final class UserCreateTest extends WebTestCase
{
    private null | KernelBrowser $client = null;
    private $userTest;
    private $userAdmin;

    protected function setUp(): void
    {
        $this->client = self::createClient();
        $userRepository = self::getContainer()->get(UserRepository::class);
        $this->userAdmin = $userRepository->findOneBy(['username' => 'admin']);
        $userRepository = self::getContainer()->get(UserRepository::class);
        $this->userTest = $userRepository->findOneBy(['username' => 'test']);
    }

    /**
     * @return array<int,array<string,string>>
     */
    public static function provideCreateUserWithErrorsCases(): iterable
    {
        yield 'empty username' => [self::createFormData(['user[username]' => ''])];
        yield 'empty password' => [self::createFormData(['user[password][second]' => ''])];
        yield 'empty email' => [self::createFormData(['user[email]' => ''])];
        yield 'bad password' => [self::createFormData(['user[password][second]' => 'fail'])];
        yield 'bad email' => [self::createFormData(['user[email]' => 'fail.fail.fail'])];
    }

    /**
     * Undocumented function
     *
     * @param  array<string,string> $overrideData
     * @return array<string,string>
     */
    private static function createFormData(array $overrideData = []): array
    {
        return $overrideData + [
            'user[username]'         => 'testty',
            'user[password][first]'  => 'password',
            'user[password][second]' => 'password',
            'user[email]'            => 'test@test.email',
        ];
    }

    public function testShowUserCreatePageFromLogin(): void
    {
        Assert::isInstanceOf($this->client, KernelBrowser::class);

        $this->client->request(Request::METHOD_GET, '/login');

        self::assertResponseStatusCodeSame(Response::HTTP_OK);

        $this->client->clickLink('Créer un utilisateur');

        self::assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testCreateUser(): void
    {
        Assert::isInstanceOf($this->client, KernelBrowser::class);
        $this->client->request(Request::METHOD_GET, '/users/create');

        self::assertResponseStatusCodeSame(Response::HTTP_OK);

        $this->client->submitForm('Ajouter', self::createFormData());

        self::assertResponseStatusCodeSame(Response::HTTP_OK);

        // dd($this->client->getResponse()->getContent());
        // self::assertRouteSame('login');

        /**
         * @var UserRepository $userRepository
         */
        $userRepository = $this->client->getContainer()->get(UserRepository::class);

        $nbUser = $userRepository->count();
        /** @var User $user */
        $user = $userRepository->findOneBy(['id' => $nbUser]);
        self::assertNotNull($user);
        self::assertSame('testty', $user->getUsername());
        self::assertSame('test@test.email', $user->getEmail());
        self::assertNotNull($user->getPassword());
    }

    /**
     * Undocumented function
     * @dataProvider provideCreateUserWithErrorsCases
     * @param array<string,string> $formData
     */
    public function testCreateUserWithErrors(array $formData): void
    {
        Assert::isInstanceOf($this->client, KernelBrowser::class);

        $this->client->request(Request::METHOD_GET, '/users/create');

        self::assertResponseStatusCodeSame(Response::HTTP_OK);

        $this->client->submitForm('Ajouter', $formData);

        self::assertResponseStatusCodeSame(Response::HTTP_OK);

        if ('fail.fail.fail' === $formData['user[email]']) {
            self::assertAnySelectorTextContains('ul li', 'Le format');
        } else {
            if ('password' !== $formData['user[password][second]']) {
                self::assertAnySelectorTextContains('ul li', 'Les deux mots de passe doivent correspondre.');
            } else {
                self::assertAnySelectorTextContains('ul li', 'Vous devez');
            }
        }
    }
}
