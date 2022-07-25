<?php

namespace Vonage\Laravel\Tests;

use Vonage\Client;

class TestNoVonageConfiguration extends AbstractTestCase
{
    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application $app
     *
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('vonage.api_key', 'my_api_key');
    }

    /**
     * Test that when we do not supply Nexmo configuration
     * a Runtime exception is generated under the Vonage namespace.
     *
     * @dataProvider classNameProvider
     *
     * @return void
     */
    public function testWhenNoConfigurationIsGivenExceptionIsRaised($className): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Please provide Vonage API credentials. Possible combinations: api_key + api_secret, api_key + signature_secret, private_key + application_id, api_key + api_secret + private_key + application_id, api_key + signature_secret + private_key + application_id');

        app($className);
    }
}
