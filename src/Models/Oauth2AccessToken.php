<?php

namespace LaravelOauth2Client\Models;

use App\Models\Admin;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Carbon;
use League\OAuth2\Client\Token\AccessTokenInterface;
use League\OAuth2\Client\Token\ResourceOwnerAccessTokenInterface;

/**
 * @property int $id
 * @property string $provider
 * @property Admin $tokenable
 * @property string $access_token
 * @property ?string $refresh_token
 * @property ?string $resource_owner_id
 * @property ?array $values
 * @property ?Carbon $expires_at
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 * @property ?Carbon $deleted_at
 */
class Oauth2AccessToken extends Model implements ResourceOwnerAccessTokenInterface
{
    protected $casts = [
        'values' => 'array',
        'expires_at' => 'datetime',
    ];

    public static function fillFromAccessToken(AccessTokenInterface $accessToken): Oauth2AccessToken
    {
        $instance = new self();

        $instance->access_token = $accessToken->getToken();
        $instance->refresh_token = $accessToken->getRefreshToken();
        $instance->resource_owner_id = $accessToken->getResourceOwnerId();
        $instance->values = $accessToken->getValues();
        $instance->expires_at = $accessToken->getExpires() ? Carbon::createFromTimestamp($accessToken->getExpires()) : null;

        return $instance;
    }

    public function tokenable(): MorphTo
    {
        return $this->morphTo();
    }

    public function getToken(): string
    {
        return $this->access_token;
    }

    public function getRefreshToken(): ?string
    {
        return $this->refresh_token;
    }

    public function setRefreshToken(?string $refreshToken)
    {
        $this->refresh_token = $refreshToken;
    }

    public function getExpires(): ?int
    {
        return $this->expires_at->getTimestamp();
    }

    public function hasExpired(): bool
    {
        return $this->expires_at !== null && $this->expires_at->isPast();
    }

    public function getValues(): array
    {
        return $this->values;
    }

    public function getResourceOwnerId(): ?string
    {
        return $this->resource_owner_id;
    }
}