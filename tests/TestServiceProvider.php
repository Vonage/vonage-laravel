<?php

namespace Vonage\Laravel\Tests;

use Illuminate\Foundation\Application;

class TestServiceProvider extends AbstractTestCase
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
        $app['config']->set('vonage.api_secret', 'my_secret');
    }

    /**
     * Test that we can create the Vonage client
     * from container binding.
     *
     * @dataProvider classNameProvider
     *
     * @param $className
     *
     * @return void
     */
    public function testClientResolutionFromContainer($className): void
    {
        $client = app($className);

        $this->assertInstanceOf($className, $client);
    }
}