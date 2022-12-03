<?php

namespace LaravelOAuth2Client\Providers;

use Illuminate\Support\ServiceProvider;

class OAuth2ServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if (! class_exists('CreateOAuth2AccessTokensTable')) {
            $timestamp = date('Y_m_d_His', time());

            $this->publishes([
                __DIR__ . '/../../database/migrations/create_oauth2_access_tokens_table.php.stub' => database_path('migrations/' . $timestamp . '_create_oauth2_access_tokens_table.php'),
            ], 'migrations');
        }
    }
}
