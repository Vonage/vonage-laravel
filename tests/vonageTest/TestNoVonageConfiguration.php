<?php

namespace Vonage\Laravel\Tests\vonageTest;

use Illuminate\Foundation\Application;
use PHPUnit\Framework\Attributes\DataProvider;
use Vonage\Laravel\Tests\AbstractTestCase;

class TestNoVonageConfiguration extends AbstractTestCase
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
    }

    /**
     * Test that when we do not supply Vonage configuration
     * a Runtime exception is generated under the Vonage namespace.
     *
     * @param $className
     *
     * @return void
     */
    #[DataProvider('classNameProvider')]
    public function testWhenNoConfigurationIsGivenExceptionIsRaised($className): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Please provide Vonage API credentials. Possible combinations: api_key + api_secret, api_key + signature_secret, private_key + application_id, api_key + api_secret + private_key + application_id, api_key + signature_secret + private_key + application_id');

        app($className);
    }
}
