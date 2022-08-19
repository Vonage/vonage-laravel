<?php

namespace Vonage\Laravel;

use Vonage\Client;
use Illuminate\Support\Str;
use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Config\Repository as Config;

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
    public function boot()
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
    public function register()
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
     *
     * @param Config $config
     *
     * @return Client
     *
     * @throws \RuntimeException
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
                $this->config['application_id']);
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

    /**
     * Checks if Vonage config does not
     * have a value for the given key.
     *
     * @param string $key
     *
     * @return bool
     */
    protected function vonageConfigHasNo($key): bool
    {
        return ! $this->vonageConfigHas($key);
    }

    /**
     * Checks if Vonage config has value for the
     * given key.
     *
     * @param string $key
     *
     * @return bool
     */
    protected function vonageConfigHas($key): bool
    {
        if (!array_key_exists($key, $this->config)) {
            return false;
        }

        return ($this->config[$key]);
    }

    /**
     * Create a Basic credentials for client.
     *
     * @param string $key
     * @param string $secret
     *
     * @return Client\Credentials\Basic
     */
    protected function createBasicCredentials($key, $secret): Client\Credentials\Basic
    {
        return new Client\Credentials\Basic($key, $secret);
    }

    /**
     * Create SignatureSecret credentials for client.
     *
     * @param string $key
     * @param string $signatureSecret
     *
     * @return Client\Credentials\SignatureSecret
     */
    protected function createSignatureCredentials($key, $signatureSecret): Client\Credentials\SignatureSecret
    {
        return new Client\Credentials\SignatureSecret($key, $signatureSecret);
    }

    /**
     * Create Keypair credentials for client.
     *
     * @param string $key
     * @param string $applicationId
     *
     * @return Client\Credentials\Keypair
     */
    protected function createPrivateKeyCredentials($key, $applicationId): Client\Credentials\Keypair
    {
        return new Client\Credentials\Keypair($this->loadPrivateKey($key), $applicationId);
    }

    /**
     * Load private key contents from root directory
     */
    protected function loadPrivateKey($key)
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

    /**
     * Raises Runtime exception.
     *
     * @param string $message
     *
     * @throws \RuntimeException
     */
    protected function raiseRunTimeException($message)
    {
        throw new \RuntimeException($message);
    }
}
