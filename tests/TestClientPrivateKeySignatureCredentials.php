<?php

namespace Vonage\Laravel\Tests;

use Illuminate\Foundation\Application;
use Vonage\Client;

class TestClientPrivateKeySignatureCredentials extends AbstractTestCase
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
        $app['config']->set('vonage.api_key', 'my_api_key');
        $app['config']->set('vonage.signature_secret', 'my_signature');
    }

    /**
     * Test that our Vonage client is created with
     * a container with private key + signature credentials
     *
     * @dataProvider classNameProvider
     *
     * @param $className
     *
     * @return void
     */
    public function testClientCreatedWithPrivateKeySignatureCredentials($className): void
    {
        $client = app($className);
        $credentialsObject = $this->getClassProperty(Client::class, 'credentials', $client);

        $credentialsArray = $this->getClassProperty(Client\Credentials\Container::class, 'credentials', $credentialsObject);
        $keypairCredentials = $this->getClassProperty(Client\Credentials\Keypair::class, 'credentials', $credentialsArray[Client\Credentials\Keypair::class]);
        $signatureCredentials = $this->getClassProperty(Client\Credentials\SignatureSecret::class, 'credentials', $credentialsArray[Client\Credentials\SignatureSecret::class]);

        $this->assertInstanceOf(Client\Credentials\Container::class, $credentialsObject);
        $this->assertEquals(['key' => '===FAKE-KEY===', 'application' => 'application-id-123'], $keypairCredentials);
        $this->assertEquals(['api_key' => 'my_api_key', 'signature_secret' => 'my_signature', 'signature_method' => 'md5hash'], $signatureCredentials);
    }
}
