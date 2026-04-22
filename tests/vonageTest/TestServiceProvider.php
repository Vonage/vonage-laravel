<?php

namespace Vonage\Laravel\Tests\vonageTest;

use Illuminate\Foundation\Application;
use PHPUnit\Framework\Attributes\DataProvider;
use Vonage\Laravel\Tests\AbstractTestCase;

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
     * @param $className
     *
     * @return void
     */
    #[DataProvider('classNameProvider')]
    public function testClientResolutionFromContainer($className): void
    {
        $client = app($className);

        $this->assertInstanceOf($className, $client);
    }
}