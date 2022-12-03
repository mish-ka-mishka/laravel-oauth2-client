# Laravel OAuth2 client

This package is based on the [PHP League OAuth 2.0 Client library,](https://github.com/thephpleague/oauth2-client) 
and it provides a simple interface to OAuth2 authentication for your Laravel application using any of the compatible
OAuth2 providers (for example see the list of [official PHP League](https://oauth2-client.thephpleague.com/providers/league/)
and [third-party](https://oauth2-client.thephpleague.com/providers/thirdparty/) providers 
or [instructions on how to implement your own](https://oauth2-client.thephpleague.com/providers/implementing/)).

## Installation

Run the following command from your project directory to add the dependency:

```shell
composer require mkaverin/laravel-oauth2-client
```

Then, copy and run database migrations:

```shell
php artisan vendor:publish --provider="LaravelOAuth2Client\Providers\OAuth2ServiceProvider" --tag=migrations
```

```shell
php artisan migrate
```

### Laravel without auto-discovery

If you don't use auto-discovery, add the ServiceProvider to the providers array in `config/app.php`:

```php
'providers' => [
    ...
    LaravelOAuth2Client\Providers\OAuth2ServiceProvider::class,
],
```

## Testing

```shell
composer test
```
