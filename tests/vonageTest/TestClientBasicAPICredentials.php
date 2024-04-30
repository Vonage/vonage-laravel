<?php

namespace Vonage\Laravel\Tests\vonageTest;

use Illuminate\Foundation\Application;
use Vonage\Client;
use Vonage\Laravel\Tests\AbstractTestCase;

class TestClientBasicAPICredentials extends AbstractTestCase
{
    /**
     * Define environment setup.
     *
     * @param  Application $app
     *
     * @return void
     */
    protected function defineEnvironment($app): void
    {
        $app['config']->set('vonage.api_key', 'my_api_key');
        $app['config']->set('vonage.api_secret', 'my_secret');
    }

    /**
     * Test that our Vonage client is created with
     * the Basic API credentials.
     *
     * @return void
     */
    public function testClientCreatedWithBasicAPICredentials(): void
    {
        $client = app(Client::class);
        $credentialsObject = $this->getClassProperty(Client::class, 'credentials', $client);
        $credentialsArray = $this->getClassProperty(Client\Credentials\Basic::class, 'credentials', $credentialsObject);

        $this->assertInstanceOf(Client\Credentials\Basic::class, $credentialsObject);
        $this->assertEquals(['api_key' => 'my_api_key', 'api_secret' => 'my_secret'], $credentialsArray);
    }
}
