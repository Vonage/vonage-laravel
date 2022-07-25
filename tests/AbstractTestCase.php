<?php

namespace Vonage\Laravel\Tests;

use Orchestra\Testbench\TestCase;
use Vonage\Laravel\VonageServiceProvider;
use Vonage\Client;

abstract class AbstractTestCase extends TestCase
{
    /**
     * Get package providers.
     *
     * @param  \Illuminate\Foundation\Application $app
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            VonageServiceProvider::class,
        ];
    }

    /**
     * Get package aliases.
     *
     * @param  \Illuminate\Foundation\Application $app
     *
     * @return array
     */
    protected function getPackageAliases($app)
    {
        return [
            'Vonage' => \Vonage\Laravel\Facade\Vonage::class,
        ];
    }

    /**
     * Gets the property of an object of a class.
     *
     * @param string $class
     * @param string $property
     * @param mixed  $object
     *
     * @return mixed
     */
    public function getClassProperty($class, $property, $object)
    {
        $reflectionClass = new \ReflectionClass($class);
        $refProperty = $reflectionClass->getProperty($property);
        $refProperty->setAccessible(true);

        return $refProperty->getValue($object);
    }

    /**
     * Returns a list of classes we should attempt to create
     */
    public function classNameProvider(): array
    {
        return [
            [Client::class],
        ];
    }
}