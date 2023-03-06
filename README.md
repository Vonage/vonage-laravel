![The Vonage logo](./vonage_logo.png)
![The Laravel logo](https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg)

# Vonage Package for Laravel
[![Latest Stable Version](http://poser.pugx.org/vonage/vonage-laravel/v)](https://packagist.org/packages/vonage/vonage-laravel)
[![Total Downloads](http://poser.pugx.org/vonage/vonage-laravel/downloads)](https://packagist.org/packages/vonage/vonage-laravel)
[![License](http://poser.pugx.org/vonage/vonage-laravel/license)](https://packagist.org/packages/vonage/vonage-laravel)
[![PHP Version Require](http://poser.pugx.org/vonage/vonage-laravel/require/php)](https://packagist.org/packages/vonage/vonage-laravel)

### Introduction

This is a Laravel Service Provider for integrating the [Vonage PHP Client Library](https://github.com/Vonage/vonage-php-sdk).

### Requirements

This Package is for use with Laravel versions 9.x and upwards due to PHP Version restrictions. You will need to be running
PHP8.0 and upwards - for older compatibility you will need to look at previous versions.

### Installation

Using [Composer](https://getcomposer.org/), run the terminal command:

```bash
composer require vonage/vonage-laravel
```

### Dealing with Guzzle Client issues
By default, this package uses vonage/client, which includes a Guzzle adapter for
accessing the API. Some other libraries supply their own Guzzle adapter, leading 
to composer not being able to resolve a list of dependencies. You may get an 
error when adding `vonage/vonage-laravel` to your application because of this.

The Vonage client allows you to override the HTTP adapter that is being used.
This takes a bit more configuration, but this package allows you to use `vonage/client-core` to supply 
your own HTTP adapter.

To do this:

Run `composer require vonage/client-core` to install the Core SDK with Composer.

Install your own httplug-compatible adapter. For example, to use Symfony's HTTP Client:

```bash
composer require symfony/http-client php-http/message-factory php-http/httplug nyholm/psr7
```

`composer require vonage/vonage-laravel` to install this package

In your .env file, add the following configuration:

```dotenv
VONAGE_HTTP_CLIENT="Symfony\\Component\\HttpClient\\HttplugClient"
```

You can now pull the Vonage\Client object from the Laravel Service Container, or use the 
Facade provided by this package.

### Configuration

You can use `artisan vendor:publish` to copy the distribution configuration file to your app's 
config directory:

```bash
php artisan vendor:publish --provider="Vonage\Laravel\VonageServiceProvider"
```

Then update `config/vonage.php` with your credentials. Alternatively, you can update your `.env` file 
with the following:

```dotenv
VONAGE_KEY=my_api_key
VONAGE_SECRET=my_secret
```

Optionally, you could also set an `application_id` and `private_key` if required:

```dotenv
VONAGE_APPLICATION_ID=my_application_id
VONAGE_PRIVATE_KEY=./private.key
```

Private keys can either be a path to a file, like above, or the string of the key itself:

```dotenv
VONAGE_PRIVATE_KEY="-----BEGIN PRIVATE KEY-----\n[...]\n-----END PRIVATE KEY-----\n"
```

```dotenv
VONAGE_PRIVATE_KEY="-----BEGIN PRIVATE KEY-----
[...]
-----END PRIVATE KEY-----
"
```

### Usage

To use the Vonage Client Library you can use the Facade, or request the instance from the service 
container:

```php
$text = new \Vonage\SMS\Message\SMS($toNumber, $fromNumber, 'Test SMS using Laravel');
Vonage::sms()->send($text);
```

Or

```php
$vonage = app('Vonage\Client');
$text = new \Vonage\SMS\Message\SMS($toNumber, $fromNumber, 'Test SMS using Laravel');
$vonage->sms()->send($text);
```

If you're using private key authentication, you can make a voice call:

```php
$outboundCall = new \Vonage\Voice\OutboundCall(
    new \Vonage\Voice\Endpoint\Phone('14843331234'),
    new \Vonage\Voice\Endpoint\Phone('14843335555')
);
$outboundCall
    ->setAnswerWebhook(
        new \Vonage\Voice\Webhook('https://example.com/answer')
    )
    ->setEventWebhook(
        new \Vonage\Voice\Webhook('https://example.com/event')
    )
;

$response = Vonage::voice()->createOutboundCall($outboundCall);
```

For more information on using the Vonage Client library, see 
the [official client library repository](https://github.com/Vonage/vonage-php-sdk-core).

[client-library]: https://github.com/Vonage/vonage-php
