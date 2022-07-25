<?php

namespace Nexmo\Laravel\Tests;

use Vonage\Client;
use Vonage\Laravel\Tests\AbstractTestCase;

class TestServiceProvider extends AbstractTestCase
{
    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application $app
     *
     * @return void
     */
    protected function getEnvironmentSetUp($app): void
    {
        $app['config']->set('vonage.api_key', 'my_api_key');
        $app['config']->set('vonage.api_secret', 'my_secret');
    }

    /**
     * Test that we can create the Vonage client
     * from container binding.
     *
     * @dataProvider classNameProvider
     *
     * @return void
     */
    public function testClientResolutionFromContainer($className): void
    {
        $client = app($className);

        $this->assertInstanceOf($className, $client);
    }
}