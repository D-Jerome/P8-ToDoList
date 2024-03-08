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
final class UserEditTest extends WebTestCase
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
    public static function provideUpdateUserWithErrorsCases(): iterable
    {
        yield 'empty username' => [self::createFormData(['user[username]' => ''])];
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
            'user[username]'         => 'testo',
            'user[password][first]'  => '!@#$1234QWERqwer',
            'user[password][second]' => '!@#$1234QWERqwer',
            'user[email]'            => 'testo@test.email',
            'user[roles]'            => 'ROLE_ADMIN',
        ];
    }

    public function testAccessUserEditPageAdmin(): void
    {
        Assert::isInstanceOf($this->client, KernelBrowser::class);
        $this->client->loginUser($this->userAdmin);
        $this->client->request(Request::METHOD_GET, '/users');

        self::assertResponseStatusCodeSame(Response::HTTP_OK);

        self::assertRouteSame('user_list');
        $this->client->clickLink('Edit');

        self::assertResponseStatusCodeSame(Response::HTTP_OK);
        self::assertRouteSame('user_edit');
    }

    public function testEditUser(): void
    {
        Assert::isInstanceOf($this->client, KernelBrowser::class);
        $this->client->loginUser($this->userTest);
        $id = $this->userTest->getId();
        $this->client->request(Request::METHOD_GET, "/users/{$id}/edit");

        self::assertResponseStatusCodeSame(Response::HTTP_OK);

        $this->client->submitForm('Modifier', self::createFormData());

        self::assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $this->client->followRedirect();

        self::assertRouteSame('user_list');

        /**
         * @var UserRepository $userRepository
         */
        $userRepository = $this->client->getContainer()->get(UserRepository::class);

        /** @var User $user */
        $user = $userRepository->findOneBy(['id'=> $id]);
        self::assertNotNull($user);

        self::assertSame('testo', $user->getUsername());
        self::assertSame('testo@test.email', $user->getEmail());
        self::assertNotNull($user->getPassword());
    }

    /**
     * Undocumented function
     * @dataProvider provideUpdateUserWithErrorsCases
     * @param array<string,string> $formData
     */
    public function testUpdateUserWithErrors(array $formData): void
    {
        Assert::isInstanceOf($this->client, KernelBrowser::class);
        $this->client->loginUser($this->userAdmin);
        $id = $this->userAdmin->getId();
        $this->client->request(Request::METHOD_GET, "/users/{$id}/edit");

        self::assertResponseStatusCodeSame(Response::HTTP_OK);

        $this->client->submitForm('Modifier', $formData);

        self::assertResponseStatusCodeSame(Response::HTTP_OK);

        if ('fail.fail.fail' === $formData['user[email]']) {
            self::assertAnySelectorTextContains('ul li', 'Le format');
        } else {
            if ('!@#$1234QWERqwer' !== $formData['user[password][second]']) {
                self::assertAnySelectorTextContains('ul li', 'Les deux mots de passe doivent correspondre.');
            } else {
                self::assertAnySelectorTextContains('ul li', 'Vous devez');
            }
        }
    }
}
