<?php

namespace Vonage\Laravel\Tests\vonageTest;

use Illuminate\Foundation\Application;
use Vonage\Client;
use Vonage\Laravel\Tests\AbstractTestCase;

class TestClientPrivateKeyCredentials extends AbstractTestCase
{
    /**
     * Define environment setup.
     *
     * @param  Application $app
     *
     * @return void
     */
    protected function getEnvironmentSetUp($app): void
    {
        $app['config']->set('vonage.private_key', '/path/to/key');
        $app['config']->set('vonage.application_id', 'application-id-123');
    }

    /**
     * Test that our Vonage client is created with
     * the private key credentials
     *
     * @dataProvider classNameProvider
     *
     * @param $className
     *
     * @return void
     */
    public function testClientCreatedWithPrivateKeyCredentials($className): void
    {
        $client = app($className);
        $credentialsObject = $this->getClassProperty(Client::class, 'credentials', $client);
        $credentialsArray = $this->getClassProperty(Client\Credentials\Keypair::class, 'credentials', $credentialsObject);

        $this->assertInstanceOf(Client\Credentials\Keypair::class, $credentialsObject);
        $this->assertEquals(['key' => '===FAKE-KEY===', 'application' => 'application-id-123'], $credentialsArray);
    }
}
