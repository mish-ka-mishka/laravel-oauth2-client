<?php
namespace LaravelOAuth2Client\Tests;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use LaravelOAuth2Client\Providers\OAuth2ServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    use DatabaseMigrations;

    protected function getPackageProviders($app): array
    {
        return [
            OAuth2ServiceProvider::class,
        ];
    }
}
