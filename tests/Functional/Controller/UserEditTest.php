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

    protected function setUp(): void
    {
        $this->client = self::createClient();
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
            'user[password][first]'  => 'password',
            'user[password][second]' => 'password',
            'user[email]'            => 'testo@test.email',
        ];
    }

    public function testShowUserEditPageFromUsers(): void
    {
        Assert::isInstanceOf($this->client, KernelBrowser::class);

        $this->client->request(Request::METHOD_GET, '/users');

        self::assertResponseStatusCodeSame(Response::HTTP_OK);

        $this->client->clickLink('Edit');

        self::assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testEditUser(): void
    {
        Assert::isInstanceOf($this->client, KernelBrowser::class);

        $this->client->request(Request::METHOD_GET, '/users/1/edit');

        self::assertResponseStatusCodeSame(Response::HTTP_OK);

        $this->client->submitForm('Modifier', self::createFormData());

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
        $user = $userRepository->find('1');
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

        $this->client->request(Request::METHOD_GET, '/users/1/edit');

        self::assertResponseStatusCodeSame(Response::HTTP_OK);

        $this->client->submitForm('Modifier', $formData);

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
