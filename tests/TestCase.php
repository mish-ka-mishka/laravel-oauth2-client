<?php
namespace LaravelOauth2Client\Tests;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use LaravelOauth2Client\Providers\Oauth2ServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    use DatabaseMigrations;

    protected function getPackageProviders($app): array
    {
        return [
            Oauth2ServiceProvider::class,
        ];
    }
}
