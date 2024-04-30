<?php

namespace Vonage\Laravel\Tests\vonageTest;

use Illuminate\Foundation\Application;
use Vonage\Client;
use Vonage\Laravel\Tests\AbstractTestCase;

class TestClientSignatureAPICredentials extends AbstractTestCase
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
        $app['config']->set('vonage.api_key', 'my_api_key');
        $app['config']->set('vonage.signature_secret', 'my_signature');
    }

    /**
     * Test that our Vonage client is created with
     * the signature credentials
     *
     * @dataProvider classNameProvider
     *
     * @param $className
     *
     * @return void
     */
    public function testClientCreatedWithSignatureAPICredentials($className): void
    {
        $client = app($className);

        $credentialsObject = $this->getClassProperty(Client::class, 'credentials', $client);
        $credentialsArray = $this->getClassProperty(Client\Credentials\SignatureSecret::class, 'credentials', $credentialsObject);

        $this->assertInstanceOf(Client\Credentials\SignatureSecret::class, $credentialsObject);
        $this->assertEquals(['api_key' => 'my_api_key', 'signature_secret' => 'my_signature', 'signature_method' => 'md5hash'], $credentialsArray);
    }
}
