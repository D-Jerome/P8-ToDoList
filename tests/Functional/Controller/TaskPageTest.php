<?php

declare(strict_types=1);

namespace App\Tests\Functionnal\Controller;

use Webmozart\Assert\Assert;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\HttpKernel\Profiler\Profile;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\SecurityBundle\DataCollector\SecurityDataCollector;

/**
 * @internal
 * @coversNothing
 */
final class TaskPageTest extends WebTestCase
{
    private null | KernelBrowser $client = null;

    protected function setUp(): void
    {
        $this->client = self::createClient();
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
            '_username' => 'test',
            '_password' => 'password',
        ];
    }

    public function testShowTaskListToDo(): void
    {
        Assert::isInstanceOf($this->client, KernelBrowser::class);
        $this->client->request(Request::METHOD_GET, '/login');

        $this->client->submitForm('Se connecter', self::createFormData());

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

        $this->client->clickLink('Consulter la liste des tâches à faire');

        self::assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testShowTaskListClosed(): void
    {
        Assert::isInstanceOf($this->client, KernelBrowser::class);
        $this->client->request(Request::METHOD_GET, '/login');

        $this->client->submitForm('Se connecter', self::createFormData());

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

        $this->client->clickLink('Consulter la liste des tâches terminées');

        self::assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testShowTaskToCreate(): void
    {
        Assert::isInstanceOf($this->client, KernelBrowser::class);
        $this->client->request(Request::METHOD_GET, '/login');

        $this->client->submitForm('Se connecter', self::createFormData());

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

        $this->client->clickLink('Créer une nouvelle tâche');

        self::assertResponseStatusCodeSame(Response::HTTP_OK);
    }
}
