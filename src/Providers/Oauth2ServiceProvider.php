<?php

namespace LaravelOauth2Client\Providers;

use Illuminate\Support\ServiceProvider;

class Oauth2ServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if (! class_exists('CreateOauth2AccessTokensTable')) {
            $timestamp = date('Y_m_d_His', time());

            $this->publishes([
                __DIR__ . '/../../database/migrations/create_oauth2_access_tokens_table.php.stub' => database_path('migrations/' . $timestamp . '_create_oauth2_access_tokens_table.php'),
            ], 'migrations');
        }
    }
}
