<?php

namespace Vonage\Laravel\Tests;

use Illuminate\Foundation\Application;
use Orchestra\Testbench\TestCase;
use Vonage\Laravel\VonageServiceProvider;
use Vonage\Client;

abstract class AbstractTestCase extends TestCase
{
    /**
     * Get package providers.
     *
     * @param  Application $app
     *
     * @return array
     */
    protected function getPackageProviders($app): array
    {
        return [
            VonageServiceProvider::class,
        ];
    }

    /**
     * Get package aliases.
     *
     * @param  Application $app
     *
     * @return array
     */
    protected function getPackageAliases($app): array
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
     * @param mixed $object
     *
     * @return mixed
     * @throws \ReflectionException
     */
    public function getClassProperty(string $class, string $property, mixed $object): mixed
    {
        $reflectionClass = new \ReflectionClass($class);
        $refProperty = $reflectionClass->getProperty($property);
        $refProperty->setAccessible(true);

        return $refProperty->getValue($object);
    }

    /**
     * Returns a list of classes we should attempt to create
     */
    public static function classNameProvider(): array
    {
        return [
            [Client::class],
        ];
    }
}