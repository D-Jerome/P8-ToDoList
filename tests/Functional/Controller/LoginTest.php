<?php

declare(strict_types=1);

namespace App\Tests\Functionnal;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\SecurityBundle\DataCollector\SecurityDataCollector;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Profiler\Profile;
use Webmozart\Assert\Assert;

/**
 * @internal
 * @coversNothing
 */
final class LoginTest extends WebTestCase
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

    public function testShouldAuthenticate(): void
    {
        // $client = self::createClient();
        Assert::isInstanceOf($this->client, KernelBrowser::class);
        $this->client->request(Request::METHOD_GET, '/login');

        $this->client->submitForm('Se connecter', self::createFormDataUser());

        self::assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $this->client->enableProfiler();

        if (($profile = $this->client->getProfile()) instanceof Profile) {
            /** @var SecurityDataCollector $securityCollector */
            $securityCollector = $profile->getCollector('security');
            self::assertTrue($securityCollector->isAuthenticated());
        }

        $this->client->followRedirect();

        self::assertResponseIsSuccessful();
        // dd($this->client->getResponse()->getContent());
        self::assertRouteSame('homepage');
    }

    public static function provideShouldShowErrorsCases(): iterable
    {
        yield 'bad username' => [self::createFormDataUser(['_username' => 'fail'])];
        yield 'bad password' => [self::createFormDataUser(['_password' => 'fail'])];
    }

    /**
     * login Data
     *
     * @param  array<string,string> $overrideData
     * @return array<string,string>
     */
    private static function createFormDataUser(array $overrideData = []): array
    {
        return $overrideData + [
            '_username' => 'test',
            '_password' => 'password',
        ];
    }

    /**
     * login Data
     *
     * @param  array<string,string> $overrideData
     * @return array<string,string>
     */
    private static function createFormDataAdmin(array $overrideData = []): array
    {
        return $overrideData + [
            '_username' => 'admin',
            '_password' => 'password',
        ];
    }

    /**
     * Undocumented function
     * @dataProvider provideShouldShowErrorsCases
     * @param array<string,string> $formData
     */
    public function testShouldShowErrors(array $formData): void
    {
        Assert::isInstanceOf($this->client, KernelBrowser::class);
        $this->client->request(Request::METHOD_GET, '/login');

        $this->client->submitForm('Se connecter', $formData);

        self::assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $this->client->enableProfiler();

        if (($profile = $this->client->getProfile()) instanceof Profile) {
            /** @var SecurityDataCollector $securityCollector */
            $securityCollector = $profile->getCollector('security');

            self::assertFalse($securityCollector->isAuthenticated());
        }

        $this->client->followRedirect();

        self::assertResponseIsSuccessful();
        // dd($client->getResponse()->getContent());
        self::assertRouteSame('login');
    }

    public function testLogoutPageWithUserConnected(): void
    {
        Assert::isInstanceOf($this->client, KernelBrowser::class);

        $this->client->request(Request::METHOD_GET, '/login');

        $this->client->submitForm('Se connecter', self::createFormDataUser());

        self::assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $this->client->enableProfiler();

        if (($profile = $this->client->getProfile()) instanceof Profile) {
            /** @var SecurityDataCollector $securityCollector */
            $securityCollector = $profile->getCollector('security');
            self::assertTrue($securityCollector->isAuthenticated());
        }

        $this->client->followRedirect();

        self::assertResponseIsSuccessful();
        // dd($client->getResponse()->getContent());
        self::assertRouteSame('homepage');

        $this->client->request(Request::METHOD_GET, '/logout');
        self::assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $this->client->followRedirect();
        self::assertRouteSame('homepage');
    }

    public function testLogout(): void
    {
        Assert::isInstanceOf($this->client, KernelBrowser::class);
        $this->client->loginUser($this->userTest);
        $this->client->request(Request::METHOD_GET, '/');

        self::assertResponseStatusCodeSame(Response::HTTP_OK);

        self::assertRouteSame('homepage');

        $this->client->clickLink('Se déconnecter');
        self::assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $this->client->followRedirect();

        self::assertRouteSame('homepage');
    }
}
