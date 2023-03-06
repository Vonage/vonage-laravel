<?php

namespace Vonage\Laravel;

use Illuminate\Contracts\Container\BindingResolutionException;
use Vonage\Client;
use Illuminate\Support\Str;
use Illuminate\Support\ServiceProvider;
use Vonage\Client\Credentials\Basic;
use Vonage\Client\Credentials\Keypair;
use Vonage\Client\Credentials\SignatureSecret;

class VonageServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected bool $defer = true;

    protected array $config;

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot(): void
    {
        // Config file path.
        $dist = __DIR__ . '/../config/vonage.php';

        // If we're installing in to a Lumen project, config_path
        // won't exist so we can't auto-publish the config
        if (function_exists('config_path')) {
            // Publishes config File.
            $this->publishes([
                $dist => config_path('vonage.php'),
            ]);
        }

        // Merge config.
        $this->mergeConfigFrom($dist, 'vonage');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register(): void
    {
        // Bind Vonage Client in Service Container.
        $this->app->singleton(Client::class, function ($app) {
            return $this->createVonageClient();
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides(): array
    {
        return [
            Client::class,
        ];
    }

    /**
     * Create a new Vonage Client.
     * @return Client
     *
     * @throws BindingResolutionException
     */
    protected function createVonageClient(): Client
    {
        $this->config = config('vonage');

        // Check for Vonage config file.
        if (!$this->config) {
            $this->raiseRunTimeException('Missing Vonage configuration section.');
        }

        // Get Client Options.
        $options = array_diff_key($this->config,
            ['private_key', 'application_id', 'api_key', 'api_secret', 'shared_secret', 'app']);

        // Do we have a private key?
        $privateKeyCredentials = null;
        if ($this->vonageConfigHas('private_key')) {
            if ($this->vonageConfigHasNo('application_id')) {
                $this->raiseRunTimeException('You must provide vonage.application_id when using a private key');
            }

            $privateKeyCredentials = $this->createPrivateKeyCredentials($this->config['private_key'],
                $this->config['application_id']);
        }

        $basicCredentials = null;
        if ($this->vonageConfigHas('api_secret')) {
            $basicCredentials = $this->createBasicCredentials($this->config['api_key'],
                $this->config['api_secret']);
        }

        $signatureCredentials = null;
        if ($this->vonageConfigHas('signature_secret')) {
            $signatureCredentials = $this->createSignatureCredentials($this->config['api_key'],
                $this->config['signature_secret']);
        }

        // We can have basic only, signature only, private key only or
        // we can have private key + basic/signature, so let's work out
        // what's been provided
        // @TODO this does not handle private and signature and nor does the SDK
        if ($basicCredentials && $signatureCredentials) {
            $this->raiseRunTimeException('Provide either vonage.api_secret or vonage.signature_secret');
        }

        if ($privateKeyCredentials && $basicCredentials) {
            $credentials = new Client\Credentials\Container(
                $privateKeyCredentials,
                $basicCredentials
            );
        } elseif ($privateKeyCredentials && $signatureCredentials) {
            $credentials = new Client\Credentials\Container(
                $privateKeyCredentials,
                $signatureCredentials
            );
        } elseif ($privateKeyCredentials) {
            $credentials = $privateKeyCredentials;
        } elseif ($signatureCredentials) {
            $credentials = $signatureCredentials;
        } elseif ($basicCredentials) {
            $credentials = $basicCredentials;
        } else {
            $possibleVonageKeys = [
                'api_key + api_secret',
                'api_key + signature_secret',
                'private_key + application_id',
                'api_key + api_secret + private_key + application_id',
                'api_key + signature_secret + private_key + application_id',
            ];
            $this->raiseRunTimeException(
                'Please provide Vonage API credentials. Possible combinations: '
                . join(", ", $possibleVonageKeys)
            );
        }

        $httpClient = null;
        if ($this->vonageConfigHas('http_client')) {
            $httpClient = $this->app->make($this->config['http_client']);
        }

        return new Client($credentials, $options, $httpClient);
    }

    protected function vonageConfigHas(string $key): bool
    {
        if (!array_key_exists($key, $this->config) || empty($this->config[$key])) {
            return false;
        }

        return true;
    }

    protected function vonageConfigHasNo(string $key): bool
    {
        return ! $this->vonageConfigHas($key);
    }

    protected function createBasicCredentials(string $key, string $secret): Basic
    {
        return new Basic($key, $secret);
    }

    protected function createSignatureCredentials(string $key, string $signatureSecret): SignatureSecret
    {
        return new SignatureSecret($key, $signatureSecret);
    }

    protected function createPrivateKeyCredentials(string $key, string $applicationId): Keypair
    {
        return new Keypair($this->loadPrivateKey($key), $applicationId);
    }

    /**
     * Load private key contents from root directory
     */
    protected function loadPrivateKey(string $key): bool|string
    {
        if (app()->runningUnitTests()) {
            return '===FAKE-KEY===';
        }

        if (Str::startsWith($key, '-----BEGIN PRIVATE KEY-----')) {
            return $key;
        }

        // If it's a relative path, start searching in the
        // project root
        if ($key[0] !== '/') {
            $key = base_path() . '/' . $key;
        }

        return file_get_contents($key);
    }

    protected function raiseRunTimeException(string $message): void
    {
        throw new \RuntimeException($message);
    }
}
